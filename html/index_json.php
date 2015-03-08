<?php
	require_once('../controller/login.php');

	header('content-type: application/json; charset=utf-8');


	if (isset($_GET['register'])) {
		// declare variables to avoid errors if $_GET['q'] is wrong
		$username = $password = $password_conf = '';
		// otherwise extract them from q
		parse_str($_GET['q']);
		$response = new Register($username, $password, $password_conf);
		echo $_GET['register'] . '(' . json_encode($response) . ')';
	} elseif (isset($_GET['login'])) {
		$username = $password = '';
		parse_str($_GET['q']);
		$response = new Login($username, $password);
		echo $_GET['login'] . '(' . json_encode($response) . ')';
	} elseif (isset($_GET['link'])) {
		$url = parse_url($_GET['q']);
		$pagename = (isset($url['query'])) ? 'register' : 'login';
		$page = new Guest($pagename);
		$response = array('body' => read($page->body, $page->alerts), 
						  'title' => $page->title, 
						  'message' => $page->message);
		echo $_GET['link'] . '(' . json_encode($response) . ')';
	}
?>