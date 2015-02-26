<?php
	// connect to database
	try {
		$DBUSER = 'root';
		$DBPASS = '';
		$DSN = "mysql:host=localhost;dbname=cs75finance;";
		$pdo = new PDO($DSN, $DBUSER, $DBPASS);
	} catch (PDOException $e) {
		//echo $e-getCode();
	}


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

	function finduser($username) {
		global $pdo;
		$query = 'SELECT username FROM users WHERE username=:username';
		try {
			$stmt = $pdo->prepare($query);
			$stmt->bindValue(':username', $username);
			$stmt->execute();
			return $stmt->rowCount() > 0;
		} catch (PDOException $e) {
			return $e->getCode();
		}
		return false;
	}

	function register($username, $password) {
		global $pdo;
		$query = 'INSERT INTO users (username, userpass) VALUES (:username, :password)';
		$password = password_hash($password, PASSWORD_DEFAULT);
		try {
			$stmt = $pdo->prepare($query);
			$stmt->bindValue(':username', $username);
			$stmt->bindValue(':password', $password);
			return $stmt->execute();
		} catch (PDOException $e) {
			return $e->getCode();
		}
		return false;
	}

	session_start();
?>
