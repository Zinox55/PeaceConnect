<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../controller/CauseController.php';
require_once __DIR__ . '/../../model/cause.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Validate required field
    if (!empty($_POST["nom_cause"])) {
        try {
            // Create Cause object
            $cause = new Cause(
                null, // id_cause (auto-increment)
                $_POST["nom_cause"],
                !empty($_POST["description"]) ? $_POST["description"] : null
            );

            // Add cause to database
            $controller = new CauseController();
            $result = $controller->addCause($cause);

            if ($result) {
                // Redirect back with success message
                header('Location: causesTables.php?added=success&cause=' . urlencode($_POST["nom_cause"]));
                exit;
            } else {
                // Redirect back with error
                header('Location: causesTables.php?error=Failed to add cause');
                exit;
            }

        } catch (Exception $e) {
            // Redirect back with error
            header('Location: causesTables.php?error=' . urlencode($e->getMessage()));
            exit;
        }
    } else {
        // Redirect back with validation error
        header('Location: causesTables.php?error=Cause name is required');
        exit;
    }
} else {
    // Not a POST request, redirect to table
    header('Location: causesTables.php');
    exit;
}
?>