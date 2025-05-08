<?php
require "routines.php";
$iddcrform = $_REQUEST['iddcrform'];
$command = "select * from dcrform  where dcrform.iddcrform = $iddcrform";
$row = getrow($command);
$filename = 'docs/'.$row['docfilename'];;
//die($filename);
    $fileinfo = pathinfo($filename);
    $sendname = $fileinfo['filename'] . '.' . strtoupper($fileinfo['extension']);

    header('Content-Type: application/pdf');
    header('Content-Length: ' . filesize($filename));
    readfile($filename);
?>