<?php require __DIR__ . '/config.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="assets/css/index.css">
    <script src="assets/js/script.js" defer></script>
    <title>Générateur de CV moderne</title>
    <script id="palettes-cv" type="application/json"><?= json_encode($palettes, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
</head>
<body>
    <main class="page-shell px-3">
        <section class="hero-zone">
            <p class="eyebrow">Générateur de CV</p>
            <h1 class="app-title">Créez un CV moderne en quelques minutes</h1>
            <p class="app-subtitle">Choisissez parmi 9 modèles et 16 couleurs, puis téléchargez votre CV en PDF texte.</p>
        </section>

        <div class="container-fluid app-layout">
            <div class="form-panel">
                <form action="export.php" method="post" enctype="multipart/form-data" class="cv-form">
                    <h2>Personnalisation du CV</h2>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="modele" class="form-label">Modèle de CV</label>
                            <select name="modele" id="modele" class="form-select">
                                <?php foreach ($modeles as $valeur => $libelle): ?>
                                    <option value="<?= $valeur ?>"><?= $libelle ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Couleur principale</label>
                            <div class="theme-grid" aria-label="Choix de la couleur du CV">
                                <?php foreach ($palettes as $theme => $palette): ?>
                                    <label class="theme-choice<?= $theme === 'bleu' ? ' is-active' : '' ?>" style="--pastille-claire: <?= $palette[2] ?>; --pastille-milieu: <?= $palette[3] ?>; --pastille: <?= $palette[1] ?>;">
                                        <input type="radio" name="couleur" value="<?= $palette[1] ?>" data-theme="<?= $theme ?>" <?= $theme === 'bleu' ? 'checked' : '' ?>>
                                        <span><?= $palette[0] ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="section-card mt-3">
                        <input type="hidden" name="ats" value="0">
                        <label class="form-check-label" for="ats"><input type="checkbox" class="form-check-input me-2" id="ats" name="ats" value="1" checked>Favoriser la lecture par les ATS (recommandé)</label>
                        <p class="form-help" id="conseil_ats" role="status">Une colonne, sans photo, avec du texte sélectionnable. Le style et la couleur choisis sont conservés.</p>
                        <p class="form-help">Indiquez le poste, l’entreprise et les missions pour chaque expérience ; le diplôme et l’établissement pour chaque formation. Saisissez les entrées de la plus récente à la plus ancienne et utilisez les mots-clés de l’offre qui correspondent à votre parcours. Aucun résultat auprès de tous les ATS ne peut être garanti.</p>
                    </div>

                    <div class="mt-3">
                        <label for="photo" class="form-label">Photo de profil optionnelle</label>
                        <input type="file" name="photo" id="photo" class="form-control" accept="image/*,.heic,.heif,.avif,.tif,.tiff,.bmp,.ico,.svg,.webp,.gif,.jfif,.jxl,.jp2,.psd">
                        <input type="hidden" name="photo_convertie" id="photo_convertie">
                        <p id="photo_statut" class="form-help" role="status" aria-live="polite"></p>
                        <p id="photo_erreur" class="form-help text-danger d-none" role="alert"></p>
                        <p class="form-help">Images jusqu’à 10 Mo : JPG, PNG, WebP, GIF, BMP, SVG, AVIF… HEIC/HEIF, TIFF et autres formats selon les décodeurs disponibles. La photo est convertie automatiquement et reste masquée en mode ATS.</p>
                    </div>

                    <h2>Informations personnelles</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="prenom" class="form-label">Prénom</label>
                            <input type="text" name="prenom" class="input form-control" id="prenom" data-previsualisation="previsualisation_prenom" data-textepardefaut="Prénom" placeholder="Elena">
                        </div>
                        <div class="col-md-6">
                            <label for="nom" class="form-label">Nom</label>
                            <input type="text" name="nom" class="input form-control" id="nom" data-previsualisation="previsualisation_nom" data-textepardefaut="Nom" placeholder="Serine">
                        </div>
                        <div class="col-md-12">
                            <label for="adresse" class="form-label">Adresse</label>
                            <input type="text" name="adresse" class="input form-control" id="adresse" data-previsualisation="previsualisation_adresse" data-textepardefaut="Adresse" placeholder="7 Avenue des Cannassons">
                        </div>
                        <div class="col-md-6">
                            <label for="numero" class="form-label">Téléphone</label>
                            <input type="text" name="numero" class="input form-control" id="numero" data-previsualisation="previsualisation_numero" data-textepardefaut="Téléphone" placeholder="06.00.00.00.00">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" class="input form-control" id="email" data-previsualisation="previsualisation_email" data-textepardefaut="Email" placeholder="nom.prenom@email.com">
                        </div>
                        <div class="col-md-4">
                            <label for="age" class="form-label">Âge</label>
                            <input type="number" name="age" class="input form-control" id="age" data-previsualisation="previsualisation_age" data-textepardefaut="Âge" placeholder="26">
                        </div>
                        <div class="col-md-8">
                            <label for="titre" class="form-label">Titre du CV</label>
                            <input type="text" name="titre" class="input form-control" id="titre" data-previsualisation="previsualisation_titre" data-textepardefaut="Titre du CV" placeholder="Développeur web junior">
                        </div>
                        <div class="col-md-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" class="input form-control" id="description" data-previsualisation="previsualisation_description" data-textepardefaut="Courte présentation" placeholder="Décrivez votre profil en quelques lignes."></textarea>
                        </div>
                    </div>

                    <h2>Formations</h2>
                    <p class="form-help mb-3">Dates au choix : 2025, 01/2025 ou Aujourd’hui.</p>
                    <div id="formation1" class="section-card">
                        <p class="section-number">Formation 1</p>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="anneedebut1" class="form-label">Début</label>
                                <input type="text" name="anneedebut1" class="input form-control" id="anneedebut1" data-previsualisation="previsualisation_anneedebut1" data-textepardefaut="Début" placeholder="2025">
                            </div>
                            <div class="col-md-3">
                                <label for="anneefin1" class="form-label">Fin</label>
                                <input type="text" name="anneefin1" class="input form-control" id="anneefin1" data-previsualisation="previsualisation_anneefin1" data-textepardefaut="Fin" placeholder="2027">
                            </div>
                            <div class="col-md-6">
                                <label for="titreformation1" class="form-label">Titre de la formation</label>
                                <input type="text" name="titreformation1" class="input form-control" id="titreformation1" data-previsualisation="previsualisation_titreformation1" data-textepardefaut="Titre de la formation" placeholder="Bachelor Développement Web — Établissement">
                            </div>
                        </div>
                    </div>

                    <div id="formation2" class="section-card d-none">
                        <p class="section-number">Formation 2</p>
                        <div class="row g-3">
                            <div class="col-md-3"><label for="anneedebut2" class="form-label">Début</label><input type="text" name="anneedebut2" class="input form-control" id="anneedebut2" data-previsualisation="previsualisation_anneedebut2" data-textepardefaut="Début" placeholder="2023"></div>
                            <div class="col-md-3"><label for="anneefin2" class="form-label">Fin</label><input type="text" name="anneefin2" class="input form-control" id="anneefin2" data-previsualisation="previsualisation_anneefin2" data-textepardefaut="Fin" placeholder="2025"></div>
                            <div class="col-md-6"><label for="titreformation2" class="form-label">Titre de la formation</label><input type="text" name="titreformation2" class="input form-control" id="titreformation2" data-previsualisation="previsualisation_titreformation2" data-textepardefaut="Titre de la formation" placeholder="BTS SIO"></div>
                        </div>
                    </div>

                    <div id="formation3" class="section-card d-none">
                        <p class="section-number">Formation 3</p>
                        <div class="row g-3">
                            <div class="col-md-3"><label for="anneedebut3" class="form-label">Début</label><input type="text" name="anneedebut3" class="input form-control" id="anneedebut3" data-previsualisation="previsualisation_anneedebut3" data-textepardefaut="Début" placeholder="2021"></div>
                            <div class="col-md-3"><label for="anneefin3" class="form-label">Fin</label><input type="text" name="anneefin3" class="input form-control" id="anneefin3" data-previsualisation="previsualisation_anneefin3" data-textepardefaut="Fin" placeholder="2023"></div>
                            <div class="col-md-6"><label for="titreformation3" class="form-label">Titre de la formation</label><input type="text" name="titreformation3" class="input form-control" id="titreformation3" data-previsualisation="previsualisation_titreformation3" data-textepardefaut="Titre de la formation" placeholder="Licence informatique"></div>
                        </div>
                    </div>

                    <div id="formation4" class="section-card d-none">
                        <p class="section-number">Formation 4</p>
                        <div class="row g-3">
                            <div class="col-md-3"><label for="anneedebut4" class="form-label">Début</label><input type="text" name="anneedebut4" class="input form-control" id="anneedebut4" data-previsualisation="previsualisation_anneedebut4" data-textepardefaut="Début" placeholder="2019"></div>
                            <div class="col-md-3"><label for="anneefin4" class="form-label">Fin</label><input type="text" name="anneefin4" class="input form-control" id="anneefin4" data-previsualisation="previsualisation_anneefin4" data-textepardefaut="Fin" placeholder="2021"></div>
                            <div class="col-md-6"><label for="titreformation4" class="form-label">Titre de la formation</label><input type="text" name="titreformation4" class="input form-control" id="titreformation4" data-previsualisation="previsualisation_titreformation4" data-textepardefaut="Titre de la formation" placeholder="Certification professionnelle"></div>
                        </div>
                    </div>

                    <div id="formation5" class="section-card d-none">
                        <p class="section-number">Formation 5</p>
                        <div class="row g-3">
                            <div class="col-md-3"><label for="anneedebut5" class="form-label">Début</label><input type="text" name="anneedebut5" class="input form-control" id="anneedebut5" data-previsualisation="previsualisation_anneedebut5" data-textepardefaut="Début" placeholder="2018"></div>
                            <div class="col-md-3"><label for="anneefin5" class="form-label">Fin</label><input type="text" name="anneefin5" class="input form-control" id="anneefin5" data-previsualisation="previsualisation_anneefin5" data-textepardefaut="Fin" placeholder="2019"></div>
                            <div class="col-md-6"><label for="titreformation5" class="form-label">Titre de la formation</label><input type="text" name="titreformation5" class="input form-control" id="titreformation5" data-previsualisation="previsualisation_titreformation5" data-textepardefaut="Titre de la formation" placeholder="Formation courte"></div>
                        </div>
                    </div>

                    <button type="button" class="boutonAjouter btn btn-primary rounded-pill" data-section="formation">Ajouter une formation</button>
                    <button type="button" class="boutonSupprimer btn btn-light rounded-pill" data-section="formation">Supprimer une formation</button>

                    <h2>Expériences professionnelles</h2>
                    <div id="experience1" class="section-card">
                        <p class="section-number">Expérience 1</p>
                        <div class="row g-3">
                            <div class="col-md-3"><label for="anneedebutexppro1" class="form-label">Début</label><input type="text" name="anneedebutexppro1" class="input form-control" id="anneedebutexppro1" data-previsualisation="previsualisation_anneedebutexppro1" data-textepardefaut="Début" placeholder="2025"></div>
                            <div class="col-md-3"><label for="anneefinexppro1" class="form-label">Fin</label><input type="text" name="anneefinexppro1" class="input form-control" id="anneefinexppro1" data-previsualisation="previsualisation_anneefinexppro1" data-textepardefaut="Fin" placeholder="2026"></div>
                            <div class="col-md-6"><label for="experience1texte" class="form-label">Description</label><textarea name="experience1" class="input form-control" id="experience1texte" data-previsualisation="previsualisation_experience1" data-textepardefaut="Description de l'expérience" placeholder="Poste — Entreprise, ville. Décrivez vos missions et résultats."></textarea></div>
                        </div>
                    </div>

                    <div id="experience2" class="section-card d-none">
                        <p class="section-number">Expérience 2</p>
                        <div class="row g-3">
                            <div class="col-md-3"><label for="anneedebutexppro2" class="form-label">Début</label><input type="text" name="anneedebutexppro2" class="input form-control" id="anneedebutexppro2" data-previsualisation="previsualisation_anneedebutexppro2" data-textepardefaut="Début" placeholder="2024"></div>
                            <div class="col-md-3"><label for="anneefinexppro2" class="form-label">Fin</label><input type="text" name="anneefinexppro2" class="input form-control" id="anneefinexppro2" data-previsualisation="previsualisation_anneefinexppro2" data-textepardefaut="Fin" placeholder="2025"></div>
                            <div class="col-md-6"><label for="experience2texte" class="form-label">Description</label><textarea name="experience2" class="input form-control" id="experience2texte" data-previsualisation="previsualisation_experience2" data-textepardefaut="Description de l'expérience" placeholder="Poste — Entreprise, ville. Décrivez vos missions et résultats."></textarea></div>
                        </div>
                    </div>

                    <div id="experience3" class="section-card d-none">
                        <p class="section-number">Expérience 3</p>
                        <div class="row g-3">
                            <div class="col-md-3"><label for="anneedebutexppro3" class="form-label">Début</label><input type="text" name="anneedebutexppro3" class="input form-control" id="anneedebutexppro3" data-previsualisation="previsualisation_anneedebutexppro3" data-textepardefaut="Début" placeholder="2023"></div>
                            <div class="col-md-3"><label for="anneefinexppro3" class="form-label">Fin</label><input type="text" name="anneefinexppro3" class="input form-control" id="anneefinexppro3" data-previsualisation="previsualisation_anneefinexppro3" data-textepardefaut="Fin" placeholder="2024"></div>
                            <div class="col-md-6"><label for="experience3texte" class="form-label">Description</label><textarea name="experience3" class="input form-control" id="experience3texte" data-previsualisation="previsualisation_experience3" data-textepardefaut="Description de l'expérience" placeholder="Poste — Entreprise, ville. Décrivez vos missions et résultats."></textarea></div>
                        </div>
                    </div>

                    <div id="experience4" class="section-card d-none">
                        <p class="section-number">Expérience 4</p>
                        <div class="row g-3">
                            <div class="col-md-3"><label for="anneedebutexppro4" class="form-label">Début</label><input type="text" name="anneedebutexppro4" class="input form-control" id="anneedebutexppro4" data-previsualisation="previsualisation_anneedebutexppro4" data-textepardefaut="Début" placeholder="2022"></div>
                            <div class="col-md-3"><label for="anneefinexppro4" class="form-label">Fin</label><input type="text" name="anneefinexppro4" class="input form-control" id="anneefinexppro4" data-previsualisation="previsualisation_anneefinexppro4" data-textepardefaut="Fin" placeholder="2023"></div>
                            <div class="col-md-6"><label for="experience4texte" class="form-label">Description</label><textarea name="experience4" class="input form-control" id="experience4texte" data-previsualisation="previsualisation_experience4" data-textepardefaut="Description de l'expérience" placeholder="Poste — Entreprise, ville. Décrivez vos missions et résultats."></textarea></div>
                        </div>
                    </div>

                    <div id="experience5" class="section-card d-none">
                        <p class="section-number">Expérience 5</p>
                        <div class="row g-3">
                            <div class="col-md-3"><label for="anneedebutexppro5" class="form-label">Début</label><input type="text" name="anneedebutexppro5" class="input form-control" id="anneedebutexppro5" data-previsualisation="previsualisation_anneedebutexppro5" data-textepardefaut="Début" placeholder="2021"></div>
                            <div class="col-md-3"><label for="anneefinexppro5" class="form-label">Fin</label><input type="text" name="anneefinexppro5" class="input form-control" id="anneefinexppro5" data-previsualisation="previsualisation_anneefinexppro5" data-textepardefaut="Fin" placeholder="2022"></div>
                            <div class="col-md-6"><label for="experience5texte" class="form-label">Description</label><textarea name="experience5" class="input form-control" id="experience5texte" data-previsualisation="previsualisation_experience5" data-textepardefaut="Description de l'expérience" placeholder="Poste — Entreprise, ville. Décrivez vos missions et résultats."></textarea></div>
                        </div>
                    </div>

                    <button type="button" class="boutonAjouter btn btn-primary rounded-pill" data-section="experience">Ajouter une expérience</button>
                    <button type="button" class="boutonSupprimer btn btn-light rounded-pill" data-section="experience">Supprimer une expérience</button>

                    <h2>Compétences</h2>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="competence1">Compétence 1</label><input type="text" name="competence1" class="input form-control" id="competence1" data-previsualisation="previsualisation_competence1" data-textepardefaut="Compétence" placeholder="HTML / CSS"></div>
                        <div class="col-md-4"><label class="form-label" for="competence2">Compétence 2</label><input type="text" name="competence2" class="input form-control" id="competence2" data-previsualisation="previsualisation_competence2" data-textepardefaut="Compétence" placeholder="PHP"></div>
                        <div class="col-md-4"><label class="form-label" for="competence3">Compétence 3</label><input type="text" name="competence3" class="input form-control" id="competence3" data-previsualisation="previsualisation_competence3" data-textepardefaut="Compétence" placeholder="JavaScript"></div>
                    </div>

                    <h2>Langues</h2>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="langue1">Langue 1</label><input type="text" name="langue1" class="input form-control" id="langue1" data-previsualisation="previsualisation_langue1" data-textepardefaut="Langue" placeholder="Français"></div>
                        <div class="col-md-4"><label class="form-label" for="langue2">Langue 2</label><input type="text" name="langue2" class="input form-control" id="langue2" data-previsualisation="previsualisation_langue2" data-textepardefaut="Langue" placeholder="Anglais"></div>
                        <div class="col-md-4"><label class="form-label" for="langue3">Langue 3</label><input type="text" name="langue3" class="input form-control" id="langue3" data-previsualisation="previsualisation_langue3" data-textepardefaut="Langue" placeholder="Espagnol"></div>
                    </div>

                    <h2>Centres d’intérêt</h2>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="centreinteret1">Centre d'intérêt 1</label><input type="text" name="centreinteret1" class="input form-control" id="centreinteret1" data-previsualisation="previsualisation_centreinteret1" data-textepardefaut="Centre d'intérêt" placeholder="Sport"></div>
                        <div class="col-md-4"><label class="form-label" for="centreinteret2">Centre d'intérêt 2</label><input type="text" name="centreinteret2" class="input form-control" id="centreinteret2" data-previsualisation="previsualisation_centreinteret2" data-textepardefaut="Centre d'intérêt" placeholder="Voyage"></div>
                        <div class="col-md-4"><label class="form-label" for="centreinteret3">Centre d'intérêt 3</label><input type="text" name="centreinteret3" class="input form-control" id="centreinteret3" data-previsualisation="previsualisation_centreinteret3" data-textepardefaut="Centre d'intérêt" placeholder="Lecture"></div>
                    </div>

                    <div class="mt-4">
                        <label for="nom_fichier" class="form-label">Nom du fichier PDF</label>
                        <input type="text" name="nom_fichier" id="nom_fichier" class="form-control" maxlength="100" placeholder="CV-Teddy-Sibu" aria-describedby="aide_nom_fichier">
                        <p id="aide_nom_fichier" class="form-help">L’extension .pdf est ajoutée automatiquement. Si ce champ est vide, le fichier sera nommé cv.pdf.</p>
                    </div>

                    <button class="btn btn-primary btn-lg rounded-pill mt-4" type="submit">Télécharger mon CV en PDF</button>
                </form>
            </div>

            <aside class="preview-panel">
                <div id="previsualisation_cv" class="preview-card modele-moderne mode-ats" style="--couleur-cv: #4fb9f5; --couleur-cv-clair: #dff5ff; --couleur-cv-milieu: #8edcff; --couleur-cv-texte: #135d9d;">
                    <p class="preview-label">Prévisualisation</p>
                    <p class="form-help mb-3">Aperçu indicatif : les sauts de page sont calculés dans le PDF A4.</p>

                    <div class="preview-top">
                        <div class="photo-preview" id="bloc_photo_preview">
                            <img src="" alt="Photo de profil" id="photo_preview" class="d-none">
                            <span id="photo_placeholder">Photo</span>
                        </div>
                        <div>
                            <div class="preview-name"><span id="previsualisation_prenom">Prénom</span> <span id="previsualisation_nom">Nom</span></div>
                            <p id="previsualisation_titre" class="preview-title">Titre du CV</p>
                            <p class="preview-contact"><span id="previsualisation_adresse">Adresse</span> · <span id="previsualisation_numero">Téléphone</span> · <span id="previsualisation_email">Email</span> · <span id="previsualisation_age">Âge</span></p>
                        </div>
                    </div>

                    <div class="preview-layout">
                        <div class="preview-sidebar">
                            <h2>Compétences</h2>
                            <ul>
                                <li id="previsualisation_competence1">Compétence</li>
                                <li id="previsualisation_competence2">Compétence</li>
                                <li id="previsualisation_competence3">Compétence</li>
                            </ul>

                            <h2>Langues</h2>
                            <ul>
                                <li id="previsualisation_langue1">Langue</li>
                                <li id="previsualisation_langue2">Langue</li>
                                <li id="previsualisation_langue3">Langue</li>
                            </ul>

                            <h2>Centres d’intérêt</h2>
                            <ul>
                                <li id="previsualisation_centreinteret1">Centre d'intérêt</li>
                                <li id="previsualisation_centreinteret2">Centre d'intérêt</li>
                                <li id="previsualisation_centreinteret3">Centre d'intérêt</li>
                            </ul>
                        </div>

                        <div class="preview-main">
                            <h2>Profil</h2>
                            <p id="previsualisation_description">Courte présentation</p>

                            <h2>Expériences professionnelles</h2>
                            <div id="previsualisation_experiencepro1" class="preview-item">
                                <p class="preview-date"><span id="previsualisation_anneedebutexppro1">Début</span> - <span id="previsualisation_anneefinexppro1">Fin</span></p>
                                <p id="previsualisation_experience1">Description de l'expérience</p>
                            </div>
                            <div id="previsualisation_experiencepro2" class="preview-item d-none"><p class="preview-date"><span id="previsualisation_anneedebutexppro2">Début</span> - <span id="previsualisation_anneefinexppro2">Fin</span></p><p id="previsualisation_experience2">Description de l'expérience</p></div>
                            <div id="previsualisation_experiencepro3" class="preview-item d-none"><p class="preview-date"><span id="previsualisation_anneedebutexppro3">Début</span> - <span id="previsualisation_anneefinexppro3">Fin</span></p><p id="previsualisation_experience3">Description de l'expérience</p></div>
                            <div id="previsualisation_experiencepro4" class="preview-item d-none"><p class="preview-date"><span id="previsualisation_anneedebutexppro4">Début</span> - <span id="previsualisation_anneefinexppro4">Fin</span></p><p id="previsualisation_experience4">Description de l'expérience</p></div>
                            <div id="previsualisation_experiencepro5" class="preview-item d-none"><p class="preview-date"><span id="previsualisation_anneedebutexppro5">Début</span> - <span id="previsualisation_anneefinexppro5">Fin</span></p><p id="previsualisation_experience5">Description de l'expérience</p></div>

                            <h2>Formations</h2>
                            <div id="previsualisation_formation1" class="preview-item">
                                <p class="preview-date"><span id="previsualisation_anneedebut1">Début</span> - <span id="previsualisation_anneefin1">Fin</span></p>
                                <p id="previsualisation_titreformation1">Titre de la formation</p>
                            </div>
                            <div id="previsualisation_formation2" class="preview-item d-none"><p class="preview-date"><span id="previsualisation_anneedebut2">Début</span> - <span id="previsualisation_anneefin2">Fin</span></p><p id="previsualisation_titreformation2">Titre de la formation</p></div>
                            <div id="previsualisation_formation3" class="preview-item d-none"><p class="preview-date"><span id="previsualisation_anneedebut3">Début</span> - <span id="previsualisation_anneefin3">Fin</span></p><p id="previsualisation_titreformation3">Titre de la formation</p></div>
                            <div id="previsualisation_formation4" class="preview-item d-none"><p class="preview-date"><span id="previsualisation_anneedebut4">Début</span> - <span id="previsualisation_anneefin4">Fin</span></p><p id="previsualisation_titreformation4">Titre de la formation</p></div>
                            <div id="previsualisation_formation5" class="preview-item d-none"><p class="preview-date"><span id="previsualisation_anneedebut5">Début</span> - <span id="previsualisation_anneefin5">Fin</span></p><p id="previsualisation_titreformation5">Titre de la formation</p></div>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </main>
</body>
</html>
