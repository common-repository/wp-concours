<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

class OlyosConcoursForm {
    public $table_name;

    private $concours_item;
    private $is_edit;

    function __construct($is_edit, $id) {
        global $wpdb;

        $this->is_edit = $is_edit;

        if ($is_edit) {
            $sql = $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}olyos_concours WHERE id = %d",
                $id
            );
            $this->concours_item = $wpdb->get_row($sql, 'ARRAY_A');
        }
        
        $this->table_name = $wpdb->prefix . "olyos_concours";
    }

    function save(&$error_str) {
        global $wpdb;

        if (!$this->check_values($error_str)) {
            return false;
        }
        
        $data = array(
            'name' => esc_html($_REQUEST['input-name']),
            'description' => wp_kses_post($_REQUEST['description']),
            'form_title' => esc_html($_REQUEST['form-title']),
            'social_title' => esc_html($_REQUEST['social-title']),
            'social_facebook' => $_REQUEST['input-social1'],
            'social_twitter' => $_REQUEST['input-social2'],
            'date_start' => DateTime::createFromFormat('d-m-Y G:i', $_REQUEST['date-start'])->format('Y-m-d G:i'),
            'date_end' => DateTime::createFromFormat('d-m-Y G:i', $_REQUEST['date-end'])->format('Y-m-d G:i'),
            'newsletter_option' => ($_REQUEST['newsletter-chb'] && ($_REQUEST['newsletter-chb'] == 'checked')) ? '1' : '0',
            'terms' => $_REQUEST['contest-terms'],
        );
        
        if ($this->is_edit) {
            // Modify contest
            $where = array(
                'id' => (int)$_REQUEST['concours_id']
            );
            return $wpdb->update($this->table_name, $data, $where);
        } else {
            // New contest
            $wpdb->insert($this->table_name, $data);
            return $wpdb->insert_id;
        }
    }

    function check_values(&$message) {
        // Check date start-end make sense
        $start_input = DateTime::createFromFormat('d-m-Y G:i', $_REQUEST['date-start']);
        $end_input = DateTime::createFromFormat('d-m-Y G:i', $_REQUEST['date-end']);

        if (($_REQUEST['date-start'] == '') || ($_REQUEST['date-end'] == '')) {
            $message = __('Dates must be filled.', 'wp-concours');
            return false;
        }

        if (!$start_input) {
            $message = __('Could not process start date string format.', 'wp-concours');
            return false;
        }
        if (!$end_input) {
            $message = __('Could not process end date string format.', 'wp-concours');
            return false;
        }
        $date_start = $start_input->format('Y-m-d');
        $date_end = $end_input->format('Y-m-d');
        if ($date_start >= $date_end) {
            $message = __('You cannot have your contest end before it starts.', 'wp-concours');
            return false;
        }

        $message = __('Values are OK', 'wp-concours');
        return true;
    }

    function display() {
        if ($this->is_edit) {
            $id = stripslashes($this->concours_item['id']);
            $name = stripslashes($this->concours_item['name']);
            $date_start = DateTime::createFromFormat('Y-m-d G:i:s', stripslashes($this->concours_item['date_start']))->format('d-m-Y G:i');
            $date_end = DateTime::createFromFormat('Y-m-d G:i:s', stripslashes($this->concours_item['date_end']))->format('d-m-Y G:i');
            $description = stripslashes($this->concours_item['description']);
            $social_title_content = stripslashes($this->concours_item['social_title']);
            $form_title_content = stripslashes($this->concours_item['form_title']);
            $social1 = esc_html(stripslashes($this->concours_item['social_facebook']));
            $social2 = esc_html(stripslashes($this->concours_item['social_twitter']));
            $newsletter_checkbox = $this->concours_item['newsletter_option'];
            $terms = esc_html(stripslashes($this->concours_item['terms']));
            $insert_type = 'edit';
        } else {
            $id = '';
            $name = '';
            $date_start = (new DateTime('now'))->format('d-m-Y G:i');
            $date_end = (new DateTime('now'))->modify('+1 day')->format('d-m-Y G:i');
            $description = '';
            $social_title_content = '';
            $form_title_content = '';
            $social1 = '';
            $social2 = '';
            $newsletter_checkbox = '1';
            $terms = '';
            $insert_type = 'add';

        }

        require_once(CONCOURS_PLUGIN_DIR . 'includes/concours_edit_display_form.php');
    }
}