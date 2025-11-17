/**
 * Validation et gestion des produits - BackOffice
 * Validation sans HTML5
 */

const API_URL = '../../controller/ProduitController.php';

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
    const notif = document.createElement('div');
    notif.style.cssText = `
        position: fixed; top: 20px; right: 20px;
        background: ${isSuccess ? '#2ecc71' : '#e74c3c'};
        color: white; padding: 15px 20px; border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2); z-index: 9999;
    `;
    notif.textContent = message;
    document.body.appendChild(notif);
    setTimeout(() => notif.remove(), 3000);
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
        const stockClass = produit.stock < 10 ? 'class="stock-low"' : '';
        
        row.innerHTML = `
            <td>${produit.id}</td>
            <td>${produit.nom}</td>
            <td>${parseFloat(produit.prix).toFixed(2)} €</td>
            <td ${stockClass}>${produit.stock}</td>
            <td>
                <button class="btn-edit" data-id="${produit.id}" title="Modifier ce produit" style="background: #3498db; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; margin-right: 5px; transition: all 0.3s;">
                    <i class="fas fa-edit"></i> Modifier
                </button>
                <button class="btn-delete" data-id="${produit.id}" title="Supprimer ce produit" style="background: #e74c3c; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; transition: all 0.3s;">
                    <i class="fas fa-trash"></i> Supprimer
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
    
    // Ajouter les effets hover
    const style = document.createElement('style');
    style.textContent = `
        .btn-edit:hover { background: #2980b9 !important; transform: translateY(-2px); }
        .btn-delete:hover { background: #c0392b !important; transform: translateY(-2px); }
        tr.editing { background: #e8f4f8 !important; border-left: 4px solid #3498db; }
    `;
    if (!document.getElementById('btn-styles')) {
        style.id = 'btn-styles';
        document.head.appendChild(style);
    }
    
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

function createProduit(formData) {
    fetch(API_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('✓ Produit ajouté avec succès !');
            loadProduits();
            resetForm();
        } else {
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
    .catch(error => console.error('Erreur:', error));
}

function editProduit(id) {
    fetch(`${API_URL}?action=readOne&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const produit = data.data;
                
                // Remplir le formulaire
                document.querySelector('[name="nom"]').value = produit.nom;
                document.querySelector('[name="description"]').value = produit.description || '';
                document.querySelector('[name="prix"]').value = produit.prix;
                document.querySelector('[name="stock"]').value = produit.stock;
                document.querySelector('[name="image"]').value = produit.image || '';
                
                // Surligner la ligne en cours de modification
                document.querySelectorAll('tbody tr').forEach(tr => tr.classList.remove('editing'));
                const currentRow = document.querySelector(`[data-id="${id}"]`)?.closest('tr');
                if (currentRow) currentRow.classList.add('editing');
                
                // Changer le titre et le bouton
                const formCard = document.querySelector('.chart-card-title');
                if (formCard) {
                    formCard.innerHTML = '<i class="fas fa-edit"></i> Modifier le produit - ID: ' + id;
                    formCard.style.color = '#3498db';
                }
                
                const submitBtn = document.querySelector('.form-admin button[type="submit"]');
                submitBtn.innerHTML = '<i class="fas fa-check"></i> Mettre à jour';
                submitBtn.style.background = '#3498db';
                submitBtn.setAttribute('data-id', id);
                submitBtn.setAttribute('data-mode', 'update');
                
                // Ajouter un bouton d'annulation
                let cancelBtn = document.querySelector('.btn-cancel-edit');
                if (!cancelBtn) {
                    cancelBtn = document.createElement('button');
                    cancelBtn.type = 'button';
                    cancelBtn.className = 'btn-cancel-edit';
                    cancelBtn.innerHTML = '<i class="fas fa-times"></i> Annuler';
                    cancelBtn.style.cssText = 'background: #95a5a6; color: white; border: none; padding: 12px 24px; border-radius: 4px; cursor: pointer; font-weight: 600; margin-left: 10px; transition: all 0.3s;';
                    submitBtn.parentElement.appendChild(cancelBtn);
                    
                    cancelBtn.addEventListener('click', function() {
                        resetForm();
                    });
                    
                    cancelBtn.addEventListener('mouseenter', function() {
                        this.style.background = '#7f8c8d';
                    });
                    cancelBtn.addEventListener('mouseleave', function() {
                        this.style.background = '#95a5a6';
                    });
                }
                
                // Scroll vers le formulaire
                formCard.parentElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                
                showNotification('📝 Mode modification activé', true);
            }
        })
        .catch(error => console.error('Erreur:', error));
}

function resetForm() {
    const form = document.querySelector('.form-admin');
    if (form) form.reset();
    
    // Retirer le surlignage
    document.querySelectorAll('tbody tr').forEach(tr => tr.classList.remove('editing'));
    
    const formCard = document.querySelector('.chart-card-title');
    if (formCard) {
        formCard.innerHTML = '<i class="fas fa-plus-circle"></i> Ajouter un produit';
        formCard.style.color = '';
    }
    
    const submitBtn = document.querySelector('.form-admin button[type="submit"]');
    if (submitBtn) {
        submitBtn.innerHTML = '<i class="fas fa-plus"></i> Ajouter';
        submitBtn.style.background = '#5F9E7F';
        submitBtn.removeAttribute('data-id');
        submitBtn.removeAttribute('data-mode');
    }
    
    const cancelBtn = document.querySelector('.btn-cancel-edit');
    if (cancelBtn) cancelBtn.remove();
    
    document.querySelectorAll('input, textarea').forEach(input => clearError(input));
}

function updateProduit(id, formData) {
    formData.id = id;
    
    fetch(API_URL, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('✓ Produit modifié avec succès !');
            loadProduits();
            resetForm();
        } else {
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
    .catch(error => console.error('Erreur:', error));
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
            showNotification(data.message);
            loadProduits();
        } else {
            showNotification(data.message, false);
        }
    })
    .catch(error => console.error('Erreur:', error));
}

document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('table')) {
        loadProduits();
    }
    
    const form = document.querySelector('.form-admin');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                nom: document.querySelector('[name="nom"]').value,
                description: document.querySelector('[name="description"]')?.value || '',
                prix: document.querySelector('[name="prix"]').value,
                stock: document.querySelector('[name="stock"]').value,
                image: document.querySelector('[name="image"]')?.value || ''
            };
            
            // Validation
            let isValid = true;
            document.querySelectorAll('input').forEach(input => clearError(input));
            
            const nomVal = validateNom(formData.nom);
            if (!nomVal.valid) {
                showError(document.querySelector('[name="nom"]'), nomVal.message);
                isValid = false;
            }
            
            const prixVal = validatePrix(formData.prix);
            if (!prixVal.valid) {
                showError(document.querySelector('[name="prix"]'), prixVal.message);
                isValid = false;
            }
            
            const stockVal = validateStock(formData.stock);
            if (!stockVal.valid) {
                showError(document.querySelector('[name="stock"]'), stockVal.message);
                isValid = false;
            }
            
            if (!isValid) return;
            
            const submitBtn = form.querySelector('button[type="submit"]');
            const mode = submitBtn.getAttribute('data-mode');
            const id = submitBtn.getAttribute('data-id');
            
            if (mode === 'update' && id) {
                updateProduit(id, formData);
            } else {
                createProduit(formData);
            }
        });
        
        // Validation en temps réel
        document.querySelector('[name="nom"]')?.addEventListener('blur', function() {
            const val = validateNom(this.value);
            if (!val.valid) showError(this, val.message);
            else clearError(this);
        });
        
        document.querySelector('[name="prix"]')?.addEventListener('blur', function() {
            const val = validatePrix(this.value);
            if (!val.valid) showError(this, val.message);
            else clearError(this);
        });
        
        document.querySelector('[name="stock"]')?.addEventListener('blur', function() {
            const val = validateStock(this.value);
            if (!val.valid) showError(this, val.message);
            else clearError(this);
        });
    }
});
