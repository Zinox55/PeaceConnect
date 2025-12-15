<?php
class InscriptionModel {
    private $pdo;
    
    public function __construct() {
        try {
            require_once __DIR__ . '/../config.php';
            $this->pdo = \config::getConnexion();
            if (!$this->pdo) {
                throw new Exception('Database connection not established.');
            }
        } catch (Exception $e) {
            throw new Exception("Erreur de connexion : " . $e->getMessage());
        }
    }

    private function safePrepare($query) {
        $stmt = $this->pdo->prepare($query);
        if ($stmt === false) {
            $err = $this->pdo->errorInfo();
            $message = isset($err[2]) ? $err[2] : 'Unknown PDO prepare error';
            throw new Exception('Failed to prepare statement: ' . $message);
        }
        return $stmt;
    }

    /**
     * Générer un token unique pour la vérification
     */
    private function generateToken() {
        return bin2hex(random_bytes(32));
    }

    /**
     * Créer une inscription avec token de vérification
     */
    public function createInscription($nom, $email, $telephone, $evenement) {
        $checkQuery = "SELECT id FROM inscriptions WHERE email = :email AND evenement = :evenement";
        $checkStmt = $this->safePrepare($checkQuery);
        $checkStmt->execute([':email' => $email, ':evenement' => $evenement]);
        
        if ($checkStmt->fetch()) {
            throw new Exception("Cette adresse email est déjà inscrite à cet événement.");
        }
        
        $token = $this->generateToken();
        $verified = 0; // Non vérifiée par défaut
        
        $query = "INSERT INTO inscriptions (nom, email, telephone, evenement, token, verified, date_inscription) VALUES (:nom, :email, :telephone, :evenement, :token, :verified, NOW())";
        $stmt = $this->safePrepare($query);
        $success = $stmt->execute([
            ':nom' => $nom,
            ':email' => $email,
            ':telephone' => $telephone,
            ':evenement' => $evenement,
            ':token' => $token,
            ':verified' => $verified
        ]);
        
        return $success ? $token : false;
    }

    /**
     * Vérifier une inscription via le token
     */
    public function verifyInscription($token) {
        // Vérifier que le token existe et n'est pas expiré (24h)
        $query = "SELECT id FROM inscriptions WHERE token = :token AND verified = 0 AND (NOW() < DATE_ADD(date_inscription, INTERVAL 24 HOUR))";
        $stmt = $this->safePrepare($query);
        $stmt->execute([':token' => $token]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            return false; // Token invalide ou expiré
        }
        
        // Marquer comme vérifiée
        $updateQuery = "UPDATE inscriptions SET verified = 1, token = NULL WHERE token = :token";
        $updateStmt = $this->safePrepare($updateQuery);
        return $updateStmt->execute([':token' => $token]);
    }

    /**
     * Récupérer une inscription par token
     */
    public function getInscriptionByToken($token) {
        $query = "SELECT * FROM inscriptions WHERE token = :token AND verified = 0";
        $stmt = $this->safePrepare($query);
        $stmt->execute([':token' => $token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getAllInscriptions() {
        $query = "SELECT * FROM inscriptions ORDER BY date_inscription DESC";
        $stmt = $this->safePrepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getInscriptionsByEvent($evenement) {
        $query = "SELECT * FROM inscriptions WHERE evenement = :evenement AND verified = 1 ORDER BY date_inscription DESC";
        $stmt = $this->safePrepare($query);
        $stmt->execute([':evenement' => $evenement]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getTotalInscriptions() {
        $query = "SELECT COUNT(*) as total FROM inscriptions WHERE verified = 1";
        $stmt = $this->safePrepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    
    public function getRecentInscriptions($limit = 10) {
        $query = "SELECT * FROM inscriptions WHERE verified = 1 AND date_inscription >= DATE_SUB(NOW(), INTERVAL 7 DAY) ORDER BY date_inscription DESC LIMIT :limit";
        $stmt = $this->safePrepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function deleteInscription($id) {
        $query = "DELETE FROM inscriptions WHERE id = :id";
        $stmt = $this->safePrepare($query);
        return $stmt->execute([':id' => $id]);
    }
    
    public function getInscriptionById($id) {
        $query = "SELECT * FROM inscriptions WHERE id = :id";
        $stmt = $this->safePrepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function updateInscription($id, $nom, $email, $telephone, $evenement) {
        $query = "UPDATE inscriptions SET nom = :nom, email = :email, telephone = :telephone, evenement = :evenement WHERE id = :id";
        $stmt = $this->safePrepare($query);
        return $stmt->execute([
            ':nom' => $nom,
            ':email' => $email,
            ':telephone' => $telephone,
            ':evenement' => $evenement,
            ':id' => $id
        ]);
    }
}
?>
