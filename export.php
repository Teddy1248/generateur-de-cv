<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config.php';
require __DIR__ . '/images.php';
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

use Dompdf\Dompdf;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Accès interdit : passe par le formulaire.');
}

function champ(string $nom): string
{
    return isset($_POST[$nom]) && is_string($_POST[$nom]) ? trim($_POST[$nom]) : '';
}

$prenom = champ('prenom');
$nom = champ('nom');
$adresse = champ('adresse');
$numero = champ('numero');
$email = champ('email');
$age = champ('age');
$titre = champ('titre');
$description = champ('description');

// Conserver un nom lisible, sans chemin ni caractères spéciaux de fichier.
$nomFichier = preg_replace('/(?:\.pdf)+$/i', '', champ('nom_fichier'));
$nomFichier = preg_replace('/[^\p{L}\p{N} _()-]+/u', '-', $nomFichier) ?? '';
$nomFichier = trim(mb_strcut($nomFichier, 0, 180, 'UTF-8'), ' -_');
if ($nomFichier === '') {
    $nomFichier = 'cv';
}

$modele = champ('modele');
$modelesAutorises = array_keys($modeles);
if (!in_array($modele, $modelesAutorises, true)) {
    $modele = 'moderne';
}

// Toujours utiliser une palette connue : les titres restent contrastés même en jaune.
$palette = $palettes['bleu'];
foreach ($palettes as $candidate) {
    if ($candidate[1] === champ('couleur')) {
        $palette = $candidate;
        break;
    }
}
$couleur = $palette[1];
$couleurClaire = $palette[2];
$couleurTexte = $palette[4];
// L'absence du paramètre conserve le mode recommandé (anciens formulaires inclus).
$ats = champ('ats') !== '0';

$formations = [];
for ($i = 1; $i <= 5; $i++) {
    $formation = [
        'debut' => champ('anneedebut' . $i),
        'fin' => champ('anneefin' . $i),
        'titre' => champ('titreformation' . $i),
    ];

    if ($formation['debut'] !== '' || $formation['fin'] !== '' || $formation['titre'] !== '') {
        $formations[] = $formation;
    }
}

$experiences = [];
for ($i = 1; $i <= 5; $i++) {
    $experience = [
        'debut' => champ('anneedebutexppro' . $i),
        'fin' => champ('anneefinexppro' . $i),
        'texte' => champ('experience' . $i),
    ];

    if ($experience['debut'] !== '' || $experience['fin'] !== '' || $experience['texte'] !== '') {
        $experiences[] = $experience;
    }
}

$competences = [];
for ($i = 1; $i <= 3; $i++) {
    $valeur = champ('competence' . $i);
    if ($valeur !== '') {
        $competences[] = $valeur;
    }
}

$langues = [];
for ($i = 1; $i <= 3; $i++) {
    $valeur = champ('langue' . $i);
    if ($valeur !== '') {
        $langues[] = $valeur;
    }
}

$centresInteret = [];
for ($i = 1; $i <= 3; $i++) {
    $valeur = champ('centreinteret' . $i);
    if ($valeur !== '') {
        $centresInteret[] = $valeur;
    }
}

$photoData = '';
try {
    // Si PHP a rejeté tout le POST, ne pas produire un CV vide silencieusement.
    if (empty($_POST) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
        throw new RuntimeException('Cet envoi dépasse la configuration du serveur. Réglez upload_max_filesize à 10M et post_max_size à 16M, puis réessayez.');
    }
    if (!$ats) {
        $fichier = $_FILES['photo'] ?? [];
        if (!is_array($fichier)) throw new RuntimeException('La photo reçue n’est pas valide.');
        $avecFichier = isset($fichier['error']) && $fichier['error'] !== UPLOAD_ERR_NO_FILE;
        if ($avecFichier) fichierPhoto($fichier);
        if (champ('photo_convertie') !== '') {
            $photoData = photoPreparee(champ('photo_convertie'));
        } elseif ($avecFichier) {
            $photoData = jpegPhoto($fichier['tmp_name']);
        }
    }
} catch (RuntimeException $erreur) {
    http_response_code(422);
    header('Content-Type: text/plain; charset=UTF-8');
    exit($erreur->getMessage() . "\nUtilisez le bouton Retour du navigateur pour corriger la photo.");
}

ob_start();
include __DIR__ . '/cv.php';
$html = ob_get_clean();

$pdf = new Dompdf();
$pdf->set_option('isRemoteEnabled', false);
$pdf->set_option('isFontSubsettingEnabled', true);
$pdf->set_option('defaultFont', 'DejaVu Sans');
$pdf->loadHtml($html, 'UTF-8');
$pdf->setPaper('A4', 'portrait');
$pdf->render();
$pdf->stream($nomFichier . '.pdf', ['Attachment' => true]);
exit;
