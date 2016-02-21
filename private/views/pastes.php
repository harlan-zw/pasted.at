<?php
$loggedIn = Account_AccountAPI::getLoggedIn() !== false;
?>

<div class="block-content-container">


	<h2 class="inline">My Pastes</h2>
	<hr>
	<div style="overflow-y:auto; max-height:400px;">
		<?php if ($loggedIn) {?>
		<table class="sortable" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th>ID</th>
					<th>Title</th>
					<th>Time</th>
					<th>Delete</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$myPastes = PasteHandler_PasteAPI::getPastesForUser();
				if ($myPastes !== false) 
					foreach ($myPastes as $value) {
						$paste = new PasteHandler_Paste();
						$paste->fill($value);
						publish('<tr>');
						publish('<td><a '. generateLinkStr('p/' . $paste->id) . '>' . $paste->id . '</a></td>');
						publish('<td>' . $paste->title . '</td>');
						publish('<td>' . $paste->since . ' </td>');
						publish('<td><a class="table_paste_delete" href="#"><i class="fa fa-trash-o"></i></a></td>');
						publish('</tr>');
					}
					?>
				</tbody>
			</table>
			<?php } else { ?>

			<span class="error">You need to be logged in to get the features of this page.</span>
			<?php } ?>
		</div>
	</div>
