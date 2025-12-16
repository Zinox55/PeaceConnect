<?php
// Controller: NewsletterController
// Gère la logique métier de la newsletter

include_once __DIR__ . '/../config.php';
include_once __DIR__ . '/../model/NewsletterSubscriber.php';
include_once __DIR__ . '/../model/EmailSender.php';

class NewsletterController {
    private $db;
    private $subscriber;
    private $emailSender;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->subscriber = new NewsletterSubscriber($this->db);
        $this->emailSender = new EmailSender();
    }

    public function subscribe() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->subscriber->email = trim($_POST['email']);
            $this->subscriber->nom = trim($_POST['nom']);

            // Validation email
            if (!filter_var($this->subscriber->email, FILTER_VALIDATE_EMAIL)) {
                header("Location: ../View/Front/list_articles.php?newsletter=invalid");
                exit();
            }

            $result = $this->subscriber->subscribe();
            
            if ($result === true) {
                header("Location: ../View/Front/list_articles.php?newsletter=success");
                exit();
            } elseif ($result === 'duplicate') {
                header("Location: ../View/Front/list_articles.php?newsletter=duplicate");
                exit();
            } else {
                header("Location: ../View/Front/list_articles.php?newsletter=error");
                exit();
            }
        }
    }

    public function unsubscribe() {
        if (isset($_GET['email'])) {
            $email = trim($_GET['email']);
            
            if ($this->subscriber->unsubscribe($email)) {
                header("Location: ../View/Front/list_articles.php?newsletter=unsubscribed");
                exit();
            } else {
                header("Location: ../View/Front/list_articles.php?newsletter=error");
                exit();
            }
        }
    }

    public function getAllSubscribers() {
        return $this->subscriber->getAllActiveSubscribers();
    }

    public function getSubscriberCount() {
        return $this->subscriber->countActiveSubscribers();
    }

    // Fonction pour envoyer un email aux abonnés
    public function notifySubscribers($articleId, $articleTitle, $articleExcerpt, $articleAuthor) {
        $subscribers = $this->subscriber->getAllActiveSubscribers();
        $emailsSent = 0;
        $emailsFailed = 0;
        $logFile = __DIR__ . '/../logs/email_log.txt';
        
        $subject = "📰 Nouvel article sur PeaceConnect : " . $articleTitle;
        $articleUrl = "http://" . $_SERVER['HTTP_HOST'] . "/PeaceConnecti/PeaceConnect/view/Front/article_detail.php?id=" . $articleId;
        
        while ($sub = $subscribers->fetch(PDO::FETCH_ASSOC)) {
            $to = $sub['email'];
            $name = $sub['nom'] ? $sub['nom'] : 'Cher abonné';
            
            $message = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                    .content { background: #f8f9fa; padding: 30px; }
                    .article { background: white; padding: 20px; border-radius: 10px; margin: 20px 0; }
                    .button { display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 30px; text-decoration: none; border-radius: 25px; font-weight: bold; }
                    .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>🌟 PeaceConnect</h1>
                        <p>Nouvel article publié !</p>
                    </div>
                    <div class='content'>
                        <p>Bonjour <strong>" . htmlspecialchars($name) . "</strong>,</p>
                        <p>Un nouvel article vient d'être publié sur PeaceConnect :</p>
                        
                        <div class='article'>
                            <h2>" . htmlspecialchars($articleTitle) . "</h2>
                            <p><em>Par " . htmlspecialchars($articleAuthor) . "</em></p>
                            <p>" . htmlspecialchars($articleExcerpt) . "</p>
                            <br>
                            <a href='" . $articleUrl . "' class='button'>Lire l'article complet</a>
                        </div>
                        
                        <p>Merci de faire partie de notre communauté !</p>
                    </div>
                    <div class='footer'>
                        <p>Vous recevez cet email car vous êtes abonné à notre newsletter.</p>
                        <p><a href='http://" . $_SERVER['HTTP_HOST'] . "/PeaceConnecti/PeaceConnect/controller/route_newsletter.php?action=unsubscribe&email=" . urlencode($to) . "'>Se désabonner</a></p>
                    </div>
                </div>
            </body>
            </html>
            ";
            
            // Utilise la nouvelle classe EmailSender
            $mailSent = $this->emailSender->send($to, $subject, $message);
            
            if ($mailSent) {
                $emailsSent++;
            } else {
                $emailsFailed++;
            }
        }
        
        // Log du résumé
        $summary = "\n========== SUMMARY ==========\n";
        $summary .= "Total subscribers: " . ($emailsSent + $emailsFailed) . "\n";
        $summary .= "Emails sent: " . $emailsSent . "\n";
        $summary .= "Emails failed: " . $emailsFailed . "\n";
        $summary .= "============================\n\n";
        @file_put_contents($logFile, $summary, FILE_APPEND);
        
        return ['sent' => $emailsSent, 'failed' => $emailsFailed];
    }
}
?>
