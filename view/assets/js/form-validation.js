// Fonction pour afficher les messages de validation
function displayMessage(elementId, message, isError = true) {
    const element = document.getElementById(elementId + "_error");
    if (element) {
        element.textContent = message;
        element.style.color = isError ? '#e74c3c' : '#2ecc71';
    }
}

// Validation du formulaire de commande
document.addEventListener('DOMContentLoaded', function() {
    const commandeForm = document.getElementById('commandeForm');
    if (!commandeForm) return;

    commandeForm.addEventListener('submit', function(event) {
        event.preventDefault();
        
        // Récupération des champs
        const nom = document.getElementById('nom').value.trim();
        const email = document.getElementById('email').value.trim();
        const telephone = document.getElementById('telephone').value.trim();
        const adresse = document.getElementById('adresse').value.trim();
        
        let isValid = true;
        
        // Validation du nom
        if (nom.length < 3) {
            displayMessage('nom', 'Le nom doit contenir au moins 3 caractères.');
            isValid = false;
        } else {
            displayMessage('nom', 'Valide', false);
        }
        
        // Validation de l'email
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email) {
            displayMessage('email', 'L\'email est obligatoire.');
            isValid = false;
        } else if (!emailPattern.test(email)) {
            displayMessage('email', 'Veuillez entrer une adresse email valide.');
            isValid = false;
        } else {
            displayMessage('email', 'Valide', false);
        }
        
        // Validation du téléphone
        const phonePattern = /^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/;
        if (!telephone) {
            displayMessage('telephone', 'Le numéro de téléphone est obligatoire.');
            isValid = false;
        } else if (!phonePattern.test(telephone)) {
            displayMessage('telephone', 'Format de téléphone invalide. Ex: 06 12 34 56 78');
            isValid = false;
        } else {
            displayMessage('telephone', 'Valide', false);
        }
        
        // Validation de l'adresse
        if (adresse.length < 10) {
            displayMessage('adresse', 'L\'adresse doit contenir au moins 10 caractères.');
            isValid = false;
        } else {
            displayMessage('adresse', 'Valide', false);
        }
        
        // Si tout est valide, on peut soumettre le formulaire
        if (isValid) {
            // Ici, vous pouvez ajouter le code pour soumettre le formulaire
            alert('Commande validée avec succès !');
            // commandeForm.submit(); // Décommentez cette ligne pour activer la soumission réelle
        }
    });
    
    // Ajout des écouteurs d'événements pour la validation en temps réel
    document.getElementById('nom')?.addEventListener('input', function() {
        if (this.value.trim().length >= 3) {
            displayMessage('nom', 'Valide', false);
        }
    });
    
    document.getElementById('email')?.addEventListener('input', function() {
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (emailPattern.test(this.value.trim())) {
            displayMessage('email', 'Valide', false);
        }
    });
    
    document.getElementById('telephone')?.addEventListener('input', function() {
        const phonePattern = /^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/;
        if (phonePattern.test(this.value.trim())) {
            displayMessage('telephone', 'Valide', false);
        }
    });
    
    document.getElementById('adresse')?.addEventListener('input', function() {
        if (this.value.trim().length >= 10) {
            displayMessage('adresse', 'Valide', false);
        }
    });
});

// Fonction pour afficher les messages d'erreur
function showError(input, message) {
    const error = document.createElement('div');
    error.className = 'error-message';
    error.style.color = '#e74c3c';
    error.style.fontSize = '0.8rem';
    error.style.marginTop = '5px';
    error.textContent = message;
    
    // Insérer le message d'erreur après le champ
    input.parentNode.insertBefore(error, input.nextSibling);
    
    // Ajouter une classe d'erreur au champ
    input.style.borderColor = '#e74c3c';
    
    // Supprimer le message d'erreur et le style après 3 secondes
    input.addEventListener('input', function clearError() {
        if (this.nextElementSibling && this.nextElementSibling.className === 'error-message') {
            this.nextElementSibling.remove();
            this.style.borderColor = '';
        }
    }, { once: true });
}

// Validation du formulaire de suivi de commande
function validateSuiviForm() {
    const form = document.querySelector('.suivi-form');
    if (!form) return;

    const input = form.querySelector('input[type="text"]');
    const button = form.querySelector('button');
    
    if (!input || !button) return;

    // Ajouter la validation au clic sur le bouton
    button.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Supprimer les messages d'erreur existants
        const existingError = form.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }
        
        // Réinitialiser le style
        input.style.borderColor = '#e0e0e0';
        
        // Validation du numéro de commande
        const commandeValue = input.value.trim();
        const commandePattern = /^CMD-\d{4}-\d{3}$/;
        
        if (!commandeValue) {
            showError(input, 'Veuillez entrer un numéro de commande');
            return;
        }
        
        if (!commandePattern.test(commandeValue)) {
            showError(input, 'Format invalide. Utilisez CMD-AAAA-NNN (ex: CMD-2025-001)');
            return;
        }
        
        // Si la validation est réussie, on peut simuler la recherche
        // Dans une vraie application, vous feriez une requête AJAX ici
        const resultDiv = document.querySelector('.suivi-result');
        if (resultDiv) {
            resultDiv.style.display = 'block';
            // Faire défendre jusqu'au résultat
            resultDiv.scrollIntoView({ behavior: 'smooth' });
        }
    });
}

// Initialiser la validation lorsque le DOM est chargé
document.addEventListener('DOMContentLoaded', function() {
    validateCommandeForm();
    validateSuiviForm();
    
    // Ajouter des styles pour les champs invalides
    const style = document.createElement('style');
    style.textContent = `
        input:invalid, textarea:invalid {
            border-color: #e74c3c !important;
        }
        
        .error-message {
            color: #e74c3c;
            font-size: 0.8rem;
            margin-top: 5px;
            display: block;
            padding-left: 10px;
        }
        
        .suivi-form input[type="text"] {
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        
        .suivi-form input[type="text"]:focus {
            border-color: #5F9E7F !important;
            box-shadow: 0 0 0 0.2rem rgba(95, 158, 127, 0.25);
            outline: none;
        }
    `;
    document.head.appendChild(style);
});
