<?php
require "routines.php";
function getdetails(){
	$table = new stdClass();
	$officecode = $_REQUEST['officecode'];
	$command = "select *, (select office.officename from office where office.idoffice=dcrform.municipal) as municipalname, (select office.officename from office where office.idoffice=dcrform.provincial) as provincialname, (select office.officename from office where office.idoffice=dcrform.regional) as regionalname from trackform left join dcrform on trackform.iddcrform = dcrform.iddcrform where trackform.status='CARPO' and trackform.officecode='$officecode' and trackform.enddate is null";
	$table->data = gettable($command);
	echo json_encode($table);
	
}
function showpdf(){
	$iddcrform= $_REQUEST['iddcrform'];
	$command = "select * from dcrform where iddcrform = $iddcrform";
	$row=getrow($command);
	$filename = 'ireport/'.$row['ireport'];;
    $fileinfo = pathinfo($filename);
    header('Content-Type: application/pdf');
    header('Content-Length: ' . filesize($filename));
    readfile($filename);
}
function deletepdf($userid){
	$myobj = new stdClass();
	$iddcrform = $_REQUEST['iddcrform'];
	$command = "CALL deletecarpocert($iddcrform, $userid)";
	$row=getrow($command);
	$myobj->iddcrform = $iddcrform;
	echo json_encode($myobj);
}
function getcert(){
	$myobj = new stdClass();
	$iddcrform = $_REQUEST['iddcrform'];
	$command = "select * from dcrform where iddcrform = $iddcrform";
	$row=getrow($command);
	if (empty($row['ireport'])){ $myobj->ireport ='';} else {$myobj->ireport=$row['ireport'];}
	$myobj->signedcarpocert =$row['signedcarpocert'];
	$myobj->iddcrform = $iddcrform;
	echo json_encode($myobj);
}
function loadtemplate(){
	$myobj = new stdClass();
	$myobj->template = file_get_contents('ireport/template.txt');
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
if ($trans=='getcert'){
	getcert();
}
if ($trans=='loadtemplate'){
	loadtemplate();
}