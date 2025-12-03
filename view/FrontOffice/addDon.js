function validerFormulaire() {
    //event.preventDefault();

    let montant = document.getElementById("montant").value.trim();
    let devise = document.getElementById("devise").value.trim();
    let donateurNom = document.getElementById("donateur_nom").value.trim();
    let donateurEmail = document.getElementById("donateur_email").value.trim();
    let dateDon = document.getElementById("date_don").value;
    let methodePaiement = document.getElementById("methode_paiement").value.trim();
    let message = document.getElementById("message").value.trim();

    if (isNaN(montant) || montant <= 0) {
        alert("❌ Le montant doit être un nombre positif.");
        return false;
    }

    if (!devise || devise.length < 2) {
        alert("❌ La devise doit être sur 2 lettres ou plus (ex: EUR, USD).");
        return false;
    }

    let regexNom = /^[A-Za-zÀ-ÖØ-öø-ÿ\s]+$/;
    if (!regexNom.test(donateurNom) || donateurNom.length < 3) {
        alert("❌ Le nom du donateur doit contenir uniquement des lettres (min. 3 caractères).");
        return false;
    }

    let regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!regexEmail.test(donateurEmail)) {
        alert("❌ L'email n'est pas valide.");
        return false;

    }

    if (!dateDon) {
        alert("❌ La date du don est obligatoire.");
        return false;
    }
    let dateSaisie = new Date(dateDon);
    let dateActuelle = new Date();
    dateActuelle.setHours(0,0,0,0);
    if (dateSaisie > dateActuelle) {
        alert("❌ La date du don ne peut pas être dans le futur.");
        return false;
    }

    const paiementsValides = ["card", "paypal", "cash"];
    if (!paiementsValides.includes(methodePaiement)) {
        alert("❌ La méthode de paiement doit être card, paypal ou cash.");
        return false;
    }

    if (message.length > 200) {
        alert("❌ Le message ne doit pas dépasser 200 caractères.");
        return false;
    }

    alert("✅ Don ajouté avec succès !");
    return true;
}

document.getElementById("addDonForm").addEventListener("submit", validerFormulaire);

document.getElementById("donateur_nom").addEventListener("keyup", function() {
    const value = this.value.trim();
    const msg = document.getElementById("donateur_nom_error");
    if (value.length >= 3 && /^[A-Za-zÀ-ÖØ-öø-ÿ\s]+$/.test(value)) {
        msg.style.color = "green";
        msg.innerText = "✅ Nom valide";
    } else {
        msg.style.color = "red";
        msg.innerText = "❌ Le nom doit contenir au moins 3 lettres et uniquement lettres/espaces";
    }
});

document.getElementById("donateur_email").addEventListener("blur", function() {
    const value = this.value.trim();
    const msg = document.getElementById("donateur_email_error");
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (regex.test(value)) {
        msg.style.color = "green";
        msg.innerText = "✅ Email valide";
    } else {
        msg.style.color = "red";
        msg.innerText = "❌ Email invalide";
    }
});
