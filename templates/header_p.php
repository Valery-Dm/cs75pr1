<!DOCTYPE html>
<html>
	<header>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="stylesheet" href="../../bootstrap/css/bootstrap.css" />
		<link rel="stylesheet" href="../templates/style.css" />
		<title><?= 'CS75 finance: ' . htmlspecialchars($title); ?></title>
	</header>
	<body>
<div class="wrap">	
<div class="container">

	<div class="navbar navbar-default">
		<div class="container-fluid">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" 
						data-toggle="collapse" data-target="#navbar" 
						aria-expanded="false" aria-controls="navbar">
				  <span class="sr-only">Toggle navigation</span>
				  <span class="icon-bar"></span>
				  <span class="icon-bar"></span>
				  <span class="icon-bar"></span>
				</button>
				<a class="navbar-brand sitename" href="main.php">CS75 finance</a>
			</div>
			<div style="height: 1px;" aria-expanded="false" id="navbar" 
				 class="navbar-collapse collapse">
				<ul class="nav navbar-nav navbar-right">
              		<li class="active">
						<a href="main.php">Portfolio 
						<span class="sr-only">(current)</span></a>
					</li>
					<li>
						<a href="">Get quotes</a>
					</li>
					<li>
						<a href="">Buy shares</a>
					</li>
					<li>
						<a href="">Sell shares</a>
					</li>
					<li>
						<a href="../templates/logout.php">Logout</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
	<p class="lead center"><?= $message; ?></p>
