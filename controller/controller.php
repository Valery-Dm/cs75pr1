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
				return array($name, $result[$count - 1], $result[0]);
			}
		}
	}

	session_start();
?>
