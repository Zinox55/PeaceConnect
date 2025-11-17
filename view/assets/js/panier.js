/**
 * Gestion du panier
 */

const PANIER_API_URL = '../../controller/PanierController.php';

function loadPanier() {
    fetch(PANIER_API_URL)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayPanier(data.data, data.total);
                updateCartCount(data.count);
            }
        })
        .catch(error => console.error('Erreur:', error));
}

function displayPanier(items, total) {
    const container = document.querySelector('.panier-container');
    if (!container) return;
    
    if (items.length === 0) {
        container.innerHTML = `
            <div style="text-align: center; padding: 60px 20px;">
                <i class="fas fa-shopping-cart" style="font-size: 4rem; color: #ccc; margin-bottom: 20px;"></i>
                <h3 style="color: #6C757D;">Votre panier est vide</h3>
                <p style="color: #999; margin: 10px 0;">Ajoutez des produits pour commencer vos achats</p>
                <a href="produits.html" class="btn btn-success" style="margin-top: 20px; display: inline-block; background-color: #5F9E7F;">
                    <i class="fas fa-shopping-bag"></i> Découvrir nos produits
                </a>
            </div>
        `;
        return;
    }
    
    let html = '<div style="margin-bottom: 20px;">';
    html += `<p style="color: #6C757D; font-size: 0.95rem;"><i class="fas fa-info-circle"></i> Vous avez <strong>${items.length}</strong> produit(s) dans votre panier</p>`;
    html += '</div>';
    
    items.forEach(item => {
        html += `
            <div class="panier-item" data-id="${item.panier_id}" style="background: #f8f9fa; padding: 15px; margin-bottom: 12px; border-radius: 8px; border-left: 4px solid #5F9E7F; transition: all 0.3s;">
                <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                    <img src="../assets/img/${item.image || 'logo.png'}" alt="${item.nom}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);" onerror="this.src='../assets/img/logo.png'">
                    <div style="flex: 1; min-width: 200px;">
                        <h4 style="margin: 0 0 5px 0; color: #333; font-size: 1.1rem;">${item.nom}</h4>
                        <p style="margin: 0; color: #5F9E7F; font-weight: 600; font-size: 1.05rem;">${parseFloat(item.prix).toFixed(2)} € / unité</p>
                    </div>
                    <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <label style="color: #6C757D; font-size: 0.9rem; font-weight: 500;">Quantité:</label>
                            <input type="number" value="${item.quantite}" min="1" class="quantite-input" data-id="${item.panier_id}" style="width: 70px; padding: 8px; border: 2px solid #5F9E7F; border-radius: 6px; text-align: center; font-weight: 600; font-size: 1rem;">
                        </div>
                        <div style="text-align: right;">
                            <div style="color: #6C757D; font-size: 0.85rem; margin-bottom: 3px;">Sous-total</div>
                            <div style="color: #5F9E7F; font-weight: 700; font-size: 1.2rem;">${parseFloat(item.sous_total).toFixed(2)} €</div>
                        </div>
                        <button class="btn btn-danger btn-remove" data-id="${item.panier_id}" title="Retirer du panier" style="padding: 8px 12px; font-size: 1.1rem; border-radius: 6px; background: #e74c3c; color: white; border: none; cursor: pointer; transition: all 0.3s;">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += `
        <div class="total" style="margin-top: 30px; padding: 25px; background: linear-gradient(135deg, #5F9E7F 0%, #4a7c5f 100%); border-radius: 12px; box-shadow: 0 4px 15px rgba(95, 158, 127, 0.3);">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                <div>
                    <div style="color: rgba(255,255,255,0.9); font-size: 0.95rem; margin-bottom: 8px;">
                        <i class="fas fa-shopping-cart"></i> Total de votre panier
                    </div>
                    <div style="color: white; font-size: 2rem; font-weight: 700;">
                        ${parseFloat(total).toFixed(2)} €
                    </div>
                </div>
                <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                    <button class="btn btn-danger" id="btn-vider-panier" style="padding: 12px 20px; background: rgba(255,255,255,0.2); color: white; border: 2px solid rgba(255,255,255,0.5); font-weight: 500; transition: all 0.3s;">
                        <i class="fas fa-trash"></i> Vider le panier
                    </button>
                    <a href="commande.html" class="btn btn-success" style="background-color: white; color: #5F9E7F; text-transform: uppercase; letter-spacing: 0.5px; padding: 12px 28px; font-weight: 700; box-shadow: 0 2px 8px rgba(0,0,0,0.2); transition: all 0.3s;">
                        <i class="fas fa-credit-card"></i> Passer commande
                    </a>
                </div>
            </div>
        </div>
    `;
    
    container.innerHTML = html;
    attachPanierEvents();
}

function attachPanierEvents() {
    // Boutons de suppression
    document.querySelectorAll('.btn-remove').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            removeFromCart(id);
        });
    });
    
    // Inputs de quantité
    document.querySelectorAll('.quantite-input').forEach(input => {
        input.addEventListener('change', function() {
            const id = this.getAttribute('data-id');
            const quantite = parseInt(this.value);
            if (quantite > 0) {
                updateQuantite(id, quantite);
            } else {
                this.value = 1;
            }
        });
    });
    
    // Bouton vider panier
    const btnVider = document.getElementById('btn-vider-panier');
    if (btnVider) {
        btnVider.addEventListener('click', function() {
            if (confirm('Êtes-vous sûr de vouloir vider le panier ?')) {
                viderPanier();
            }
        });
    }
}

function removeFromCart(panier_id) {
    fetch(PANIER_API_URL, {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ panier_id: panier_id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message);
            loadPanier();
        } else {
            showNotification(data.message, false);
        }
    })
    .catch(error => console.error('Erreur:', error));
}

function updateQuantite(panier_id, quantite) {
    fetch(PANIER_API_URL, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ panier_id: panier_id, quantite: quantite })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadPanier();
        } else {
            showNotification(data.message, false);
        }
    })
    .catch(error => console.error('Erreur:', error));
}

function viderPanier() {
    fetch(`${PANIER_API_URL}?action=vider`, {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message);
            loadPanier();
        } else {
            showNotification(data.message, false);
        }
    })
    .catch(error => console.error('Erreur:', error));
}

function updateCartCount(count) {
    const cartCount = document.querySelector('.cart-count');
    if (cartCount) {
        cartCount.textContent = `(${count || 0})`;
    }
}

function showNotification(message, isSuccess = true) {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed; top: 20px; right: 20px;
        background-color: ${isSuccess ? '#5F9E7F' : '#e74c3c'};
        color: white; padding: 15px 20px; border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3); z-index: 9999;
        animation: slideInRight 0.4s ease-out;
        display: flex; align-items: center; gap: 10px;
        font-weight: 500;
    `;
    notification.innerHTML = `
        <i class="fas ${isSuccess ? 'fa-check-circle' : 'fa-exclamation-circle'}" style="font-size: 1.3rem;"></i>
        <span>${message}</span>
    `;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.4s ease-out';
        setTimeout(() => notification.remove(), 400);
    }, 3000);
}

// Ajouter les animations CSS
if (!document.getElementById('panier-animations')) {
    const style = document.createElement('style');
    style.id = 'panier-animations';
    style.textContent = `
        @keyframes slideInRight {
            from { transform: translateX(120%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(120%); opacity: 0; }
        }
        .panier-item {
            transition: all 0.3s ease;
        }
        .panier-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(95, 158, 127, 0.2) !important;
        }
        .btn-remove:hover {
            background: #c0392b !important;
            transform: scale(1.1);
        }
        .quantite-input:focus {
            outline: none;
            border-color: #5F9E7F;
            box-shadow: 0 0 0 3px rgba(95, 158, 127, 0.1);
        }
        #btn-vider-panier:hover {
            background: rgba(255,255,255,0.3) !important;
            border-color: white !important;
        }
    `;
    document.head.appendChild(style);
}

document.addEventListener('DOMContentLoaded', function() {
    loadPanier();
});
