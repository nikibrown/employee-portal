<?php
/**
 * portal-settings.php
 *
 * @version 1.0
 * @date 7/19/16 6:54 AM
 * @package Wordpress Employee Portal
 */
?>
<div class="wrap">

	<h2>Employee Portal Settings</h2>

	<p>Configure notification options and preferences here.</p>

	<h2 class="nav-tab-wrapper">
		<a class="nav-tab nav-tab-active" href="<?php echo admin_url() ?>admin.php?page=portal-settings">Manage Portal Settings</a>
		<a class="nav-tab" href="<?php echo admin_url() ?>admin.php?page=portal-settings&instructions">View Instructions</a>
	</h2>

	<form method="post" action="options.php">
		<table class="form-table">
			<?php

			settings_fields( 'portal-settings-group' );
			do_settings_sections( 'portal-settings-group' );
			?>

			<tr valign="top">
				<th scope="row">
					<label>Enable Employee Notifications?</label>
				</th>
				<td>
					<select name="portal_send_notifications">
						<option value="0"<?php echo get_option( 'portal_send_notifications' ) == 0 ? ' selected="selected"' : ''; ?>>No</option>
						<option value="1"<?php echo get_option( 'portal_send_notifications' ) == 1 ? ' selected="selected"' : ''; ?>>Yes</option>
					</select>
					<br />
					<p class="description">When enabled, employees will receive notifications for the option types selected below</p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">Event Types to <strong>Send</strong> notifications for</th>
				<td>
					<div style="max-height: 200px; overflow: auto;">
						<?php
						$enabled = get_option( 'portal_notifications_for' );
						global $portal_notification_types;
						foreach( $portal_notification_types as $type => $label )
						{
							$sel = '';
							if( is_array( $enabled ) && in_array( $type, $enabled ) )
							{
								$sel = ' checked="checked"';
							}
							echo '<input type="checkbox" name="portal_notifications_for[]" value="'.$type.'" id="portal_notify_for_'.$type.'"'.$sel.'> <label for="portal_notify_for_'.$type.'">'.$label.'</label><br />';
						}
						?>
					</div>
				</td>
			</tr>


			<tr valign="top">
				<th scope="row">Custom Styles<br />
					<p class="description">Loaded wherever widgets are used.<br />
					See the <a href="<?php echo admin_url() ?>admin.php?page=portal-settings&instructions">instructions</a> for a full list of available selectors</p>
				</th>
				<td>
					<textarea cols="100%" rows="25" name="portal_custom_css" id="portal_custom_css" aria-describedby="portal_custom_css-description"><?php echo get_option( 'portal_custom_css' ); ?></textarea>
				</td>
			</tr>
			<tr valign="top">
			</tr>

		</table>

		<?php submit_button(); ?>

	</form>
</div>
