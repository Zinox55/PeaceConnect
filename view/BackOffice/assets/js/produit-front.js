const API_URL = '../../controller/ProduitController.php';
const PANIER_API_URL = '../../controller/PanierController.php';

let allProducts = [];
let filteredProducts = [];
let selectedCategories = new Set();

function loadProduitsFront() {
    fetch(API_URL)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                allProducts = data.data;
                filteredProducts = [...allProducts];
                displayProduitsFront(filteredProducts);
                updateResultsCount();
            }
        })
        .catch(error => console.error('Erreur:', error));
}

function displayProduitsFront(produits) {
    const grid = document.querySelector('.produits-grid');
    if (!grid) return;
    
    grid.innerHTML = '';
    
    if (produits.length === 0) {
        grid.innerHTML = `
            <div style="grid-column: 1/-1; text-align: center; padding: 40px 20px;">
                <i class="fas fa-search" style="font-size: 3rem; color: #ccc; margin-bottom: 15px;"></i>
                <h3 style="color: #6C757D;">Aucun produit trouvé</h3>
                <p style="color: #999;">Essayez de modifier vos critères de recherche</p>
            </div>
        `;
        return;
    }
    
    produits.forEach(produit => {
        const card = document.createElement('div');
        card.className = 'card';
        
        // Suppression de la section notation/avis
        
        // Gérer le chemin d'image selon le format
        let imagePath = '../back/assets/img/logo.png';
        if (produit.image && produit.image.trim() !== '') {
            // Si l'image commence par 'produit_', c'est un fichier uploadé dans produits/
            if (produit.image.startsWith('produit_')) {
                imagePath = `../back/assets/img/produits/${produit.image}`;
            } else {
                // Sinon utiliser le chemin direct
                imagePath = `../back/assets/img/${produit.image}`;
            }
        }
        
        const stars = renderStars(produit.note != null ? parseInt(produit.note) : 5);
            const qrData = produit.code_barre && produit.code_barre.trim() !== ''
                ? `${produit.code_barre}`
                : `PRODUCT:${produit.id}|${produit.nom}`;
            const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=${encodeURIComponent(qrData)}`;
            const barcode = `<div class="qr" style="margin-top: 8px; display: flex; align-items: center; gap: 8px;">
                <img src="${qrUrl}" alt="QR Code" style="width: 80px; height: 80px; border: 1px solid #e0e0e0; border-radius: 6px; background: #fff;">
                <div style="font-size: 0.8rem; color: #666;">QR</div>
            </div>`;

        card.innerHTML = `
            <div class="card-img">
                <img src="${imagePath}" alt="${produit.nom}" style="width: 100%; height: 200px; object-fit: cover;" onerror="this.src='../back/assets/img/logo.png'">
            </div>
            <div class="card-content">
                <h3>${produit.nom}</h3>
                <div class="meta">
                  <div class="stars">${stars}</div>
                  <div class="vendor">PeaceConnect</div>
                  <p class="description" style="color: #6C757D; font-size: 0.9rem; line-height: 1.4;">${produit.description || ''}</p>
                  <p class="prix">${parseFloat(produit.prix).toFixed(2)} DT</p>
                  ${barcode}
                </div>
                <button class="btn btn-success btn-add-to-cart" data-id="${produit.id}" data-nom="${produit.nom}" data-prix="${produit.prix}" ${produit.stock <= 0 ? 'disabled' : ''} style="width: 100%; margin-top: 15px; text-transform: uppercase; letter-spacing: 0.5px; background-color: #5F9E7F;">
                    <i class="fas fa-cart-plus"></i> ${produit.stock <= 0 ? 'Rupture de stock' : 'Ajouter au panier'}
                </button>
            </div>
        `;
        
        grid.appendChild(card);
    });
    
    attachCartButtons();
}

function renderStars(note) {
    const max = 5;
    let html = '';
    for (let i = 1; i <= max; i++) {
        const filled = i <= note;
        html += `<i class="fas fa-star" style="color: ${filled ? '#f6c23e' : '#ddd'}; margin-right: 2px;"></i>`;
    }
    return html;
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
                // Inform global badge to update
                const evt = new CustomEvent('panier:updated', { detail: { count: data.count || 0 } });
                document.dispatchEvent(evt);
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
    initFilters();
});

// ========== FILTRAGE ET RECHERCHE ==========

function initFilters() {
    const searchInput = document.getElementById('searchInput');
    const sortFilter = document.getElementById('sortFilter');
    const priceFilter = document.getElementById('priceFilter');
    const stockFilter = document.getElementById('stockFilter');
    const resetBtn = document.getElementById('resetFilters');
    const categoryCheckboxes = document.querySelectorAll('.category-checkbox');
    const sidebar = document.querySelector('.filter-sidebar');
    const toggleBtn = document.querySelector('.sidebar-toggle');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            applyFilters();
        });
    }

    if (sortFilter) {
        sortFilter.addEventListener('change', function() {
            applyFilters();
        });
    }

    if (priceFilter) {
        priceFilter.addEventListener('change', function() {
            applyFilters();
        });
    }

    if (stockFilter) {
        stockFilter.addEventListener('change', function() {
            applyFilters();
        });
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            searchInput.value = '';
            sortFilter.value = 'default';
            priceFilter.value = 'all';
            stockFilter.value = 'all';
            selectedCategories.clear();
            categoryCheckboxes.forEach(cb => cb.checked = false);
            applyFilters();
            
            // Animation du bouton
            this.style.transform = 'rotate(360deg)';
            setTimeout(() => {
                this.style.transform = 'rotate(0deg)';
            }, 500);
        });
    }

    // Catégories
    categoryCheckboxes.forEach(cb => {
        cb.addEventListener('change', () => {
            if (cb.checked) {
                selectedCategories.add(cb.value.toLowerCase());
            } else {
                selectedCategories.delete(cb.value.toLowerCase());
            }
            applyFilters();
        });
    });

    // Gestion du bouton toggle des filtres
    const filterToggleBtn = document.getElementById('filterToggleBtn');
    const filterSidebar = document.getElementById('filterSidebar');
    const sidebarClose = document.getElementById('sidebarClose');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    
    // Fermer la sidebar par défaut au chargement
    if (filterSidebar) {
        filterSidebar.classList.add('hidden');
    }
    
    function openSidebar() {
        filterSidebar.classList.remove('hidden');
        filterToggleBtn.classList.add('active');
        filterToggleBtn.innerHTML = '<i class="fas fa-times"></i><span>Fermer</span>';
        if (window.innerWidth <= 768 && sidebarOverlay) {
            sidebarOverlay.classList.add('active');
        }
    }
    
    function closeSidebar() {
        filterSidebar.classList.add('hidden');
        filterToggleBtn.classList.remove('active');
        filterToggleBtn.innerHTML = '<i class="fas fa-sliders-h"></i><span>Filtres</span>';
        if (sidebarOverlay) {
            sidebarOverlay.classList.remove('active');
        }
    }
    
    if (filterToggleBtn && filterSidebar) {
        filterToggleBtn.addEventListener('click', function() {
            const isHidden = filterSidebar.classList.contains('hidden');
            if (isHidden) {
                openSidebar();
            } else {
                closeSidebar();
            }
        });
    }
    
    if (sidebarClose && filterSidebar) {
        sidebarClose.addEventListener('click', function() {
            closeSidebar();
        });
    }
    
    // Fermer la sidebar en cliquant sur l'overlay
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            closeSidebar();
        });
    }
    
    // Gérer le redimensionnement de la fenêtre
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768 && filterSidebar) {
            filterSidebar.classList.remove('hidden');
            if (filterToggleBtn) {
                filterToggleBtn.classList.remove('active');
            }
            if (sidebarOverlay) {
                sidebarOverlay.classList.remove('active');
            }
        }
    });

    // Mobile toggle (ancien code pour compatibilité)
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });
    }
}

function applyFilters() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const sortValue = document.getElementById('sortFilter').value;
    const priceValue = document.getElementById('priceFilter').value;
    const stockValue = document.getElementById('stockFilter').value;

    // Filtrer par recherche
    filteredProducts = allProducts.filter(product => {
        const matchesSearch = product.nom.toLowerCase().includes(searchTerm) || 
                            (product.description && product.description.toLowerCase().includes(searchTerm));
        
        // Filtrer par prix
        let matchesPrice = true;
        const prix = parseFloat(product.prix);
        if (priceValue === '50') {
            matchesPrice = prix < 50;
        } else if (priceValue === '100') {
            matchesPrice = prix >= 50 && prix <= 100;
        } else if (priceValue === '200') {
            matchesPrice = prix > 100 && prix <= 200;
        } else if (priceValue === '999') {
            matchesPrice = prix > 200;
        }
        
        // Filtrer par stock
        let matchesStock = true;
        const stock = parseInt(product.stock);
        if (stockValue === 'available') {
            matchesStock = stock > 0;
        } else if (stockValue === 'rupture') {
            matchesStock = stock === 0;
        } else if (stockValue === 'precommande') {
            matchesStock = stock < 0; // Pour les précommandes si applicable
        }
        
        // Filtrer par catégories (si le produit a une catégorie)
        let matchesCategory = true;
        if (selectedCategories.size > 0) {
            const cat = (product.categorie || product.category || '').toLowerCase();
            matchesCategory = selectedCategories.has(cat);
        }

        return matchesSearch && matchesPrice && matchesStock && matchesCategory;
    });

    // Trier
    if (sortValue === 'price-asc') {
        filteredProducts.sort((a, b) => parseFloat(a.prix) - parseFloat(b.prix));
    } else if (sortValue === 'price-desc') {
        filteredProducts.sort((a, b) => parseFloat(b.prix) - parseFloat(a.prix));
    } else if (sortValue === 'nouveautes') {
        filteredProducts.sort((a, b) => (b.id || 0) - (a.id || 0)); // Plus récent en premier
    } else if (sortValue === 'popularite') {
        // Simuler la popularité par ordre inverse de stock (plus vendu = moins de stock)
        filteredProducts.sort((a, b) => parseInt(a.stock) - parseInt(b.stock));
    }

    displayProduitsFront(filteredProducts);
    updateResultsCount();
}

function updateResultsCount() {
    const resultsCount = document.getElementById('resultsCount');
    if (resultsCount) {
        const total = allProducts.length;
        const shown = filteredProducts.length;
        
        if (shown === total) {
            resultsCount.textContent = `${total} produit${total > 1 ? 's' : ''} disponible${total > 1 ? 's' : ''}`;
        } else {
            resultsCount.textContent = `${shown} produit${shown > 1 ? 's' : ''} trouvé${shown > 1 ? 's' : ''} sur ${total}`;
        }
        resultsCount.style.display = 'block';
    }
}
