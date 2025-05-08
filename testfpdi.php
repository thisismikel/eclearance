<?php
include ( 'PdfToText/PdfToText.phpclass' ) ;
$pdf = new PdfToText ( 'docs/52.pdf' ) ;
$result = $pdf -> document_stripos ( "approved", $group_by_page = true, $start = 72) ;
$npages = count($result);
if ($npages > 0) {
	if (function_exists("array_key_last")){
		$index = array_key_last($result);
		
	} else { 
	
	   $index = array_keys($result)[count(result)-1];}
}
print_r($result[$index]);
//echo $pdf -> Text ;
?>