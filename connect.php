<?php

	 $conn = new mysqli("localhost", "root", "JBarber2014","dar");
	

     if ($conn->connect_error) {
        die('Connect Error (' . $conn->connect_errno . ') ' . $conn->connect_error);
     }
	 $conn->set_charset("utf8");

  

?>
