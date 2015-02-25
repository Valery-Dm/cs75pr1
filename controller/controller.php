<?php
	function render($template, $data=array()) {
		$path = __DIR__ . '/../templates/' . $template . '.php';
		if (file_exists($path)) {
			extract($data);
			require($path);
		}
	}
	$message = '';
	function message() {
		if (isset($_SESSION['username'])) {
			$message = 'Welcome back, ' . $_SESSION['username'];
		}
	}
	
	session_start();
	message();
?>
