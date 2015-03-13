<div class="col-md-6 center">
	<div id="quote-message" class="result alerts <?= $hidden_m; ?>">
		<h3><?= htmlspecialchars($message); ?></h3>
	</div>

	<div id="quote-result" class="form-group result <?= $hidden_d; ?>">
		<form id="form-buy" method="post">
			<p><?= 'Current price for ' 
					. $data[0] . ' is $<strong>' 
					. $data[1] . '</strong>'; ?></p>
			<input type="text" name="buyquote" id="buyquote" class="form-control hidden" 
				   value="<?= htmlspecialchars($data[2]); ?>" />
			<input type="text" name="buyname" id="buyname" class="form-control hidden" 
				   value="<?= htmlspecialchars($data[0]); ?>" />
			<input type="text" name="buyprice" id="buyprice" class="form-control hidden" 
				   value="<?= htmlspecialchars($data[1]); ?>" />
			<input type="number" name="buytotal" id="buytotal" class="form-control"
				   value="0" min="0" step="1" />
			<span class="help-block">Enter number of shares to buy</span>
			<button type="submit" class="btn btn-success btn-block form-control">
				Buy shares
			</button>
		</form>
	</div>

	<div class="form-group">
		<form id="form-quote" method="post">
			<input type="text" name="quotes" id="quotes" class="form-control" 
				   value="<?= (isset($_POST['quotes'])) ? 
					htmlspecialchars($_POST['quotes']) : '' ?>" autofocus />
			<?= "<span class='alerts $hidden_a'>$message</span>" ?>
			<span class="help-block">Enter quote abbreviation</span>
			<button type="submit" class="btn btn-primary btn-block form-control" >
				Get price
			</button>
		</form>
	</div>
	<span class="loading hidden">
			<img src="../model/ajax-loader.gif" alt="loading results" />
	</span>
	<div id="result-list"></div>
</div>
