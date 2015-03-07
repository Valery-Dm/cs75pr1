<?php
	require_once('../controller/login.php');

	// select page to show
	if (isset($_SESSION['userid'])) {
		// move registered user to his portfolio
		header('Location:main.php?page=Portfolio');
	} 
	// Register page functionality here
	if (isset($_GET['form']) and $_GET['form'] == 'register') {
		// set registration form variables
		$title = $message = 'Registration form';
		$body = 'register';
		// empty alert messages 
		$alerts = array('name' => '',
						'pass' => '',
						'conf' => '',
						'hidden' => 'hidden');
		// if form is submitted
		if (isset($_POST['username'])) {
			$register = new Register($_POST['username'],
									 $_POST['password'],
									 $_POST['password_conf']);
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
	} else {
		// Logon page functionality
		$title = 'Login form';
		$message = 'Please login';
		$body = 'login';
		$alerts = array('error' => '',
						'hidden' => 'hidden');
		// on form submit
		if (isset($_POST['username'])) {
			$login = new Login($_POST['username'], 
							   $_POST['password']);
			// login on success
			if ($login->url) {
				header('Location:'.$login->url);
				exit;
			}
			$alerts = array('error' => $login->namealert,
							'hidden' => '');
		}
	}

	// build page
	render('header', array('title' => $title, 'message' => $message));
	render($body, $alerts);
	render('footer');
?>
