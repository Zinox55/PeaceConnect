<?php
session_start();
$isConnected = isset($_SESSION['e']);
$userEmail = $isConnected ? $_SESSION['e'] : '';
// Load 3 upcoming events for homepage section
try {
	require_once __DIR__ . '/../../model/EventModel.php';
	$eventModel = new EventModel();
	$latestEvents = $eventModel->getUpcomingEvents(3);
} catch (Exception $e) {
	$latestEvents = [];
}
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

	<link rel="stylesheet" href
	="css/tiny-slider.css">
	<link rel="stylesheet" href="css/aos.css">
	<link rel="stylesheet" href="css/flatpickr.min.css">
	<link rel="stylesheet" href="css/glightbox.min.css">
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="hero-navbar.css">

	<title>PeaceConnect &mdash; Free Bootstrap 5 Website Template by Untree.co</title>
</head>
<body>

	<!-- Navbar (store style unified) -->
	<nav class="site-nav" id="siteNav">
		<div class="container">
			<div class="menu-wrap">
				<a href="index.php" class="logo">PeaceConnect</a>
				<ul class="site-menu" id="mainMenu">
					<li class="active"><a href="index.php">Home</a></li>
					<li><a href="list_articles.php">Articles</a></li>
					<li><a href="index_integrated.php">Store</a></li>
					<li><a href="events.php">Events</a></li>
					<li><a href="indexRanim.php">Donation</a></li>
					<li>
						<?php if ($isConnected): ?>
							<a href="userinfo.php">Profile</a>
						<?php else: ?>
							<a href="signin.php">Sign In</a>
						<?php endif; ?>
					</li>
				</ul>
				<a href="tel:+1234899381" class="call-us"><span class="icon-phone"></span>+123 489 9381</a>
				<div class="burger" id="burger"><span></span></div>
			</div>
		</div>
	</nav>
	<!-- Mobile Menu -->
	<div class="mobile-menu" id="mobileMenu">
		<div class="close-btn" id="closeMobile">&times;</div>
		<ul>
			<li><a href="index.php" class="active">Home</a></li>
			<li><a href="list_articles.php">Articles</a></li>
			<li><a href="index_integrated.php">Store</a></li>
			<li><a href="events.php">Events</a></li>
			<li><a href="indexRanim.php">Donation</a></li>
			<li>
				<?php if ($isConnected): ?>
					<a href="userinfo.php">Profile</a>
				<?php else: ?>
					<a href="signin.php">Sign In</a>
				<?php endif; ?>
			</li>
		</ul>
	</div>

	<div class="hero overlay" style="background-image: url('images/hero_2.jpg')">
		<div class="container">
			<div class="row align-items-center justify-content-between">
				<div class="col-lg-6 text-left">
					<span class="subheading-white text-white mb-3" data-aos="fade-up">PeaceConnect</span>
					<h1 class="heading text-white mb-2" data-aos="fade-up">Give a helping hand to those who need it!</h1>
					<p data-aos="fade-up" class=" mb-5 text-white lead text-white-50">Join us to support communities with education, health, and rapid relief. Together we turn compassion into real impact.</p>
					<p data-aos="fade-up"  data-aos-delay="100">
						<a href="/PeaceConnect/view/FrontOffice/indexRanim.php" class="btn btn-primary me-4 d-inline-flex align-items-center"> <span class="icon-attach_money me-2"></span><span>Donate Now</span></a> 
						<a href="https://www.youtube.com/watch?v=mwtbEGNABWU" class="text-white glightbox d-inline-flex align-items-center"><span class="icon-play me-2"></span><span>Watch the video</span></a>
					</p>		
					
				</div>

				<!-- Quick donation form removed: use dedicated donation page -->
			</div>
		</div>
	</div>

	<div class="section bg-light">
		<div class="container">
			<div class="row">
				<div class="col-lg-6" data-aos="fade-up">
					<div class="vision">
						<h2>Our Vision</h2>
						<p class="mb-4 lead">We envision peaceful, resilient communities where everyone can thrive. Our programs champion dignity, inclusion, and sustainable change.</p>
						<p><a href="#" class="link-underline">Learn More</a></p>
					</div>
				</div>
				<div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
					<div class="mission">
						<h2>Our Mission</h2>
						<p class="mb-4 lead">We mobilize volunteers and donors to deliver essential support—food, shelter, education, and mental health—to people facing crisis and inequality.</p>
						<p><a href="#" class="link-underline">Learn More</a></p>
					</div>
				</div>
			</div>		
		</div>		
	</div>


	<div class="section flip-section" style="background-image: url('images/img_v_2-min.jpg')">
		<div class="blob-1">
			<img src="images/blob.png" alt="Image" class="img-fluid">
		</div>
		<div class="container">
			<div class="row justify-content-center mb-5">
				<div class="col-lg-7 text-center" data-aos="fade-up">
					<span class="subheading-white mb-3 text-white">Help Now</span>
					<h2 class="heading text-white">Help Today</h2>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-3 position-relative" data-aos="fade-up" data-aos-delay="100">
					<div class="card-flip">
						<div class="flip">
							<div class="front">
								<!-- front content -->
								<div class="flip-content-wrap">
									<span class="icon-local_drink"></span>
									<h3>Pure Water</h3>
								</div>
							</div>
							<div class="back">
								<!-- back content -->
								<div class="flip-content-wrap">
									<h3>Pure Water</h3>
									<p>Clean, safe drinking water for families and schools.</p>
								</div>
							</div>
						</div>
					</div>
					<!-- End Card Flip -->
				</div>
				<div class="col-lg-3 position-relative" data-aos="fade-up" data-aos-delay="200">
					<div class="card-flip">
						<div class="flip">
							<div class="front">
								<!-- front content -->
								<div class="flip-content-wrap">
									<span class="icon-graduation-cap"></span>
									<h3>Give Education</h3>
								</div>
							</div>
							<div class="back">
								<!-- back content -->
								<div class="flip-content-wrap">
									<h3>Give Education</h3>
									<p>Scholarships and supplies that keep children learning.</p>
								</div>
							</div>
						</div>
					</div>

				</div>
				<div class="col-lg-3 position-relative" data-aos="fade-up" data-aos-delay="300">
					<div class="card-flip">
						<div class="flip">
							<div class="front">
								<!-- front content -->
								<div class="flip-content-wrap">
									<span class="icon-dollar"></span>
									<h3>Give Donation</h3>
								</div>
							</div>
							<div class="back">
								<!-- back content -->
								<div class="flip-content-wrap">
									<h3>Give Donation</h3>
									<p>Your gift powers urgent aid and long‑term projects.</p>
								</div>
							</div>
						</div>
					</div>

				</div>
				<div class="col-lg-3 position-relative" data-aos="fade-up" data-aos-delay="400">
					<div class="card-flip">
						<div class="flip">
							<div class="front">
								<!-- front content -->
								<div class="flip-content-wrap">
									<span class="icon-medkit"></span>
									<h3>Medical Mission</h3>
								</div>
							</div>
							<div class="back">
								<!-- back content -->
								<div class="flip-content-wrap">
									<h3>Medical Mission</h3>
									<p>Community clinics and medical outreach for everyone.</p>
								</div>
							</div>
						</div>
					</div>

				</div>
			</div>		
		</div>		
	</div>




	<div class="section bg-light">
		<div class="container">
			<div class="row mb-5 align-items-center justify-content-between">
				<div class="col-lg-5" data-aos="fade-up" data-aos-delay="0">
					<span class="subheading mb-3">Who we are</span>
					<h2 class="heading">About Us</h2>
					<p>PeaceConnect connects local volunteers with trusted causes. We partner with communities to deliver practical help where it’s needed most.</p>
				</div>

				<div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
						<blockquote>
							“When people act with empathy and purpose, small efforts grow into movements that change lives.”
						</blockquote>
				</div>
			</div>
			<div class="row justify-content-between">
				<div class="col-lg-5 pe-lg-5" data-aos="fade-up" data-aos-delay="200">

					<ul class="nav nav-pills mb-5 custom-nav-pills" id="pills-tab" role="tablist">
						<li class="nav-item" role="presentation">
							<button class="nav-link active" id="pills-mission-tab" data-bs-toggle="pill" data-bs-target="#pills-mission" type="button" role="tab" aria-controls="pills-mission" aria-selected="true">Our Mission</button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link" id="pills-values-tab" data-bs-toggle="pill" data-bs-target="#pills-values" type="button" role="tab" aria-controls="pills-values" aria-selected="false">Our Values</button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link" id="pills-history-tab" data-bs-toggle="pill" data-bs-target="#pills-history" type="button" role="tab" aria-controls="pills-history" aria-selected="false">Our History</button>
						</li>
					</ul>
					<div class="tab-content" id="pills-tabContent">
						<div class="tab-pane fade show active" id="pills-mission" role="tabpanel" aria-labelledby="pills-mission-tab">
							<h2 class="mb-3 text-primary fw-bold">Our Mission</h2>
							<p>We deliver community‑driven assistance—food security, clean water, education, and livelihood support—designed with local partners.</p>
							<p>Transparency guides our work: clear goals, measurable outcomes, and respectful collaboration with the people we serve.</p>
							<p class="mt-5">
								<a href="/PeaceConnect/view/FrontOffice/indexRanim.php" class="btn btn-primary me-4">Donate Now</a>
								<a href="#" class="link-more">Learn More <span class="icon-chevron-right"></span></a>
							</p>
						</div>
						<div class="tab-pane fade" id="pills-values" role="tabpanel" aria-labelledby="pills-values-tab">
							<h2 class="mb-3 text-primary fw-bold">Our Values</h2>
							<p>We stand for dignity, inclusion, and accountability in everything we do.</p>
							<p>Every program is co‑created with communities to ensure long‑term impact and local ownership.</p>
							<p class="mt-5">
								<a href="#" class="btn btn-primary me-4">Be A Volunteer</a>
								<a href="#" class="link-more">Learn More <span class="icon-chevron-right"></span></a>
							</p>
						</div>
						<div class="tab-pane fade" id="pills-history" role="tabpanel" aria-labelledby="pills-history-tab">

							<h2 class="mb-3 text-primary fw-bold">Our History</h2>
							<p>Founded by volunteers, PeaceConnect has grown into a regional network supporting initiatives across communities.</p>
							<p>From rapid relief to sustainable development, our focus remains the same: people first.</p>
							<p class="mt-5">
								<a href="#" class="btn btn-primary me-4">Be a Sponsor</a>
								<a href="#" class="link-more">Learn More <span class="icon-chevron-right"></span></a>
							</p>
						</div>
					</div>

				</div>
				<div class="col-lg-6">
					<div class="overlap-imgs">
						<img src="images/img_v_2-min.jpg" alt="Image" class="img-fluid rounded" data-aos="fade-up" data-aos="100">
						<img src="images/img_v_3-min.jpg" alt="Image" class="img-fluid rounded" data-aos="fade-up" data-aos="200">
					</div>
				</div>		
			</div>		
		</div>		
	</div>


	<div class="section cause-section bg-light">

		<div class="container">
			<div class="row justify-content-center mb-5">
				<div class="col-lg-6 text-center" data-aos="fade-up" data-aos-delay="100">
					<span class="subheading mb-3">Causes</span>
					<h2 class="heading">Featured Causes</h2>
					<p>Explore current initiatives funded by donors like you.</p>

					<div id="features-slider-nav" class="mt-5 d-flex justify-content-center">
						<button  class="btn btn-primary prev d-flex align-items-center me-2" data-controls="prev"> <span class="icon-chevron-left"></span> <span class="ms-3">Prev</span></button>
						<button class="btn btn-primary next d-flex align-items-center" data-controls="next"><span class="me-3">Next</span> <span class="icon-chevron-right"></span></button>
					</div>
				</div>
			</div>	
		</div>


		<div class="container mb-5">
			<div class="features-slider-wrap position-relative" data-aos="fade-up" data-aos-delay="200">
				<div class="features-slider" id="features-slider">

					<div class="item">
						<div class="causes-item bg-white">
							<a href="#"><img src="images/img_v_1-min.jpg" alt="Image" class="img-fluid mb-4 rounded"></a>
							<div class="px-4 pb-5 pt-3">

								<h3><a href="#">Food for the Hungry</a></h3>
								<p>Community‑led projects delivering real, measurable benefits on the ground.</p>

								<div class="progress mb-2">
									<div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
								</div>

								<div class="d-flex mb-4 justify-content-between amount">
									<div>$509.00</div>
									<div>$10,000.00</div>
								</div>
								<div>
										<a href="/PeaceConnect/view/FrontOffice/indexRanim.php" class="btn btn-primary">Donate Now</a>
								</div>
							</div>
						</div>
					</div>


					<div class="item">
						<div class="causes-item bg-white">
							<a href="#"><img src="images/img_v_2-min.jpg" alt="Image" class="img-fluid mb-4 rounded"></a>
							<div class="px-4 pb-5 pt-3">
								<h3><a href="#">Education for Children</a></h3>
								<p>Community‑led projects delivering real, measurable benefits on the ground.</p>

								<div class="progress mb-2">
									<div class="progress-bar" role="progressbar" style="width: 68%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">68%</div>
								</div>

								<div class="d-flex mb-4 justify-content-between amount">
									<div>$7,597.00</div>
									<div>$10,000.00</div>
								</div>
								<div>
									<a href="#" class="btn btn-primary">Donate Now</a>
								</div>
							</div>
						</div>
					</div>

					<div class="item">
						<div class="causes-item bg-white">
							<a href="#"><img src="images/img_v_3-min.jpg" alt="Image" class="img-fluid mb-4 rounded"></a>
							<div class="px-4 pb-5 pt-3">
								<h3><a href="#">Support Livelihood</a></h3>
								<p>Community‑led projects delivering real, measurable benefits on the ground.</p>

								<div class="progress mb-2">
									<div class="progress-bar" role="progressbar" style="width: 87%;" aria-valuenow="87" aria-valuemin="0" aria-valuemax="100">87%</div>
								</div>

								<div class="d-flex mb-4 justify-content-between amount">
									<div>$19,509.00</div>
									<div>$25,000.00</div>
								</div>
								<div>
									<a href="/PeaceConnect/view/FrontOffice/indexRanim.php" class="btn btn-primary">Donate Now</a>
								</div>
							</div>
						</div>
					</div>


					<div class="item">
						<div class="causes-item bg-white">
							<a href="#"><img src="images/img_v_4-min.jpg" alt="Image" class="img-fluid mb-4 rounded"></a>
							<div class="px-4 pb-5 pt-3">

								<h3><a href="#">Food for the Hungry</a></h3>
								<p>Community‑led projects delivering real, measurable benefits on the ground.</p>

								<div class="progress mb-2">
									<div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
								</div>

								<div class="d-flex mb-4 justify-content-between amount">
									<div>$509.00</div>
									<div>$10,000.00</div>
								</div>
								<div>
									<a href="/PeaceConnect/view/FrontOffice/indexRanim.php" class="btn btn-primary">Donate Now</a>
								</div>
							</div>
						</div>
					</div>


					<div class="item">
						<div class="causes-item bg-white">
							<a href="#"><img src="images/img_v_5-min.jpg" alt="Image" class="img-fluid mb-4 rounded"></a>
							<div class="px-4 pb-5 pt-3">
								<h3><a href="#">Education for Children</a></h3>
								<p>Community‑led projects delivering real, measurable benefits on the ground.</p>

								<div class="progress mb-2">
									<div class="progress-bar" role="progressbar" style="width: 54%;" aria-valuenow="54" aria-valuemin="0" aria-valuemax="100">54%</div>
								</div>

								<div class="d-flex mb-4 justify-content-between amount">
									<div>$6,031.00</div>
									<div>$10,000.00</div>
								</div>
								<div>
									<a href="/PeaceConnect/view/FrontOffice/indexRanim.php" class="btn btn-primary">Donate Now</a>
								</div>
							</div>
						</div>
					</div>

					<div class="item">
						<div class="causes-item bg-white">
							<a href="#"><img src="images/img_v_6-min.jpg" alt="Image" class="img-fluid mb-4 rounded"></a>
							<div class="px-4 pb-5 pt-3">
								<h3><a href="#">Support Livelihood</a></h3>
								<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Soluta labore eligendi tempora laudantium voluptate, amet ad libero facilis nihil officiis.</p>

								<div class="progress mb-2">
									<div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
								</div>

								<div class="d-flex mb-4 justify-content-between amount">
									<div>$509.00</div>
									<div>$10,000.00</div>
								</div>
								<div>
									<a href="/PeaceConnect/view/FrontOffice/indexRanim.php" class="btn btn-primary">Donate Now</a>
								</div>
							</div>
						</div>
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
					<a href="#" class="btn btn-outline-white me-3" data-aos="fade-up" data-aos-delay="100">Become a Volunteer</a> <a href="/PeaceConnect/view/FrontOffice/indexRanim.php" class="btn btn-outline-white" data-aos="fade-up" data-aos-delay="200">Donate Now</a>
				</div>		
			</div>		
		</div>		
	</div>


	<div class="section bg-light">
		<div class="container">
			<div class="row justify-content-between">
				<div class="col-lg-5" data-aos="fade-up">
					<span class="subheading mb-3">Impact</span>
					<h2 class="heading mb-4">Explore Volunteer Work and Impact</h2>
					<p>See highlights from our latest projects and community actions.</p>
					<p>Figures reflect independent monitoring and feedback from local partners.</p>
				</div>		
				<div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
					<div class="row section-counter">
						<div class="col-lg-6">
							<div class="counter">
								<i class="flaticon-social-services d-block text-secondary"></i>
								
								<span class="number countup">589</span>
								<span class="d-block">New Causes</span>
							</div>

							<div class="counter">
								<i class="flaticon-charity-money d-block text-secondary"></i>
								<span class="number">$<span class="countup">920</span>M</span>
								<span class="d-block">Fund Raised</span>
							</div>

						</div>
						<div class="col-lg-6">
							<div class="counter mt-5">
								<i class="flaticon-money-donation d-block text-secondary"></i>
								<span class="number countup">4211</span>
								<span class="d-block">Donors</span>
							</div>

							<div class="counter">
								<i class="flaticon-organs-donation d-block text-secondary"></i>
								<span class="number countup">389</span>
								<span class="d-block">Volunteers</span>
							</div>
						</div>
					</div>
				</div>
			</div>		
		</div>		
	</div>

	<div class="section bg-light pt-0">
		<div class="container">
			<div class="row justify-content-center text-center">
				<div class="col-lg-6 mb-5" data-aos="fade-up">
					<span class="subheading mb-1">Events</span>
					<h2 class="heading mb-1">Our Latest Events</h2>
					<p>Be the first to join our next community actions.</p>
				</div>		
			</div>
			<div class="row">
				<?php if (!empty($latestEvents)): ?>
					<?php foreach ($latestEvents as $event): ?>
						<div class="col-lg-4 col-md-6 mb-4">
							<div class="causes-item bg-white h-100 d-flex flex-column">
								<a href="events.php"><img src="./assets_events/images/<?= htmlspecialchars($event['image'] ?? 'default-event.jpg') ?>" alt="Event Image" class="img-fluid mb-4 rounded" style="object-fit: cover; width: 100%; height: 220px;"></a>
								<div class="px-4 pb-4 pt-2 d-flex flex-column flex-grow-1">
									<span class="date"><?= date('M d, Y', strtotime($event['date_event'])) ?></span>
									<h3 class="mb-2"><a href="events.php"><?= htmlspecialchars($event['titre']) ?></a></h3>
									<?php $desc = $event['description'] ?? ''; $short = (strlen($desc) > 120) ? substr($desc, 0, 120) . '...' : $desc; ?>
									<p class="flex-grow-1"><?= htmlspecialchars($short) ?></p>
									<p class="mb-0"><a href="inscription.php?event=<?= urlencode($event['titre']) ?>&date=<?= urlencode(date('d/m/Y', strtotime($event['date_event']))) ?>&lieu=<?= urlencode($event['lieu']) ?>" class="btn btn-primary d-inline-flex align-items-center"><span class="icon-user-plus me-2"></span><span>Register</span></a></p>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				<?php else: ?>
					<div class="col-12">
						<div class="causes-item bg-white text-center p-5">
							<h3 class="mb-3">No upcoming events</h3>
							<p>We’re preparing new initiatives. Check back soon!</p>
							<a href="events.php" class="btn btn-primary">View All Events</a>
						</div>
					</div>
				<?php endif; ?>
			</div>	

			<div class="row mt-3">
				<div class="col-12 text-center">
					<a href="events.php" class="link-underline">See all events</a>
				</div>
			</div>
		</div>		
	</div>

	<div class="section sec-instagram pb-0 bg-light">
		<div class="container mb-5">
			<div class="row align-items-center">
				<div class="col-lg-3" data-aos="fade-up">
					<span class="subheading mb-3">Instagram</span>
					<h2 class="heading">We Are In Instagram</h2>
				</div>
				<div class="col-lg-7" data-aos="fade-up" data-aos-delay="100">
					<p>
					Follow daily moments from volunteers and community partners.</p>
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
					<p class="copyright">Copyright &copy;<script>document.write(new Date().getFullYear());</script>. All Rights Reserved. &mdash; Designed with love by <a href="https://untree.co">Untree.co</a> <!-- License information: https://untree.co/license/ -->
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
	<script src="js/navbar.js"></script>
	<script src="js/counter.js"></script>
	<script src="js/custom.js"></script>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			var burger = document.getElementById('burger');
			var mobileMenu = document.getElementById('mobileMenu');
			var closeMobile = document.getElementById('closeMobile');
			var siteNav = document.getElementById('siteNav');
			var hero = document.querySelector('.hero');
			var navLinks = document.querySelectorAll('#mainMenu a');
			var mobileLinks = document.querySelectorAll('#mobileMenu a');

			function openMobile() {
				mobileMenu.classList.add('open');
			}

			function closeMobileMenu() {
				mobileMenu.classList.remove('open');
			}

			function handleScroll() {
				var trigger = hero ? hero.offsetHeight - 80 : 80;
				if (window.scrollY > trigger) {
					siteNav.classList.add('scrolled');
				} else {
					siteNav.classList.remove('scrolled');
				}
			}

			function syncActive(link) {
				navLinks.forEach(function(a) { a.classList.remove('active'); });
				mobileLinks.forEach(function(a) { a.classList.remove('active'); });
				if (link) {
					var href = link.getAttribute('href');
					navLinks.forEach(function(a) { if (a.getAttribute('href') === href) a.classList.add('active'); });
					mobileLinks.forEach(function(a) { if (a.getAttribute('href') === href) a.classList.add('active'); });
				}
			}

			burger.addEventListener('click', openMobile);
			closeMobile.addEventListener('click', closeMobileMenu);
			mobileLinks.forEach(function(link) {
				link.addEventListener('click', closeMobileMenu);
			});
			navLinks.forEach(function(link) {
				link.addEventListener('click', function() { syncActive(link); });
			});

			handleScroll();
			window.addEventListener('scroll', handleScroll);
		});
	</script>
</body>
</html>
