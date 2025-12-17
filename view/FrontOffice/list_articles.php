<?php
// View: Liste des articles Frontend
// Affichage de tous les articles approuvés
session_start();
if (!isset($_SESSION['e'])) {
	header('Location: signin.php?redirect=' . urlencode('list_articles.php'));
	exit();
}

include_once __DIR__ . '/../../controller/ArticleController.php';

$articleController = new ArticleController();
$articles = $articleController->indexFront();
$topPosts = $articleController->getTopPosts(3);
?>
<!-- /*
* Template Name: Volunteer
* Template Author: Untree.co
* Tempalte URI: https://untree.co/
* License: https://creativecommons.org/licenses/by/3.0/
*/ -->
<!doctype html>
<html lang="en">
<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="author" content="Untree.co">
	<link rel="shortcut icon" href="favicon.png">

	<meta name="description" content="" />
	<meta name="keywords" content="" />
	
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Roboto&family=Work+Sans:wght@400;700&display=swap" rel="stylesheet">


	<link rel="stylesheet" href="fonts/icomoon/style.css">
	<link rel="stylesheet" href="fonts/flaticon/font/flaticon.css">

	<link rel="stylesheet" href="css/tiny-slider.css">
	<link rel="stylesheet" href="css/aos.css">
	<link rel="stylesheet" href="css/flatpickr.min.css">
	<link rel="stylesheet" href="css/glightbox.min.css">
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
	<link rel="stylesheet" href="hero-navbar.css">

	<style>
		.causes-item {
			transition: all 0.3s ease;
			border-radius: 15px;
			overflow: hidden;
			box-shadow: 0 5px 15px rgba(0,0,0,0.08);
			height: 100%;
			display: flex;
			flex-direction: column;
		}
		.causes-item:hover {
			transform: translateY(-10px);
			box-shadow: 0 15px 35px rgba(0,0,0,0.15);
		}
		.causes-item img {
			transition: transform 0.4s ease;
			border-radius: 0;
		}
		.causes-item:hover img {
			transform: scale(1.05);
		}
		.article-badge {
			position: absolute;
			top: 15px;
			right: 15px;
			background: linear-gradient(135deg, #59886b 0%, #476d56 100%);
			color: white;
			padding: 5px 15px;
			border-radius: 20px;
			font-size: 12px;
			font-weight: 600;
			text-transform: uppercase;
			letter-spacing: 1px;
		}
		.article-image-wrapper {
			position: relative;
			overflow: hidden;
		}
		.article-title {
			font-size: 1.3rem;
			font-weight: 700;
			line-height: 1.4;
			margin-bottom: 10px;
			color: #2c3e50;
			transition: color 0.3s;
		}
		.article-title:hover {
			color: #667eea;
		}
		.article-excerpt {
			color: #6c757d;
			line-height: 1.6;
			flex-grow: 1;
		}
		.article-meta {
			display: flex;
			align-items: center;
			justify-content: space-between;
			padding: 10px 0;
			border-bottom: 1px solid #e9ecef;
			margin-bottom: 15px;
		}
		.author-info {
			display: flex;
			align-items: center;
			gap: 8px;
		}
		.author-avatar {
			width: 30px;
			height: 30px;
			border-radius: 50%;
			background: linear-gradient(135deg, #ffc85c 0%, #ffb03a 100%);
			color: #000;
			display: flex;
			align-items: center;
			justify-content: center;
			font-weight: 600;
			font-size: 14px;
		}
		.stats-group {
			display: flex;
			gap: 15px;
			align-items: center;
		}
		.stat-item {
			display: flex;
			align-items: center;
			gap: 5px;
			font-size: 14px;
		}
		.read-more-btn {
			background: linear-gradient(135deg, #59886b 0%, #476d56 100%);
			color: white;
			padding: 10px 25px;
			border-radius: 25px;
			text-decoration: none;
			display: inline-flex;
			align-items: center;
			gap: 8px;
			font-weight: 600;
			transition: all 0.3s ease;
			border: none;
		}
		.read-more-btn:hover {
			box-shadow: 0 5px 15px rgba(89, 136, 107, 0.4);
			transform: translateX(5px);
			color: white;
		}
		.article-content-wrapper {
			display: flex;
			flex-direction: column;
			height: 100%;
		}
		/* Newsletter Banner Sticky */
		.newsletter-banner {
			position: fixed;
			bottom: 0;
			left: 0;
			right: 0;
			background: linear-gradient(135deg, #59886b 0%, #476d56 100%);
			color: white;
			padding: 15px 0;
			box-shadow: 0 -5px 20px rgba(0,0,0,0.2);
			z-index: 999;
			transform: translateY(100%);
			transition: transform 0.3s ease;
		}
		.newsletter-banner.show {
			transform: translateY(0);
		}
		.newsletter-banner-content {
			display: flex;
			align-items: center;
			justify-content: space-between;
			gap: 20px;
			flex-wrap: wrap;
		}
		.newsletter-banner .close-banner {
			background: rgba(255,255,255,0.2);
			border: none;
			color: white;
			padding: 5px 10px;
			border-radius: 5px;
			cursor: pointer;
		}
		.newsletter-quick-form {
			display: flex;
			gap: 10px;
			flex: 1;
			max-width: 600px;
		}
		.newsletter-quick-form input {
			border: none;
			padding: 10px 15px;
			border-radius: 20px;
			flex: 1;
		}
		.newsletter-quick-form button {
			background: white;
			color: #667eea;
			border: none;
			padding: 10px 25px;
			border-radius: 20px;
			font-weight: 600;
			cursor: pointer;
		}
	</style>

	<title>PeaceConnect &mdash; Our Articles</title>
</head>
<body>

	<!-- Navbar (articles page) -->
	<nav class="site-nav" id="siteNav">
		<div class="container">
			<div class="menu-wrap">
				<a href="index.php" class="logo">PeaceConnect</a>
				   <ul class="site-menu" id="mainMenu">
					   <li><a href="index.php">Home</a></li>
					   <li class="active"><a href="list_articles.php">Articles</a></li>
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
			   <li class="active"><a href="list_articles.php">Articles</a></li>
			   <li><a href="userinfo.php">Profile</a></li>
		</ul>
		<p style="margin-top:30px; font-size:14px; color:#555;">Appelez-nous : <strong>+216 71 523 640</strong></p>
	</div>

	<div class="site-mobile-menu site-navbar-target">
		<div class="site-mobile-menu-header">
			<div class="site-mobile-menu-close">
				<span class="icofont-close js-menu-toggle"></span>
			</div>
		</div>
		<div class="site-mobile-menu-body"></div>
	</div>

	<!-- Old nav removed, using new unified navbar above -->

	<div class="hero overlay" style="background-image: url('images/img_v_6-min.jpg')">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-lg-8 text-center mx-auto">
					<span class="subheading-white text-white mb-3" data-aos="fade-up">Articles</span>

					<h1 class="heading text-white mb-2" data-aos="fade-up">Nos Articles</h1>
					<p data-aos="fade-up" class="mb-4 text-white lead text-white-50">Découvrez nos articles sur la paix, la cohésion sociale et l'inclusion.</p>
					
					<!-- Newsletter CTA dans le Hero -->
					<div class="mt-4" data-aos="fade-up" data-aos-delay="200">
						<a href="#newsletter-section" class="btn btn-light btn-lg" style="border-radius: 30px; padding: 15px 40px; font-weight: 600; box-shadow: 0 5px 20px rgba(0,0,0,0.2);">
							📧 S'abonner à la Newsletter
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="section bg-light">
		<div class="container">
			<div class="row mb-5" data-aos="fade-up">
				<div class="col-lg-12 text-center">
					<h2 class="heading mb-3">Nos Derniers Articles</h2>
					<p class="text-muted">Découvrez nos articles sur la paix, la cohésion sociale et l'inclusion</p>
				</div>
			</div>
			<div class="row g-4">
                <?php while ($row = $articles->fetch(PDO::FETCH_ASSOC)): ?>
                        <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                            <div class="causes-item bg-white">
                                <div class="article-image-wrapper">
                                    <a href="article_detail.php?id=<?php echo $row['id']; ?>">
										<?php if($row['image']): ?>
											<?php $imgUrl = '/PeaceConnect/model/uploads/' . rawurlencode($row['image']); ?>
											<img src="<?php echo htmlspecialchars($imgUrl); ?>" alt="<?php echo htmlspecialchars($row['titre']); ?>" class="img-fluid" style="width: 100%; height: 250px; object-fit: cover;" onerror="this.onerror=null;this.src='images/img_v_1-min.jpg'">
										<?php else: ?>
											<img src="images/img_v_1-min.jpg" alt="Image" class="img-fluid" style="width: 100%; height: 250px; object-fit: cover;">
										<?php endif; ?>
                                    </a>
                                    <span class="article-badge">Article</span>
                                </div>
                                <div class="px-4 pb-4 pt-3 article-content-wrapper">
                                    <div class="article-meta">
                                        <div class="author-info">
                                            <div class="author-avatar">
                                                <?php echo strtoupper(substr($row['auteur'], 0, 1)); ?>
                                            </div>
                                            <small class="text-muted fw-bold"><?php echo htmlspecialchars($row['auteur']); ?></small>
                                        </div>
                                        <small class="text-muted">
                                            <i class="icon-calendar"></i> <?php echo date('d M Y', strtotime($row['date_creation'])); ?>
                                        </small>
                                    </div>
                                    
                                    <h3 class="article-title">
                                        <a href="article_detail.php?id=<?php echo $row['id']; ?>" class="text-dark text-decoration-none">
                                            <?php echo htmlspecialchars($row['titre']); ?>
                                        </a>
                                    </h3>
                                    
                                    <p class="article-excerpt mb-3"><?php 
                                        $contenu = $row['contenu'];
                                        echo htmlspecialchars((strlen($contenu) > 120) ? substr($contenu, 0, 120) . '...' : $contenu); 
                                    ?></p>
                                    
                                    <div class="d-flex justify-content-between align-items-center mt-auto">
                                        <div class="stats-group">
                                            <span class="stat-item">
                                                <i class="icon-heart text-danger"></i>
                                                <strong><?php echo $row['like_count']; ?></strong>
                                            </span>
                                            <span class="stat-item">
                                                <i class="icon-comment text-primary"></i>
                                                <strong><?php echo $row['comment_count']; ?></strong>
                                            </span>
                                        </div>
                                        <a href="article_detail.php?id=<?php echo $row['id']; ?>" class="read-more-btn">
                                            Lire plus
                                            <i class="icon-chevron-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                <?php endwhile; ?>
			</div>	

            <!-- Pagination (Static for now, can be made dynamic later) -->
			<div class="row align-items-center py-5">
				<div class="col-lg-3">
					<!-- Pagination (1 of 10) -->
				</div>
				<div class="col-lg-6 text-center">
					<div class="custom-pagination">
						<a href="#" class="active">1</a>
						<a href="#" onclick="return false;">2</a>
						<a href="#" onclick="return false;">3</a>
						<a href="#" onclick="return false;">4</a>
						<a href="#" onclick="return false;">5</a>
					</div>
				</div>
			</div>
		</div>		
	</div>

	<!-- Newsletter Section -->
	<div class="section bg-white" id="newsletter-section">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 mx-auto text-center" data-aos="fade-up">
					<div style="font-size: 3rem; margin-bottom: 20px;">📬</div>
					<span class="subheading mb-3 text-primary" style="font-size: 1.1rem; font-weight: 600;">Newsletter</span>
					<h2 class="heading mb-3" style="font-size: 2.5rem;">Restez Informé des Nouveaux Articles</h2>
					<p class="mb-5 text-muted" style="font-size: 1.1rem;">Abonnez-vous à notre newsletter et recevez une notification par email à chaque nouvel article publié sur PeaceConnect.</p>
					
					<?php
					// Afficher les messages
					if (isset($_GET['newsletter'])) {
						$msg = $_GET['newsletter'];
						if ($msg == 'success') {
							echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
								<strong>✅ Merci !</strong> Votre inscription a été confirmée. Vous recevrez les notifications des nouveaux articles.
								<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
							</div>';
						} elseif ($msg == 'duplicate') {
							echo '<div class="alert alert-info alert-dismissible fade show" role="alert">
								<strong>ℹ️ Déjà inscrit !</strong> Cette adresse email est déjà abonnée à notre newsletter.
								<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
							</div>';
						} elseif ($msg == 'invalid') {
							echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
								<strong>❌ Erreur !</strong> Veuillez entrer une adresse email valide.
								<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
							</div>';
						} elseif ($msg == 'unsubscribed') {
							echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
								<strong>👋 Au revoir !</strong> Vous avez été désabonné de notre newsletter.
								<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
							</div>';
						}
					}
					?>
					
					<form action="../../controller/route_newsletter.php" method="POST" class="row g-3 justify-content-center" data-aos="fade-up" data-aos-delay="100">
						<input type="hidden" name="action" value="subscribe">
						<div class="col-md-4">
							<input type="text" name="nom" class="form-control" placeholder="Votre nom" style="border-radius: 25px; padding: 15px 25px; border: 2px solid #e0e0e0;">
						</div>
						<div class="col-md-5">
							<input type="email" name="email" class="form-control" placeholder="Votre email *" required style="border-radius: 25px; padding: 15px 25px; border: 2px solid #e0e0e0;">
						</div>
						<div class="col-md-3">
							<button type="submit" class="btn btn-primary w-100" style="background: linear-gradient(135deg, #59886b 0%, #476d56 100%); border: none; border-radius: 25px; padding: 15px 25px; font-weight: 600;">
								S'abonner 📧
							</button>
						</div>
					</form>
					
					<p class="text-muted mt-3" style="font-size: 0.9em;">
						<small>🔒 Vos données sont sécurisées. Vous pouvez vous désabonner à tout moment.</small>
					</p>
				</div>
			</div>
		</div>
	</div>

	<div class="section sec-instagram pb-0" style="background: #fff;">
		<div class="container mb-5" style="background: #fff; border-radius: 16px; box-shadow: 0 2px 16px rgba(0,0,0,0.08);">
			<div class="row align-items-center">
				<div class="col-lg-3" data-aos="fade-up">
					<span class="subheading mb-3">Instagram</span>
					<h2 class="heading">We Are In Instagram</h2>
				</div>
				<div class="col-lg-7" data-aos="fade-up" data-aos-delay="100">
					<p>We share human stories, impactful initiatives, and meaningful reflections on peace and community life.</p>
				</div>
			</div>
		</div>

		<div class="instagram-slider-wrap" data-aos="fade-up" data-aos-delay="200">
			<div class="instagram-slider" id="instagram-slider">
				<div class="item">
					<a class="instagram-item">
						<span class="icon-instagram"></span>
						<img src="images/img_v_8-min.jpg" alt="Image" class="img-fluid">		
					</a>
				</div>
                <!-- More items... -->
			</div>
		</div>
	</div>

	<div class="site-footer">
		<div class="container">
			<div class="row">
				<div class="col-6 col-sm-6 col-md-6 col-lg-3">
					<div class="widget">
						<h3>Navigation</h3>
						<ul class="list-unstyled float-left links">
							<li><a href="index.php">Home</a></li>
							<li><a href="events.php">Events</a></li>
							<li><a href="list_articles.php">Articles</a></li>
							<li><a href="index_integrated.php">Store</a></li>
							<li><a href="indexRanim.php">Donation</a></li>
							<li><a href="contact.html">Contact</a></li>
						</ul>
					</div>
				</div>

				<div class="col-6 col-sm-6 col-md-6 col-lg-3">
					<div class="widget">
						<h3>Popular Causes</h3>
						<ul class="list-unstyled float-left links">
							<li><a href="indexRanim.php">Food for the Hungry</a></li>
							<li><a href="indexRanim.php">Education for Children</a></li>
							<li><a href="indexRanim.php">Support for Livelihood</a></li>
							<li><a href="indexRanim.php">Medical Mission</a></li>
							<li><a href="indexRanim.php">Community Health</a></li>
						</ul>
					</div>
				</div>

				<div class="col-6 col-sm-6 col-md-6 col-lg-3">
					<div class="widget">
						<h3>Quick Links</h3>
						<ul class="list-unstyled float-left links">
							<li><a href="index.php#about">About Us</a></li>
							<li><a href="events.php">Our Events</a></li>
							<li><a href="index_integrated.php">Shop Products</a></li>
							<li><a href="userinfo.php">My Profile</a></li>
						</ul>
					</div>
				</div>

				<div class="col-6 col-sm-6 col-md-6 col-lg-3">
					<div class="widget">
						<h3>Contact</h3>
						<address>21 Rue el baten, el ghazela, Ariana 2080</address>
						<ul class="list-unstyled links mb-4">
							<li><a href="tel:+21671523640">+216 71 523 640</a></li>
							<li><a href="tel:+21697254985">+216 97 254 985</a></li>
							<li><a href="mailto:info@peaceconnect.org">info@peaceconnect.org</a></li>
						</ul>

						<h3>Connect</h3>
						<ul class="list-unstyled social">
							<li><a href="#"><span class="icon-instagram"></span></a></li>
							<li><a href="#"><span class="icon-twitter"></span></a></li>
							<li><a href="#"><span class="icon-facebook"></span></a></li>
							<li><a href="#"><span class="icon-linkedin"></span></a></li>
							<li><a href="#"><span class="icon-pinterest"></span></a></li>
							<li><a href="#"><span class="icon-dribbble"></span></a></li>
						</ul>
					</div>
				</div>
			</div>

			<div class="row mt-5">
				<div class="col-12 text-center">
					<p class="copyright">Copyright &copy;<script>document.write(new Date().getFullYear());</script>. All Rights Reserved. &mdash; Designed with love by <a href="https://untree.co">Untree.co</a></p>
				</div>
			</div>
		</div>
	</div>

	<!-- Newsletter Sticky Banner -->
	<div class="newsletter-banner" id="newsletterBanner">
		<div class="container">
			<div class="newsletter-banner-content">
				<div>
					<h5 class="mb-0">📧 Restez informé des nouveaux articles !</h5>
					<small>Abonnez-vous à notre newsletter</small>
				</div>
				<form action="../../controller/route_newsletter.php" method="POST" class="newsletter-quick-form">
					<input type="hidden" name="action" value="subscribe">
					<input type="text" name="nom" placeholder="Votre nom">
					<input type="email" name="email" placeholder="Votre email" required>
					<button type="submit">S'abonner</button>
				</form>
				<button class="close-banner" onclick="closeNewsletterBanner()">✕ Fermer</button>
			</div>
		</div>
	</div>

	<div id="overlayer"></div>
	<div class="loader">
		<div class="spinner-border text-primary" role="status">
			<span class="visually-hidden">Loading...</span>
		</div>
	</div>

	<script src="js/bootstrap.bundle.min.js"></script>
	<script src="js/tiny-slider.js"></script>
	<script src="js/flatpickr.min.js"></script>
	<script src="js/glightbox.min.js"></script>
	<script src="js/aos.js"></script>
	<script src="js/counter.js"></script>
	<script src="js/custom.js"></script>
	
	<script>
		// Navbar mobile and scroll behaviors
		document.addEventListener('DOMContentLoaded', function() {
			var burger = document.getElementById('burger');
			var mobileMenu = document.getElementById('mobileMenu');
			var closeMobile = document.getElementById('closeMobile');
			var siteNav = document.getElementById('siteNav');
			var hero = document.querySelector('.hero');

			function openMobile() { mobileMenu.classList.add('open'); }
			function closeMobileMenu() { mobileMenu.classList.remove('open'); }
			function handleScroll() {
				var trigger = hero ? hero.offsetHeight - 80 : 80;
				if (window.scrollY > trigger) { siteNav.classList.add('scrolled'); }
				else { siteNav.classList.remove('scrolled'); }
			}

			burger?.addEventListener('click', openMobile);
			closeMobile?.addEventListener('click', closeMobileMenu);
			Array.prototype.forEach.call(mobileMenu?.querySelectorAll('a') || [], function(a) {
				a.addEventListener('click', closeMobileMenu);
			});

			handleScroll();
			window.addEventListener('scroll', handleScroll);
		});

		// Show newsletter banner after 3 seconds if not closed
		window.addEventListener('load', function() {
			setTimeout(function() {
				if (!localStorage.getItem('newsletterBannerClosed')) {
					document.getElementById('newsletterBanner').classList.add('show');
				}
			}, 3000);
		});
		
		function closeNewsletterBanner() {
			document.getElementById('newsletterBanner').classList.remove('show');
			localStorage.setItem('newsletterBannerClosed', 'true');
		}
		
		// Show banner again after 24 hours
		setInterval(function() {
			localStorage.removeItem('newsletterBannerClosed');
		}, 86400000); // 24 hours
	</script>

</body>
</html>
