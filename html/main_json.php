<?php
	require_once('../controller/main_contr.php');
	require_once('../controller/quotes_contr.php');

	header('content-type: application/json; charset=utf-8');

	if (isset($_GET['menu'])) {
		// parse query
		$url = parse_url($_GET['q']);
		parse_str($url['query'], $name);
		// construct page
		$page = new Page($name['page']);
		$body = read($page->body, $page->data);
		// create response array
		$response = array('title' => $page->title,
						  'message' => $page->message,
						  'href' => $_GET['q'],
						  'body' => $body);
		echo $_GET['menu'] . '(' . json_encode($response) . ')';
	} elseif (isset($_GET['form-quote'])) {
		$queries = [$_GET['q'][0]['name'] => $_GET['q'][0]['value']];
		$response = array('form' => 'form-quote', 
						  'response' => quotes($queries));
		echo $_GET['form-quote'] . '(' . json_encode($response) . ')';
	} elseif (isset($_GET['form-buy'])) {
		$queries = [];
		foreach ($_GET['q'] as $q) {
			$queries += [$q['name'] => $q['value']];
		}
		$response = array('form' => 'form-buy', 
						  'response' => quotes($queries));
		echo $_GET['form-buy'] . '(' . json_encode($response) . ')';
	} elseif (isset($_GET['form-sell'])) {
		//$response = $_GET['q'];
		$queries = [];
		foreach ($_GET['q'] as $q) {
			$queries += [$q['name'] => $q['value']];
		}
		
		$response = shares($queries);
		echo $_GET['form-sell'] . '(' . json_encode($response) . ')';
	}
?>