<?php
	
	require_once('../controller/controller.php');
	
	// select page to show
	if (isset($_SESSION['username'])) {
		header('Location:main.php');
	} 
	
	if (isset($_GET['form']) and $_GET['form'] == 'register') {
		$checkname = $checkpassword = $confirmpassword = '';
		$hidden = 'hidden';
		if (isset($_POST['username'])) {
			$checkname = checkname($_POST['username']);
			if (isset($_POST['password'])) {
				$checkpassword = checkpassword($_POST['password']);
				if (isset($_POST['password_conf'])) {
					$confirmpassword = confirmpassword($_POST['password'], $_POST['password_conf']);
					if ($checkname or $checkpassword or $confirmpassword) {
						$hidden = '';
					} elseif (finduser($_POST['username']) == 1) {
						$checkname = 'Username already exist, choose another one';
						$hidden = '';
					} elseif (register($_POST['username'], $_POST['password']) == 1) {
						$_SESSION['username'] = $_POST['username'];
						header('Location:main.php');
					} else {
						$checkname = 'can\'t register now, try again later';
						$hidden = '';
					}
				}
			}
		}
		$alerts = array('name' => $checkname, 'pass' => $checkpassword, 'conf' => $confirmpassword, 'hidden' => $hidden);
		$title = $message = 'Registration form';
		$body = 'register';
	} else {
		$error = '';
		$hidden = 'hidden';
		if (isset($_POST['username']) and isset($_POST['password'])) {
			if (strlen($_POST['username']) < 3 or strlen($_POST['password']) < 4) {
				$error = 'Wrong user name or password';
				$hidden = '';
			} else {
				
			}
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
