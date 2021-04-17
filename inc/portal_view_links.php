<?php
/**
 * portal_view_links.php
 *
 * @version 1.0
 * @date 7/18/16 7:22 PM
 * @package Wordpress Employee Portal
 */
$links = new Portal_Wp_List_Table();
$links->prepare_links();
?>
	<div class="wrap">
		<h2>Available Links</h2>
		<?php $links->display(); ?>
	</div>
<?php
