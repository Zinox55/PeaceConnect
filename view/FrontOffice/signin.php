<?php
session_start();
include_once '../../controller/userController.php';
include_once '../../model/sign_up.php';
$message="";
$userC = new userController();

// Your reCAPTCHA secret key (you need to get this from Google)
$recaptcha_secret = "6LdrQCAsAAAAAAmaBk6EjksS8T4DEBB3vp6nM2cN";

// Check if cookies exist and auto-fill
$remembered_email = isset($_COOKIE['user_email']) ? $_COOKIE['user_email'] : '';
$remembered_password = isset($_COOKIE['user_password']) ? $_COOKIE['user_password'] : '';

if(isset($_POST['email']) && 
   isset($_POST['password'])) {
	if(!empty($_POST['email']) && !empty($_POST['password'])) {
		
		// Verify reCAPTCHA
		if(isset($_POST['g-recaptcha-response'])) {
			$recaptcha_response = $_POST['g-recaptcha-response'];
			
			// Verify with Google
			$verify_url = 'https://www.google.com/recaptcha/api/siteverify';
			$verify_data = array(
				'secret' => $recaptcha_secret,
				'response' => $recaptcha_response,
				'remoteip' => $_SERVER['REMOTE_ADDR']
			);
			
			$options = array(
				'http' => array(
					'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
					'method'  => 'POST',
					'content' => http_build_query($verify_data)
				)
			);
			
			$context  = stream_context_create($options);
			$verify_response = file_get_contents($verify_url, false, $context);
			$response_data = json_decode($verify_response);
			
			if($response_data->success) {
				$message=$userC->connectUser($_POST['email'],$_POST['password']);
				$_SESSION['e']=$_POST["email"];
				
				if($message!="wrong email or password"){
					if(isset($_POST['remember']) && $_POST['remember'] == 'on') {
						setcookie('user_email', $_POST['email'], time() + (30 * 24 * 60 * 60), '/');
						setcookie('user_password', $_POST['password'], time() + (30 * 24 * 60 * 60), '/');
					} else {
						if(isset($_COOKIE['user_email'])) {
							setcookie('user_email', '', time() - 3600, '/');
						}
						if(isset($_COOKIE['user_password'])) {
							setcookie('user_password', '', time() - 3600, '/');
						}
					}
					
					header('Location:user.php');
					exit();
				}
				else{
					$message="wrong email or password";
				}
			} else {
				// CAPTCHA verification failed
				$message="Please complete the CAPTCHA verification";
			}
		} else {
			$message="Please complete the CAPTCHA";
		}
	}
	else{
		$message="missing info";
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
	<script src="js/sign_in.js"></script>
	
	<!-- Google reCAPTCHA -->
	<script src="https://www.google.com/recaptcha/api.js" async defer></script>

	<title>Volunteer &mdash; Free Bootstrap 5 Website Template by Untree.co</title>
</head>
<body>

	<div class="site-mobile-menu site-navbar-target">
		<div class="site-mobile-menu-header">
			<div class="site-mobile-menu-close">
				<span class="icofont-close js-menu-toggle"></span>
			</div>
		</div>
		<div class="site-mobile-menu-body"></div>
	</div>

	<nav class="site-nav">
		<div class="container">
			<div class="menu-bg-wrap">
				<div class="site-navigation">
					<div class="row g-0 align-items-center">
						<div class="col-2">
							<a href="index.html" class="logo m-0 float-start text-white">PeaceConnect</a>
						</div>
						<div class="col-8 text-center">
							<ul class="js-clone-nav d-none d-lg-inline-block text-start site-menu mx-auto">
								<li class="active"><a href="index.html">Home</a></li>
								<li><a href="#">Article</a></li>
								<li><a href="#">store</a></li>
								<li><a href="#">event</a></li>
								<li><a href="#">donation</a></li>
								<li><a href="signin.php">sign In</a></li>
							</ul>
						</div>
						<div class="col-2 text-end">
							<a href="#" class="burger ms-auto float-end site-menu-toggle js-menu-toggle d-inline-block d-lg-none light">
								<span></span>
							</a>

							<a href="#" class="call-us d-flex align-items-center">
								<span class="icon-phone"></span>
								<span>123-489-9381</span>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</nav>

	<div class="hero overlay" style="background-image: url('images/img_v_8-min.jpg')">
		<div class="container">
			<div class="row align-items-center justify-content-center">
				<div class="col-lg-6">
					<form id="sign" action="signin.php" method="POST">
						<fieldset class="border p-4 rounded text-white">
							<legend class="w-auto px-2 text-white">Sign In</legend>
							
							<?php if(!empty($message)): ?>
								<div class="alert alert-danger" role="alert">
									<?php echo htmlspecialchars($message); ?>
								</div>
							<?php endif; ?>
							
							<div class="mb-3">
								<label for="email" class="form-label text-white">Email</label>
								<input type="email" class="form-control" id="email" name="email" 
									   value="<?php echo htmlspecialchars($remembered_email); ?>">
								<span id="email_error"></span>
							</div>
							
							<div class="mb-3">
								<label for="password" class="form-label text-white">Password</label>
								<input type="password" class="form-control" id="password" name="password"
									   value="<?php echo htmlspecialchars($remembered_password); ?>">
								<span id="password_error"></span>
							</div>
							
							<!-- reCAPTCHA Widget -->
							<div class="mb-3">
								<div class="g-recaptcha" data-sitekey="6LdrQCAsAAAAAL1KOrr4L456x7BTevbtVxSEG4F8"></div>
							</div>
							
							<div class="mb-3 d-flex justify-content-between align-items-center">
								<div class="form-check">
									<input class="form-check-input" type="checkbox" id="remember" name="remember"
										   <?php echo !empty($remembered_email) ? 'checked' : ''; ?>>
									<label class="form-check-label text-white" for="remember">
										Remember me
									</label>
								</div>
								<a href="forget_password.php" class="text-white text-decoration-none">Forgot Password?</a>
							</div>
							
							<button type="submit" id="submit" class="btn btn-primary w-100 mb-3">Sign In</button>
							
							<div class="text-center">
								<span class="text-white">Don't have an account? </span>
								<a href="signup.php" class="text-white fw-bold">Sign Up</a>
							</div>
						</fieldset>
					</form>
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
							<li><a href="#">About us</a></li>
							<li><a href="#">Donate Now</a></li>
							<li><a href="#">Causes</a></li>
							<li><a href="#">Volunteer</a></li>
							<li><a href="#">Terms</a></li>
							<li><a href="#">Privacy</a></li>
						</ul>
					</div>
				</div>

				<div class="col-6 col-sm-6 col-md-6 col-lg-3">
					<div class="widget">
						<h3>Popular Causes</h3>
						<ul class="list-unstyled float-left links">
							<li><a href="#">Food for the Hungry</a></li>
							<li><a href="#">Education for Children</a></li>
							<li><a href="#">Support for Livelihood</a></li>
							<li><a href="#">Medical Mission</a></li>
							<li><a href="#">Education</a></li>
						</ul>
					</div>
				</div>

				<div class="col-6 col-sm-6 col-md-6 col-lg-3">
					<div class="widget">
						<h3>Services</h3>
						<ul class="list-unstyled float-left links">
							<li><a href="#">Causes</a></li>
							<li><a href="#">Volunteer</a></li>
							<li><a href="#">Terms</a></li>
						</ul>
					</div>
				</div>

				<div class="col-6 col-sm-6 col-md-6 col-lg-3">
					<div class="widget">
						<h3>Contact</h3>
						<address>43 Raymouth Rd. Baltemoer, London 3910</address>
						<ul class="list-unstyled links mb-4">
							<li><a href="tel://11234567890">+1(123)-456-7890</a></li>
							<li><a href="tel://11234567890">+1(123)-456-7890</a></li>
							<li><a href="mailto:info@mydomain.com">info@mydomain.com</a></li>
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
					<p class="copyright">Copyright &copy;<script>document.write(new Date().getFullYear());</script>. All Rights Reserved. &mdash; Designed with love by <a href="https://untree.co">Untree.co</a>
					</p>
				</div>
			</div>
		</div>
	</div>

	<script src="js/bootstrap.bundle.min.js"></script>
	<script src="js/tiny-slider.js"></script>
	<script src="js/flatpickr.min.js"></script>
	<script src="js/glightbox.min.js"></script>
	<script src="js/aos.js"></script>
	<script src="js/navbar.js"></script>
	<script src="js/counter.js"></script>
</body>
</html>