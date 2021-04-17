<?php
/**
 * Form to add a new note
 * new-note.php
 *
 * @version 1.0
 * @date 3/12/16 9:09 PM
 * @package Wordpress Employee Portal
 */
?>
<form method="post" action="">
	<input type="hidden" name="portal_new_note_submit" value="true" />
	<table class="form-table">

		<tr valign="top">
			<th scope="row">
				<label>Title (optional)</label>
			</th>
			<td>
				<input type="text" name="title" autofocus="autofocus" value="" placeholder="" />
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label>Employee</label>
			</th>
			<td>
				<select name="user_id">
					<?php
					echo '<option value="">All Employees</option>';
					foreach( $user_list as $c )
					{
						echo '<option value="'.$c->data->ID.'">'. $c->data->user_nicename .'</option>';
					}
					?>
				</select>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label>Note</label>
			</th>
		</tr>
		<tr valign="top">
			<td scope="row" colspan="2">
				<?php
				$content = '';
				$editor_id = 'portal_note';
				$settings = array( 'media_buttons' => false );
				$settings = array( 'textarea_name' => 'note' );
				wp_editor( $content, $editor_id, $settings );
				?>
			</td>
		</tr>

	</table>

	<?php submit_button( 'Add note' ); ?>

</form>