<?php
include(__DIR__ . '/../config.php');
include(__DIR__ . '/../model/cause.php');

class CauseController {

    public function listCauses() {
        $sql = "SELECT * FROM cause";
        $db = config::getConnexion();
        try {
            $list = $db->query($sql);
            return $list;
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function deleteCause($id_cause) {
        $sql = "DELETE FROM cause WHERE id_cause = :id_cause";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id_cause', $id_cause);
        try {
            $req->execute();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function addCause(Cause $cause) {
        $sql = "INSERT INTO cause (nom_cause, description) 
                VALUES (:nom_cause, :description)";
        
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'nom_cause' => $cause->getNomCause(),
                'description' => $cause->getDescription()
            ]);
            return true;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }

    public function updateCause(Cause $cause, $id_cause) {
        try {
            $db = config::getConnexion();
            $query = $db->prepare(
                'UPDATE cause SET 
                    nom_cause = :nom_cause,
                    description = :description
                WHERE id_cause = :id_cause'
            );
            $query->execute([
                'id_cause' => $id_cause,
                'nom_cause' => $cause->getNomCause(),
                'description' => $cause->getDescription()
            ]);
            return true;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function showCause($id_cause) {
        $sql = "SELECT * FROM cause WHERE id_cause = :id_cause";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->bindValue(':id_cause', $id_cause);
        try {
            $query->execute();
            $cause = $query->fetch();
            return $cause;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }
}
?>