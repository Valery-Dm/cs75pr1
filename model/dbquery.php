<?php

	/*
	* Handles all database queries.
	* Takes query string and parameters array as arguments.
	*/
	function dbquery($query, $params = array()) {

		// create time object
		$time = new DateTime('NOW');

		// open log file
		$file = fopen('../model/errors.log', 'a');

		// connect to database
		try {

			$DBUSER = 'lampp';
			$DBPASS = 'serveradmin';
			$DSN = "mysql:host=localhost;dbname=cs75finance;";
			$pdo = new PDO($DSN, $DBUSER, $DBPASS);
			// prepare statement
			$stmt = $pdo->prepare($query);
			foreach ($params as $param => $val) {
				$stmt->bindValue($param, $val);
			}

			// execute and return resul
			if ($query[0] == 'S') {
				// for SELECT queries
				if ($stmt->execute()) {
					return $stmt->fetchAll();
				} else {
					// log errors
					fwrite($file, $time->format('c') 
							   . '>dbquery:select> ' 
							   . "can't get data\n");
					fclose($file);
					return 2;
				}
			}
			$result = $stmt->execute();
			if (!$result) {
				// log errors
				fwrite($file, $time->format('c') 
						   . '>dbquery:change> ' 
						   . "can't write into db\n");
				fclose($file);
				return $result;
			}
			return $result;

		} catch (PDOException $e) {
			// log errors
			fwrite($file, $time->format('c') 
					   . '>dbquery:code> ' 
					   . $e->getCode() . "\n");
			fclose($file);
			return false;
		}
		// something wrong if function can reach here
		fwrite($file, $time->format('c') 
				   . '>dbquery:unknown> ' 
				   . "something went wrong\n");
		fclose($file);
		return false;
	}
	
?>