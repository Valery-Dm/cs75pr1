<?php
	require_once('../controller/controller.php');

	/*
	* Construct page based on page name 
	* and the data given 
	* (data comes from submitted forms).
	* If page name is not valid construct 404 page
	*/
	class Page {
		public $title;
		public $message;
		public $select;
		public $body;
		public $data;
		
		function __construct ($page) {
			$this->body = strtolower($page);
			if (file_exists('../controller/'.$this->body.'_contr.php')){
				require('../controller/'.$this->body.'_contr.php');	
			} else {
				$this->body = '404';
			}
			switch ($page) {
				case 'Portfolio':
					$this->select = 
					$this->title = $page;
					$cash = getusercash($_SESSION['userid']);
					if ($cash === false) {
						$this->message = 'Something went wrong, 
									try to login later';
					} else {
						$this->message = 'Welcome, '
									. $_SESSION['username']
									. ', your deposit is $'
									. $cash;
					}
					$this->data = portfolio();
					break;
				case 'Quotes':
					$this->select = $page;	
					$this->title = 
					$this ->message = 'Get quote and buy';
					$this->data = quotes();
					break;
				case 'Sell':
					$this->select = $page;
					$this->title = 
					$this->message = 'Sell your shares';
					$this->data = shares();
					break;
				default:
					$this->select =	
					$this->title = 
					$this->message = '404';
					$this->data = array('alert' => 
										'There is no such page');
					break;
			}
		}
		
		function post($post=array()) {
			switch (key($post)) {
				case 'quotes':
					$this->data = quotes($post);
					break;
				case 'buyquote':
					$this->data = 
						($post['buytotal'] > 0) ?
						quotes($post) : 
						quotes(['quotes' => 
								trim($post['buyquote'], '"')]);
					break;
				case 'shares':
					$this->data = shares($post);
			}
		}
	}
?>