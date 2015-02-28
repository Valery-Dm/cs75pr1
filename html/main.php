<?php
	require_once('../controller/controller.php');

	if (!isset($_SESSION['userid'])) {
		header('Location:index.php');
	}
	$username = $_SESSION['username'];
	//getuserinfo($username);
	$title = 'Portfolio';
	$message = 'Welcome, ' . $username . $_SESSION['userid'];
	$body = 'portfolio';

	// build page
	render('header_p', array('title' => $title, 'message' => $message));
	render($body);
	render('footer');
?>