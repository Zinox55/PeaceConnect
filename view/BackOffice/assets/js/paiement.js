/**
 * Gestion de la page de paiement
 * Support Stripe, PayPal, Carte bancaire et Virement
 */

console.log('🚀 Page paiement.js chargée');

// Récupérer les données du panier depuis localStorage
const panierDataRaw = localStorage.getItem('panierCheckout');
console.log('📦 Données brutes localStorage:', panierDataRaw);

const panierData = JSON.parse(panierDataRaw || '{}');
console.log('🛒 Données panier parsées:', panierData);

// Variables globales
let methodePaiementSelectionnee = null;
let stripe = null;
let stripeCardElement = null;
let paypalButtonsRendered = false;

// Afficher le résumé de la commande
function afficherResume() {
    const container = document.getElementById('commandeDetails');
    
    if (!panierData.articles || panierData.articles.length === 0) {
        container.innerHTML = `
            <div style="text-align: center; padding: 20px;">
                <i class="fas fa-shopping-cart" style="font-size: 3rem; color: #ccc; margin-bottom: 15px;"></i>
                <p style="color: #999;">Aucun article dans votre panier</p>
                <a href="panier.html" style="display: inline-block; margin-top: 15px; padding: 10px 20px; background: #5F9E7F; color: white; text-decoration: none; border-radius: 8px;">Retour au panier</a>
            </div>
        `;
        return;
    }
    
    let html = '<div style="margin-bottom: 20px;">';
    
    // Afficher les articles
    panierData.articles.forEach(article => {
        const sousTotal = article.prix * article.quantite;
        html += `
            <div style="display: flex; align-items: center; gap: 15px; padding: 15px; border-bottom: 1px solid #eee;">
                <img src="${article.image || '../back/assets/img/default.jpg'}" alt="${article.nom}" 
                     style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                <div style="flex: 1;">
                    <h4 style="margin: 0 0 5px 0; font-size: 1rem;">${article.nom}</h4>
                    <p style="margin: 0; color: #666; font-size: 0.9rem;">Quantité: ${article.quantite}</p>
                </div>
                <div style="text-align: right;">
                    <p style="margin: 0; font-weight: 600; color: #5F9E7F;">${sousTotal.toFixed(2)} DT</p>
                    <p style="margin: 0; color: #999; font-size: 0.85rem;">${article.prix.toFixed(2)} DT / unité</p>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    
    // Total
    html += `
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px; background: #f8f9fa; border-radius: 8px;">
            <h3 style="margin: 0; color: #333;">Total à payer</h3>
            <h2 style="margin: 0; color: #5F9E7F; font-size: 2rem;">${panierData.total.toFixed(2)} DT</h2>
        </div>
    `;
    
    // Informations client
    if (panierData.client) {
        html += `
            <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                <h4 style="margin: 0 0 10px 0; color: #5F9E7F;"><i class="fas fa-user"></i> Informations de livraison</h4>
                <p style="margin: 5px 0;"><strong>Nom:</strong> ${panierData.client.nom}</p>
                <p style="margin: 5px 0;"><strong>Email:</strong> ${panierData.client.email}</p>
                <p style="margin: 5px 0;"><strong>Téléphone:</strong> ${panierData.client.telephone || 'Non renseigné'}</p>
                <p style="margin: 5px 0;"><strong>Adresse:</strong> ${panierData.client.adresse}</p>
            </div>
        `;
    }
    
    container.innerHTML = html;
}

// Gestion de la sélection du mode de paiement
document.querySelectorAll('.payment-option').forEach(option => {
    option.addEventListener('click', function() {
        // Désélectionner toutes les options
        document.querySelectorAll('.payment-option').forEach(opt => {
            opt.style.borderColor = '#e0e0e0';
            opt.style.background = 'white';
            opt.querySelector('.fa-check-circle').style.display = 'none';
        });
        
        // Sélectionner l'option cliquée
        this.style.borderColor = '#5F9E7F';
        this.style.background = '#f0f8f4';
        this.querySelector('.fa-check-circle').style.display = 'block';
        
        methodePaiementSelectionnee = this.dataset.method;
        
        // Masquer tous les formulaires
        document.getElementById('cardForm').style.display = 'none';
        document.getElementById('stripeForm').style.display = 'none';
        document.getElementById('paypalForm').style.display = 'none';
        document.getElementById('virementInfo').style.display = 'none';
        
        // Afficher le formulaire correspondant
        if (methodePaiementSelectionnee === 'card') {
            document.getElementById('cardForm').style.display = 'block';
        } else if (methodePaiementSelectionnee === 'stripe') {
            document.getElementById('stripeForm').style.display = 'block';
            initStripe();
        } else if (methodePaiementSelectionnee === 'paypal') {
            document.getElementById('paypalForm').style.display = 'block';
            initPayPal();
        } else if (methodePaiementSelectionnee === 'virement') {
            document.getElementById('virementInfo').style.display = 'block';
            // Afficher la référence de virement
            const refElement = document.getElementById('referenceVirement');
            if (refElement && panierData.numeroCommande) {
                refElement.textContent = panierData.numeroCommande;
            }
        }
        
        // Activer le bouton de paiement
        const btnPayer = document.getElementById('btnPayer');
        
        // Masquer le bouton pour PayPal (utilise son propre bouton)
        if (methodePaiementSelectionnee === 'paypal') {
            btnPayer.style.display = 'none';
        } else {
            btnPayer.style.display = 'block';
            btnPayer.disabled = false;
            btnPayer.style.opacity = '1';
            btnPayer.style.cursor = 'pointer';
            
            // Mettre à jour le texte du bouton
            if (methodePaiementSelectionnee === 'card') {
                btnPayer.innerHTML = '<i class="fas fa-lock"></i> Payer ' + panierData.total.toFixed(2) + ' DT';
            } else if (methodePaiementSelectionnee === 'stripe') {
                btnPayer.innerHTML = '<i class="fas fa-lock"></i> Payer avec Stripe ' + panierData.total.toFixed(2) + ' DT';
            } else if (methodePaiementSelectionnee === 'virement') {
                btnPayer.innerHTML = '<i class="fas fa-check"></i> Confirmer la commande';
            }
        }
    });
});

// Initialiser Stripe
function initStripe() {
    if (stripe) return; // Déjà initialisé
    
    try {
        // Remplacez par votre clé publique Stripe
        stripe = Stripe('pk_test_YOUR_PUBLISHABLE_KEY_HERE');
        
        const elements = stripe.elements();
        stripeCardElement = elements.create('card', {
            style: {
                base: {
                    fontSize: '16px',
                    color: '#32325d',
                    fontFamily: '"Work Sans", sans-serif',
                    '::placeholder': {
                        color: '#aab7c4',
                    },
                },
                invalid: {
                    color: '#dc3545',
                },
            },
        });
        
        stripeCardElement.mount('#stripe-card-element');
        
        stripeCardElement.on('change', function(event) {
            const displayError = document.getElementById('stripe-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });
    } catch (error) {
        console.error('Erreur lors de l\'initialisation de Stripe:', error);
        alert('Erreur lors de l\'initialisation du paiement Stripe. Veuillez réessayer.');
    }
}

// Initialiser PayPal
function initPayPal() {
    if (paypalButtonsRendered) return; // Déjà rendu
    
    const container = document.getElementById('paypal-button-container');
    container.innerHTML = ''; // Nettoyer le container
    
    if (!window.paypal) {
        console.error('PayPal SDK non chargé');
        container.innerHTML = '<p style="color: #dc3545;">Erreur: PayPal SDK non disponible</p>';
        return;
    }
    
    try {
        paypal.Buttons({
            createOrder: async function(data, actions) {
                // Créer d'abord la commande
                const commandeData = {
                    nom: panierData.client.nom,
                    email: panierData.client.email,
                    telephone: panierData.client.telephone || '',
                    adresse: panierData.client.adresse,
                    methode_paiement: 'paypal'
                };
                
                const response = await fetch('../../controller/CommandeController.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(commandeData)
                });
                
                const result = await response.json();
                
                if (!result.success) {
                    throw new Error(result.message || 'Erreur lors de la création de la commande');
                }
                
                // Stocker le numéro de commande
                localStorage.setItem('paypal_numero_commande', result.numero_commande);
                
                // Créer la commande PayPal
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: panierData.total.toFixed(2),
                            currency_code: 'TND'
                        },
                        description: `Commande PeaceConnect ${result.numero_commande}`
                    }]
                });
            },
            onApprove: async function(data, actions) {
                // Capturer le paiement
                const order = await actions.order.capture();
                
                const numeroCommande = localStorage.getItem('paypal_numero_commande');
                
                // Confirmer le paiement côté serveur
                await fetch('../../controller/PaiementController.php?action=confirmer', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        numero_commande: numeroCommande,
                        methode_paiement: 'paypal',
                        transaction_id: order.id,
                        statut_paiement: 'paye',
                        payment_method_details: {
                            payer: order.payer,
                            status: order.status
                        }
                    })
                });
                
                // Nettoyer le localStorage
                localStorage.removeItem('panierCheckout');
                localStorage.removeItem('panier');
                localStorage.removeItem('paypal_numero_commande');
                
                // Rediriger vers la page de confirmation
                window.location.href = `confirmation.html?numero=${numeroCommande}`;
            },
            onError: function(err) {
                console.error('Erreur PayPal:', err);
                alert('Erreur lors du paiement PayPal. Veuillez réessayer.');
            }
        }).render('#paypal-button-container');
        
        paypalButtonsRendered = true;
    } catch (error) {
        console.error('Erreur lors de l\'initialisation de PayPal:', error);
        container.innerHTML = '<p style="color: #dc3545;">Erreur lors de l\'initialisation de PayPal</p>';
    }
}

// Formatage automatique du numéro de carte
document.getElementById('cardNumber')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\s/g, '');
    let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
    e.target.value = formattedValue;
});

// Formatage de la date d'expiration
document.getElementById('cardExpiry')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length >= 2) {
        value = value.slice(0, 2) + '/' + value.slice(2, 4);
    }
    e.target.value = value;
});

// Validation du CVV (chiffres uniquement)
document.getElementById('cardCVV')?.addEventListener('input', function(e) {
    e.target.value = e.target.value.replace(/\D/g, '');
});

// Traitement du paiement
document.getElementById('btnPayer')?.addEventListener('click', async function() {
    if (!methodePaiementSelectionnee) {
        alert('Veuillez sélectionner un mode de paiement');
        return;
    }
    
    // Validation pour carte bancaire
    if (methodePaiementSelectionnee === 'card') {
        const cardNumber = document.getElementById('cardNumber').value.replace(/\s/g, '');
        const cardExpiry = document.getElementById('cardExpiry').value;
        const cardCVV = document.getElementById('cardCVV').value;
        const cardName = document.getElementById('cardName').value;
        
        if (!cardNumber || cardNumber.length < 13) {
            alert('Numéro de carte invalide');
            return;
        }
        
        if (!cardExpiry || cardExpiry.length !== 5) {
            alert('Date d\'expiration invalide (MM/AA)');
            return;
        }
        
        if (!cardCVV || cardCVV.length < 3) {
            alert('CVV invalide');
            return;
        }
        
        if (!cardName || cardName.trim().length < 3) {
            alert('Nom sur la carte invalide');
            return;
        }
    }
    
    // Désactiver le bouton pendant le traitement
    this.disabled = true;
    this.style.opacity = '0.6';
    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Traitement en cours...';
    
    try {
        if (methodePaiementSelectionnee === 'stripe') {
            await traiterPaiementStripe();
        } else {
            await traiterPaiementStandard();
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur lors du paiement: ' + error.message);
        
        // Réactiver le bouton
        this.disabled = false;
        this.style.opacity = '1';
        restaurerTexteBtn();
    }
});

// Traiter le paiement Stripe
async function traiterPaiementStripe() {
    if (!stripe || !stripeCardElement) {
        throw new Error('Stripe n\'est pas correctement initialisé');
    }
    
    // Créer la commande d'abord
    const commandeData = {
        nom: panierData.client.nom,
        email: panierData.client.email,
        telephone: panierData.client.telephone || '',
        adresse: panierData.client.adresse,
        methode_paiement: 'stripe'
    };
    
    const response = await fetch('../../controller/CommandeController.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(commandeData)
    });
    
    const result = await response.json();
    
    if (!result.success) {
        throw new Error(result.message || 'Erreur lors de la création de la commande');
    }
    
    // Créer le PaymentIntent via Stripe
    const {error, paymentIntent} = await stripe.confirmCardPayment(
        'pi_test_secret_key', // En production, obtenir cela depuis le serveur
        {
            payment_method: {
                card: stripeCardElement,
                billing_details: {
                    name: panierData.client.nom,
                    email: panierData.client.email,
                }
            }
        }
    );
    
    if (error) {
        throw new Error(error.message);
    }
    
    if (paymentIntent.status === 'succeeded') {
        // Confirmer le paiement côté serveur
        await fetch('../../controller/PaiementController.php?action=confirmer', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                numero_commande: result.numero_commande,
                methode_paiement: 'stripe',
                transaction_id: paymentIntent.id,
                payment_intent_id: paymentIntent.id,
                statut_paiement: 'paye',
                payment_method_details: {
                    status: paymentIntent.status,
                    payment_method: paymentIntent.payment_method
                }
            })
        });
        
        // Nettoyer et rediriger
        localStorage.removeItem('panierCheckout');
        localStorage.removeItem('panier');
        window.location.href = `confirmation.html?numero=${result.numero_commande}`;
    }
}

// Traiter le paiement standard (carte ou virement)
async function traiterPaiementStandard() {
    // Préparer les données de la commande
    const commandeData = {
        nom: panierData.client.nom,
        email: panierData.client.email,
        telephone: panierData.client.telephone || '',
        adresse: panierData.client.adresse,
        methode_paiement: methodePaiementSelectionnee
    };
    
    // Envoyer la commande au serveur
    const response = await fetch('../../controller/CommandeController.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(commandeData)
    });
    
    const result = await response.json();
    
    if (result.success) {
        // Simuler le traitement du paiement
        await simulerPaiement(result.numero_commande, methodePaiementSelectionnee);
        
        // Nettoyer le localStorage
        localStorage.removeItem('panierCheckout');
        localStorage.removeItem('panier');
        
        // Rediriger vers la page de confirmation
        window.location.href = `confirmation.html?numero=${result.numero_commande}`;
    } else {
        throw new Error(result.message || 'Erreur lors de la création de la commande');
    }
}

function restaurerTexteBtn() {
    const btnPayer = document.getElementById('btnPayer');
    if (methodePaiementSelectionnee === 'card') {
        btnPayer.innerHTML = '<i class="fas fa-lock"></i> Payer ' + panierData.total.toFixed(2) + ' DT';
    } else if (methodePaiementSelectionnee === 'stripe') {
        btnPayer.innerHTML = '<i class="fas fa-lock"></i> Payer avec Stripe ' + panierData.total.toFixed(2) + ' DT';
    } else if (methodePaiementSelectionnee === 'virement') {
        btnPayer.innerHTML = '<i class="fas fa-check"></i> Confirmer la commande';
    }
}

// Simuler le traitement du paiement (à remplacer par une vraie intégration)
async function simulerPaiement(numeroCommande, methode) {
    // Simuler un délai de traitement
    await new Promise(resolve => setTimeout(resolve, 1500));
    
    // Générer un ID de transaction fictif
    const transactionId = 'TXN-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9).toUpperCase();
    
    // Mettre à jour le statut de paiement
    const response = await fetch('../../controller/PaiementController.php?action=confirmer', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            numero_commande: numeroCommande,
            methode_paiement: methode,
            transaction_id: transactionId,
            statut_paiement: methode === 'virement' ? 'en_attente' : 'paye'
        })
    });
    
    const result = await response.json();
    
    if (!result.success) {
        throw new Error('Erreur lors de la confirmation du paiement');
    }
    
    return result;
}

// Initialiser la page
document.addEventListener('DOMContentLoaded', function() {
    afficherResume();
    
    // Vérifier si le panier est vide
    if (!panierData.articles || panierData.articles.length === 0) {
        document.querySelector('.payment-methods').style.display = 'none';
        document.getElementById('btnPayer').style.display = 'none';
    }
});
