<?php
	require_once('../controller/controller.php');

	if (!isset($_SESSION['username'])) {
		header('Location:index.php');
	}

	$title = 'Portfolio';
	$message = 'Welcome, ' . $_SESSION['username'];
	$body = 'portfolio';

	// build page
	render('header_p', array('title' => $title, 'message' => $message));
	render($body);
	render('footer');
?>