<?php
	require_once('../controller/controller.php');

	/*
	* Prepares portfolio page.
	* Returns array with attributes or string with an alert
	*/
	function portfolio() {
		$info = getuserinfo($_SESSION['userid']);
		
		if (is_string($info)) {
			// in case of error show the alert
			$result = array('hidden_a' => '', 
							'hidden_d' => 'hidden', 
							'message' => $info, 
							'data' => array());
			
		} else {
			// get data for return 
			$result = array('hidden_a' => 'hidden', 
							'hidden_d' => '', 
							'message' => '', 
							'data' => $info);
		}
		
		return $result;
	}
?>