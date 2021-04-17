<?php
/**
 * Manage schedule items
 * portal_schedule_manage.php
 *
 * @version 1.0
 * @date 3/12/16 4:27 PM
 * @package Wordpress Employee Portal
 */

$schedules = new Portal_Wp_List_Table();
$schedules->prepare_schedules();
?>
	<div class="wrap">
		<h2>Manage Calendar <a href="#" class="add-new-h2 js-new-schedule">Add New Calendar</a></h2>

		<h2 class="nav-tab-wrapper">
			<a class="nav-tab nav-tab-active" href="<?php echo admin_url() ?>admin.php?page=portal-scheduling">Manage Calendar</a>
			<a class="nav-tab" href="<?php echo admin_url() ?>admin.php?page=portal-scheduling&view">View Calendar</a>
		</h2>

		<?php portal_display_status(); ?>

		<?php include( 'forms/add-schedule.php' ); ?>

		<?php $schedules->display(); ?>
	</div>
<?php
