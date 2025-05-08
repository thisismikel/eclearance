<?php

require_once('tcpdf/tcpdf.php');
require 'routines.php';
class MYPDF extends tcpdf {

    //Page header
	
    public function Header() {
        // Logo
        $image_file = 'DARLogo.jpg';
        // Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)
        $this->Image($image_file, 10, 10, 130, 0, 'JPG', '', 'T', true, 300, '', false, false, 0, false, false, false);
		
		
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
$tagvs = array(
 'p' => array(0 => array('n' => 0, 'h' => 0), 1 => array('n' => 0, 'h' => 0)),
 'li' => array(0 => array('n' => 1, 'h' => 0), 1 => array('n' => 1, 'h' => 0)),
 'ol' => array(0 => array('n' => 0, 'h' => 0), 1 => array('n' => 0, 'h' => 0)),
 'ul' => array(0 => array('n' => 0, 'h' => 0), 1 => array('n' => 0, 'h' => 0))
 
 );
 $pdf->setHtmlVSpace($tagvs);
	$pdf->AddPage();
	//$data = str_replace("<p>", '<p style ="height:0px">', $data);
	//die($data);
	//die(htmlspecialchars($data, ENT_QUOTES));
	$pdf->writeHTML($data, true, 0, true, true);
	$lsave = $_REQUEST['lsave'];
	
	if ($lsave == "0") {
		$pdf->Output('DAR_order.pdf', 'I');
	}else {
		$iddcrform = $_REQUEST['iddcrform'];
		$stamp = getdate();
		$fname = "Order".$iddcrform."_".$stamp['year'].$stamp['mon'].$stamp['mday'].$stamp['hours'].$stamp['minutes'].$stamp['seconds'].".pdf";

		//$fullpath = realpath('')."\\docs\\".$fname;
		$fullpath = realpath('')."/docs/".$fname;
		$pdf->Output($fullpath,'F');
		$command ="CALL insertorderdoc($iddcrform, '$fname', $userid)";
		$row = getrow($command);
		$myobj->id = $row['@id'];
		$myobj->filename=$fname;
		$myobj->iddcrform =$iddcrform;
		echo json_encode($myobj);
		
	}

//============================================================+
// END OF FILE
//============================================================+
?>