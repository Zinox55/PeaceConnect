<?php
include(__DIR__ . '/../../config.php');

$token = $_GET["token"];
$token_hash = hash("sha256", $token);

try {
    $db = config::getConnexion(); // PDO connection

    $sql = "SELECT * FROM sign_up WHERE reset_token = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$token_hash]);
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user === false) {
        die("Token not found");
    }

    if (strtotime($user["token_expiry"]) <= time()) {
        die("Token has expired");
    }

} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
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

    <title>PeaceConnect — Reset Password</title>
</head>

<body>

    <!-- NAVBAR -->
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
                                <li><a href="index.html">Home</a></li>
                                <li><a href="#">Article</a></li>
                                <li><a href="#">Store</a></li>
                                <li><a href="#">Event</a></li>
                                <li><a href="#">Donation</a></li>
                                <li><a href="signin.php">Sign In</a></li>
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

    <!-- HERO -->
    <div class="hero overlay" style="background-image: url('images/hero_2.jpg')">
        <div class="container">
            <div class="row align-items-center justify-content-between">
                <div class="col-lg-6 text-left">
                    <span class="subheading-white text-white mb-3" data-aos="fade-up">PeaceConnect</span>
                    <h1 class="heading text-white mb-2" data-aos="fade-up">Reset Your Password</h1>
                    <p data-aos="fade-up" class="mb-5 text-white lead text-white-50">
                        Update your account password safely and securely.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- RESET PASSWORD SECTION -->
    <div class="section bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5" data-aos="fade-up">

                    <div class="bg-white p-5 shadow-lg rounded-4 border">
                        <h2 class="text-center mb-4 fw-bold">🔐 Reset Password</h2>

                        <form method="post" action="proccess_reset.php">

                            <!-- TOKEN -->
                            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                            <!-- NEW PASSWORD -->
                            <div class="mb-4">
                                <label for="password_reset" class="form-label fw-semibold">New Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><span class="icon-lock"></span></span>
                                    <input type="password" id="password_reset" name="password_reset" class="form-control p-3" placeholder="Enter new password" required>
                                </div>
                            </div>

                            <!-- CONFIRM PASSWORD -->
                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><span class="icon-check"></span></span>
                                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control p-3" placeholder="Repeat password" required>
                                </div>
                            </div>

                            <!-- SUBMIT BUTTON -->
                            <button class="btn btn-primary w-100 py-3 fw-bold rounded-3">
                                Update Password
                            </button>

                        </form>

                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- END RESET PASSWORD -->

    <!-- SCRIPTS -->
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/tiny-slider.js"></script>
    <script src="js/flatpickr.min.js"></script>
    <script src="js/aos.js"></script>
    <script src="js/glightbox.min.js"></script>
    <script src="js/custom.js"></script>

</body>

</html>
