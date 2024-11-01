<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

require_once( CONCOURS_PLUGIN_DIR . 'class/olyos_concours_form.php' );



// Check if edit or add
if (isset($_REQUEST['insert_type']) && ($_REQUEST['insert_type'] == 'edit')) {
    $is_edit = true;
    $concours_id = (int)esc_attr($_REQUEST['concours_id']);
} else {
    // Defaults to a new contest
    $is_edit = false;
    $concours_id = '';
}

$error_str = '';

$concours_form = new OlyosConcoursForm($is_edit, $concours_id);

$result = $concours_form->save($error_str);

if ($result === false) {
    // There was an error, could not save to BDD
    $message_str = __('Error while saving to database', 'wp-concours') . ': '.$error_str;

    if ($is_edit) {
        wp_redirect('admin.php?page=concours&insert_type=edit&concours_id='.$concours_id.'&result_message='.urlencode($message_str));
    } else {
        wp_redirect('admin.php?page=concours&insert_type=add&result_message='.urlencode($message_str));
    }
} else {
    // Could save the contest
    $message_str = __('Contest Modified.', 'wp-concours');
    if ($is_edit) {
        wp_redirect('admin.php?page=concours&insert_type=edit&concours_id='.$concours_id.'&result_message='.urlencode($message_str));
    } else {
        wp_redirect('admin.php?page=concours&insert_type=edit&concours_id='.$result.'&result_message='.urlencode($message_str));
    }
}