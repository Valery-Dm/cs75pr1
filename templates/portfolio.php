<div class="alerts <?= $hidden_a; ?>" >
	<h3><?= (is_string($data)) ? htmlspecialchars($data) : ''; ?></h3>
</div>
<div class="result <?= $hidden_d; ?>">
	<table class="table table-responsive">
		<thead>
			<tr>
				<th>Share symbol</th>
				<th>Share name</th>
				<th>Price per share</th>
				<th>Date</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($data as $row): ?>		
				<tr>
					<td><?= htmlspecialchars($row['sharesquote']); ?></td>
					<td><?= htmlspecialchars($row['sharesname']); ?></td>
					<td><?= number_format($row['sharesprice'], 2); ?></td>
					<td><?= htmlspecialchars($row['sharesdate']); ?></td>
				</tr>
		<?php endforeach ?>
		</tbody>
	</table>
</div>