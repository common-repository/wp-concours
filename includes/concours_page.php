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
        $concours_id = "";
    }

    $message_str = '';
    if (isset($_REQUEST['result_message'])) {
        $message_str = '<div id="message" class="updated notice is-dismissible"><p>'.urldecode($_REQUEST['result_message']).'</p></div>';
    }

    $concours_form = new OlyosConcoursForm($is_edit, $concours_id);
?>

<div class="wrap">
    <?php olyosconc_display_admin_tabs($_GET['page']); ?>
    <h1><?php ($is_edit) ? _e('Modify contest', 'wp-concours') : _e('Add new contest', 'wp-concours') ?></h1>

    <?php echo $message_str; ?>
    <div id="concours-content">
        <div id="concours-content-main">
            <?php $concours_form->display(); ?>
        </div>
        <div id="concours-content-aside">
            <?php require_once( CONCOURS_PLUGIN_DIR . 'includes/admin_column.php' ); ?>
        </div>
    </div>
</div>
