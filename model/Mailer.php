<?php
/**
 * Classe Mailer - Gestion avancée des emails avec Gmail
 * Utilise PHPMailer pour l'envoi d'emails via SMTP Gmail
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class Mailer {
    private $mail;
    private $config;
    
    public function __construct() {
        $this->mail = new PHPMailer(true);
        $this->loadConfig();
        $this->configureMailer();
    }
    
    /**
     * Charger la configuration email
     */
    private function loadConfig() {
        $configFile = __DIR__ . '/../config/config_mail.php';
        if (file_exists($configFile)) {
            $config = require $configFile;
            $this->config = [
                'smtp_host' => $config['smtp']['host'],
                'smtp_port' => $config['smtp']['port'],
                'smtp_secure' => $config['smtp']['secure'] === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS,
                'smtp_username' => $config['smtp']['username'],
                'smtp_password' => $config['smtp']['password'],
                'from_email' => $config['from']['email'],
                'from_name' => $config['from']['name'],
                'reply_to' => $config['reply_to']['email'],
                'debug' => $config['options']['debug'] ?? false
            ];
        } else {
            throw new Exception("Fichier config/config_mail.php non trouvé");
        }
    }
    
    /**
     * Configurer PHPMailer
     */
    private function configureMailer() {
        try {
            // Configuration du serveur SMTP
            $this->mail->isSMTP();
            $this->mail->Host = $this->config['smtp_host'];
            $this->mail->SMTPAuth = true;
            $this->mail->Username = $this->config['smtp_username'];
            $this->mail->Password = $this->config['smtp_password'];
            $this->mail->SMTPSecure = $this->config['smtp_secure'];
            $this->mail->Port = $this->config['smtp_port'];
            
            // Debug SMTP si activé
            if ($this->config['debug']) {
                $this->mail->SMTPDebug = SMTP::DEBUG_SERVER;
                $this->mail->Debugoutput = 'html';
            }
            
            // Configuration de l'encodage
            $this->mail->CharSet = 'UTF-8';
            $this->mail->Encoding = 'base64';
            
            // Configuration de l'expéditeur
            $this->mail->setFrom($this->config['from_email'], $this->config['from_name']);
            $this->mail->addReplyTo($this->config['reply_to'], $this->config['from_name']);
            
            // HTML par défaut
            $this->mail->isHTML(true);
            
        } catch (Exception $e) {
            throw new Exception("Erreur configuration mailer: " . $e->getMessage());
        }
    }
    
    /**
     * Envoyer un email
     * @param string $to Email destinataire
     * @param string $subject Sujet
     * @param string $body Corps HTML
     * @param string $altBody Corps texte alternatif
     * @return bool
     */
    public function send($to, $subject, $body, $altBody = '') {
        try {
            $this->mail->clearAddresses();
            $this->mail->clearAttachments();
            
            $this->mail->addAddress($to);
            $this->mail->Subject = $subject;
            $this->mail->Body = $body;
            $this->mail->AltBody = $altBody ?: strip_tags($body);
            
            $result = $this->mail->send();
            
            // Logger le succès
            if ($result) {
                $this->logEmail('SUCCESS', $to, $subject);
            }
            
            return $result;
        } catch (Exception $e) {
            // Logger l'erreur
            $this->logEmail('ERROR', $to, $subject, $this->mail->ErrorInfo);
            error_log("Erreur envoi email: " . $this->mail->ErrorInfo);
            return false;
        }
    }
    
    /**
     * Envoyer un email avec template
     * @param string $to Email destinataire
     * @param string $templateName Nom du template
     * @param array $data Données pour le template
     * @return bool
     */
    public function sendTemplate($to, $templateName, $data = []) {
        $template = $this->loadTemplate($templateName, $data);
        return $this->send($to, $template['subject'], $template['body'], $template['altBody']);
    }
    
    /**
     * Charger un template email
     * @param string $templateName
     * @param array $data
     * @return array
     */
    private function loadTemplate($templateName, $data = []) {
        switch($templateName) {
            case 'stock_alert':
                return $this->templateStockAlert($data);
            case 'order_confirmation':
                return $this->templateOrderConfirmation($data);
            case 'order_status':
                return $this->templateOrderStatus($data);
            case 'low_stock_admin':
                return $this->templateLowStockAdmin($data);
            default:
                throw new Exception("Template non trouvé: $templateName");
        }
    }
    
    /**
     * Template: Alerte de stock faible (Admin)
     */
    private function templateLowStockAdmin($data) {
        $produits = $data['produits'] ?? [];
        $total = $data['total'] ?? 0;
        
        $produitsHTML = '';
        foreach ($produits as $p) {
            $badgeColor = $p['stock'] == 0 ? '#e74a3b' : '#f6c23e';
            $produitsHTML .= "
                <tr style='border-bottom: 1px solid #e3e6f0;'>
                    <td style='padding: 12px; font-weight: 600;'>{$p['nom']}</td>
                    <td style='padding: 12px; text-align: center;'>
                        <span style='background: $badgeColor; color: white; padding: 4px 12px; border-radius: 12px; font-weight: 700;'>
                            {$p['stock']}
                        </span>
                    </td>
                    <td style='padding: 12px;'>{$p['etat_stock']}</td>
                    <td style='padding: 12px;'>" . number_format($p['prix'], 2) . " €</td>
                </tr>
            ";
        }
        
        $body = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #f6c23e 0%, #f4a23e 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: white; padding: 30px; border: 1px solid #e3e6f0; }
                .footer { background: #f8f9fc; padding: 20px; text-align: center; border-radius: 0 0 8px 8px; color: #858796; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                th { background: #f8f9fc; padding: 12px; text-align: left; font-weight: 700; }
                .alert-box { background: #fff3cd; border-left: 4px solid #f6c23e; padding: 15px; margin: 20px 0; border-radius: 4px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1 style='margin: 0;'>⚠️ Alerte de Stock</h1>
                    <p style='margin: 10px 0 0 0;'>PeaceConnect - Dashboard Admin</p>
                </div>
                <div class='content'>
                    <div class='alert-box'>
                        <strong>Attention!</strong> Vous avez <strong>$total produit" . ($total > 1 ? 's' : '') . "</strong> avec un stock faible ou en rupture.
                    </div>
                    
                    <h3>Produits nécessitant votre attention:</h3>
                    
                    <table>
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th style='text-align: center;'>Stock</th>
                                <th>État</th>
                                <th>Prix</th>
                            </tr>
                        </thead>
                        <tbody>
                            $produitsHTML
                        </tbody>
                    </table>
                    
                    <p style='margin-top: 30px;'>
                        <a href='http://localhost/PeaceConnect/view/back/dashboard.html' 
                           style='display: inline-block; background: #4e73df; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: 600;'>
                            Voir le Dashboard
                        </a>
                    </p>
                </div>
                <div class='footer'>
                    <p>© 2025 PeaceConnect - Système de gestion des stocks</p>
                    <p style='font-size: 12px;'>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $altBody = "ALERTE DE STOCK - PeaceConnect\n\n";
        $altBody .= "Vous avez $total produit(s) avec un stock faible ou en rupture.\n\n";
        foreach ($produits as $p) {
            $altBody .= "- {$p['nom']}: {$p['stock']} en stock ({$p['etat_stock']})\n";
        }
        
        return [
            'subject' => "⚠️ Alerte Stock: $total produit" . ($total > 1 ? 's' : '') . " nécessite" . ($total > 1 ? 'nt' : '') . " votre attention",
            'body' => $body,
            'altBody' => $altBody
        ];
    }
    
    /**
     * Template: Confirmation de commande (Client)
     */
    private function templateOrderConfirmation($data) {
        $commande = $data['commande'] ?? [];
        $details = $data['details'] ?? [];
        $client = $data['client'] ?? [];
        
        $detailsHTML = '';
        foreach ($details as $d) {
            $detailsHTML .= "
                <tr>
                    <td style='padding: 12px;'>{$d['produit_nom']}</td>
                    <td style='padding: 12px; text-align: center;'>{$d['quantite']}</td>
                    <td style='padding: 12px; text-align: right;'>" . number_format($d['prix_unitaire'], 2) . " €</td>
                    <td style='padding: 12px; text-align: right; font-weight: 600;'>" . number_format($d['quantite'] * $d['prix_unitaire'], 2) . " €</td>
                </tr>
            ";
        }
        
        $body = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #1cc88a 0%, #17a673 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: white; padding: 30px; border: 1px solid #e3e6f0; }
                .footer { background: #f8f9fc; padding: 20px; text-align: center; border-radius: 0 0 8px 8px; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                th { background: #f8f9fc; padding: 12px; text-align: left; font-weight: 700; }
                .total-row { background: #f8f9fc; font-weight: 700; font-size: 1.1em; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1 style='margin: 0;'>✅ Commande Confirmée</h1>
                    <p style='margin: 10px 0 0 0;'>Merci pour votre commande!</p>
                </div>
                <div class='content'>
                    <p>Bonjour <strong>{$client['nom']}</strong>,</p>
                    <p>Nous avons bien reçu votre commande <strong>N°{$commande['id']}</strong>.</p>
                    
                    <h3>Détails de votre commande:</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th style='text-align: center;'>Quantité</th>
                                <th style='text-align: right;'>Prix unitaire</th>
                                <th style='text-align: right;'>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            $detailsHTML
                            <tr class='total-row'>
                                <td colspan='3' style='padding: 12px; text-align: right;'>Total:</td>
                                <td style='padding: 12px; text-align: right; color: #1cc88a;'>" . number_format($commande['total'], 2) . " €</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <p><strong>Statut:</strong> {$commande['statut']}</p>
                    <p><strong>Date:</strong> " . date('d/m/Y à H:i', strtotime($commande['date_commande'])) . "</p>
                    
                    <p style='margin-top: 30px;'>
                        <a href='http://localhost/PeaceConnect/view/front/suivi.html' 
                           style='display: inline-block; background: #1cc88a; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: 600;'>
                            Suivre ma commande
                        </a>
                    </p>
                </div>
                <div class='footer'>
                    <p>© 2025 PeaceConnect</p>
                    <p>Pour toute question: support@peaceconnect.org</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        return [
            'subject' => "✅ Confirmation de commande N°{$commande['id']} - PeaceConnect",
            'body' => $body,
            'altBody' => "Votre commande N°{$commande['id']} a été confirmée. Total: {$commande['total']} €"
        ];
    }
    
    /**
     * Template: Changement de statut commande
     */
    private function templateOrderStatus($data) {
        $commande = $data['commande'] ?? [];
        $client = $data['client'] ?? [];
        
        $statusColors = [
            'En attente' => '#f6c23e',
            'Confirmée' => '#1cc88a',
            'Expédiée' => '#36b9cc',
            'Livrée' => '#1cc88a',
            'Annulée' => '#e74a3b'
        ];
        
        $color = $statusColors[$commande['statut']] ?? '#4e73df';
        
        $body = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, $color 0%, $color 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: white; padding: 30px; border: 1px solid #e3e6f0; }
                .status-badge { display: inline-block; background: $color; color: white; padding: 8px 20px; border-radius: 20px; font-weight: 700; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1 style='margin: 0;'>📦 Mise à jour de commande</h1>
                </div>
                <div class='content'>
                    <p>Bonjour <strong>{$client['nom']}</strong>,</p>
                    <p>Le statut de votre commande <strong>N°{$commande['id']}</strong> a été mis à jour:</p>
                    
                    <p style='text-align: center; margin: 30px 0;'>
                        <span class='status-badge'>{$commande['statut']}</span>
                    </p>
                    
                    <p style='margin-top: 30px;'>
                        <a href='http://localhost/PeaceConnect/view/front/suivi.html' 
                           style='display: inline-block; background: $color; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-weight: 600;'>
                            Suivre ma commande
                        </a>
                    </p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        return [
            'subject' => "📦 Commande N°{$commande['id']}: {$commande['statut']}",
            'body' => $body,
            'altBody' => "Votre commande N°{$commande['id']} est maintenant: {$commande['statut']}"
        ];
    }
    
    /**
     * Envoyer une alerte de stock à l'admin
     * @param array $produits Liste des produits en alerte
     * @param string $adminEmail Email de l'admin
     * @return bool
     */
    public function sendStockAlertToAdmin($produits, $adminEmail) {
        return $this->sendTemplate($adminEmail, 'low_stock_admin', [
            'produits' => $produits,
            'total' => count($produits)
        ]);
    }
    
    /**
     * Logger les envois d'emails
     * @param string $status SUCCESS ou ERROR
     * @param string $to Destinataire
     * @param string $subject Sujet
     * @param string $error Message d'erreur (optionnel)
     */
    private function logEmail($status, $to, $subject, $error = '') {
        $logDir = __DIR__ . '/../logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $logFile = $logDir . '/emails_' . date('Y-m') . '.log';
        $timestamp = date('Y-m-d H:i:s');
        $logLine = "[$timestamp] $status | To: $to | Subject: $subject";
        
        if ($error) {
            $logLine .= " | Error: $error";
        }
        
        $logLine .= "\n";
        file_put_contents($logFile, $logLine, FILE_APPEND);
    }
}
?>
