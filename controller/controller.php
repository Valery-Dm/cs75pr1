<?php
	require_once('../model/dbquery.php');

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

	function finduser($username){
		$query = 'SELECT username FROM users WHERE username=:username';
		return dbquery($query, array(':username' => $username));
	}

	function register($username, $password) {
		$query = 'INSERT INTO users (username, userpass) VALUES (:username, :password)';
		$password = password_hash($password, PASSWORD_DEFAULT);
		return dbquery($query, array(':username' => $username, 
									 ':password' => $password)) == 1;
	}

	function loguserin($username, $password) {
		$query = 'SELECT userpass FROM users WHERE username=:username';
		$result = dbquery($query, array(':username' => $username));
		if ($result == 2) {
			return 2;
		} elseif (!is_array($result)) {
			 return false;
		} else {
			return password_verify($password, $result['userpass']);
		}
	}

	session_start();
?>
