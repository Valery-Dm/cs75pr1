<?php
	require_once('../controller/controller.php');

	function getuserinfo($userid, $order = 'sharesquote ASC') {
		$query = "SELECT * FROM shares WHERE sharesuser=:userid 
				  ORDER BY $order";
		$result = dbquery($query, array(':userid' => $userid));
		if (!$result) {
			return 'You have no shares in your portfolio';
		} elseif ($result == 2) {
			return 'Can\'t get your data right now, try to login later';
		} else {
			return $result;
		}
	}
?>