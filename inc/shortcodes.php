<?php
/**
 * shortcodes.php
 * Provide shortcodes for direct theme integration with portal for public pages
 *
 * @version 1.0
 * @date 7/17/16 10:45 PM
 * @package Wordpress Employee Portal
 */


/**
 * Check to see if we have a) a valid user, and b) a user who's an admin or associated with the portal
 *
 * @param bool $id_only (false returns entire user object)
 * @return bool|int
 */
function portal_valid_user( $id_only = true )
{
	if( is_user_logged_in() )
	{
		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;
		if( current_user_can( 'access_portal' ) || current_user_can( 'edit_users' ) )
		{
			return ( $id_only ) ? $user_id : $current_user;
		}
	}
	return false;
}


function portal_login_form( $atts )
{
	$heading = '';
	$limit = '';
	extract( shortcode_atts( array(
		'heading' => 'Notes',
		'limit' => '',
	), $atts ) );

	if( function_exists( 'portal_login' ) && !is_user_logged_in() )
	{
		add_action( 'wp_head', 'portal_custom_css' );
		portal_login();
	}
}
add_shortcode( 'portal-login', 'portal_login_form' );


/**
 * Output the username for the currently logged in user viewing the portal
 *
 * @return string
 */
function portal_username()
{
	add_action( 'wp_head', 'portal_custom_css' );
	$user = portal_valid_user( false );
	if( $user !== false )
	{
		return '<h1>'. $user->user_nicename .'</h1>';
	}
	return '';
}
add_shortcode( 'portal-username', 'portal_username' );


/**
 * @param $atts
 * @param null $content
 * @return string
 */
function portal_notes( $atts, $content = null )
{
	$heading = '';
	$limit = '';
	$excerpt = true;
	extract( shortcode_atts( array(
		'heading' => 'Notes',
		'limit' => '',
		'excerpt' => (bool)$excerpt,
	), $atts ) );

	$string = '<h2>'.$heading.'</h2>';

	$data = array();
	$user = portal_valid_user();
	if( $user !== false )
	{
		global $wpdb;
		$params = array(
			$user,
		);
		$sql = "SELECT ".PORTAL_NOTES.".id AS note_id, `added`, `user_id`, `title`, `note` AS shorted FROM ". PORTAL_NOTES ." WHERE (user_id = %s OR user_id IS NULL) ORDER BY added DESC";
		if( !empty( $limit ) )
		{
			$sql .= " LIMIT %d";
			$params[] = $limit;
		}

		$results = $wpdb->get_results( $wpdb->prepare( $sql, $params ) );
		if( !empty( $results ) )
		{
			$string .= '<ul class="portal-list">';
			foreach( $results as $post )
			{
				$label = ( $excerpt == true ) ? '<span class="js-portal-opener" data-content="'.htmlentities( stripslashes( $post->shorted ) ).'">'.$post->title .'</span>' : stripslashes( $post->shorted );
				$string .= '<li>'. $label .'</li>';
			}
			$string .= '</ul>';
		}

	}

	return $string;
}
add_shortcode( 'portal-notes', 'portal_notes' );


/**
 * @param $atts
 * @param null $content
 * @return string
 */
function portal_calendar( $atts, $content = null )
{
	$heading = '';
	$limit = '';
	extract( shortcode_atts( array(
		'heading' => 'Calendar',
		'limit' => '',
	), $atts ) );

	$string = '<h2>'.$heading.'</h2>';

	$user = portal_valid_user();
	if( $user !== false )
	{
		global $wpdb;
		$params = array(
			$user,
		);
		$sql = "SELECT ".PORTAL_SCHEDULE.".id AS schedule_id, `starttime`, `endtime`, `scheduled`, `schedule_note`, `position`, `user_id` FROM ". PORTAL_SCHEDULE ." WHERE (user_id = %d OR user_id IS NULL) ORDER BY starttime DESC";
		if( !empty( $limit ) )
		{
			$sql .= " LIMIT %d";
			$params[] = $limit;
		}

		$query = $wpdb->prepare( $sql, $params );
		$results = $wpdb->get_results( $query );
		if( !empty( $results ) )
		{
			$string .= '<ul class="portal-list">';
			foreach( $results as $result )
			{

				$datetime = date( 'F j, Y', $result->starttime );
				$timerange = date( 'ga', $result->starttime ) .' - '. date( 'ga', $result->endtime );
				$string .= '<li>';
					if( !empty( $result->position ) )
					{
						$string .= '<strong>'. $result->position .'</strong><br />';
					}
					$string .= $datetime . ' | '. $timerange . '<br />';
					$string .= stripslashes( $result->schedule_note );

				$string .= '</li>';
			}

			$string .= '</ul>';
		}
	}

	return $string;
}
add_shortcode( 'portal-calendar', 'portal_calendar' );


/**
 * @param $atts
 * @param null $content
 * @return string
 */
function portal_links( $atts, $content = null )
{
	$heading = '';
	$limit = '';
	extract( shortcode_atts( array(
		'heading' => 'Links',
		'limit' => '',
	), $atts ) );

	$string = '<h2>'. $heading .'</h2>';

	$data = array();
	$user = portal_valid_user();
	if( $user !== false )
	{
		global $wpdb;
		$params = array();
		$sql = "SELECT id, title, uri, featured FROM ". PORTAL_LINKS;
		$sql .= " ORDER BY added DESC";
		if( !empty( $limit ) )
		{
			$sql .= " LIMIT %d";
			$params[] = $limit;
		}

		$results = $wpdb->get_results( $wpdb->prepare( $sql, $params ) );
		if( !empty( $results ) )
		{
			$string .= '<ul class="portal-list">';
			foreach( $results as $post )
			{
				//If we don't have a valid link, just go on then...
				if( filter_var( $post->uri, FILTER_VALIDATE_URL ) === FALSE )
				{
					continue;
				}
				$string .= '<li><a href="'. $post->uri.'" target="_blank">'. stripslashes( $post->title ) .'</a></li>';
			}
			$string .= '</ul>';
		}

	}

	return $string;
}
add_shortcode( 'portal-links', 'portal_links' );


/**
 * @param $atts
 * @param null $content
 * @return string
 */
function portal_featured_link( $atts, $content = null )
{
	$id = '';
	extract( shortcode_atts( array(
		'id' => '',
	), $atts ) );

	$string = '';

	$user = portal_valid_user();
	if( $user !== false )
	{
		global $wpdb;
		$params = array();
		$sql = "SELECT id, title, uri, featured FROM ". PORTAL_LINKS;
		if( !empty( $id ) )
		{
			$sql .= " WHERE id = %d";
			$params[] = $id;
		}
		else
		{
			$sql .= " WHERE featured = %d";
			$params[] = 1;
		}
		$sql .= " ORDER BY added DESC LIMIT 1";

		$link = $wpdb->get_row( $wpdb->prepare( $sql, $params ) );
		if( !empty( $link ) )
		{
			$string = '<div class="portal-button-wrapper">';
			$string .= '<a class="portal-featured-link-button" href="'. $link->uri.'" target="_blank">'.$link->title.'</a>';
			$string .= '</div>';
		}

	}

	return $string;
}
add_shortcode( 'portal-featured-link', 'portal_featured_link' );


/**
 * @param $atts
 * @param null $content
 * @return string
 */
function portal_downloads( $atts, $content = null )
{
	$heading = '';
	$limit = '';
	extract( shortcode_atts( array(
		'heading' => 'Downloads',
		'limit' => '',
	), $atts ) );

	$results = array();

	$string = '<h2>'. $heading .'</h2>';
	$user = portal_valid_user();
	if( $user !== false )
	{
		$results = get_option( PORTAL_NOTE_STORE );
	}
	if( !empty( $results ) )
	{
		$string .= '<ul class="portal-list">';
		$c = 0;
		foreach( $results as $k => $post )
		{
			$link = wp_get_attachment_url( $post );
			$title = get_the_title( $post );
			$title = !empty( $title ) ? $title : basename( $link );
			$string .= '<li>';
				$string .= '<a href="'.$link.'" target="_blank" title="View" class="js-portal-window">'. $title .'</a>';
			$string .= '</li>';
			$c++;
			if( !empty( $limit ) && $c >= (int)$limit )
			{
				break;
			}
		}
		$string .= '</ul>';
	}

	return $string;
}
add_shortcode( 'portal-downloads', 'portal_downloads' );