// Ce fichier JavaScript reste volontairement simple.
// Objectif : mettre à jour la prévisualisation du CV sans recharger la page.

// 1) Prévisualisation automatique des textes
const champs = document.querySelectorAll(".input");

champs.forEach(function (champ) {
    const idPrevisualisation = champ.dataset.previsualisation;
    const texteParDefaut = champ.dataset.textepardefaut;
    const zoneCV = document.getElementById(idPrevisualisation);

    if (!zoneCV) {
        return;
    }

    function mettreAJourTexte() {
        const texteSaisi = champ.value.trim();

        if (texteSaisi === "") {
            zoneCV.textContent = texteParDefaut;
        } else {
            zoneCV.textContent = texteSaisi;
        }
    }

    champ.addEventListener("input", mettreAJourTexte);
    mettreAJourTexte();
});

// 2) Changement de modèle de CV
const selectModele = document.getElementById("modele");
const carteCV = document.getElementById("previsualisation_cv");

function appliquerModele() {
    Array.from(carteCV.classList).forEach(function (classe) {
        if (classe.startsWith("modele-")) carteCV.classList.remove(classe);
    });
    carteCV.classList.add("modele-" + selectModele.value);
}
selectModele.addEventListener("change", appliquerModele);

// 3) Les mêmes palettes sont utilisées dans l'aperçu et le PDF.
const palettesCouleurs = JSON.parse(document.getElementById("palettes-cv").textContent);

function appliquerCouleur(theme) {
    const palette = palettesCouleurs[theme];

    if (!palette) {
        return;
    }

    carteCV.style.setProperty("--couleur-cv", palette[1]);
    carteCV.style.setProperty("--couleur-cv-clair", palette[2]);
    carteCV.style.setProperty("--couleur-cv-milieu", palette[3]);
    carteCV.style.setProperty("--couleur-cv-texte", palette[4]);
}

const choixCouleurs = document.querySelectorAll('input[name="couleur"]');
const cartesCouleurs = document.querySelectorAll(".theme-choice");

choixCouleurs.forEach(function (choix) {
    choix.addEventListener("change", function () {
        appliquerCouleur(choix.dataset.theme);

        cartesCouleurs.forEach(function (carte) {
            carte.classList.remove("is-active");
        });

        choix.closest(".theme-choice").classList.add("is-active");
    });
});

// Le mode ATS conserve les couleurs et applique une lecture sur une colonne.
const optionATS = document.getElementById("ats");
function appliquerATS() {
    carteCV.classList.toggle("mode-ats", optionATS.checked);
    document.getElementById("conseil_ats").textContent = optionATS.checked
        ? "Une colonne, sans photo, avec du texte sélectionnable. Le style et la couleur choisis sont conservés."
        : "Mode visuel : la photo et les deux colonnes sont disponibles sur les modèles d’origine. Pour une candidature via un ATS, activez le mode recommandé.";
}
optionATS.addEventListener("change", appliquerATS);

// 4) Prévisualisation de la photo de profil
const inputPhoto = document.getElementById("photo");
const imagePhoto = document.getElementById("photo_preview");
const textePhoto = document.getElementById("photo_placeholder");

const photoConvertie = document.getElementById("photo_convertie");
const statutPhoto = document.getElementById("photo_statut");
const formulaireCV = document.querySelector(".cv-form");
const boutonPDF = formulaireCV.querySelector('button[type="submit"]');
let numeroPhoto = 0;

// N'envoyer que la version utile : le fichier original peut peser 10 Mo.
// L'événement formdata agit sur l'envoi sans effacer la photo sélectionnée.
formulaireCV.addEventListener("formdata", function (evenement) {
    if (optionATS.checked) {
        evenement.formData.delete("photo");
        evenement.formData.delete("photo_convertie");
    } else if (photoConvertie.value !== "") {
        evenement.formData.delete("photo");
    }
});


// Les formats affichables sont rasterisés localement : SVG compris, sans bibliothèque.
// Le serveur prend le relais pour les formats que le navigateur ne décode pas.
function convertirDansNavigateur(fichier) {
    return new Promise(function (resoudre, rejeter) {
        const url = URL.createObjectURL(fichier);
        const image = new Image();
        function terminer() { URL.revokeObjectURL(url); }
        image.onload = function () {
            try {
                if (!image.naturalWidth || !image.naturalHeight) throw new Error("Dimensions illisibles");
                const ratio = Math.min(1, 1000 / Math.max(image.naturalWidth, image.naturalHeight));
                const canvas = document.createElement("canvas");
                canvas.width = Math.max(1, Math.round(image.naturalWidth * ratio));
                canvas.height = Math.max(1, Math.round(image.naturalHeight * ratio));
                const contexte = canvas.getContext("2d");
                contexte.fillStyle = "#ffffff";
                contexte.fillRect(0, 0, canvas.width, canvas.height);
                contexte.drawImage(image, 0, 0, canvas.width, canvas.height);
                const jpeg = canvas.toDataURL("image/jpeg", 0.9);
                if (!jpeg.startsWith("data:image/jpeg;base64,")) throw new Error("Conversion impossible");
                resoudre(jpeg);
            } catch (erreur) { rejeter(erreur); }
            terminer();
        };
        image.onerror = function () { terminer(); rejeter(new Error("Format non décodable dans ce navigateur")); };
        image.src = url;
    });
}

inputPhoto.addEventListener("change", async function () {
    const selection = ++numeroPhoto;
    const erreurPhoto = document.getElementById("photo_erreur");
    erreurPhoto.classList.add("d-none");
    photoConvertie.value = "";
    statutPhoto.textContent = "";
    imagePhoto.classList.add("d-none");
    imagePhoto.removeAttribute("src");
    textePhoto.classList.remove("d-none");
    boutonPDF.disabled = false;
    const fichier = inputPhoto.files[0];
    if (!fichier) return;
    if (fichier.size <= 0 || fichier.size > 10 * 1024 * 1024) {
        inputPhoto.value = "";
        erreurPhoto.textContent = "La photo n’a pas été ajoutée. Choisissez une image de 10 Mo maximum.";
        erreurPhoto.classList.remove("d-none");
        return;
    }
    boutonPDF.disabled = true;
    statutPhoto.textContent = "Préparation de la photo…";
    try {
        let jpeg;
        try {
            jpeg = await convertirDansNavigateur(fichier);
        } catch (erreur) {
            if (selection !== numeroPhoto) return;
            const donnees = new FormData();
            donnees.append("photo", fichier);
            const reponse = await fetch("photo.php", { method: "POST", body: donnees });
            const resultat = await reponse.json();
            if (!reponse.ok || !resultat.photo) throw new Error(resultat.erreur || "Conversion indisponible pour cette image.");
            jpeg = resultat.photo;
        }
        if (selection !== numeroPhoto) return;
        photoConvertie.value = jpeg;
        imagePhoto.src = jpeg;
        imagePhoto.classList.remove("d-none");
        textePhoto.classList.add("d-none");
        statutPhoto.textContent = "Photo prête. Elle sera ajoutée au PDF si le mode ATS est désactivé.";
    } catch (erreur) {
        if (selection !== numeroPhoto) return;
        inputPhoto.value = "";
        statutPhoto.textContent = "";
        erreurPhoto.textContent = "Photo non ajoutée : " + (erreur.message || "format non pris en charge sur cet hébergement.");
        erreurPhoto.classList.remove("d-none");
    } finally {
        if (selection === numeroPhoto) boutonPDF.disabled = false;
    }
});

// 5) Ajouter / supprimer des formations et expériences
// On garde un compteur très simple pour chaque section.
const compteurs = {
    formation: 1,
    experience: 1
};

const maximums = {
    formation: 5,
    experience: 5
};

function afficherElement(id) {
    const element = document.getElementById(id);

    if (element) {
        element.classList.remove("d-none");
        element.querySelectorAll("input, textarea").forEach(function (champ) { champ.disabled = false; });
    }
}

function cacherElement(id) {
    const element = document.getElementById(id);

    if (element) {
        element.classList.add("d-none");
        element.querySelectorAll("input, textarea").forEach(function (champ) { champ.disabled = true; });
    }
}

function ajouterBloc(section) {
    if (compteurs[section] >= maximums[section]) {
        return;
    }

    compteurs[section] = compteurs[section] + 1;
    const numero = compteurs[section];

    afficherElement(section + numero);

    if (section === "formation") {
        afficherElement("previsualisation_formation" + numero);
    }

    if (section === "experience") {
        afficherElement("previsualisation_experiencepro" + numero);
    }
}

function supprimerBloc(section) {
    if (compteurs[section] <= 1) {
        return;
    }

    const numero = compteurs[section];

    cacherElement(section + numero);

    if (section === "formation") {
        cacherElement("previsualisation_formation" + numero);
    }

    if (section === "experience") {
        cacherElement("previsualisation_experiencepro" + numero);
    }

    compteurs[section] = compteurs[section] - 1;
}

const boutonsAjouter = document.querySelectorAll(".boutonAjouter");
const boutonsSupprimer = document.querySelectorAll(".boutonSupprimer");

boutonsAjouter.forEach(function (bouton) {
    bouton.addEventListener("click", function () {
        const section = bouton.dataset.section;
        ajouterBloc(section);
    });
});

boutonsSupprimer.forEach(function (bouton) {
    bouton.addEventListener("click", function () {
        const section = bouton.dataset.section;
        supprimerBloc(section);
    });
});

// Initialisation (y compris lors d'un retour du navigateur).
function initialiserApercu() {
    appliquerModele();
    appliquerATS();
    const couleur = document.querySelector('input[name="couleur"]:checked');
    if (couleur) {
        appliquerCouleur(couleur.dataset.theme);
        cartesCouleurs.forEach(function (carte) {
            carte.classList.toggle("is-active", carte.contains(couleur));
        });
    }
    champs.forEach(function (champ) { champ.dispatchEvent(new Event("input")); });
}
document.querySelectorAll(".section-card.d-none").forEach(function (bloc) {
    bloc.querySelectorAll("input, textarea").forEach(function (champ) { champ.disabled = true; });
});
window.addEventListener("pageshow", initialiserApercu);
initialiserApercu();
