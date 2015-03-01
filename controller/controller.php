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


	session_start();
?>
