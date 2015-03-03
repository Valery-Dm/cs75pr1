<?php
	require_once('../controller/controller.php');
	require_once('../controller/portfolio_contr.php');

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
			// hothing to update and delete last row
			array_splice($queries, 1, 1);
			array_splice($params, 1, 1);
		} else {
			// save last row
			$params[0][':rows'] -= 1;
		}
		
		$result = dbquery1($queries, $params);
		return $result;
	}

	function shares($queries) {
		$hidden_a = $hidden_m = 'hidden'; 
		$hidden_d = $message = $data = '';
		// get portfolio
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
							if ($share == $row['sharesquote']) {
								$rows++;
								$rem = $row['sharesq'] - $quantity;
								// break if row's quantity is sufficient
								if ($rem >= 0) {
									// store last remainder
									$remainder = $rem;
									break;
								}
								// if not - update Q for the next one
								$quantity = abs($rem);
							}
						}
						if ($rows == 0) {
							$hidden_m = '';
							$hidden_a = 'hidden';
							$message = "You have no $share 
										shares in your portfolio";
						} elseif ($remainder === 'none') {
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
								$_SESSION['cash'] += $price;
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