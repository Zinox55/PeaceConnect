<!--
Template Name: Volunteer
Template Author: Untree.co
Template URI: https://untree.co/
License: https://creativecommons.org/licenses/by/3.0/
-->
<?php
include_once '../../controller/userController.php';
include_once '../../model/sign_up.php';

session_start();

if (!isset($_SESSION['user_email'])) {
    header("Location: signin.php");
    exit;
}

$profileController = new userController();
$userEmail = $_SESSION['user_email'];
$user = $profileController->getUserByEmail($userEmail);


$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

 $current_password = isset($_POST['current_password']) ? $_POST['current_password'] : '';
$new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
$re_new_password = isset($_POST['re_new_password']) ? $_POST['re_new_password'] : '';

 $update = $profileController->updatePassword($userEmail, $new_password);
   
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
							<a href="index.php" class="logo m-0 float-start text-white">PeaceConnect</a>
						</div>
						<div class="col-8 text-center">
							<ul class="js-clone-nav d-none d-lg-inline-block text-start site-menu mx-auto">
								<li><a href="index.php">Home</a></li>
								<li class="active"><a href="userinfo.php">My Profile</a></li>
								<li><a href="disconnect.php">Logout</a></li>
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
					
	
					
					
					<!-- User Profile Section -->
					<div class="mb-1">
						<div class="bg-white bg-opacity-90 p-4 rounded shadow">
							<h4 class="text-dark mb-3 pb-2 border-bottom">User Profile</h4>
							
							<!-- Display User Info -->
		
								<div class="row">
									<div class="col-md-6 mb-3">
										<label class="text-muted small">Name</label>
										<p class="text-dark fw-bold mb-0"><?php echo htmlspecialchars($user['name']); ?></p>
									</div>
									<div class="col-md-6 mb-3">
										<label class="text-muted small">Email</label>
										<p class="text-dark fw-bold mb-0"><?php echo htmlspecialchars($user['email']); ?></p>
									</div>
								</div>
							
						</div>
					</div>

					<!-- Change Password Section -->
					<form id="passwordForm" action="userinfo.php" method="POST">
						<div class="bg-white bg-opacity-90 p-4 rounded shadow">
							<h4 class="text-dark mb-3 pb-2 border-bottom">Change Password</h4>
							
							<div class="mb-3">
								<label for="current_password" class="form-label text-dark fw-semibold">Current Password</label>
								<input type="password" class="form-control" id="current_password" name="current_password" required>
							</div>
							
							<div class="mb-3">
								<label for="new_password" class="form-label text-dark fw-semibold">New Password</label>
								<input type="password" class="form-control" id="new_password" name="new_password" required>
							</div>
							
							<div class="mb-4">
								<label for="re_new_password" class="form-label text-dark fw-semibold">Verify New Password</label>
								<input type="password" class="form-control" id="re_new_password" name="re_new_password" required>
							</div>
							
							<button type="submit" class="btn btn-primary btn-lg w-100">Update Password</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<div class="site-footer">
		<div class="container">
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