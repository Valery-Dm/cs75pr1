<?php
	require_once('../controller/controller.php');

	if (!isset($_SESSION['userid'])) {
		header('Location:index.php');
	}
	$data = 'There is no such page';
	
	if (isset($_GET['page'])) {
		$page = $_GET['page'];
		$body = strtolower($page);
		require('../controller/' . $body . '_contr.php');	
		$queries = array();
		if ($page == 'Quotes') {
			$title = $message = 'Get quote';
			if (isset($_POST['quotes'])) {
				$queries = array('quotes' => $_POST['quotes']);
			} elseif (isset($_POST['buytotal']) and $_POST['buytotal'] > 0) {
				$queries = array('name' => $_POST['buyname'], 
								 'total' => $_POST['buytotal'], 
								 'price' => $_POST['buyprice']);
			}
			$data = quotes($queries);
		} elseif ($page == 'sell') {
			$title = $message = 'Here you can sell your shares';
		} elseif ($page == 'Portfolio') {
			$data = getuserinfo($_SESSION['userid']);
			$title = $page;
			$message = 'Welcome, ' 
								. $_SESSION['username'] 
								. ',<br /> your deposit is $' 
								. $_SESSION['cash'];
		} else {
			$title = $body = $message = '404';
		}
	} else {
		$title = $body = $message = '404';
	}

	// if no actual data - the alert needs to be shown	
	if (!is_array($data)) {
		$data = array('data' => $data, 
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