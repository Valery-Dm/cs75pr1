<?php
	require_once('../controller/controller.php');

	if (!isset($_SESSION['userid'])) {
		header('Location:index.php');
	}
	
	// set initial variables
	$data = '';
	$hidden_a = 'hidden';
	$hidden_d = '';
	$qprice = '';
	$page = '';

	if (isset($_GET['page'])) {
		$page = $_GET['page'];
		$body = strtolower($page);
		if ($page == 'Quotes') {
			$title = $message = 'Get quotes';
			$hidden_d = 'hidden';
			if (isset($_POST['quotes'])) {
				$result = getjson('quotes', $_POST['quotes']);
				if (is_string($result)) {
					$data = $result;
					$hidden_a = '';
				} else {
					$hidden_d = '';
					$qprice = 'Current price for '
									. $result[0] 
									. ' is <strong>$'
									. $result[1]
									. '</strong>';
				}
			}
		} elseif ($page == 'Portfolio') {
			$data = getuserinfo($_SESSION['userid']);
			if (!is_array($data)) {
				$hidden_a = '';
				$hidden_d = 'hidden';
			}
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

	// build page
	render('header_p', array('title' => $title, 
							 'message' => $message, 
							 'select' => $page,
							 'menu' => array('Portfolio', 
											 'Quotes', 
											 'Buy', 
											 'Sell')));
	render($body, array('data' => $data,  
						'qprice' => $qprice, 
					    'hidden_a' => $hidden_a, 
					    'hidden_d' => $hidden_d));
	render('footer');
?>