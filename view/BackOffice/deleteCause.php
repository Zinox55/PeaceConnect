<?php
require_once __DIR__ . '/../../controller/CauseController.php';

// Check if ID is provided in URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_cause = intval($_GET['id']); // Convert to integer for security
    
    $controller = new CauseController();
    
    try {
        // Delete the cause
        $controller->deleteCause($id_cause);
        
        // Redirect back to table with success message
        header('Location: causesTables.php?deleted=success');
        exit;
        
    } catch (Exception $e) {
        // Redirect back with error message
        header('Location: causesTables.php?error=' . urlencode($e->getMessage()));
        exit;
    }
    
} else {
    // No ID provided, redirect back with error
    header('Location: causesTables.php?error=No cause ID provided');
    exit;
}
?>