<?php
/*
Plugin Name: Wordpress Employee Portal
Plugin URI: http://www.rippkedesign.com
Version: 1.2.1
Description: Full featured employee portal to handle scheduling, add forms for employees, manage links, and add notes for individual employees or all employees.
Author: Rippke Design
Author URI: http://www.rippkedesign.com
License: MIT
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) )
{
	echo 'Good try ;)';
	exit;
}

define( 'PORTAL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'PORTAL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PORTAL_VERSION', '1.2.1' );
register_activation_hook( __FILE__, 'portal_install' );
register_deactivation_hook( __FILE__, 'portal_uninstall' );
add_action( 'admin_init', 'register_portal_settings' );
add_action( 'admin_menu', 'portal_admin_pages' );

//Get the user specific date format
$date_format = get_option( 'date_format' );
$time_for = get_option( 'time_format' );
global $wpdb;

define( 'PORTAL_DATE_FORMAT', $date_format );
define( 'PORTAL_FULL_TIMESTAMP', $date_format . ' '. $time_for );
define( 'PORTAL_NOTES', $wpdb->prefix . "portal_notes" );
define( 'PORTAL_SCHEDULE', $wpdb->prefix . "portal_schedule" );
define( 'PORTAL_LINKS', $wpdb->prefix . "portal_links" );
define( 'PORTAL_NOTE_STORE', 'portal_files' ); //wp_options key for stored forms

//Available notification types for employees
$portal_notification_types = array(
	'notes' => 'Notes',
	'downloads' => 'Downloads',
	'links' => 'Links',
	'schedules' => 'Schedules',
);

//Default selected notification types for employees
$portal_default_notify = array(
	'notes' => 'Notes',
	'downloads' => 'Downloads',
	'links' => 'Links',
	'schedules' => 'Schedules',
);

if( !is_admin() )
{
	include PORTAL_PLUGIN_DIR . '/inc/shortcodes.php';
}

if( !function_exists( 'portal_custom_css' ) )
{
	/**
	 *
	 */
	function portal_custom_css()
	{
		$css = get_option( 'portal_custom_css' );
		if( !empty( $css ) )
		{
			echo '<style type="text/css" id="portal_custom_css">';
			echo stripslashes( $css );
			echo '</style>';
		}
	}
}


if( !function_exists( 'portal_check_widgets' ) )
{
	/**
	 * Checks to see if any of the shortcodes are getting used
	 * If they are, load up any custom CSS used in the settings
	 */
	function portal_check_widgets()
	{
		global $post;
		$possible_widgets = array(
				'portal-login',
				'portal-username',
				'portal-notes',
				'portal-calendar',
				'portal-links',
				'portal-featured-link',
				'portal-downloads',
		);
		foreach( $possible_widgets as $check )
		{
			if( has_shortcode( $post->post_content, $check ) )
			{
				add_action( 'wp_head', 'portal_custom_css' );
				wp_enqueue_script( 'portal-utils', plugins_url( 'assets/js/portal-utils.js', __FILE__ ), array( 'jquery' ), PORTAL_VERSION, true );
				break;
			}
		}
	}

	add_action( 'wp_enqueue_scripts', 'portal_check_widgets' );
}

function enqueue_calendar()
{
	wp_enqueue_script( 'jqueryui' );
	wp_enqueue_style( 'portal-cal-styles', plugins_url( 'assets/css/fullcalendar.css', __FILE__ ), array(), PORTAL_VERSION );
	wp_enqueue_style( 'portal-cal-print', plugins_url( 'assets/css/fullcalendar.print.css', __FILE__ ), array(), PORTAL_VERSION, 'print' );
	wp_enqueue_script( 'portal-cal-moment', plugins_url( 'assets/js/lib/moment.min.js', __FILE__ ), array( 'jquery' ), PORTAL_VERSION, true );
	wp_enqueue_script( 'portal-calendar', plugins_url( 'assets/js/fullcalendar.min.js', __FILE__ ), array( 'jquery' ), PORTAL_VERSION, true );

	//Datetime pickers
	wp_enqueue_style( 'portal-datetimepicker-styles', plugins_url( 'assets/css/jquery.datetimepicker.css', __FILE__ ), array(), PORTAL_VERSION );
	wp_enqueue_script( 'portal-datetimepicker', plugins_url( 'assets/js/jquery.datetimepicker.full.js', __FILE__ ), array( 'jquery' ), PORTAL_VERSION, true );

	//Add the qtips to the calendar
	wp_enqueue_style( 'portal-qtip-styles', plugins_url( 'assets/css/jquery.qtip.min.css', __FILE__ ), array(), PORTAL_VERSION );
	wp_enqueue_script( 'portal-qtip', plugins_url( 'assets/js/jquery.qtip.min.js', __FILE__ ), array( 'jquery' ), PORTAL_VERSION, true );
}

function enqueue_portal_admin()
{
	// Css rules for Color Picker
	wp_enqueue_style( 'wp-color-picker' );

	wp_enqueue_style( 'portal-admin-styles', plugins_url( 'assets/css/portal-styles.css', __FILE__ ), array(), PORTAL_VERSION );
	wp_enqueue_script( 'portal-admin-script', plugins_url( 'assets/js/employee-portal.js', __FILE__ ), array( 'jquery', 'wp-color-picker' ), PORTAL_VERSION );

	// WP_List_Table is not loaded automatically so we need to load it in our application
	if( ! class_exists( 'WP_List_Table' ) )
	{
		require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
	}
	include_once( PORTAL_PLUGIN_DIR . '/inc/tableClass.php' );
}


// Load admin style sheet and JavaScript.
if( is_admin() )
{
	add_action( 'admin_enqueue_scripts', 'enqueue_calendar' );
	add_action( 'admin_enqueue_scripts', 'enqueue_portal_admin' );
	require_once( PORTAL_PLUGIN_DIR . 'inc/user-profile-alter.php' );
}


if( !function_exists( 'add_roles_on_plugin_activation' ) )
{
	/**
	 * Add the employee user role
	 */
	function add_roles_on_plugin_activation()
	{
		$result = add_role(
			'portal_employee',
			'Employee',
			array(
				'read' => true,  // true allows this capability
				'level_0' => true,
				'access_portal' => true,
			)
		);
		if ( null !== $result )
		{
			//echo 'Employee role created';
		}
		else
		{
			//echo 'The Employee role already exists';
		}
	}
}


if( !function_exists( 'portal_install' ) )
{
	/**
	 * Plugin installation function
	 */
	function portal_install()
	{
		global $portal_default_notify;
		include PORTAL_PLUGIN_DIR.'/inc/create-tables.php';
		//Delete custom post type and postmeta here
		add_roles_on_plugin_activation();
		create_notes_table();
		create_schedule_table();
		create_links_table();
		register_portal_settings();
		add_option( 'portal_send_notifications', '', '', 'yes' );
		add_option( 'portal_notifications_for', $portal_default_notify, '', 'yes' );
		add_option( 'portal_custom_css', '', '', 'yes' );
	}
}


if( !function_exists( 'register_portal_settings' ) )
{
	/**
	 * Register the portal configuration settings
	 */
	function register_portal_settings()
	{
		register_setting( 'portal-settings-group', 'portal_send_notifications' );
		register_setting( 'portal-settings-group', 'portal_notifications_for' );
		register_setting( 'portal-settings-group', 'portal_custom_css' );
	}
}


if( !function_exists( 'portal_uninstall' ) )
{
	/**
	 * Plugin uninstallation function
	 */
	function portal_uninstall()
	{
		include PORTAL_PLUGIN_DIR.'/inc/destroy-tables.php';
		//Remove the employee user role
		remove_role( 'portal_employee' );
		destroy_notes_table();
		destroy_schedule_table();
		destroy_links_table();
		delete_option( PORTAL_NOTE_STORE );
		delete_option( 'portal_notifications_for' );
		delete_option( 'portal_send_notifications' );
		delete_option( 'portal_custom_css' );
	}
}


if( !function_exists( 'portal_flash' ) )
{
	/**
	 * Handle outputting formatted success and error messages wordpress style
	 */
	function portal_flash( $message, $status = 'updated' )
	{
		echo '<div id="message" class="'.$status.' notice is-dismissible"><p>'.$message.'</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
	}
}


if( !function_exists( 'portal_display_status' ) )
{
	/**
	 * Single location to handle numeric display statuses based on actions
	 * Allows us to more easily reference messaging
	 * Uses portal_flash method
	 */
	function portal_display_status()
	{
		if( isset( $_GET['status'] ) && !empty( $_GET['status'] ) )
		{
			switch( $_GET['status'] )
			{
				//Note added
				case 1:
					portal_flash( 'Note added successfully!' );
				break;
				//Can't add note or delete note
				case 2:
				case 4:
					portal_flash( 'There has been an error.', 'error' );
				break;
				//Note deleted
				case 3:
					portal_flash( 'Note deleted successfully!' );
					break;
				case 5:
					portal_flash( 'Form added successfully!' );
				break;
				case 6:
					portal_flash( 'Unable to delete form', 'error' );
				break;
				case 7:
					portal_flash( 'Form deleted successfully!' );
				break;
				case 8:
					portal_flash( 'Calendar added successfully!' );
				break;
				case 9:
					portal_flash( 'Calendar removed successfully!' );
				break;
				case 10:
					portal_flash( 'Calendar updated successfully' );
				break;
				case 11:
					portal_flash( 'Link added successfully' );
				break;
				case 12:
					portal_flash( 'Link updated successfully' );
				break;
				case 13:
					portal_flash( 'Link removed successfully' );
				break;
			}
		}
	}
}


if( !function_exists( 'portal_notify' ) )
{
	/**
	 * @param $type
	 * @param null $user_id
	 * @param null $message
	 * @return bool|null
	 */
	function portal_notify( $type, $user_id = null, $message = null )
	{
		global $portal_notification_types;
		$notify = get_option( 'portal_send_notifications' );
		$enabled = get_option( 'portal_notifications_for' );
		if( $notify == 0 || !in_array( $type, array_keys( $portal_notification_types ) ) || !in_array( $type, $enabled ) )
		{
			return null;
		}

		$sendto = '';
		if( !is_null( $user_id ) )
		{
			$user_info = get_userdata( $user_id );
			$sendto = $user_info->user_email;
		}
		else
		{
			$staff = get_users( array( 'role__in' => array( 'portal_employee' ) ) );
			$sendto = array();
			// Array of WP_User objects.
			foreach ( $staff as $user )
			{
				$sendto[] = $user->user_email;
			}
		}

		if( empty( $sendto ) )
		{
			return null;
		}


		$subject = ( !isset( $subject ) || empty( $subject ) ) ? get_bloginfo( 'sitename' ) : $subject;
		$headers = 'From: '.get_bloginfo( 'sitename' ).' Portal <'.get_option( 'admin_email' ).'>' . "\r\n" . 'Subject: '.$subject."\r\n";
		$message = !is_null( $message ) ? $message : 'A new '. $type .' has been added to the Portal.  Please login to view.';

		if( wp_mail( $sendto, $subject, strip_tags( $message ), $headers ) )
		{
			return true;
		}
	}
}


/**
 * Process all the form submissions
 */
add_action( 'wp_loaded', 'wpa76991_portal_process_note_form' );
function wpa76991_portal_process_note_form()
{
	global $wpdb;

	//Save new notes
	if( isset( $_POST['portal_new_note_submit'] ) && !empty( $_POST['portal_new_note_submit'] ) )
	{
		$insertion = array(
			'title' => !empty( $_POST['title'] ) ? $_POST['title'] : null,
			'note' => $_POST['note'],
			'user_id' => !empty( $_POST['user_id'] ) ? $_POST['user_id'] : null,
			'added' => current_time( 'mysql', false ),
		);
		$status = $wpdb->insert( PORTAL_NOTES, $insertion );
		if( $status !== false )
		{
			$status = 1;

			$message = "A new note titled &quot;". $insertion['title'] ." has been added to the portal.\r\n";
			$message .= $insertion['note'];
			portal_notify( 'notes', $insertion['user_id'], $message );
		}
		else
		{
			$status = 2;
		}
		wp_redirect( 'admin.php?page=portal-notes&status='. $status );
		die;
	}

	//Delete notes
	if( isset( $_GET['page'] ) && $_GET['page'] == 'portal-notes' && isset( $_GET['delete'] ) && !empty( $_GET['delete'] ) )
	{
		$removal = array(
			'id' => $_GET['delete']
		);
		$status = $wpdb->delete( PORTAL_NOTES, $removal );
		if( $status !== false )
		{
			$status = 3;
		}
		else
		{
			$status = 4;
		}
		wp_redirect( 'admin.php?page=portal-notes&status='. $status );
		die;
	}

	//Handle form uploads
	if(isset( $_POST['portal_new_file_submit'] ) && isset( $_FILES['upload_new_form'] ) )
	{
		$forms = get_option( PORTAL_NOTE_STORE );

		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );

		$pdf = $_FILES['upload_new_form'];

		// Use the wordpress function to upload
		// test_upload_pdf corresponds to the position in the $_FILES array
		// 0 means the content is not associated with any other posts
		$params = array(
			'post_title' => isset( $_POST['title'] ) && !empty( $_POST['title'] ) ? $_POST['title'] : null
		);
		$attachment_id = media_handle_upload( 'upload_new_form', 0, $params );

		$message = '';
		if( is_wp_error( $attachment_id ) )
		{
			$status = 2;
			$message = $uploaded->get_error_message();
		}
		else
		{
			$status = 5;
			$forms[] = $attachment_id;
			update_option( PORTAL_NOTE_STORE, $forms );

			$link = wp_get_attachment_url( $attachment_id );
			$title = get_the_title( $attachment_id );
			$title = !empty( $title ) ? $title : basename( $link );
			$message = "A new download titled \"". $title."\" has been added to the portal, and may be downloaded at:\r\n{$link}";
			portal_notify( 'downloads', null, $message );
		}
		wp_redirect( 'admin.php?page=portal-forms&status='. $status .'&message='. $message );
		die;
	}

	//Delete forms
	if( isset( $_GET['form-delete'] ) && !empty( $_GET['form-delete'] ) )
	{
		$forms = get_option( PORTAL_NOTE_STORE );

		//Delete failed
		$message = '';
		if ( false === wp_delete_attachment( $_GET['form-delete'] ) )
		{
			$status = 6;
		}
		else
		{
			if( !empty( $forms ) )
			{
				foreach( $forms as $k => $v )
				{
					if( $v == $_GET['form-delete'] )
					{
						unset( $forms[$k] );
						break;
					}
				}
				update_option( PORTAL_NOTE_STORE, $forms );
			}
			$status = 7;
		}

		wp_redirect( 'admin.php?page=portal-forms&status='. $status .'&message='. $message );
		die;
	}

	//Add a new schedule item
	if( isset( $_POST['portal_new_schedule_submit'] ) && !empty( $_POST['portal_new_schedule_submit'] ) )
	{
		$insertion = array(
			'position' => !empty( $_POST['position'] ) ? $_POST['position'] : null,
			'starttime' => strtotime( $_POST['starttime'] ),
			'endtime' => strtotime( $_POST['endtime'] ),
			'user_id' => !empty( $_POST['user_id'] ) ? $_POST['user_id'] : null,
			'scheduled' => current_time( 'mysql', false ),
			'schedule_note' => !empty( $_POST['schedule_note'] ) ? $_POST['schedule_note'] : '',
		);
		$status = $wpdb->insert( PORTAL_SCHEDULE, $insertion );
		if( $status !== false )
		{
			$status = 8;

			$message = "A new schedule has been added\r\n";
			$message .= "From ". date( PORTAL_FULL_TIMESTAMP, $insertion['starttime'] ) ." to  ". date( PORTAL_FULL_TIMESTAMP, $insertion['endtime'] ) ."\r\n";
			if( !empty( $insertion['position'] ) )
			{
				$message .= "Position: ". $insertion['position'] ."\r\n";
			}
			if( !empty( $insertion['schedule_note'] ) )
			{
				$message .= $insertion['schedule_note'] ."\r\n";
			}
			portal_notify( 'schedules', $insertion['user_id'], $message );
		}
		else
		{
			$status = 4;
		}
		wp_redirect( 'admin.php?page=portal-scheduling&status='. $status );
		die;
	}

	//Edit a schedule item
	if( isset( $_POST['portal_new_schedule_edit'] ) && !empty( $_POST['portal_new_schedule_edit'] ) )
	{
		$insertion = array(
			'position' => !empty( $_POST['position'] ) ? $_POST['position'] : null,
			'starttime' => strtotime( $_POST['starttime'] ),
			'endtime' => strtotime( $_POST['endtime'] ),
			'user_id' => !empty( $_POST['user_id'] ) ? $_POST['user_id'] : null,
			'scheduled' => current_time( 'mysql', false ),
			'schedule_note' => !empty( $_POST['schedule_note'] ) ? $_POST['schedule_note'] : '',
		);
		$status = $wpdb->update( PORTAL_SCHEDULE, $insertion, array( 'id' => $_POST['portal_new_schedule_edit'] ) );
		if( $status !== false )
		{
			$status = 10;
		}
		else
		{
			$status = 4;
		}
		wp_redirect( 'admin.php?page=portal-scheduling&edit='.$_POST['portal_new_schedule_edit'].'&status='. $status );
		die;
	}

	//Delete a schedule item
	if( isset( $_GET['schedule-delete'] ) && !empty( $_GET['schedule-delete'] ) )
	{
		$removal = array(
			'id' => $_GET['schedule-delete']
		);
		$status = $wpdb->delete( PORTAL_SCHEDULE, $removal );
		if( $status !== false )
		{
			$status = 9;
		}
		else
		{
			$status = 2;
		}
		wp_redirect( 'admin.php?page=portal-scheduling&status='. $status );
		die;
	}

	//Add a new link
	if( isset( $_POST['portal_new_link_submit'] ) && !empty( $_POST['portal_new_link_submit'] ) )
	{
		$insertion = array(
			'title' => !empty( $_POST['title'] ) ? $_POST['title'] : null,
			'uri' => !empty( $_POST['uri'] ) ? esc_url( $_POST['uri'] ) : null,
			'featured' => ( isset( $_POST['featured'] ) && $_POST['featured'] == 1 ) ? 1 : 0,
			'added' => current_time( 'mysql', false ),
		);
		$status = $wpdb->insert( PORTAL_LINKS, $insertion );
		if( $status !== false )
		{
			$status = 11;

			$message = "A new link has been added:\r\n";
			$message .= $insertion['uri'];
			portal_notify( 'links', null, $message );
		}
		else
		{
			$status = 4;
		}
		wp_redirect( 'admin.php?page=portal-links&status='. $status );
		die;
	}

	//Edit a link
	if( isset( $_POST['portal_new_link_edit'] ) && !empty( $_POST['portal_new_link_edit'] ) )
	{
		$insertion = array(
			'title' => !empty( $_POST['title'] ) ? $_POST['title'] : null,
			'uri' => !empty( $_POST['uri'] ) ? esc_url( $_POST['uri'] ) : null,
			'featured' => ( isset( $_POST['featured'] ) && $_POST['featured'] == 1 ) ? 1 : 0,
		);
		$status = $wpdb->update( PORTAL_LINKS, $insertion, array( 'id' => $_POST['portal_new_link_edit'] ) );
		if( $status !== false )
		{
			$status = 12;
		}
		else
		{
			$status = 4;
		}
		wp_redirect( 'admin.php?page=portal-links&edit='.$_POST['portal_new_link_edit'].'&status='. $status );
		die;
	}

	//Delete a link
	if( isset( $_GET['link-delete'] ) && !empty( $_GET['link-delete'] ) )
	{
		$removal = array(
			'id' => $_GET['link-delete']
		);
		$status = $wpdb->delete( PORTAL_LINKS, $removal );
		if( $status !== false )
		{
			$status = 12;
		}
		else
		{
			$status = 2;
		}
		wp_redirect( 'admin.php?page=portal-links&status='. $status );
		die;
	}


}


if( !function_exists( 'portal_admin_pages' ) )
{
	/**
	 * Function to add admin pages
	 * Also adds employee level pages
	 */
	function portal_admin_pages()
	{
		if( current_user_can( 'activate_plugins' ) )
		{
			add_menu_page( 'Employee Portal', 'Employee Portal', 'activate_plugins', 'employee_portal_plugin', 'portal_schedule_manage', 'dashicons-schedule' );
			add_submenu_page( 'employee_portal_plugin', 'Manage Calendar', 'Calendar', 'activate_plugins', 'portal-scheduling', 'portal_schedule_manage' );
			add_submenu_page( 'employee_portal_plugin', 'Manage Notes', 'Notes', 'activate_plugins', 'portal-notes', 'portal_manage_notes' );
			add_submenu_page( 'employee_portal_plugin', 'Manage Downloads', 'Downloads', 'activate_plugins', 'portal-forms', 'portal_manage_forms' );
			add_submenu_page( 'employee_portal_plugin', 'Manage Links', 'Links', 'activate_plugins', 'portal-links', 'portal_manage_links' );
			add_submenu_page( 'employee_portal_plugin', 'View Employees', 'Employees', 'activate_plugins', 'users.php?role=portal_employee', '' );
			add_submenu_page( '', 'Add new Calendar Item', 'Calendar', 'activate_plugins', 'portal-new-schedule', 'portal_add_schedule' );
			add_submenu_page( 'employee_portal_plugin', 'Settings', 'Settings', 'activate_plugins', 'portal-settings', 'portal_manage_settings' );
		}
		else
		{
			//Add the actual employee pages
			add_menu_page( 'Portal', 'Portal', 'access_portal', 'employee_portal_plugin', 'portal_view_schedule', 'dashicons-schedule' );
			add_submenu_page( 'employee_portal_plugin', 'My Calendar', 'Calendar', 'access_portal', 'schedule', 'portal_view_schedule' );
			add_submenu_page( 'employee_portal_plugin', 'My Notes', 'Notes', 'access_portal', 'notes', 'portal_view_notes' );
			add_submenu_page( 'employee_portal_plugin', 'Available Downloads', 'Downloads', 'access_portal', 'forms', 'portal_view_forms' );
			add_submenu_page( 'employee_portal_plugin', 'Links', 'Links', 'access_portal', 'links', 'portal_view_links' );
		}
	}
}


// Function that outputs the contents of the dashboard widget
function dashboard_widget_function( $post, $callback_args ) {
	portal_view_schedule( false, 'agendaDay' );
}

// Function used in the action hook
function add_dashboard_widgets() {
	wp_add_dashboard_widget( 'dashboard_widget', 'Calendar', 'dashboard_widget_function' );
}

// Register the new dashboard widget with the 'wp_dashboard_setup' action
add_action('wp_dashboard_setup', 'add_dashboard_widgets' );


//Manage and view the schedule as an admin
if( !function_exists( 'portal_schedule_manage' ) )
{
	function portal_schedule_manage()
	{
		if( !isset( $_GET['view'] ) )
		{
			include PORTAL_PLUGIN_DIR.'/inc/portal_schedule_manage.php';
		}
		else
		{
			include PORTAL_PLUGIN_DIR . '/inc/portal_schedule_calendar.php';
		}
	}
}


//Manage and view the notes pages
if( !function_exists( 'portal_manage_notes' ) )
{
	function portal_manage_notes()
	{
		if( !isset( $_GET['new'] ) )
		{
			include PORTAL_PLUGIN_DIR.'/inc/portal_manage_notes.php';
		}
		else
		{
			include PORTAL_PLUGIN_DIR . '/inc/portal_new_note.php';
		}
	}
}


//Manage and view the forms
if( !function_exists( 'portal_manage_forms' ) )
{
	function portal_manage_forms()
	{
		include PORTAL_PLUGIN_DIR . '/inc/portal_manage_forms.php';
	}
}


if( !function_exists( 'portal_manage_links' ) )
{
	function portal_manage_links()
	{
		include PORTAL_PLUGIN_DIR . '/inc/portal_manage_links.php';
	}
}

if( !function_exists( 'portal_manage_settings' ) )
{
	function portal_manage_settings()
	{
		if( !isset( $_GET['instructions'] ) )
		{
			include PORTAL_PLUGIN_DIR.'/inc/portal-settings.php';
		}
		else
		{
			include PORTAL_PLUGIN_DIR . '/inc/portal-instructions.php';
		}
	}
}


//View the schedule as an employee
if( !function_exists( 'portal_view_schedule' ) )
{
	function portal_view_schedule( $schedule_title = true, $viewomde = 'agendaWeek' )
	{
		global $schedule_title;
		include PORTAL_PLUGIN_DIR . '/inc/portal_view_schedule.php';
	}
}


//Add a schedule item
if( !function_exists( 'portal_add_schedule' ) )
{
	function portal_add_schedule()
	{
		include PORTAL_PLUGIN_DIR .'/inc/portal_schedule_manage.php';
	}
}


//View/add forms
if( !function_exists( 'portal_view_forms' ) )
{
	function portal_view_forms()
	{
		include PORTAL_PLUGIN_DIR . '/inc/portal_view_forms.php';
	}
}


if( !function_exists( 'portal_view_links' ) )
{
	function portal_view_links()
	{
		include PORTAL_PLUGIN_DIR . '/inc/portal_view_links.php';
	}
}


if( !function_exists( 'portal_view_notes' ) )
{
	/**
	 * View a single note (for employees)
	 */
	function portal_view_notes()
	{
		include PORTAL_PLUGIN_DIR . '/inc/portal_view_notes.php';
	}
}


if( !function_exists( 'portal_login' ) )
{
	function portal_login()
	{
		$args = array(
			'echo' => true,
			'redirect' => site_url( $_SERVER['REQUEST_URI'] ),
			'form_id' => 'portal-loginform',
			'label_username' => __( 'Username' ),
			'label_password' => __( 'Password' ),
			'label_remember' => __( 'Remember Me' ),
			'label_log_in' => __( 'Log In' ),
			'id_username' => 'user_login',
			'id_password' => 'user_pass',
			'id_remember' => 'rememberme',
			'id_submit' => 'wp-submit',
			'remember' => true,
			'value_username' => NULL,
			'value_remember' => false
		);
		wp_login_form( $args );
	}
}