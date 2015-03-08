<?php
	require_once('../controller/controller.php');
	// destroy current session and exit
	$_SESSION = [];
	session_destroy();
	header('Location:../html/index.php');
	exit;
?>