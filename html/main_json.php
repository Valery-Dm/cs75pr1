<?php
	require_once('../controller/login.php');

	header('content-type: application/json; charset=utf-8');

	if (isset($_GET['link'])) {
		// declare variables to avoid errors if $_GET['q'] is wrong
		$username = $password = $password_conf = '';
		// otherwise extract them from q
		parse_str($_GET['q']);
		$response = new Register($username, $password, $password_conf);
		echo $_GET['register'] . '(' . json_encode($response) . ')';
	} elseif (isset($_GET['form'])) {
		
	} else {
		//header('Location:main.php');
	}
?>