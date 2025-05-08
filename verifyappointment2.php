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
$pdf = new MYPDF();
$pdf->SetMargins(PDF_MARGIN_LEFT, 40, PDF_MARGIN_RIGHT);
$pdf->SetAutoPageBreak(true, 40);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('DAR REGION XI');
$pdf->SetTitle('DAR ONLINE APPOINTMENT');
$pdf->SetSubject('Davao City DAR Online APPOINTMENT');
$pdf->SetKeywords('DAR online Appointment');

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
$idappointment = $_REQUEST['idappointment'];
$command = "SELECT * FROM dar.appointment left join schedule on appointment.idschedule = schedule.idschedule left join users on appointment.userid = users.userid join office on schedule.idoffice = office.idoffice where appointment.idappointment = $idappointment";
$row = getrow($command);
if (empty($row['idappointment'])){
	$pdf->AddPage();
	$pdf->Ln(7);
	$remarks ='<br/><h2 style="text-align:center;background-color: rgb(128, 128, 128);">INVALID DOCUMENT</h2>';
	$pdf->writeHTML($remarks, true, 0, true, true);
} else {
	//$pdf->AddPage();
	$pdf->AddPage('L', 'A5');
	$pdf->setFormDefaultProp(array('lineWidth'=>1, 'borderStyle'=>'solid', 'fillColor'=>array(255, 255, 200), 'strokeColor'=>array(255, 128, 128)));
	$pdf->Ln(7);
	$pdf->SetFont('helvetica', '', 12);
	$date = new DateTime($row['scheduledate']);
	$scheddate = date("F j, Y, g:i a",$date->getTimestamp());
	$remarks = '<p style="text-align:center;"><strong>Appointment Schedule DAR XI</strong></p>';
	$remarks .= 'Name: <strong>'.$row['fullname'].'</strong><br/>';
	$remarks .= 'Email: <strong>'.$row['emailaddress'].'</strong>  Mobile No.: <strong>'.$row['cellno'].'</strong><br/>';
	$remarks .= 'Scheduled Date: <strong>'.$scheddate.'</strong><br/>';
	$remarks .= 'Location: <strong>'.$row['officename'].', '.$row['location'].'</strong><br/>';
	$pdf->writeHTML($remarks, true, 0, true, true);

$style = array(
    'border' => false,
    'padding' => 0,
    'fgcolor' => array(0,0,0),
    'bgcolor' => false
);
$pdf->write2DBarcode('https://darxi.net/verifyappointment.php?idappointment='.$idappointment, 'QRCODE,H', 20, 70, 25, 25, $style, 'N');
	
if (!empty($row['appremarks'])){
$pdf->Ln(5);
$note ='<p>Note:  '.$row['appremarks']. '.';
if (!empty($row['ridappointment'])){
	$note .= '<a href="https://darxi.net/verifyappointment.php?idappointment='.$row['ridappointment'].'"> <i>Click here for Details.</i></a>';
}
$note .="</p>";
$pdf->writeHTML($note, true, 0, true, true);
}
	
	
}
$pdf->Output('DAR_appointment.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
?>