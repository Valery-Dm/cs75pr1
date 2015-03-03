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

	function portfolio() {
		$info = getuserinfo($_SESSION['userid']);
		if (is_array($info)) {
			$result = array('hidden_a' => 'hidden', 
							'hidden_d' => '', 
							'message' => '', 
							'data' => $info);
		} else {
			$result = array('hidden_a' => '', 
							'hidden_d' => 'hidden', 
							'message' => $info, 
							'data' => array());
		}
		return $result;
	}
?>