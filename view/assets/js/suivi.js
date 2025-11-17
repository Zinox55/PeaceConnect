/**
 * Gestion du suivi de commande
 */

const COMMANDE_API_URL = '../../controller/CommandeController.php';

function validateNumeroCommande(numero) {
    const trimmed = numero.trim();
    if (trimmed.length === 0) {
        return { valid: false, message: 'Le numéro de commande est obligatoire' };
    }
    // Format: CMD-2025-123456
    const pattern = /^CMD-\d{4}-\d{6}$/;
    if (!pattern.test(trimmed)) {
        return { valid: false, message: 'Format invalide (ex: CMD-2025-123456)' };
    }
    return { valid: true, message: '' };
}

function showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.style.cssText = `
        position: fixed; top: 20px; right: 20px;
        background-color: #e74c3c; color: white;
        padding: 15px 20px; border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2); z-index: 9999;
    `;
    errorDiv.textContent = message;
    document.body.appendChild(errorDiv);
    setTimeout(() => errorDiv.remove(), 3000);
}

function getStatutBadge(statut) {
    const statuts = {
        'en_attente': { 
            label: 'En attente', 
            color: '#ffc107', 
            icon: 'fa-clock' 
        },
        'confirmee': { 
            label: 'Confirmée', 
            color: '#17a2b8', 
            icon: 'fa-check' 
        },
        'livree': { 
            label: 'Livrée', 
            color: '#28a745', 
            icon: 'fa-check-circle' 
        },
        'annulee': { 
            label: 'Annulée', 
            color: '#dc3545', 
            icon: 'fa-times-circle' 
        }
    };
    
    const info = statuts[statut] || statuts['en_attente'];
    return `<span style="color: ${info.color}; font-weight: 600; background-color: ${info.color}15; padding: 6px 14px; border-radius: 20px; font-size: 0.9rem; display: inline-block;">
        <i class="fas ${info.icon}"></i> ${info.label}
    </span>`;
}

function afficherCommande(commande, details) {
    const resultDiv = document.querySelector('.suivi-result');
    if (!resultDiv) return;
    
    const dateCommande = new Date(commande.date_commande).toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    let produitsHTML = '';
    details.forEach(detail => {
        const imagePath = detail.image ? `../assets/img/${detail.image}` : '../assets/img/logo.png';
        produitsHTML += `
            <div style="display: flex; align-items: center; gap: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px; margin-bottom: 10px;">
                <img src="${imagePath}" alt="${detail.nom}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px;" onerror="this.src='../assets/img/logo.png'">
                <div style="flex: 1;">
                    <h5 style="margin: 0 0 5px 0; color: #333;">${detail.nom}</h5>
                    <p style="margin: 0; color: #6c757d; font-size: 0.9rem;">
                        Quantité: ${detail.quantite} × ${parseFloat(detail.prix_unitaire).toFixed(2)} €
                    </p>
                </div>
                <div style="text-align: right;">
                    <strong style="color: #5F9E7F; font-size: 1.1rem;">${(detail.quantite * detail.prix_unitaire).toFixed(2)} €</strong>
                </div>
            </div>
        `;
    });
    
    resultDiv.innerHTML = `
        <div style="text-align: center; margin-bottom: 30px;">
            <i class="fas fa-box-open" style="font-size: 3rem; color: #5F9E7F; margin-bottom: 15px;"></i>
            <h3 style="color: #5F9E7F; margin-bottom: 10px; font-size: 1.8rem;">
                Commande ${commande.numero_commande}
            </h3>
            <div style="margin: 20px 0;">
                ${getStatutBadge(commande.statut)}
            </div>
        </div>
        
        <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 25px;">
            <h4 style="color: #333; margin-bottom: 15px; font-size: 1.1rem;">
                <i class="fas fa-info-circle" style="color: #5F9E7F;"></i> Informations
            </h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <p style="margin: 0 0 8px 0; color: #6c757d; font-size: 0.9rem;">Date de commande</p>
                    <p style="margin: 0; color: #333; font-weight: 500;">${dateCommande}</p>
                </div>
                <div>
                    <p style="margin: 0 0 8px 0; color: #6c757d; font-size: 0.9rem;">Nombre d'articles</p>
                    <p style="margin: 0; color: #333; font-weight: 500;">${commande.nb_articles} article(s)</p>
                </div>
                <div>
                    <p style="margin: 0 0 8px 0; color: #6c757d; font-size: 0.9rem;">Client</p>
                    <p style="margin: 0; color: #333; font-weight: 500;">${commande.nom_client}</p>
                </div>
                <div>
                    <p style="margin: 0 0 8px 0; color: #6c757d; font-size: 0.9rem;">Email</p>
                    <p style="margin: 0; color: #333; font-weight: 500;">${commande.email_client}</p>
                </div>
            </div>
            ${commande.telephone_client ? `
                <div style="margin-top: 15px;">
                    <p style="margin: 0 0 8px 0; color: #6c757d; font-size: 0.9rem;">Téléphone</p>
                    <p style="margin: 0; color: #333; font-weight: 500;">${commande.telephone_client}</p>
                </div>
            ` : ''}
            <div style="margin-top: 15px;">
                <p style="margin: 0 0 8px 0; color: #6c757d; font-size: 0.9rem;">Adresse de livraison</p>
                <p style="margin: 0; color: #333; font-weight: 500;">${commande.adresse_client}</p>
            </div>
        </div>
        
        <div style="margin-bottom: 25px;">
            <h4 style="color: #333; margin-bottom: 15px; font-size: 1.1rem;">
                <i class="fas fa-shopping-bag" style="color: #5F9E7F;"></i> Articles commandés
            </h4>
            ${produitsHTML}
        </div>
        
        <div style="background: linear-gradient(135deg, #5F9E7F 0%, #4a7c5f 100%); color: white; padding: 20px; border-radius: 8px; text-align: center; margin-bottom: 25px;">
            <p style="margin: 0 0 5px 0; font-size: 0.9rem; opacity: 0.9;">Total de la commande</p>
            <p style="margin: 0; font-size: 2rem; font-weight: 700;">${parseFloat(commande.total).toFixed(2)} €</p>
        </div>
        
        <div style="text-align: center; padding-top: 20px; border-top: 1px solid #ddd;">
            <a href="produits.html" class="btn" style="background: #5F9E7F; color: white; padding: 12px 24px; border-radius: 5px; text-decoration: none; display: inline-block;">
                <i class="fas fa-shopping-cart"></i> Continuer mes achats
            </a>
        </div>
    `;
    
    resultDiv.style.display = 'block';
    resultDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function rechercherCommande(numero) {
    const submitBtn = document.querySelector('.suivi-form button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Recherche...';
    
    fetch(`${COMMANDE_API_URL}?action=suivre&numero=${encodeURIComponent(numero)}`)
        .then(response => response.json())
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-search"></i> Suivre ma commande';
            
            if (data.success) {
                afficherCommande(data.commande, data.details);
            } else {
                showError(data.message);
                const resultDiv = document.querySelector('.suivi-result');
                if (resultDiv) resultDiv.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showError('Erreur de connexion');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-search"></i> Suivre ma commande';
        });
}

document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si un numéro est passé dans l'URL
    const urlParams = new URLSearchParams(window.location.search);
    const numeroParam = urlParams.get('numero');
    
    if (numeroParam) {
        const input = document.getElementById('numero_commande');
        if (input) {
            input.value = numeroParam;
            rechercherCommande(numeroParam);
        }
    }
    
    const form = document.querySelector('.suivi-form');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const input = document.getElementById('numero_commande');
        const numero = input.value.trim();
        
        const validation = validateNumeroCommande(numero);
        if (!validation.valid) {
            showError(validation.message);
            input.style.borderColor = '#e74c3c';
            return;
        }
        
        input.style.borderColor = '#5F9E7F';
        rechercherCommande(numero);
    });
    
    // Validation en temps réel
    const input = document.getElementById('numero_commande');
    if (input) {
        input.addEventListener('input', function() {
            if (this.value.trim() !== '') {
                const validation = validateNumeroCommande(this.value);
                this.style.borderColor = validation.valid ? '#2ecc71' : '#e74c3c';
            } else {
                this.style.borderColor = '#e0e0e0';
            }
        });
    }
});
