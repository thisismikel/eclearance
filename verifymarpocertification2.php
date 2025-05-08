<?php
use setasign\Fpdi\Tcpdf\Fpdi;
require 'routines.php';
require_once('tcpdf/tcpdf.php');
require_once('fpdi/src/autoload.php');
class MYPDF extends Fpdi {

    //Page header
	public $notedby;
	public $receivedby;
	public function setdata($receivedby, $notedby){
		$this->notedby = $notedby;
		$this->receivedby = $receivedby;
	}
	
    public function Header() {
        
		
    }

    // Page footer
    
}
$pdf = new MYPDF();
$pdf->SetMargins(PDF_MARGIN_LEFT, 40, PDF_MARGIN_RIGHT);
$pdf->SetAutoPageBreak(true, 40);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('DAR MARPO CERTIFICATION');
$pdf->SetTitle('DAR MARPO CERTIFICATION');
$pdf->SetSubject('Davao City MARPO CERTIFICATION');
$pdf->SetKeywords('MARPO CERTIFICATION');

// set default header data


// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
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
$pdf->setFontSubsetting(true);
$pdf->setFormDefaultProp(array('lineWidth'=>1, 'borderStyle'=>'solid', 'fillColor'=>array(255, 255, 200), 'strokeColor'=>array(255, 128, 128)));
$iddcrform = $_REQUEST['iddcrform'];
$command = "select * from dcrform where dcrform.transaction = 'MAROCert' and iddcrform = $iddcrform";
$row = getrow($command);
if (empty($row['iddcrform'])){
	$pdf->AddPage();
	$pdf->Ln(7);
	$remarks ='<br/><h2 style="text-align:center;background-color: rgb(128, 128, 128);">INVALID DOCUMENT</h2>';
	$pdf->writeHTML($remarks, true, 0, true, true);
} else {
	$docfilename = $row['marpocert'];
	if (empty($docfilename)){
		$pdf->AddPage();
		$pdf->Ln(7);
		$remarks ='<br/><h2 style="text-align:center;background-color: rgb(128, 128, 128);">INVALID DOCUMENT</h2>';
		$pdf->writeHTML($remarks, true, 0, true, true);
	} else {
		$remarks ='<br/><br/><br/><h2 style="text-align:center;background-color: rgb(128, 128, 128);">VERIFIED - '.$iddcrform.'</h2>';

		$finalname = realpath('')."/marpo/".$docfilename;
		$pageCount = $pdf->setSourceFile($finalname);
		for ($i = 1; $i <= $pageCount; $i++) {
			$tplId = $pdf->importPage($i);
			$pdf->AddPage();
			$pdf->useImportedPage($tplId);
			if ($i == 1){
				$pdf->SetY(230);
				$pdf->SetX(0);
				$pdf->writeHTML($remarks, true, 0, true, true);
			}
		}
	}
}
$pdf->Output('MARPO_Certification.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
?>