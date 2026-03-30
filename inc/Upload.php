<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';

final class Upload
{
    public static function saveImage(array $file): string
    {
        return self::saveFile($file, [
            'jpg', 'jpeg', 'png', 'webp', 'gif'
        ], PORTFOLIO_IMAGE_DIR);
    }

    public static function savePdf(array $file): string
    {
        return self::saveFile($file, ['pdf'], PORTFOLIO_PDF_DIR);
    }

    private static function saveFile(array $file, array $allowedExt, string $targetDir): string
    {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload i skedarit deshtoi.');
        }

        if (!is_uploaded_file($file['tmp_name'])) {
            throw new RuntimeException('Skedari nuk eshte valid.');
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt, true)) {
            throw new InvalidArgumentException('Format i papranueshem i skedarit.');
        }

        $safeName = uniqid('file_', true) . '.' . $ext;
        $targetPath = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . $safeName;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new RuntimeException('Nuk u arrit te ruhet skedari.');
        }

        // Kthe relative path per <img> / <a href>
        $relativeRoot = str_replace(__DIR__, '', dirname(__DIR__));
        // Meqenese po e ekzekutojme ne root folder, ruajme path relative nga webroot:
        if (str_contains($targetDir, '/portfolio/images') || str_contains($targetDir, '\\portfolio\\images')) {
            return 'uploads/portfolio/images/' . $safeName;
        }
        return 'uploads/portfolio/pdfs/' . $safeName;
    }
}

