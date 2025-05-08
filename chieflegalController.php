<?php
require "routines.php";

function getdetails(){
	$table = new stdClass();
	$officecode = $_REQUEST['officecode'];
	$command = "select *, (select office.officename from office where office.idoffice=dcrform.provincial) as provincialname, (select office.officename from office where office.idoffice=dcrform.regional) as regionalname from trackform left join dcrform on trackform.iddcrform = dcrform.iddcrform where trackform.status='CHIEFLEGAL' and trackform.officecode='$officecode' and trackform.enddate is null";
	$table->data = gettable($command);
	echo json_encode($table);
	
}
function showpdf(){
	$iddcrform= $_REQUEST['iddcrform'];
	$command = "select * from dcrform where iddcrform = $iddcrform";
	$row=getrow($command);
	$filename = 'landclearance/'.$row['landclearance'];;
    $fileinfo = pathinfo($filename);
    header('Content-Type: application/pdf');
    header('Content-Length: ' . filesize($filename));
    readfile($filename);
}
function deletepdf($userid){
	$myobj = new stdClass();
	$iddcrform = $_REQUEST['iddcrform'];
	$command = "CALL deletelandclearance($iddcrform, $userid)";
	$row=getrow($command);
	$myobj->iddcrform = $iddcrform;
	echo json_encode($myobj);
}
function showpdfar(){
	$iddcrform= $_REQUEST['iddcrform'];
	$command = "select * from dcrform where iddcrform = $iddcrform";
	$row=getrow($command);
	$filename = 'areceipt/'.$row['areceipt'];;
    $fileinfo = pathinfo($filename);
    header('Content-Type: application/pdf');
    header('Content-Length: ' . filesize($filename));
    readfile($filename);
}
function deletepdfar($userid){
	$myobj = new stdClass();
	$iddcrform = $_REQUEST['iddcrform'];
	$command = "CALL deleteacknowledgementreceipt($iddcrform, $userid)";
	$row=getrow($command);
	$myobj->iddcrform = $iddcrform;
	echo json_encode($myobj);
}

function getcert(){
	$myobj = new stdClass();
	$iddcrform = $_REQUEST['iddcrform'];
	$command = "select * from dcrform where iddcrform = $iddcrform";
	$row=getrow($command);
	if (empty($row['landclearance'])){ $myobj->landclearance ='';} else {$myobj->landclearance=$row['landclearance'];}
	$myobj->signedchief=$row['signedchief'];
	$myobj->signedparo=$row['signedparo'];
	$myobj->areceipt =$row['areceipt'];
	$myobj->signedar =$row['signedar'];
	$myobj->iddcrform = $iddcrform;
	echo json_encode($myobj);
}
function loadtemplate(){
	$myobj = new stdClass();
	$myobj->template = file_get_contents('landclearance/template.txt');
	echo json_encode($myobj);
	
}
function loadtemplate2(){
	$myobj = new stdClass();
	$myobj->template = file_get_contents('areceipt/template.txt');
	echo json_encode($myobj);
	
}
$myobj = validatetoken();
if (empty($myobj->userid)){
    header('Location:index.html?message='.urlencode("Invalid User"));
} else {$userid = $myobj->userid;}
$trans = $_REQUEST['trans'];

if ($trans=='getdetails'){
	getdetails();
}
if ($trans=='showpdf'){
	showpdf();
}
if ($trans=='deletepdf'){
	deletepdf($userid);
}
if ($trans=='showpdfar'){
	showpdfar();
}
if ($trans=='deletepdfar'){
	deletepdfar($userid);
}

if ($trans=='getcert'){
	getcert();
}
if ($trans=='loadtemplate'){
	loadtemplate();
}
if ($trans=='loadtemplate2'){
	loadtemplate2();
}

?>