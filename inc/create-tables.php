<?php
/**
 * Creates plugin tables on install
 * create-tables.php
 *
 * @version 1.0
 * @date 3/12/16 8:48 PM
 * @updated 18-Jul-2016
 * @package Wordpress Employee Portal
 */

if( !function_exists( 'create_notes_table' ) )
{
	/**
	 * Create the notes table
	 */
	function create_notes_table()
	{
		global $wpdb;
		$table_name = PORTAL_NOTES;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE `$table_name` (
		  `id` int(9) NOT NULL AUTO_INCREMENT,
		  `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `user_id` int(11) DEFAULT NULL,
		  `title` varchar(220) DEFAULT NULL,
		  `note` text NOT NULL,
		  PRIMARY KEY (`id`)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

	}
}


if( !function_exists( 'create_schedule_table' ) )
{
	/**
	 * Create the schedule table
	 */
	function create_schedule_table()
	{
		global $wpdb;
		$table_name = PORTAL_SCHEDULE;

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `position` varchar(220) DEFAULT NULL,
          `starttime` int(11) DEFAULT NULL,
          `endtime` int(11) DEFAULT NULL,
          `scheduled` datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
          `user_id` int(11) DEFAULT NULL,
          `schedule_note` text NOT NULL,
          PRIMARY KEY id (id)
        ) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}
}


if( !function_exists( 'create_links_table' ) )
{
	/**
	 * Create the links table
	 */
	function create_links_table()
	{
		global $wpdb;
		$table_name = PORTAL_LINKS;

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `title` varchar(220) DEFAULT NULL,
          `uri` varchar(220) DEFAULT NULL,
          `featured` tinyint(1) DEFAULT '0',
          `added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
          PRIMARY KEY id (id)
        ) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}
}