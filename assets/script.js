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

selectModele.addEventListener("change", function () {
    carteCV.classList.remove("modele-moderne", "modele-classique", "modele-sidebar");
    carteCV.classList.add("modele-" + selectModele.value);
});

// 3) Changement de couleur principale
const inputCouleur = document.getElementById("couleur");

inputCouleur.addEventListener("input", function () {
    carteCV.style.setProperty("--couleur-cv", inputCouleur.value);
});

// 4) Prévisualisation de la photo de profil
const inputPhoto = document.getElementById("photo");
const imagePhoto = document.getElementById("photo_preview");
const textePhoto = document.getElementById("photo_placeholder");

inputPhoto.addEventListener("change", function () {
    const fichier = inputPhoto.files[0];

    if (!fichier) {
        imagePhoto.classList.add("d-none");
        textePhoto.classList.remove("d-none");
        return;
    }

    const lecteur = new FileReader();

    lecteur.onload = function () {
        imagePhoto.src = lecteur.result;
        imagePhoto.classList.remove("d-none");
        textePhoto.classList.add("d-none");
    };

    lecteur.readAsDataURL(fichier);
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
    }
}

function cacherElement(id) {
    const element = document.getElementById(id);

    if (element) {
        element.classList.add("d-none");
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
