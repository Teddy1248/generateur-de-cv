<?php
// Conversion des photos vers un JPEG léger, lisible par Dompdf.
const PHOTO_TAILLE_MAX = 10 * 1024 * 1024;
const PHOTO_DIMENSION_MAX = 1000;

function fichierPhoto(array $fichier): string
{
    $erreur = $fichier['error'] ?? UPLOAD_ERR_NO_FILE;
    if ($erreur === UPLOAD_ERR_INI_SIZE || $erreur === UPLOAD_ERR_FORM_SIZE) {
        throw new RuntimeException('La photo dépasse la limite autorisée. Le serveur doit autoriser les fichiers de 10 Mo.');
    }
    if ($erreur !== UPLOAD_ERR_OK || !isset($fichier['tmp_name']) || !is_string($fichier['tmp_name']) || !is_uploaded_file($fichier['tmp_name'])) {
        throw new RuntimeException('La photo n’a pas pu être reçue. Veuillez la sélectionner à nouveau.');
    }
    $taille = filesize($fichier['tmp_name']);
    if ($taille === false || $taille <= 0 || $taille > PHOTO_TAILLE_MAX) {
        throw new RuntimeException('Choisissez une image de 10 Mo maximum.');
    }
    return $fichier['tmp_name'];
}

function jpegPhoto(string $chemin): string
{
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($chemin);
    // SVG est rasterisé dans le navigateur ; les convertisseurs serveur ne le lisent pas.
    // Chaque codeur est imposé d'après le contenu, jamais d'après le nom du fichier.
    $formats = [
        'image/jpeg' => 'JPEG', 'image/png' => 'PNG', 'image/gif' => 'GIF',
        'image/webp' => 'WEBP', 'image/bmp' => 'BMP', 'image/x-ms-bmp' => 'BMP',
        'image/x-bmp' => 'BMP', 'image/avif' => 'AVIF', 'image/tiff' => 'TIFF',
        'image/x-icon' => 'ICO', 'image/vnd.microsoft.icon' => 'ICO',
        'image/heic' => 'HEIC', 'image/heif' => 'HEIC',
        'image/heic-sequence' => 'HEIC', 'image/heif-sequence' => 'HEIC',
        'image/jxl' => 'JXL', 'image/jp2' => 'JP2',
        'image/vnd.adobe.photoshop' => 'PSD', 'image/x-photoshop' => 'PSD',
    ];
    if (!isset($formats[$mime])) {
        throw new RuntimeException('Ce format d’image ne peut pas être converti ici. Essayez un JPG, un PNG ou un WebP.');
    }

    // Imagick permet notamment HEIC/HEIF et TIFF si ses décodeurs sont installés.
    if (class_exists('Imagick')) {
        $image = null;
        $fond = null;
        try {
            Imagick::setResourceLimit(Imagick::RESOURCETYPE_MEMORY, 128 * 1024 * 1024);
            Imagick::setResourceLimit(Imagick::RESOURCETYPE_MAP, 128 * 1024 * 1024);
            Imagick::setResourceLimit(Imagick::RESOURCETYPE_DISK, 128 * 1024 * 1024);
            Imagick::setResourceLimit(Imagick::RESOURCETYPE_THREAD, 1);
            Imagick::setResourceLimit(Imagick::RESOURCETYPE_TIME, 15);
            $image = new Imagick();
            // Première image uniquement pour les fichiers animés ou multipages.
            $image->readImage($formats[$mime] . ':' . $chemin . '[0]');
            if (method_exists($image, 'autoOrientImage')) $image->autoOrientImage();
            $image->thumbnailImage(PHOTO_DIMENSION_MAX, PHOTO_DIMENSION_MAX, true, true);
            $image->transformImageColorspace(Imagick::COLORSPACE_SRGB);
            $fond = new Imagick();
            $fond->newImage($image->getImageWidth(), $image->getImageHeight(), 'white');
            $fond->compositeImage($image, Imagick::COMPOSITE_OVER, 0, 0);
            $fond->setImageFormat('jpeg');
            $fond->setImageCompressionQuality(90);
            $fond->stripImage();
            return 'data:image/jpeg;base64,' . base64_encode($fond->getImageBlob());
        } catch (Throwable $erreur) {
            // Repli sur GD pour les formats pris en charge sur cet hébergement.
        } finally {
            if ($image instanceof Imagick) $image->clear();
            if ($fond instanceof Imagick) $fond->clear();
        }
    }

    if (!function_exists('imagecreatefromstring')) {
        throw new RuntimeException('La conversion des photos nécessite GD ou Imagick sur cet hébergement.');
    }
    $dimensions = @getimagesize($chemin);
    if (!$dimensions || $dimensions[0] <= 0 || $dimensions[1] <= 0 || $dimensions[0] * $dimensions[1] > 25000000) {
        throw new RuntimeException('Ce format ou ces dimensions nécessitent une conversion préalable en JPG ou PNG.');
    }
    $source = @imagecreatefromstring(file_get_contents($chemin));
    if ($source === false) {
        throw new RuntimeException('Le décodeur de ce format n’est pas disponible sur cet hébergement. Essayez un JPG ou un PNG.');
    }
    $destination = null;
    try {
        $largeur = imagesx($source);
        $hauteur = imagesy($source);
        $ratio = min(1, PHOTO_DIMENSION_MAX / max($largeur, $hauteur));
        $largeurFinale = max(1, (int) round($largeur * $ratio));
        $hauteurFinale = max(1, (int) round($hauteur * $ratio));
        $destination = imagecreatetruecolor($largeurFinale, $hauteurFinale);
        imagefill($destination, 0, 0, imagecolorallocate($destination, 255, 255, 255));
        imagecopyresampled($destination, $source, 0, 0, 0, 0, $largeurFinale, $hauteurFinale, $largeur, $hauteur);
        ob_start();
        imagejpeg($destination, null, 90);
        $jpeg = ob_get_clean();
        return 'data:image/jpeg;base64,' . base64_encode($jpeg);
    } finally {
        imagedestroy($source);
        if ($destination !== null) imagedestroy($destination);
    }
}

function photoPreparee(string $valeur): string
{
    $prefixe = 'data:image/jpeg;base64,';
    if (strlen($valeur) > 3 * 1024 * 1024 || strpos($valeur, $prefixe) !== 0) {
        throw new RuntimeException('La photo préparée n’est pas valide. Sélectionnez-la à nouveau.');
    }
    $jpeg = base64_decode(substr($valeur, strlen($prefixe)), true);
    $dimensions = $jpeg !== false ? @getimagesizefromstring($jpeg) : false;
    if (!$dimensions || $dimensions[2] !== IMAGETYPE_JPEG || min($dimensions[0], $dimensions[1]) <= 0 || max($dimensions[0], $dimensions[1]) > PHOTO_DIMENSION_MAX) {
        throw new RuntimeException('La photo préparée n’est pas valide. Sélectionnez-la à nouveau.');
    }
    return $prefixe . base64_encode($jpeg);
}
