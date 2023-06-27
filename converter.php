<?php
require_once('./vendor/autoload.php');

use Fpdf\Fpdf;

$type = $_GET['type'];
$text = $_GET['text'];
$nonce = $_GET['nonce'];

generate_file($type, $text, $nonce);

// Generate the file based on the specified type
function generate_file($type,$text,$nonce='123') {

     if (!(wp_verify_nonce( $nonce, 'kamranconvert' )) ) 
     {
        return "You are not authorized !";
     }

    // Generate .txt file
    if ($type === 'txt') {
        header('Content-Disposition: attachment; filename="file.txt"');
        header('Content-Type: text/plain');
        echo $text;
        exit;
    }

    // Generate .doc file using PHPWord
    if ($type === 'doc') {
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();
        $section->addText($text);
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        header('Content-Disposition: attachment; filename="file.docx"');
        $objWriter->save('php://output');
        exit;
    }

    // Generate .pdf file using FPDF
    if ($type === 'pdf') {
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 12);
        $pdf->MultiCell(0, 10, $text);
        header('Content-Disposition: attachment; filename="file.pdf"');
        $pdf->Output('php://output', 'F');
        exit;
    }
}

if (isset($_GET['type'])) {
    $type = $_GET['type'];
    generate_file($type);
}
?>