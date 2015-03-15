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
	<div id="result-block" class="hidden">
		<div id="rheader" class="clear">
			<hr />
			<button>Add to Portfolio</button>
			<h2 id="rname"></h2>
			<h5 id="rlegend"></h5>
			<p id="rquote" class="hidden"></p>
			<h2 id="rprice"></h2>
			<ul id="rselect">
				<li class="active">Candle chart</li>
				<li>History table</li>
			</ul>
			<hr />
			<ul id="rmenu">
				<li>1 day</li>
				<li>5 days</li>
				<li class="active">1 month</li>
				<li>3 months</li>
				<li>5 months</li>
				<li>1 year</li>
				<li>5 years</li>
			</ul>
			<p id="rrange"></p>
		</div>
		<div id="rchart"></div>
		<div id="rfooter"></div>
	</div>
</div>
