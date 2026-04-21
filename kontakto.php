<?php
declare(strict_types=1);

require_once __DIR__ . '/app.php';

startSessionIfNeeded();

$gabimet = [];
$suksesi = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emri = postValue('emri_plote');
    $email = postValue('email');
    $subjekti = postValue('subjekti');
    $mesazhi = postValue('mesazhi');

    if ($emri === '') {
        $gabimet[] = 'Emri dhe mbiemri eshte i detyrueshem.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $gabimet[] = 'Email nuk eshte valid.';
    }

    if ($subjekti === '') {
        $gabimet[] = 'Subjekti eshte i detyrueshem.';
    }

    if ($mesazhi === '' || mb_strlen($mesazhi) < 10) {
        $gabimet[] = 'Mesazhi duhet te kete te pakten 10 karaktere.';
    }

    if (!$gabimet) {
        $stmt = $pdo->prepare(
            'INSERT INTO kontaktet (emri_plote, email, subjekti, mesazhi, statusi, created_at)
             VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)'
        );
        $stmt->execute([$emri, $email, $subjekti, $mesazhi, 'ri']);
        $suksesi = 'Mesazhi u dergua me sukses.';
    }
}
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmileCare | Kontakto</title>
    <link rel="stylesheet" href="kontakto.css">
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
                <span class="eyebrow">Kontakti</span>
                <h1>Jemi ketu per pyetje, informata dhe caktim te vizitave</h1>
                <p>Na kontaktoni permes formes, telefonit ose email-it dhe stafi yne do t'ju pergjigjet sa me shpejt.</p>
            </div>
            <div class="hero-visual">
                <img src="1.jpg" alt="SmileCare contact">
            </div>
        </header>
        <section class="contact-grid">
            <article class="content-card info-card">
                <p class="section-tag">Informacion</p>
                <h2>Detajet e klinikes</h2>
                <div class="contact-list">
                    <div><strong>Adresa</strong><span>Prishtine, Rruga e Shendetit 12</span></div>
                    <div><strong>Telefoni</strong><span>+383 44 123 456</span></div>
                    <div><strong>Email</strong><span>info@smilecare.com</span></div>
                    <div><strong>Orari</strong><span>E hene - E shtune, 09:00 - 19:00</span></div>
                </div>
            </article>
            <article class="content-card form-card">
                <p class="section-tag">Na shkruaj</p>
                <h2>Dergo nje mesazh</h2>
                <p>Ploteso formen dhe mesazhi do te ruhet ne sistem per adminin.</p>

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

                <form class="contact-form" method="post" action="">
                    <input type="text" name="emri_plote" placeholder="Emri dhe Mbiemri" value="<?= htmlspecialchars($_POST['emri_plote'] ?? '') ?>" required>
                    <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    <input type="text" name="subjekti" placeholder="Subjekti" value="<?= htmlspecialchars($_POST['subjekti'] ?? '') ?>" required>
                    <textarea name="mesazhi" rows="6" placeholder="Mesazhi" required><?= htmlspecialchars($_POST['mesazhi'] ?? '') ?></textarea>
                    <button type="submit">Dergo mesazhin</button>
                </form>
            </article>
        </section>
    </div>
    <script src="main.js"></script>
</body>
</html>
