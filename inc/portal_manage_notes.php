<?php
/**
 * Manage notes for employees or all employees
 * portal_manage_notes.php
 *
 * @version 1.0
 * @date 3/12/16 4:30 PM
 * @package Wordpress Employee Portal
 */

$user_list = get_users( array( 'role' => 'portal_employee' ) );

$notes_table = new Portal_Wp_List_Table();
$notes_table->prepare_items();
?>
	<div class="wrap">
		<h2>Manage Notes <a href="admin.php?page=portal-notes&new" class="add-new-h2">Add New</a></h2>

		<?php portal_display_status(); ?>

		<?php $notes_table->display(); ?>
	</div>
<?php
