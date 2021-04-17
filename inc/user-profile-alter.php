<?php
/**
 * Taps into user profiles to add color picker for calendars
 *
 * user-profile-alter.php
 *
 * @version 1.0
 * @date 3/14/16 11:36 AM
 * @package Wordpress Employee Portal
 */

add_action( 'show_user_profile', 'extra_portal_profile_fields' );
add_action( 'edit_user_profile', 'extra_portal_profile_fields' );
function extra_portal_profile_fields( $user )
{
?>
	<h3>User Profile Color for Schedule</h3>

	<table class="form-table">
		<tr>
			<th><label for="user_avatar">User avatar</label></th>
			<td>
				<input class="js-portal-color-picker" name="portal_color" type="text" value="
                    <?php $color = get_the_author_meta( 'portal_color', $user->ID );
				echo $color ? $color : '';?>" />
				<span class="description"><?php _e("Please select a color for your schedule."); ?></span>
			</td>
		</tr>
	</table>
<?php
}

add_action( 'personal_options_update', 'portal_extra_user_fields' );
add_action( 'edit_user_profile_update', 'portal_extra_user_fields' );

function portal_extra_user_fields( $user_id )
{
	if ( !current_user_can( 'edit_user', $user_id ) ) {
		return false;
	}
	else
	{

		if( isset( $_POST['portal_color'] ) && $_POST['portal_color'] != "" && preg_match( '/^#[a-f0-9]{6}$/i', $_POST['portal_color'] ) )
		{
			update_usermeta( $user_id, 'portal_color', $_POST['portal_color'] );
		}
	}
}