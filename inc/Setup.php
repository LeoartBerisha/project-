<?php
declare(strict_types=1);

require_once __DIR__ . '/DB.php';

final class Setup
{
    public static function ensureDefaultContentPages(): void
    {
        $pdo = DB::pdo();

        $pages = [
            'home' => [
                'title' => 'Klinika dentare',
                'body' => '<h1>Zbukuro buzeqeshjen me Smile Care</h1><p>Klinikë moderne dentare me teknologji të avancuar dhe staf profesional.</p>',
            ],
            'about' => [
                'title' => 'Rreth Nesh',
                'body' => '
                    <h2>Qellimi i Projektit</h2>
                    <p>Ky projekt u krijua per te thjeshtuar rezervimin e termineve dhe per te ofruar nje experience te qarte per pacientet.</p>
                    <h2>Ekipi</h2>
                    <p>Staf mjekesor, administrate dhe mbeshtetje teknike qe punojne se bashku per nje sherbim cilesor.</p>
                    <h2>Informacion i Pergjithshem</h2>
                    <ul>
                        <li>Rezervim termini permes platformes.</li>
                        <li>Kontakti permes formularit.</li>
                        <li>Dashboard admin per menaxhim.</li>
                    </ul>
                ',
            ],
            'contact' => [
                'title' => 'Kontakti',
                'body' => '
                    <p><strong>Adresa:</strong> Prishtinë</p>
                    <p><strong>Telefoni:</strong> +383 44 123 456</p>
                    <p><strong>Email:</strong> info@smilecare.com</p>
                ',
            ],
            'appointments' => [
                'title' => 'Terminet',
                'body' => '
                    <p><strong>Hënë – E enjte:</strong> 09:00 – 16:00</p>
                    <p><strong>E premte:</strong> 09:00 – 13:00</p>
                    <p class="info">
                        Nëse keni nevojë për urgjencë dentare, ju lutem kontaktoni menjëherë klinikën.
                    </p>
                ',
            ],
        ];

        foreach ($pages as $slug => $payload) {
            $stmt = $pdo->prepare("SELECT COUNT(*) AS cnt FROM content_pages WHERE slug = :slug LIMIT 1");
            $stmt->execute([':slug' => $slug]);
            $cnt = (int)($stmt->fetch()['cnt'] ?? 0);
            if ($cnt > 0) {
                continue;
            }

            $insert = $pdo->prepare("
                INSERT INTO content_pages (slug, title, body, is_published, created_by, updated_by)
                VALUES (:slug, :title, :body, 1, NULL, NULL)
            ");
            $insert->execute([
                ':slug' => $slug,
                ':title' => $payload['title'],
                ':body' => $payload['body'],
            ]);
        }
    }
}

