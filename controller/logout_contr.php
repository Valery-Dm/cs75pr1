<?php
	require_once('../html/main.php');	
	$page = null;
	session_destroy();
	header('Location:../html/index.php');
	exit;
?>