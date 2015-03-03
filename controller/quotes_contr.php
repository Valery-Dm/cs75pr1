<?php
	require_once('../controller/controller.php');	

	function buyshares($share=array()) {
		
		// first try to minus total price from user's account
		$charge_query = 'UPDATE users SET cash=cash-:total WHERE userid=:userid';
		
		$total = $share['total'] * $share['price'];
		$charge = dbquery($charge_query, array(':total' => $total, 
											   ':userid' => $_SESSION['userid']));
		if (!$charge) {
			return 'can\'t buy now, try again later';
		}
		// then try to add bought shares
		$query = 'INSERT INTO shares 
				 (sharesquote, sharesname, sharesq, sharesprice, sharesuser)
				  VALUES (:quote, :name, :total, :price, :userid)';
		$buy = dbquery($query, array(':quote' => trim($share['quote'], '"'), 
									 ':name' => trim($share['name'], '"'), 
									 ':total' => $share['total'], 
									 ':price' => $share['price'], 
									 ':userid' => $_SESSION['userid']));
		if (!$buy) {
			// return spent money
			$return_query = 'UPDATE users SET cash=cash+:total WHERE userid=:userid';
			$return = dbquery($charge_query, array(':total' => $total));
			if (!$return) { /* send an email to admin */ }
			return 'can\'t buy now, try again later';
		} else {
			$_SESSION['cash'] = $_SESSION['cash'] - $total;
			return 'You\'ve bought ' 
						. $share['total'] . ' shares of ' 
						. $share['name'];
		}
		
	}

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
		} elseif (count($queries) == 4) {
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