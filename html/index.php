<?php
	require_once('../controller/controller.php');
	render('header', array('title' => 'Log in', 'message' => 'Please login'));
?>
	<div class="form-group">
		<form method="post">
			<div class="col-md-3"></div>
			<div class="col-md-6 center">
				<label for="username">Username</label>
				<input type="text" name="username" id="username" class="form-control" autofocus />
				<span class="help-block">Please enter your login name</span><br />
				<label for="password">Password</label>
				<input type="password" name="password" id="password" class="form-control" />
				<span class="help-block">Please enter you password</span><br />
				<button type="submit" class="btn btn-primary btn-block" class="form-control" >Login</button>
				<br />
				<p>If you have no account<br /><a href="register.php">Register</a></p>
			</div>
			<div class="col-md-3"></div>
		</form>
	</div>
<?php
	render('footer');
?>
