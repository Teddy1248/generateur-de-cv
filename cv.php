<?php
function e(string $valeur): string
{
    return htmlspecialchars($valeur, ENT_QUOTES, 'UTF-8');
}

function afficherListe(array $elements): string
{
    if (count($elements) === 0) {
        return '<p class="muted">Non renseigné</p>';
    }

    $html = '<ul>';
    foreach ($elements as $element) {
        $html .= '<li>' . e($element) . '</li>';
    }
    $html .= '</ul>';

    return $html;
}

$nomComplet = trim($prenom . ' ' . $nom);
if ($nomComplet === '') {
    $nomComplet = 'Votre nom';
}

$titreAffiche = $titre !== '' ? $titre : 'Titre du CV';
$descriptionAffiche = $description !== '' ? $description : 'Courte présentation du profil.';
$classeModele = 'cv-' . $modele;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= e($titreAffiche) ?></title>
    <style>
        @page { margin: 28px; }
        body {
            margin: 0;
            font-family: DejaVu Sans, sans-serif;
            color: #1f2937;
            font-size: 12px;
            line-height: 1.45;
            background: #ffffff;
        }
        h1, h2, h3, p { margin-top: 0; }
        ul { margin-top: 6px; padding-left: 18px; }
        li { margin-bottom: 4px; }
        .cv { width: 100%; }
        .header {
            padding: 22px;
            border-radius: 12px;
            background: <?= e($couleur) ?>;
            color: #172033;
        }
        .header-table { width: 100%; border-collapse: collapse; }
        .photo-cell { width: 92px; vertical-align: top; }
        .photo {
            width: 74px;
            height: 74px;
            border-radius: 50%;
            border: 3px solid rgba(255,255,255,0.9);
            object-fit: cover;
        }
        h1 { font-size: 28px; line-height: 1.05; margin-bottom: 7px; }
        .job-title { margin-bottom: 8px; font-weight: bold; font-size: 14px; }
        .contact { font-size: 11px; opacity: 0.96; }
        .section { margin-top: 18px; }
        .section h2 {
            margin-bottom: 9px;
            padding-bottom: 5px;
            border-bottom: 1.5px solid <?= e($couleur) ?>;
            color: <?= e($couleur) ?>;
            font-size: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .item { margin-bottom: 12px; }
        .date { margin-bottom: 2px; font-weight: bold; color: #111827; }
        .muted { color: #6b7280; }
        .two-columns { width: 100%; border-collapse: collapse; margin-top: 18px; }
        .left-column { width: 32%; vertical-align: top; padding-right: 18px; }
        .right-column { width: 68%; vertical-align: top; }

        .cv-classique .header {
            border: 2px solid <?= e($couleur) ?>;
            background: #ffffff;
            color: #111827;
        }
        .cv-classique .contact { color: #4b5563; }
        .cv-classique .photo { border-color: <?= e($couleur) ?>; }

        .cv-sidebar .main-table { width: 100%; border-collapse: collapse; }
        .cv-sidebar .sidebar-cell {
            width: 32%;
            padding: 20px;
            vertical-align: top;
            background: #eef7ff;
        }
        .cv-sidebar .content-cell {
            width: 68%;
            padding: 20px;
            vertical-align: top;
        }
        .cv-sidebar .sidebar-cell h2 { font-size: 13px; }
        .cv-sidebar .header { border-radius: 0; }
    </style>
</head>
<body>
    <div class="cv <?= e($classeModele) ?>">
        <div class="header">
            <table class="header-table">
                <tr>
                    <?php if ($photoData !== ''): ?>
                        <td class="photo-cell"><img src="<?= e($photoData) ?>" class="photo" alt="Photo de profil"></td>
                    <?php endif; ?>
                    <td>
                        <h1><?= e($nomComplet) ?></h1>
                        <p class="job-title"><?= e($titreAffiche) ?></p>
                        <p class="contact">
                            <?= e($adresse) ?>
                            <?php if ($numero !== ''): ?> · <?= e($numero) ?><?php endif; ?>
                            <?php if ($email !== ''): ?> · <?= e($email) ?><?php endif; ?>
                            <?php if ($age !== ''): ?> · <?= e($age) ?> ans<?php endif; ?>
                        </p>
                    </td>
                </tr>
            </table>
        </div>

        <?php if ($modele === 'sidebar'): ?>
            <table class="main-table">
                <tr>
                    <td class="sidebar-cell">
                        <div class="section">
                            <h2>Compétences</h2>
                            <?= afficherListe($competences) ?>
                        </div>
                        <div class="section">
                            <h2>Langues</h2>
                            <?= afficherListe($langues) ?>
                        </div>
                        <div class="section">
                            <h2>Centres d'intérêts</h2>
                            <?= afficherListe($centresInteret) ?>
                        </div>
                    </td>
                    <td class="content-cell">
                        <div class="section">
                            <h2>Profil</h2>
                            <p><?= nl2br(e($descriptionAffiche)) ?></p>
                        </div>
                        <div class="section">
                            <h2>Formations</h2>
                            <?php foreach ($formations as $formation): ?>
                                <div class="item">
                                    <p class="date"><?= e($formation['debut']) ?> - <?= e($formation['fin']) ?></p>
                                    <p><?= e($formation['titre']) ?></p>
                                </div>
                            <?php endforeach; ?>
                            <?php if (count($formations) === 0): ?><p class="muted">Non renseigné</p><?php endif; ?>
                        </div>
                        <div class="section">
                            <h2>Expériences professionnelles</h2>
                            <?php foreach ($experiences as $experience): ?>
                                <div class="item">
                                    <p class="date"><?= e($experience['debut']) ?> - <?= e($experience['fin']) ?></p>
                                    <p><?= nl2br(e($experience['texte'])) ?></p>
                                </div>
                            <?php endforeach; ?>
                            <?php if (count($experiences) === 0): ?><p class="muted">Non renseigné</p><?php endif; ?>
                        </div>
                    </td>
                </tr>
            </table>
        <?php else: ?>
            <div class="section">
                <h2>Profil</h2>
                <p><?= nl2br(e($descriptionAffiche)) ?></p>
            </div>

            <table class="two-columns">
                <tr>
                    <td class="left-column">
                        <div class="section">
                            <h2>Compétences</h2>
                            <?= afficherListe($competences) ?>
                        </div>
                        <div class="section">
                            <h2>Langues</h2>
                            <?= afficherListe($langues) ?>
                        </div>
                        <div class="section">
                            <h2>Centres d'intérêts</h2>
                            <?= afficherListe($centresInteret) ?>
                        </div>
                    </td>
                    <td class="right-column">
                        <div class="section">
                            <h2>Formations</h2>
                            <?php foreach ($formations as $formation): ?>
                                <div class="item">
                                    <p class="date"><?= e($formation['debut']) ?> - <?= e($formation['fin']) ?></p>
                                    <p><?= e($formation['titre']) ?></p>
                                </div>
                            <?php endforeach; ?>
                            <?php if (count($formations) === 0): ?><p class="muted">Non renseigné</p><?php endif; ?>
                        </div>
                        <div class="section">
                            <h2>Expériences professionnelles</h2>
                            <?php foreach ($experiences as $experience): ?>
                                <div class="item">
                                    <p class="date"><?= e($experience['debut']) ?> - <?= e($experience['fin']) ?></p>
                                    <p><?= nl2br(e($experience['texte'])) ?></p>
                                </div>
                            <?php endforeach; ?>
                            <?php if (count($experiences) === 0): ?><p class="muted">Non renseigné</p><?php endif; ?>
                        </div>
                    </td>
                </tr>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
