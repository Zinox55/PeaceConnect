// validation.js - CODE PROPRE ET SÉPARÉ

document.addEventListener('DOMContentLoaded', function() {
    console.log("✅ validation.js chargé avec succès");
    
    const form = document.getElementById('inscriptionForm');
    if (!form) {
        console.error("❌ Formulaire non trouvé");
        return;
    }
    
    // Gestionnaire de soumission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log("📝 Formulaire soumis - validation.js");
        
        handleFormSubmission(this);
    });
});

function handleFormSubmission(form) {
    // Cacher les anciennes erreurs
    const oldError = document.getElementById('errorContainer');
    if (oldError) oldError.remove();
    
    // Désactiver le bouton
    const btn = form.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;
    btn.innerHTML = 'Envoi...';
    btn.disabled = true;
    
    // Envoyer au serveur
    const formData = new FormData(form);
    
    fetch("../../controller/inscriptioncontroller.php?action=process", {
        method: "POST",
        body: formData
    })
    .then(r => r.text())
    .then(response => {
        console.log("📨 Réponse serveur:", response);
        
        if (response.includes('success')) {
            showSuccess(form);
        } else {
            showError(response, form, btn, originalText);
        }
    })
    .catch(err => {
        console.error("🔥 Erreur fetch:", err);
        showNetworkError(form, btn, originalText);
    });
}

function showSuccess(form) {
    document.getElementById("successMessage").style.display = "block";
    form.style.display = "none";
    
    setTimeout(() => {
        window.location.href = "events.php";
    }, 1000);
}

function showError(response, form, btn, originalText) {
    const errorMsg = response.includes('error:') ? response.replace('error:', '').trim() : response;
    
    const errorContainer = document.createElement('div');
    errorContainer.id = 'errorContainer';
    errorContainer.className = 'alert alert-danger';
    errorContainer.innerHTML = `
        <h5 class="alert-heading">❌ Erreur</h5>
        <p class="mb-0">${errorMsg}</p>
    `;
    
    form.parentNode.insertBefore(errorContainer, form);
    btn.innerHTML = originalText;
    btn.disabled = false;
}

function showNetworkError(form, btn, originalText) {
    const errorContainer = document.createElement('div');
    errorContainer.id = 'errorContainer';
    errorContainer.className = 'alert alert-danger';
    errorContainer.innerHTML = `
        <h5 class="alert-heading">❌ Erreur réseau</h5>
        <p class="mb-0">Impossible de se connecter au serveur</p>
    `;
    
    form.parentNode.insertBefore(errorContainer, form);
    btn.innerHTML = originalText;
    btn.disabled = false;
}