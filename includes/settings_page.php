<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

 // check user capabilities
 if (!current_user_can('manage_options')) {
    return;
 }
?>

<div class="wrap">
    <?php olyosconc_display_admin_tabs($_GET['page']); ?>
    <h1><?php _e('Settings', 'wp-concours'); ?></h1>

    <div id="concours-content">
        <div id="concours-content-main">

            <?php
            if ( isset( $_GET['settings-updated'] ) ) {
                // add settings saved message with the class of "updated"
                add_settings_error( 'concours_messages', 'concours_message', __( 'Settings Saved', 'olyos-concours' ), 'updated' );
            }
            // show error/update messages
            settings_errors( 'concours_messages' );
            ?>

            <form action="options.php" method="post" id="concours-settings-form">
                <?php
                settings_fields( 'concours-settings' );
                do_settings_sections( 'concours-settings' );
                submit_button();
                ?>
            </form>

        </div>

        <div id="concours-content-aside">
            <?php require_once( CONCOURS_PLUGIN_DIR . 'includes/admin_column.php' ); ?>
        </div>
    </div>
</div>
