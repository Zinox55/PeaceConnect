// validation.js - centralise les contrôles de saisie (sans HTML5 natif)

document.addEventListener('DOMContentLoaded', function() {
    console.log("✅ validation.js chargé avec succès");
    attachFrontInscriptionValidation();
    attachBackofficeEventValidation();
    attachBackofficeInscriptionValidation();
});

// ===== Helpers communs =====
function clearValidationState(form) {
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    form.querySelectorAll('.validation-alert, #errorContainer').forEach(el => el.remove());
}

function markInvalid(input, message) {
    if (!input) return;
    input.classList.add('is-invalid');
    const feedback = input.parentNode.querySelector('.invalid-feedback');
    if (feedback) {
        feedback.textContent = message;
    }
}

function renderValidationAlert(form, messages) {
    const alert = document.createElement('div');
    alert.className = 'alert alert-danger validation-alert';
    alert.innerHTML = `
        <h5 class="alert-heading">❌ Erreur</h5>
        <ul class="mb-0">
            ${messages.map(msg => `<li>${msg}</li>`).join('')}
        </ul>
    `;
    form.parentNode.insertBefore(alert, form);
}

// ===== Frontoffice : inscription =====
function attachFrontInscriptionValidation() {
    const form = document.getElementById('inscriptionForm');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        clearValidationState(form);

        const nomInput = form.querySelector('input[name="nom"]');
        const emailInput = form.querySelector('input[name="email"]');
        const telInput = form.querySelector('input[name="telephone"]');
        const eventInput = form.querySelector('input[name="evenement"]');

        const nom = (nomInput?.value || '').trim();
        const email = (emailInput?.value || '').trim();
        const tel = (telInput?.value || '').trim();
        const evenement = (eventInput?.value || '').trim();

        const errors = [];
        if (!nom) {
            errors.push('Le nom complet est obligatoire.');
            markInvalid(nomInput, 'Veuillez entrer votre nom complet.');
        }
        if (!email || !/^\S+@\S+\.\S+$/.test(email)) {
            errors.push('L\'email doit être valide.');
            markInvalid(emailInput, 'Veuillez entrer une adresse email valide.');
        }
        if (!/^\d{8}$/.test(tel)) {
            errors.push('Le téléphone doit contenir 8 chiffres.');
            markInvalid(telInput, 'Veuillez entrer 8 chiffres.');
        }
        if (!evenement) {
            errors.push('L\'événement est requis.');
            markInvalid(eventInput, 'Sélectionnez un événement.');
        }

        if (errors.length) {
            renderValidationAlert(form, errors);
            return;
        }

        console.log("📋 Formulaire soumis - validation.js");
        handleFormSubmission(form);
    });
}

// ===== Backoffice : Ã©vÃ©nements =====
function attachBackofficeEventValidation() {
    document.querySelectorAll('form[data-validate="back-event"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            clearValidationState(form);

            const titreInput = form.querySelector('input[name="titre"]');
            const dateInput = form.querySelector('input[name="date_event"]');
            const lieuInput = form.querySelector('input[name="lieu"]');

            const errors = [];
            if (!(titreInput?.value || '').trim()) {
                errors.push('Le titre est obligatoire.');
                markInvalid(titreInput, 'Titre requis');
            }
            if (!(dateInput?.value || '').trim()) {
                errors.push('La date est obligatoire.');
                markInvalid(dateInput, 'Date requise');
            }
            const lieuValue = (lieuInput?.value || '').trim();
            if (!lieuValue) {
                errors.push('Le lieu est obligatoire.');
                markInvalid(lieuInput, 'Lieu requis');
            } else if (typeof isValidGouvernorat === 'function' && !isValidGouvernorat(lieuValue)) {
                errors.push('Veuillez sélectionner un gouvernorat valide.');
                markInvalid(lieuInput, 'Choisissez un gouvernorat valide.');
            }

            if (errors.length) {
                e.preventDefault();
                renderValidationAlert(form, errors);
            }
        });
    });
}

// ===== Backoffice : inscriptions =====
function attachBackofficeInscriptionValidation() {
    document.querySelectorAll('form[data-validate="back-inscription"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            clearValidationState(form);

            const nomInput = form.querySelector('input[name="nom"]');
            const emailInput = form.querySelector('input[name="email"]');
            const telInput = form.querySelector('input[name="telephone"]');
            const eventInput = form.querySelector('input[name="evenement"]');

            const nom = (nomInput?.value || '').trim();
            const email = (emailInput?.value || '').trim();
            const tel = (telInput?.value || '').trim();
            const evenement = (eventInput?.value || '').trim();

            const errors = [];
            if (!nom) {
                errors.push('Le nom complet est obligatoire.');
                markInvalid(nomInput, 'Nom requis');
            }
            if (!email || !/^\S+@\S+\.\S+$/.test(email)) {
                errors.push('L\'email doit être valide.');
                markInvalid(emailInput, 'Email valide requis');
            }
            if (tel && !/^\d{8}$/.test(tel)) {
                errors.push('Le téléphone doit contenir 8 chiffres ou être vide.');
                markInvalid(telInput, '8 chiffres attendus');
            }
            if (!evenement) {
                errors.push('Le nom de l\'événement est obligatoire.');
                markInvalid(eventInput, 'Événement requis');
            }

            if (errors.length) {
                e.preventDefault();
                renderValidationAlert(form, errors);
            }
        });
    });
}

// ===== Soumission AJAX inscription front =====
function handleFormSubmission(form) {
    const oldError = document.getElementById('errorContainer');
    if (oldError) oldError.remove();

    const btn = form.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;
    btn.innerHTML = 'Envoi...';
    btn.disabled = true;

    const formData = new FormData(form);

    fetch("../../controller/inscriptioncontroller.php?action=process", {
        method: "POST",
        body: formData
    })
    .then(r => r.text())
    .then(response => {
        console.log("📨 Réponse serveur:", response);

        if (response.includes('success')) {
            showSuccess(form);
        } else {
            showError(response, form, btn, originalText);
        }
    })
    .catch(err => {
        console.error("🔥 Erreur fetch:", err);
        showNetworkError(form, btn, originalText);
    });
}

function showSuccess(form) {
    // Masquer le formulaire
    form.style.display = "none";
    
    // Créer un message de succès élégant
    const successContainer = document.createElement('div');
    successContainer.className = 'alert alert-success text-center';
    successContainer.style.cssText = 'border-left: 5px solid #28a745; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2); animation: slideIn 0.5s ease-out;';
    successContainer.innerHTML = `
        <style>
            @keyframes slideIn {
                from { transform: translateY(-30px); opacity: 0; }
                to { transform: translateY(0); opacity: 1; }
            }
            .success-icon {
                font-size: 3rem;
                color: #28a745;
                margin-bottom: 1rem;
                animation: checkBounce 0.6s ease-out;
            }
            @keyframes checkBounce {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.2); }
            }
        </style>
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h3 class="alert-heading mb-3" style="color: #155724; font-weight: 700;">
            ✅ Inscription En Attente de Confirmation
        </h3>
        <p class="mb-3" style="font-size: 1.1rem; color: #155724;">
            <strong>📧 Presque terminé !</strong> Vérifiez votre boîte email.
        </p>
        <div style="background: #d4edda; border-radius: 10px; padding: 20px; margin: 20px 0;">
            <p style="margin: 0; color: #155724; font-size: 0.95rem;">
                Un email de confirmation a été envoyé à votre adresse.<br>
                Cliquez sur le lien dans l'email pour activer votre inscription.
            </p>
        </div>
        <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; text-align: left; border-radius: 5px;">
            <p style="margin: 0; color: #856404; font-size: 0.9rem;">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Important :</strong> Le lien expire dans <strong>24 heures</strong>.<br>
                <small>💡 Pensez à vérifier votre dossier spam/courrier indésirable.</small>
            </p>
        </div>
        <div class="mt-4">
            <a href="events.php" class="btn btn-primary btn-lg">
                <i class="fas fa-calendar-alt me-2"></i>Retour aux événements
            </a>
        </div>
    `;
    
    // Insérer le message avant le formulaire
    form.parentNode.insertBefore(successContainer, form);
    
    // Faire défiler vers le message
    successContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function showError(response, form, btn, originalText) {
    const errorMsg = response.includes('error:') ? response.replace('error:', '').trim() : response;

    const errorContainer = document.createElement('div');
    errorContainer.id = 'errorContainer';
    errorContainer.className = 'alert alert-danger';
    errorContainer.innerHTML = `
        <h5 class="alert-heading">❌ Erreur</h5>
        <p class="mb-0">${errorMsg}</p>
    `;

    form.parentNode.insertBefore(errorContainer, form);
    btn.innerHTML = originalText;
    btn.disabled = false;
}

function showNetworkError(form, btn, originalText) {
    const errorContainer = document.createElement('div');
    errorContainer.id = 'errorContainer';
    errorContainer.className = 'alert alert-danger';
    errorContainer.innerHTML = `
        <h5 class="alert-heading">❌ Erreur réseau</h5>
        <p class="mb-0">Impossible de se connecter au serveur</p>
    `;

    form.parentNode.insertBefore(errorContainer, form);
    btn.innerHTML = originalText;
    btn.disabled = false;
}
