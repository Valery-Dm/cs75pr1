<?php
	function render($template, $data=array()) {
		$path = __DIR__ . '/../templates/' . $template . '.php';
		if (file_exists($path)) {
			extract($data);
			require($path);
		}
	}

	function checkname($name) {
		if (strlen($name) > 10) {
			return 'name is too long';
		} elseif (strlen($name) < 3) {
			return 'name is too short or not entered';
		} elseif (preg_match('/[\W_]/', $name)){
			return 'use only digits and english letters';
		}
		return false;
	}

	function checkpassword($pass) {
		if (strlen($pass) < 4) {
			return 'password is too short or not entered';
		} elseif (!preg_match('/(?=.*\d)(?=.*[A-Z])[\S]/', $pass)){
			return 'use at least one digit and one uppercase letter';
		}
		return false;
	}

	function confirmpassword($pass, $conf) {
		if ($pass !== $conf) {
			return 'passwords do not match';
		}
		return false;
	}

	session_start();
?>
