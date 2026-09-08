# Générateur de CV - Version moderne

Cette version ajoute :

- 3 modèles de CV : Moderne, Classique, Sidebar
- un choix de couleur principale
- une prévisualisation en direct
- l'ajout d'une photo de profil JPG/PNG
- l'ajout jusqu'à 5 formations
- l'ajout jusqu'à 5 expériences professionnelles
- l'export du CV en PDF avec Dompdf

## Fichiers principaux

- `index.php` : formulaire + prévisualisation
- `assets/css/index.css` : design du site
- `assets/js/script.js` : JavaScript simple et commenté
- `export.php` : récupération du formulaire + génération PDF
- `cv.php` : modèle HTML du PDF

## Mise en ligne sur o2switch

1. Dézipper le dossier.
2. Envoyer tout le contenu dans `public_html` ou dans le dossier du sous-domaine.
3. Remplacer les anciens fichiers.
4. Vérifier que l'extension PHP `mbstring` est activée si l'export PDF ne fonctionne pas.

