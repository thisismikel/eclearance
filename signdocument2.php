<?php

function decodeAsciiHex($input) {
    $output = "";

    $isOdd = true;
    $isComment = false;

    for($i = 0, $codeHigh = -1; $i < strlen($input) && $input[$i] != '>'; $i++) {
        $c = $input[$i];

        if($isComment) {
            if ($c == '\r' || $c == '\n')
                $isComment = false;
            continue;
        }

        switch($c) {
            case '\0': case '\t': case '\r': case '\f': case '\n': case ' ': break;
            case '%': 
                $isComment = true;
            break;

            default:
                $code = hexdec($c);
                if($code === 0 && $c != '0')
                    return "";

                if($isOdd)
                    $codeHigh = $code;
                else
                    $output .= chr($codeHigh * 16 + $code);

                $isOdd = !$isOdd;
            break;
        }
    }

    if($input[$i] != '>')
        return "";

    if($isOdd)
        $output .= chr($codeHigh * 16);

    return $output;
}

function decodeAscii85($input) {
    $output = "";

    $isComment = false;
    $ords = array();

    for($i = 0, $state = 0; $i < strlen($input) && $input[$i] != '~'; $i++) {
        $c = $input[$i];

        if($isComment) {
            if ($c == '\r' || $c == '\n')
                $isComment = false;
            continue;
        }

        if ($c == '\0' || $c == '\t' || $c == '\r' || $c == '\f' || $c == '\n' || $c == ' ')
            continue;
        if ($c == '%') {
            $isComment = true;
            continue;
        }
        if ($c == 'z' && $state === 0) {
            $output .= str_repeat(chr(0), 4);
            continue;
        }
        if ($c < '!' || $c > 'u')
            return "";

        $code = ord($input[$i]) & 0xff;
        $ords[$state++] = $code - ord('!');

        if ($state == 5) {
            $state = 0;
            for ($sum = 0, $j = 0; $j < 5; $j++)
                $sum = $sum * 85 + $ords[$j];
            for ($j = 3; $j >= 0; $j--)
                $output .= chr($sum >> ($j * 8));
        }
    }
    if ($state === 1)
        return "";
    elseif ($state > 1) {
        for ($i = 0, $sum = 0; $i < $state; $i++)
            $sum += ($ords[$i] + ($i == $state - 1)) * pow(85, 4 - $i);
        for ($i = 0; $i < $state - 1; $i++)
            $ouput .= chr($sum >> ((3 - $i) * 8));
    }

    return $output;
}

function decodeFlate($input) {
    return @gzuncompress($input);
}

function getObjectOptions($object) {
    $options = array();
    if (preg_match("#<<(.*)>>#ismU", $object, $options)) {
        $options = explode("/", $options[1]);
        @array_shift($options);

        $o = array();
        for ($j = 0; $j < @count($options); $j++) {
            $options[$j] = preg_replace("#\s+#", " ", trim($options[$j]));
            if (strpos($options[$j], " ") !== false) {
                $parts = explode(" ", $options[$j]);
                $o[$parts[0]] = $parts[1];
            } else
                $o[$options[$j]] = true;
        }
        $options = $o;
        unset($o);
    }

    return $options;
}
function getDecodedStream($stream, $options) {
    $data = "";
    if (empty($options["Filter"]))
        $data = $stream;
    else {
        $length = !empty($options["Length"]) ? $options["Length"] : strlen($stream);
        $_stream = substr($stream, 0, $length);

        foreach ($options as $key => $value) {
            if ($key == "ASCIIHexDecode")
                $_stream = decodeAsciiHex($_stream);
            if ($key == "ASCII85Decode")
                $_stream = decodeAscii85($_stream);
            if ($key == "FlateDecode")
                $_stream = decodeFlate($_stream);
        }
        $data = $_stream;
    }
    return $data;
}
function getDirtyTexts(&$texts, $textContainers) {
    for ($j = 0; $j < count($textContainers); $j++) {
        if (preg_match_all("#\[(.*)\]\s*TJ#ismU", $textContainers[$j], $parts))
            $texts = array_merge($texts, @$parts[1]);
        elseif(preg_match_all("#Td\s*(\(.*\))\s*Tj#ismU", $textContainers[$j], $parts))
            $texts = array_merge($texts, @$parts[1]);
    }
}
function getCharTransformations(&$transformations, $stream) {
    preg_match_all("#([0-9]+)\s+beginbfchar(.*)endbfchar#ismU", $stream, $chars, PREG_SET_ORDER);
    preg_match_all("#([0-9]+)\s+beginbfrange(.*)endbfrange#ismU", $stream, $ranges, PREG_SET_ORDER);

    for ($j = 0; $j < count($chars); $j++) {
        $count = $chars[$j][1];
        $current = explode("\n", trim($chars[$j][2]));
        for ($k = 0; $k < $count && $k < count($current); $k++) {
            if (preg_match("#<([0-9a-f]{2,4})>\s+<([0-9a-f]{4,512})>#is", trim($current[$k]), $map))
                $transformations[str_pad($map[1], 4, "0")] = $map[2];
        }
    }
    for ($j = 0; $j < count($ranges); $j++) {
        $count = $ranges[$j][1];
        $current = explode("\n", trim($ranges[$j][2]));
        for ($k = 0; $k < $count && $k < count($current); $k++) {
            if (preg_match("#<([0-9a-f]{4})>\s+<([0-9a-f]{4})>\s+<([0-9a-f]{4})>#is", trim($current[$k]), $map)) {
                $from = hexdec($map[1]);
                $to = hexdec($map[2]);
                $_from = hexdec($map[3]);

                for ($m = $from, $n = 0; $m <= $to; $m++, $n++)
                    $transformations[sprintf("%04X", $m)] = sprintf("%04X", $_from + $n);
            } elseif (preg_match("#<([0-9a-f]{4})>\s+<([0-9a-f]{4})>\s+\[(.*)\]#ismU", trim($current[$k]), $map)) {
                $from = hexdec($map[1]);
                $to = hexdec($map[2]);
                $parts = preg_split("#\s+#", trim($map[3]));

                for ($m = $from, $n = 0; $m <= $to && $n < count($parts); $m++, $n++)
                    $transformations[sprintf("%04X", $m)] = sprintf("%04X", hexdec($parts[$n]));
            }
        }
    }
}
function getTextUsingTransformations($texts, $transformations) {
    $document = "";
    for ($i = 0; $i < count($texts); $i++) {
        $isHex = false;
        $isPlain = false;

        $hex = "";
        $plain = "";
        for ($j = 0; $j < strlen($texts[$i]); $j++) {
            $c = $texts[$i][$j];
            switch($c) {
                case "<":
                    $hex = "";
                    $isHex = true;
                break;
                case ">":
                    $hexs = str_split($hex, 4);
                    for ($k = 0; $k < count($hexs); $k++) {
                        $chex = str_pad($hexs[$k], 4, "0");
                        if (isset($transformations[$chex]))
                            $chex = $transformations[$chex];
                        $document .= html_entity_decode("&#x".$chex.";");
                    }
                    $isHex = false;
                break;
                case "(":
                    $plain = "";
                    $isPlain = true;
                break;
                case ")":
                    $document .= $plain;
                    $isPlain = false;
                break;
                case "\\":
                    $c2 = $texts[$i][$j + 1];
                    if (in_array($c2, array("\\", "(", ")"))) $plain .= $c2;
                    elseif ($c2 == "n") $plain .= '\n';
                    elseif ($c2 == "r") $plain .= '\r';
                    elseif ($c2 == "t") $plain .= '\t';
                    elseif ($c2 == "b") $plain .= '\b';
                    elseif ($c2 == "f") $plain .= '\f';
                    elseif ($c2 >= '0' && $c2 <= '9') {
                        $oct = preg_replace("#[^0-9]#", "", substr($texts[$i], $j + 1, 3));
                        $j += strlen($oct) - 1;
                        $plain .= html_entity_decode("&#".octdec($oct).";");
                    }
                    $j++;
                break;

                default:
                    if ($isHex)
                        $hex .= $c;
                    if ($isPlain)
                        $plain .= $c;
                break;
            }
        }
        $document .= "\n";
    }

    return $document;
}

function pdf2text($filename, $search) {
    $pageNumber = 1;
    $infile = @file_get_contents($filename, FILE_BINARY);
    if (empty($infile))
        return "";

    $transformations = array();    

    preg_match_all("#obj(.*)endobj#ismU", $infile, $objects);
    $objects = @$objects[1];

    for ($i = 0; $i < count($objects); $i++) {
        $texts = array();
        $currentObject = $objects[$i];

        if (preg_match("#stream(.*)endstream#ismU", $currentObject, $stream)) {
            $stream = ltrim($stream[1]);

            $options = getObjectOptions($currentObject);
            if (!(empty($options["Length1"]) && empty($options["Type"]) && empty($options["Subtype"])))
                continue;

            $data = getDecodedStream($stream, $options); 
            if (strlen($data)) {
                if (preg_match_all("#BT(.*)ET#ismU", $data, $textContainers)) {
                    //print_r($textContainers);
                    $textContainers = @$textContainers[1];
                    //print_r($textContainers);print "<br><br>";
                    getDirtyTexts($texts, $textContainers);
                    //print_r($textContainers);
                }else{
                    getCharTransformations($transformations, $data);
                }
            }

            //print "HOJA = ".$pageNumber;
            //$textTransformed = getTextUsingTransformations($texts, $transformations);
            //print $textTransformed."<br><br><br>";
            //print_r($currentObject);
            //print_r($options);
            //print_r($stream);
            //print_r($data);
            //print ($textContainers[0])."<br>";
            //print_r($textContainers);
            //print_r($texts);            
            //print "<br><br><br>";

            $m = array();            
            foreach ($texts as $key => $value) {
                $prearray = array($value);
                //print_r($textContainers[$key]);
                preg_match("/\/\F[0-9]\s[0-9][0-9]/",$textContainers[$key],$m);
                if(!empty($m)){
                    $fontSize = array();
                    $fontSize = explode(" ", $m[0]);
                    $fontSize = array_filter($fontSize);
                    //print_r($fontSize);
                }

                $textTransformed = getTextUsingTransformations($prearray, $transformations);
                //print $textTransformed."<br><br><br>";
                $pos = strpos($textTransformed, $search);
                if($pos !== false){
                    $data = NULL;
                    $pos = strpos($textContainers[$key], "Tm");
                    if($pos !== false){
                        $data = substr($textContainers[$key], 0, $pos);
                    }else{
                        $pos = strpos($textContainers[$key], "Td");
                        if($pos !== false){
                            $data = substr($textContainers[$key], 0, $pos);
                        }
                    }
                    if(!empty($data)){
                        $dataArray = array();
                        $dataArray = explode(" ", $data);
                        $dataArray = array_filter($dataArray);

                        $returnArray = array();
                        $returnArray['keyword'] = $search;
                        $returnArray['page'] = $pageNumber;
                        $returnArray['font'] = $fontSize[1];
                        $returnArray['x'] = $dataArray[count($dataArray)];
                        $returnArray['y'] = $dataArray[count($dataArray)+1];                        

                        return $returnArray;
                    }
                }
            }
            $pageNumber++;
        }
    }

    //return getTextUsingTransformations($texts, $transformations);
    return false;
}

///usage of above code --- identify searched word location////////////////////////////

  // initiate FPDI
$pdf = new FPDI('P', 'pt', 'A4');
$pdf->setPrintHeader(false);

// set the source file
$pdffile = "template.pdf";
$numberOfPages = $pdf->setSourceFile($pdffile);

$theReturnedFecha = pdf2text($pdffile, "@DATE");
$theReturnedCliente = pdf2text($pdffile, "@CLIENT");


// import all pages
for($i = 1; $i <= $numberOfPages; $i++){
    $pdf->AddPage();
    $tplIdx = $pdf->importPage($i);
    $pdf->useTemplate($tplIdx);
    if($theReturnedFecha !== false){
        if($theReturnedFecha['page'] == $i){
            $pdf->SetFont('Helvetica');
            $pdf->SetFontSize($theReturnedFecha['font']);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFillColor(255,255,255);
            $pdf->SetXY($theReturnedFecha['x'], ($theReturnedFecha['y']*-1)-$theReturnedFecha['font']);
            $pdf->Cell(0, 0, dateInSpanish("2015-11-04"), 0, 0, 'L', true);
        }
        //print_r($theReturnedFecha);
    }

    if($theReturnedCliente !== false){
        if($theReturnedCliente['page'] == $i){
            $pdf->SetFont('Helvetica');
            $pdf->SetFontSize($theReturnedCliente['font']);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFillColor(255,255,255);
            $pdf->SetXY($theReturnedCliente['x'], ($theReturnedCliente['y']*-1)-$theReturnedCliente['font']);
            $pdf->Cell(0, 0, $x_nombre_cli, 0, 0, 'L', true);
        }
        //print_r($theReturnedFecha);
    }        
}

$pdf->Output();






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
$pdf->SetAuthor('DAR PARO DAVAO');
$pdf->SetTitle('DAR Clearance');
$pdf->SetSubject('Davao City DAR Clearance');
$pdf->SetKeywords('DAR Clearance');

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
$command = "select * from dcrform where dcrform.transaction = 'CLEARANCE' and iddcrform = $iddcrform";
$row = getrow($command);
if (empty($row['iddcrform'])){
	$pdf->AddPage();
	$pdf->Ln(7);
	$remarks ='<br/><h2 style="text-align:center;background-color: rgb(128, 128, 128);">INVALID DOCUMENT</h2>';
	$pdf->writeHTML($remarks, true, 0, true, true);
} else {
	$docfilename = $row['docfilename'];
	if (empty($docfilename)){
		$pdf->AddPage();
		$pdf->Ln(7);
		$remarks ='<br/><h2 style="text-align:center;background-color: rgb(128, 128, 128);">INVALID DOCUMENT</h2>';
		$pdf->writeHTML($remarks, true, 0, true, true);
	} else {
		$remarks ='<br/><h2 style="text-align:center;background-color: rgb(128, 128, 128);">VERIFIED - '.$iddcrform.'</h2>';

		$finalname = realpath('')."/docs/".$docfilename;
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
$pdf->Output('DAR_clearance.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
?>