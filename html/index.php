<?php
	require_once('../controller/login.php');

	// select page to show
	if (isset($_SESSION['userid'])) {
		// move registered user to his portfolio
		header('Location:main.php?page=Portfolio');
	} 
	// register page functionality here
	if (isset($_GET['form']) and $_GET['form'] == 'register') {
		// prepare empty messages 
		$checkname = $checkpassword = $confirmpassword = '';
		$hidden = 'hidden';
		// if form submited validate input
		if (isset($_POST['username'])) {
			$checkname = checkname($_POST['username']);
			if (isset($_POST['password'])) {
				$checkpassword = checkpassword($_POST['password']);
				if (isset($_POST['password_conf'])) {
					$confirmpassword = confirmpassword($_POST['password'], 
											$_POST['password_conf']);
					// if form has valid values
					if (!($checkname or $checkpassword or $confirmpassword)) {
						// check if user exists
						$checkname = finduser($_POST['username']);
						if ($checkname == 'nouser') {
							// try to register and login
							$checkname = register($_POST['username'], 
												  $_POST['password']);
							if ($checkname == 'login') {
								header('Location:main.php?page=Portfolio');
							}
						}
					}
				}
			}
			$hidden = '';
		}
		// set registration form variables and collect messages if any
		$alerts = array('name' => $checkname, 'pass' => $checkpassword, 'conf' => $confirmpassword, 'hidden' => $hidden);
		$title = $message = 'Registration form';
		$body = 'register';
	} else {
		// index page functionality
		$error = '';
		$hidden = 'hidden';
		if (isset($_POST['username']) and isset($_POST['password'])) {
			// don't check further if credentials is out of allowed bounds
			$userlen = strlen($_POST['username']);
			if (10 < $userlen or $userlen < 3 or 
				strlen($_POST['password']) < 4) {
				$error = 'Wrong user name or password';
			} else {
				// try to log user in
				$error = loguserin($_POST['username'], $_POST['password']);
				if ($error == 'login') {
					header('Location:main.php?page=Portfolio');
				}
			}
			$hidden = '';
		}
		$title = 'Login form';
		$message = 'Please login';
		$body = 'login';
		$alerts = array('error' => $error, 'hidden' => $hidden);
	}

	// build page
	render('header', array('title' => $title, 'message' => $message));
	render($body, $alerts);
	render('footer');
?>
