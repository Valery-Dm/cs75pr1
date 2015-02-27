<?php
	
	require_once('../controller/controller.php');
	
	// select page to show
	if (isset($_SESSION['username'])) {
		// move registered user to his portfolio
		header('Location:main.php');
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
					$confirmpassword = confirmpassword($_POST['password'], $_POST['password_conf']);
					// if form has valid values
					if (!($checkname or $checkpassword or $confirmpassword)) {
						// check if user exists
						$finduser = finduser($_POST['username']);
						if ($finduser == 2) {
							// if it's connect to database error on usercheck
							$checkname = 'can\'t register now, try again later';
						} elseif (is_array($finduser)) {
							$checkname = 'Username already exists, choose another one';
						} elseif (!register($_POST['username'], $_POST['password'])) {
							// if it's connect to database error on register
							$checkname = 'can\'t register now, try again later';
						} else {
							// move to portfolio
							$_SESSION['username'] = $_POST['username'];
							header('Location:main.php');
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
			if (10 < $userlen or $userlen < 3 or strlen($_POST['password']) < 4) {
				$error = 'Wrong user name or password';
			} else {
				// try to log user in
				$loguserin = loguserin($_POST['username'], $_POST['password']);
				if ($loguserin === 2) {
					// in case of db error
					$error = 'Can\'t login now, try again later';
				} elseif (!$loguserin) {
					$error = 'Wrong user name or password';
				} else {
					// move registered user to his portfolio
					$_SESSION['username'] = $_POST['username'];
					header('Location:main.php');
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
