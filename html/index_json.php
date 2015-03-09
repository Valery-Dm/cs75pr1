<?php
	require_once('../controller/login.php');

	header('content-type: application/json; charset=utf-8');

	if (isset($_GET['q'])) {	
		// declare variables to avoid errors if $_GET['q'] is wrong
		$username = $password = $password_conf = '';
		parse_str($_GET['q']);

		if (isset($_GET['checkname'])) {
			$response = new CheckUser($username);
			$callback = 'checkname';
		} elseif (isset($_GET['register'])) {
			$response = new Register($username, $password, $password_conf);
			$callback = 'register';
		} elseif (isset($_GET['login'])) {
			$response = new Login($username, $password);
			$callback = 'login';
		} elseif (isset($_GET['link'])) {
			$url = parse_url($_GET['q']);
			$pagename = (isset($url['query'])) ? 'register' : 'login';
			$page = new Guest($pagename);
			$response = array('body' => read($page->body, $page->alerts), 
							  'title' => $page->title, 
							  'message' => $page->message);
			$callback = 'link';
		}
		echo $_GET[$callback] . '(' . json_encode($response) . ')';
	}
?>