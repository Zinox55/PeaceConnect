<?php
// Prevent any output before PDF generation
ob_start();

require_once __DIR__ . '/../../controller/DonController.php';

// Check if donation ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: tables.php?error=no_id');
    exit;
}

$donController = new DonController();
$id_don = intval($_GET['id']);

// Clean any output buffer before generating PDF
ob_end_clean();

// Generate and download PDF
$donController->exportReceiptPDF($id_don);
