const COMMANDE_API_URL = '../../controller/CommandeController.php';
const PANIER_API_URL = '../../controller/PanierController.php';

// Fonctions de validation
function validateNom(nom) {
    const trimmed = nom.trim();
    if (trimmed.length === 0) {
        return { valid: false, message: 'Le nom est obligatoire' };
    }
    if (trimmed.length < 3) {
        return { valid: false, message: 'Le nom doit contenir au moins 3 caractères' };
    }
    return { valid: true, message: '' };
}

function validateEmail(email) {
    const trimmed = email.trim();
    if (trimmed.length === 0) {
        return { valid: false, message: 'L\'email est obligatoire' };
    }
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(trimmed)) {
        return { valid: false, message: 'Email invalide' };
    }
    return { valid: true, message: '' };
}

function validateTelephone(telephone) {
    const trimmed = telephone.trim();
    if (trimmed.length === 0) {
        return { valid: false, message: 'Le téléphone est obligatoire' };
    }
    // Pattern français flexible
    const phonePattern = /^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/;
    if (!phonePattern.test(trimmed)) {
        return { valid: false, message: 'Format de téléphone invalide (ex: 06 12 34 56 78)' };
    }
    return { valid: true, message: '' };
}

function validateAdresse(adresse) {
    const trimmed = adresse.trim();
    if (trimmed.length === 0) {
        return { valid: false, message: 'L\'adresse est obligatoire' };
    }
    if (trimmed.length < 10) {
        return { valid: false, message: 'L\'adresse doit contenir au moins 10 caractères' };
    }
    return { valid: true, message: '' };
}

function showError(inputElement, message) {
    const errorElement = document.getElementById(inputElement.name + '_error');
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.style.color = '#e74c3c';
    }
    inputElement.style.borderColor = '#e74c3c';
}

function clearError(inputElement) {
    const errorElement = document.getElementById(inputElement.name + '_error');
    if (errorElement) {
        errorElement.textContent = '';
    }
    inputElement.style.borderColor = '#ddd';
}

function showSuccess(inputElement) {
    inputElement.style.borderColor = '#2ecc71';
}

function showNotification(message, isSuccess = true) {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed; top: 20px; right: 20px;
        background-color: ${isSuccess ? '#5F9E7F' : '#e74c3c'};
        color: white; padding: 15px 20px; border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2); z-index: 9999;
    `;
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}

function verifierPanier() {
    fetch(PANIER_API_URL)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.count === 0) {
                showNotification('Votre panier est vide', false);
                setTimeout(() => {
                    window.location.href = 'produits.html';
                }, 2000);
            }
        })
        .catch(error => console.error('Erreur:', error));
}

function passerCommande(formData) {
    const submitBtn = document.querySelector('#commandeForm button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement...';
    
    fetch(COMMANDE_API_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Afficher une modal de succès
            const modal = document.createElement('div');
            modal.style.cssText = `
                position: fixed; top: 0; left: 0; width: 100%; height: 100%;
                background: rgba(0,0,0,0.8); display: flex; align-items: center;
                justify-content: center; z-index: 10000;
            `;
            modal.innerHTML = `
                <div style="background: white; padding: 40px; border-radius: 12px; text-align: center; max-width: 500px; margin: 20px;">
                    <i class="fas fa-check-circle" style="font-size: 4rem; color: #5F9E7F; margin-bottom: 20px;"></i>
                    <h2 style="color: #5F9E7F; margin-bottom: 15px;">Commande confirmée !</h2>
                    <p style="color: #6C757D; margin-bottom: 10px;">Votre commande a été enregistrée avec succès.</p>
                    <p style="font-size: 1.2rem; font-weight: 600; color: #333; margin: 20px 0;">
                        N° de commande : <span style="color: #5F9E7F;">${data.numero_commande}</span>
                    </p>
                    <p style="color: #6C757D; font-size: 0.9rem; margin-bottom: 25px;">Conservez ce numéro pour suivre votre commande</p>
                    <div style="display: flex; gap: 10px; justify-content: center;">
                        <a href="suivi.html?numero=${data.numero_commande}" class="btn btn-success" style="background: #5F9E7F; color: white; padding: 12px 24px; border-radius: 5px; text-decoration: none;">
                            <i class="fas fa-box-open"></i> Suivre ma commande
                        </a>
                        <a href="produits.html" class="btn" style="background: #6c757d; color: white; padding: 12px 24px; border-radius: 5px; text-decoration: none;">
                            Continuer mes achats
                        </a>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        } else {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> Confirmer la commande';
            
            if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    const input = document.querySelector(`[name="${field}"]`);
                    if (input) showError(input, data.errors[field]);
                });
            } else {
                showNotification(data.message, false);
            }
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showNotification('Erreur de connexion', false);
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> Confirmer la commande';
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si le panier n'est pas vide
    verifierPanier();
    
    const form = document.getElementById('commandeForm');
    if (!form) return;
    
    // Validation en temps réel
    const nomInput = document.getElementById('nom');
    if (nomInput) {
        nomInput.addEventListener('input', function() {
            const validation = validateNom(this.value);
            if (this.value.trim() !== '') {
                if (validation.valid) {
                    showSuccess(this);
                    clearError(this);
                } else {
                    showError(this, validation.message);
                }
            } else {
                clearError(this);
            }
        });
    }
    
    const emailInput = document.getElementById('email');
    if (emailInput) {
        emailInput.addEventListener('input', function() {
            const validation = validateEmail(this.value);
            if (this.value.trim() !== '') {
                if (validation.valid) {
                    showSuccess(this);
                    clearError(this);
                } else {
                    showError(this, validation.message);
                }
            } else {
                clearError(this);
            }
        });
    }
    
    const telInput = document.getElementById('telephone');
    if (telInput) {
        telInput.addEventListener('input', function() {
            const validation = validateTelephone(this.value);
            if (this.value.trim() !== '') {
                if (validation.valid) {
                    showSuccess(this);
                    clearError(this);
                } else {
                    showError(this, validation.message);
                }
            } else {
                clearError(this);
            }
        });
    }
    
    const adresseInput = document.getElementById('adresse');
    if (adresseInput) {
        adresseInput.addEventListener('input', function() {
            const validation = validateAdresse(this.value);
            if (this.value.trim() !== '') {
                if (validation.valid) {
                    showSuccess(this);
                    clearError(this);
                } else {
                    showError(this, validation.message);
                }
            } else {
                clearError(this);
            }
        });
    }
    
    // Soumission du formulaire
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            nom: nomInput.value,
            email: emailInput.value,
            telephone: telInput.value,
            adresse: adresseInput.value
        };
        
        // Validation finale
        let isValid = true;
        
        const nomVal = validateNom(formData.nom);
        if (!nomVal.valid) {
            showError(nomInput, nomVal.message);
            isValid = false;
        }
        
        const emailVal = validateEmail(formData.email);
        if (!emailVal.valid) {
            showError(emailInput, emailVal.message);
            isValid = false;
        }
        
        const telVal = validateTelephone(formData.telephone);
        if (!telVal.valid) {
            showError(telInput, telVal.message);
            isValid = false;
        }
        
        const adresseVal = validateAdresse(formData.adresse);
        if (!adresseVal.valid) {
            showError(adresseInput, adresseVal.message);
            isValid = false;
        }
        
        if (isValid) {
            passerCommande(formData);
        } else {
            showNotification('Veuillez corriger les erreurs', false);
        }
    });
});
