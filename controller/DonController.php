<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../model/don.php';


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
    // Remove NULL from VALUES and fix column list
    $sql = "INSERT INTO don (montant, devise, date_don, donateur_nom, message, methode_paiement, transaction_id, donateur_email, cause) 
            VALUES (:montant, :devise, :date_don, :donateur_nom, :message, :methode_paiement, :transaction_id, :donateur_email, :cause)";
    
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
            'donateur_email' => $don->getDonateurEmail(), 
            'cause'         => $don->getCause()
        ]);
        
        // Get the last inserted ID for receipt display
        $donId = $db->lastInsertId();
        
        // Send email notifications
        $this->sendDonationNotificationEmail($don, $donId);
        $this->sendDonationReceiptEmail($don, $donId);
        
        return true;
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
        return false;
    }
}

    /**
     * Send notification email after a donation is made
     */
    public function sendDonationNotificationEmail($don, $donId) {
        try {
            // Get cause information
            require_once __DIR__ . '/CauseController.php';
            $causeController = new CauseController();
            $cause = $causeController->showCause($don->getCause());
            $causeName = $cause ? $cause['nom_cause'] : 'Unknown Cause';
            
            // Email configuration
            $to = "ghribiranim6@gmail.com";
            $subject = "New Donation Received - PeaceConnect";
            
            // Email content
            $message = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #f9f9f9; }
                    .header { background: #4e73df; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                    .content { background: white; padding: 30px; border-radius: 0 0 5px 5px; }
                    .donation-details { background: #f8f9fc; padding: 15px; margin: 20px 0; border-left: 4px solid #4e73df; }
                    .amount { font-size: 24px; color: #1cc88a; font-weight: bold; }
                    .footer { text-align: center; margin-top: 20px; color: #888; font-size: 12px; }
                    table { width: 100%; border-collapse: collapse; }
                    td { padding: 8px 0; }
                    .label { font-weight: bold; width: 150px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>🎉 New Donation Received!</h1>
                    </div>
                    <div class='content'>
                        <p>Hello Admin,</p>
                        <p>A new donation has been made on PeaceConnect platform.</p>
                        
                        <div class='donation-details'>
                            <h3>Donation Details:</h3>
                            <table>
                                <tr>
                                    <td class='label'>Donation ID:</td>
                                    <td>#" . $donId . "</td>
                                </tr>
                                <tr>
                                    <td class='label'>Donor Name:</td>
                                    <td>" . htmlspecialchars($don->getDonateurNom()) . "</td>
                                </tr>
                                <tr>
                                    <td class='label'>Donor Email:</td>
                                    <td>" . htmlspecialchars($don->getDonateurEmail()) . "</td>
                                </tr>
                                <tr>
                                    <td class='label'>Amount:</td>
                                    <td class='amount'>" . number_format($don->getMontant(), 2) . " " . strtoupper($don->getDevise()) . "</td>
                                </tr>
                                <tr>
                                    <td class='label'>Cause:</td>
                                    <td>" . htmlspecialchars($causeName) . "</td>
                                </tr>
                                <tr>
                                    <td class='label'>Payment Method:</td>
                                    <td>" . ucfirst($don->getMethodePaiement()) . "</td>
                                </tr>
                                <tr>
                                    <td class='label'>Date:</td>
                                    <td>" . ($don->getDateDon() ? $don->getDateDon()->format('F d, Y H:i:s') : date('F d, Y H:i:s')) . "</td>
                                </tr>
                            </table>
                            
                            " . ($don->getMessage() ? "<p><strong>Message:</strong><br><em>\"" . htmlspecialchars($don->getMessage()) . "\"</em></p>" : "") . "
                        </div>
                        
                        <p>You can view and manage this donation in your dashboard.</p>
                        
                        <p style='text-align: center; margin-top: 30px;'>
                            <a href='http://localhost/PeaceConnectr/PeaceConnect/view/BackOffice/tables.php' 
                               style='background: #4e73df; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                                View in Dashboard
                            </a>
                        </p>
                    </div>
                    <div class='footer'>
                        <p>This is an automated notification from PeaceConnect</p>
                        <p>&copy; " . date('Y') . " PeaceConnect. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
            ";
            
            // Headers for HTML email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: PeaceConnect <noreply@peaceconnect.org>" . "\r\n";
            $headers .= "Reply-To: noreply@peaceconnect.org" . "\r\n";
            
            // Send email
            $emailSent = mail($to, $subject, $message, $headers);
            
            return $emailSent;
            
        } catch (Exception $e) {
            // Log error but don't stop the donation process
            error_log("Email notification failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send donation receipt email to donor with PDF attachment
     */
    public function sendDonationReceiptEmail($don, $donId) {
        try {
            // Get cause information
            require_once __DIR__ . '/CauseController.php';
            $causeController = new CauseController();
            $cause = $causeController->showCause($don->getCause());
            $causeName = $cause ? $cause['nom_cause'] : 'Unknown Cause';
            
            // Generate PDF receipt
            $pdfContent = $this->generateReceiptPDF($don, $donId, $causeName, true);
            
            if (!$pdfContent) {
                error_log("Failed to generate PDF for donation #" . $donId);
                return false;
            }
            
            // Email configuration
            $to = $don->getDonateurEmail();
            $from = "noreply@peaceconnect.org";
            $fromName = "PeaceConnect";
            $subject = "Thank You for Your Donation - Receipt #" . $donId;
            
            // Create boundary for multipart email
            $boundary = md5(time());
            
            // Headers
            $headers = "From: " . $fromName . " <" . $from . ">" . "\r\n";
            $headers .= "Reply-To: " . $from . "\r\n";
            $headers .= "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-Type: multipart/mixed; boundary=\"" . $boundary . "\"" . "\r\n";
            
            // HTML Email body
            $htmlMessage = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #f9f9f9; }
                    .header { background: #28a745; color: white; padding: 30px; text-align: center; border-radius: 5px 5px 0 0; }
                    .content { background: white; padding: 30px; border-radius: 0 0 5px 5px; }
                    .donation-box { background: #f8f9fc; padding: 20px; margin: 20px 0; border-left: 4px solid #28a745; border-radius: 5px; }
                    .amount { font-size: 32px; color: #28a745; font-weight: bold; text-align: center; margin: 20px 0; }
                    .thank-you { font-size: 24px; color: #4e73df; text-align: center; margin: 20px 0; }
                    .footer { text-align: center; margin-top: 30px; color: #888; font-size: 12px; }
                    .btn { display: inline-block; background: #4e73df; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                    table { width: 100%; }
                    td { padding: 8px 0; }
                    .label { font-weight: bold; color: #666; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>🙏 Thank You for Your Generous Donation!</h1>
                    </div>
                    <div class='content'>
                        <p class='thank-you'>Dear " . htmlspecialchars($don->getDonateurNom()) . ",</p>
                        
                        <p>Thank you for your generous contribution to <strong>" . htmlspecialchars($causeName) . "</strong>. Your support makes a real difference!</p>
                        
                        <div class='amount'>
                            " . number_format($don->getMontant(), 2) . " " . strtoupper($don->getDevise()) . "
                        </div>
                        
                        <div class='donation-box'>
                            <h3 style='color: #4e73df; margin-top: 0;'>Donation Summary</h3>
                            <table>
                                <tr>
                                    <td class='label'>Receipt Number:</td>
                                    <td>#" . $donId . "</td>
                                </tr>
                                <tr>
                                    <td class='label'>Date:</td>
                                    <td>" . ($don->getDateDon() ? $don->getDateDon()->format('F d, Y H:i:s') : date('F d, Y H:i:s')) . "</td>
                                </tr>
                                <tr>
                                    <td class='label'>Cause:</td>
                                    <td>" . htmlspecialchars($causeName) . "</td>
                                </tr>
                                <tr>
                                    <td class='label'>Payment Method:</td>
                                    <td>" . ucfirst(str_replace('_', ' ', $don->getMethodePaiement())) . "</td>
                                </tr>
                            </table>
                        </div>
                        
                        " . ($don->getMessage() ? "<p><em>\"" . htmlspecialchars($don->getMessage()) . "\"</em></p>" : "") . "
                        
                        <p style='margin-top: 30px;'><strong>📎 Your receipt is attached to this email as a PDF.</strong></p>
                        
                        <p>Please keep this receipt for your records. If you have any questions, feel free to contact us.</p>
                        
                        <p style='margin-top: 40px;'>With gratitude,<br><strong>The PeaceConnect Team</strong></p>
                    </div>
                    <div class='footer'>
                        <p>This is an automated receipt from PeaceConnect</p>
                        <p>&copy; " . date('Y') . " PeaceConnect. All rights reserved.</p>
                        <p style='margin-top: 10px;'>
                            <a href='http://localhost/PeaceConnectr/PeaceConnect/view/FrontOffice/indexRanim.php' style='color: #4e73df;'>Make Another Donation</a>
                        </p>
                    </div>
                </div>
            </body>
            </html>
            ";
            
            // Create email body
            $body = "--" . $boundary . "\r\n";
            $body .= "Content-Type: text/html; charset=UTF-8" . "\r\n";
            $body .= "Content-Transfer-Encoding: 7bit" . "\r\n\r\n";
            $body .= $htmlMessage . "\r\n\r\n";
            
            // Attach PDF
            $body .= "--" . $boundary . "\r\n";
            $body .= "Content-Type: application/pdf; name=\"donation_receipt_" . $donId . ".pdf\"" . "\r\n";
            $body .= "Content-Transfer-Encoding: base64" . "\r\n";
            $body .= "Content-Disposition: attachment; filename=\"donation_receipt_" . $donId . ".pdf\"" . "\r\n\r\n";
            $body .= chunk_split(base64_encode($pdfContent)) . "\r\n";
            $body .= "--" . $boundary . "--";
            
            // Send email
            $emailSent = mail($to, $subject, $body, $headers);
            
            if ($emailSent) {
                error_log("Receipt email sent successfully to " . $to . " for donation #" . $donId);
            } else {
                error_log("Failed to send receipt email to " . $to . " for donation #" . $donId);
            }
            
            return $emailSent;
            
        } catch (Exception $e) {
            error_log("Receipt email failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate PDF receipt and return content as string
     */
    private function generateReceiptPDF($don, $donId, $causeName, $returnString = false) {
        try {
            require_once __DIR__ . '/vendor/tcpdf/tcpdf.php';
            
            // Create PDF
            $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            
            // Set document information
            $pdf->SetCreator('PeaceConnect');
            $pdf->SetAuthor('PeaceConnect');
            $pdf->SetTitle('Donation Receipt #' . $donId);
            
            // Remove default header/footer
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            
            // Add a page
            $pdf->AddPage();
            
            // Set font
            $pdf->SetFont('helvetica', '', 10);
            
            // Header with logo space
            $pdf->SetFillColor(78, 115, 223);
            $pdf->Rect(0, 0, 210, 40, 'F');
            
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('helvetica', 'B', 24);
            $pdf->SetXY(15, 15);
            $pdf->Cell(0, 10, 'PeaceConnect', 0, 1, 'L');
            
            $pdf->SetFont('helvetica', '', 12);
            $pdf->SetXY(15, 25);
            $pdf->Cell(0, 8, 'Donation Receipt', 0, 1, 'L');
            
            // Receipt number and date
            $pdf->SetTextColor(78, 115, 223);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->SetXY(140, 15);
            $pdf->Cell(0, 5, 'Receipt #' . $donId, 0, 1, 'L');
            
            $pdf->SetTextColor(100, 100, 100);
            $pdf->SetFont('helvetica', '', 9);
            $pdf->SetXY(140, 22);
            $date = $don->getDateDon() ? $don->getDateDon()->format('F d, Y') : date('F d, Y');
            $pdf->Cell(0, 5, $date, 0, 1, 'L');
            
            // Donor Information Box
            $pdf->SetY(50);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(0, 8, 'Donor Information', 0, 1, 'L');
            
            $pdf->SetFillColor(248, 249, 252);
            $pdf->SetDrawColor(78, 115, 223);
            $pdf->SetLineWidth(0.5);
            $pdf->RoundedRect(15, $pdf->GetY(), 180, 25, 3, '1111', 'DF');
            
            $pdf->SetY($pdf->GetY() + 5);
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Cell(40, 6, 'Name:', 0, 0, 'L');
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(0, 6, $don->getDonateurNom(), 0, 1, 'L');
            
            $pdf->SetFont('helvetica', '', 10);
            $pdf->Cell(40, 6, 'Email:', 0, 0, 'L');
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(0, 6, $don->getDonateurEmail(), 0, 1, 'L');
            
            // Donation Details Box
            $pdf->SetY($pdf->GetY() + 10);
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(0, 8, 'Donation Details', 0, 1, 'L');
            
            $pdf->SetFillColor(248, 249, 252);
            $pdf->RoundedRect(15, $pdf->GetY(), 180, 45, 3, '1111', 'DF');
            
            $pdf->SetY($pdf->GetY() + 5);
            $pdf->SetFont('helvetica', '', 10);
            
            $details = [
                ['Cause:', $causeName],
                ['Amount:', number_format($don->getMontant(), 2) . ' ' . strtoupper($don->getDevise())],
                ['Payment Method:', ucfirst(str_replace('_', ' ', $don->getMethodePaiement()))],
                ['Transaction Date:', $don->getDateDon() ? $don->getDateDon()->format('F d, Y H:i:s') : date('F d, Y H:i:s')]
            ];
            
            foreach ($details as $detail) {
                $pdf->Cell(50, 6, $detail[0], 0, 0, 'L');
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(0, 6, $detail[1], 0, 1, 'L');
                $pdf->SetFont('helvetica', '', 10);
            }
            
            // Amount Highlight Box
            $pdf->SetY($pdf->GetY() + 10);
            $pdf->SetFillColor(40, 167, 69);
            $pdf->RoundedRect(15, $pdf->GetY(), 180, 20, 3, '1111', 'F');
            
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('helvetica', 'B', 16);
            $pdf->SetY($pdf->GetY() + 7);
            $pdf->Cell(0, 8, 'Total Donated: ' . number_format($don->getMontant(), 2) . ' ' . strtoupper($don->getDevise()), 0, 1, 'C');
            
            // Message if exists
            if ($don->getMessage()) {
                $pdf->SetY($pdf->GetY() + 10);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetFont('helvetica', 'B', 11);
                $pdf->Cell(0, 6, 'Your Message:', 0, 1, 'L');
                
                $pdf->SetFont('helvetica', 'I', 10);
                $pdf->SetTextColor(100, 100, 100);
                $pdf->MultiCell(180, 5, '"' . $don->getMessage() . '"', 0, 'L');
            }
            
            // Thank you message
            $pdf->SetY($pdf->GetY() + 15);
            $pdf->SetTextColor(78, 115, 223);
            $pdf->SetFont('helvetica', 'B', 14);
            $pdf->Cell(0, 8, 'Thank You for Your Generosity!', 0, 1, 'C');
            
            $pdf->SetTextColor(100, 100, 100);
            $pdf->SetFont('helvetica', '', 9);
            $pdf->MultiCell(0, 5, 'Your contribution makes a real difference. We are grateful for your support.', 0, 'C');
            
            // Footer
            $pdf->SetY(270);
            $pdf->SetDrawColor(200, 200, 200);
            $pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
            
            $pdf->SetY($pdf->GetY() + 3);
            $pdf->SetFont('helvetica', '', 8);
            $pdf->SetTextColor(150, 150, 150);
            $pdf->Cell(0, 4, 'PeaceConnect - Making a Difference Together', 0, 1, 'C');
            $pdf->Cell(0, 4, 'This is an official receipt for your donation. Please keep it for your records.', 0, 1, 'C');
            $pdf->Cell(0, 4, 'Generated on ' . date('F d, Y H:i:s'), 0, 1, 'C');
            
            // Return PDF as string or output
            if ($returnString) {
                return $pdf->Output('donation_receipt_' . $donId . '.pdf', 'S');
            } else {
                $pdf->Output('donation_receipt_' . $donId . '.pdf', 'D');
            }
            
        } catch (Exception $e) {
            error_log("PDF generation failed: " . $e->getMessage());
            return false;
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
                    donateur_email = :donateur_email,
                    cause = :cause
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
                'donateur_email' => $don->getDonateurEmail(),
                'cause'           => $don->getCause() //fama hkeya linna
            ]);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function showDon($id_don) {
        $sql = "SELECT * FROM don WHERE id_don = :id_don";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->bindValue(':id_don', $id_don);
        try {
            $query->execute();
            $don = $query->fetch();
            return $don;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    public function exportReceiptPDF($id_don) {
        require_once __DIR__ . '/vendor/tcpdf/tcpdf.php';
        require_once __DIR__ . '/CauseController.php';
        
        // Get donation data
        $donData = $this->showDon($id_don);
        if (!$donData) {
            die('Error: Donation not found');
        }
        
        // Get cause data
        $causeController = new CauseController();
        $causeData = null;
        if (!empty($donData['cause'])) {
            $causeData = $causeController->showCause($donData['cause']);
        }
        
        // Default cause if not found
        if (!$causeData) {
            $causeData = ['nom_cause' => 'General Donation', 'description' => 'Thank you for your support'];
        }
        
        // Generate receipt number
        $receipt_number = "RC-" . date('Y') . "-" . str_pad($id_don, 6, '0', STR_PAD_LEFT);
        
        // Create PDF
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        
        // Set document info
        $pdf->SetCreator('PeaceConnect');
        $pdf->SetAuthor('PeaceConnect');
        $pdf->SetTitle('Donation Receipt - ' . $receipt_number);
        $pdf->SetSubject('Donation Receipt');
        
        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // Set margins
        $pdf->SetMargins(20, 20, 20);
        $pdf->SetAutoPageBreak(TRUE, 20);
        
        // Add page
        $pdf->AddPage();
        
        // ===== HEADER SECTION =====
        $pdf->SetFillColor(102, 126, 234);
        $pdf->Rect(0, 0, 210, 40, 'F');
        
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('helvetica', 'B', 28);
        $pdf->SetY(12);
        $pdf->Cell(0, 10, 'PeaceConnect', 0, 1, 'C', false);
        
        $pdf->SetFont('helvetica', '', 13);
        $pdf->Cell(0, 8, 'Donation Receipt', 0, 1, 'C', false);
        
        $pdf->Ln(8);
        
        // ===== RECEIPT INFO BOX =====
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(245, 247, 250);
        $pdf->RoundedRect(20, $pdf->GetY(), 170, 20, 3, '1111', 'F');
        
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->Cell(85, 10, 'Receipt Number: ' . $receipt_number, 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(85, 10, 'Date: ' . date('M d, Y', strtotime($donData['date_don'])), 0, 1, 'R');
        
        $pdf->Ln(15);
        
        // ===== DONOR INFORMATION =====
        $pdf->SetFont('helvetica', 'B', 13);
        $pdf->SetTextColor(102, 126, 234);
        $pdf->Cell(0, 8, 'Donor Information', 0, 1, 'L');
        
        $pdf->SetDrawColor(102, 126, 234);
        $pdf->SetLineWidth(0.5);
        $pdf->Line(20, $pdf->GetY(), 190, $pdf->GetY());
        $pdf->Ln(3);
        
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 11);
        
        // Name row
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(40, 7, 'Name:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(0, 7, $donData['donateur_nom'], 0, 1, 'L');
        
        // Email row
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(40, 7, 'Email:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(0, 7, $donData['donateur_email'], 0, 1, 'L');
        
        $pdf->Ln(8);
        
        // ===== DONATION DETAILS =====
        $pdf->SetFont('helvetica', 'B', 13);
        $pdf->SetTextColor(102, 126, 234);
        $pdf->Cell(0, 8, 'Donation Details', 0, 1, 'L');
        
        $pdf->SetDrawColor(102, 126, 234);
        $pdf->Line(20, $pdf->GetY(), 190, $pdf->GetY());
        $pdf->Ln(3);
        
        $pdf->SetTextColor(0, 0, 0);
        
        // Cause
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(40, 7, 'Cause:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(0, 7, $causeData['nom_cause'], 0, 1, 'L');
        
        // Payment Method
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(40, 7, 'Payment Method:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(0, 7, ucfirst($donData['methode_paiement']), 0, 1, 'L');
        
        // Transaction ID (if exists)
        if (!empty($donData['transaction_id'])) {
            $pdf->SetFont('helvetica', 'B', 11);
            $pdf->Cell(40, 7, 'Transaction ID:', 0, 0, 'L');
            $pdf->SetFont('helvetica', '', 11);
            $pdf->Cell(0, 7, $donData['transaction_id'], 0, 1, 'L');
        }
        
        // Message (if exists)
        if (!empty($donData['message'])) {
            $pdf->SetFont('helvetica', 'B', 11);
            $pdf->Cell(40, 7, 'Message:', 0, 0, 'L');
            $pdf->SetFont('helvetica', '', 11);
            $pdf->MultiCell(0, 7, $donData['message'], 0, 'L');
        }
        
        $pdf->Ln(10);
        
        // ===== AMOUNT HIGHLIGHT BOX =====
        $yPos = $pdf->GetY();
        $pdf->SetFillColor(102, 126, 234);
        $pdf->RoundedRect(40, $yPos, 130, 35, 5, '1111', 'F');
        
        $pdf->SetY($yPos + 5);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 8, 'TOTAL DONATION AMOUNT', 0, 1, 'C');
        
        $pdf->SetFont('helvetica', 'B', 24);
        $pdf->Cell(0, 15, number_format($donData['montant'], 2) . ' ' . strtoupper($donData['devise']), 0, 1, 'C');
        
        $pdf->Ln(12);
        
        // ===== THANK YOU MESSAGE =====
        $pdf->SetTextColor(80, 80, 80);
        $pdf->SetFont('helvetica', 'I', 10);
        $pdf->MultiCell(0, 5, 'Thank you for your generous donation! Your contribution helps us make a positive impact in our community. This receipt serves as confirmation of your donation and may be used for tax purposes.', 0, 'C');
        
        $pdf->Ln(10);
        
        // ===== FOOTER =====
        $pdf->SetY(-35);
        $pdf->SetDrawColor(200, 200, 200);
        $pdf->Line(20, $pdf->GetY(), 190, $pdf->GetY());
        $pdf->Ln(3);
        
        $pdf->SetTextColor(100, 100, 100);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 5, 'PeaceConnect - Making a Difference Together', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(0, 5, '21 Rue el baten, el ghazela, Ariana 2080', 0, 1, 'C');
        $pdf->Cell(0, 5, 'Email: info@PeaceConnect.com | Phone: +216 97 254 985', 0, 1, 'C');
        
        // Output PDF
        $pdf->Output('Receipt_' . $receipt_number . '.pdf', 'D');
        exit;
    }
}
?>
