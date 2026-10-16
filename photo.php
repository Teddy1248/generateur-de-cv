<?php
require __DIR__ . '/images.php';
header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo json_encode(['erreur' => 'Utilisez le formulaire pour choisir une image.']);
    exit;
}

try {
    if (empty($_FILES) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
        throw new RuntimeException('Le serveur refuse cette taille d’envoi. Configurez post_max_size à 16M et upload_max_filesize à 10M.');
    }
    $fichier = $_FILES['photo'] ?? [];
    if (!is_array($fichier)) throw new RuntimeException('Le fichier reçu n’est pas valide.');
    echo json_encode(['photo' => jpegPhoto(fichierPhoto($fichier))]);
} catch (RuntimeException $erreur) {
    http_response_code(422);
    echo json_encode(['erreur' => $erreur->getMessage()], JSON_UNESCAPED_UNICODE);
} catch (Throwable $erreur) {
    http_response_code(422);
    echo json_encode(['erreur' => 'L’image n’a pas pu être convertie. Essayez un JPG ou un PNG.']);
}
