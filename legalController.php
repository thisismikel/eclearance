<?php
require "routines.php";

function getdetails(){
	$table = new stdClass();
	$officecode = $_REQUEST['officecode'];
	$command = "select *, (select office.officename from office where office.idoffice=dcrform.municipal) as municipalname, (select office.officename from office where office.idoffice=dcrform.provincial) as provincialname, (select office.officename from office where office.idoffice=dcrform.regional) as regionalname from trackform left join dcrform on trackform.iddcrform = dcrform.iddcrform where trackform.status='LEGAL' and trackform.officecode='$officecode' and trackform.enddate is null";
	$table->data = gettable($command);
	echo json_encode($table);
	
}

function forDARPO($userid){
	$myobj = new stdClass();
	$idtrackform = $_REQUEST['idtrackform'];
	$iddcrform = $_REQUEST['iddcrform'];
	$provincial = $_REQUEST['provincial'];
	$officename = $_REQUEST['officename'];
	$details = htmlspecialchars($_REQUEST['details'],ENT_QUOTES,"UTF-8");
	$command = "select * from office where idoffice=$provincial";
	$noffice = getrow($command);
	$officecode = $noffice['officecode'];
	$command ="CALL tagtrackform($idtrackform,'$details', $userid)";
	
	getrow($command,false);
	$command ="CALL inserttrackform('$officecode', 'DARPO', '', $iddcrform, NULL, $userid)";
	
	getrow($command,false);
	$myobj->iddcrform=$iddcrform;
	$myobj->officecode=$officecode;
	$myobj->officename=$officename;
	$myobj->role='DARPO';
	echo json_encode($myobj);
}
function forlegalhead($userid){
	$myobj = new stdClass();
	$idtrackform = $_REQUEST['idtrackform'];
	$iddcrform = $_REQUEST['iddcrform'];
	$regional = $_REQUEST['regional'];
	$officename = $_REQUEST['officename'];
	$details = htmlspecialchars($_REQUEST['details'],ENT_QUOTES,"UTF-8");
	$command = "select * from office where idoffice=$regional";
	$noffice = getrow($command);
	$officecode = $noffice['officecode'];
	$command ="CALL tagtrackform($idtrackform,'$details', $userid)";
	getrow($command,false);
	$command ="CALL inserttrackform('$officecode', 'LEGALHEAD', '', $iddcrform, NULL, $userid)";
	getrow($command,false);
	$myobj->iddcrform=$iddcrform;
	$myobj->officecode=$officecode;
	$myobj->officename=$officename;
	$myobj->role="LEGALHEAD";
	echo json_encode($myobj);
}
function getorders(){
	$table = new stdClass();
	$iddcrform = $_REQUEST['iddcrform'];
	$command = "select * from orderdoc where iddcrform=$iddcrform";
	$table = gettable($command);
	echo json_encode($table);
	
}
function deletepdf($userid){
	$myobj = new stdClass();
	$idorderdoc=$_REQUEST['idorderdoc'];
	$filename=$_REQUEST['filename'];
	$iddcrform=$_REQUEST['iddcrform'];
	$command = "CALL deleteorderdoc($idorderdoc, $userid)";
	getrow($command,false);
	$myobj->iddcrform=$iddcrform;
	$file_with_path='docs/'.$filename;
	
	if (file_exists($file_with_path)) {
		unlink($file_with_path);
	}
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

if ($trans=='forDARPO'){
	forDARPO($userid);
}
if ($trans=='forlegalhead'){
	forlegalhead($userid);
}
if ($trans=='getorders'){
	getorders();
}
if ($trans=='deletepdf'){
	deletepdf($userid);
}


?>