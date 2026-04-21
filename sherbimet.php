<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmileCare | Sherbimet</title>
    <link rel="stylesheet" href="sherbimet.css">
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
                <span class="eyebrow">Sherbime dentare</span>
                <h1>Zgjidhje profesionale per kujdesin, funksionin dhe estetiken e dhembeve</h1>
                <p>Çdo sherbim planifikohet sipas nevojave te pacientit, me fokus te cilesia, qartesia ne komunikim dhe rezultati afatgjate.</p>
            </div>
            <div class="hero-visual">
                <img src="3.jpg" alt="Sherbime te klinikes">
            </div>
        </header>
        <section class="services-grid">
            <article class="service-card">
                <p class="section-tag">01</p>
                <h2>Kontroll Dentar</h2>
                <p>Vizita kontrolluese per vleresimin e gjendjes orale dhe planin e trajtimit.</p>
            </article>
            <article class="service-card">
                <p class="section-tag">02</p>
                <h2>Pastrimi Profesional</h2>
                <p>Heqje e gureve dentare dhe mirembajtje per buzeqeshje me te fresket.</p>
            </article>
            <article class="service-card">
                <p class="section-tag">03</p>
                <h2>Mbushje te Dhembeve</h2>
                <p>Trajtime restauruese per mbrojtje, funksion dhe paraqitje me natyrale.</p>
            </article>
            <article class="service-card">
                <p class="section-tag">04</p>
                <h2>Ortodonci</h2>
                <p>Zgjidhje per drejtimin e dhembeve dhe permiresimin e kafshimit.</p>
            </article>
            <article class="service-card">
                <p class="section-tag">05</p>
                <h2>Estetike Dentare</h2>
                <p>Zbardhim, rregullim estetik dhe trajtime per buzeqeshje me harmonike.</p>
            </article>
            <article class="service-card featured">
                <p class="section-tag">Rezervim online</p>
                <h2>Cakto konsulten tende</h2>
                <p>Perzgjedh doktorin, daten dhe oren ne pak hapa nga sistemi i termineve.</p>
                <a href="termini.php" class="button-link">Shko te terminet</a>
            </article>
        </section>
    </div>
    <script src="main.js"></script>
</body>
</html>
