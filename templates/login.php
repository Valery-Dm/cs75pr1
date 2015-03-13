<div class="col-md-6 center">
	<div class="form-group">
		<form id="loginform" method="post">

<label for="username">Username</label>
<input type="text" name="username" id="username" class="form-control" 
	   value="<?= (isset($_POST['username'])) ? 
		htmlspecialchars($_POST['username']) : '' ?>" autofocus />
<?= "<span id='namealert' class='help-block alerts $hidden'>$namealert</span>" ?>
<span class="help-block">Please enter your login name</span>
<br />

<label for="password">Password</label>
<input type="password" name="password" id="password" class="form-control" />
<span class="help-block">Please enter you password</span>
<br />
<button type="submit" class="btn btn-primary btn-block form-control" >
	Login
</button>
<br />
<p>If you have no account<br />
	<a href="../html/index.php?form=register">Register</a>
</p>

		</form>
	</div>
</div>