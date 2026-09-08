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
// Chaque couleur possède : une couleur principale, deux couleurs claires et une couleur de texte.
// On garde volontairement un objet simple pour que ce soit facile à modifier.
const palettesCouleurs = {
    bleu: {
        principale: "#4fb9f5",
        claire: "#dff5ff",
        milieu: "#8edcff",
        texte: "#135d9d"
    },
    rouge: {
        principale: "#ff7f92",
        claire: "#ffe5e8",
        milieu: "#ffb8c2",
        texte: "#a9364b"
    },
    orange: {
        principale: "#ffab66",
        claire: "#fff0dd",
        milieu: "#ffd0a3",
        texte: "#a85824"
    },
    rose: {
        principale: "#f48cc5",
        claire: "#ffe8f6",
        milieu: "#ffc4e4",
        texte: "#984878"
    },
    jaune: {
        principale: "#ffd84f",
        claire: "#fff8d8",
        milieu: "#ffe98f",
        texte: "#8a6a16"
    },
    vert: {
        principale: "#78daa0",
        claire: "#e4fbeb",
        milieu: "#b8efc8",
        texte: "#247c51"
    }
};

function appliquerCouleur(theme) {
    const palette = palettesCouleurs[theme];

    if (!palette) {
        return;
    }

    carteCV.style.setProperty("--couleur-cv", palette.principale);
    carteCV.style.setProperty("--couleur-cv-clair", palette.claire);
    carteCV.style.setProperty("--couleur-cv-milieu", palette.milieu);
    carteCV.style.setProperty("--couleur-cv-texte", palette.texte);
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
