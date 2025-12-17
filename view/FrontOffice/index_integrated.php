<?php
session_start();
if (!isset($_SESSION['e'])) {
    header('Location: signin.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PeaceConnect - Accueil</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="hero-navbar.css" />
  <link rel="stylesheet" href="../BackOffice/assets/css/style-front.css" />
</head>
<body>
  <!-- Navbar (store landing defaults) -->
  <nav class="site-nav store-nav" id="siteNav">
    <div class="container">
      <div class="menu-wrap">
        <a href="index.php" class="logo">PeaceConnect</a>
        <ul class="site-menu" id="mainMenu">
          <li><a href="index.php">Home</a></li>
          <li class="active"><a href="index_integrated.php">Store</a></li>
          <li><a href="produits.html">Products</a></li>
          <li><a href="panier.html" class="cart-link"><span class="cart-icon-wrapper"><i class="fas fa-shopping-cart"></i><span class="cart-badge" aria-label="Articles dans le panier" role="status"></span></span> Cart</a></li>
          <li><a href="suivi.html">Orders</a></li>
          <li><a href="userinfo.php">Profile</a></li>
        </ul>
        <a href="tel:+21671523640" class="call-us"><span class="icon-phone"></span>+216 71 523 640</a>
        <div class="burger" id="burger"><span></span></div>
      </div>
    </div>
  </nav>
  <div class="mobile-menu" id="mobileMenu">
    <div class="close-btn" id="closeMobile">&times;</div>
    <ul>
      <li><a href="index.php">Home</a></li>
      <li class="active"><a href="index_integrated.php">Store</a></li>
      <li><a href="produits.html">Products</a></li>
      <li><a href="panier.html">Cart</a></li>
      <li><a href="suivi.html">Orders</a></li>
      <li><a href="userinfo.php">Profile</a></li>
    </ul>
  </div>

  <!-- Hero section comme la home -->
  <section class="hero overlay" style="background-image:url('https://images.unsplash.com/photo-1532629345422-7515f3d16bb6?w=1600&auto=format&fit=crop');">
    <div class="hero-inner">
      <div class="container">
        <div class="row align-items-center justify-content-between">
          <div class="col-lg-6 text-left">
            <span class="subheading-white text-white mb-3" data-aos="fade-up">PeaceConnect</span>
            <h1 class="heading text-white mb-2" data-aos="fade-up">Give a helping hand to those who need it!</h1>
            <p data-aos="fade-up" class="mb-5 text-white lead text-white-50">Join us to support communities with education, health, and rapid relief. Together we turn compassion into real impact.</p>
            <p data-aos="fade-up"  data-aos-delay="100">
              <a href="/PeaceConnect/view/FrontOffice/indexRanim.php" class="btn btn-primary me-4 d-inline-flex align-items-center"> <span class="icon-attach_money me-2"></span><span>Donate Now</span></a> 
              <a href="https://www.youtube.com/watch?v=mwtbEGNABWU" class="text-white glightbox d-inline-flex align-items-center"><span class="icon-play me-2"></span><span>Watch the video</span></a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Bloc informations en fond blanc -->
  <div style="background: #fff; padding: 40px 0;">
    <div class="container">
      <section style="margin-bottom:40px;">
        <h2 style="font-size:2rem;font-weight:700;color:#214E41;margin-bottom:1rem;">Welcome to Our Store!</h2>
        <p style="max-width:720px;">Browse our exclusive solidarity products and take advantage of our current offers. Every purchase helps support our community initiatives.</p>
      </section>
      <section style="margin-bottom:40px;">
        <h2 style="font-size:1.5rem;font-weight:600;color:#214E41;margin-bottom:1rem;">How to Order</h2>
        <p style="max-width:720px;">Select your favorite products and add them to your cart. When you're ready, proceed to checkout to complete your order securely.</p>
      </section>
      <section style="margin-bottom:40px;">
        <h2 style="font-size:1.5rem;font-weight:600;color:#214E41;margin-bottom:1rem;">Track Your Order</h2>
        <p style="max-width:720px;">Want to know the status of your order? Use our order tracking page to follow every step of your delivery in real time.</p>
        <p><a href="suivi.html" class="btn-hero" style="background:#214E41;">Track My Order</a></p>
      </section>
      <section style="margin-bottom:40px;">
        <h2 style="font-size:1.5rem;font-weight:600;color:#214E41;margin-bottom:1rem;">Need Help?</h2>
        <p style="max-width:720px;">If you have any questions or need assistance, feel free to contact our support team. We're here to help you!</p>
      </section>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function(){
      var burger = document.getElementById('burger');
      var mobileMenu = document.getElementById('mobileMenu');
      var closeMobile = document.getElementById('closeMobile');
      var siteNav = document.getElementById('siteNav');
      var hero = document.querySelector('.hero');

      function openMobile(){ mobileMenu.classList.add('open'); }
      function closeMobileMenu(){ mobileMenu.classList.remove('open'); }
      function handleScroll(){
        var trigger = hero ? hero.offsetHeight - 80 : 80;
        if(window.scrollY > trigger){ siteNav.classList.add('scrolled'); }
        else { siteNav.classList.remove('scrolled'); }
      }

      burger.addEventListener('click', openMobile);
      closeMobile.addEventListener('click', closeMobileMenu);
      Array.prototype.forEach.call(mobileMenu.querySelectorAll('a'), function(a){ a.addEventListener('click', closeMobileMenu); });

      handleScroll();
      window.addEventListener('scroll', handleScroll);
    });
  </script>
  <script src="../BackOffice/assets/js/cart-badge.js" defer></script>
</body>
</html>
