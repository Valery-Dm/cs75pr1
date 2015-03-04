<?php
	function call_api($url) {
		$call = @file_get_contents($url);
		if (!$call) {
			return false;
		}
		
		return explode(',', $call);
	}
?>