<?php
/**
 * Create a new table class that will extend the WP_List_Table
 * Produces table outputs for forms, schedule line-items, and notes
 *
 * @updated 18-Jul-2016
 * @package Wordpress Employee Portal
 */
class Portal_Wp_List_Table extends WP_List_Table
{
    /**
     * Prepare the items for the table to process
     *
     * @return Void
     */
    public function prepare_items( $contest_id = null )
    {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
 
        $data = $this->table_data( $contest_id );
        usort( $data, array( &$this, 'sort_data' ) );
 
        $perPage = 20;
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);
 
        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ) );
 
        $data = array_slice($data,(($currentPage-1)*$perPage),$perPage);
 
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $data;
    }


	/**
	 * @param null $note_id
	 */
	public function prepare_items_employee( $note_id = null )
	{
		$columns = array(
			'title' => 'Title',
			'excerpt' => 'Excerpt',
			'added' => 'Added',
			'actions' => 'Actions',
		);
		$sortable = array('title' => array('title', false), 'added' => array('added', false));
		if( !is_null( $note_id ) )
		{
			$columns['excerpt'] = 'Content';
			unset( $columns['actions'] );
			unset( $columns['added'] );
			$sortable = array();
		}

		$hidden = array();

		$data = $this->employee_notes( $note_id );
		usort( $data, array( &$this, 'sort_data' ) );

		$perPage = 20;
		$currentPage = $this->get_pagenum();
		$totalItems = count($data);

		$this->set_pagination_args( array(
			'total_items' => $totalItems,
			'per_page'    => $perPage
		) );

		$data = array_slice($data,(($currentPage-1)*$perPage),$perPage);

		$this->_column_headers = array($columns, $hidden, $sortable);
		$this->items = $data;
	}


	/**
	 *
	 */
	public function prepare_forms()
	{
		$columns = array(
			'title' => 'Title',
			'link' => 'Link',
			'added' => 'Added',
			'actions' => 'Actions',
		);
		$sortable = array('title' => array('title', false), 'added' => array('added', false));

		$hidden = array();

		$is_admin = false;
		if( current_user_can( 'activate_plugins' ) )
		{
			$is_admin = true;
		}
		$data = $this->available_forms( $is_admin );
		usort( $data, array( &$this, 'sort_data' ) );

		$perPage = 20;
		$currentPage = $this->get_pagenum();
		$totalItems = count($data);

		$this->set_pagination_args( array(
			'total_items' => $totalItems,
			'per_page'    => $perPage
		) );

		$data = array_slice($data,(($currentPage-1)*$perPage),$perPage);

		$this->_column_headers = array($columns, $hidden, $sortable);
		$this->items = $data;
	}


	/**
	 * Prepare links for tableized output
	 */
	public function prepare_links()
	{
		$columns = array(
			'title' => 'Title',
			'uri' => 'URI',
			'featured' => 'Featured',
			'added' => 'Added',
			'actions' => 'Actions',
		);
		$sortable = array(
			'title' => array( 'title', false),
			'uri' => array( 'uri' => false ),
			'featured' => array( 'featured' => false ),
			'added' => array( 'added' => false ),
		);

		$hidden = array();

		$is_admin = false;
		if( current_user_can( 'activate_plugins' ) )
		{
			$is_admin = true;
		}
		$data = $this->available_links( $is_admin );
		usort( $data, array( &$this, 'sort_data' ) );

		$perPage = 20;
		$currentPage = $this->get_pagenum();
		$totalItems = count($data);

		$this->set_pagination_args( array(
			'total_items' => $totalItems,
			'per_page'    => $perPage
		) );

		$data = array_slice($data,(($currentPage-1)*$perPage),$perPage);

		$this->_column_headers = array($columns, $hidden, $sortable);
		$this->items = $data;
	}


	/**
	 * Prepare schedule items
	 *
	 * @param bool|false $json
	 * @return mixed|string|void
	 */
	public function prepare_schedules( $json = false )
	{
		$columns = array(
			'id' => 'ID',
			'title' => 'Employee',
			'position' => 'Position',
			'start' => 'Start',
			'end' => 'End',
			'added' => 'Added',
			'actions' => 'Actions',
		);
		$sortable = array(
			'title' => array( 'title', false ),
			'added' => array( 'added', false ),
			'start' => array( 'start', false ),
			'end' => array( 'end', false ),
			'position' => array( 'position', false ),
		);

		$hidden = array( 'id' );

		$user_id = null;
		if( !current_user_can( 'activate_plugins' ) )
		{
			$user = wp_get_current_user();
			$user_id = $user->ID;
		}

		$data = $this->schedules( $json, $user_id );
		if( $json )
		{
			return json_encode( $data );
		}
		usort( $data, array( &$this, 'sort_data' ) );

		$perPage = ( $json ) ? 1000 : 20;
		$currentPage = $this->get_pagenum();
		$totalItems = count($data);

		$this->set_pagination_args( array(
			'total_items' => $totalItems,
			'per_page'    => $perPage
		) );

		$data = array_slice($data,(($currentPage-1)*$perPage),$perPage);

		$this->_column_headers = array($columns, $hidden, $sortable);
		$this->items = $data;
	}

 
    /**
     * Override the parent columns method. Defines the columns to use in your listing table
     *
     * @return Array
     */
    public function get_columns()
    {
        $columns = array(
            'title' => 'Title',
            'user' => 'For',
            'excerpt' => 'Excerpt',
            'added' => 'Added',
			'actions' => 'Actions',
        );
 
        return $columns;
    }


    /**
     * Define which columns are hidden
     *
     * @return Array
     */
    public function get_hidden_columns()
    {
        return array();
    }

 
    /**
     * Define the sortable columns
     *
     * @return Array
     */
    public function get_sortable_columns()
    {
        return array(
			'title' => array( 'title', false ),
			'added' => array( 'added', false ),
			'user' => array( 'user', false )
		);
    }

 
    /**
     * Get the table data
     *
     * @return Array
     */
    private function table_data()
    {
        global $wpdb;
        $data = array();

        $sql = "SELECT ".PORTAL_NOTES.".id AS note_id, `added`, `user_id`, `title`, `note` AS shorted, `user_nicename` FROM ". PORTAL_NOTES ." LEFT JOIN {$wpdb->prefix}users ON {$wpdb->prefix}users.ID = ".PORTAL_NOTES.".user_id";
        $results = $wpdb->get_results( $sql );
        if( !empty( $results ) )
        {
            foreach( $results as $post )
            {
				$actions = '<a href="admin.php?page=portal-notes&delete='. $post->note_id.'" title="Delete this note" class="js-portal-confirm">Delete</a>';
                $data[] = array(
                    'title' => $post->title,
                    'user'  => !empty( $post->user_nicename ) ? $post->user_nicename : 'All Employee',
                    'excerpt' => wp_trim_words( $post->shorted, 10 ),
                    'added' => date( PORTAL_DATE_FORMAT, strtotime( $post->added ) ),
					'actions' => $actions,
                );
            }
        }
 
        return $data;
    }


	/**
	 * Get employee notes
	 * @param null $note_id
	 * @return array
	 */
	public function employee_notes( $note_id = null )
	{
		global $wpdb;
		$data = array();

		$user = wp_get_current_user();
		$user_id = $user->ID;

		$params = array();
		$sql = "SELECT ".PORTAL_NOTES.".id AS note_id, `added`, `user_id`, `title`, `note` AS shorted FROM ". PORTAL_NOTES ." WHERE (user_id = %d OR user_id IS NULL) ORDER BY added DESC";
		$params[] = $user_id;

		if( !is_null( $note_id ) )
		{
			$params = array();
			$sql = "SELECT ".PORTAL_NOTES.".id AS note_id, `added`, `user_id`, `title`, `note` AS shorted FROM ". PORTAL_NOTES ." WHERE (user_id = %d OR user_id IS NULL) AND id = %d LIMIT 1";
			$params[] = $user_id;
			$params[] = $note_id;
		}

		$results = $wpdb->get_results( $wpdb->prepare( $sql, $params ) );
		if( !empty( $results ) )
		{
			foreach( $results as $post )
			{
				$actions = '<a href="admin.php?page=notes&view='. $post->note_id.'" title="View">View</a>';
				$data[] = array(
					'title' => $post->title,
					'excerpt' => !is_null( $note_id ) ? stripslashes( $post->shorted ) : wp_trim_words( $post->shorted, 10 ),
					'added' => date( PORTAL_FULL_TIMESTAMP, strtotime( $post->added ) ),
					'actions' => $actions,
				);
			}
		}

		return $data;
	}


	/**
	 * Get list of available forms
	 *
	 * @param bool|false $is_admin
	 * @return array
	 */
	public function available_forms( $is_admin = false )
	{
		$data = array();

		$results = get_option( PORTAL_NOTE_STORE );

		if( !empty( $results ) )
		{
			foreach( $results as $k => $post )
			{
				$link = wp_get_attachment_url( $post );
				$title = get_the_title( $post );
				$title = !empty( $title ) ? $title : basename( $link );
				$actions = '<a href="'.$link.'" target="_blank" title="View" class="js-portal-window">View</a>';
				if( $is_admin )
				{
					$actions .= ' | <a href="admin.php?page=portal-forms&form-delete=' . $post .'" class="js-portal-confirm">Delete</a>';
				}
				$params = array(
					'title' => $title,
					'link' => '<a href="'.$link .'" target="_blank" class="js-portal-window">'. $link .'</a>',
					'added' => get_the_date( PORTAL_FULL_TIMESTAMP, $post ),
					'actions' => $actions,
				);
				$data[] = $params;
			}
		}
		return $data;
	}


	/**
	 * Get list of available links
	 *
	 * @param bool|false $is_admin
	 * @return array
	 */
	public function available_links( $is_admin = false )
	{
		global $wpdb;

		$data = array();

		$results = $wpdb->get_results( "SELECT id, title, uri, featured, added FROM ". PORTAL_LINKS." ORDER BY added DESC" );

		if( !empty( $results ) )
		{
			foreach( $results as $post )
			{
				$actions = '<a href="'. $post->uri .'" target="_blank">View</a>';
				if( $is_admin )
				{
					$actions = '<a href="admin.php?page=portal-links&link-delete='. $post->id.'" title="Delete" class="js-portal-confirm">Delete</a>';
					$actions .= ' | <a href="admin.php?page=portal-links&edit='. $post->id.'">Edit</a>';
				}
				$data[] = array(
					'title' => $post->title,
					'uri' => wp_trim_words( $post->uri, 10 ),
					'featured' => ( $post->featured == 1 ) ? 'Yes' : 'No',
					'added' => date( PORTAL_FULL_TIMESTAMP, strtotime( $post->added ) ),
					'actions' => $actions,
				);
			}
		}
		return $data;
	}


	/**
	 * Get available schedules
	 * @param bool|false $json
	 * @param null $employee_id
	 * @return array
	 */
	public function schedules( $json = false, $employee_id = null )
	{
		global $wpdb;
		$data = array();

		$params = array();
		$sql = "SELECT ".PORTAL_SCHEDULE.".id AS schedule_id, `starttime`, `endtime`, `scheduled`, `schedule_note`, `position`,  `user_nicename`, `user_id` FROM ". PORTAL_SCHEDULE ." LEFT JOIN {$wpdb->prefix}users ON {$wpdb->prefix}users.ID = ".PORTAL_SCHEDULE.".user_id ORDER BY starttime DESC";

		if( !is_null( $employee_id ) )
		{
			$sql = "SELECT ".PORTAL_SCHEDULE.".id AS schedule_id, `starttime`, `endtime`, `scheduled`, `schedule_note`, `position`, `user_id` FROM ". PORTAL_SCHEDULE ." WHERE (user_id = %d OR user_id IS NULL) ORDER BY starttime DESC";
			$params[] = $employee_id;
		}

		$results = $wpdb->get_results( $wpdb->prepare( $sql, $params ) );
		if( !empty( $results ) )
		{
			foreach( $results as $post )
			{
				$actions = '<a href="admin.php?page=portal-scheduling&schedule-delete='. $post->schedule_id.'" title="Delete" class="js-portal-confirm">Delete</a>';
				if( is_null( $employee_id ) )
				{
					$actions .= ' | <a href="admin.php?page=portal-scheduling&edit='. $post->schedule_id.'">Edit</a>';
				}
				$title = !is_null( $employee_id ) ? $post->position : $post->user_nicename;
				$schedule = array(
					'id' => $post->schedule_id,
					'title' => ( empty( $title ) ? 'All' : $title ),
					'start' => ( $json ) ? date( 'c', $post->starttime ) : date( PORTAL_FULL_TIMESTAMP, $post->starttime ),
					'end' => ( $json ) ? date( 'c', $post->endtime ) : date( PORTAL_FULL_TIMESTAMP, $post->endtime ),
					'added' => date( PORTAL_FULL_TIMESTAMP, strtotime( $post->scheduled ) ),
					'actions' => $actions,
					'position' => $post->position,
					'backgroundColor' => get_the_author_meta( 'portal_color', $post->user_id ),
					'description' => !empty( $post->schedule_note ) ? stripslashes( $post->schedule_note ) : $title,
				);
				if( $json )
				{
					unset( $schedule['actions'] );
					unset( $schedule['added'] );
					unset( $schedule['position'] );
				}
				$data[] = $schedule;
			}
		}

		return $data;
	}

 
    /**
     * Define what data to show on each column of the table
     *
     * @param  Array $item        Data
     * @param  String $column_name - Current column name
     *
     * @return Mixed
     */
    public function column_default( $item, $column_name )
    {
        switch( $column_name ) {
            case 'title':
            case 'user':
            case 'added':
			case 'excerpt':
			case 'actions':
			case 'link':
			case 'start':
			case 'end':
			case 'position':
			case 'uri':
			case 'featured':
                return $item[ $column_name ];
 
            default:
                return print_r( $item, true ) ;
        }
    }


    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @return Mixed
     */
    private function sort_data( $a, $b )
    {
        // Set defaults
        $orderby = 'title';
        $order = 'asc';
 
        // If orderby is set, use this as the sort column
        if(!empty($_GET['orderby']))
        {
            $orderby = $_GET['orderby'];
        }
 
        // If order is set use this as the order
        if(!empty($_GET['order']))
        {
            $order = $_GET['order'];
        }
 
 
        $result = strnatcmp( $a[$orderby], $b[$orderby] );
 
        if($order === 'asc')
        {
            return $result;
        }
 
        return -$result;
    }
}