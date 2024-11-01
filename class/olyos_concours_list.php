<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('WP_List_Table')) {
	require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class OlyosConcoursList extends WP_List_Table {

    /** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __('Contest', 'wp-concours'), //singular name of the listed records
			'plural'   => __('Contests', 'wp-concours'), //plural name of the listed records
			'ajax'     => false //should this table support ajax?
		] );

	}

    public static function get_concours($per_page = 5, $page_number = 1) {
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}olyos_concours";

        if (!empty($_REQUEST['orderby'])) {
            $sql .= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
            $sql .= !empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
        }

        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET ' . ($page_number - 1) * $per_page;

        $result = $wpdb->get_results($sql, 'ARRAY_A');

        return $result;
    }

    public static function delete_concours($id) {
        global $wpdb;

        $wpdb->delete(
            "{$wpdb->prefix}olyos_concours",
            [ 'id' => $id ],
            [ '%d' ]
        );
    }

    public static function record_count() {
        global $wpdb;
        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}olyos_concours";
        return $wpdb->get_var( $sql );
    }

    function column_name($item){
        $delete_nonce = wp_create_nonce( 'delete_concours' );

        // Build row actions
        $actions = array(
            'edit' 		=> sprintf('<a href="?page=%s&insert_type=%s&concours_id=%s" id="%3$s" class="edit-entry">'.__('Edit', 'wp-concours').'</a>', 'concours', 'edit', absint($item['id'])),
            'delete' => sprintf('<a href="?page=%s&action=%s&concours=%s&_wpnonce=%s">Delete</a>', esc_attr($_REQUEST['page']), 'delete', absint($item['id']), $delete_nonce)
        );
    
        return sprintf('%1$s %2$s', stripslashes($item['name']), $this->row_actions($actions));
    }

    function column_shortcode($item) {
        return sprintf('[concours id=%s]', $item['id']);
    }

    function column_participant_count($item) {
        // Build row actions
        $actions = array(
            'View' 		=> sprintf('<a href="?page=%s&concours_id=%s" id="%2$s" class="view-entry">'.__('View participants', 'wp-concours').'</a>', 'concours-participant-list', absint($item['id'])),
        );
    
        return sprintf('%1$s %2$s', $this->get_participant_count($item['id']), $this->row_actions($actions));
    }

    function column_state($item) {
        $start = DateTime::createFromFormat('Y-m-d G:i:s', $item['date_start']);
        $end = DateTime::createFromFormat('Y-m-d G:i:s', $item['date_end']);
        $now = new DateTime('now');

        if ($now > $end) {
            $state = __('Ended', 'wp-concours');
        } elseif ($now < $start) {
            $state = __('Not started', 'wp-concours');
        } else {
            $state = __('Currently running', 'wp-concours');
        }
        return $state;
    }

    public function column_default($item, $column_name) {
        switch ($column_name) {
            case 'id':
                return $item[$column_name];
            default:
                return print_r( $item, true ); //Show the whole array for troubleshooting purposes
        }
    }

    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
        );
    }

    function get_columns() {
        $columns = [
            'cb'      => '<input type="checkbox" />',
            'id' => __('ID', 'wp-concours'),
            'name' => __('Name', 'wp-concours'),
            'shortcode' => __('Shortcode', 'wp-concours'),
            'participant_count' => __('Participant count', 'wp-concours'),
            'state' => __('State', 'wp-concours'),
        ];
        return $columns;
    }

    function get_sortable_columns() {
        $columns = [
            'id' => array('id', true),
            'name' => array('name', true),
            // 'participant_count' => array('participant_count', true)
        ];
        return $columns;
    }

    public function get_bulk_actions() {
        $actions = [
            'bulk-delete' => __('Delete', 'wp-concours')
        ];

        return $actions;
    }

    public function prepare_items() {
        /* -- Register the Columns -- */
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        $per_page = 20;
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();
        $total_pages = ceil($total_items/$per_page);

        $this->process_bulk_action();

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $per_page,
            "total_pages" => $total_pages
        ]);

        $this->items = self::get_concours($per_page, $current_page);
    }

    public function process_bulk_action() {
        // security check!
        if ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) {

            $nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
            $action = 'bulk-' . $this->_args['plural'];

            if (!wp_verify_nonce($nonce, $action)) {
                wp_die( 'Nope! Security check failed!' );
            }
        }

        $action = $this->current_action();

        switch ($action) {
            case 'delete':
                self::delete_concours(absint($_GET['concours']));
                break;;
            case 'bulk-delete':
                $delete_ids = esc_sql($_POST['bulk-delete']);
                // loop over the array of record IDs and delete them
                foreach ($delete_ids as $id) {
                    self::delete_concours($id);
                }
                break;
            default:
                break;
        }
    }

    public function get_participant_count($concours_id) {
        global $wpdb;

        $sql = $wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}olyos_concours_participation
            WHERE id_concours = %d",
            $concours_id
        );

        return $wpdb->get_var($sql);
    }
}