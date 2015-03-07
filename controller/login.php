<?php
	require_once('../controller/controller.php');

	/*
	* Class should be constructed with user name
	* and password. Public variables will be populated 
	* either with URL to porfolio (if logon was successfull) 
	* or alert message to be shown on the Login page.
	*/
	class Login {
		public $url = '';
		public $namealert = '';

		function __construct($name, $pass) {
			$len = strlen($name);
			if ($len < 3 or $len > 10 or strlen($pass) < 4) {
				$this->namealert = 'Wrong user name or password';
			} else {
				$this->namealert = $this->loguserin($name, $pass);
			}
		}
		/*
		* Check user name and password. Return string.
		* Log user in on success.
		*/
		function loguserin($name, $pass) {
			$query = 'SELECT * FROM users WHERE username=:username';
			// call DB
			$login = dbquery([$query], [[':username' => $name]]);
			if ($login == 2 or $login === false) {
				// in case of db error
				return "can't login now, try again later";
			} elseif (count($login) == 0 or
					  !password_verify($pass, $login[0]['userpass'])) {
				// if user doesn't exist or if wrong password provided
				return 'Wrong user name or password';
			} else {
				// populate global array with user's data
				$_SESSION['userid'] = $login[0]['userid'];
				$_SESSION['username'] = htmlspecialchars($login[0]['username']);
				// assign url variable
				$this->url = 'main.php?page=Portfolio';
				return '';
			}
		}
	}

	/*
	* Extends Login class.
	* Class should be constructed with user name,
	* password and confirmation password.
	* Public variables will be populated either with
	* URL to porfolio (if logon was successfull) 
	* or alert messages to be shown on the Register page.
	*/
	class Register extends Login {
		public $passalert = '';
		public $confalert = '';

		function __construct($name, $pass, $conf) {
			// validate input
			$cname = $this->checkname($name);
			$cpass = $this->checkpassword($pass);
			// populate variables
			if ($cname) {
				$this->namealert = $cname;
			} elseif ($cpass) {
				$this->passalert = $cpass;
			} elseif ($pass !== $conf) {
				$this->confalert = 'passwords do not match';
			} else {
				// check if user name already exists
				$find = $this->finduser($name);
				if ($find) {
					$this->namealert = $find;
				} else {
					// try to register and log user in
					$register = $this->register($name, $pass);
					if ($register) {
						$this->namealert = $register;
					}
				}
			}
		}
		/*
		* Validate user input for name field. 
		* Return string.
		*/
		function checkname($name) {
			$len = strlen($name);
			if ($len < 3) {
				return 'name is too short or not entered';
			} elseif (preg_match('/[\W_]/', $name)) {
				return 'use only digits and english letters';
			} elseif ($len > 10){
				return 'name is too long';
			}
			return '';
		}
		/*
		* Validate user input for password field. 
		* Return string or false.
		*/
		function checkpassword($pass) {
			if (strlen($pass) < 4) {
				return 'password is too short or not entered';
			} elseif (!preg_match('/(?=.*\d)(?=.*[A-Z])[\S]/', $pass)){
				return 'use at least one digit and one uppercase letter';
			}
			return '';
		}
		/*
		* Check if user name already exists. 
		* Return string.
		*/
		function finduser($username){
			$query = 'SELECT username FROM users 
					  WHERE username=:username';
			$finduser = dbquery([$query], [[':username' => $username]]);
			if ($finduser == 2) {
				// if we have a 'connect to database' error on usercheck
				return "can't register now, try again later";
			} elseif (count($finduser) == 1) {
				return 'Username already exists, choose another one';
			}
			return '';
		}
		/*
		* Hash user's password and calls for db. 
		* Return string on error or calls for login function.
		*/
		function register($username, $password) {
			$query = 'INSERT INTO users (username, userpass) 
					  VALUES (:username, :password)';
			$password_hash = password_hash($password, PASSWORD_DEFAULT);
			// call DB
			$register = dbquery([$query], [[':username' => $username, 
										    ':password' => $password_hash]]);
			if (!$register) {
				// if we have a 'connect to database' error on register
				return "can't register now, try again later";
			} else {
				// try to login
				return $this->loguserin($username, $password);
			}
		}
	}
?>