

<?php
session_start();
if (!isset($_SESSION['e'])) {
    header('Location: signin.php');
    exit();
}

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controller/DonController.php';
require_once __DIR__ . '/../../controller/CauseController.php';
require_once __DIR__ . '/../../model/don.php';

$error = "";
$success = "";
$donC = new DonController();
$causeC = new CauseController();
// Fetch all causes for the dropdown
$listCauses = $causeC->listCauses();
// addDon.php hattitou linna maa index
    if (
        !empty($_POST["montant"]) &&
        !empty($_POST["donateur_nom"]) &&
        !empty($_POST["donateur_email"]) &&
        !empty($_POST["methode_paiement"])&&
		!empty($_POST["cause"])
    ) {
        try {
            // Create Don object
            $don = new Don(
                null, // id_don (auto-increment)
                floatval($_POST["montant"]),
                !empty($_POST["devise"]) ? $_POST["devise"] : 'DT',
                !empty($_POST["date_don"]) ? new DateTime($_POST["date_don"]) : new DateTime(),
                $_POST["donateur_nom"],
                !empty($_POST["message"]) ? $_POST["message"] : '',
                $_POST["methode_paiement"],
                null, // transaction_id
                $_POST["donateur_email"],
				intval($_POST["cause"])
            );
            
            

            // Try to add to database
            $result = $donC->addDon($don);
            
            if ($result) {
                // Get the last inserted ID
                $db = config::getConnexion();
                $lastId = $db->lastInsertId();
                
                // Redirect to receipt page
                header("Location: receiptDon.php?id=" . $lastId);
                exit;
            }

            

        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
            //echo "✗ EXCEPTION CAUGHT: " . $error . "<br>";
        }
        
    
}
?>

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
	<link rel="stylesheet" href="hero-navbar.css">

	<title>PeaceConnect</title>
</head>
<script>
document.addEventListener("DOMContentLoaded", function() {
    console.log("DOM Loaded");
    
    const form = document.getElementById("donation-form");
    console.log("Form:", form);
    
    if (!form) {
        alert("ERROR: Form with id='donation-form' not found!");
        return;
    }
    
    form.addEventListener("submit", function(event) {
        console.log("Form submit triggered");
        
        // Check cause
        const causeElement = document.getElementById("cause");
        console.log("Cause element:", causeElement);
        
        if (!causeElement) {
            alert("ERROR: Element with id='cause' not found!");
            event.preventDefault();
            return false;
        }
        
        const causeValue = causeElement.value;
        console.log("Cause value:", causeValue);
        
        if (!causeValue || causeValue === "") {
            alert("❌ Veuillez sélectionner une cause!");
            event.preventDefault();
            return false;
        }
        
        // Check donor name
        const donateurNomElement = document.getElementById("donateur_nom");
        if (!donateurNomElement) {
            alert("ERROR: Element with id='donateur_nom' not found!");
            event.preventDefault();
            return false;
        }
        
        const donateurNom = donateurNomElement.value.trim();
        const regexNom = /^[A-Za-zÀ-ÖØ-öø-ÿ\s]+$/;
        
        if (donateurNom.length < 3 || !regexNom.test(donateurNom)) {
            alert("❌ Le nom doit contenir au moins 3 lettres (lettres seulement)!");
            event.preventDefault();
            return false;
        }
        
        // Check email
        const donateurEmailElement = document.getElementById("donateur_email");
        if (!donateurEmailElement) {
            alert("ERROR: Element with id='donateur_email' not found!");
            event.preventDefault();
            return false;
        }
        
        const donateurEmail = donateurEmailElement.value.trim();
        const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (!regexEmail.test(donateurEmail)) {
            alert("❌ L'email n'est pas valide!");
            event.preventDefault();
            return false;
        }
        
        // Check payment method
        const methodePaiementElement = document.getElementById("methode_paiement");
        if (!methodePaiementElement) {
            alert("ERROR: Element with id='methode_paiement' not found!");
            event.preventDefault();
            return false;
        }
        
        const methodePaiement = methodePaiementElement.value;
        const paiementsValides = ["card", "paypal", "cash"];
        
        if (!paiementsValides.includes(methodePaiement)) {
            alert("❌ Veuillez sélectionner une méthode de paiement valide!");
            event.preventDefault();
            return false;
        }
        
        // Check amount
        const montantElement = document.getElementById("montant");
        if (!montantElement) {
            alert("ERROR: Element with id='montant' not found!");
            event.preventDefault();
            return false;
        }
        
        const montant = parseFloat(montantElement.value);
        if (isNaN(montant) || montant <= 0) {
            alert("❌ Le montant doit être un nombre positif!");
            event.preventDefault();
            return false;
        }
        
        // All validations passed
        console.log("✅ All validations passed!");
        return true;
    });
});
</script>
<body>

	<!-- Navbar (unified) -->
	<nav class="site-nav" id="siteNav">
		<div class="container">
			<div class="menu-wrap">
				<a href="index.php" class="logo">PeaceConnect</a>
				<ul class="site-menu" id="mainMenu">
					<li><a href="index.php">Home</a></li>
					<li class="active"><a href="indexRanim.php">Donations</a></li>
					<li><a href="causes.html">Causes</a></li>
					<li><a href="contact.html">Contact</a></li>
					<li><a href="userinfo.php">Profile</a></li>
				</ul>
				<a href="tel:+33123456789" class="call-us"><span class="icon-phone"></span>+33 1 23 45 67 89</a>
				<div class="burger" id="burger"><span></span></div>
			</div>
		</div>
	</nav>
	<div class="mobile-menu" id="mobileMenu">
		<div class="close-btn" id="closeMobile">&times;</div>
		<ul>
			<li><a href="index.php">Home</a></li>
			<li class="active"><a href="indexRanim.php">Donations</a></li>
			<li><a href="causes.html">Causes</a></li>
			<li><a href="contact.html">Contact</a></li>
			<li><a href="userinfo.php">Profile</a></li>
		</ul>
	</div>

	<div class="hero overlay" style="background-image: url('images/hero_2.jpg'); padding-top: 150px;">
		<div class="container">
			<div class="row align-items-center justify-content-between">
				<div class="col-lg-6 text-left">
					<h1 class="heading text-white mb-2" data-aos="fade-up">Give a helping hand to those who need it!</h1>
					<p data-aos="fade-up" class=" mb-5 text-white lead text-white-50">Together, we can create peace through kindness. Join our community of donors bringing relief and hope to people in need.</p>
					<p data-aos="fade-up"  data-aos-delay="100">
						<a href="#" class="btn btn-primary me-4 d-inline-flex align-items-center"> <span>Donate</span></a>	
					</p>		
				</div>

				<div class="col-lg-5">
    <form id="donation-form" action="indexRanim.php" method="POST" class="bg-white p-5 rounded donation-form" data-aos="fade-up">
        <h3>Quick Donation Form</h3>
						<!--  CAUSE SELECTION - NEW FIELD -->
						<div class="form-field mb-3">
							<select class="form-control px-4" id="id_cause" name="cause" required style="height: 50px;">
								<option value=""> Select a Cause </option>
								<?php 
								if ($listCauses && $listCauses->rowCount() > 0) {
									while ($cause = $listCauses->fetch(PDO::FETCH_ASSOC)) {
										echo "<option value='{$cause['id_cause']}'>{$cause['nom_cause']}</option>";
									}
								}
								?>
							</select>
						</div>

        <!-- Montant custom input -->
        <div class="field-icon mb-3">
            <span>dt</span>
            <input type="text" placeholder="0.00" class="form-control px-4" id="montant" name="montant" value="1.00">
        </div>

        <!-- Donateur name & Date -->
        <div class="row mb-3">
            <div class="col-md-6">
                <input type="text" placeholder="Name" class="form-control px-4" id="donateur_nom" name="donateur_nom" style="height: 50px;">
            </div>
            <div class="col-md-6">
                <input type="date" class="form-control px-4" id="date_don" name="date_don" style="height: 50px;">
            </div>
        </div>

        <!-- Email -->
        <div class="form-field mb-3">
            <input type="email" placeholder="Email" class="form-control px-4" id="donateur_email" name="donateur_email">
        </div>

        <!-- Devise -->
        <div class="form-field mb-3">
            <input type="text" placeholder="Devise (ex: DT, USD)" class="form-control px-4" id="devise" name="devise">
        </div>

        <!-- Méthode de paiement -->
        <div class="form-field mb-3">
            <select class="form-control px-4" id="methode_paiement" name="methode_paiement">
                <option value="">Select payment method</option>
                <option value="card">Card</option>
                <option value="paypal">Paypal</option>
                <option value="cash">Cash</option>
            </select>
        </div>

        <!-- Message -->
        <div class="form-field mb-3">
            <textarea class="form-control px-4" id="message" name="message" placeholder="Leave a message (optional)" rows="3"></textarea>
        </div>

        <!-- Submit button -->
        <input type="submit" value="Donate now" class="btn btn-secondary w-100">
    </form>
</div>

			</div>
		</div>
	</div>

	<div class="section bg-light">
		<div class="container">
			<div class="row">
				<div class="col-lg-6" data-aos="fade-up">
					<div class="vision">
						<h2>Our Vision</h2>
						<p class="mb-4 lead">At PeaceConnect, we envision a world united by compassion, where every act of generosity builds bridges across borders, cultures, and crises. We believe that peace and dignity begin with solidarity, and that by connecting those who can help with those who need it most, we can create lasting change for generations to come.</p>
					</div>
				</div>
				<div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
					<div class="mission">
						<h2>Our Mission</h2>
						<p class="mb-4 lead">Our mission is to empower individuals, journalists, and organizations to take meaningful action in times of need. Through transparent donations, impactful storytelling, and community-driven initiatives, PeaceConnect strives to provide relief, raise awareness, and inspire hope. We turn empathy into action: one contribution, one story, one connection at a time.</p>
					</div>
				</div>
			</div>		
		</div>		
	</div>




	<div class="section flip-section secondary-bg" style="background-image: url('images/img_v_4-min.jpg')">
		<div class="container">
			<div class="row">
				<div class="col-lg-7 mx-auto text-center">
					<span class="subheading-white mb-3 text-white" data-aos="fade-up">Help Now</span>
					<h3 class="mb-4 heading text-white" data-aos="fade-up">Let's Help The Unfortunate People </h3>
					<a href="#" class="btn btn-outline-white" data-aos="fade-up" data-aos-delay="200">Donate Now</a>
				</div>		
			</div>		
		</div>		
	</div>

	<div class="section sec-instagram pb-0">
		<div class="container mb-5">
			<div class="row align-items-center">
				<div class="col-lg-3" data-aos="fade-up">
					<span class="subheading mb-3">Instagram</span>
					<h2 class="heading">We Are In Instagram</h2>
				</div>
				<div class="col-lg-7" data-aos="fade-up" data-aos-delay="100">
					<p>
					Join our community on Instagram and see how your support is making a difference every day!</p>
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

				<div class="item">
					<a class="instagram-item">
						<span class="icon-instagram"></span>
						<img src="images/img_v_2-min.jpg" alt="Image" class="img-fluid">		
					</a>
				</div>

				<div class="item">
					<a class="instagram-item">
						<span class="icon-instagram"></span>
						<img src="images/img_v_3-min.jpg" alt="Image" class="img-fluid">		
					</a>
				</div>

				<div class="item">
					<a class="instagram-item">
						<span class="icon-instagram"></span>
						<img src="images/img_v_4-min.jpg" alt="Image" class="img-fluid">		
					</a>
				</div>

				<div class="item">
					<a class="instagram-item">
						<span class="icon-instagram"></span>
						<img src="images/img_v_5-min.jpg" alt="Image" class="img-fluid">		
					</a>
				</div>

				<div class="item">
					<a class="instagram-item">
						<span class="icon-instagram"></span>
						<img src="images/img_v_6-min.jpg" alt="Image" class="img-fluid">		
					</a>
				</div>

				<div class="item">
					<a class="instagram-item">
						<span class="icon-instagram"></span>
						<img src="images/img_v_7-min.jpg" alt="Image" class="img-fluid">		
					</a>
				</div>


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
					</div> <!-- /.widget -->
				</div> <!-- /.col-lg-3 -->

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
					</div> <!-- /.widget -->
				</div> <!-- /.col-lg-3 -->

				<div class="col-6 col-sm-6 col-md-6 col-lg-3">
					<div class="widget">
						<h3>Quick Links</h3>
						<ul class="list-unstyled float-left links">
							<li><a href="index.php#about">About Us</a></li>
							<li><a href="events.php">Our Events</a></li>
							<li><a href="index_integrated.php">Shop Products</a></li>
							<li><a href="userinfo.php">My Profile</a></li>
						</ul>
					</div> <!-- /.widget -->
				</div> <!-- /.col-lg-3 -->


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

					</div> <!-- /.widget -->
				</div> <!-- /.col-lg-3 -->

			</div> <!-- /.row -->


			<div class="row mt-5">
				<div class="col-12 text-center">
					<p class="copyright">Copyright &copy;<script>document.write(new Date().getFullYear());</script>. All Rights Reserved.   <!-- License information: https://untree.co/license/ -->
					</p>
				</div>
			</div>
		</div> <!-- /.container -->
	</div> <!-- /.site-footer -->







	<!-- Preloader -->
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

			burger.addEventListener('click', openMobile);
			closeMobile.addEventListener('click', closeMobileMenu);
			Array.prototype.forEach.call(mobileMenu.querySelectorAll('a'), function(a) {
				a.addEventListener('click', closeMobileMenu);
			});

			handleScroll();
			window.addEventListener('scroll', handleScroll);
		});
	</script>
	
</body>
</html>
					