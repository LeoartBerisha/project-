<?php
declare(strict_types=1);

require_once __DIR__ . '/app.php';

startSessionIfNeeded();

$gabimet = [];
$suksesi = '';
$oraret = appTimeSlots();
$doktoret = appDoctors();

$selectedDate = trim($_POST['data_terminit'] ?? $_GET['data'] ?? '');
$selectedDoctor = trim($_POST['doktori'] ?? $_GET['doktori'] ?? '');
$oraTeZena = [];
$doctorDateBlocked = false;
$blockedMessage = '';

try {
    ensureBlockedDoctorDatesTable($pdo);
} catch (PDOException $e) {
    $gabimet[] = 'Tabela e bllokimeve nuk u krijua dot.';
}

if ($selectedDate !== '' && $selectedDoctor !== '' && in_array($selectedDoctor, $doktoret, true)) {
    $stmtOraret = $pdo->prepare(
        "SELECT ora_terminit
         FROM terminet
         WHERE data_terminit = ?
         AND statusi IN ('ne_pritje', 'konfirmuar')
         AND mesazhi LIKE ?"
    );
    $stmtOraret->execute([$selectedDate, '%Doktori: ' . $selectedDoctor . '%']);
    $oraTeZena = array_map(
        static fn(array $row): string => normalizeTimeSlot((string)$row['ora_terminit']) ?? '',
        $stmtOraret->fetchAll()
    );
}

if ($selectedDate !== '' && $selectedDoctor !== '') {
    $stmtBlocked = $pdo->prepare(
        'SELECT blocked_time, note
         FROM blocked_doctor_dates
         WHERE doctor_name = ? AND blocked_date = ?'
    );
    $stmtBlocked->execute([$selectedDoctor, $selectedDate]);
    $blockedRows = $stmtBlocked->fetchAll();
    if ($blockedRows) {
        foreach ($blockedRows as $blockedRow) {
            if (empty($blockedRow['blocked_time'])) {
                $doctorDateBlocked = true;
                $blockedMessage = 'Doktori i zgjedhur nuk punon ne kete date.';
                $oraTeZena = $oraret;
                if (!empty($blockedRow['note'])) {
                    $blockedMessage .= ' Shenim: ' . $blockedRow['note'];
                }
                break;
            }

            $blockedTime = normalizeTimeSlot((string)$blockedRow['blocked_time']);
            if ($blockedTime !== null && !in_array($blockedTime, $oraTeZena, true)) {
                $oraTeZena[] = $blockedTime;
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emriMbiemri = postValue('emri_mbiemri');
    $email = postValue('email');
    $nrTelefonit = postValue('nr_telefonit');
    $pershkrimi = postValue('pershkrimi');
    $doktori = $selectedDoctor;
    $data = $selectedDate;
    $ora = postValue('ora_terminit');

    if ($emriMbiemri === '') {
        $gabimet[] = 'Emri dhe mbiemri eshte i detyrueshem.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $gabimet[] = 'Email nuk eshte valid.';
    }

    if ($nrTelefonit === '') {
        $gabimet[] = 'Numri i telefonit eshte i detyrueshem.';
    }

    if ($pershkrimi === '') {
        $gabimet[] = 'Pershkrimi eshte i detyrueshem.';
    }

    if ($doktori === '') {
        $gabimet[] = 'Zgjedhja e doktorit eshte e detyrueshme.';
    }

    if ($data === '') {
        $gabimet[] = 'Data eshte e detyrueshme.';
    } elseif ($data < date('Y-m-d')) {
        $gabimet[] = 'Data nuk mund te jete ne te kaluaren.';
    }

    if ($doctorDateBlocked) {
        $gabimet[] = $blockedMessage;
    }

    if ($ora === '') {
        $gabimet[] = 'Ora eshte e detyrueshme.';
    } elseif (!in_array($ora, $oraret, true)) {
        $gabimet[] = 'Ora duhet te jete nga 09:00 deri ne 19:00.';
    } elseif (in_array($ora, $oraTeZena, true)) {
        $gabimet[] = 'Kjo ore eshte e zene per daten e zgjedhur.';
    }

    if (!$gabimet) {
        $stmt = $pdo->prepare(
            'INSERT INTO terminet (user_id, sherbimi_id, emri_plote, email, telefoni, data_terminit, ora_terminit, mesazhi, statusi)
             VALUES (?, NULL, ?, ?, ?, ?, ?, ?, ?)'
        );

        $userId = $_SESSION['user_id'] ?? 1;
        $stmt->execute([
            $userId,
            $emriMbiemri,
            $email,
            $nrTelefonit,
            $data,
            $ora,
            $pershkrimi . ' | Doktori: ' . $doktori,
            'ne_pritje'
        ]);

        $suksesi = 'Rezervimi u dergua me sukses.';
        $oraTeZena[] = $ora;
    }
}
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termini</title>
    <link rel="stylesheet" href="termini.css">
</head>
<body>
    <div class="page-shell">
        <nav class="top-nav">
            <div class="brand">SmileCare</div>
            <button class="menu-toggle" type="button" aria-label="Hap menune" aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <a href="index.php">Ballina</a>
            <a href="rrethnesh.php">Rreth Nesh</a>
            <a href="termini.php">Termini</a>
            <a href="sherbimet.php">Sherbimet</a>
            <a href="kontakto.php">Kontakto</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="logout.php">Dilni</a>
            <?php else: ?>
                <a href="kyqu.php">Kyqu</a>
            <?php endif; ?>
        </nav>
        <header class="hero">
            <div class="hero-copy">
                <span class="eyebrow">Rezervim online</span>
                <h1>Zgjidh daten, oren dhe doktorin ne nje proces te qarte</h1>
                <p>Platforma kontrollon oraret e zena automatikisht dhe te lejon te rezervosh vetem slotet e lira nga 09:00 deri ne 19:00.</p>
            </div>
            <div class="hero-note">
                <strong>Orar i kontrolluar</strong>
                <span>Rezervimet e vendosura ne admin panel bllokohen automatikisht per perdoruesit e tjere.</span>
            </div>
        </header>

        <section class="content-card form-shell">
            <h2>Rezervim i shpejte</h2>
            <p>Plotesoni formen per te rezervuar termin tek doktori i deshiruar.</p>

            <?php if ($gabimet): ?>
                <div class="message error">
                    <?php foreach ($gabimet as $gabim): ?>
                        <p><?= htmlspecialchars($gabim) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($suksesi !== ''): ?>
                <div class="message success">
                    <p><?= htmlspecialchars($suksesi) ?></p>
                </div>
            <?php endif; ?>

            <form action="" method="post" class="appointment-form">
                <label for="emri_mbiemri">Emri dhe Mbiemri</label>
                <input type="text" id="emri_mbiemri" name="emri_mbiemri" value="<?= htmlspecialchars($_POST['emri_mbiemri'] ?? '') ?>" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>

                <label for="nr_telefonit">Nr. Telefonit</label>
                <input type="text" id="nr_telefonit" name="nr_telefonit" value="<?= htmlspecialchars($_POST['nr_telefonit'] ?? '') ?>" required>

                <label for="pershkrimi">Pershkrimi</label>
                <textarea id="pershkrimi" name="pershkrimi" rows="5" required><?= htmlspecialchars($_POST['pershkrimi'] ?? '') ?></textarea>

                <label for="doktori">Zgjedh Doktorin</label>
                <select id="doktori" name="doktori" required>
                    <option value="">Zgjedh doktorin</option>
                    <?php foreach ($doktoret as $doktor): ?>
                        <option value="<?= htmlspecialchars($doktor) ?>" <?= ($selectedDoctor === $doktor) ? 'selected' : '' ?>><?= htmlspecialchars($doktor) ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="data_terminit">Data</label>
                <input type="date" id="data_terminit" name="data_terminit" value="<?= htmlspecialchars($selectedDate) ?>" min="<?= date('Y-m-d') ?>" required>

                <label for="ora_terminit">Ora</label>
                <select id="ora_terminit" name="ora_terminit" required>
                    <option value="">Zgjedh oren</option>
                    <?php foreach ($oraret as $orari): ?>
                        <?php $isBooked = in_array($orari, $oraTeZena, true); ?>
                        <option value="<?= htmlspecialchars($orari) ?>"
                            <?= (($_POST['ora_terminit'] ?? '') === $orari) ? 'selected' : '' ?>
                            <?= $isBooked ? 'disabled' : '' ?>>
                            <?= htmlspecialchars($orari) ?><?= $isBooked ? ' - E zene' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <?php if ($selectedDate !== ''): ?>
                    <p class="helper-text">Per daten <?= htmlspecialchars($selectedDate) ?>, oraret e zena bllokohen automatikisht.</p>
                <?php endif; ?>
                <?php if ($doctorDateBlocked): ?>
                    <p class="helper-text blocked-text"><?= htmlspecialchars($blockedMessage) ?></p>
                <?php endif; ?>

                <button type="submit">Rezervo Terminin</button>
            </form>
        </section>
    </div>
    <script src="main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dateInput = document.getElementById('data_terminit');
            const doctorInput = document.getElementById('doktori');

            if (!dateInput || !doctorInput) {
                return;
            }

            const refreshAvailability = function () {
                if (!dateInput.value && !doctorInput.value) {
                    return;
                }

                const url = new URL(window.location.href);
                if (dateInput.value) {
                    url.searchParams.set('data', dateInput.value);
                }
                if (doctorInput.value) {
                    url.searchParams.set('doktori', doctorInput.value);
                }
                window.location.href = url.toString();
            };

            dateInput.addEventListener('change', refreshAvailability);
            doctorInput.addEventListener('change', refreshAvailability);
        });
    </script>
</body>
</html>
