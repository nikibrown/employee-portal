<?php
/**
 * View notes
 * portal_view_notes.php
 *
 * @version 1.0
 * @date 3/12/16 4:47 PM
 * @package Wordpress Employee Portal
 */
$user_list = get_users( array( 'role' => 'portal_employee' ) );

$id = isset( $_GET['view'] ) && !empty( $_GET['view'] ) ? $_GET['view'] : null;
$exampleListTable = new Portal_Wp_List_Table();
$exampleListTable->prepare_items_employee( $id );
?>
	<div class="wrap">
		<?php if( !isset( $_GET['view'] ) ) { ?>
			<h2>Viewing Notes</h2>
		<?php } else { ?>
			<h2>Viewing Notes <a href="admin.php?page=notes" class="add-new-h2">Back</a></h2>
		<?php } ?>


		<?php $exampleListTable->display(); ?>
	</div>
<?php
