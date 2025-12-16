<?php
/**
 * Classe simple pour générer des PDF sans dépendances
 * Utilise FPDF (inclus directement)
 */

// Télécharger FPDF si pas encore présent
if (!class_exists('FPDF')) {
    // Include FPDF library
    require_once __DIR__ . '/fpdf.php';
}

class ArticlePDFExporter extends FPDF {
    private $articleTitle;
    private $articleAuthor;
    private $articleDate;
    
    function __construct() {
        parent::__construct('P', 'mm', 'A4');
        $this->SetMargins(20, 20, 20);
        $this->SetAutoPageBreak(true, 20);
    }
    
    function setArticleInfo($title, $author, $date) {
        $this->articleTitle = $title;
        $this->articleAuthor = $author;
        $this->articleDate = $date;
    }
    
    // En-tête
    function Header() {
        // Logo/Titre PeaceConnect
        $this->SetFillColor(102, 126, 234);
        $this->Rect(0, 0, 210, 15, 'F');
        
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 20);
        $this->Cell(0, 15, 'PeaceConnect', 0, 1, 'C');
        
        $this->Ln(5);
    }
    
    // Pied de page
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(128, 128, 128);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . ' | PeaceConnect - ' . date('Y'), 0, 0, 'C');
    }
    
    // Titre de l'article
    function ArticleTitle($title) {
        $this->SetFont('Arial', 'B', 18);
        $this->SetTextColor(51, 51, 51);
        $this->MultiCell(0, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $title), 0, 'L');
        $this->Ln(3);
    }
    
    // Métadonnées de l'article
    function ArticleMeta($author, $date) {
        $this->SetFont('Arial', 'I', 10);
        $this->SetTextColor(100, 100, 100);
        
        $metaText = 'Par ' . $author . ' | Publie le ' . $date;
        $this->Cell(0, 6, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $metaText), 0, 1, 'L');
        
        // Ligne de séparation
        $this->SetDrawColor(200, 200, 200);
        $this->Line(20, $this->GetY() + 2, 190, $this->GetY() + 2);
        $this->Ln(8);
    }
    
    // Contenu de l'article
    function ArticleBody($content) {
        $this->SetFont('Arial', '', 11);
        $this->SetTextColor(51, 51, 51);
        
        // Convertir les sauts de ligne
        $paragraphs = explode("\n", $content);
        
        foreach ($paragraphs as $paragraph) {
            if (trim($paragraph) != '') {
                $this->MultiCell(0, 7, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', trim($paragraph)), 0, 'J');
                $this->Ln(3);
            }
        }
    }
    
    // Ajouter une image
    function ArticleImage($imagePath) {
        if (file_exists($imagePath)) {
            // Calculer la largeur pour garder les proportions
            $maxWidth = 170;
            $imageInfo = getimagesize($imagePath);
            
            if ($imageInfo) {
                $width = $imageInfo[0];
                $height = $imageInfo[1];
                $ratio = $width / $height;
                
                $pdfWidth = min($maxWidth, $width * 0.264583); // Conversion pixels vers mm
                $pdfHeight = $pdfWidth / $ratio;
                
                // Centrer l'image
                $x = (210 - $pdfWidth) / 2;
                
                try {
                    $this->Image($imagePath, $x, $this->GetY(), $pdfWidth, $pdfHeight);
                    $this->Ln($pdfHeight + 5);
                } catch (Exception $e) {
                    // Ignorer si l'image ne peut pas être ajoutée
                }
            }
        }
    }
}

// Fonction pour générer le PDF d'un article
function generateArticlePDF($article, $imagePath = null) {
    $pdf = new ArticlePDFExporter();
    $pdf->setArticleInfo($article->titre, $article->auteur, $article->date_creation);
    
    $pdf->AddPage();
    
    // Titre
    $pdf->ArticleTitle($article->titre);
    
    // Métadonnées
    $dateFormatted = date('d/m/Y à H:i', strtotime($article->date_creation));
    $pdf->ArticleMeta($article->auteur, $dateFormatted);
    
    // Image si présente
    if ($imagePath && file_exists($imagePath)) {
        $pdf->ArticleImage($imagePath);
    }
    
    // Contenu
    $pdf->ArticleBody($article->contenu);
    
    // Retourner le PDF
    return $pdf;
}
?>
