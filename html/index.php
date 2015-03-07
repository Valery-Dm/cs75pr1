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
		$register = new Register($_POST);
		// login on success
		if ($register->url) {
			header('Location:'.$register->url);
			exit;
		}
		$alerts = array('name' => $register->namealert,
						'pass' => $register->passalert,
						'conf' => $register->confalert,
						'hidden' => '');
	}

	// build page
	render('header', array('title' => $page->title, 
						   'message' => $page->message));
	render($page->body, $alerts);
	render('footer');
?>
