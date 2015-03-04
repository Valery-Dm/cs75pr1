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
			$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			// lock db
			$pdo->beginTransaction();
			$result = 1;
			$error = false;
			$error_s = 1;
			
			// execute all queries
			for ($index = 0, $lenght = count($queries); 
				 $index < $lenght; $index++) {

				// prepare statement
				$stmt = $pdo->prepare($queries[$index]);
				foreach ($params[$index] as $param => $val) {
					$stmt->bindValue($param, $val);
					// default rows quantity
					$rows = 1;
					if ($param == ':rows') {
						// given rows quantity
						$rows = $val;
					}
				}
				
				// execute and return result
				if ($queries[$index][0] == 'S') {
					// for SELECT queries
					if ($stmt->execute()) {
						$result = $stmt->fetchAll();
					} else {
						// log errors
						fwrite($file, $time->format('c') 
								   . '>dbquery:select> ' 
								   . "can't get data\n");
						fclose($file);
						// error mark for read db
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
					} elseif ($rows != $stmt->rowCount()) {
						// If affected rows quantity is wrong
						// Prevent parallel operations
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
				return $error;
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