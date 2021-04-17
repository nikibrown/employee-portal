<?php
/**
 * Called on plugin uninstall for self-cleanup
 *
 * destroy-tables.php
 *
 * @version 1.0
 * @date 3/12/16 8:49 PM
 * @updated 18-Jul-2016
 * @package Wordpress Employee Portal
 */

if( !function_exists( 'destroy_notes_table' ) )
{
	/**
	 * Destroy the notes table
	 */
	function destroy_notes_table()
	{
		global $wpdb;
		$table_name = PORTAL_NOTES;

		$sql = "DROP TABLE IF EXISTS `{$table_name}`";
		$wpdb->query( $sql );
	}
}

if( !function_exists( 'destroy_schedule_table' ) )
{
	/**
	 * Destroy the schedule table
	 */
	function destroy_schedule_table()
	{
		global $wpdb;
		$table_name = PORTAL_SCHEDULE;

		$sql = "DROP TABLE IF EXISTS `{$table_name}`";
		$wpdb->query( $sql );
	}
}


if( !function_exists( 'destroy_links_table' ) )
{
	/**
	 * Destroy the links table
	 */
	function destroy_links_table()
	{
		global $wpdb;
		$table_name = PORTAL_LINKS;

		$sql = "DROP TABLE IF EXISTS `{$table_name}`";
		$wpdb->query( $sql );
	}
}