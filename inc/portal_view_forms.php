<?php
/**
 * View available uploaded forms as an employee
 * portal_view_forms.php
 *
 * @version 1.0
 * @date 3/12/16 4:44 PM
 * @package Wordpress Employee Portal
 */

$forms = new Portal_Wp_List_Table();
$forms->prepare_forms();
?>
	<div class="wrap">
		<h2>Available Downloads</h2>
		<?php $forms->display(); ?>
	</div>
<?php
