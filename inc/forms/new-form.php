<?php
/**
 * Form to upload a new form available to all employees
 * new-form.php
 *
 * @version 1.0
 * @date 3/13/16 10:07 PM
 * @package Wordpress Employee Portal
 */
?>

<form  method="post" enctype="multipart/form-data">
	<input type="hidden" name="portal_new_file_submit" value="true" />
	<table class="form-table">

		<tr valign="top">
			<th scope="row">
				<label>Download Label (optional)</label>
			</th>
			<td>
				<input type="text" name="title" autofocus="autofocus" value="" placeholder="" />
			</td>
		</tr>

		<tr valign="top">
			<th scope="row">
				<label>Download</label>
			</th>
			<td>
				<input type='file' id='upload_new_form' name='upload_new_form'></input>
			</td>
		</tr>

	</table>

	<?php submit_button( 'Add New Download' ); ?>

</form>
