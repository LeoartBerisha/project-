<?php
declare(strict_types=1);

require_once __DIR__ . '/app.php';

startSessionIfNeeded();

$gabimet = [];
$suksesi = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emri = postValue('emri_plote');
    $email = postValue('email');
    $telefoni = postValue('telefoni');
    $fjalekalimi = $_POST['password'] ?? '';
    $konfirmoFjalekalimin = $_POST['confirm_password'] ?? '';

    if ($emri === '') {
        $gabimet[] = 'Emri i plote eshte i detyrueshem.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $gabimet[] = 'Email nuk eshte valid.';
    }

    if ($telefoni === '') {
        $gabimet[] = 'Telefoni eshte i detyrueshem.';
    }

    if (strlen($fjalekalimi) < 6) {
        $gabimet[] = 'Fjalekalimi duhet te kete te pakten 6 karaktere.';
    }

    if ($fjalekalimi !== $konfirmoFjalekalimin) {
        $gabimet[] = 'Fjalekalimet nuk perputhen.';
    }

    if (!$gabimet) {
        $kontrollo = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $kontrollo->execute([$email]);

        if ($kontrollo->fetch()) {
            $gabimet[] = 'Ky email ekziston ne sistem.';
        } else {
            $hash = password_hash($fjalekalimi, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare(
                'INSERT INTO users (emri_plote, email, password, roli, telefoni) VALUES (?, ?, ?, ?, ?)'
            );
            $stmt->execute([$emri, $email, $hash, 'perdorues', $telefoni]);

            $suksesi = 'Regjistrimi u krye me sukses. Tani mund te kyqeni.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmileCare | Regjistrohu</title>
    <link rel="stylesheet" href="regjister.css">
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
                <span class="eyebrow">Krijo llogari</span>
                <h1>Regjistrohu per rezervime me te shpejta dhe qasje ne sistem</h1>
                <p>Krijo profilin tend qe te mund te rezervosh termine dhe te perdorosh platformen me lehte ne vizitat e ardhshme.</p>
            </div>
            <div class="hero-note">
                <strong>Proces i thjeshte</strong>
                <span>Ploteso te dhenat kryesore dhe vazhdo me kyqjen ne sistem.</span>
            </div>
        </header>
        <section class="content-card auth-card">
            <h2>Forma e regjistrimit</h2>

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

            <form action="" method="post" class="auth-form">
                <label for="emri_plote">Emri i plote</label>
                <input type="text" id="emri_plote" name="emri_plote" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>

                <label for="telefoni">Telefoni</label>
                <input type="text" id="telefoni" name="telefoni" required>

                <label for="password">Fjalekalimi</label>
                <input type="password" id="password" name="password" required>

                <label for="confirm_password">Konfirmo fjalekalimin</label>
                <input type="password" id="confirm_password" name="confirm_password" required>

                <button type="submit">Regjistrohu</button>
            </form>

            <p class="auth-link">Keni llogari? <a href="kyqu.php">Kyqu</a></p>
        </section>
    </div>
    <script src="main.js"></script>
</body>
</html>
