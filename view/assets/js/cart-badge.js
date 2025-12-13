// Cart badge management
const PANIER_BADGE_API_URL = '../../controller/PanierController.php';

function fetchCartCount() {
  fetch(`${PANIER_BADGE_API_URL}?_=${Date.now()}`)
    .then(r => r.json())
    .then(data => {
      if (data && typeof data.count !== 'undefined') {
        updateCartBadge(data.count);
      }
    })
    .catch(err => console.warn('Cart badge fetch error:', err));
}

function updateCartBadge(count) {
  const badge = document.querySelector('.cart-badge');
  if (!badge) return;
  if (!count || count < 1) {
    badge.classList.remove('show','bump');
    badge.textContent = '';
    return;
  }
  const display = count > 99 ? '99+' : String(count);
  const needsLong = display.length > 2; // covers 99+
  badge.classList.toggle('long', needsLong);
  if (badge.textContent !== display) {
    badge.textContent = display;
    badge.classList.add('show','bump');
    setTimeout(() => badge.classList.remove('bump'), 500);
  } else if (!badge.classList.contains('show')) {
    badge.classList.add('show');
  }
}

document.addEventListener('panier:updated', e => {
  updateCartBadge(e.detail.count);
});

// Optional storage sync if localStorage used elsewhere
window.addEventListener('storage', e => {
  if (e.key === 'panier') {
    fetchCartCount();
  }
});

document.addEventListener('DOMContentLoaded', () => {
  fetchCartCount();
});