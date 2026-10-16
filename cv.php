<?php
// Gabarit PDF : les données sont préparées et validées dans export.php.
function e(string $valeur): string
{
    return htmlspecialchars($valeur, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function afficherListe(array $elements): string
{
    $html = '<ul>';
    foreach ($elements as $element) {
        $html .= '<li>' . nl2br(e($element)) . '</li>';
    }
    return $html . '</ul>';
}

function afficherParcours(array $elements, string $champTexte): string
{
    $html = '';
    foreach ($elements as $element) {
        $dates = implode(' - ', array_filter([$element['debut'], $element['fin']], function ($date) { return $date !== ''; }));
        $html .= '<div class="item">';
        if ($dates !== '') $html .= '<p class="date">' . e($dates) . '</p>';
        if ($element[$champTexte] !== '') $html .= '<p>' . nl2br(e($element[$champTexte])) . '</p>';
        $html .= '</div>';
    }
    return $html;
}

function rubrique(string $titreRubrique, string $contenu): string
{
    return '<div class="section"><h2>' . e($titreRubrique) . '</h2>' . $contenu . '</div>';
}

$nomComplet = trim($prenom . ' ' . $nom);
$colonneUnique = $ats || !in_array($modele, ['moderne', 'classique', 'sidebar'], true);
$profilHtml = $description !== '' ? rubrique('Profil', '<p>' . nl2br(e($description)) . '</p>') : '';
$experiencesHtml = $experiences ? rubrique('Expériences professionnelles', afficherParcours($experiences, 'texte')) : '';
$formationsHtml = $formations ? rubrique('Formations', afficherParcours($formations, 'titre')) : '';
$competencesHtml = $competences ? rubrique('Compétences', afficherListe($competences)) : '';
$languesHtml = $langues ? rubrique('Langues', afficherListe($langues)) : '';
$interetsHtml = $centresInteret ? rubrique('Centres d’intérêt', afficherListe($centresInteret)) : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= e(trim($nomComplet . ' - ' . $titre, ' -')) ?></title>
    <style>
        @page { margin: 32px 36px; }
        body { margin: 0; font-family: DejaVu Sans, sans-serif; color: #243347; font-size: 11px; line-height: 1.45; }
        h1, h2, p { margin-top: 0; }
        p { margin-bottom: 7px; overflow-wrap: break-word; word-wrap: break-word; }
        .header { padding: 19px; border-radius: 12px; background: <?= e($couleur) ?>; color: #172033; }
        h1 { margin-bottom: 6px; font-size: 26px; line-height: 1.15; word-wrap: break-word; }
        .job-title { margin-bottom: 8px; font-size: 13px; font-weight: bold; }
        .contact { margin-bottom: 3px; font-size: 10px; }
        .photo { float: right; width: 70px; height: 70px; margin: 0 0 10px 15px; border-radius: 50%; }
        .section { margin-top: 14px; }
        h2 { margin-bottom: 7px; padding-bottom: 4px; border-bottom: 1px solid <?= e($couleur) ?>; color: <?= e($couleurTexte) ?>; font-size: 12px; page-break-after: avoid; }
        .item { margin-bottom: 10px; }
        .date { margin-bottom: 2px; color: #172033; font-size: 10px; font-weight: bold; page-break-after: avoid; }
        ul { margin: 0; padding-left: 17px; }
        li { margin-bottom: 3px; word-wrap: break-word; }
        .main-table { width: 100%; border-collapse: collapse; table-layout: fixed; margin-top: 14px; }
        .sidebar-cell { width: 31%; padding-right: 16px; vertical-align: top; }
        .content-cell { width: 69%; vertical-align: top; }
        .cv-classique .header { border: 2px solid <?= e($couleur) ?>; background: white; }
        .cv-sidebar .header { border-radius: 12px 12px 0 0; }
        .cv-sidebar .sidebar-cell { padding: 14px; background: <?= e($couleurClaire) ?>; }
        .cv-sidebar .content-cell { padding: 0 0 0 16px; }
        .cv-epure .header { padding: 4px 0 14px; border-radius: 0; border-bottom: 3px solid <?= e($couleur) ?>; background: white; }
        .cv-epure h2 { border: 0; }
        .cv-elegant .header { border-left: 5px solid <?= e($couleurTexte) ?>; border-radius: 0; background: <?= e($couleurClaire) ?>; }
        .cv-elegant h1 { font-family: DejaVu Serif, serif; }
        .cv-professionnel .header { border-radius: 0; border-top: 6px solid <?= e($couleurTexte) ?>; background: <?= e($couleurClaire) ?>; }
        .cv-professionnel h2 { padding: 5px 8px; border: 0; background: <?= e($couleurClaire) ?>; }
        .cv-compact { font-size: 10px; line-height: 1.3; }
        .cv-compact .header { padding: 12px 15px; }
        .cv-compact h1 { font-size: 23px; }
        .cv-compact .section { margin-top: 10px; }
        .cv-compact .item { margin-bottom: 6px; }
        .cv-compact h2 { margin-bottom: 5px; }
        .cv-academique .header { padding: 4px 0 14px; border-radius: 0; border-bottom: 2px solid <?= e($couleurTexte) ?>; background: white; }
        .cv-academique h1 { font-family: DejaVu Serif, serif; }
        .cv-academique h2 { border-bottom: 2px solid <?= e($couleurTexte) ?>; }
        .cv-essentiel .header { padding: 4px 0 14px 15px; border-radius: 0; border-left: 4px solid <?= e($couleur) ?>; background: white; }
        .cv-essentiel h2 { padding-left: 9px; border: 0; border-left: 3px solid <?= e($couleur) ?>; }
    </style>
</head>
<body>
    <div class="cv cv-<?= e($modele) ?>">
        <div class="header">
            <?php if (!$ats && $photoData !== ''): ?><img src="<?= e($photoData) ?>" class="photo" alt="Photo de profil"><?php endif; ?>
            <?php if ($nomComplet !== ''): ?><h1><?= e($nomComplet) ?></h1><?php endif; ?>
            <?php if ($titre !== ''): ?><p class="job-title"><?= e($titre) ?></p><?php endif; ?>
            <?php if ($adresse !== ''): ?><p class="contact">Adresse : <?= e($adresse) ?></p><?php endif; ?>
            <?php if ($numero !== ''): ?><p class="contact">Téléphone : <?= e($numero) ?></p><?php endif; ?>
            <?php if ($email !== ''): ?><p class="contact">Email : <?= e($email) ?></p><?php endif; ?>
            <?php if ($age !== ''): ?><p class="contact">Âge : <?= e($age) ?> ans</p><?php endif; ?>
            <?php if (!$ats && $photoData !== ''): ?><div style="clear: both;"></div><?php endif; ?>
        </div>
        <?php if ($colonneUnique): ?>
            <?= $profilHtml . $experiencesHtml . $formationsHtml . $competencesHtml . $languesHtml . $interetsHtml ?>
        <?php else: ?>
            <?= $modele !== 'sidebar' ? $profilHtml : '' ?>
            <table class="main-table"><tr>
                <td class="sidebar-cell"><?= $competencesHtml . $languesHtml . $interetsHtml ?></td>
                <td class="content-cell"><?= ($modele === 'sidebar' ? $profilHtml : '') . $experiencesHtml . $formationsHtml ?></td>
            </tr></table>
        <?php endif; ?>
    </div>
</body>
</html>
