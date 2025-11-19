<?php
require_once '../model/InscriptionModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $evenement = trim($_POST['evenement'] ?? '');
    
    if (empty($nom) || empty($email) || empty($evenement)) {
        echo "error: Tous les champs obligatoires doivent être remplis.";
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "error: L'adresse email n'est pas valide.";
        exit;
    }
    
    if (!empty($telephone) && !preg_match('/^\d{8}$/', $telephone)) {
        echo "error: Le numéro de téléphone doit contenir 8 chiffres.";
        exit;
    }
    
    try {
        $model = new InscriptionModel();
        $result = $model->createInscription($nom, $email, $telephone, $evenement);
        
        if ($result) {
            echo "success";
        } else {
            echo "error: Une erreur est survenue lors de l'inscription.";
        }
        
    } catch (Exception $e) {
        echo "error: " . $e->getMessage();
    }
    
} else {
    echo "error: Méthode non autorisée.";
}
?>