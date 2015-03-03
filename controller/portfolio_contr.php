<?php
	require_once('../controller/controller.php');

	/*
	* Prepares portfolio page.
	* Returns array with attributes or string with an alert
	*/
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