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
					if (!($checkname or $checkpassword or $confirmpassword)) {
						$finduser = finduser($_POST['username']);
						if ($finduser == 2) {
							$checkname = 'can\'t register now, try again later';
						} elseif (is_array($finduser)) {
							$checkname = 'Username already exists, choose another one';
						} elseif (!register($_POST['username'], $_POST['password'])) {
							$checkname = 'can\'t register now, try again later';
						} else {
							$_SESSION['username'] = $_POST['username'];
							header('Location:main.php');
						}
					}
				}
			}
			$hidden = '';
		}
		$alerts = array('name' => $checkname, 'pass' => $checkpassword, 'conf' => $confirmpassword, 'hidden' => $hidden);
		$title = $message = 'Registration form';
		$body = 'register';
	} else {
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
					$error = 'Can\'t login now, try again later';
				} elseif (!$loguserin) {
					$error = 'Wrong user name or password';
				} else {
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
