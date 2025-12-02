<?php
include_once __DIR__ . '/../../Controller/ArticleController.php';
include_once __DIR__ . '/../../Controller/CommentaireController.php';
include_once __DIR__ . '/../../Model/Like.php';

$articleController = new ArticleController();
$commentaireController = new CommentaireController();

// Database connection for Like model
$database = new Database();
$db = $database->getConnection();
$likeModel = new Like($db);

if (isset($_GET['id'])) {
    $articleController->edit($_GET['id']);
    $article = $articleController->edit($_GET['id']);
    $comments = $commentaireController->getByArticle($_GET['id']);
    $likeCount = $likeModel->countLikes($_GET['id']);
} else {
    header("Location: list_articles.php");
    exit();
}
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $article->titre; ?> - Blog</title>
    
    <link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Roboto&family=Work+Sans:wght@400;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="fonts/icomoon/style.css">
	<link rel="stylesheet" href="fonts/flaticon/font/flaticon.css">
	<link rel="stylesheet" href="css/tiny-slider.css">
	<link rel="stylesheet" href="css/aos.css">
	<link rel="stylesheet" href="css/flatpickr.min.css">
	<link rel="stylesheet" href="css/glightbox.min.css">
	<link rel="stylesheet" href="css/style.css">
</head>
<body>

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
								<li class="active"><a href="list_articles.php">Articles</a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</nav>

	<div class="hero overlay" style="background-image: url('images/hero_2.jpg'); height: 300px; min-height: 300px;">
		<div class="container">
			<div class="row align-items-center justify-content-center">
				<div class="col-lg-8 text-center">
					<h1 class="heading text-white mb-2" data-aos="fade-up"><?php echo $article->titre; ?></h1>
                    <p class="text-white">By <?php echo $article->auteur; ?> | <?php echo $article->date_creation; ?></p>
				</div>
			</div>
		</div>
	</div>

	<div class="section">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 mx-auto">
                    <?php if($article->image): ?>
                        <img src="../../Uploads/<?php echo $article->image; ?>" alt="Image" class="img-fluid mb-4 rounded">
                    <?php endif; ?>
                    
                    <div class="mb-4">
                        <button onclick="speakText()" class="btn btn-info btn-sm" type="button"><span class="icon-volume-up"></span> Listen</button>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"); ?>" target="_blank" class="btn btn-primary btn-sm"><span class="icon-facebook"></span> Share</a>
                        
                        <form action="../../Controller/route_like.php" method="POST" style="display:inline;">
                            <input type="hidden" name="article_id" value="<?php echo $article->id; ?>">
                            <button type="submit" class="btn btn-danger btn-sm"><span class="icon-heart"></span> Like (<?php echo $likeCount; ?>)</button>
                        </form>
                    </div>

					<div id="article-content">
                        <p><?php echo nl2br($article->contenu); ?></p>
                    </div>
                    
                    <hr>
                    
                    <!-- Comments Section -->
                    <div class="pt-5">
                        <h3 class="mb-5">Comments</h3>
                        <ul class="comment-list">
                            <?php while ($comment = $comments->fetch(PDO::FETCH_ASSOC)): ?>
                            <li class="comment">
                                <div class="vcard bio">
                                    <img src="images/person_1.jpg" alt="Image placeholder">
                                </div>
                                <div class="comment-body">
                                    <h3><?php echo $comment['auteur']; ?></h3>
                                    <div class="meta"><?php echo $comment['date_creation']; ?></div>
                                    <p><?php echo $comment['contenu']; ?></p>
                                </div>
                            </li>
                            <?php endwhile; ?>
                        </ul>
                        
                        <div class="comment-form-wrap pt-5">
                            <h3 class="mb-5">Leave a comment</h3>
                            <form action="../../Controller/route_comment.php" method="POST" class="p-5 bg-light" novalidate onsubmit="return validateCommentForm()">
                                <!-- Note: Direct action to controller file might need adjustment if not using route file, but I implemented create() in Controller to handle POST. 
                                     However, accessing Controller file directly might not trigger the method unless I add routing logic there too.
                                     I'll create a route_comment.php to be safe. -->
                                <input type="hidden" name="action" value="create"> <!-- Need to handle this in route -->
                                <input type="hidden" name="article_id" value="<?php echo $article->id; ?>">
                                
                                <div class="form-group">
                                    <label for="name">Name *</label>
                                    <input type="text" class="form-control" id="name" name="auteur">
                                    <small id="nameError" class="text-danger"></small>
                                </div>
                                <div class="form-group">
                                    <label for="message">Message *</label>
                                    <textarea name="contenu" id="message" cols="30" rows="10" class="form-control"></textarea>
                                    <small id="messageError" class="text-danger"></small>
                                </div>
                                <div class="form-group">
                                    <input type="submit" value="Post Comment" class="btn btn-primary">
                                </div>
                            </form>
                        </div>
                    </div>

				</div>
			</div>
		</div>
	</div>

    <script>
    function speakText() {
        var text = document.getElementById('article-content').innerText;
        var msg = new SpeechSynthesisUtterance();
        msg.text = text;
        window.speechSynthesis.speak(msg);
    }

    function validateCommentForm() {
        var name = document.getElementById('name').value;
        var message = document.getElementById('message').value;
        var isValid = true;

        if (name.trim() == "") {
            document.getElementById('nameError').innerText = "Name is required";
            isValid = false;
        } else {
            document.getElementById('nameError').innerText = "";
        }

        if (message.trim() == "") {
            document.getElementById('messageError').innerText = "Message is required";
            isValid = false;
        } else {
            document.getElementById('messageError').innerText = "";
        }

        return isValid;
    }
    </script>

	<script src="js/bootstrap.bundle.min.js"></script>
	<script src="js/tiny-slider.js"></script>
	<script src="js/flatpickr.min.js"></script>
	<script src="js/glightbox.min.js"></script>
	<script src="js/aos.js"></script>
	<script src="js/navbar.js"></script>
	<script src="js/counter.js"></script>
	<script src="js/custom.js"></script>

</body>
</html>
