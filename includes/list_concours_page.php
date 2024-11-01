<?php
    if (!defined('ABSPATH')) exit; // Exit if accessed directly

    require_once( CONCOURS_PLUGIN_DIR . 'class/olyos_concours_list.php' );
    $concours_list = new OlyosConcoursList();
    
    $page  = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRIPPED );
    $paged = filter_input( INPUT_GET, 'paged', FILTER_SANITIZE_NUMBER_INT );
?>

<div class="wrap">
    <?php olyosconc_display_admin_tabs($_GET['page']); ?>
    <h1><?php _e('List of your contests', 'wp-concours'); ?></h1>
    <p><?php _e('To insert a new contest in a page or post, use the following shortcode :', 'wp-concours'); ?> [concours id="ID"] (ex: [concours id="1"])</p>

    <form id="wpse-list-table-form" method="post">
        <input type="hidden" name="page" value="<?php echo $page ?>" />
        <input type="hidden" name="paged" value="<?php echo $paged ?>" />
        <?php $concours_list->prepare_items(); ?>
        <?php $concours_list->display(); ?>
    </form>

</div>