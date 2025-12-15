<?php
class StatsModel {
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
    
    public function getTopEvents($limit = 5) {
        $query = "
            SELECT 
                e.titre,
                e.id,
                COUNT(i.id) as nb_inscriptions
            FROM events e
            LEFT JOIN inscriptions i ON e.titre = i.evenement
            GROUP BY e.id, e.titre
            ORDER BY nb_inscriptions DESC
            LIMIT :limit
        ";
        
        $stmt = $this->safePrepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getInscriptionsByMonth() {
        $query = "
            SELECT 
                DATE_FORMAT(date_inscription, '%Y-%m') as mois,
                COUNT(*) as nb_inscriptions
            FROM inscriptions
            WHERE date_inscription >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(date_inscription, '%Y-%m')
            ORDER BY mois ASC
        ";
        
        $stmt = $this->safePrepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getGeneralStats() {
        $query = "
            SELECT 
                (SELECT COUNT(*) FROM events) as total_events,
                (SELECT COUNT(*) FROM inscriptions) as total_inscriptions,
                (SELECT COUNT(*) FROM events WHERE date_event >= NOW()) as events_a_venir,
                (SELECT AVG(nb_inscriptions) FROM (
                    SELECT COUNT(*) as nb_inscriptions 
                    FROM inscriptions 
                    GROUP BY evenement
                ) as subquery) as moyenne_inscriptions_par_event
        ";
        
        $stmt = $this->safePrepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getRecentActivity() {
        $query = "
            SELECT 
                'inscription' as type,
                nom,
                evenement,
                date_inscription as date
            FROM inscriptions
            UNION ALL
            SELECT 
                'event' as type,
                titre as nom,
                lieu as evenement, 
                date_event as date
            FROM events
            ORDER BY date DESC
            LIMIT 10
        ";
        
        $stmt = $this->safePrepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>