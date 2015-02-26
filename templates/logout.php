<?php
	require_once('../controller/controller.php');	
	
	session_destroy();
	header('Location:../html/index.php');
?>