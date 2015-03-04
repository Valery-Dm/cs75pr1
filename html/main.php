<?php
	require_once('../controller/controller.php');

	// if user is not logged in switch to login page
	if (!isset($_SESSION['userid'])) {
		header('Location:index.php');
	}
	// message for wrong address	
	$data = 'There is no such page';
	$page = '';
	// page configuration section
	if (isset($_GET['page'])) {
		$page = $_GET['page'];
		$body = strtolower($page);
		if (file_exists('../controller/' . $body . '_contr.php')){
			require('../controller/' . $body . '_contr.php');
		} else {
			$body = '404';
		}
		$queries = array();
		if ($page == 'Quotes') {
			// configures Quotes page
			$title = $message = 'Get quote and buy';
			if (isset($_POST['quotes'])) {
				$queries = array('quotes' => $_POST['quotes']);
			} elseif (isset($_POST['buytotal']) and $_POST['buytotal'] > 0) {
				$queries = array('quote' => $_POST['buyquote'], 
								 'name' => $_POST['buyname'], 
								 'total' => $_POST['buytotal'], 
								 'price' => $_POST['buyprice']);
			} else {
				$queries = array();
			}
			$data = quotes($queries);
		} elseif ($page == 'Sell') {
			// Configures Sell page
			$title = $message = 'Sell your shares';
			if (isset($_POST['sharesq']) and 
				isset($_POST['shares'])) {
				$queries = array('shares' => $_POST['shares'], 
								 'sharesq' => $_POST['sharesq']);
			} else {
				$queries = array();
			}
			$data = shares($queries);
		} elseif ($page == 'Portfolio') {
			// Configures Portfolio page
			$data = portfolio();
			$title = $page;
			$cash = getusercash($_SESSION['userid']);
			if ($cash === false) {
				$message = 'Something went wrong, try to login again';
			} else {
				$message = 'Welcome, ' . $_SESSION['username'] 
							. ', your deposit is $' . $cash;
			}
		} else {
			// Wrong GET 'page' value
			$title = $message = '404';
		}
	} else {
		// GET 'page' attribute is not present
		$title = $body = $message = '404';
	}

	// if no actual data - the alert needs to be shown	
	if (!is_array($data)) {
		$data = array('message' => $data, 
					  'data' => '',
					  'hidden_a' => '', 
					  'hidden_d' => 'hidden');
	}

	// build page
	render('header_p', array('title' => $title, 
							 'message' => $message, 
							 'select' => $page,
							 'menu' => array('Portfolio', 
											 'Quotes', 
											 'Sell')));
	render($body, $data);
	render('footer');
?>