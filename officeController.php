<?php
require "routines.php";
function insertdetails($userid){
	$myobj = new stdClass();
	$officename = htmlspecialchars(strtoupper($_REQUEST['officename']),ENT_QUOTES,"UTF-8");
	$officecode = htmlspecialchars(strtoupper($_REQUEST['officecode']),ENT_QUOTES,"UTF-8");
	$location = htmlspecialchars(strtoupper($_REQUEST['location']),ENT_QUOTES,"UTF-8");
	$head = htmlspecialchars(strtoupper($_REQUEST['head']),ENT_QUOTES,"UTF-8");
	$active=$_REQUEST['active'];
	$officetype = $_REQUEST['officetype'];
	$command ="CALL insertoffice('$officecode', '$officename', '$location', '$head', '$active','$officetype', $userid)";
	$myobj->idoffice = -1;
	$result = getrow($command);
	$myobj->idoffice = $result['@id'];
	$myobj->officename = $officename;
	$myobj->officecode=$officecode;
	$myobj->location=$location;
	$myobj->head=$head;
	$myobj->active=$active;
	$myobj->trans='ADD';
	echo json_encode($myobj);
}
function updatedetails($userid){
	$myobj = new stdClass();
	$officename = htmlspecialchars(strtoupper($_REQUEST['officename']),ENT_QUOTES,"UTF-8");
	$officecode = htmlspecialchars(strtoupper($_REQUEST['officecode']),ENT_QUOTES,"UTF-8");
	$location = htmlspecialchars(strtoupper($_REQUEST['location']),ENT_QUOTES,"UTF-8");
	$head = htmlspecialchars(strtoupper($_REQUEST['head']),ENT_QUOTES,"UTF-8");
	$active=$_REQUEST['active'];
	$idoffice=$_REQUEST['idoffice'];
	$officetype = $_REQUEST['officetype'];
	$command ="CALL updateoffice($idoffice, '$officecode', '$officename', '$location', '$head', '$active','$officetype', $userid)";
	getrow($command,false);
	$myobj->idoffice = $idoffice;
	echo json_encode($myobj);

}
function getdetails(){
	$table = new stdClass();
	$command = "select * from office order by `officecode`";
	$table->data = gettable($command);
	echo json_encode($table);
	
}

function deletedetail($userid){
	$myobj = new stdClass();
	$idoffice = $_REQUEST['idoffice'];
	$command ="CALL deleteoffice($idoffice, $userid)";
	$myobj->idoffice=$idoffice;
	getrow($command,false);
	echo json_encode($myobj);
}
$myobj = validatetoken();
if (empty($myobj->userid)){
    header('Location:index.html?message='.urlencode("Invalid User"));
} else {$userid = $myobj->userid;}
$trans = $_REQUEST['trans'];
if ($trans=='ADD'){
	insertdetails($userid);
}
if ($trans=='UPDATE'){
	updatedetails($userid);
}
if ($trans=='getdetails'){
	getdetails();
}
if ($trans=='delete'){
	deletedetail($userid);
}
if ($trans=='course'){
	getcourse();
}
?>