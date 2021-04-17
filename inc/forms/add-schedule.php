<?php
/**
 * Form to add/edit a schedule item
 * add-schedule.php
 *
 * @version 1.0
 * @date 3/14/16 6:38 AM
 * @package Wordpress Employee Portal
 */

$user_list = get_users( array( 'role' => 'portal_employee' ) );

$button_text = 'Add Scheduled Item';
if( isset( $_GET['edit'] ) && !empty( $_GET['edit'] ) )
{
	$button_text = 'Edit Scheduled Item';
	global $wpdb;
	$sql = "SELECT ".PORTAL_SCHEDULE.".id AS schedule_id, `starttime`, `endtime`, `scheduled`, `schedule_note`, `position`, `user_id` FROM ". PORTAL_SCHEDULE ." WHERE id = %d LIMIT 1";
	$content = $wpdb->get_row( $wpdb->prepare( $sql, array( $_GET['edit'] ) ) );
}
?>
<div class="js-new-schedule-form<?php echo ( !isset( $_GET['new'] ) && !isset( $_GET['edit'] ) ) ? ' portal-form-hide': ''; ?>">
	<form method="post" action="">
		<?php if( isset( $_GET['edit'] ) && !empty( $_GET['edit'] ) )
		{
			echo '<input type="hidden" name="portal_new_schedule_edit" value="'.$_GET['edit'].'" />';
		}
		else
		{
			echo '<input type="hidden" name="portal_new_schedule_submit" value="true" />';
		}
		?>
		<table class="form-table">

			<tr valign="top">
				<th scope="row">
					<label>Position</label>
				</th>
				<td>
					<input type="text" name="position" value="<?php echo isset( $content->position ) ? stripslashes( $content->position ) : ''; ?>" />
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label>Start/End</label>
				</th>
				<td>
					<input type="text" class="js-portal-datetime" name="starttime" value="<?php echo isset( $content->starttime ) ? date( 'Y/m/d h:i', $content->starttime ) : ''; ?>" placeholder="Start time" />
					<input type="text" class="js-portal-datetime" name="endtime" value="<?php echo isset( $content->endtime ) ? date( 'Y/m/d h:i', $content->endtime ) : ''; ?>" placeholder="End time" />
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label>Employee</label>
				</th>
				<td>
					<select name="user_id">
						<?php
						echo '<option value="">Select Employee</option>';
						echo '<option value="">All Employees</option>';
						foreach( $user_list as $c )
						{
							$sel = '';
							if( isset( $content->user_id ) && $content->user_id == $c->data->ID )
							{
								$sel = ' selected="selected"';
							}
							echo '<option value="'.$c->data->ID.'"'.$sel.'>'. $c->data->user_nicename .'</option>';
						}
						?>
					</select>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label>Note</label>
				</th>
				<td scope="row">
					<?php
					$default_content = ( isset( $content->schedule_note ) ? stripslashes( $content->schedule_note ) : '' );
					$editor_id = 'schedule_note';
					$settings = array( 'textarea_name' => 'schedule_note', 'teeny' => true, 'media_buttons' => false );
					wp_editor( $default_content, $editor_id, $settings );
					?>
				</td>
			</tr>

		</table>

		<?php submit_button( $button_text ); ?>

	</form>
</div>