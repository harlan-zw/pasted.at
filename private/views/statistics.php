<div class="block-content-container">
	<h2 class="inline">Statistics</h2>
	<hr>
	<div style="overflow-y:auto; max-height:400px;">
		<table class="sortable" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th>Statistic</th>
					<th>Value</th>
				</tr>
			</thead>
			<tbody>
				<?php

				$op = new ORM_Operator(new Statistics());
				$op->forLoop(function($entry) {
					publish('<tr class="padded">');
					publish('<td>' . $entry->name . ' </td><td>' . $entry->value . '</td>');
					publish('</tr>');

				});


				?>
			</tbody>
		</table>
	</div>

</div>