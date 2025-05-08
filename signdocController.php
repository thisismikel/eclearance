<?php
use setasign\Fpdi\Tcpdf\Fpdi;
require 'routines.php';
require_once('tcpdf/tcpdf.php');
require_once('fpdi/src/autoload.php');
date_default_timezone_set('Asia/Manila');
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
$myobj = validatetoken();
if (empty($myobj->userid)){
    header('Location:index.html?message='.urlencode("Invalid User"));
} else {$userid = $myobj->userid;}
$x = $_REQUEST['x'];
$y= $_REQUEST['y'];
$filename = $_REQUEST['filename'];
$pageno = $_REQUEST['pagenumber'];
$finalname = realpath('')."\\docs\\".$filename;
$finalname = realpath('').'/docs/'.$filename;
$sigimage = $_REQUEST['sigimage'];


		
		$pageCount = $pdf->setSourceFile($finalname);
		for ($i = 1; $i <= $pageCount; $i++) {
			$tplId = $pdf->importPage($i);
			$pdf -> AddPage();
			$size = $pdf->useImportedPage($tplId);
			if ($pageno == $i){
				
				$y = $size['height']- $y - 50;
				$pdf->SetXY($x, $y);
				//$pdf->Image('images/image_demo.jpg', 15, 140, 75, 113, 'JPG', 'http://www.tcpdf.org', '', true, 150, '', false, false, 1, false, false, false);
				//Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)
				$sigfullpath = 'signatures/'.$sigimage;
				$ext =  strtoupper(pathinfo($sigfullpath, PATHINFO_EXTENSION));
				$pdf->Image($sigfullpath, '', '', 50, 0, $ext, '', '', true, 300);
				$x = $x + 50;
				$pdf->SetXY($x, $y+10);
				$pdf->Image('images/dar_seal.png', '', '', 25, 0, 'PNG', '', '', true, 300);
				$im = imagecreate(150, 90);
				$bg = imagecolorallocate($im, 255, 255, 255);
				$textcolor = imagecolorallocate($im, 0, 0, 0);
				$fullname=$_REQUEST['fullname'];
				imagestring($im, 5, 0, 0, 'Digitally', $textcolor);
				imagestring($im, 5, 0, 14, 'stamped', $textcolor);
				imagestring($im, 5, 0, 28, $fullname, $textcolor);
				imagestring($im, 5, 0, 42, date("Y/m/d"), $textcolor);
				imagestring($im, 5, 0, 55, date("H:i:s"), $textcolor);
				imagestring($im, 5, 0, 68, 'UTC+08:00', $textcolor);
				ob_start();
				$img = imagepng($im);
				$imgdata = ob_get_contents();
				ob_end_clean();
				$x = $x + 26;
				$pdf->SetXY($x, $y);
				$pdf->Image('@'.$imgdata,'','',75,0,'PNG','','',true, 300);
				imagedestroy($im);
			}				
		}
		$signer = '';
		if (array_key_exists('signer', $_REQUEST)){
			$signer = $_REQUEST['signer'];
		}
		$signedfn = 'Signed_'.$filename;
		if ($signer == '2' ) {
			$signedfn = 'H'.$filename;
		}
		if ($signer == '3' ) {
			$signedfn = 'RD'.$filename;
		}
		//$fullpath  = realpath('')."\\docs\\".$signedfn;
		$fullpath = realpath('')."/docs/".$signedfn;
		$pdf->Output($fullpath,'F');
		$idorderdoc = $_REQUEST['idorderdoc'];
		
		if (empty($signer)){
			$command ="CALL updateorderdoc($idorderdoc, '$signedfn', $userid)";
		}
		if ($signer =='2' ) {
			$command ="CALL updateorderdoc2($idorderdoc, '$signedfn', $userid)";
		}
		if ($signer =='3' ) {
			$command ="CALL updateorderdoc3($idorderdoc, '$signedfn', $userid)";
		}
	
		getrow($command,false);
		$myobj = new stdClass();
		$myobj->filename = $signedfn;
		$myobj->iddcrform = $_REQUEST['iddcrform'];
		echo json_encode($myobj);
?>