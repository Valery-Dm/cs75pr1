<?php
	function call_api($url) {
		$call = file_get_contents($url);
		if (!$call) {
			return false;
		}
		// Yahoo returns string instead of json
		return explode(',', $call);
	}
?>