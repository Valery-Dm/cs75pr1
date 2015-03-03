<?php
	require_once('../controller/controller.php');

	/*
	* Validates user input for name field. 
	* Returns string or false.
	*/
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

	/*
	* Validates user input for password field. 
	* Returns string or false.
	*/
	function checkpassword($pass) {
		if (strlen($pass) < 4) {
			return 'password is too short or not entered';
		} elseif (!preg_match('/(?=.*\d)(?=.*[A-Z])[\S]/', $pass)){
			return 'use at least one digit and one uppercase letter';
		}
		return false;
	}

	/*
	* Compares passwords. 
	* Returns string or false.
	*/
	function confirmpassword($pass, $conf) {
		if ($pass !== $conf) {
			return 'passwords do not match';
		}
		return false;
	}

	/*
	* Checks if user's name already exists. 
	* Returns string.
	*/
	function finduser($username){
		$query = 'SELECT username FROM users WHERE username=:username';
		$finduser = dbquery([$query], [[':username' => $username]]);
		if ($finduser == 2) {
			// if we have a 'connect to database' error on usercheck
			return 'can\'t register now, try again later';
		} elseif (count($finduser) == 1) {
			return 'Username already exists, choose another one';
		} else {
			return 'nouser';
		}
	}

	/*
	* Hashes user's password and calls for db. 
	* Returns string on error or calls for login function.
	*/
	function register($username, $password) {
		$query = 'INSERT INTO users (username, userpass) 
				  VALUES (:username, :password)';
		$password_hash = password_hash($password, PASSWORD_DEFAULT);
		$register = dbquery([$query], [[':username' => $username, 
									  ':password' => $password_hash]]);
		if (!$register) {
			// if we have a 'connect to database' error on register
			return 'can\'t register now, try again later';
		} else {
			// try to login
			$loguserin = loguserin($username, $password);
			return $loguserin;
		}
	}

	/*
	* Checks user's name and password. Returns string.
	*/
	function loguserin($username, $password) {
		$query = 'SELECT * FROM users WHERE username=:username';
		$login = dbquery([$query], [[':username' => $username]]);
		if ($login == 2 or $login === false) {
			// in case of db error
			return 'Can\'t login now, try again later';
		} elseif (count($login) == 0 or
				  !password_verify($password, $login[0]['userpass'])) {
			// if user doesn't exist or if wrong password provided
			return 'Wrong user name or password';
		} else {
			// populate global array with user's data
			$_SESSION['userid'] = $login[0]['userid'];
			$_SESSION['username'] = $login[0]['username'];
			$_SESSION['cash'] = $login[0]['cash'];
			return 'login';
		}
	}

?>