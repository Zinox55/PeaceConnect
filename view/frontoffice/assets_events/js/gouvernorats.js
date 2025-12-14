// Liste des 24 gouvernorats de Tunisie avec leurs coordonnÃ©es GPS
const GOUVERNORATS_TUNISIE = [
    { name: "Ariana", lat: 36.8565, lng: 10.1647 },
    { name: "BÃ©ja", lat: 36.7256, lng: 9.1844 },
    { name: "Ben Arous", lat: 36.7592, lng: 10.2372 },
    { name: "Bizerte", lat: 37.2744, lng: 9.8739 },
    { name: "GabÃ¨s", lat: 33.8869, lng: 10.0994 },
    { name: "Gafsa", lat: 34.4258, lng: 8.7852 },
    { name: "Jendouba", lat: 36.5024, lng: 8.7755 },
    { name: "Kairouan", lat: 35.6781, lng: 9.9197 },
    { name: "Kasserine", lat: 35.1656, lng: 8.8341 },
    { name: "KÃ©bili", lat: 33.7392, lng: 9.8007 },
    { name: "Kef", lat: 36.1761, lng: 8.7139 },
    { name: "Mahdia", lat: 35.5047, lng: 11.0625 },
    { name: "Manouba", lat: 36.8117, lng: 10.3903 },
    { name: "MÃ©denine", lat: 33.3540, lng: 10.5038 },
    { name: "Monastir", lat: 35.7789, lng: 10.8311 },
    { name: "Nabeul", lat: 36.4563, lng: 10.7367 },
    { name: "Sfax", lat: 34.7406, lng: 10.7605 },
    { name: "Sidi Bouzid", lat: 35.0347, lng: 9.4898 },
    { name: "Siliana", lat: 36.0208, lng: 9.3721 },
    { name: "Sousse", lat: 35.8256, lng: 10.6369 },
    { name: "Tataouine", lat: 32.9289, lng: 10.4547 },
    { name: "Tozeur", lat: 33.9197, lng: 8.1353 },
    { name: "Tunis", lat: 36.8065, lng: 10.1957 },
    { name: "Zaghouan", lat: 36.4025, lng: 10.1437 }
];

/**
 * Initialiser l'autocomplete pour un champ de gouvernorat
 * @param {string} inputId - ID du champ input
 * @param {string} suggestionsId - ID du conteneur des suggestions (optionnel)
 */
function initGouvernoratAutocomplete(inputId, suggestionsId = null) {
    const input = document.getElementById(inputId);
    if (!input) return;

    // CrÃ©er le conteneur de suggestions s'il n'existe pas
    let suggestionsContainer = suggestionsId ? document.getElementById(suggestionsId) : null;
    
    if (!suggestionsContainer) {
        suggestionsContainer = document.createElement('ul');
        suggestionsContainer.id = suggestionsId || `${inputId}_suggestions`;
        suggestionsContainer.className = 'gouvernorat-suggestions';
        input.parentNode.insertBefore(suggestionsContainer, input.nextSibling);
    }

    input.addEventListener('input', function() {
        const value = this.value.trim().toLowerCase();
        suggestionsContainer.innerHTML = '';

        if (value.length === 0) {
            suggestionsContainer.style.display = 'none';
            return;
        }

        // Filtrer les gouvernorats
        const filtered = GOUVERNORATS_TUNISIE.filter(gov => 
            gov.name.toLowerCase().startsWith(value)
        );

        if (filtered.length === 0) {
            suggestionsContainer.innerHTML = '<li class="no-match">Aucun gouvernorat trouvÃ©</li>';
            suggestionsContainer.style.display = 'block';
            return;
        }

        // Afficher les suggestions
        filtered.forEach(gov => {
            const li = document.createElement('li');
            li.className = 'gouvernorat-option';
            li.textContent = gov.name;
            li.dataset.value = gov.name;
            li.addEventListener('click', function() {
                input.value = gov.name;
                suggestionsContainer.innerHTML = '';
                suggestionsContainer.style.display = 'none';
                
                // DÃ©clencher un Ã©vÃ©nement custom pour la validation
                input.dispatchEvent(new Event('gouvernorat-selected'));
            });
            suggestionsContainer.appendChild(li);
        });

        suggestionsContainer.style.display = 'block';
    });

    // Fermer les suggestions quand on clique ailleurs
    document.addEventListener('click', function(event) {
        if (event.target !== input) {
            suggestionsContainer.style.display = 'none';
        }
    });

    // Valider le gouvernorat au blur
    input.addEventListener('blur', function() {
        const value = this.value.trim();
        const isValid = GOUVERNORATS_TUNISIE.some(gov => 
            gov.name.toLowerCase() === value.toLowerCase()
        );

        if (value && !isValid) {
            this.classList.add('is-invalid');
            this.classList.remove('is-valid');
        } else if (isValid) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        }
    });
}

/**
 * Obtenir les coordonnÃ©es GPS d'un gouvernorat
 * @param {string} gouvernoratName - Nom du gouvernorat
 * @returns {object|null} Objet {lat, lng} ou null si non trouvÃ©
 */
function getGouvernoratCoordinates(gouvernoratName) {
    const gov = GOUVERNORATS_TUNISIE.find(g => 
        g.name.toLowerCase() === gouvernoratName.toLowerCase()
    );
    return gov ? { lat: gov.lat, lng: gov.lng } : null;
}

/**
 * Valider si un gouvernorat existe
 * @param {string} gouvernoratName - Nom Ã  vÃ©rifier
 * @returns {boolean}
 */
function isValidGouvernorat(gouvernoratName) {
    return GOUVERNORATS_TUNISIE.some(g => 
        g.name.toLowerCase() === gouvernoratName.toLowerCase()
    );
}

/**
 * Obtenir la liste de tous les gouvernorats (noms seulement)
 * @returns {array}
 */
function getAllGouvernoratNames() {
    return GOUVERNORATS_TUNISIE.map(g => g.name);
}
