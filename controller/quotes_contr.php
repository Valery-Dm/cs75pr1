<?php
	require_once('../controller/controller.php');	

	function quotes($queries) {
		$hidden_d = $hidden_a = $hidden_m = $message = $data = 'hidden'; 
		if (count($queries) == 1) {
			$data = getjson('quotes', $queries['quotes']);
			if (is_string($data)) {
				$hidden_a = '';
				$message = $data;
			} else {
				$hidden_d = '';
			}
		} elseif (count($queries) == 3) {
			$message = buyshares($queries);
			$hidden_m = '';
		}
		return array('data' => $data, 
					 'message' => $message,
					 'hidden_a' => $hidden_a, 
					 'hidden_m' => $hidden_m,
					 'hidden_d' => $hidden_d);
	}
?>