<?php
	require_once('../controller/controller.php');

	/*
	* Takes Quote name as string, Quantity as int and Price as float.
	* Opens db connection for isolated transaction. 
	* Returns string with success or error message.
	*/
	function sellshares($quote, $sharesq, $price) {

		$DBUSER = 'lampp';
		$DBPASS = 'serveradmin';
		$DSN = "mysql:host=localhost;dbname=cs75finance;";
		$pdo = new PDO($DSN, $DBUSER, $DBPASS);
		
		$cash = $sharesq * $price;
		$userid = $_SESSION['userid'];
		$index = 0;
		
		$queries = [   "SELECT sharesq, sharesname
						FROM shares 
						WHERE sharesquote=:quote 
						AND sharesuser=:userid
						ORDER BY sharesquote ASC
						LIMIT 1
						LOCK IN SHARE MODE", 
					
					   "DELETE FROM shares 
						WHERE sharesquote=:quote 
						AND sharesuser=:userid
						ORDER BY sharesquote ASC
						LIMIT 1", 
					
					   "UPDATE shares 
						SET sharesq=:sharesq
						WHERE sharesquote=:quote
						AND sharesuser=:userid
						ORDER BY sharesquote ASC
						LIMIT 1",
				   
				   	   "UPDATE users 
						SET cash=cash + :cash
						WHERE userid=:userid"	];
		
		// isolate transaction
		$pdo->beginTransaction();
		$error = false;
		$rem = 1;
		$message = "Can't finish operation now";
		// execution will stop in no-update case
		while (true) {

			$stmt = $pdo->prepare($queries[$index]);
			// bind parameters
			$stmt->bindValue(':userid', $userid, PDO::PARAM_INT);
			// common value or that for final stage
			($index != 3) ?
				$stmt->bindValue(':quote', $quote, PDO::PARAM_STR)
				: $stmt->bindValue(':cash', $cash, PDO::PARAM_STR);
			
			// just for update case
			($index == 2) ?
				$stmt->bindValue(':sharesq', $sharesq, PDO::PARAM_INT) : '';
			
			// in case of db error
			if ($stmt->execute() === false){
				$error = true;
				break;
			}
			
			// fetch result for select queries
			if ($index == 0) {
				$fetch = $stmt->fetch();
				if ($fetch > 0) {
					$rem = $fetch['sharesq'] - abs($sharesq);
					$sharesq = abs($rem);
					// delete this row or update its quantity
					if ($rem <= 0) {
						$index = 1;
						continue;
					} else {
						$index = 2;
						continue;
					}
					
				} else {
					$message = "You have no $quote 
								shares in your portfolio
								or quantity is not enough";
					$error = true;
					break;
				}
			}
			// delete query
			if ($index == 1) {
				// if last row go to final stage
				if ($rem == 0) {
					$index = 3;
					continue;
				} else {
					// find next row
					$index = 0;
					continue;
				}
				
			}
			// update query
			if ($index == 2) {
				if ($stmt->rowCount() > 0) {
					// finalize queue
					$index = 3;
					continue;
				} else {
					$error = true;
					break;
				}
			}
			// update user's money
			if ($index == 3) {
				if ($stmt->rowCount() > 0) {
					$message = "You have sold $quote 
								shares successfully";
					break;
				} else {
					$error = true;
					break;
				}
			}
		}
		// close transaction
		($error) ? $pdo->rollBack() : $pdo->commit();
		
		return $message;
	}

	/*
	* Prepares Sell page and calls sellshares function.
	* Returns prepared array with attributes.
	*/
	function shares($queries=array()) {
		// set deafult values
		$hidden_a = $hidden_m = 'hidden'; 
		$hidden_d = $message = $data = '';
		
		// get user's portfolio
		$info = getuserinfo($_SESSION['userid']);
		
		// if user has no portfolio or in case of db error
		if (is_string($info)) {
			$message = $info;
			$hidden_m = '';
			$hidden_a = $hidden_d = 'hidden';
		} else {
			$data = $info;
			// if we have shares to sell
			if (count($queries) === 2) {
				$share = strtoupper($queries['shares']);
				// return immediately if we have incomplete query
				if ($share == '' or 
					!is_numeric($queries['sharesq']) or 
					$queries['sharesq'] <= 0) {
						$message = 'You didn\'t specify shares name or quantity';
						$hidden_a = '';
				} else {
					$sharesq = intval($queries['sharesq']);
					// get current price
					$price = getjson('quotes', $share);
					// in case of json error
					if (is_string($price)) {
						$message = $price;
						$hidden_m = '';
						$hidden_a = 'hidden';
					} else {
						// select price from the array
						$price = floatval($price[1]);
						// try to sell shares
						$message = sellshares($share, $sharesq, $price);
						$hidden_m = '';
						$hidden_a = 'hidden';
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