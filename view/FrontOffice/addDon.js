function validerFormulaire(event) {
    // Get form values
    let cause = document.getElementById("cause").value.trim();
    let montant = document.getElementById("montant").value.trim();
    let devise = document.getElementById("devise").value.trim();
    let donateurNom = document.getElementById("donateur_nom").value.trim();
    let donateurEmail = document.getElementById("donateur_email").value.trim();
    let dateDon = document.getElementById("date_don").value;
    let methodePaiement = document.getElementById("methode_paiement").value.trim();
    let message = document.getElementById("message").value.trim();

    // Validate cause selection
    if (!cause || cause === "") {
        alert("⚠️ Veuillez sélectionner une cause.");
        return false;
    }

    // Validate amount
    if (isNaN(montant) || montant <= 0) {
        alert("❌ Le montant doit être un nombre positif.");
        return false;
    }

    // Validate currency
    if (!devise || devise.length < 2) {
        alert("❌ La devise doit être sur 2 lettres ou plus (ex: DT, EUR, USD).");
        return false;
    }

    // Validate donor name
    let regexNom = /^[A-Za-zÀ-ÖØ-öø-ÿ\s]+$/;
    if (!regexNom.test(donateurNom) || donateurNom.length < 3) {
        alert("❌ Le nom du donateur doit contenir uniquement des lettres (min. 3 caractères).");
        return false;
    }

    // Validate email
    let regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!regexEmail.test(donateurEmail)) {
        alert("❌ L'email n'est pas valide.");
        return false;
    }

    // Validate date (optional field)
    if (dateDon) {
        let dateSaisie = new Date(dateDon);
        let dateActuelle = new Date();
        dateActuelle.setHours(0, 0, 0, 0);
        if (dateSaisie > dateActuelle) {
            alert("❌ La date du don ne peut pas être dans le futur.");
            return false;
        }
    }

    // Validate payment method
    const paiementsValides = ["card", "paypal", "cash"];
    if (!paiementsValides.includes(methodePaiement)) {
        alert("❌ La méthode de paiement doit être card, paypal ou cash.");
        return false;
    }

    // Validate message length
    if (message.length > 200) {
        alert("❌ Le message ne doit pas dépasser 200 caractères.");
        return false;
    }

    // If all validations pass
    return true;
}

// Wait for DOM to be fully loaded
document.addEventListener("DOMContentLoaded", function() {
    // Attach validation to the form
    const form = document.getElementById("donation-form");
    if (form) {
        form.addEventListener("submit", function(event) {
            if (!validerFormulaire(event)) {
                event.preventDefault(); // Stop form submission if validation fails
            }
        });
    }

    // Real-time validation for donor name
    const donateurNomInput = document.getElementById("donateur_nom");
    if (donateurNomInput) {
        donateurNomInput.addEventListener("keyup", function() {
            const value = this.value.trim();
            const regexNom = /^[A-Za-zÀ-ÖØ-öø-ÿ\s]+$/;
            
            // Create or get error message element
            let msg = document.getElementById("donateur_nom_error");
            if (!msg) {
                msg = document.createElement("span");
                msg.id = "donateur_nom_error";
                msg.style.fontSize = "12px";
                msg.style.display = "block";
                msg.style.marginTop = "5px";
                this.parentElement.appendChild(msg);
            }

            if (value.length >= 3 && regexNom.test(value)) {
                msg.style.color = "green";
                msg.innerText = "✅ Nom valide";
            } else if (value.length > 0) {
                msg.style.color = "red";
                msg.innerText = "❌ Le nom doit contenir au moins 3 lettres et uniquement lettres/espaces";
            } else {
                msg.innerText = "";
            }
        });
    }

    // Real-time validation for email
    const donateurEmailInput = document.getElementById("donateur_email");
    if (donateurEmailInput) {
        donateurEmailInput.addEventListener("blur", function() {
            const value = this.value.trim();
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            // Create or get error message element
            let msg = document.getElementById("donateur_email_error");
            if (!msg) {
                msg = document.createElement("span");
                msg.id = "donateur_email_error";
                msg.style.fontSize = "12px";
                msg.style.display = "block";
                msg.style.marginTop = "5px";
                this.parentElement.appendChild(msg);
            }

            if (regex.test(value)) {
                msg.style.color = "green";
                msg.innerText = "✅ Email valide";
            } else if (value.length > 0) {
                msg.style.color = "red";
                msg.innerText = "❌ Email invalide";
            } else {
                msg.innerText = "";
            }
        });
    }

    // Handle amount radio buttons
    const amountRadios = document.querySelectorAll('.js-amount');
    const montantInput = document.getElementById('montant');
    
    if (amountRadios.length > 0 && montantInput) {
        amountRadios.forEach(function(radio) {
            radio.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                if (value) {
                    montantInput.value = value;
                }
            });
        });
    }
});