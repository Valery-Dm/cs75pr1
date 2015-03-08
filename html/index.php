<?php
	require_once('../controller/login.php');

	// select page to show
	if (isset($_SESSION['userid'])) {
		// move registered user to his portfolio
		header('Location:main.php?page=Portfolio');
	} 
	// construct page	
	$pagename = (isset($_GET['form'])) ? $_GET['form'] : 'login';
	$page = new Guest($pagename);
	$alerts = $page->alerts;

	// on form submit
	if (isset($_POST['username'])) {
		if (count($_POST) == 2) {
			$user = new Login($_POST['username'],
							  $_POST['password']);
		} elseif (count($_POST) == 3) {
			$user = new Register($_POST['username'],
								 $_POST['password'],
								 $_POST['password_conf']);
		}
		
		// login on success
		if ($user->url) {
			header('Location:'.$user->url);
			$user = null;
			exit;
		}
		$alerts = $user->alerts;
	}

	// build page
	render('header', array('title' => $page->title, 
						   'message' => $page->message));
	render($page->body, $alerts);
	render('footer');
?>
