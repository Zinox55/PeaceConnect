<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../controller/DonController.php';
require_once __DIR__ . '/../../model/don.php';

$error = "";
$success = "";
$donC = new DonController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Validate required fields
    if (
        !empty($_POST["montant"]) &&
        !empty($_POST["donateur_nom"]) &&
        !empty($_POST["donateur_email"]) &&
        !empty($_POST["methode_paiement"])
    ) {
        try {
            // Create Don object with all fields from the form
            $don = new Don(
                null, // id_don (auto-increment)
                floatval($_POST["montant"]),
                !empty($_POST["devise"]) ? $_POST["devise"] : 'DT',
                !empty($_POST["date_don"]) ? new DateTime($_POST["date_don"]) : new DateTime(),
                $_POST["donateur_nom"],
                !empty($_POST["message"]) ? $_POST["message"] : '',
                $_POST["methode_paiement"],
                !empty($_POST["transaction_id"]) ? $_POST["transaction_id"] : null,
                $_POST["donateur_email"]
            );

            // Add donation to database
            $result = $donC->addDon($don);

            if ($result) {
                // Redirect back to tables with success message
                header('Location: tables.php?added=success&donor=' . urlencode($_POST["donateur_nom"]));
                exit;
            } else {
                // Redirect back with error
                header('Location: tables.php?error=Failed to add donation');
                exit;
            }

        } catch (Exception $e) {
            // Redirect back with error
            header('Location: tables.php?error=' . urlencode($e->getMessage()));
            exit;
        }
    } else {
        // Redirect back with validation error
        header('Location: tables.php?error=Please fill in all required fields');
        exit;
    }
} else {
    // Not a POST request, redirect to tables
    header('Location: tables.php');
    exit;
}
?>