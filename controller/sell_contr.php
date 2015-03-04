<?php
	require_once('../controller/controller.php');

	/*
	* Will try to update all table records all at once.
	* Returns true or false.
	*/
	function sellshares($quote, $rows, $remainder, $price) {

		// prepare queries
		$query_del =   'DELETE FROM shares 
						WHERE sharesquote=:quote 
						AND sharesuser=:userid
						ORDER BY sharesquote ASC
						LIMIT :rows';
		$query_upd =   'UPDATE shares 
						SET sharesq=:remainder
						WHERE sharesquote=:quote
						AND sharesuser=:userid
						ORDER BY sharesquote ASC
						LIMIT 1';
		$query_add =   'UPDATE users 
						SET cash=cash + :cash
						WHERE userid=:userid';
		$queries = array($query_del, $query_upd, $query_add);

		// collect parameters
		$params = [	[':quote' => $quote, 
					 ':userid' => $_SESSION['userid'], 
					 ':rows' => $rows], 
					[':remainder' => $remainder, 
					 ':quote' => $quote, 
					 ':userid' => $_SESSION['userid']], 
					[':cash' => $price, 
					 ':userid' => $_SESSION['userid']] ];
		
		if ($remainder > 0 and $rows == 1) {
			// nothing to delete
			array_splice($queries, 0, 1);
			array_splice($params, 0, 1);
		} elseif ($remainder == 0) {
			// hothing to update and last row should be deleted
			array_splice($queries, 1, 1);
			array_splice($params, 1, 1);
		} else {
			// save last row from delete
			$params[0][':rows'] -= 1;
		}
		// call db
		$result = dbquery($queries, $params);
		return $result;
	}

	/*
	* Prepares Sell page and sells shares.
	* Returns prepared array with attributes.
	*/
	function shares($queries) {
		// set deafult values
		$hidden_a = $hidden_m = 'hidden'; 
		$hidden_d = $message = $data = '';
		
		// get user's portfolio
		$info = getuserinfo($_SESSION['userid']);
		
		// if user has no portfolio or in case of db error
		if (is_string($info)) {
			$message = $info;
			$hidden_m = '';
			$hidden_d = 'hidden';
		} else {
			$data = $info;
			// if we have shares to sell
			if (count($queries) === 2) {
				// return immediately if we have incomplete query
				if ($queries['shares'] == '' or $queries['sharesq'] == 0) {
					$message = 'You didn\'t specify shares name or quantity';
					$hidden_a = '';
				} else {
					// get current price
					$price = getjson('quotes', $queries['shares']);
					// in case of json error
					if (is_string($price)) {
						$message = $price;
						$hidden_m = '';
						$hidden_a = 'hidden';
					} else {
						// select price from the array
						$price = floatval($price[1]);
						$quantity = $queries['sharesq'];
						// check if user has enough shares to sell
						$remainder = 'none';
						$rows = 0;
						$share = strtoupper($queries['shares']);
						foreach ($data as $row) {
							// if share is found
							if ($share == $row['sharesquote']) {
								// count rows and remainder
								$rows++;
								$rem = $row['sharesq'] - $quantity;
								// break if row's quantity is sufficient
								if ($rem >= 0) {
									// store last remainder
									$remainder = $rem;
									break;
								}
								// if not - update quantity for next turn
								$quantity = abs($rem);
							}
						}
						// if no shares found
						if ($rows == 0) {
							$hidden_m = '';
							$hidden_a = 'hidden';
							$message = "You have no $share 
										shares in your portfolio";
						} elseif ($remainder === 'none') {
							// or quantity is not enough 
							$hidden_m = '';
							$hidden_a = 'hidden';
							$message = "You have not enough $share 
										shares in your portfolio";
						} else {
							$price = $queries['sharesq'] * $price;
							// try to sell shares
							$sell = sellshares($share, $rows, 
											   $remainder, $price);
							if (!$sell) {
								$hidden_m = '';
								$hidden_a = 'hidden';
								$message = 'Can\'t finish operation now,
											try again later';
							} else {
								$hidden_m = '';
								$hidden_a = 'hidden';
								$message = "You have sold your $share shares successfully";
							}
						}
					}
				}
			}
		}
		// collect page variables
		return array(	'message' => $message, 
					 	'data' => $data,
						'hidden_a' => $hidden_a, 
						'hidden_d' => $hidden_d, 
						'hidden_m' => $hidden_m		);
	}
?>