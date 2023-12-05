<?php
	session_start();
    session_destroy();
    header("location: https://tars.buddemeyer.com.br/consulta_xml/login.php"); 
?>
