<?php
	require_once('../controller/controller.php');
	require_once('../controller/main_contr.php');

	// if user is not logged in switch to login page
	if (!isset($_SESSION['userid'])) {
		header('Location:index.php');
	}

	// page configuration section
	$pagename = (isset($_GET['page'])) ? 
					   $_GET['page'] : '404';
	$page = new Page($pagename);
	if (count($_POST) > 0) {
		$page->post($_POST);
	}

	// build page
	render('header_p', array('title' => $page->title, 
							 'message' => $page->message, 
							 'select' => $page->select,
							 'menu' => array('Portfolio', 
											 'Quotes', 
											 'Sell', 
											 'Logout')));
	render($page->body, $page->data);
	render('footer_p');

?>