<?php
declare(strict_types=1);

require_once __DIR__ . '/app.php';

$gabimet = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = postValue('email');
    $fjalekalimi = (string)($_POST['password'] ?? '');

    if ($email === '') {
        $gabimet[] = 'Email eshte i detyrueshem.';
    }

    if ($fjalekalimi === '') {
        $gabimet[] = 'Fjalekalimi eshte i detyrueshem.';
    }

    if (!$gabimet) {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($fjalekalimi, $user['password'])) {
            startSessionIfNeeded();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['emri_plote'];
            $_SESSION['user_role'] = $user['roli'];

            if ($user['roli'] === 'admin') {
                redirectTo('admin.php');
            }

            redirectTo('index.php');
        }

        $gabimet[] = 'Email ose fjalekalimi eshte gabim.';
    }
}
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmileCare | Kyqu</title>
    <link rel="stylesheet" href="kyqu.css?v=3">
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
            <a href="kyqu.php">Kyqu</a>
        </nav>
        <header class="hero">
            <div class="hero-copy">
                <span class="eyebrow">Qasje ne sistem</span>
                <h1>Kyqu per te menaxhuar terminet dhe profilin tend</h1>
                <p>Perdoruesit mund te qasen ne llogarine e tyre, ndersa administratori menaxhon rezervimet dhe mesazhet nga dashboard-i.</p>
            </div>
            <div class="hero-note">
                <strong>Qasje e sigurt</strong>
                <span>Identifikim me email dhe fjalekalim, me ridrejtim sipas rolit.</span>
            </div>
        </header>
        <section class="content-card auth-card">
            <h2>Hyr ne llogari</h2>
            <?php if ($gabimet): ?>
                <div class="message error">
                    <?php foreach ($gabimet as $gabim): ?>
                        <p><?= htmlspecialchars($gabim) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="" method="post" class="auth-form">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Fjalekalimi</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Kyqu</button>
            </form>

            <p class="auth-link">Nuk keni llogari? <a href="regjister.php">Regjistrohu</a></p>
        </section>
    </div>
    <script src="main.js"></script>
</body>
</html>
