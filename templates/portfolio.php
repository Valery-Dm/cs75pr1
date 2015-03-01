<div class="alerts <?= $hidden_a; ?>" >
	<h3><?= (is_string($data)) ? $data : ''; ?></h3>
</div>
<div class="<?= $hidden_d; ?>">
	<pre><?php var_dump($data); ?></pre>
	
</div>