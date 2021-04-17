<?php
/**
 * View the employee schedules
 * portal_view_schedule.php
 *
 * @version 1.0
 * @date 3/12/16 4:49 PM
 * @package Wordpress Employee Portal
 */
global $schedule_title, $viewmode;
$schedules = new Portal_Wp_List_Table();
?>
	<style>
		#portal_calendar {
			padding-top: 1rem;
			margin: 0 auto;
			max-height: 90%;
		}
	</style>
	<div class="wrap">
		<?php if( $schedule_title ) { ?>
		<h2>View Schedule</h2>
		<?php } ?>

		<div id='portal_calendar'></div>

	</div>
	<script>
		var calEvents = <?php echo $schedules->prepare_schedules( true ); ?>;
		var viewType = '<?php echo $viewomde; ?>';
	</script>
<?php

