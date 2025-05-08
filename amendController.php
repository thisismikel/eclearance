<?php
require "routines.php";

function getdetails(){
	$table = new stdClass();
	$officecode = $_REQUEST['officecode'];
	$command = "select *, (select office.officename from office where office.idoffice=dcrform.municipal) as municipalname, (select office.officename from office where office.idoffice=dcrform.provincial) as provincialname, (select office.officename from office where office.idoffice=dcrform.regional) as regionalname from trackform left join dcrform on trackform.iddcrform = dcrform.iddcrform where trackform.status='AMEND' and trackform.officecode='$officecode' and trackform.enddate is null";
	$table->data = gettable($command);
	echo json_encode($table);
	
}
function getoffices(){
	$myobj = new stdClass();
	$officecode = $_REQUEST['officecode'];
	$command = "select * from office where officecode = '$officecode'";
	$provincial = getrow($command);
	$myobj->error = false;
	if (empty($provincial)){
		$myobj->error = true;
	} else {
		if ($provincial['officetype']!='PROVINCIAL'){
			$myobj->error = true;
		}
	}
	//die('province:'.$provincial['officetype']);
	if (!$myobj->error){
		$myobj->provincial = $provincial;
		$idofficeprovincial = $provincial['idoffice'];
		//$myobj->idofficemunicipal = $idofficemunicipal;
		$command = "select office.* from provincialoffice left join office on provincialoffice.idofficemunicipal = office.idoffice where provincialoffice.idofficeprovincial = $idofficeprovincial";
		$municipal = gettable($command);
		//$idofficeprovincial = $row['idofficeprovincial'];
		//$command = "select * from office where idoffice = $idofficeprovincial";
		//$provincial = getrow($command);
		//$myobj->provincial = $provincial;
		$myobj->municipal = $municipal;
		//$myobj->idofficemunicipal = $idofficemunicipal;
		$myobj->idofficeprovincial = $idofficeprovincial;
		$myobj->idofficeregional = 52;
		$command = "select * from office where idoffice = 52";
		$regional = getrow($command);
		$myobj->regional = $regional;
	}
	
	echo json_encode($myobj);
}
function tagamend($userid){
	$myobj = new stdClass();
	$iddcrform = $_REQUEST['iddcrform'];
	$command ="CALL tagamended($iddcrform, $userid)";
	getrow($command, false);
	$myobj->result = "Ok";
	echo json_encode($myobj);

}


function seen($userid){
	$myobj = new stdClass();
	$idtrackform = $_REQUEST['idtrackform'];
	$command ="CALL seen($idtrackform,$userid)";
	$result = getrow($command);
	$myobj->seen = $result['@seen'];
	echo json_encode($myobj);

}
function getcase(){
	$myobj = new stdClass();
	$iddcrform = $_REQUEST['iddcrform'];
	$municipal = $_REQUEST['municipal'];
	$regional = $_REQUEST['regional'];
	$provincial = $_REQUEST['provincial'];
	$command = "select trackform.*, users.fullname, (select officename from office where office.officecode = trackform.officecode) as officename,(select users.fullname from users where users.userid = trackform.seenby) as firstseen from trackform left join users on trackform.userid = users.userid where iddcrform =  $iddcrform order by idtrackform" ;
	
	$myobj->trackform = gettable($command);
	$myobj->municipal = $municipal;
	$myobj->regional = $regional;
	$myobj->provincial =$provincial;
	echo json_encode($myobj);
}

function forAmend($userid){
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
	$command ="CALL inserttrackform('$officecode', 'AMEND', '', $iddcrform, NULL, $userid)";
	
	getrow($command,false);
	$myobj->iddcrform=$iddcrform;
	$myobj->officecode=$officecode;
	$myobj->officename=$officename;
	$myobj->role='SECRETARIAT';
	echo json_encode($myobj);
}
function forPARO($userid){
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
	$command ="CALL inserttrackform('$officecode', 'PARO', '', $iddcrform, NULL, $userid)";
	
	getrow($command,false);
	$myobj->iddcrform=$iddcrform;
	$myobj->officecode=$officecode;
	$myobj->officename=$officename;
	$myobj->role='PARO';
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
if ($trans=='offices'){
	getoffices();
}
if ($trans=='updatetrack'){
		updatetrack($userid);
}
if ($trans=='forLegal'){
	forLegal($userid);
}
if ($trans=='forPARO'){
	forPARO($userid);
}
if ($trans=='forAmend'){
	forAmend($userid);
}
if ($trans=='tagamend'){
	tagamend($userid);
}
if ($trans=='seen'){
	seen($userid);
}
if ($trans=='getcase'){
	getcase();
}

?>