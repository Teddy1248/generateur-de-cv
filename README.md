# Générateur de CV — 9 modèles, 16 couleurs

Le champ **Nom du fichier PDF**, juste avant le bouton de téléchargement, permet de choisir le nom du CV exporté, par exemple `CV-Teddy-Sibu`. L’extension `.pdf` est ajoutée automatiquement, même si elle a déjà été saisie. Un champ vide conserve le nom `cv.pdf`. Les accents, espaces, chiffres, tirets, traits de soulignement et parenthèses sont conservés ; les autres caractères sont remplacés et la longueur est limitée côté serveur. Ce choix ne change pas le titre inscrit dans le CV.

Le formulaire, les cartes pastel, la grande prévisualisation, les boutons et les technologies d’origine sont conservés : PHP, CSS, JavaScript sans framework et Dompdf. Aucune nouvelle dépendance.

## Modèles et couleurs

- Modèles d’origine : Moderne, Classique, Sidebar.
- Nouveaux modèles : Épuré (filet discret), Élégant (typographie serif), Professionnel (titres sur fond clair), Compact (espacements réduits), Académique (titres soulignés), Essentiel (accents latéraux).
- Couleurs : Bleu, Rouge, Orange, Rose, Jaune, Vert, Violet, Lavande, Prune, Corail, Turquoise, Menthe, Sauge, Beige, Marine, Ardoise.
- Les palettes pastel et leurs couleurs de texte contrastées sont partagées par le formulaire et le PDF dans `config.php`.

## Mode ATS recommandé

L’option « Favoriser la lecture par les ATS » est activée par défaut pour les neuf modèles. Elle conserve les variations visuelles du modèle et la palette, tout en générant :

- une seule colonne, sans tableau de mise en page ni photo ;
- du véritable texte sélectionnable et copiable dans le PDF A4 ;
- des coordonnées dans le corps du document, avec des libellés explicites ;
- les rubriques Profil, Expériences professionnelles, Formations, Compétences, Langues, Centres d’intérêt, lorsque ces informations sont renseignées ;
- une police classique avec prise en charge des accents, sans espacement artificiel des lettres ;
- aucune donnée fictive ou rubrique « Non renseigné » dans le document exporté.

Les six nouveaux modèles restent sur une colonne même si l’option est désactivée. Les trois modèles d’origine retrouvent alors leurs deux colonnes et autorisent la photo, comme les six autres modèles. Le mode visuel à deux colonnes est moins prudent pour une candidature via un ATS.

**Il ne s’agit pas d’une certification ATS ni d’une garantie de sélection.** Le logiciel utilisé par le recruteur, le contenu du CV et sa correspondance avec l’offre restent déterminants. L’aperçu du site est indicatif : la pagination est calculée à l’export PDF. Le générateur n’impose pas une page ; un parcours long peut en occuper plusieurs.

Conseils : saisir chaque expérience avec son intitulé de poste, l’entreprise, la ville et les missions ; chaque formation avec le diplôme et l’établissement. Utiliser des dates cohérentes (par exemple 01/2025), ranger les expériences et formations de la plus récente à la plus ancienne, et reprendre les mots-clés pertinents de l’offre sans inventer de compétences. Les champs de dates acceptent maintenant également « Aujourd’hui ». Les données restent dans l’ordre saisi.

Référence utilisée pour les choix de structure : [Greenhouse — Unsuccessful resume parse](https://support.greenhouse.io/hc/en-us/articles/200989175-Unsuccessful-resume-parse), consultée le 15 octobre 2026.

## Correctifs inclus

- Les formations et expériences retirées avec « Supprimer » sont désactivées dans le formulaire et ne sont plus exportées. « Ajouter » réactive leur contenu tant que la page reste ouverte.
- Les blocs masqués sont exclus dès l’initialisation.
- Les couleurs pastel ne sont plus utilisées comme couleur de texte sur fond blanc : chaque palette possède un ton de texte plus foncé.
- Les sauts de ligne sont conservés dans l’aperçu et le PDF.
- Les champs envoyés sous forme de tableaux inattendus sont ignorés côté serveur.
- Les photos acceptent désormais jusqu’à 10 Mo (10 × 1 024 × 1 024 octets). Elles sont préparées en JPEG, redimensionnées à 1 000 pixels maximum sur le grand côté et ignorées à l’export ATS.
- Le PDF ne charge aucune ressource distante.

## Fichiers principaux

- `index.php` : formulaire et aperçu.
- `config.php` : liste commune des modèles et des palettes.
- `assets/css/index.css` : design d’origine et variantes.
- `assets/js/script.js` : aperçu et interactions en JavaScript simple.
- `export.php` : lecture du formulaire et export Dompdf.
- `cv.php` : gabarit du PDF et variantes de présentation.
- `images.php` : validation et conversion des photos en JPEG pour le PDF.
- `photo.php` : conversion de secours pour les formats non décodés par le navigateur.
- `.user.ini` : limites PHP-FPM des envois et de la mémoire.
- `vendor/`, `composer.json`, `composer.lock` : dépendances d’origine, conservées.

Le fichier historique `assets/script.js` n’est pas utilisé par la page. Le script actif est `assets/js/script.js`.

## Mise à jour sur o2switch

1. Faire une sauvegarde des fichiers du site existant.
2. Décompresser l’archive.
3. Envoyer **le contenu** du dossier `generateur-cv-o2switch-ready` dans le dossier du site ou sous-domaine et remplacer les fichiers correspondants. Ne pas oublier `config.php`, `images.php`, `photo.php`, `.user.ini` et `vendor/`. Activer l’affichage des fichiers cachés dans le gestionnaire de fichiers pour envoyer `.user.ini`.
4. Utiliser PHP **8.1 ou ultérieur**, avec les extensions DOM/XML, mbstring et GD pour les photos. Aucun accès à une base de données n’est nécessaire.
5. Recharger la page en vidant le cache navigateur pour récupérer le CSS et le JavaScript modifiés.
6. Remplir un exemple puis télécharger un PDF avec le mode ATS activé. Vérifier les coordonnées, les accents, la présence de toutes les expériences et la pagination. Copier le texte du PDF dans un éditeur pour contrôler son ordre de lecture.
7. Essayer aussi un autre modèle et une autre couleur, puis ajouter et retirer une expérience avant de télécharger à nouveau.

## Vérifications de cette livraison

La syntaxe JavaScript et les interactions principales sont contrôlées avec un DOM de test : choix des neuf modèles, choix des seize palettes, bascule ATS, saisie de texte et ajout/retrait des blocs. La structure HTML et les liaisons entre champs et aperçu sont vérifiées, ainsi que le contraste des couleurs de texte.

Le présent environnement ne dispose pas de PHP : la syntaxe PHP et le rendu final Dompdf ne peuvent pas y être exécutés. Les étapes 6 et 7 ci-dessus restent donc à valider sur l’hébergement PHP ; aucun test auprès d’un ATS propriétaire n’a été réalisé.

Pour lancer le site en local **si PHP est déjà installé** :

```bash
php -d upload_max_filesize=10M -d post_max_size=16M -d memory_limit=256M -S localhost:8000
```

Puis ouvrir `http://localhost:8000` depuis le dossier du projet.

## Photos : formats et fichiers de 10 Mo

La limite de **10 Mo est un maximum**, pas un poids minimum : les petites images restent acceptées. Le sélecteur permet les fichiers image, y compris les extensions HEIC/HEIF, TIFF, SVG, AVIF, WebP, GIF, BMP, ICO, JXL, JP2 et PSD.

1. Le navigateur essaie de décoder l’image et la transforme localement en JPEG sur fond blanc, au maximum 1 000 pixels sur le grand côté, sans agrandir les petites photos. Cela permet notamment l’utilisation de SVG sans envoyer de SVG brut à Dompdf. Les fichiers animés deviennent une photo fixe.
2. Si le navigateur ne peut pas lire le fichier, `photo.php` essaie de le convertir sur le serveur avec Imagick puis GD. HEIC/HEIF, TIFF, JXL, JP2 et PSD dépendent des décodeurs et de la politique ImageMagick réellement disponibles. La première image/page est utilisée.
3. L’aperçu et le PDF utilisent la même image préparée. La conversion retire les métadonnées de la version JPEG, qui reste dans le formulaire ; aucun fichier photo n’est enregistré durablement par le site.
4. En cas de format non décodable, de fichier corrompu ou trop lourd, un message s’affiche et la photo n’est pas ajoutée. Le téléchargement est suspendu pendant la préparation pour éviter un PDF avec une ancienne photo.

**Tous les formats d’image existants ne peuvent pas être garantis sur tous les hébergements.** Le fichier est contrôlé par son contenu ; les fichiers non image ne sont pas admis par la conversion serveur. Les fichiers SVG sont convertis uniquement dans le navigateur. La solution n’ajoute aucune bibliothèque JavaScript ni dépendance Composer. Sans JavaScript, les formats décodables par le serveur restent utilisables à l’export, mais les interactions d’aperçu ne sont pas disponibles.

Sur o2switch, vérifier dans la configuration PHP : `upload_max_filesize = 10M`, `post_max_size = 16M`, et `memory_limit = 256M`. Le `.user.ini` fourni propose ces valeurs pour PHP-FPM/CGI ; l’hébergeur peut imposer ses propres limites et la prise en compte du fichier peut prendre quelques minutes. Le serveur de développement `php -S` nécessite les options `-d` montrées plus haut. GD est recommandé ; Imagick avec ses codecs étend la prise en charge. Le repli GD est limité aux images de 25 mégapixels pour éviter une consommation excessive de mémoire ; les grandes images décodables par le navigateur sont réduites avant l’export.

Vérifications supplémentaires : contrôles JavaScript avec simulation des chemins de conversion (navigateur, serveur, erreur), fichiers en dessous de 10 Mo et exactement à 10 Mo, refus au-dessus, et changement rapide de sélection. Ces contrôles ne remplacent pas l’exécution réelle des décodeurs. PHP et les décodeurs serveur n’ont pas pu être exécutés dans cet environnement ; le rendu PDF, la configuration des envois et les formats disponibles restent à vérifier sur l’hébergement.

Documentation : [PHP GD — imagecreatefromstring](https://www.php.net/manual/fr/function.imagecreatefromstring.php), [Imagick — queryFormats](https://www.php.net/manual/fr/imagick.queryformats.php).

## Correctif : téléchargement bloqué par la limite PHP de 8 Mo

Si le message indique `POST Content-Length ... exceeds the limit of 8388608 bytes`, le serveur local conserve une limite de 8 Mo pour l’envoi total. Arrêter le serveur avec Ctrl + C, puis lancer, depuis le dossier du projet :

```bash
bash demarrer.sh
```

Ce script utilise les options PHP `-d` nécessaires. Le fichier `.user.ini` ne configure pas le serveur de développement démarré avec `php -S`.

Le formulaire ne transmet plus la photo originale à l’export lorsque sa version JPEG préparée est disponible. En mode ATS, aucune version de la photo n’est envoyée à l’export. Le fichier sélectionné est conservé dans le formulaire pour pouvoir changer d’option ensuite. Le transfert initial vers le convertisseur serveur, lorsqu’il est nécessaire pour un format spécial, reste soumis à la configuration des envois de PHP.

Tests : vérification de la charge envoyée dans les trois cas (mode ATS, JPEG préparé, fichier original sans conversion), conservation des champs du CV et de la sélection photo ; contrôle de la syntaxe du script Bash. Le rendu final Dompdf reste à vérifier sur PHP.
