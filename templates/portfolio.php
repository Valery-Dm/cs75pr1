<div class="result alerts <?= $hidden_a; ?>" >
	<h3><?= htmlspecialchars($message); ?></h3>
</div>
<div class="result <?= $hidden_d; ?>">
	<table class="table table-hover">
		<thead>
			<tr>
				<th>Share</th>
				<th>Q.</th>
				<th>Price per share</th>
				<th>Date</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($data as $row): ?>		
				<tr>
					<td>
						<?= '(' .
							htmlspecialchars($row['sharesquote']) .
							')<br />' . 
							htmlspecialchars($row['sharesname']); 
						?>
					</td>
					<td><?= htmlspecialchars($row['sharesq']); ?></td>
					<td><?= number_format($row['sharesprice'], 2); ?></td>
					<td><?= htmlspecialchars($row['sharesdate']); ?></td>
				</tr>
		<?php endforeach ?>
		</tbody>
	</table>
</div>