<?php
include(__DIR__ . '/../config.php');
include(__DIR__ . '/../Model/Don.php');

class DonController {

    public function listDons() {
        $sql = "SELECT * FROM don";
        $db = config::getConnexion();
        try {
            $list = $db->query($sql);
            return $list;
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function deleteDon($id_don) {
        $sql = "DELETE FROM don WHERE id_don = :id_don";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id_don', $id_don);
        try {
            $req->execute();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function addDon(Don $don) {
        $sql = "INSERT INTO don VALUES (
            NULL, :montant, :devise, :date_don, :donateur_nom, :message, :methode_paiement, :transaction_id, :donateur_email
        )";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'montant' => $don->getMontant(),
                'devise' => $don->getDevise(),
                'date_don' => $don->getDateDon() ? $don->getDateDon()->format('Y-m-d H:i:s') : null,
                'donateur_nom' => $don->getDonateurNom(),
                'message' => $don->getMessage(),
                'methode_paiement' => $don->getMethodePaiement(),
                'transaction_id' => $don->getTransactionId(),
                'donateur_email' => $don->getDonateurEmail()
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function updateDon(Don $don, $id_don) {
        try {
            $db = config::getConnexion();
            $query = $db->prepare(
                'UPDATE don SET 
                    montant = :montant,
                    devise = :devise,
                    date_don = :date_don,
                    donateur_nom = :donateur_nom,
                    message = :message,
                    methode_paiement = :methode_paiement,
                    transaction_id = :transaction_id,
                    donateur_email = :donateur_email
                WHERE id_don = :id_don'
            );
            $query->execute([
                'id_don' => $id_don,
                'montant' => $don->getMontant(),
                'devise' => $don->getDevise(),
                'date_don' => $don->getDateDon() ? $don->getDateDon()->format('Y-m-d H:i:s') : null,
                'donateur_nom' => $don->getDonateurNom(),
                'message' => $don->getMessage(),
                'methode_paiement' => $don->getMethodePaiement(),
                'transaction_id' => $don->getTransactionId(),
                'donateur_email' => $don->getDonateurEmail()
            ]);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function showDon($id_don) {
        $sql = "SELECT * FROM don WHERE id_don = $id_don";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        try {
            $query->execute();
            $don = $query->fetch();
            return $don;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }
}
?>
