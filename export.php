<?php
require __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Accès interdit : passe par le formulaire.');
}

function champ(string $nom): string
{
    return trim($_POST[$nom] ?? '');
}

$prenom = champ('prenom');
$nom = champ('nom');
$adresse = champ('adresse');
$numero = champ('numero');
$email = champ('email');
$age = champ('age');
$titre = champ('titre');
$description = champ('description');

$modele = champ('modele');
$modelesAutorises = ['moderne', 'classique', 'sidebar'];
if (!in_array($modele, $modelesAutorises, true)) {
    $modele = 'moderne';
}

$couleur = champ('couleur');
if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $couleur)) {
    $couleur = '#1e88e5';
}

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
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $tailleMax = 2 * 1024 * 1024;

    if ($_FILES['photo']['size'] <= $tailleMax) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($_FILES['photo']['tmp_name']);
        $mimesAutorises = ['image/jpeg', 'image/png'];

        if (in_array($mime, $mimesAutorises, true)) {
            $contenuImage = file_get_contents($_FILES['photo']['tmp_name']);
            $photoData = 'data:' . $mime . ';base64,' . base64_encode($contenuImage);
        }
    }
}

ob_start();
include __DIR__ . '/cv.php';
$html = ob_get_clean();

$pdf = new Dompdf();
$pdf->set_option('isRemoteEnabled', true);
$pdf->loadHtml($html, 'UTF-8');
$pdf->setPaper('A4', 'portrait');
$pdf->render();
$pdf->stream('cv.pdf', ['Attachment' => true]);
exit;
