<?php
require_once __DIR__ . '/../../controller/DonController.php';

// Check if ID is provided
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_don = $_GET['id'];
    
    $controller = new DonController();
    
    try {
        // Delete the donation
        $controller->deleteDon($id_don);
        
        // Redirect back to tables page with success message
        header('Location: tables.php?deleted=success');
        exit;
        
    } catch (Exception $e) {
        // Redirect back with error message
        header('Location: tables.php?error=' . urlencode($e->getMessage()));
        exit;
    }
    
} else {
    // No ID provided, redirect back
    header('Location: tables.php?error=no_id');
    exit;
}
?>