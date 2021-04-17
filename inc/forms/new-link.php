<?php
/**
 * new-link.php
 *
 * @version 1.0
 * @date 7/18/16 7:21 PM
 * @package Wordpress Employee Portal
 */
$button_text = 'Add New Link';
if( isset( $_GET['edit'] ) && !empty( $_GET['edit'] ) )
{
	$button_text = 'Edit Link';
	global $wpdb;
	$sql = $wpdb->prepare( "SELECT id, title, uri, featured FROM ". PORTAL_LINKS ." WHERE id = %d LIMIT 1", array( $_GET['edit'] ) );
	$content = $wpdb->get_row( $sql );
}
?>
<form  method="post" enctype="multipart/form-data">
	<?php if( isset( $_GET['edit'] ) && !empty( $_GET['edit'] ) )
	{
		echo '<input type="hidden" name="portal_new_link_edit" value="'.$_GET['edit'].'" />';
	}
	else
	{
		echo '<input type="hidden" name="portal_new_link_submit" value="true" />';
	}
	?>
	<table class="form-table">

		<tr valign="top">
			<th scope="row">
				<label>Description</label>
			</th>
			<td>
				<input type="text" name="title" autofocus="autofocus" value="<?php echo isset( $content->title ) ? $content->title : ''; ?>" placeholder="" />
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label>URI</label>
			</th>
			<td>
				<input type="text" name="uri" value="<?php echo isset( $content->uri ) ? $content->uri : ''; ?>" placeholder="http://..." />
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label>Featured?</label>
			</th>
			<td>
				<input type="checkbox" name="featured" value="1"<?php echo ( isset( $content->featured ) && $content->featured == 1 ) ? ' checked="checked"' : ''; ?> />
			</td>
		</tr>

	</table>

	<?php submit_button( $button_text ); ?>

</form>