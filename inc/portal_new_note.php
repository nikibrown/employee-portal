<?php
/**
 * Add a new note for an employee or all employees
 * portal_new_note.php
 *
 * @version 1.0
 * @date 3/12/16 9:18 PM
 * @package Wordpress Employee Portal
 */
$user_list = get_users( array( 'role' => 'portal_employee' ) );
?>
	<div class="wrap">
		<h2>Add a new Note</h2>

		<?php portal_display_status(); ?>

		<?php include( 'forms/new-note.php' ); ?>
	</div>
<?php
