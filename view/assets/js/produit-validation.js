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
                <button class="btn-edit" data-id="${produit.id}" style="background: var(--primary-blue); color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; margin-right: 5px;">Modifier</button>
                <button class="btn-delete" data-id="${produit.id}" style="background: var(--danger-red); color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">Supprimer</button>
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

function createProduit(formData) {
    fetch(API_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message);
            loadProduits();
            document.querySelector('.form-admin').reset();
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
                document.querySelector('[name="nom"]').value = produit.nom;
                document.querySelector('[name="description"]').value = produit.description || '';
                document.querySelector('[name="prix"]').value = produit.prix;
                document.querySelector('[name="stock"]').value = produit.stock;
                document.querySelector('[name="image"]').value = produit.image || '';
                
                const submitBtn = document.querySelector('.form-admin button[type="submit"]');
                submitBtn.textContent = 'Mettre à jour';
                submitBtn.setAttribute('data-id', id);
                submitBtn.setAttribute('data-mode', 'update');
            }
        })
        .catch(error => console.error('Erreur:', error));
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
            showNotification(data.message);
            loadProduits();
            document.querySelector('.form-admin').reset();
            const submitBtn = document.querySelector('.form-admin button[type="submit"]');
            submitBtn.textContent = 'Ajouter';
            submitBtn.removeAttribute('data-id');
            submitBtn.removeAttribute('data-mode');
        } else {
            showNotification(data.message, false);
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
