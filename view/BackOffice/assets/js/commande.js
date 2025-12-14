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
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Préparation du paiement...';
    
    console.log('📦 Début passerCommande avec données:', formData);
    
    // Récupérer les articles du panier
    fetch(PANIER_API_URL)
        .then(response => {
            console.log('📡 Réponse reçue du panier:', response.status);
            return response.json();
        })
        .then(panierData => {
            console.log('🛒 Données panier:', panierData);
            
            if (!panierData.success || panierData.count === 0) {
                throw new Error('Votre panier est vide');
            }
            
            // Préparer les données pour la page de paiement
            const checkoutData = {
                client: formData,
                articles: panierData.data,
                total: panierData.total,
                count: panierData.count
            };
            
            console.log('💾 Sauvegarde dans localStorage:', checkoutData);
            
            // Sauvegarder dans localStorage
            localStorage.setItem('panierCheckout', JSON.stringify(checkoutData));
            
            console.log('✅ Affichage section paiement...');
            
            // Afficher la section paiement au lieu de rediriger
            afficherSectionPaiement(checkoutData);
        })
        .catch(error => {
            console.error('❌ Erreur:', error);
            showNotification(error.message || 'Erreur lors de la préparation du paiement', false);
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

// ============ GESTION DE LA SECTION PAIEMENT ============

let checkoutDataGlobal = null;
let stripeInstance = null;
let stripeCardElement = null;

function afficherSectionPaiement(checkoutData) {
    checkoutDataGlobal = checkoutData;
    
    // Masquer section livraison
    document.getElementById('sectionLivraison').style.display = 'none';
    
    // Afficher section paiement
    document.getElementById('sectionPaiement').style.display = 'block';
    
    // Scroll vers le haut
    window.scrollTo({ top: 0, behavior: 'smooth' });
    
    // Afficher le résumé
    afficherResumeCommande(checkoutData);
    
    // Gérer les options de paiement
    setupPaymentOptions();
}

function afficherResumeCommande(data) {
    const container = document.getElementById('resumeCommande');
    let html = '';
    
    // Informations client
    html += `
        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <strong style="color: #5F9E7F;">Client:</strong>
            <p style="margin: 5px 0 0 0; font-size: 0.9rem;">${data.client.nom}</p>
            <p style="margin: 5px 0 0 0; font-size: 0.9rem;">${data.client.email}</p>
            <p style="margin: 5px 0 0 0; font-size: 0.9rem;">${data.client.telephone}</p>
        </div>
    `;
    
    // Articles
    html += '<div style="margin-bottom: 20px;">';
    data.articles.forEach(article => {
        const prix = parseFloat(article.prix) || 0;
        const quantite = parseInt(article.quantite) || 0;
        const sousTotal = prix * quantite;
        html += `
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee;">
                <div>
                    <strong style="display: block; margin-bottom: 5px;">${article.nom}</strong>
                    <small style="color: #999;">${quantite} × ${prix.toFixed(2)} DT</small>
                </div>
                <strong style="color: #5F9E7F;">${sousTotal.toFixed(2)} DT</strong>
            </div>
        `;
    });
    html += '</div>';
    
    // Total
    const total = parseFloat(data.total) || 0;
    html += `
        <div style="display: flex; justify-content: space-between; padding: 15px 0; border-top: 2px solid #5F9E7F; font-size: 1.3rem; font-weight: 700;">
            <span>Total:</span>
            <span style="color: #5F9E7F;">${total.toFixed(2)} DT</span>
        </div>
    `;
    
    container.innerHTML = html;
}

function setupPaymentOptions() {
    const options = document.querySelectorAll('.payment-option');
    const formContainer = document.getElementById('paymentFormContainer');
    const btnPayer = document.getElementById('btnPayer');
    const btnRetour = document.getElementById('btnRetour');
    
    // Gérer le retour
    btnRetour.addEventListener('click', () => {
        document.getElementById('sectionPaiement').style.display = 'none';
        document.getElementById('sectionLivraison').style.display = 'block';
        const submitBtn = document.querySelector('#commandeForm button[type="submit"]');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-arrow-right"></i> Continuer vers le paiement';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
    
    // Gérer la sélection de méthode
    options.forEach(option => {
        option.addEventListener('click', () => {
            const radio = option.querySelector('input[type="radio"]');
            radio.checked = true;
            
            // Style visuel
            options.forEach(opt => opt.style.borderColor = '#ddd');
            option.style.borderColor = '#5F9E7F';
            option.style.background = '#f0f8f4';
            
            // Activer le bouton payer
            btnPayer.disabled = false;
            
            // Afficher le formulaire correspondant
            const method = radio.value;
            afficherFormulaireMethode(method, formContainer);
        });
    });
    
    // Gérer le paiement
    btnPayer.addEventListener('click', () => {
        const methodSelected = document.querySelector('input[name="methodePaiement"]:checked');
        if (!methodSelected) {
            alert('Veuillez sélectionner une méthode de paiement');
            return;
        }
        
        traiterPaiement(methodSelected.value);
    });
}

function afficherFormulaireMethode(method, container) {
    let html = '';
    
    switch(method) {
        case 'card':
            html = `
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <h4 style="margin-bottom: 15px; color: #5F9E7F;">Informations de carte</h4>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: 500;">Numéro de carte</label>
                        <input type="text" id="cardNumber" placeholder="1234 5678 9012 3456" 
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 500;">Date d'expiration</label>
                            <input type="text" id="cardExpiry" placeholder="MM/AA" 
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 5px; font-weight: 500;">CVV</label>
                            <input type="text" id="cardCvv" placeholder="123" 
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                    </div>
                    <p style="margin-top: 15px; font-size: 0.85rem; color: #999;">
                        <i class="fas fa-lock"></i> Paiement sécurisé SSL
                    </p>
                </div>
            `;
            break;
            
        case 'stripe':
            html = `
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <h4 style="margin-bottom: 15px; color: #635bff;">
                        <i class="fab fa-stripe"></i> Paiement Stripe
                    </h4>
                    <div id="stripe-card-element" style="padding: 12px; background: white; border: 1px solid #ddd; border-radius: 4px;"></div>
                    <p style="margin-top: 15px; font-size: 0.85rem; color: #999;">
                        <i class="fas fa-shield-alt"></i> Protégé par Stripe
                    </p>
                </div>
            `;
            setTimeout(() => initStripe(), 100);
            break;
            
        case 'paypal':
            html = `
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <h4 style="margin-bottom: 15px; color: #0070ba;">
                        <i class="fab fa-paypal"></i> Paiement PayPal
                    </h4>
                    <div id="paypal-button-container"></div>
                    <p style="margin-top: 15px; font-size: 0.85rem; color: #999;">
                        <i class="fas fa-shield-alt"></i> Protection des achats PayPal
                    </p>
                </div>
            `;
            setTimeout(() => initPayPal(), 100);
            break;
            
        case 'virement':
            html = `
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <h4 style="margin-bottom: 15px; color: #5F9E7F;">
                        <i class="fas fa-university"></i> Informations bancaires
                    </h4>
                    <div style="background: white; padding: 15px; border-radius: 4px; margin-bottom: 15px;">
                        <p style="margin: 5px 0;"><strong>IBAN:</strong> FR76 1234 5678 9012 3456 7890 123</p>
                        <p style="margin: 5px 0;"><strong>BIC:</strong> BNPAFRPPXXX</p>
                        <p style="margin: 5px 0;"><strong>Bénéficiaire:</strong> PeaceConnect</p>
                    </div>
                    <div class="alert alert-info" style="background: #d1ecf1; color: #0c5460; padding: 12px; border-radius: 4px; font-size: 0.9rem;">
                        <i class="fas fa-info-circle"></i> 
                        Veuillez mentionner le numéro de commande dans le libellé du virement
                    </div>
                </div>
            `;
            break;
    }
    
    container.innerHTML = html;
}

function initStripe() {
    // Simulation Stripe - dans la production, utilisez votre vraie clé
    console.log('Stripe initialisé (mode simulation)');
}

function initPayPal() {
    // Simulation PayPal - dans la production, utilisez votre vraie clé
    console.log('PayPal initialisé (mode simulation)');
}

function traiterPaiement(method) {
    const btnPayer = document.getElementById('btnPayer');
    btnPayer.disabled = true;
    btnPayer.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement en cours...';
    
    const API_URL = '../../controller/PaiementController.php';
    
    // Préparer les données de paiement
    const paiementData = {
        client: checkoutDataGlobal.client,
        articles: checkoutDataGlobal.articles,
        total: checkoutDataGlobal.total,
        methode_paiement: method
    };
    
    console.log('💳 Traitement paiement:', method, paiementData);
    
    // Envoyer au serveur
    fetch(`${API_URL}?action=creer`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(paiementData)
    })
    .then(response => {
        console.log('📡 Statut réponse:', response.status);
        return response.text().then(text => {
            console.log('📄 Réponse brute:', text);
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('❌ Erreur parsing JSON:', e);
                console.error('Réponse reçue:', text);
                throw new Error('Réponse serveur invalide: ' + text.substring(0, 200));
            }
        });
    })
    .then(data => {
        console.log('✅ Réponse paiement:', data);
        
        if (data.success) {
            // Succès !
            showNotification('Paiement effectué avec succès !', true);
            
            // Vider le localStorage
            localStorage.removeItem('panierCheckout');
            
            // Mettre à jour le badge du panier
            if (typeof updateCartBadge === 'function') {
                updateCartBadge();
            }
            
            // Rediriger vers page de confirmation
            setTimeout(() => {
                window.location.href = `confirmation.html?numero=${data.numero_commande}`;
            }, 1500);
        } else {
            throw new Error(data.message || 'Erreur lors du paiement');
        }
    })
    .catch(error => {
        console.error('❌ Erreur paiement:', error);
        showNotification(error.message || 'Erreur lors du paiement', false);
        btnPayer.disabled = false;
        btnPayer.innerHTML = '<i class="fas fa-lock"></i> Payer maintenant';
    });
}
