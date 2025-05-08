<?php
use setasign\Fpdi\Tcpdf\Fpdi;
require 'routines.php';
require_once('tcpdf/tcpdf.php');
require_once('fpdi/src/autoload.php');
class MYPDF extends Fpdi {

    
	public function setdata($receivedby, $notedby){
	}
	
    public function Header() {
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        	}
}


// initiate PDF
$pdf = new MYPDF();
$pdf->SetMargins(PDF_MARGIN_LEFT, 40, PDF_MARGIN_RIGHT);
$pdf->SetAutoPageBreak(true, 40);
$pdf->setPageUnit('pt');

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('DAR DOCUMENT');
$pdf->SetTitle('DAR DOCUMENT');
$pdf->SetSubject('Davao City DAR DOCUMENT');
$pdf->SetKeywords('DAR Document');

// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
//$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// IMPORTANT: disable font subsetting to allow users editing the document
$pdf->setFontSubsetting(false);

// set font
$pdf->SetFont('helvetica', '', 10, '', false);
//$pdf->setFormDefaultProp(array('lineWidth'=>1, 'borderStyle'=>'solid', 'fillColor'=>array(255, 255, 200), 'strokeColor'=>array(255, 128, 128)));
// add a page
//$pdf->AddPage();

/*
It is possible to create text fields, combo boxes, check boxes and buttons.
Fields are created at the current position and are given a name.
This name allows to manipulate them via JavaScript in order to perform some validation for instance.
*/

// set default form properties
//$pdf->setFormDefaultProp(array('lineWidth'=>1, 'borderStyle'=>'solid', 'fillColor'=>array(255, 255, 200), 'strokeColor'=>array(255, 128, 128)));
$x =142.042685;
$y= 792-684-4;
$finalname = realpath('')."\\docs\\52.pdf";
		
		$pageCount = $pdf->setSourceFile($finalname);
		
		
			
			$tplId = $pdf->importPage(1);
			
			//$pdf->getTemplateSize($tplId);
			$pdf -> AddPage();
			//$pdf ->useTemplate($tplIdx, 0, null, $size['w'], 310, FALSE);
			
			$size = $pdf->useImportedPage($tplId);
			//$size = $pdf->useTemplate($tplId, ['adjustPageSize' => true]);
			//$pdf->useTemplate($tplId, 0, 0, 0, 0, 0,true);
			
			/// $pdf->getScaleFactor();
			$y = $size['height']-523.440034 - 25;
			//-684;
			//* $pdf->getScaleFactor();
			//print_r($size);
			//die('test');
			$pdf->SetXY($x, $y);
			
			$pdf->Write(0, "x:". $x.", y: ".$y." this is a test", '', 0, 'L', true, 0, false, false, 0);

				
			
			
		

//$pdf->Cell(0, 0, "x:". $x." y: ".$y." this is a test", 0, 0, 'L', true);
$pdf->Output('dar','I');



//syncfusion.com/ebooks/pdf/text-operators

?>