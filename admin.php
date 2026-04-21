<?php
declare(strict_types=1);

require_once __DIR__ . '/app.php';

startSessionIfNeeded();
requireAdminUser();

$doktoret = appDoctors();
$oraret = appTimeSlots();

$rezervimet = [];
$mesazhet = [];
$doctorStats = [];
$blockedDates = [];
$gabim = '';
$suksesi = '';
$selectedDoctorFilter = trim($_GET['doctor_filter'] ?? '');
$selectedDateFilter = trim($_GET['date_filter'] ?? '');

try {
    ensureBlockedDoctorDatesTable($pdo);
} catch (PDOException $e) {
    $gabim = 'Krijimi i tabeles se bllokimeve deshtoi: ' . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $gabim === '') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'block_doctor_date') {
            $doctorName = postValue('doctor_name');
            $blockedDate = postValue('blocked_date');
            $blockedTime = postValue('blocked_time');
            $note = postValue('note');

            if ($doctorName === '' || !in_array($doctorName, $doktoret, true)) {
                throw new RuntimeException('Zgjedh nje doktor te vlefshem.');
            }

            if ($blockedDate === '') {
                throw new RuntimeException('Zgjedh nje date per bllokim.');
            }

            if ($blockedDate < date('Y-m-d')) {
                throw new RuntimeException('Mund te bllokosh vetem data te ardhshme.');
            }

            if ($blockedTime !== '' && !in_array($blockedTime, $oraret, true)) {
                throw new RuntimeException('Zgjedh nje ore te vlefshme.');
            }

            $stmtBlock = $pdo->prepare(
                'INSERT INTO blocked_doctor_dates (doctor_name, blocked_date, blocked_time, note)
                 VALUES (?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE note = VALUES(note)'
            );
            $stmtBlock->execute([$doctorName, $blockedDate, $blockedTime !== '' ? $blockedTime : null, $note !== '' ? $note : null]);
            $suksesi = $blockedTime !== ''
                ? 'Data dhe ora u bllokuan me sukses per doktorin e zgjedhur.'
                : 'Data u bllokua me sukses per doktorin e zgjedhur.';
        }

        if ($action === 'delete_blocked_date') {
            $blockId = (int)($_POST['block_id'] ?? 0);
            if ($blockId <= 0) {
                throw new RuntimeException('Bllokimi nuk u gjet.');
            }

            $stmtDelete = $pdo->prepare('DELETE FROM blocked_doctor_dates WHERE id = ?');
            $stmtDelete->execute([$blockId]);
            $suksesi = 'Bllokimi u hoq me sukses.';
        }
    } catch (Throwable $e) {
        $gabim = $e->getMessage();
    }
}

try {
    $rezervimeSql = '
        SELECT id, emri_plote, email, telefoni, data_terminit, ora_terminit, mesazhi, statusi, created_at
        FROM terminet
        WHERE 1=1
    ';
    $rezervimeParams = [];

    if ($selectedDoctorFilter !== '' && in_array($selectedDoctorFilter, $doktoret, true)) {
        $rezervimeSql .= ' AND mesazhi LIKE ?';
        $rezervimeParams[] = '%Doktori: ' . $selectedDoctorFilter . '%';
    }

    if ($selectedDateFilter !== '') {
        $rezervimeSql .= ' AND data_terminit = ?';
        $rezervimeParams[] = $selectedDateFilter;
    }

    $rezervimeSql .= ' ORDER BY data_terminit DESC, ora_terminit DESC';
    $stmtRezervimet = $pdo->prepare($rezervimeSql);
    $stmtRezervimet->execute($rezervimeParams);
    $rezervimet = $stmtRezervimet->fetchAll();

    $stmtMesazhet = $pdo->query(
        'SELECT id, emri_plote, email, subjekti, mesazhi, statusi, created_at
         FROM kontaktet
         ORDER BY created_at DESC'
    );
    $mesazhet = $stmtMesazhet->fetchAll();

    foreach ($doktoret as $doktor) {
        $stmtStats = $pdo->prepare(
            "SELECT COUNT(*) AS total
             FROM terminet
             WHERE statusi IN ('ne_pritje', 'konfirmuar')
               AND data_terminit >= CURDATE()
               AND mesazhi LIKE ?"
        );
        $stmtStats->execute(['%Doktori: ' . $doktor . '%']);
        $doctorStats[$doktor] = (int)($stmtStats->fetch()['total'] ?? 0);
    }

    $stmtBlocked = $pdo->query(
        'SELECT id, doctor_name, blocked_date, blocked_time, note, created_at
         FROM blocked_doctor_dates
         ORDER BY blocked_date ASC, blocked_time ASC, doctor_name ASC'
    );
    $blockedDates = $stmtBlocked->fetchAll();
} catch (PDOException $e) {
    $gabim = 'Leximi nga databaza deshtoi: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmileCare | Admin Panel</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="page-shell">
        <header class="hero">
            <div class="hero-copy">
                <span class="eyebrow">Admin dashboard</span>
                <h1>Paneli i menaxhimit per rezervime dhe komunikim me pacientet</h1>
                <p>Ketu admini mund te shohe rezervimet, mesazhet dhe aktivitetin kryesor ne sistem.</p>
                <p style="margin-top: 18px;">
                    <a href="index.php" class="back-link">Kthehu ne faqe</a>
                    <a href="logout.php" class="back-link secondary-link">Dilni</a>
                </p>
            </div>
            <div class="hero-note">
                <strong>Kontroll i plote</strong>
                <span>Te dhenat lexohen direkt nga databaza per monitorim me te qarte.</span>
            </div>
        </header>
        <?php if ($suksesi !== ''): ?>
            <div class="content-card success-box">
                <p><?= htmlspecialchars($suksesi) ?></p>
            </div>
        <?php endif; ?>
        <?php if ($gabim !== ''): ?>
            <div class="content-card error-box">
                <p><?= htmlspecialchars($gabim) ?></p>
            </div>
        <?php endif; ?>

        <main class="admin-grid">
        <section class="content-card">
            <h2>Filtro rezervimet</h2>
            <form method="get" class="filter-form">
                <div>
                    <label for="doctor_filter">Doktori</label>
                    <select id="doctor_filter" name="doctor_filter">
                        <option value="">Te gjithe doktoret</option>
                        <?php foreach ($doktoret as $doktor): ?>
                            <option value="<?= htmlspecialchars($doktor) ?>" <?= $selectedDoctorFilter === $doktor ? 'selected' : '' ?>>
                                <?= htmlspecialchars($doktor) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="date_filter">Data</label>
                    <input type="date" id="date_filter" name="date_filter" value="<?= htmlspecialchars($selectedDateFilter) ?>">
                </div>

                <div class="filter-actions">
                    <button type="submit">Filtro</button>
                    <a href="admin.php" class="clear-link">Pastro</a>
                </div>
            </form>
        </section>

        <section class="content-card">
            <h2>Doktoret dhe terminet e ardhshme</h2>
            <div class="doctor-stats">
                <?php foreach ($doktoret as $doktor): ?>
                    <article class="stat-card">
                        <h3><?= htmlspecialchars($doktor) ?></h3>
                        <strong><?= (int)($doctorStats[$doktor] ?? 0) ?></strong>
                        <span>termine te ardhshme</span>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="content-card">
            <h2>Blloko date ose ore per doktor</h2>
            <form method="post" class="block-form">
                <input type="hidden" name="action" value="block_doctor_date">

                <label for="doctor_name">Doktori</label>
                <select id="doctor_name" name="doctor_name" required>
                    <option value="">Zgjedh doktorin</option>
                    <?php foreach ($doktoret as $doktor): ?>
                        <option value="<?= htmlspecialchars($doktor) ?>"><?= htmlspecialchars($doktor) ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="blocked_date">Data</label>
                <input type="date" id="blocked_date" name="blocked_date" min="<?= date('Y-m-d') ?>" required>

                <label for="blocked_time">Ora</label>
                <select id="blocked_time" name="blocked_time">
                    <option value="">Gjithe dita</option>
                    <?php foreach ($oraret as $orari): ?>
                        <option value="<?= htmlspecialchars($orari) ?>"><?= htmlspecialchars($orari) ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="note">Shenim</label>
                <input type="text" id="note" name="note" placeholder="Pushim, takim, mungese...">

                <button type="submit">Blloko daten</button>
            </form>
        </section>

        <section class="content-card">
            <h2>Datat e bllokuara</h2>
            <table>
                <tr>
                    <th>Doktori</th>
                    <th>Data</th>
                    <th>Ora</th>
                    <th>Shenimi</th>
                    <th>Veprimi</th>
                </tr>
                <?php if ($blockedDates): ?>
                    <?php foreach ($blockedDates as $blocked): ?>
                        <tr>
                            <td><?= htmlspecialchars($blocked['doctor_name']) ?></td>
                            <td><?= htmlspecialchars($blocked['blocked_date']) ?></td>
                            <td><?= !empty($blocked['blocked_time']) ? htmlspecialchars(normalizeTimeSlot((string)$blocked['blocked_time']) ?? '') : 'Gjithe dita' ?></td>
                            <td><?= htmlspecialchars($blocked['note'] ?? '-') ?></td>
                            <td>
                                <form method="post" class="inline-form">
                                    <input type="hidden" name="action" value="delete_blocked_date">
                                    <input type="hidden" name="block_id" value="<?= (int)$blocked['id'] ?>">
                                    <button type="submit" class="danger-button">Hiqe</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Nuk ka bllokime.</td>
                    </tr>
                <?php endif; ?>
            </table>
        </section>

        <section class="content-card">
            <h2>Rezervimet</h2>
            <table>
                <tr>
                    <th>Emri</th>
                    <th>Email</th>
                    <th>Telefoni</th>
                    <th>Data</th>
                    <th>Ora</th>
                    <th>Pershkrimi / Doktori</th>
                    <th>Statusi</th>
                </tr>
                <?php if ($rezervimet): ?>
                    <?php foreach ($rezervimet as $rezervim): ?>
                        <tr>
                            <td><?= htmlspecialchars($rezervim['emri_plote']) ?></td>
                            <td><?= htmlspecialchars($rezervim['email']) ?></td>
                            <td><?= htmlspecialchars($rezervim['telefoni']) ?></td>
                            <td><?= htmlspecialchars($rezervim['data_terminit']) ?></td>
                            <td><?= htmlspecialchars(substr((string)$rezervim['ora_terminit'], 0, 5)) ?></td>
                            <td><?= htmlspecialchars($rezervim['mesazhi']) ?></td>
                            <td><?= htmlspecialchars($rezervim['statusi']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">Nuk ka rezervime per filtrat e zgjedhur.</td>
                    </tr>
                <?php endif; ?>
            </table>
        </section>

        <section class="content-card">
            <h2>Mesazhet</h2>
            <table>
                <tr>
                    <th>Emri</th>
                    <th>Email</th>
                    <th>Subjekti</th>
                    <th>Mesazhi</th>
                    <th>Statusi</th>
                </tr>
                <?php if ($mesazhet): ?>
                    <?php foreach ($mesazhet as $mesazh): ?>
                        <tr>
                            <td><?= htmlspecialchars($mesazh['emri_plote']) ?></td>
                            <td><?= htmlspecialchars($mesazh['email']) ?></td>
                            <td><?= htmlspecialchars($mesazh['subjekti']) ?></td>
                            <td><?= htmlspecialchars($mesazh['mesazhi']) ?></td>
                            <td><?= htmlspecialchars($mesazh['statusi']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Nuk ka mesazhe ende.</td>
                    </tr>
                <?php endif; ?>
            </table>
        </section>
        </main>
    </div>
    <script src="main.js"></script>
</body>
</html>
