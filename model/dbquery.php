<?php

	/*
	* Handles all database queries.
	* Takes several queries and their parameters.
	* Locks DB for all queries and roll changes back on any error.
	* Also trucks error records to a log-file.
	*/
	function dbquery($queries, $params = array()) {

		// create time object
		$time = new DateTime('NOW');

		// open log file
		$file = fopen('../model/errors.log', 'a');

		try {
			// connect to database
			$DBUSER = 'lampp';
			$DBPASS = 'serveradmin';
			$DSN = "mysql:host=localhost;dbname=cs75finance;";
			$pdo = new PDO($DSN, $DBUSER, $DBPASS);
			// This will allow bind values of different types in a loop 
			$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			
			// isolate given queries
			$pdo->beginTransaction();
			// this will be chaged in SELECT part
			$result = 1;
			// marks for errors
			$error = false;
			$error_s = 1;
			
			// execute all queries in a loop
			for ($index = 0, $lenght = count($queries); 
				 $index < $lenght; $index++) {

				// prepare statement
				$stmt = $pdo->prepare($queries[$index]);
				// bind parameters
				foreach ($params[$index] as $param => $val) {
					$stmt->bindValue($param, $val);
					// default rows quantity
					$rows = 1;
					if ($param == ':rows') {
						// given rows quantity
						$rows = $val;
					} elseif ($param == ':username') {
						// for finduser query expect no user result
						$rows = 0;
					}
				}
				
				// execute and return result
				if ($queries[$index][0] == 'S') {
					// for SELECT queries
					if ($stmt->execute()) {
						$result = $stmt->fetchAll();
						// This will prevent wrong operations
						if (count($result) < $rows) {
							$error = true;
						} 
					} else {
						// log errors
						fwrite($file, $time->format('c') 
								   . '>dbquery:select> ' 
								   . "can't get data\n");
						fclose($file);
						// error mark for SELECT
						$error_s = 2;
					}
				} else {
					// for other queries
					if (!$stmt->execute()) {
						// log errors
						fwrite($file, $time->format('c') 
								   . '>dbquery:change> ' 
								   . "can't write into db\n");
						fclose($file);
						// error mark
						$error = true;
					}
				}
			}

			// if we have an error
			if ($error) {
				$pdo->rollBack();
				return false;
			} elseif ($error_s == 2) {
				// no need to roll back SELECT queries
				$pdo->commit();
				return 2;
			}
			
			// commit on success
			$pdo->commit();
			// return data for SELECT or 1 for others
			return $result;
		} catch (PDOException $e) {
			// log errors
			fwrite($file, $time->format('c') 
					   . '>dbquery:code> ' 
					   . $e->getCode() . "\n");
			fclose($file);
			return false;
		}
	}
?>