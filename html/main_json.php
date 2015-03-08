<?php
	require_once('../controller/main_contr.php');
	require_once('../controller/quotes_contr.php');
	require_once('../controller/sell_contr.php');

	header('content-type: application/json; charset=utf-8');

	function getresponse($queries, $form, $function) {
		$query = [];
		foreach ($queries as $q) {
			$query += [$q['name'] => $q['value']];
		}
		$response = array('form' => $form, 
						  'response' => $function($query));
		return $_GET[$form] . 
				'(' . json_encode($response) . ')';
	}

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
		echo getresponse($_GET['q'], 'form-quote', 'quotes');
	} elseif (isset($_GET['form-buy'])) {
		echo getresponse($_GET['q'], 'form-buy', 'quotes');
	} elseif (isset($_GET['form-sell'])) {
		echo getresponse($_GET['q'], 'form-sell', 'shares');
	}
?>