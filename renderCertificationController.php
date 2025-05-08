<?php

require_once('tcpdf/tcpdf.php');
require 'routines.php';
class MYPDF extends tcpdf {

    //Page header
	public $province;
	
    public function Header() {
        // Logo
        $image_file = 'DARLogo.jpg';
        // Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)
        $this->Image($image_file, 10, 10, 130, 0, 'JPG', '', 'T', true, 300, '', false, false, 0, false, false, false);
		$this->Ln(17);$this->SetX(28);
		$this->Write(0, "Region XI", '', 0, 'L', true, 0, false, false, 0);
		$this->Ln(1);$this->SetX(28);
		$this->Write(0, $this->province, '', 0, 'L', true, 0, false, false, 0);
		
		
    }

    }
// initiate PDF
$pdf = new MYPDF();

$pdf->SetMargins(PDF_MARGIN_LEFT, 40, PDF_MARGIN_RIGHT);
$pdf->SetAutoPageBreak(true, 40);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('DAR REGION XI');
$pdf->SetTitle('DAR RECOMMENDATION LETTER');
$pdf->SetSubject('Davao City DAR Recommendation letter');
$pdf->SetKeywords('DAR Recommendation Letter');

// set default header data
// set header and footer fonts

$pdf->setHeaderFont(Array('helvetica', '', 10));
//$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// remove default header/footer
$pdf->setPrintHeader(true);
$pdf->setPrintFooter(false);


// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, 35, PDF_MARGIN_RIGHT);
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
$myobj = validatetoken();
if (empty($myobj->userid)){
    header('Location:index.html?message='.urlencode("Invalid User"));
} else {$userid = $myobj->userid;}
$data = $_REQUEST['ckcontent'];
//file_put_contents("ireport/template.txt",$data);

$tagvs = array(
 'p' => array(0 => array('n' => 0, 'h' => 0), 1 => array('n' => 0, 'h' => 0)),
 'li' => array(0 => array('n' => 1, 'h' => 0), 1 => array('n' => 1, 'h' => 0)),
 'ol' => array(0 => array('n' => 0, 'h' => 0), 1 => array('n' => 0, 'h' => 0)),
 'ul' => array(0 => array('n' => 0, 'h' => 0), 1 => array('n' => 0, 'h' => 0))
 
 );
 $pdf->province = $_REQUEST['provincial'];
 $pdf->setHtmlVSpace($tagvs);
	$pdf->AddPage();
	//$data = str_replace("<p>", '<p style ="height:0px">', $data);
	//die($data);
	//die(htmlspecialchars($data, ENT_QUOTES));
	//$pdf->Ln(5);
	//$pdf->Ln(19);
	$pdf->Ln(3);
		$pdf->SetFont ('helvetica', 'B', 14);
		$pdf->Cell(0, 0, 'CERTIFICATION', 0, 1, 'C', 0, '', 1);
		$pdf->SetFont ('helvetica', '', 10);
		$pdf->Ln(0);
		$pdf->Write(0, '(Attestation and Recommendation)', '', 0, 'C', true, 0, false, false, 0);
		$pdf->Ln(10);
	$pdf->writeHTML($data, true, 0, true, true);
	$pdf->SetFont ('helvetica', '', 7);
	$pdf->Ln(0);
	$pdf->Cell(0, 0, 'Copy of Distribution', 0, 1, 'L', 0, '', 0);
	$pdf->Cell(0, 0, 'Original – PARPO II/LTC Folder', 0, 1, 'L', 0, '', 0);
	$pdf->Cell(0, 0, 'Duplicate – LTID-FOD', 0, 1, 'L', 0, '', 0);
	$pdf->Cell(0, 0, 'Triplicate – Legal Division', 0, 1, 'L', 0, '', 0);
	$lsave = $_REQUEST['lsave'];
	
	if ($lsave == "0") {
		$pdf->Output('CARPO_Certificate.pdf', 'I');
	}else {
		$iddcrform = $_REQUEST['iddcrform'];
		$stamp = getdate();
		$fname = "Carpo".$iddcrform."_".$stamp['year'].$stamp['mon'].$stamp['mday'].$stamp['hours'].$stamp['minutes'].$stamp['seconds'].".pdf";
		if (stristr(PHP_OS, 'WIN')) {
				$fullpath = realpath('')."\\ireport\\".$fname;
		} else {
			$fullpath = realpath('')."/ireport/".$fname;
		}
		$pdf->Output($fullpath,'F');
		$command ="CALL insertcarpocert($iddcrform, '$fname', $userid)";
		$row = getrow($command);
		$fileinfo = pathinfo($fullpath);
		header('Content-Type: application/pdf');
		header('Content-Length: ' . filesize($fullpath));
		readfile($fullpath);
		
		
	}

//============================================================+
// END OF FILE
//============================================================+
?>