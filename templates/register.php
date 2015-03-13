<div class="col-md-6 center">
	<div class="form-group">
		<form id="registerform" method="post">

<label for="username">Username</label>
<input type="text" name="username" id="username" class="form-control" 
	   value="<?= (isset($_POST['username'])) ? 
		htmlspecialchars($_POST['username']) : '' ?>" autofocus />
<?= "<span id='namealert' class='help-block alerts $hidden'>$namealert</span>" ?>
<span class="help-block">Choose your unique login name.<br /> 
	It must be single word 3 to 10 characters long.<br />
	Only digits and english letters are allowed.
</span>
<br />
<label for="password">Password</label>
<input type="password" name="password" id="password" class="form-control" />
<?= "<span id='passalert' class='help-block alerts $hidden'>$passalert</span>" ?>
<span class="help-block">Choose your password.<br />
	It should be at least 4 characters long.<br />
	Use at least one uppercase character and at least one digit.
</span>
<br />
<label for="password_conf">Password Confirmation</label>
<input type="password" name="password_conf" id="password_conf" class="form-control" />
<?= "<span id='confalert' class='help-block alerts $hidden'>$confalert</span>" ?>
<span class="help-block">Retype you password</span><br />
<button type="submit" class="btn btn-primary btn-block form-control" >Register</button>
<br />
<p>If you already have an account<br /><a href="../html/index.php">Login</a></p>

		</form>
	</div>
</div>