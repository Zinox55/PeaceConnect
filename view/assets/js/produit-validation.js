const API_URL = '../../controller/ProduitController.php';
const UPLOAD_URL = '../../controller/UploadController.php';

// Debug logs
console.log('=== Produit Validation JS Loaded ===');
console.log('jQuery available:', typeof $ !== 'undefined');
console.log('Bootstrap modal available:', typeof $.fn !== 'undefined' && typeof $.fn.modal !== 'undefined');

// Variables globales
let isEditMode = false;
let currentEditId = null;
let uploadedImageName = '';

// Éléments DOM
let modal, btnOpenModal, btnCancel, closeModal, form, modalTitleText;

// Fonctions de validation
function validateNom(nom) {
    const trimmedNom = nom.trim();
    if (trimmedNom.length === 0) {
        return { valid: false, message: 'Le nom est obligatoire' };
    }
    if (trimmedNom.length < 3) {
        return { valid: false, message: 'Le nom doit contenir au moins 3 caractères' };
    }
    return { valid: true, message: '' };
}

function validatePrix(prix) {
    if (prix === '' || prix === null || prix === undefined) {
        return { valid: false, message: 'Le prix est obligatoire' };
    }
    const prixNum = parseFloat(prix);
    if (isNaN(prixNum)) {
        return { valid: false, message: 'Le prix doit être un nombre valide' };
    }
    if (prixNum < 0) {
        return { valid: false, message: 'Le prix doit être positif' };
    }
    const prixStr = prix.toString();
    if (prixStr.includes('.')) {
        const decimals = prixStr.split('.')[1];
        if (decimals && decimals.length > 2) {
            return { valid: false, message: 'Maximum 2 décimales' };
        }
    }
    return { valid: true, message: '' };
}

function validateStock(stock) {
    if (stock === '' || stock === null || stock === undefined) {
        return { valid: false, message: 'Le stock est obligatoire' };
    }
    const stockNum = parseInt(stock, 10);
    if (isNaN(stockNum)) {
        return { valid: false, message: 'Le stock doit être un nombre entier' };
    }
    if (!Number.isInteger(parseFloat(stock))) {
        return { valid: false, message: 'Le stock doit être un entier' };
    }
    if (stockNum < 0) {
        return { valid: false, message: 'Le stock ne peut pas être négatif' };
    }
    return { valid: true, message: '' };
}

function showError(inputElement, message) {
    const oldError = inputElement.parentElement.querySelector('.error-message');
    if (oldError) oldError.remove();
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.style.color = '#e74c3c';
    errorDiv.style.fontSize = '0.85rem';
    errorDiv.style.marginTop = '5px';
    errorDiv.textContent = message;
    
    inputElement.style.borderColor = '#e74c3c';
    inputElement.parentElement.appendChild(errorDiv);
}

function clearError(inputElement) {
    const errorDiv = inputElement.parentElement.querySelector('.error-message');
    if (errorDiv) errorDiv.remove();
    inputElement.style.borderColor = '';
}

function showNotification(message, isSuccess = true) {
    const alertId = isSuccess ? 'alert-success' : 'alert-error';
    const alert = document.getElementById(alertId);
    
    if (alert) {
        alert.innerHTML = `<i class="fas fa-${isSuccess ? 'check-circle' : 'exclamation-circle'}"></i> ${message}`;
        alert.style.display = 'block';
        
        setTimeout(() => {
            alert.style.display = 'none';
        }, 5000);
    } else {
        // Fallback si les éléments alert n'existent pas
        const notif = document.createElement('div');
        notif.style.cssText = `
            position: fixed; top: 20px; right: 20px;
            background: ${isSuccess ? '#2ecc71' : '#e74c3c'};
            color: white; padding: 15px 20px; border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3); z-index: 9999;
            font-weight: 600;
        `;
        notif.textContent = message;
        document.body.appendChild(notif);
        setTimeout(() => notif.remove(), 4000);
    }
}

// Gestion du Modal
function openModal() {
    console.log('=== OPEN MODAL ===');
    isEditMode = false;
    currentEditId = null;
    uploadedImageName = '';
    
    if (modalTitleText) {
        modalTitleText.textContent = 'Ajouter un produit';
    }
    if (form) {
        form.reset();
        document.getElementById('produitId').value = '';
        document.getElementById('image').value = '';
        
        // Réinitialiser l'aperçu de l'image
        const imagePreview = document.getElementById('imagePreview');
        if (imagePreview) {
            imagePreview.style.display = 'none';
        }
    }
    clearAllErrors();
    
    // Utiliser Bootstrap modal si jQuery est chargé
    if (typeof $ !== 'undefined' && $.fn.modal) {
        console.log('Using Bootstrap modal');
        $('#produitModal').modal('show');
    } else {
        console.log('Using fallback modal');
        // Fallback sans jQuery
        const modal = document.getElementById('produitModal');
        if (modal) {
            // Supprimer les backdrops existants d'abord
            document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
            
            modal.classList.add('show');
            modal.style.display = 'block';
            modal.style.zIndex = '1050';
            modal.setAttribute('aria-modal', 'true');
            modal.removeAttribute('aria-hidden');
            document.body.classList.add('modal-open');
            
            // Ajouter le backdrop
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.style.zIndex = '1040';
            backdrop.id = 'modal-backdrop';
            document.body.appendChild(backdrop);
        }
    }
}

function closeModalFunc() {
    console.log('=== CLOSE MODAL ===');
    // Utiliser Bootstrap modal si jQuery est chargé
    if (typeof $ !== 'undefined' && $.fn.modal) {
        $('#produitModal').modal('hide');
    } else {
        // Fallback sans jQuery
        const modal = document.getElementById('produitModal');
        if (modal) {
            modal.classList.remove('show');
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
            modal.removeAttribute('aria-modal');
            document.body.classList.remove('modal-open');
            
            // Supprimer tous les backdrops
            document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
                backdrop.remove();
            });
        }
    }
    
    if (form) form.reset();
    clearAllErrors();
    isEditMode = false;
    currentEditId = null;
}

function clearAllErrors() {
    document.querySelectorAll('input, textarea').forEach(el => {
        clearError(el);
    });
}

function loadProduits() {
    fetch(API_URL)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayProduits(data.data);
            }
        })
        .catch(error => console.error('Erreur:', error));
}

function displayProduits(produits) {
    const tbody = document.querySelector('table tbody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    produits.forEach(produit => {
        const row = document.createElement('tr');
        const stock = parseInt(produit.stock);
        const stockText = stock === 0 ? 'Rupture' : stock === 1 ? '1 unité' : `${stock} unités`;
        const stockBadge = stock === 0 
            ? `<span class="badge badge-danger">${stockText}</span>` 
            : stock < 10 
            ? `<span class="badge badge-warning text-dark">${stockText}</span>` 
            : `<span class="badge badge-success">${stockText}</span>`;
        
        // Gérer l'affichage de l'image
        let imageHtml = '';
        if (produit.image && produit.image.trim() !== '') {
            const imagePath = produit.image.startsWith('produit_') 
                ? `../assets/img/produits/${produit.image}` 
                : `../assets/img/${produit.image}`;
            imageHtml = `<img src="${imagePath}" alt="${produit.nom}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px; border: 1px solid #ddd;">`;
        } else {
            imageHtml = '<span class="badge badge-secondary">Aucune image</span>';
        }
        
        row.innerHTML = `
            <td>${produit.id}</td>
            <td class="text-center">${imageHtml}</td>
            <td>${produit.nom}</td>
            <td>${produit.description || '-'}</td>
            <td>${parseFloat(produit.prix).toFixed(2)} DT</td>
            <td>${stockBadge}</td>
            <td class="text-nowrap">
                <button class="btn btn-sm btn-primary btn-edit" data-id="${produit.id}" title="Modifier">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-danger btn-delete" data-id="${produit.id}" title="Supprimer" style="margin-left: 5px;">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
    
    attachButtonEvents();
}

function attachButtonEvents() {
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            editProduit(this.getAttribute('data-id'));
        });
    });
    
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('Supprimer ce produit ?')) {
                deleteProduit(this.getAttribute('data-id'));
            }
        });
    });
}

async function uploadImage() {
    const fileInput = document.getElementById('imageFile');
    if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
        return null;
    }
    
    const formData = new FormData();
    formData.append('image', fileInput.files[0]);
    
    try {
        const response = await fetch(UPLOAD_URL, {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        
        if (data.success) {
            return data.filename;
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        throw error;
    }
}

async function createProduit(formData) {
    console.log('=== CREATE PRODUIT ===');
    console.log('FormData avant upload:', formData);
    console.log('API URL:', API_URL);
    
    const btnSave = document.getElementById('btnSave');
    const originalText = btnSave.innerHTML;
    btnSave.disabled = true;
    btnSave.innerHTML = '<span class="loading-spinner"></span> Enregistrement...';
    
    try {
        // Uploader l'image si un fichier est sélectionné
        const fileInput = document.getElementById('imageFile');
        if (fileInput && fileInput.files && fileInput.files.length > 0) {
            console.log('Upload de l\'image en cours...');
            const filename = await uploadImage();
            if (filename) {
                console.log('Image uploadée:', filename);
                formData.image = filename;
            } else {
                console.log('Aucun nom de fichier retourné');
                formData.image = '';
            }
        } else {
            console.log('Aucun fichier sélectionné');
            formData.image = '';
        }
        
        console.log('FormData après upload:', formData);
        
        const response = await fetch(API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('✅ ' + data.message, true);
            loadProduits();
            closeModalFunc();
        } else {
            if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    const input = document.getElementById(field);
                    if (input) showError(input, data.errors[field]);
                });
            }
            showNotification('❌ ' + (data.message || 'Erreur lors de l\'ajout'), false);
        }
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('❌ ' + error.message, false);
    } finally {
        btnSave.disabled = false;
        btnSave.innerHTML = originalText;
    }
}

function editProduit(id) {
    fetch(`${API_URL}?action=readOne&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const produit = data.data;
                
                isEditMode = true;
                currentEditId = id;
                
                // Remplir le formulaire
                document.getElementById('produitId').value = produit.id;
                document.getElementById('nom').value = produit.nom;
                document.getElementById('description').value = produit.description || '';
                document.getElementById('prix').value = produit.prix;
                document.getElementById('stock').value = produit.stock;
                document.getElementById('image').value = produit.image || '';
                
                // Afficher l'aperçu de l'image existante
                const imagePreview = document.getElementById('imagePreview');
                const previewImg = document.getElementById('previewImg');
                if (produit.image && produit.image.trim() !== '' && imagePreview && previewImg) {
                    const imagePath = produit.image.startsWith('produit_') 
                        ? `../assets/img/produits/${produit.image}` 
                        : `../assets/img/${produit.image}`;
                    previewImg.src = imagePath;
                    imagePreview.style.display = 'block';
                } else if (imagePreview) {
                    imagePreview.style.display = 'none';
                }
                
                // Changer le titre du modal
                if (modalTitleText) {
                    modalTitleText.textContent = 'Modifier le produit';
                }
                
                // Ouvrir le modal
                if (typeof $ !== 'undefined' && $.fn.modal) {
                    $('#produitModal').modal('show');
                } else {
                    // Fallback sans jQuery
                    const modal = document.getElementById('produitModal');
                    if (modal) {
                        modal.classList.add('show');
                        modal.style.display = 'block';
                        modal.setAttribute('aria-modal', 'true');
                        modal.removeAttribute('aria-hidden');
                        document.body.classList.add('modal-open');
                        
                        const backdrop = document.createElement('div');
                        backdrop.className = 'modal-backdrop fade show';
                        backdrop.id = 'modal-backdrop';
                        document.body.appendChild(backdrop);
                    }
                }
                
                clearAllErrors();
            } else {
                showNotification('❌ Produit introuvable', false);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showNotification('❌ Erreur lors du chargement du produit', false);
        });
}

async function updateProduit(id, formData) {
    formData.id = id;
    
    const btnSave = document.getElementById('btnSave');
    const originalText = btnSave.innerHTML;
    btnSave.disabled = true;
    btnSave.innerHTML = '<span class="loading-spinner"></span> Mise à jour...';
    
    try {
        // Uploader l'image si un nouveau fichier est sélectionné
        const fileInput = document.getElementById('imageFile');
        if (fileInput && fileInput.files && fileInput.files.length > 0) {
            console.log('Upload nouvelle image...');
            const filename = await uploadImage();
            if (filename) {
                console.log('Nouvelle image uploadée:', filename);
                formData.image = filename;
            }
        } else {
            // Conserver l'image existante
            const currentImage = document.getElementById('image').value;
            formData.image = currentImage || '';
            console.log('Conservation image existante:', formData.image);
        }
        
        const response = await fetch(API_URL, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('✅ ' + data.message, true);
            loadProduits();
            closeModalFunc();
        } else {
            showNotification('❌ ' + (data.message || 'Erreur lors de la mise à jour'), false);
        }
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('❌ ' + error.message, false);
    } finally {
        btnSave.disabled = false;
        btnSave.innerHTML = originalText;
    }
}

function deleteProduit(id) {
    fetch(API_URL, {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('✅ ' + data.message, true);
            loadProduits();
        } else {
            showNotification('❌ ' + (data.message || 'Erreur lors de la suppression'), false);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showNotification('❌ Erreur de connexion au serveur', false);
    });
}

// Initialisation au chargement
document.addEventListener('DOMContentLoaded', function() {
    // Récupérer les éléments du modal
    modal = document.getElementById('produitModal');
    btnOpenModal = document.getElementById('btnOpenModal');
    btnCancel = document.getElementById('btnCancel');
    closeModal = document.getElementById('closeModal');
    form = document.getElementById('produitForm');
    modalTitleText = document.getElementById('modalTitleText');
    
    // Événements du modal
    if (btnOpenModal) {
        btnOpenModal.addEventListener('click', openModal);
    }
    
    if (btnCancel) {
        btnCancel.addEventListener('click', closeModalFunc);
    }
    
    if (closeModal) {
        closeModal.addEventListener('click', closeModalFunc);
    }
    
    // Charger les produits
    if (document.querySelector('table')) {
        loadProduits();
    }
    
    // Gestion du formulaire
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                nom: document.getElementById('nom').value,
                description: document.getElementById('description')?.value || '',
                prix: document.getElementById('prix').value,
                stock: document.getElementById('stock').value
                // image sera ajoutée par uploadImage() si un fichier est sélectionné
            };
            
            // Validation
            let isValid = true;
            clearAllErrors();
            
            const nomVal = validateNom(formData.nom);
            if (!nomVal.valid) {
                showError(document.getElementById('nom'), nomVal.message);
                isValid = false;
            }
            
            const prixVal = validatePrix(formData.prix);
            if (!prixVal.valid) {
                showError(document.getElementById('prix'), prixVal.message);
                isValid = false;
            }
            
            const stockVal = validateStock(formData.stock);
            if (!stockVal.valid) {
                showError(document.getElementById('stock'), stockVal.message);
                isValid = false;
            }
            
            if (!isValid) {
                showNotification('❌ Veuillez corriger les erreurs', false);
                return;
            }
            
            // Créer ou modifier
            if (isEditMode && currentEditId) {
                updateProduit(currentEditId, formData);
            } else {
                createProduit(formData);
            }
        });
        
        // Validation en temps réel
        const nomInput = document.getElementById('nom');
        if (nomInput) {
            nomInput.addEventListener('blur', function() {
                const val = validateNom(this.value);
                if (!val.valid) showError(this, val.message);
                else clearError(this);
            });
            
            nomInput.addEventListener('input', function() {
                if (this.classList.contains('invalid')) {
                    const val = validateNom(this.value);
                    if (val.valid) clearError(this);
                }
            });
        }
        
        const prixInput = document.getElementById('prix');
        if (prixInput) {
            prixInput.addEventListener('blur', function() {
                const val = validatePrix(this.value);
                if (!val.valid) showError(this, val.message);
                else clearError(this);
            });
            
            prixInput.addEventListener('input', function() {
                if (this.classList.contains('invalid')) {
                    const val = validatePrix(this.value);
                    if (val.valid) clearError(this);
                }
            });
        }
        
        const stockInput = document.getElementById('stock');
        if (stockInput) {
            stockInput.addEventListener('blur', function() {
                const val = validateStock(this.value);
                if (!val.valid) showError(this, val.message);
                else clearError(this);
            });
            
            stockInput.addEventListener('input', function() {
                if (this.classList.contains('invalid')) {
                    const val = validateStock(this.value);
                    if (val.valid) clearError(this);
                }
            });
        }
    }
    
    // Gestion de l'aperçu de l'image
    const imageFileInput = document.getElementById('imageFile');
    if (imageFileInput) {
        imageFileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Vérifier le type de fichier
                if (!file.type.startsWith('image/')) {
                    showNotification('❌ Veuillez sélectionner une image', false);
                    this.value = '';
                    return;
                }
                
                // Vérifier la taille (5MB max)
                if (file.size > 5 * 1024 * 1024) {
                    showNotification('❌ L\'image ne doit pas dépasser 5MB', false);
                    this.value = '';
                    return;
                }
                
                // Afficher l'aperçu
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imagePreview = document.getElementById('imagePreview');
                    const previewImg = document.getElementById('previewImg');
                    
                    if (imagePreview && previewImg) {
                        previewImg.src = e.target.result;
                        imagePreview.style.display = 'block';
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
