<?php
/**
 * Manage available forms provided to employees
 * portal_manage_forms.php
 *
 * @version 1.0
 * @date 3/12/16 4:30 PM
 * @package Wordpress Employee Portal
 */

$forms = new Portal_Wp_List_Table();
$forms->prepare_forms();
?>
	<div class="wrap">
		<h2>Manage Available Downloads</h2>

		<?php portal_display_status(); ?>

		<?php include( 'forms/new-form.php' ); ?>

		<?php $forms->display(); ?>
	</div>
<?php
