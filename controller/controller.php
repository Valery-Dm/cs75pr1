<?php
	require_once('../model/dbquery.php');
	require_once('../model/call_api.php');

	/*
	* Calls for page template in HTML folder, exracts attributes
	*/
	function render($template, $data=array()) {
		$path = __DIR__ . '/../templates/' . $template . '.php';
		if (file_exists($path)) {
			extract($data);
			require($path);
		}
	}

	/*
	* Calls API for json data
	*/
	function getjson($type, $query) {
		if ($type == 'quotes') {
			// if we have valid query
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
				// Company name can be placed into two positions
				if ($count == 4) {
					$name = $result[1] . $result[2];
				} else {
					$name = $result[1];
				}
				return array($name, $result[$count - 1], $result[0]);
			}
		}
	}

	/*
	* Gets user's money from db. Returns float.
	*/
	function getusercash($userid) {
		$query = 'SELECT cash FROM users WHERE userid=:userid';
		$result = dbquery([$query], [[':userid' => $userid]]);
		if (!is_array($result)) {
			return false;
		}
		return $result[0]['cash'];
	}

	/*
	* Prepares db query to get user's holds.
	* Returns array with attributes or string with an alert.
	* Can be customized and sort data with an order.
	*/
	function getuserinfo($userid, $order = 'sharesquote ASC') {
		$query = "SELECT * FROM shares WHERE sharesuser=:userid 
				  ORDER BY $order";
		$result = dbquery([$query], [[':userid' => $userid]]);
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
