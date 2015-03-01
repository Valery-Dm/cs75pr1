<?php
	require_once('../model/dbquery.php');
	require_once('../model/call_api.php');

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
			$_SESSION['cash'] = $result['cash'];
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

	function buyshares($share=array()) {
		
		// first try to minus total price from user's account
		$charge_query = 'UPDATE users SET cash=cash-:total WHERE userid=:userid';
		
		$total = $share['total'] * $share['price'];
		$charge = dbquery($charge_query, array(':total' => $total, 
											   ':userid' => $_SESSION['userid']));
		if (!$charge) {
			return 'can\'t buy now, try again later';
		}
		// then try to add bought shares
		$query = 'INSERT INTO shares (sharesname, sharesq, sharesprice, sharesuser)
				  VALUES (:name, :total, :price, :userid)';
		$buy = dbquery($query, array(':name' => $share['name'], 
									 ':total' => $share['total'], 
									 ':price' => $share['price'], 
									 ':userid' => $_SESSION['userid']));
		if (!$buy) {
			// return spent money
			$return_query = 'UPDATE users SET cash=cash+:total WHERE userid=:userid';
			$return = dbquery($charge_query, array(':total' => $total));
			if (!$return) { /* send an email to admin */ }
			return 'can\'t buy now, try again later';
		} else {
			$_SESSION['cash'] = $_SESSION['cash'] - $total;
			return 'You\'ve bought ' 
						. $share['total'] . ' shares of ' 
						. $share['name'];
		}
		
	}

	function getjson($type, $query) {
		if ($type == 'quotes') {
			if (preg_match('/[^a-zA-Z]/', $query) or $query == '') {
				return 'invalid quote';
			} else {
				$url = "http://download.finance.yahoo.com/d/
						quotes.json?f=snl1&s=$query";
				$result = call_api($url);
				$count = count($result);
				if (!$result or $count == 1) {
					return 'Can\'t get quotes now';
				} elseif ($result[$count - 1] == 0.00) {
					return 'invalid quote';
				}
				if ($count == 4) {
					$name = $result[1] . $result[2];
				} else {
					$name = $result[1];
				}
				return array($name, $result[$count - 1]);
			}
		}
	}

	session_start();
?>
