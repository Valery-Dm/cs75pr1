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
					} else {
						if (register($_POST['username'], $_POST['password'])) {
							$_SESSION['username'] = $_POST['username'];
							header('Location:main.php');
						} else {
							echo 'can\'t register now, try again later';
						}
					}
				}
			}
		}
		$alerts = array('name' => $checkname, 'pass' => $checkpassword, 'conf' => $confirmpassword, 'hidden' => $hidden);
		$title = $message = 'Registration form';
		$body = 'register';
	} else {
		$title = 'Login form';
		$message = 'Please login';
		$body = 'login';
		$alerts = array();
	}

	// build page
	render('header', array('title' => $title, 'message' => $message));
	render($body, $alerts);
	render('footer');
?>
