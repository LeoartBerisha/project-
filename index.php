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
    <title>SmileCare | Ballina</title>
    <link rel="stylesheet" href="index.css">
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
                <span class="eyebrow">Klinike dentare moderne</span>
                <h1>Buzeqeshje me e shendetshme, kujdes me i afert</h1>
                <p>
                    Kujdes dentar profesional me mjeke te perkushtuar, ambiente moderne
                    dhe sistem te thjeshte online per rezervimin e termineve.
                </p>
                <div class="hero-actions">
                    <a class="button primary" href="termini.php">Rezervo termin</a>
                    <a class="button secondary" href="sherbimet.php">Shiko sherbimet</a>
                </div>
                <div class="hero-stats">
                    <div>
                        <strong>4+</strong>
                        <span>Doktore profesioniste</span>
                    </div>
                    <div>
                        <strong>09:00 - 19:00</strong>
                        <span>Orari i punës</span>
                    </div>
                    <div>
                        <strong>100%</strong>
                        <span>Kujdes i personalizuar</span>
                    </div>
                </div>
            </div>

            <div class="hero-slider" data-slider>
                <div class="slides">
                    <img src="1.jpg" alt="Klinika dentare" class="slide is-active">
                    <img src="2.jpg" alt="Sherbime moderne" class="slide">
                    <img src="3.jpg" alt="SmileCare interior" class="slide">
                </div>
                <div class="slider-badges">
                    <span>Teknologji moderne</span>
                    <span>Rezervim online</span>
                    <span>Staf profesional</span>
                </div>
            </div>
        </header>

        <section class="intro-grid">
            <article class="content-card soft-card">
                <p class="section-tag">Pse ne</p>
                <h2>Eksperience komode nga hyrja deri te trajtimi</h2>
                <p>
                    Ne ofrojme kontrolle rutinore, estetike dentare dhe trajtime te personalizuara
                    ne nje ambient te ngrohte dhe te organizuar.
                </p>
            </article>

            <article class="content-card highlight-card">
                <p class="section-tag">Shpejte dhe thjeshte</p>
                <h2>Rezervo ne pak hapa</h2>
                <p>
                    Zgjedh daten, oren dhe doktorin. Sistemi i bllokon automatikisht oraret e zena
                    qe te shmangen rezervimet e dyfishta.
                </p>
            </article>
        </section>

        <section class="services-preview">
            <div class="section-head">
                <div>
                    <p class="section-tag">Sherbimet kryesore</p>
                    <h2>Kujdes i plote per cdo nevoje dentare</h2>
                </div>
                <a class="text-link" href="sherbimet.php">Te gjitha sherbimet</a>
            </div>

            <div class="service-grid">
                <article class="service-card">
                    <h3>Kontroll Dentar</h3>
                    <p>Kontrolle te rregullta per zbulim te hershem dhe mirembajtje te shendetit oral.</p>
                </article>
                <article class="service-card">
                    <h3>Pastrimi Profesional</h3>
                    <p>Heqje e gureve dhe pllakezave me kujdes profesional dhe pajisje moderne.</p>
                </article>
                <article class="service-card">
                    <h3>Estetike Dentare</h3>
                    <p>Zgjidhje per buzeqeshje me te ndritshme, me harmonike dhe me vetebesim.</p>
                </article>
            </div>
        </section>

        <section class="team-section">
            <div class="section-head">
                <div>
                    <p class="section-tag">Ekipi yne</p>
                    <h2>Staf i perkushtuar dhe me qasje njerezore</h2>
                </div>
            </div>

            <div class="team-grid">
                <article class="team-card">
                    <img src="doc1.jpg" alt="Doktoreshe e SmileCare">
                    <div>
                        <h3>Dr. Sara Hoxha</h3>
                        <p>Stomatologe e pergjithshme me fokus te kujdesi preventiv dhe komunikimi me pacientin.</p>
                    </div>
                </article>
                <article class="team-card">
                    <img src="doc2.jpeg" alt="Doktor i SmileCare">
                    <div>
                        <h3>Dr. Arben Krasniqi</h3>
                        <p>Eksperience ne trajtime restauruese dhe plane te personalizuara per paciente te rinj e te rritur.</p>
                    </div>
                </article>
                <article class="team-card">
                    <img src="doc3.jpeg" alt="Doktor i klinikes">
                    <div>
                        <h3>Dr. Liridon Berisha</h3>
                        <p>Punon me qasje te qarte, te qete dhe me standarde te larta ne sherbim.</p>
                    </div>
                </article>
                <article class="team-card">
                    <img src="doc4.jpeg" alt="Staf profesional">
                    <div>
                        <h3>Dr. Blendina Gashi</h3>
                        <p>Perkushtim per estetike dentare dhe krijimin e nje eksperience sa me komode.</p>
                    </div>
                </article>
            </div>
        </section>

        <section class="cta-banner">
            <div>
                <p class="section-tag">Gati per viziten tende?</p>
                <h2>Rezervo online dhe zgjedh orarin qe te pershtatet me se shumti</h2>
            </div>
            <a class="button primary" href="termini.php">Cakto termin</a>
        </section>
    </div>
    <script src="main.js"></script>
</body>
</html>
