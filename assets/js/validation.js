document.addEventListener('DOMContentLoaded', function() {
    // Récupérer l'événement depuis l'URL
    const urlParams = new URLSearchParams(window.location.search);
    const eventName = urlParams.get('event');
    const eventDate = urlParams.get('date');
    const eventLieu = urlParams.get('lieu');
    
    if (eventName) {
        document.getElementById('eventInfo').style.display = 'block';
        document.getElementById('eventTitle').textContent = decodeURIComponent(eventName);
        document.getElementById('eventDate').textContent = decodeURIComponent(eventDate || '—');
        document.getElementById('eventLieu').textContent = decodeURIComponent(eventLieu || '—');
        document.getElementById('evenementField').value = decodeURIComponent(eventName);
    }

    // Gestion du formulaire
    const inscriptionForm = document.getElementById('inscriptionForm');
    if (inscriptionForm) {
        inscriptionForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Réinitialiser les messages d'erreur
            clearErrors();
            
            // Validation
            const errors = validateForm();
            
            if (errors.length === 0) {
                submitForm();
            } else {
                displayErrors(errors);
            }
        });
        
        // Validation en temps réel
        setupRealTimeValidation();
    }
});

function validateForm() {
    const errors = [];
    const nom = document.querySelector("input[name='nom']").value.trim();
    const email = document.querySelector("input[name='email']").value.trim();
    const telephone = document.querySelector("input[name='telephone']").value.trim();
    const evenement = document.querySelector("input[name='evenement']").value.trim();

    // Validation nom
    if (!nom) {
        errors.push({ field: 'nom', message: 'Le nom complet est obligatoire' });
    } else if (nom.length < 2) {
        errors.push({ field: 'nom', message: 'Le nom doit contenir au moins 2 caractères' });
    }

    // Validation email
    if (!email) {
        errors.push({ field: 'email', message: 'L\'email est obligatoire' });
    } else if (!validateEmail(email)) {
        errors.push({ field: 'email', message: 'Format d\'email invalide' });
    }

    // Validation téléphone
    if (telephone && !validatePhone(telephone)) {
        errors.push({ field: 'telephone', message: 'Le téléphone doit contenir 8 chiffres' });
    }

    // Validation événement
    if (!evenement) {
        errors.push({ field: 'evenement', message: 'L\'événement est obligatoire' });
    }

    return errors;
}

function displayErrors(errors) {
    // Créer le conteneur d'erreurs
    let errorContainer = document.getElementById('errorContainer');
    if (!errorContainer) {
        errorContainer = document.createElement('div');
        errorContainer.id = 'errorContainer';
        errorContainer.className = 'alert alert-danger';
        const form = document.getElementById('inscriptionForm');
        form.parentNode.insertBefore(errorContainer, form);
    }
    
    // Afficher les erreurs
    errorContainer.innerHTML = `
        <h5 class="alert-heading">❌ Erreurs de validation</h5>
        <ul class="mb-0">
            ${errors.map(error => `<li>${error.message}</li>`).join('')}
        </ul>
    `;
    errorContainer.style.display = 'block';
    
    // Marquer les champs en erreur
    errors.forEach(error => {
        const field = document.querySelector(`[name="${error.field}"]`);
        if (field) {
            field.classList.add('is-invalid');
            
            // Ajouter le message d'erreur sous le champ
            let errorElement = field.parentNode.querySelector('.invalid-feedback');
            if (!errorElement) {
                errorElement = document.createElement('div');
                errorElement.className = 'invalid-feedback';
                field.parentNode.appendChild(errorElement);
            }
            errorElement.textContent = error.message;
        }
    });
}

function clearErrors() {
    // Supprimer le conteneur d'erreurs
    const errorContainer = document.getElementById('errorContainer');
    if (errorContainer) {
        errorContainer.style.display = 'none';
    }
    
    // Supprimer les styles d'erreur des champs
    const invalidFields = document.querySelectorAll('.is-invalid');
    invalidFields.forEach(field => {
        field.classList.remove('is-invalid');
    });
    
    const errorMessages = document.querySelectorAll('.invalid-feedback');
    errorMessages.forEach(msg => {
        msg.textContent = '';
    });
}

function setupRealTimeValidation() {
    const fields = document.querySelectorAll('#inscriptionForm input');
    
    fields.forEach(field => {
        field.addEventListener('blur', function() {
            clearFieldError(this);
            validateField(this);
        });
        
        field.addEventListener('input', function() {
            clearFieldError(this);
        });
    });
}

function validateField(field) {
    const value = field.value.trim();
    let isValid = true;
    let message = '';

    switch(field.name) {
        case 'nom':
            if (!value) {
                isValid = false;
                message = 'Le nom complet est obligatoire';
            } else if (value.length < 2) {
                isValid = false;
                message = 'Le nom doit contenir au moins 2 caractères';
            }
            break;
            
        case 'email':
            if (!value) {
                isValid = false;
                message = 'L\'email est obligatoire';
            } else if (!validateEmail(value)) {
                isValid = false;
                message = 'Format d\'email invalide';
            }
            break;
            
        case 'telephone':
            if (value && !validatePhone(value)) {
                isValid = false;
                message = 'Le téléphone doit contenir 8 chiffres';
            }
            break;
            
        case 'evenement':
            if (!value) {
                isValid = false;
                message = 'L\'événement est obligatoire';
            }
            break;
    }

    if (!isValid) {
        field.classList.add('is-invalid');
        let errorElement = field.parentNode.querySelector('.invalid-feedback');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'invalid-feedback';
            field.parentNode.appendChild(errorElement);
        }
        errorElement.textContent = message;
    }

    return isValid;
}

function clearFieldError(field) {
    field.classList.remove('is-invalid');
    const errorElement = field.parentNode.querySelector('.invalid-feedback');
    if (errorElement) {
        errorElement.textContent = '';
    }
}

function submitForm() {
    const formData = new FormData(document.getElementById('inscriptionForm'));

    fetch("../../controller/InscriptionController.php", {
        method: "POST",
        body: formData
    })
    .then(r => r.text())
    .then(res => {
        if (res.trim() === "success") {
            // Afficher le message de succès et cacher le formulaire
            document.getElementById("successMessage").style.display = "block";
            document.getElementById("inscriptionForm").style.display = "none";

            // Redirection après 3 secondes
            setTimeout(() => {
                window.location.href = "events.php";
            }, 3000);
        } else {
            // Afficher l'erreur du serveur
            displayErrors([{ field: 'general', message: res.replace('error: ', '') }]);
        }
    })
    .catch(err => {
        displayErrors([{ field: 'general', message: 'Erreur réseau : ' + err }]);
    });
}

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validatePhone(phone) {
    const re = /^\d{8}$/;
    return re.test(phone.replace(/\s/g, ''));
}