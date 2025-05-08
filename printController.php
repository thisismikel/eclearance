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
        // Logo
        $image_file = 'DARLogo.jpg';
        // Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)
        $this->Image($image_file, 10, 10, 130, 0, 'JPG', '', 'T', true, 300, '', false, false, 0, false, false, false);
		
        $this->Ln(4);$this->SetX(0);
		$this->SetFont('helvetica', 'R', 8);
		
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-37);
        // Set font
        $this->SetFont('helvetica', 'I', 10);
        $this->SetTextColor(204,0,0);
        	}
}


// initiate PDF
$pdf = new MYPDF();
$pdf->SetMargins(PDF_MARGIN_LEFT, 40, PDF_MARGIN_RIGHT);
$pdf->SetAutoPageBreak(true, 40);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('DAR PARO DAVAO');
$pdf->SetTitle('DAR Clearance');
$pdf->SetSubject('Davao City DAR Clearance');
$pdf->SetKeywords('DAR Clearance');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

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
$pdf->setFontSubsetting(false);

// set font
$pdf->SetFont('helvetica', '', 10, '', false);

// add a page
$pdf->AddPage();

/*
It is possible to create text fields, combo boxes, check boxes and buttons.
Fields are created at the current position and are given a name.
This name allows to manipulate them via JavaScript in order to perform some validation for instance.
*/

// set default form properties
$pdf->setFormDefaultProp(array('lineWidth'=>1, 'borderStyle'=>'solid', 'fillColor'=>array(255, 255, 200), 'strokeColor'=>array(255, 128, 128)));

$myobj = validatetoken();
if (empty($myobj->userid)){
    header('Location:index.html?message='.urlencode("Invalid User"));
} else {$userid = $myobj->userid;}
$iddcrform = $_REQUEST['iddcrform'];

$command = "select dcrform.*,trackform.*,dcrform.status as status2,users.fullname as paro, users.signature,(select office.officename from office where office.idoffice=dcrform.municipal) as municipalname, (select office.officename from office where office.idoffice=dcrform.provincial) as provincialname, (select office.officename from office where office.idoffice=dcrform.regional) as regionalname from trackform left join dcrform on trackform.iddcrform = dcrform.iddcrform left join office on dcrform.municipal = office.idoffice left join users on trackform.userid = users.userid where trackform.status = 'COMPLETED' and dcrform.transaction = 'CLEARANCE' and dcrform.iddcrform = $iddcrform";
$row = getrow($command);
$amended = $row['amended'];
$docfilename = $row['docfilename'];
$lotno = $row['lotno'];
$registeredowners = $row['registeredowners'];
$titleno = $row['titleno'];
$area = number_format($row['area'],2);
$tdno = $row['tdno'];
$propertylocation = $row['propertylocation'];
$instrument = $row['instrument'];
$transferor = $row['transferor'];
$transferees = $row['transferees'];
$dateconveyance = date("F j\, Y", strtotime($row['dateconveyance']));
$placeconveyance = $row['placeconveyance'];
$docno = $row['docno'];
$pageno = $row['pageno'];
$bookno = $row['bookno'];
$yrnotarized = $row['yrnotarized'];
$notarypublic = $row['notarypublic'];
$transferor = $row['notarypublic'];
$applicant = $row['applicant'];
$dateapproval = date("F j\, Y", strtotime($row['startdate']));
$provincialname = $row['provincialname'];
$paro = trim($row['paro']);
$scount = strlen($paro);
if (empty($row['signature'])){
	$space ='<p></p><p></p><p></p><p></p>';
	$signature = '';
	$sigfullpath ='';
} else {
	$space='';
	//$signature = '<img src="signatures/'.$row['signature'].'" style="height:60px">';
	$sigfullpath = 'signatures/'.$row['signature'];
				
}
				
	
//}
if ($scount < 60){
	$stuff = round((60 - $scount)/2);
	$paro = str_repeat("&nbsp;", $stuff).$paro.str_repeat("&nbsp;", $stuff);
}

$pdf->Ln(7);
$pdf->SetFont('helvetica', '', 12);
$remarks = '<p style="text-indent:100px">TRANSFER CLEARANCE</p>';
$remarks .='<p style="text-indent:50px;text-align:justify">This is to CERTIFY that a parcel of land, indentified as <b>'.$lotno.'</b>, registered name of <b>'.$registeredowners.'</b>,';
$remarks .=' embraced by <b>'.$titleno.'</b>, containing an area <b>'.$area.' square meters</b>, more or less, declared';
$remarks .=' under Tax Declaration No. <b>'.$tdno.'</b>, situated at <b>'.$propertylocation.'</b>, is a subject of <b>'.$instrument.'</b> ';
$remarks .='executed by <b>'.$transferor.'</b> in favor of <b>'.$transferees.'</b>, execution on <b>'.$dateconveyance.'</b> at <b>'.$placeconveyance.'</b>';
$remarks .=' per Doc No. <b>'.$docno.'</b> Page No. <b>'.$pageno.'</b> Book No. <b>'.$bookno.'</b> Series of <b>'.$yrnotarized.'</b> of the Notarial Register of ';
$remarks .='<b>'.$notarypublic.'</b> is the retention area of the <b>'.$transferor.'</b>.</p>';
$remarks .='<p style="text-indent:50px;text-align:justify">The Department of Agrarian Reform(DAR) reserves the right to annul this Transfer Clearance upon knowledge that any';
$remarks .='on the documents submitted by the applicant is false or perjured.</p>';
$remarks .='<p style="text-indent:50px;text-align:justify">This Clearance is issued upon the request of <b>'.$applicant.'</b> to facilitate the transfer of title.<p>';
$remarks .='<p style="text-indent:50px">Given this <b>'.$dateapproval.'</b> in <b>'.$provincialname.'</b>.</p>';
$remarks .='<p style="text-indent:50px">APPROVED BY:</p>';
$remarks .=$space;
//$remarks .='<table><tr><td style="text-align:center;width:50%;margin:0px;padding:0px">';
//$remarks .= $signature.'<br/>';
//$remarks .='<u><b>'.$paro.'</b></u><br>Provincial Agrarian Reform Program Officer II';
//$remarks .='</td><td style="width:50%;"></td></tr>';
//$remarks .='</table>';
$pdf->writeHTML($remarks, true, 0, true, true);
				$pdf->Ln(1);
				$x = $pdf->GetX();
				$y = $pdf->GetY();
				$pdf->SetXY($x+10, $y);
				if (!empty($sigfullpath)) {$pdf->Image($sigfullpath, '', '', 25, 0, 'PNG', '', '', true, 300);}
				$x = $pdf->GetX();
				$y = $pdf->GetY();
				$pdf->SetXY($x+25, $y+3);
				$pdf->Image('images/dar_seal.png', '', '', 6, 0, 'PNG', '', '', true, 300);
				$im = imagecreate(150, 90);
				$bg = imagecolorallocate($im, 255, 255, 255);
				$textcolor = imagecolorallocate($im, 0, 0, 0);
				imagestring($im, 5, 0, 0, 'Digitally', $textcolor);
				imagestring($im, 5, 0, 14, 'stamped', $textcolor);
				imagestring($im, 5, 0, 28, 'Jun Legal', $textcolor);
				imagestring($im, 5, 0, 42, date("Y/m/d"), $textcolor);
				imagestring($im, 5, 0, 55, date("H:i:s"), $textcolor);
				imagestring($im, 5, 0, 68, 'UTC+08:00', $textcolor);
				ob_start();
				$img = imagepng($im);
				$imgdata = ob_get_contents();
				ob_end_clean();
				$x = $pdf->GetX();
				$pdf->SetXY($x+6, $y);
				$pdf->Image('@'.$imgdata,'','',35,0,'PNG','','',true, 300);
				imagedestroy($im);
				$remarks2 ='<u><b>'.$paro.'</b></u><br>Provincial Agrarian Reform Program Officer II';
				//$x = $pdf->GetX();
				//$y = $pdf->GetY();
				//$pdf->SetXY($x+5, $y);
				$pdf->Ln(20);
				$pdf->writeHTML($remarks2, true, 0, true, true);
$style = array(
    'border' => false,
    'padding' => 0,
    'fgcolor' => array(0,0,0),
    'bgcolor' => false
);
$pdf->write2DBarcode('https://darxi.net/verifydarclearance.php?iddcrform='.$iddcrform, 'QRCODE,H', 20, 190, 25, 25, $style, 'N');
if ($amended == 'Y'){
$pdf->Ln(5);
$note ='<p>This document amends previously issued Transfer Clearance to '.$applicant. ' dated '. $dateapproval.' with title no. '.$titleno.'.  Please attachement/s</p><br/>';
$pdf->writeHTML($note, true, 0, true, true);
}
if (empty($docfilename)){
	$filename = $iddcrform;
	$filename .= '.pdf';
	//$fullpath = realpath('')."\\docs\\".$filename;
	$fullpath = realpath('')."/docs/".$filename;
} else {
	$patterns = '/.pdf/';
	$filename = preg_replace($patterns, '1', $docfilename).".pdf";
	//$fullpath = realpath('')."\\docs\\".$filename;
	$fullpath = realpath('')."/docs/".$filename;
	
}
if ($amended == 'Y'){
	$finalname = realpath('')."/docs/".$docfilename;
	$pageCount = $pdf->setSourceFile($finalname);
	for ($i = 1; $i <= $pageCount; $i++) {
		$tplId = $pdf->importPage($i);
		$pdf->AddPage();
		$pdf->useImportedPage($tplId);
	}
    
}

$pdf->Output($fullpath,'F');

$command = "CALL updatefilename($iddcrform, '$filename', $userid)";
getrow($command,false);




?>