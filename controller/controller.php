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
		$finduser = dbquery($query, array(':username' => $username));
		if ($finduser == 2) {
			// if we have a 'connect to database' error on usercheck
			return 'can\'t register now, try again later';
		} elseif (is_array($finduser)) {
			return 'Username already exists, choose another one';
		} else {
			return 'nouser';
		}
	}

	function register($username, $password) {
		$query = 'INSERT INTO users (username, userpass) 
				  VALUES (:username, :password)';
		$password_hash = password_hash($password, PASSWORD_DEFAULT);
		$register = dbquery($query, array(':username' => $username, 
										  ':password' => $password_hash));
		if (!$register) {
			// if we have a 'connect to database' error on register
			return 'can\'t register now, try again later';
		} else {
			// try to login
			$loguserin = loguserin($username, $password);
			return $loguserin;
		}
	}

	function loguserin($username, $password) {
		$query = 'SELECT * FROM users WHERE username=:username';
		$result = dbquery($query, array(':username' => $username));
		if ($result == 2) {
			// in case of db error
			return 'Can\'t login now, try again later';
		} elseif (!is_array($result) or 
				  !password_verify($password, $result['userpass'])) {
			return 'Wrong user name or password';
		} else {
			$_SESSION['userid'] = $result['userid'];
			$_SESSION['username'] = $result['username'];
			return 'login';
		}
	}

	function getuserinfo($userid) {
		$query = 'SELECT * FROM shares WHERE sharesuser=:userid';
		$result = dbquery($query, array(':userid' => $userid));
		if (!$result) {
			return 'You have no shares in your portfolio';
		} elseif ($result == 2) {
			return 'Can\'t get your data right now, try to login later';
		} else {
			return $result;
		}
	}

	session_start();
?>
