<?php
use setasign\Fpdi\Tcpdf\Fpdi;

require_once('tcpdf/tcpdf.php');
require_once('fpdi/src/autoload.php');
class MYPDF extends fpdi {

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

// add a page
$pdf->AddPage();

// get external file content


//$pdf->SetFont('freeserif', '', 12);
// now write some text above the imported page
$pdf->writeHTML("<h1>hello</h1>", true, 0, true, true);
$fullpath = realpath('')."\\docs\\generated.pdf";
$pdf->Output($fullpath,'F');

?>