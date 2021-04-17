<?php
/**
 * View the schedule calendar
 * portal_schedule_calendar.php
 *
 * @version 1.0
 * @date 3/14/16 5:46 AM
 * @package Wordpress Employee Portal
 */

$schedules = new Portal_Wp_List_Table();
?>
	<style>
		#portal_calendar {
			padding-top: 1rem;
			margin: 0 auto;
		}
	</style>
	<div class="wrap">
		<h2>Manage Calendar <a href="admin.php?page=portal-scheduling&new" class="add-new-h2">Add New Calendar</a></h2>

		<h2 class="nav-tab-wrapper">
			<a class="nav-tab" href="<?php echo admin_url() ?>admin.php?page=portal-scheduling">Manage Calendar</a>
			<a class="nav-tab nav-tab-active" href="<?php echo admin_url() ?>admin.php?page=portal-scheduling">View Calendar</a>
		</h2>

		<div id='portal_calendar'></div>

	</div>
	<script>
		var calEvents = <?php echo $schedules->prepare_schedules( true ); ?>;
	</script>
<?php

