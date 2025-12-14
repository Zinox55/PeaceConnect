// validation.js - centralise les contrÃ´les de saisie (sans HTML5 natif)

document.addEventListener('DOMContentLoaded', function() {
    console.log("âœ… validation.js chargÃ© avec succÃ¨s");
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
        <h5 class="alert-heading">âŒ Erreur</h5>
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
            errors.push('L\'email doit Ãªtre valide.');
            markInvalid(emailInput, 'Veuillez entrer une adresse email valide.');
        }
        if (!/^\d{8}$/.test(tel)) {
            errors.push('Le tÃ©lÃ©phone doit contenir 8 chiffres.');
            markInvalid(telInput, 'Veuillez entrer 8 chiffres.');
        }
        if (!evenement) {
            errors.push('L\'Ã©vÃ©nement est requis.');
            markInvalid(eventInput, 'SÃ©lectionnez un Ã©vÃ©nement.');
        }

        if (errors.length) {
            renderValidationAlert(form, errors);
            return;
        }

        console.log("ðŸ“ Formulaire soumis - validation.js");
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
                errors.push('Veuillez sÃ©lectionner un gouvernorat valide.');
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
                errors.push('L\'email doit Ãªtre valide.');
                markInvalid(emailInput, 'Email valide requis');
            }
            if (tel && !/^\d{8}$/.test(tel)) {
                errors.push('Le tÃ©lÃ©phone doit contenir 8 chiffres ou Ãªtre vide.');
                markInvalid(telInput, '8 chiffres attendus');
            }
            if (!evenement) {
                errors.push('Le nom de l\'Ã©vÃ©nement est obligatoire.');
                markInvalid(eventInput, 'Ã‰vÃ©nement requis');
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
        console.log("ðŸ“¨ RÃ©ponse serveur:", response);

        if (response.includes('success')) {
            showSuccess(form);
        } else {
            showError(response, form, btn, originalText);
        }
    })
    .catch(err => {
        console.error("ðŸ”¥ Erreur fetch:", err);
        showNetworkError(form, btn, originalText);
    });
}

function showSuccess(form) {
    const successMessage = document.getElementById("successMessage");
    if (successMessage) {
        successMessage.style.display = "block";
    }
    form.style.display = "none";

    setTimeout(() => {
        window.location.href = "events.php";
    }, 1000);
}

function showError(response, form, btn, originalText) {
    const errorMsg = response.includes('error:') ? response.replace('error:', '').trim() : response;

    const errorContainer = document.createElement('div');
    errorContainer.id = 'errorContainer';
    errorContainer.className = 'alert alert-danger';
    errorContainer.innerHTML = `
        <h5 class="alert-heading">âŒ Erreur</h5>
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
        <h5 class="alert-heading">âŒ Erreur rÃ©seau</h5>
        <p class="mb-0">Impossible de se connecter au serveur</p>
    `;

    form.parentNode.insertBefore(errorContainer, form);
    btn.innerHTML = originalText;
    btn.disabled = false;
}
