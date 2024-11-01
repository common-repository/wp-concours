<?php
/*
Plugin Name: WP Concours
Plugin URI: https://www.olyos.fr
Version: 1.1
Description: Organize contests : personalize rules and content, generate a list of participants and randomly pick winners.
Author: Olyos - Agence Web
Author URI: https://www.olyos.fr
Text Domain: wp-concours
Domain Path: /languages/
Licence: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if (!defined('ABSPATH')) exit; // Exit if accessed directly

define('CONCOURS_PLUGIN_VERSION','1.1');
define('CONCOURS_PLUGIN_DIR', plugin_dir_path(__FILE__));

$GLOBALS['olyos_concours_settings'] = get_option('olyos_concours_settings');

// init translation
add_action( 'init', 'olyosconc_init_languages');

// Hook when install
register_activation_hook(__FILE__,"olyosconc_install");
// Hook when desactivate
register_deactivation_hook(__FILE__,"olyosconc_desactivate");
// Hook when uninstall
register_uninstall_hook(__FILE__,"olyosconc_uninstall");

// add admin main menu
add_action( 'admin_menu', 'olyosconc_register_admin_menu' );

// add to footer
add_action('admin_footer', 'olyosconc_admin_footer');

// Register style sheet.
add_action('wp_enqueue_scripts', 'olyosconc_register_fo_css');

add_action('admin_action_process_concours_form', 'olyosconc_process_concours_form');

// Ajax
add_action( 'wp_ajax_olyosconc_send_test_mail', 'olyosconc_send_test_mail' );


/**
 * Init language
 */
function olyosconc_init_languages(){
    load_plugin_textdomain('wp-concours', false, plugin_basename(dirname(__FILE__)) . '/languages/');
}

/**
 * On install
 */
function olyosconc_install() {
    global $wpdb;
    $table_concours = $wpdb->prefix . "olyos_concours";
    $table_user = $wpdb->prefix . "olyos_concours_user";
    $table_participation = $wpdb->prefix . "olyos_concours_participation";

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "
        CREATE TABLE $table_concours (
            id INT UNSIGNED AUTO_INCREMENT,
            name VARCHAR(255),
            description TEXT,
            form_title VARCHAR(255),
            social_title VARCHAR(255),
            social_facebook TEXT,
            social_twitter TEXT,
            date_start DATETIME DEFAULT NULL,
            date_end DATETIME DEFAULT NULL,
            newsletter_option BOOLEAN,
            terms TEXT,
            PRIMARY KEY  (id)
        ) $charset_collate;
        CREATE TABLE $table_user (
            id BIGINT UNSIGNED AUTO_INCREMENT,
            firstname VARCHAR(255),
            lastname VARCHAR(255),
            email VARCHAR(100),
            PRIMARY KEY  (id)
        ) $charset_collate;
        CREATE TABLE $table_participation (
            id_user BIGINT UNSIGNED,
            id_concours INT UNSIGNED,
            subscribe_newsletter BOOLEAN DEFAULT 0,
            ip_address VARCHAR(45),
            PRIMARY KEY  (id_user,id_concours)
        ) $charset_collate;
    ";

    // Update tables without deleting existing content
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    olyosconc_load_default_settings();
}

/**
 * On uninstall
 */
function olyosconc_uninstall() {
    global $wpdb;
    $table_concours = $wpdb->prefix . "olyos_concours";
    $table_participation = $wpdb->prefix . "olyos_concours_participation";
    $table_user = $wpdb->prefix . "olyos_concours_user";

    // delete table
    $wpdb->query("DROP TABLE IF EXISTS $table_participation");
    $wpdb->query("DROP TABLE IF EXISTS $table_user");
    $wpdb->query("DROP TABLE IF EXISTS $table_concours");

    // Delete options
    delete_option('olyos_concours_settings');
}

function olyosconc_desactivate() {
    // Flush Cache/temp
    // Flush Permalinks
}

/**
 * Plugin sub navigation menu
 */
function olyosconc_register_admin_menu(){
    $menu_hook_suffixes = array();

    add_menu_page('WP Concours', 'WP Concours', 'manage_options', 'concours-infos', 'olyosconc_display_concours_infos', plugins_url('img/icon.png', __FILE__), 100 );
    $menu_hook_suffixes[] = add_submenu_page('concours-infos', __('Informations', 'wp-concours'), __('Informations', 'wp-concours'), 'manage_options', "concours-infos", "olyosconc_display_concours_infos");
    $menu_hook_suffixes[] = add_submenu_page('concours-infos', __('Contests list', 'wp-concours'), __('Contests list', 'wp-concours'), 'manage_options', "concours-list", "olyosconc_display_concours_list");
    $menu_hook_suffixes[] = add_submenu_page('concours-infos', __('Add new contest', 'wp-concours'), __('Add new contest', 'wp-concours'), 'manage_options', "concours", "olyosconc_display_concours_add");
    $menu_hook_suffixes[] = add_submenu_page('concours-infos', __('Settings', 'wp-concours'), __('Settings', 'wp-concours'), 'manage_options', "concours-settings", "olyosconc_display_concours_settings");
    $menu_hook_suffixes[] = add_submenu_page('concours-infos', __('Pick winners', 'wp-concours'), null, 'manage_options', "concours-participant-list", "olyosconc_display_concours_participants"); // null to not display item in admin menu

    // Only add JS/CSS when on a plugin page
    foreach ($menu_hook_suffixes as $hook_suffix) {
        add_action( 'load-' . $hook_suffix , 'olyosconc_admin_init' );
    }
}

function olyosconc_display_concours_list() {
    if (!current_user_can('manage_options')) {
      wp_die(__('You do not have sufficient permissions to access this page.', 'wp-concours'));
    }
    require_once( CONCOURS_PLUGIN_DIR . 'includes/list_concours_page.php' );
}

function olyosconc_display_concours_add() {
    if (!current_user_can('manage_options')) {
      wp_die(__('You do not have sufficient permissions to access this page.', 'wp-concours'));
    }
    require_once( CONCOURS_PLUGIN_DIR . 'includes/concours_page.php' );
}

function olyosconc_display_concours_infos() {
    if (!current_user_can('manage_options')) {
      wp_die(__('You do not have sufficient permissions to access this page.', 'wp-concours'));
    }
    require_once( CONCOURS_PLUGIN_DIR . 'includes/infos_concours_page.php' );
}

function olyosconc_display_concours_participants() {
    if (!current_user_can('manage_options')) {
      wp_die(__('You do not have sufficient permissions to access this page.', 'wp-concours'));
    }
    require_once( CONCOURS_PLUGIN_DIR . 'includes/participants_list_page.php' );
}

function olyosconc_display_concours_settings() {
    if (!current_user_can('manage_options')) {
      wp_die(__('You do not have sufficient permissions to access this page.', 'wp-concours'));
    }
    require_once( CONCOURS_PLUGIN_DIR . 'includes/settings_page.php' );
}

function olyosconc_process_concours_form() {
    require_once( CONCOURS_PLUGIN_DIR . 'includes/process_concours_form.php' );
}

function olyosconc_admin_init() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-ui-core');

    // Widgets
    wp_register_script('datetimepicker', plugins_url('widgets/datetimepicker/jquery.datetimepicker.full.min.js', __FILE__));
    wp_enqueue_script( 'datetimepicker');
    wp_register_style('datetimepicker_css', plugins_url('widgets/datetimepicker/jquery.datetimepicker.min.css', __FILE__));
    wp_enqueue_style( 'datetimepicker_css');

    wp_register_style('myBackofficeStyleSheet', plugins_url('css/backoffice.css', __FILE__));
    wp_enqueue_style( 'myBackofficeStyleSheet');

    // Ajax pour l'admin
    wp_enqueue_script( 'ajax-script', plugins_url( '/includes/send_mail.js', __FILE__ ), array('jquery') );
	wp_localize_script( 'ajax-script', 'ajax_object',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'we_value' => 1234 ) );
}

function olyosconc_display_admin_tabs($current = 'list') {
    require_once( CONCOURS_PLUGIN_DIR . 'includes/admin_header.php' );

    $tabs = array(
        'concours-infos' => array('', __('Informations', 'wp-concours')),
        'concours-list' => array('', __('Contests list', 'wp-concours')),
        'concours-participant-list' => array('', __('View participants', 'wp-concours')),
        'concours-settings' => array('', __('Settings', 'wp-concours')),
    );

    echo '<div class="nav-tab-wrapper">';
    foreach( $tabs as $slug => $value ){
        $class = ($slug == $current) ? ' nav-tab-active' : '';
        echo '<a class="nav-tab'.$class.'" href="?page='.$slug.$value[0].'">'.$value[1].'</a>';
    }

    echo '<a href="?page=concours&insert_type=add" id="concours-add-tab" class="nav-tab">'.__('Add new contest', 'wp-concours').'</a>';
    echo '</div>';
}

function olyosconc_admin_footer() {
    $locale = explode('_', get_locale())[0];
?>
    <script type="text/javascript">
        jQuery(document).ready(function(){
            if (jQuery.datetimepicker) {
                jQuery.datetimepicker.setLocale("<?php echo($locale); ?>");

                jQuery('.mytimedatepicker').datetimepicker({
                    format: 'd-m-Y H:i'
                });
            }

            jQuery("#concours-shortcode input[type='text']").click(function () {
                jQuery(this).select();
            });
        });

    </script>
<?php
}

function olyosconc_register_fo_css() {
    wp_register_style('concours-frontend', plugins_url('css/style.css', __FILE__));
}

/////////////////////////
// Shortcode
/////////////////////////
add_shortcode('concours', 'olyosconc_concours_shortcode');

function olyosconc_concours_shortcode($atts) {
    $a = shortcode_atts( array(
        'id' => -1
    ), $atts );

    if ($a['id'] == -1) {
        return '<p class="concours-message error">'.__('No id attribute found.', 'wp-concours').'</p>';
    }

    require_once( CONCOURS_PLUGIN_DIR . 'class/olyos_concours_shortcode.php' );
    try {
        $shortcode = new OlyosConcoursShortcode($a['id']);
    } catch (Exception $e) {
        return '<p class="concours-message error">'.__('Shortcode ID unknown: ', 'wp-concours').$e->getMessage().'</p>';

    }

    if (isset($GLOBALS['olyos_concours_settings']['concours_css_checkbox'])) {
        $load_css = ($GLOBALS['olyos_concours_settings']['concours_css_checkbox'] == 'on') ? true : false ;
    } else {
        $load_css = false;
    }
    if ($load_css) {
        wp_enqueue_style('concours-frontend');
    }

    // Show the shortcode
    return $shortcode->get_shortcode_html();
}

/////////////////////////
// Options
/////////////////////////
add_action('admin_init', function() {
    require_once( CONCOURS_PLUGIN_DIR . 'includes/settings_config.php' );
});

function olyosconc_load_default_settings() {
    $default = array(
        'concours_css_checkbox' => 'on',
        'concours_thankyoumail_checkbox' => 'on',
        'concours_thankyoumail_body' => '',
    );
    update_option('olyos_concours_settings', $default);
}

function olyosconc_send_test_mail() {
    $email_target = get_bloginfo('admin_email');
    $email_title = __('Thanks for your participation', 'wp-concours');
    $email_content = file_get_contents( CONCOURS_PLUGIN_DIR . "/includes/thankyou-mail.html");

    $email_content = str_replace('{{plugin_url}}', plugin_dir_url(__FILE__), $email_content);
    $email_content = str_replace('{{message_title}}', __('Your participation to this contest have been registered !', 'wp-concours'), $email_content);

    $message_body = (isset($GLOBALS['olyos_concours_settings']['concours_thankyoumail_body'])) ? $GLOBALS['olyos_concours_settings']['concours_thankyoumail_body'] : '';
    $email_content = str_replace('{{message_body}}', $message_body, $email_content);

    add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));

    if (wp_mail($email_target, $email_title, $email_content)) {
        _e('E-mail sent to : ', 'wp-concours');
    } else {
        _e('Could not send e-mail to : ', 'wp-concours');
    }
    echo $email_target;

	wp_die();
}
