<?php
	require_once('../controller/controller.php');

	if (!isset($_SESSION['userid'])) {
		header('Location:index.php');
	}
	
	// set initial variables
	$data = '';
	$select = '';
	$hidden_a = 'hidden';
	$hidden_d = '';
	$price = '';
	$pricelabel = '';
	
	if (isset($_GET['page'])) {
		$page = $_GET['page'];
		$body = strtolower($page);
		if ($page == 'Quotes') {
			$title = 'Get quotes';
			$message = 'Get quotes';
		} elseif ($page == 'Portfolio') {
			$data = getuserinfo($_SESSION['userid']);
			if (!is_array($data)) {
				$hidden_a = '';
				$hidden_d = 'hidden';
			}
			$title = $page;
			$message = 'Welcome, ' . $_SESSION['username'];
		} else {
			$title = '404';
			$message = '';
			$body = '404';
		}
	}

	// build page
	render('header_p', array('title' => $title, 
							 'message' => $message, 
							 'select' => $page,
							 'menu' => array('Portfolio', 
											 'Quotes', 
											 'Buy', 
											 'Sell')));
	render($body, array('data' => $data,  
						'price' => $price, 
						'pricelabel' => $pricelabel,
					    'hidden_a' => $hidden_a, 
					    'hidden_d' => $hidden_d));
	render('footer');
?>