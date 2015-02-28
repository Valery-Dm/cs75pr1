<div class="form-group">
	<form method="post">
		<div class="col-md-3"></div>
		<div class="col-md-6 center">
			<div class="result">
				<label for="price"><?= $pricelabel; ?></label>
				<input type="text" name="price" id="price" 
					   value="<?= $price; ?>"/>
			</div>
			<label for="quotes">Quote</label>
			<input type="text" name="quotes" id="quotes" class="form-control" 
				   value="<?= (isset($_POST['quotes'])) ? $_POST['quotes'] : '' ?>" autofocus />
			<?= "<span class='alerts $hidden_a'>$data</span>" ?>
			<span class="help-block">Enter quote abbreviation</span><br />
			<button type="submit" class="btn btn-primary btn-block form-control" >Get price</button>
		</div>
		<div class="col-md-3"></div>
	</form>
</div>