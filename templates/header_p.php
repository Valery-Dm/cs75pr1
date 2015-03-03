<!DOCTYPE html>
<html>
	<head>
		<title><?= 'CS75 finance: ' . htmlspecialchars($title); ?></title>
		<meta charset="utf-8" />
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="stylesheet" href="../../bootstrap/css/bootstrap.css" />
		<link rel="stylesheet" href="../templates/style.css" />
	</head>
	<body>
<div class="wrap">
	<div class="container">
	<noscript>
		<div class="alerts nojava">
			It seems that javascript is not enabled.  
			Less perfomance expected.
		</div>
	</noscript>
	<div class="navbar navbar-default top">
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
				<a class="navbar-brand sitename" href="main.php?page=Portfolio">CS75 finance</a>
			</div>
			<div id="navbar" class="navbar-collapse collapse" >
				<ul class="nav navbar-nav navbar-right">
              		<?php 	
						foreach ($menu as $item) {
							if ($item == $select) { 
					?>
								<li class="active">
									<a href="main.php?page=<?= htmlspecialchars($item); ?>">
										<?= htmlspecialchars($item); ?>
										<span class="sr-only">(current)</span>
									</a>
								</li>
					<?php
							} else { 
					?>
								<li>
									<a href="main.php?page=<?= htmlspecialchars($item); ?>">
										<?= htmlspecialchars($item); ?>
									</a>
								</li>
					<?php
							}
						}
					?>
					<li>
						<a href="../controller/logout.php">Logout</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
	<p class="lead center"><?= htmlspecialchars($message); ?></p>
