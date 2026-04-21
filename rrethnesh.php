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
    <title>SmileCare | Rreth Nesh</title>
    <link rel="stylesheet" href="rrethnesh.css">
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
                <span class="eyebrow">Rreth SmileCare</span>
                <h1>Klinike qe kombinon kujdesin profesional me nje eksperience te qete</h1>
                <p>Qellimi yne eshte qe çdo pacient te ndihet i mirepritur, i informuar dhe i sigurt ne cdo hap te trajtimit dentar.</p>
            </div>
            <div class="hero-visual">
                <img src="2.jpg" alt="Ambient i klinikes">
            </div>
        </header>
        <section class="story-grid">
            <article class="content-card">
                <p class="section-tag">Misioni yne</p>
                <h2>Kujdes me standarde moderne</h2>
                <p>SmileCare eshte krijuar per te ofruar trajtime dentare te sigurta, estetike dhe te organizuara ne nje ambient modern e miqesor.</p>
            </article>
            <article class="content-card accent-card">
                <p class="section-tag">Qasja jone</p>
                <h2>Pacienti ne qender te cdo vendimi</h2>
                <p>Fokusi yne mbetet komunikimi i qarte, planifikimi i sakte dhe komoditeti i pacientit ne çdo vizite.</p>
            </article>
        </section>
        <section class="values-section">
            <div class="section-head">
                <div>
                    <p class="section-tag">Vlerat tona</p>
                    <h2>Çfare na dallon ne punën e perditshme</h2>
                </div>
            </div>
            <div class="value-grid">
                <article class="value-card">
                    <h3>Profesionalizem</h3>
                    <p>Staf i pergatitur me qasje serioze dhe te kujdesshme ne çdo trajtim.</p>
                </article>
                <article class="value-card">
                    <h3>Komunikim i qarte</h3>
                    <p>Pacienti informohet per çdo procedure, plan dhe rekomandim.</p>
                </article>
                <article class="value-card">
                    <h3>Komoditet</h3>
                    <p>Ambient i ngrohte dhe proces i organizuar per eksperience pa stres.</p>
                </article>
            </div>
        </section>
        <section class="team-feature">
            <div class="section-head">
                <div>
                    <p class="section-tag">Ekipi</p>
                    <h2>Njerez qe e marrin seriozisht shendetin dhe buzeqeshjen tende</h2>
                </div>
            </div>
            <div class="team-grid">
                <article class="team-card">
                    <img src="doc1.jpg" alt="Doktoreshë">
                    <div>
                        <h3>Dr. Sara Hoxha</h3>
                        <p>Fokus ne kontrolle, edukim te pacientit dhe qasje te ngrohte ne trajtim.</p>
                    </div>
                </article>
                <article class="team-card">
                    <img src="doc2.jpeg" alt="Doktor">
                    <div>
                        <h3>Dr. Arben Krasniqi</h3>
                        <p>Trajtime restauruese dhe kujdes i vazhdueshem ne plane afatgjata.</p>
                    </div>
                </article>
                <article class="team-card">
                    <img src="doc4.jpeg" alt="Staf">
                    <div>
                        <h3>Dr. Blendina Gashi</h3>
                        <p>Angazhim ne estetike dentare dhe krijimin e rezultateve natyrale.</p>
                    </div>
                </article>
            </div>
        </section>
    </div>
    <script src="main.js"></script>
</body>
</html>
