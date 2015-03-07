<?php
	require_once('../controller/controller.php');	
	/* test
	for	($i = 0; $i < 5; $i++) {
		print buyshares(['quote' => 'V', 'name' => 'Visa Inc.', 
						 'total' => $i, 'price' => 275]) ."\n";
	}
	*/

	/*
	* Function prepares DB query based on given parameters,
	* Then puts them into dbquery function.
	* It takes Quote, its full Name, Quantity and Price of 1 share.
	* Return string with error or success message.
	*/
	function buyshares($share=array()) {

		// check user's deposit
		$total = $share['buytotal'] * $share['buyprice'];
		$userid = $_SESSION['userid']; // replace by number for test
		$usercash = getusercash($userid);
		if (!$usercash) {
			return 'can\'t buy now, try again later';
		} elseif ($usercash - $total < 0) {
			return 'You have not enough money to buy';
		}

		// prepare queries
		$queries = [   'UPDATE users 
						SET cash=cash - :total 
						WHERE userid=:userid',

					   'INSERT INTO shares 
				   		(sharesquote, sharesname, sharesq, 
						sharesprice, sharesuser)
				  		VALUES (:quote, :name, :total, 
						:price, :userid)'	];

		// prepare parameters
		$params = [	   [':total' => $total, ':userid' => $userid], 

					   [':quote' => trim($share['buyquote'], '"'), 
						':name' => trim($share['buyname'], '"'), 
						':total' => $share['buytotal'], 
						':price' => $share['buyprice'], 
						':userid' => $userid]	];

		// try to update db
		$result = dbquery($queries, $params);
		if (!$result) {
			return 'can\'t buy now, try again later';
		} else {
			return 'You\'ve bought ' 
						. $share['buytotal'] . ' shares of ' 
						. $share['buyname'];
		}
	}

	/*
	* Function prepares Quotes page and call for buyshares function.
	* Returns array with atributes to main.php
	*/
	function quotes($queries=array()) {
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