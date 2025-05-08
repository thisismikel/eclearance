<?php
require "routines.php";

function updatedetails($userid){
	$subject=htmlspecialchars(strtoupper($_REQUEST['subject']),ENT_QUOTES,"UTF-8");
	$to1=$_REQUEST['to'];
	$to='';
	foreach($to1 as $val){
		if (!empty($val)) {$to.=$val.';';}
	}
	$iddcrform=$_REQUEST['iddcrform'];
	$referenceno=htmlspecialchars(strtoupper($_REQUEST['referenceno']),ENT_QUOTES,"UTF-8");
	$datefiled=$_REQUEST['datefiled'];
	$urgent=$_REQUEST['urgent'];
	if (empty($urgent)) {$urgent='';}
	$facebook=$_REQUEST['facebook'];
	if (empty($facebook)) {$facebook='';}
	$email=$_REQUEST['email'];
	if (empty($email)) {$email='';}
	$text=$_REQUEST['text'];
	if (empty($text)) {$text='';}
	$call=$_REQUEST['call'];
	if (empty($call)) {$call='';}
	$complaint=$_REQUEST['complaint'];
	if (empty($complaint)) {$complaint='';}
	$request=$_REQUEST['request'];
	if (empty($request)) {$request='';}
	$suggestion=$_REQUEST['suggestion'];
	if (empty($suggestion)) {$suggestion='';}
	$commendation=$_REQUEST['commendation'];
	if (empty($commendation)) {$commendation='';}
	$followup=$_REQUEST['followup'];
	if (empty($followup)) {$followup='';}
	$inquiry=$_REQUEST['inquiry'];
	if (empty($inquiry)) {$inquiry='';}
	$others=$_REQUEST['others'];
	if (empty($others)) {$others='';}
	$odetails=htmlspecialchars(strtoupper($_REQUEST['odetails']),ENT_QUOTES,"UTF-8");
	$details=$_REQUEST['details'];
	$preparedby=htmlspecialchars(strtoupper($_REQUEST['preparedby']),ENT_QUOTES,"UTF-8");
	$remarks=$_REQUEST['remarks'];
	$chiefofstaff=htmlspecialchars(strtoupper($_REQUEST['chiefofstaff']),ENT_QUOTES,"UTF-8");
	$command ="CALL updatedcrform($iddcrform, '$subject', '$to', '$referenceno', '$datefiled' , '$urgent', '$facebook', '$email', '$text', '$call', '$complaint', '$request', '$suggestion', '$commendation', '$followup', '$inquiry', '$others', '$odetails', '$details', '$preparedby', '$remarks', '$chiefofstaff', $userid)";
	getrow($command,false);
	$myobj->iddcrform = $iddcrform;
	echo json_encode($myobj);

}
function getdetails(){
	$officecode = $_REQUEST['officecode'];
	$command = "select * from trackform left join dcrform on trackform.iddcrform = dcrform.iddcrform where trackform.status='RECEIVED' and trackform.officecode = '$officecode' and trackform.enddate is null";
	$table->data = gettable($command);
	echo json_encode($table);
	
}
function route($userid){
	$idtrackform = $_REQUEST['idtrackform'];
	$iddcrform = $_REQUEST['iddcrform'];
	$to = $_REQUEST['to'];
	$arecipient = explode(";", $to);
	$command ="CALL tagtrackform($idtrackform, $userid)";
	getrow($command,false);
	foreach ($arecipient as $value) {
		
		if (!empty($value)){
			$command ="CALL insertcasedoc($iddcrform, '$value' , $userid)";
			
			$row = getrow($command);
			$idcasedoc = $row['@id'];
			$command ="CALL inserttrackform('$value', 'RECEIVED', '', $iddcrform, $idcasedoc, $userid)";
			getrow($command,false);
		}
	}
	$myobj->iddcrform=$iddcrform;
	echo json_encode($myobj);
}


$myobj = validatetoken();
if (empty($myobj->userid)){
    header('Location:index.html?message='.urlencode("Invalid User"));
} else {$userid = $myobj->userid;}
$trans = $_REQUEST['trans'];

if ($trans=='UPDATE'){
	updatedetails($userid);
}
if ($trans=='getdetails'){
	getdetails();
}


if ($trans=='route'){
	route($userid);
}
?>