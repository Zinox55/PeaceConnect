<?php
session_start();
if (!isset($_SESSION['e'])) {
    header('Location: signin.php');
    exit();
}

require_once __DIR__ . '/../../controller/DonController.php';
require_once __DIR__ . '/../../controller/CauseController.php';

// Check if donation ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: indexRanim.php?error=no_receipt');
    exit;
}

$donController = new DonController();
$causeController = new CauseController();

$id_don = intval($_GET['id']);
$donData = $donController->showDon($id_don);

if (!$donData) {
    header('Location: indexRanim.php?error=receipt_not_found');
    exit;
}

// Get cause information
$causeData = null;
if (!empty($donData['cause'])) {
    $causeData = $causeController->showCause($donData['cause']);
}

// Default if no cause
if (!$causeData) {
    $causeData = [
        'nom_cause' => 'General Donation',
        'description' => 'Thank you for your generous donation to our cause'
    ];
}

// Generate receipt number
$receipt_number = "RC-" . date('Y') . "-" . str_pad($id_don, 6, '0', STR_PAD_LEFT);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Receipt #<?= $receipt_number ?> - PeaceConnect</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .receipt-container {
            background: white;
            max-width: 800px;
            width: 100%;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        .receipt-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        
        .receipt-header h1 {
            font-size: 32px;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .receipt-header p {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .receipt-body {
            padding: 40px;
        }
        
        .receipt-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
            padding-bottom: 30px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .info-block h3 {
            color: #667eea;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .info-block p {
            color: #333;
            font-size: 16px;
            line-height: 1.6;
        }
        
        .donation-details {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid rgba(102, 126, 234, 0.2);
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            color: #555;
            font-weight: 400;
        }
        
        .detail-value {
            color: #333;
            font-weight: 700;
        }
        
        .amount-highlight {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin: 30px 0;
        }
        
        .amount-highlight h2 {
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 10px;
            opacity: 0.9;
        }
        
        .amount-highlight .amount {
            font-size: 48px;
            font-weight: 700;
        }
        
        .message-box {
            background: #fff9e6;
            border-left: 4px solid #ffc107;
            padding: 20px;
            border-radius: 5px;
            margin: 30px 0;
        }
        
        .message-box h4 {
            color: #f57c00;
            margin-bottom: 10px;
        }
        
        .message-box p {
            color: #666;
            line-height: 1.6;
            font-style: italic;
        }
        
        .thank-you {
            text-align: center;
            padding: 30px 0;
            color: #667eea;
        }
        
        .thank-you h2 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .thank-you p {
            color: #666;
            font-size: 16px;
        }
        
        .actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            padding: 30px;
            background: #f8f9fa;
        }
        
        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .btn-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }
        
        .btn-secondary:hover {
            background: #667eea;
            color: white;
        }
        
        .footer-info {
            background: #f8f9fa;
            padding: 20px 40px;
            text-align: center;
            color: #666;
            font-size: 14px;
            border-top: 1px solid #e0e0e0;
        }
        
        .verified-badge {
            display: inline-block;
            background: #4caf50;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 10px;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .actions {
                display: none !important;
            }
            
            .receipt-container {
                box-shadow: none;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container" id="receipt">
        <div class="receipt-header">
            <h1>🤝 PeaceConnect</h1>
            <p>Official Donation Receipt</p>
            <span class="verified-badge">✓ VERIFIED</span>
        </div>
        
        <div class="receipt-body">
            <div class="receipt-info">
                <div class="info-block">
                    <h3>Receipt Number</h3>
                    <p><?= htmlspecialchars($receipt_number) ?></p>
                </div>
                
                <div class="info-block">
                    <h3>Date of Donation</h3>
                    <p><?= date('F d, Y', strtotime($donData['date_don'])) ?></p>
                    <p style="font-size: 14px; color: #999;"><?= date('h:i A', strtotime($donData['date_don'])) ?></p>
                </div>
                
                <div class="info-block">
                    <h3>Donor Information</h3>
                    <p><strong><?= htmlspecialchars($donData['donateur_nom']) ?></strong></p>
                    <p style="font-size: 14px; color: #666;"><?= htmlspecialchars($donData['donateur_email']) ?></p>
                </div>
                
                <div class="info-block">
                    <h3>Cause Supported</h3>
                    <p><strong><?= htmlspecialchars($causeData['nom_cause']) ?></strong></p>
                </div>
            </div>
            
            <div class="amount-highlight">
                <h2>Total Donation Amount</h2>
                <div class="amount"><?= number_format($donData['montant'], 2) ?> <?= htmlspecialchars($donData['devise']) ?></div>
            </div>
            
            <div class="donation-details">
                <div class="detail-row">
                    <span class="detail-label">Donation ID</span>
                    <span class="detail-value">#<?= htmlspecialchars($donData['id_don']) ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Payment Method</span>
                    <span class="detail-value"><?= htmlspecialchars(ucfirst($donData['methode_paiement'])) ?></span>
                </div>
                
                <?php if (!empty($donData['transaction_id'])): ?>
                <div class="detail-row">
                    <span class="detail-label">Transaction ID</span>
                    <span class="detail-value"><?= htmlspecialchars($donData['transaction_id']) ?></span>
                </div>
                <?php endif; ?>
                
                <div class="detail-row">
                    <span class="detail-label">Currency</span>
                    <span class="detail-value"><?= htmlspecialchars($donData['devise']) ?></span>
                </div>
            </div>
            
            <?php if (!empty($donData['message'])): ?>
            <div class="message-box">
                <h4>📝 Your Message</h4>
                <p>"<?= htmlspecialchars($donData['message']) ?>"</p>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($causeData['description'])): ?>
            <div style="background: #e8f5e9; padding: 20px; border-radius: 10px; margin: 20px 0;">
                <h4 style="color: #2e7d32; margin-bottom: 10px;">💚 About This Cause</h4>
                <p style="color: #555; line-height: 1.6;"><?= htmlspecialchars($causeData['description']) ?></p>
            </div>
            <?php endif; ?>
            
            <div class="thank-you">
                <h2>Thank You for Your Generosity! 🙏</h2>
                <p>Your contribution makes a real difference in people's lives.</p>
            </div>
        </div>
        
        <div class="footer-info">
            <p><strong>PeaceConnect</strong> - 21 Rue el baten, el ghazela, Ariana 2080</p>
            <p>Email: info@PeaceConnect.com | Phone: +216 97 254 985</p>
            <p style="margin-top: 10px; font-size: 12px;">This receipt is valid for tax purposes. Please keep it for your records.</p>
        </div>
        
        <div class="actions">
            <a href="exportReceiptPDF.php?id=<?= $id_don ?>" class="btn btn-primary" target="_blank">
                📄 Download PDF
            </a>
            <a href="indexRanim.php" class="btn btn-secondary">
                🏠 Back to Home
            </a>
        </div>
    </div>
</body>
</html>