<div class="form-group">
	<form method="post">
		<div class="col-md-3"></div>
		<div class="col-md-6 center">
			<label for="username">Username</label>
			<input type="text" name="username" id="username" class="form-control" 
				   value="<?= (isset($_POST['username'])) ? $_POST['username'] : '' ?>" autofocus />
			<?= "<span class='alerts $hidden'>$error</span>" ?>
			<span class="help-block">Please enter your login name</span><br />
			<label for="password">Password</label>
			<input type="password" name="password" id="password" class="form-control" />
			<span class="help-block">Please enter you password</span><br />
			<button type="submit" class="btn btn-primary btn-block form-control" >Login</button>
			<br />
			<p>If you have no account<br /><a href="../html/index.php?form=register">Register</a></p>
		</div>
		<div class="col-md-3"></div>
	</form>
</div>