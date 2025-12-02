// assets/js/event.js - SCRIPT SPÉCIFIQUE À LA PAGE EVENT

document.addEventListener('DOMContentLoaded', function() {
    // =============== INITIALISATIONS ===============
    initAnimations();
    createParticles();
    setupEventListeners();
    
    // =============== ANIMATIONS AU SCROLL ===============
    function initAnimations() {
        animateOnScroll();
        window.addEventListener('scroll', animateOnScroll);
    }
    
    function animateOnScroll() {
        const elements = document.querySelectorAll('.modern-event-card, .search-section, .section-header');
        
        elements.forEach(element => {
            const elementTop = element.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;
            
            if (elementTop < windowHeight - 100) {
                element.classList.add('visible');
            }
        });
    }
    
    // =============== PARTICULES FLOTTANTES ===============
    function createParticles() {
        const particlesContainer = document.querySelector('.floating-particles');
        if (!particlesContainer) return;
        
        particlesContainer.innerHTML = '';
        
        for (let i = 0; i < 12; i++) {
            const particle = document.createElement('div');
            particle.classList.add('particle');
            
            particle.style.left = Math.random() * 100 + '%';
            particle.style.top = Math.random() * 100 + '%';
            
            const size = Math.random() * 12 + 4;
            particle.style.width = size + 'px';
            particle.style.height = size + 'px';
            
            particle.style.animationDelay = Math.random() * 5 + 's';
            particle.style.animationDuration = (Math.random() * 8 + 12) + 's';
            particle.style.opacity = Math.random() * 0.4 + 0.1;
            
            const green = Math.floor(Math.random() * 100 + 100);
            particle.style.backgroundColor = `rgba(143, ${green}, 150, ${Math.random() * 0.3 + 0.2})`;
            
            particlesContainer.appendChild(particle);
        }
    }
    
    // =============== ÉCOUTEURS D'ÉVÉNEMENTS ===============
    function setupEventListeners() {
        // Effet de focus sur la recherche
        const searchInput = document.querySelector('.search-input-group .form-control');
        if (searchInput) {
            searchInput.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            searchInput.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        }
        
        // Hover sur les cartes
        const cards = document.querySelectorAll('.modern-event-card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-15px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
        
        // Fermer le modal avec ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModernPopup();
            }
        });
        
        // Ajouter l'effet neon aux boutons
        document.querySelectorAll('.event-cta, .search-btn, .modal-btn-primary').forEach(btn => {
            btn.classList.add('neon-effect');
        });
        
        // Animation du titre principal
        animateHeroTitle();
    }
    
    function animateHeroTitle() {
        const heroTitle = document.querySelector('.hero-title');
        if (heroTitle) {
            const text = heroTitle.textContent;
            heroTitle.innerHTML = '';
            
            text.split('').forEach((char, i) => {
                const span = document.createElement('span');
                span.textContent = char;
                span.style.animationDelay = `${i * 0.03}s`;
                span.style.display = 'inline-block';
                span.style.opacity = '0';
                span.style.transform = 'translateY(20px)';
                span.style.animation = 'fadeIn 0.5s ease forwards';
                heroTitle.appendChild(span);
            });
        }
    }
    
    // Animation des vagues du footer
    function animateFooterWaves() {
        const waves = document.querySelector('.footer-waves');
        if (waves) {
            let angle = 0;
            setInterval(() => {
                angle += 0.02;
                waves.style.transform = `translateX(${Math.sin(angle) * 10}px)`;
            }, 50);
        }
    }
    
    animateFooterWaves();
});

// =============== FONCTIONS GLOBALES ===============

// Fonction pour ouvrir le popup
function openModernPopup(titre, description, date, lieu, categorie, image, link) {
    const modal = document.getElementById("modernPopup");
    
    // Afficher le modal
    modal.style.display = "flex";
    document.body.style.overflow = "hidden";
    
    // Remplir le contenu
    document.getElementById("modern-popup-title").innerText = titre;
    document.getElementById("modern-popup-description").innerHTML = description;
    document.getElementById("modern-popup-date").innerText = "📅 Date : " + date;
    document.getElementById("modern-popup-lieu").innerText = "📍 Lieu : " + lieu;
    document.getElementById("modern-popup-categorie").innerText = "🏷️ Catégorie : " + categorie;
    document.getElementById("modern-popup-img").src = image;
    document.getElementById("modern-popup-link").href = link;
    
    // Animation d'entrée
    const modalElements = modal.querySelectorAll('.modal-title, .modal-detail, .modal-description, .modal-btn');
    modalElements.forEach((el, i) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            el.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }, i * 100 + 200);
    });
}

// Fonction pour fermer le popup
function closeModernPopup() {
    const modal = document.getElementById("modernPopup");
    
    if (!modal) return;
    
    // Animation de sortie
    modal.style.opacity = '0';
    modal.style.transform = 'scale(0.9)';
    
    setTimeout(() => {
        modal.style.display = "none";
        document.body.style.overflow = "auto";
        modal.style.opacity = '1';
        modal.style.transform = 'scale(1)';
    }, 300);
}


















