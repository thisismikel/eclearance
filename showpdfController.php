<?php


require 'routines.php';
$myobj = validatetoken();
if (empty($myobj->userid)){
    header('Location:index.html?message='.urlencode("Invalid User"));
} else {$userid = $myobj->userid;}

$idorderdoc= $_REQUEST['idorderdoc'];
$command ="select * from orderdoc where idorderdoc = $idorderdoc";
$row = getrow($command);
$filename = $row['filename'];

$filename = 'docs/'.$row['filename'];;

    $fileinfo = pathinfo($filename);
    $sendname = $fileinfo['filename'] . '.' . strtoupper($fileinfo['extension']);

    header('Content-Type: application/pdf');
    header('Content-Length: ' . filesize($filename));
    readfile($filename);

	
?>