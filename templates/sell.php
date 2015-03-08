<div class="col-md-3"></div>
<div class="col-md-6 center">

	<div id="quote-message" class="result alerts <?= $hidden_m; ?>" >
		<h3><?= htmlspecialchars($message); ?></h3>
	</div>

	<div class="form-group <?= $hidden_d; ?>">
		<form id="form-sell" method="post">
			<input list="sharesdata" name="shares" id="shares" class="form-control"
				   value="<?= (isset($_POST['shares'])) ? $_POST['shares'] : '' ?>"
				   autofocus />
			<datalist id="sharesdata">
			<?php foreach($data as $item): ?>
				<option value="<?= htmlspecialchars($item['sharesquote']); ?>" >
					<?= htmlspecialchars($item['sharesname']); ?>
				</option>
			<?php endforeach; ?>
			</datalist>
			<?= '<span class="help-block alerts ' . $hidden_a . '">'
					 . htmlspecialchars($message) .
				 '</span>'; ?>
			<span class="help-block">Choose shares name</span>
			<input type="number" name="sharesq" id="sharesq" 
				   class="form-control" min="0" step="1"
				   value="<?= (isset($_POST['sharesq'])) ? $_POST['sharesq'] : 0 ?>" />
			<span class="help-block">Enter quantity</span>
			<button type="submit" class="btn btn-primary btn-block form-control" >
				Sell shares
			</button>
		</form>
	</div>

</div>
<div class="col-md-3"></div>