<?php
	require_once('../controller/controller.php');	

	/*
	* Function prepares DB query based on given parameters,
	* Then puts them into dbquery function.
	* Return string on error or success.
	*/
	function buyshares($share=array()) {

		// check user's deposit
		$total = $share['total'] * $share['price'];
		$usercash = usercash($_SESSION['userid']);
		if (!$usercash) {
			return 'can\'t buy now, try again later';
		} elseif ($usercash - $total < 0) {
			return 'You have not enough money to buy';
		}

		// prepare queries
		$query_charge ='UPDATE users 
						SET cash=cash - :total 
						WHERE userid=:userid';
		$query_add =   'INSERT INTO shares 
				   		(sharesquote, sharesname, sharesq, 
						sharesprice, sharesuser)
				  		VALUES (:quote, :name, :total, 
						:price, :userid)';

		// prepare parameters
		$params = [[':total' => $total, ':userid' => $_SESSION['userid']], 
				   [':quote' => trim($share['quote'], '"'), 
					':name' => trim($share['name'], '"'), 
					':total' => $share['total'], 
					':price' => $share['price'], 
					':userid' => $_SESSION['userid']]];

		// try to update db
		$result = dbquery(array($query_charge, $query_add), $params);
		if (!$result) {
			return 'can\'t buy now, try again later';
		} else {
			$_SESSION['cash'] -= $total;
			return 'You\'ve bought ' 
						. $share['total'] . ' shares of ' 
						. $share['name'];
		}
		
	}

	/*
	* Function prepares Quotes page and call for buyshares function.
	* Returns array with atributes to main.php
	*/
	function quotes($queries) {
		// select what page should be constructed
		// set default parameters
		$hidden_d = $hidden_a = $hidden_m = $message = $data = 'hidden'; 
		// if we have POST query
		if (count($queries) == 1) {
			// If POST contains 'price query' call for Yahoo
			$data = getjson('quotes', $queries['quotes']);
			if (is_string($data)) {
				$hidden_a = '';
				$message = $data;
			} else {
				$hidden_d = '';
			}
		} elseif (count($queries) == 4) {
			// Buy shares
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