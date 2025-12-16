<?php
// View: Détail d'un article Frontend
// Affichage d'un article complet avec commentaires
session_start();
if (!isset($_SESSION['e'])) {
    $current = 'article_detail.php' . (isset($_GET['id']) ? ('?id=' . urlencode($_GET['id'])) : '');
    header('Location: signin.php?redirect=' . urlencode($current));
    exit();
}

include_once __DIR__ . '/../../controller/ArticleController.php';
include_once __DIR__ . '/../../controller/CommentaireController.php';
include_once __DIR__ . '/../../model/Like.php';

// Function to render comment content with emojis and GIFs
function renderCommentContent($content) {
    // First, escape HTML for security
    $content = htmlspecialchars($content);
    
    // Convert line breaks
    $content = nl2br($content);
    
    // Convert GIF markdown to actual images
    $content = preg_replace_callback(
        '/\[GIF:([^\]]+)\]\(([^)]+)\)/',
        function($matches) {
            $altText = htmlspecialchars($matches[1]);
            $gifUrl = htmlspecialchars($matches[2]);
            return '<br><img src="' . $gifUrl . '" alt="' . $altText . '" class="comment-gif" loading="lazy"><br>';
        },
        $content
    );
    
    return $content;
}

$articleController = new ArticleController();
$commentaireController = new CommentaireController();

// Database connection for Like model
// Use PDO from app config
$db = config::getConnexion();
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
	<title><?php echo htmlspecialchars($article->titre); ?> - Blog</title>
    
    <link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css2?family=Roboto&family=Work+Sans:wght@400;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="fonts/icomoon/style.css">
	<link rel="stylesheet" href="fonts/flaticon/font/flaticon.css">
	<link rel="stylesheet" href="css/tiny-slider.css">
	<link rel="stylesheet" href="css/aos.css">
	<link rel="stylesheet" href="css/flatpickr.min.css">
	<link rel="stylesheet" href="css/glightbox.min.css">
	<link rel="stylesheet" href="css/style.css">
	
	<style>
		/* Hero section improvements */
		.hero.overlay {
			background-attachment: fixed;
			background-size: cover;
			background-position: center;
		}
		
		.article-header-image {
			width: 100%;
			height: 450px;
			object-fit: cover;
			border-radius: 20px;
			box-shadow: 0 10px 40px rgba(0,0,0,0.15);
			margin-bottom: 30px;
		}
		.article-meta-info {
			display: flex;
			align-items: center;
			gap: 30px;
			padding: 20px;
			background: #f8f9fa;
			border-radius: 15px;
			margin-bottom: 30px;
			flex-wrap: wrap;
		}
		.meta-item {
			display: flex;
			align-items: center;
			gap: 8px;
		}
		.author-badge {
			width: 40px;
			height: 40px;
			border-radius: 50%;
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			color: white;
			display: flex;
			align-items: center;
			justify-content: center;
			font-weight: 700;
			font-size: 18px;
		}
		#article-content {
			font-size: 1.1rem;
			line-height: 1.8;
			color: #2c3e50;
			padding: 30px;
			background: white;
			border-radius: 15px;
			box-shadow: 0 5px 15px rgba(0,0,0,0.05);
		}
		.action-buttons {
			display: flex;
			gap: 10px;
			flex-wrap: wrap;
			margin-bottom: 30px;
		}
		.action-btn {
			border-radius: 25px;
			padding: 10px 25px;
			font-weight: 600;
			transition: all 0.3s ease;
			border: none;
			display: inline-flex;
			align-items: center;
			gap: 8px;
		}
		.action-btn:hover {
			transform: translateY(-2px);
			box-shadow: 0 5px 15px rgba(0,0,0,0.2);
		}
		.comment-card {
			background: white;
			padding: 25px;
			border-radius: 15px;
			box-shadow: 0 3px 10px rgba(0,0,0,0.08);
			margin-bottom: 20px;
			transition: all 0.3s ease;
		}
		.comment-card:hover {
			box-shadow: 0 5px 20px rgba(0,0,0,0.12);
		}
		.comment-author-avatar {
			width: 50px;
			height: 50px;
			border-radius: 50%;
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			color: white;
			display: flex;
			align-items: center;
			justify-content: center;
			font-weight: 700;
			font-size: 20px;
			margin-right: 15px;
		}
		.comment-form-card {
			background: #f8f9fa;
			padding: 30px;
			border-radius: 15px;
			box-shadow: 0 5px 15px rgba(0,0,0,0.05);
		}
		
		/* Responsive improvements */
		@media (max-width: 768px) {
			.hero.overlay {
				height: 300px !important;
				min-height: 300px !important;
			}
			
			.hero .container .row {
				height: 300px !important;
			}
			
			.hero h1.heading {
				font-size: 1.8rem !important;
			}
			
			.article-meta-info {
				flex-direction: column;
				gap: 15px;
				text-align: center;
			}
			
			.action-buttons {
				justify-content: center;
			}
			
			.action-btn {
				font-size: 0.9rem;
				padding: 8px 20px;
			}
		}
		
		@media (max-width: 576px) {
			.hero h1.heading {
				font-size: 1.5rem !important;
			}
			
			.hero .d-flex {
				flex-direction: column !important;
				gap: 10px !important;
			}
		}
		
		/* Emoji & GIF Picker Styles */
		.emoji-grid, .gif-grid {
			display: grid;
			grid-template-columns: repeat(5, 1fr);
			gap: 8px;
		}
		
		.emoji-item {
			font-size: 24px;
			cursor: pointer;
			padding: 8px;
			border-radius: 5px;
			text-align: center;
			transition: background 0.2s;
		}
		
		.emoji-item:hover {
			background: #f0f0f0;
		}
		
		.gif-item {
			text-align: center;
			cursor: pointer;
			transition: transform 0.2s;
		}
		
		.gif-item:hover {
			transform: scale(1.05);
		}
		
		.gif-item small {
			display: block;
			margin-top: 5px;
			font-size: 10px;
			color: #666;
		}
		
		.comment-toolbar .btn {
			border: none;
			font-size: 12px;
			padding: 4px 8px;
		}
		
		.emoji-btn, .gif-btn {
			margin-right: 5px;
		}
		
		/* Enhanced comment display */
		.comment-content {
			line-height: 1.6;
		}
		
		.comment-content img.comment-gif {
			max-width: 200px;
			max-height: 150px;
			border-radius: 8px;
			margin: 10px 0;
			box-shadow: 0 2px 8px rgba(0,0,0,0.1);
		}
		
		.comment-content .emoji {
			font-size: 1.2em;
		}
	</style>
</head>
<body>

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
                                <li class="active"><a href="list_articles.php">Articles</a></li>
                                <li><a href="contact.html">Contact</a></li>
                                <li><a href="userinfo.php">User</a></li>
                            </ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</nav>

	<div class="hero overlay" style="background-image: url('images/hero_2.jpg'); height: 400px; min-height: 400px; position: relative;">
		<!-- Dark overlay for better text readability -->
		<div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 1;"></div>
		<div class="container" style="position: relative; z-index: 2;">
			<div class="row align-items-center justify-content-center" style="height: 400px;">
				<div class="col-lg-10 text-center">
					<div style="background: rgba(0,0,0,0.7); padding: 40px; border-radius: 20px; backdrop-filter: blur(10px);">
						<h1 class="heading text-white mb-3" data-aos="fade-up" style="font-size: 2.5rem; font-weight: 700; text-shadow: 2px 2px 4px rgba(0,0,0,0.8); line-height: 1.2;">
							<?php echo htmlspecialchars($article->titre); ?>
						</h1>
						<div class="d-flex justify-content-center align-items-center gap-4 flex-wrap" style="color: #f8f9fa;">
							<div class="d-flex align-items-center gap-2">
								<i class="icon-user" style="font-size: 18px;"></i>
								<span style="font-weight: 600;"><?php echo htmlspecialchars($article->auteur); ?></span>
							</div>
							<div class="d-flex align-items-center gap-2">
								<i class="icon-calendar" style="font-size: 18px;"></i>
								<span><?php echo date('d M Y', strtotime($article->date_creation)); ?></span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="section">
		<div class="container">
			<div class="row">
				<div class="col-lg-10 mx-auto">
					<!-- Article Image -->
                    <?php if($article->image): ?>
                        <img src="../../model/uploads/<?php echo $article->image; ?>" alt="<?php echo htmlspecialchars($article->titre); ?>" class="article-header-image" data-aos="fade-up">
                    <?php endif; ?>
                    
                    <!-- Article Meta -->
                    <div class="article-meta-info" data-aos="fade-up">
                    	<div class="meta-item">
                    		<div class="author-badge"><?php echo strtoupper(substr($article->auteur, 0, 1)); ?></div>
                    		<div>
                    			<small class="text-muted d-block">Auteur</small>
                    			<strong><?php echo htmlspecialchars($article->auteur); ?></strong>
                    		</div>
                    	</div>
                    	<div class="meta-item">
                    		<i class="icon-calendar" style="font-size: 24px; color: #667eea;"></i>
                    		<div>
                    			<small class="text-muted d-block">Publié le</small>
                    			<strong><?php echo date('d M Y \u00e0 H:i', strtotime($article->date_creation)); ?></strong>
                    		</div>
                    	</div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="action-buttons" data-aos="fade-up">
                        <a href="export_pdf_download.php?id=<?php echo $article->id; ?>" class="btn btn-success action-btn" target="_blank">
                        	<span class="icon-file-text"></span> Télécharger PDF
                        </a>
                        <button onclick="speakText()" class="btn btn-info action-btn" type="button">
                        	<span class="icon-volume-up"></span> Écouter
                        </button>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"); ?>" target="_blank" class="btn btn-primary action-btn">
                        	<span class="icon-facebook"></span> Partager
                        </a>
                        <form action="../../controller/route_like.php" method="POST" style="display:inline;">
                            <input type="hidden" name="article_id" value="<?php echo $article->id; ?>">
                            <button type="submit" class="btn btn-danger action-btn">
                            	<span class="icon-heart"></span> J'aime (<?php echo $likeCount; ?>)
                            </button>
                        </form>
                    </div>

					<!-- Article Content -->
					<div id="article-content" data-aos="fade-up">
                        <?php echo nl2br(htmlspecialchars($article->contenu)); ?>
                    </div>
                    
                    <hr class="my-5">
                    
                    <!-- Comments Section -->
                    <div class="pt-4" data-aos="fade-up">
                        <h3 class="mb-4">💬 Commentaires</h3>
                        <div class="comments-wrapper">
                            <?php while ($comment = $comments->fetch(PDO::FETCH_ASSOC)): ?>
                            <div class="comment-card">
                            	<div class="d-flex">
                            		<div class="comment-author-avatar">
                            			<?php echo strtoupper(substr($comment['auteur'], 0, 1)); ?>
                            		</div>
	                                <div class="flex-grow-1">
	                                    <h5 class="mb-1"><?php echo htmlspecialchars($comment['auteur']); ?></h5>
	                                    <small class="text-muted">
	                                    	<i class="icon-calendar"></i> <?php echo date('d M Y \u00e0 H:i', strtotime($comment['date_creation'])); ?>
	                                    </small>
	                                    <div class="mt-3 mb-0 comment-content"><?php echo renderCommentContent($comment['contenu']); ?></div>
	                                </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        
                        <div class="comment-form-card mt-5">
                            <h4 class="mb-4">✍️ Laissez un commentaire</h4>
                            <form action="../../controller/route_comment.php" method="POST" novalidate onsubmit="return validateCommentForm()">
                                <input type="hidden" name="action" value="create">
                                <input type="hidden" name="article_id" value="<?php echo $article->id; ?>">
                                
                                <div class="form-group mb-3">
                                    <label for="name" class="fw-bold">Votre nom *</label>
                                    <input type="text" class="form-control" id="name" name="auteur" placeholder="Entrez votre nom" style="border-radius: 10px; padding: 12px;">
                                    <small id="nameError" class="text-danger"></small>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="message" class="fw-bold">Votre commentaire *</label>
                                    <div class="comment-input-wrapper" style="position: relative;">
                                        <textarea name="contenu" id="message" cols="30" rows="5" class="form-control" placeholder="Partagez votre avis... 😊" style="border-radius: 10px; padding: 12px; padding-bottom: 50px;"></textarea>
                                        
                                        <!-- Emoji & GIF Toolbar -->
                                        <div class="comment-toolbar" style="position: absolute; bottom: 10px; left: 15px; right: 15px; display: flex; justify-content: space-between; align-items: center; background: #f8f9fa; padding: 8px 12px; border-radius: 8px; border-top: 1px solid #e9ecef;">
                                            <div class="emoji-section">
                                                <button type="button" class="btn btn-sm btn-light emoji-btn" onclick="toggleEmojiPicker()" title="Ajouter un emoji">
                                                    😊
                                                </button>
                                                <button type="button" class="btn btn-sm btn-light gif-btn" onclick="toggleGifPicker()" title="Ajouter un GIF">
                                                    🎬 GIF
                                                </button>
                                            </div>
                                            <div class="char-counter">
                                                <small class="text-muted"><span id="charCount">0</span>/500</small>
                                            </div>
                                        </div>
                                        
                                        <!-- Emoji Picker -->
                                        <div id="emojiPicker" class="emoji-picker" style="display: none; position: absolute; bottom: 60px; left: 15px; background: white; border: 1px solid #ddd; border-radius: 10px; padding: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.15); z-index: 1000; max-width: 300px;">
                                            <div class="emoji-categories">
                                                <div class="emoji-category">
                                                    <h6>😊 Émotions</h6>
                                                    <div class="emoji-grid">
                                                        <span class="emoji-item" onclick="insertEmoji('😊')">😊</span>
                                                        <span class="emoji-item" onclick="insertEmoji('😂')">😂</span>
                                                        <span class="emoji-item" onclick="insertEmoji('❤️')">❤️</span>
                                                        <span class="emoji-item" onclick="insertEmoji('👍')">👍</span>
                                                        <span class="emoji-item" onclick="insertEmoji('👏')">👏</span>
                                                        <span class="emoji-item" onclick="insertEmoji('🙏')">🙏</span>
                                                        <span class="emoji-item" onclick="insertEmoji('💪')">💪</span>
                                                        <span class="emoji-item" onclick="insertEmoji('🔥')">🔥</span>
                                                        <span class="emoji-item" onclick="insertEmoji('✨')">✨</span>
                                                        <span class="emoji-item" onclick="insertEmoji('🎉')">🎉</span>
                                                        <span class="emoji-item" onclick="insertEmoji('😍')">😍</span>
                                                        <span class="emoji-item" onclick="insertEmoji('🤔')">🤔</span>
                                                        <span class="emoji-item" onclick="insertEmoji('😢')">😢</span>
                                                        <span class="emoji-item" onclick="insertEmoji('😮')">😮</span>
                                                        <span class="emoji-item" onclick="insertEmoji('🤗')">🤗</span>
                                                    </div>
                                                </div>
                                                <div class="emoji-category mt-3">
                                                    <h6>🌟 Symboles</h6>
                                                    <div class="emoji-grid">
                                                        <span class="emoji-item" onclick="insertEmoji('🌟')">🌟</span>
                                                        <span class="emoji-item" onclick="insertEmoji('⭐')">⭐</span>
                                                        <span class="emoji-item" onclick="insertEmoji('💫')">💫</span>
                                                        <span class="emoji-item" onclick="insertEmoji('🌈')">🌈</span>
                                                        <span class="emoji-item" onclick="insertEmoji('🕊️')">🕊️</span>
                                                        <span class="emoji-item" onclick="insertEmoji('☮️')">☮️</span>
                                                        <span class="emoji-item" onclick="insertEmoji('💝')">💝</span>
                                                        <span class="emoji-item" onclick="insertEmoji('🎯')">🎯</span>
                                                        <span class="emoji-item" onclick="insertEmoji('🚀')">🚀</span>
                                                        <span class="emoji-item" onclick="insertEmoji('💡')">💡</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- GIF Picker -->
                                        <div id="gifPicker" class="gif-picker" style="display: none; position: absolute; bottom: 60px; left: 15px; background: white; border: 1px solid #ddd; border-radius: 10px; padding: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.15); z-index: 1000; width: 350px;">
                                            <div class="gif-search mb-3">
                                                <input type="text" class="form-control form-control-sm" placeholder="Rechercher un GIF..." onkeyup="searchGifs(this.value)">
                                            </div>
                                            <div class="gif-categories">
                                                <h6>🎬 GIFs Populaires</h6>
                                                <div class="gif-grid" id="gifGrid">
                                                    <div class="gif-item" onclick="insertGif('https://media.giphy.com/media/3o7abKhOpu0NwenH3O/giphy.gif', 'Happy')">
                                                        <img src="https://media.giphy.com/media/3o7abKhOpu0NwenH3O/200w.gif" alt="Happy" style="width: 80px; height: 60px; object-fit: cover; border-radius: 5px; cursor: pointer;">
                                                        <small>Happy</small>
                                                    </div>
                                                    <div class="gif-item" onclick="insertGif('https://media.giphy.com/media/l0MYt5jPR6QX5pnqM/giphy.gif', 'Applause')">
                                                        <img src="https://media.giphy.com/media/l0MYt5jPR6QX5pnqM/200w.gif" alt="Applause" style="width: 80px; height: 60px; object-fit: cover; border-radius: 5px; cursor: pointer;">
                                                        <small>Applause</small>
                                                    </div>
                                                    <div class="gif-item" onclick="insertGif('https://media.giphy.com/media/26u4cqiYI30juCOGY/giphy.gif', 'Thumbs Up')">
                                                        <img src="https://media.giphy.com/media/26u4cqiYI30juCOGY/200w.gif" alt="Thumbs Up" style="width: 80px; height: 60px; object-fit: cover; border-radius: 5px; cursor: pointer;">
                                                        <small>Thumbs Up</small>
                                                    </div>
                                                    <div class="gif-item" onclick="insertGif('https://media.giphy.com/media/3o6Zt481isNVuQI1l6/giphy.gif', 'Peace')">
                                                        <img src="https://media.giphy.com/media/3o6Zt481isNVuQI1l6/200w.gif" alt="Peace" style="width: 80px; height: 60px; object-fit: cover; border-radius: 5px; cursor: pointer;">
                                                        <small>Peace</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <small id="messageError" class="text-danger"></small>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary action-btn">
                                    	<i class="icon-paper-plane"></i> Publier le commentaire
                                    </button>
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
        } else if (message.length > 500) {
            document.getElementById('messageError').innerText = "Message too long (max 500 characters)";
            isValid = false;
        } else {
            document.getElementById('messageError').innerText = "";
        }

        return isValid;
    }
    
    // Emoji & GIF Functions
    function toggleEmojiPicker() {
        var picker = document.getElementById('emojiPicker');
        var gifPicker = document.getElementById('gifPicker');
        
        // Close GIF picker if open
        gifPicker.style.display = 'none';
        
        // Toggle emoji picker
        picker.style.display = picker.style.display === 'none' ? 'block' : 'none';
    }
    
    function toggleGifPicker() {
        var picker = document.getElementById('gifPicker');
        var emojiPicker = document.getElementById('emojiPicker');
        
        // Close emoji picker if open
        emojiPicker.style.display = 'none';
        
        // Toggle GIF picker
        picker.style.display = picker.style.display === 'none' ? 'block' : 'none';
    }
    
    function insertEmoji(emoji) {
        var textarea = document.getElementById('message');
        var cursorPos = textarea.selectionStart;
        var textBefore = textarea.value.substring(0, cursorPos);
        var textAfter = textarea.value.substring(cursorPos);
        
        textarea.value = textBefore + emoji + textAfter;
        textarea.focus();
        textarea.setSelectionRange(cursorPos + emoji.length, cursorPos + emoji.length);
        
        updateCharCount();
        document.getElementById('emojiPicker').style.display = 'none';
    }
    
    function insertGif(gifUrl, altText) {
        var textarea = document.getElementById('message');
        var cursorPos = textarea.selectionStart;
        var textBefore = textarea.value.substring(0, cursorPos);
        var textAfter = textarea.value.substring(cursorPos);
        
        var gifMarkdown = `[GIF:${altText}](${gifUrl})`;
        textarea.value = textBefore + gifMarkdown + textAfter;
        textarea.focus();
        textarea.setSelectionRange(cursorPos + gifMarkdown.length, cursorPos + gifMarkdown.length);
        
        updateCharCount();
        document.getElementById('gifPicker').style.display = 'none';
    }
    
    function updateCharCount() {
        var textarea = document.getElementById('message');
        var charCount = document.getElementById('charCount');
        var currentLength = textarea.value.length;
        
        charCount.textContent = currentLength;
        
        if (currentLength > 500) {
            charCount.style.color = '#dc3545';
        } else if (currentLength > 400) {
            charCount.style.color = '#ffc107';
        } else {
            charCount.style.color = '#6c757d';
        }
    }
    
    function searchGifs(query) {
        // Simple GIF search simulation
        var gifGrid = document.getElementById('gifGrid');
        
        if (query.length < 2) {
            // Show default GIFs
            gifGrid.innerHTML = `
                <div class="gif-item" onclick="insertGif('https://media.giphy.com/media/3o7abKhOpu0NwenH3O/giphy.gif', 'Happy')">
                    <img src="https://media.giphy.com/media/3o7abKhOpu0NwenH3O/200w.gif" alt="Happy" style="width: 80px; height: 60px; object-fit: cover; border-radius: 5px; cursor: pointer;">
                    <small>Happy</small>
                </div>
                <div class="gif-item" onclick="insertGif('https://media.giphy.com/media/l0MYt5jPR6QX5pnqM/giphy.gif', 'Applause')">
                    <img src="https://media.giphy.com/media/l0MYt5jPR6QX5pnqM/200w.gif" alt="Applause" style="width: 80px; height: 60px; object-fit: cover; border-radius: 5px; cursor: pointer;">
                    <small>Applause</small>
                </div>
                <div class="gif-item" onclick="insertGif('https://media.giphy.com/media/26u4cqiYI30juCOGY/giphy.gif', 'Thumbs Up')">
                    <img src="https://media.giphy.com/media/26u4cqiYI30juCOGY/200w.gif" alt="Thumbs Up" style="width: 80px; height: 60px; object-fit: cover; border-radius: 5px; cursor: pointer;">
                    <small>Thumbs Up</small>
                </div>
                <div class="gif-item" onclick="insertGif('https://media.giphy.com/media/3o6Zt481isNVuQI1l6/giphy.gif', 'Peace')">
                    <img src="https://media.giphy.com/media/3o6Zt481isNVuQI1l6/200w.gif" alt="Peace" style="width: 80px; height: 60px; object-fit: cover; border-radius: 5px; cursor: pointer;">
                    <small>Peace</small>
                </div>
            `;
            return;
        }
        
        // Show loading
        gifGrid.innerHTML = '<div class="text-center"><small>Recherche...</small></div>';
        
        // Simulate search results based on query
        setTimeout(() => {
            var searchResults = getGifSearchResults(query.toLowerCase());
            gifGrid.innerHTML = searchResults;
        }, 500);
    }
    
    function getGifSearchResults(query) {
        var gifs = {
            'happy': `<div class="gif-item" onclick="insertGif('https://media.giphy.com/media/3o7abKhOpu0NwenH3O/giphy.gif', 'Happy')">
                        <img src="https://media.giphy.com/media/3o7abKhOpu0NwenH3O/200w.gif" alt="Happy" style="width: 80px; height: 60px; object-fit: cover; border-radius: 5px; cursor: pointer;">
                        <small>Happy</small>
                      </div>`,
            'peace': `<div class="gif-item" onclick="insertGif('https://media.giphy.com/media/3o6Zt481isNVuQI1l6/giphy.gif', 'Peace')">
                        <img src="https://media.giphy.com/media/3o6Zt481isNVuQI1l6/200w.gif" alt="Peace" style="width: 80px; height: 60px; object-fit: cover; border-radius: 5px; cursor: pointer;">
                        <small>Peace</small>
                      </div>`,
            'love': `<div class="gif-item" onclick="insertGif('https://media.giphy.com/media/l0MYt5jPR6QX5pnqM/giphy.gif', 'Love')">
                       <img src="https://media.giphy.com/media/l0MYt5jPR6QX5pnqM/200w.gif" alt="Love" style="width: 80px; height: 60px; object-fit: cover; border-radius: 5px; cursor: pointer;">
                       <small>Love</small>
                     </div>`
        };
        
        for (var key in gifs) {
            if (key.includes(query)) {
                return gifs[key];
            }
        }
        
        return '<div class="text-center"><small>Aucun GIF trouvé</small></div>';
    }
    
    // Initialize character counter
    document.addEventListener('DOMContentLoaded', function() {
        var textarea = document.getElementById('message');
        if (textarea) {
            textarea.addEventListener('input', updateCharCount);
            updateCharCount();
        }
        
        // Close pickers when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.comment-input-wrapper')) {
                document.getElementById('emojiPicker').style.display = 'none';
                document.getElementById('gifPicker').style.display = 'none';
            }
        });
    });
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
