/**
 * Gestion des produits - FrontOffice
 */

const API_URL = '../../controller/ProduitController.php';
const PANIER_API_URL = '../../controller/PanierController.php';

function loadProduitsFront() {
    fetch(API_URL)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayProduitsFront(data.data);
            }
        })
        .catch(error => console.error('Erreur:', error));
}

function displayProduitsFront(produits) {
    const grid = document.querySelector('.produits-grid');
    if (!grid) return;
    
    grid.innerHTML = '';
    
    produits.forEach(produit => {
        const card = document.createElement('div');
        card.className = 'card';
        card.setAttribute('data-produit-id', produit.id);
        
        const rating = (Math.random() * 1 + 4).toFixed(1);
        const reviews = Math.floor(Math.random() * 300 + 50);
        const fullStars = Math.floor(rating);
        const hasHalfStar = rating % 1 >= 0.5;
        
        let starsHTML = '';
        for (let i = 0; i < fullStars; i++) {
            starsHTML += '<i class="fas fa-star"></i>';
        }
        if (hasHalfStar) {
            starsHTML += '<i class="fas fa-star-half-alt"></i>';
        }
        const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
        for (let i = 0; i < emptyStars; i++) {
            starsHTML += '<i class="far fa-star"></i>';
        }
        
        const imagePath = produit.image ? `../assets/img/${produit.image}` : '../assets/img/logo.png';
        const stock = parseInt(produit.stock);
        const stockClass = stock === 0 ? 'rupture' : stock < 10 ? 'faible' : 'ok';
        const stockLabel = stock === 0 ? 'Rupture de stock' : stock < 10 ? `Plus que ${stock} en stock !` : `${stock} en stock`;
        
        card.innerHTML = `
            <div class="card-img">
                <img src="${imagePath}" alt="${produit.nom}" style="width: 100%; height: 200px; object-fit: cover;" onerror="this.src='../assets/img/logo.png'">
                ${stock < 10 ? '<span class="badge-stock" style="position: absolute; top: 10px; right: 10px; background: ' + (stock === 0 ? '#e74c3c' : '#f39c12') + '; color: white; padding: 5px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;"><i class="fas fa-exclamation-triangle"></i> ' + (stock === 0 ? 'Rupture' : 'Stock faible') + '</span>' : ''}
            </div>
            <div class="card-content">
                <h3>${produit.nom}</h3>
                <div class="vendor">PeaceConnect</div>
                <div class="rating">
                    <span class="stars">${starsHTML}</span>
                    <span class="count">${rating} (${reviews})</span>
                </div>
                <p class="description" style="color: #6C757D; font-size: 0.9rem; margin: 10px 0; line-height: 1.4;">${produit.description || ''}</p>
                <div class="stock-info" data-stock="${stock}" style="margin: 10px 0; padding: 8px; background: ${stock === 0 ? '#fee' : stock < 10 ? '#fef3e0' : '#e8f5e9'}; border-radius: 5px; text-align: center;">
                    <span style="color: ${stock === 0 ? '#c0392b' : stock < 10 ? '#f39c12' : '#27ae60'}; font-weight: 600; font-size: 0.9rem;">
                        <i class="fas ${stock === 0 ? 'fa-times-circle' : stock < 10 ? 'fa-exclamation-circle' : 'fa-check-circle'}"></i>
                        ${stockLabel}
                    </span>
                </div>
                <p class="prix">${parseFloat(produit.prix).toFixed(2)} €</p>
                <button class="btn btn-success btn-add-to-cart" data-id="${produit.id}" data-nom="${produit.nom}" data-prix="${produit.prix}" data-stock="${stock}" ${stock <= 0 ? 'disabled' : ''} style="width: 100%; margin-top: 15px; text-transform: uppercase; letter-spacing: 0.5px; background-color: ${stock <= 0 ? '#95a5a6' : '#5F9E7F'}; cursor: ${stock <= 0 ? 'not-allowed' : 'pointer'};">
                    <i class="fas ${stock <= 0 ? 'fa-ban' : 'fa-cart-plus'}"></i> ${stock <= 0 ? 'Rupture de stock' : 'Ajouter au panier'}
                </button>
            </div>
        `;
        
        grid.appendChild(card);
    });
    
    attachCartButtons();
}

function attachCartButtons() {
    document.querySelectorAll('.btn-add-to-cart').forEach(btn => {
        btn.addEventListener('click', function() {
            if (this.disabled) return;
            
            const id = this.getAttribute('data-id');
            const nom = this.getAttribute('data-nom');
            addToCart(id, nom);
        });
    });
}

function addToCart(produit_id, nom) {
    // Vérifier le stock avant d'ajouter
    const card = document.querySelector(`[data-produit-id="${produit_id}"]`);
    const stockInfo = card ? card.querySelector('.stock-info') : null;
    const currentStock = stockInfo ? parseInt(stockInfo.getAttribute('data-stock')) : 0;
    
    if (currentStock <= 0) {
        showNotification('Stock insuffisant', false);
        return;
    }
    
    fetch(PANIER_API_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            produit_id: produit_id,
            quantite: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(`${nom} ajouté au panier`);
            updateCartCount();
            
            // Diminuer le stock visuellement
            if (stockInfo && card) {
                const newStock = currentStock - 1;
                stockInfo.setAttribute('data-stock', newStock);
                
                const stockLabel = newStock === 0 ? 'Rupture de stock' : newStock < 10 ? `Plus que ${newStock} en stock !` : `${newStock} en stock`;
                const bgColor = newStock === 0 ? '#fee' : newStock < 10 ? '#fef3e0' : '#e8f5e9';
                const textColor = newStock === 0 ? '#c0392b' : newStock < 10 ? '#f39c12' : '#27ae60';
                const icon = newStock === 0 ? 'fa-times-circle' : newStock < 10 ? 'fa-exclamation-circle' : 'fa-check-circle';
                
                stockInfo.style.background = bgColor;
                stockInfo.innerHTML = `
                    <span style="color: ${textColor}; font-weight: 600; font-size: 0.9rem;">
                        <i class="fas ${icon}"></i>
                        ${stockLabel}
                    </span>
                `;
                
                // Désactiver le bouton si stock = 0
                const btn = card.querySelector('.btn-add-to-cart');
                if (newStock <= 0 && btn) {
                    btn.disabled = true;
                    btn.style.backgroundColor = '#95a5a6';
                    btn.style.cursor = 'not-allowed';
                    btn.innerHTML = '<i class="fas fa-ban"></i> Rupture de stock';
                }
            }
        } else {
            showNotification(data.message, false);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showNotification('Erreur de connexion', false);
    });
}

function updateCartCount() {
    fetch(`${PANIER_API_URL}?action=count`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const cartCount = document.querySelector('.cart-count');
                if (cartCount) {
                    cartCount.textContent = `(${data.count})`;
                }
            }
        })
        .catch(error => console.error('Erreur:', error));
}

function showNotification(message, isSuccess = true) {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed; top: 20px; right: 20px;
        background-color: ${isSuccess ? '#5F9E7F' : '#e74c3c'};
        color: white; padding: 15px 20px; border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2); z-index: 9999;
        animation: slideIn 0.3s ease-out;
    `;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    }, 2000);
}

// Animations CSS
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);

document.addEventListener('DOMContentLoaded', function() {
    loadProduitsFront();
    updateCartCount();
});
