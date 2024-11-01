<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly


// register a new setting for "concours-settings" page
register_setting(
    'concours-settings',
    'olyos_concours_settings',
    'olyos_concours_settings_validation'
    );

// register a new section in the "concours-settings" page
// add_settings_section(
//     'concours_settings_section',
//     __('General', 'wp-concours'),
//     '',
//     'concours-settings'
// );

add_settings_section(
    'concours_settings_section_mail',
    __('Mail to participants', 'wp-concours'),
    'concours_settings_section_mail_description',
    'concours-settings'
);

// Add fields in those sections
// add_settings_field(
//     'concours_css_checkbox',
//     __('Use plugin Stylesheet', 'wp-concours'),
//     'concours_css_checkbox_render',
//     'concours-settings',
//     'concours_settings_section',
//     array( 'label_for' => 'concours_css_checkbox' )
// );

add_settings_field(
    'concours_thankyoumail_checkbox',
    __('Send mail', 'wp-concours'),
    'concours_thankyoumail_checkbox_render',
    'concours-settings',
    'concours_settings_section_mail',
    array( 'label_for' => 'concours_thankyoumail_checkbox' )
);

add_settings_field(
    'concours_thankyoumail_body',
    __('Mail body', 'wp-concours'),
    'concours_thankyoumail_body_render',
    'concours-settings',
    'concours_settings_section_mail'
);

add_settings_field(
    'concours_thankyoumail_infos',
    __('Informations', 'wp-concours'),
    'concours_thankyoumail_infos_render',
    'concours-settings',
    'concours_settings_section_mail'
);

function concours_css_checkbox_render($args) {
    echo '<label for="concours_css_checkbox">';
    if (!isset($GLOBALS['olyos_concours_settings']['concours_css_checkbox'])) {
        echo '<input type="checkbox" name="olyos_concours_settings[concours_css_checkbox]" value="on" id="concours_css_checkbox">';
    } else {
        echo '<input type="checkbox" name="olyos_concours_settings[concours_css_checkbox]" '.checked($GLOBALS['olyos_concours_settings']['concours_css_checkbox'], 'on', false).' value="on" id="concours_css_checkbox">';
    }
    echo __('We provide you with a very simple default stylesheet', 'wp-concours');
    echo '</label>';
}

function concours_thankyoumail_checkbox_render() {
    echo '<label for="concours_thankyoumail_checkbox">';
    if (!isset($GLOBALS['olyos_concours_settings']['concours_thankyoumail_checkbox'])) {
        echo '<input type="checkbox" name="olyos_concours_settings[concours_thankyoumail_checkbox]" value="on" id="concours_thankyoumail_checkbox">';
    } else {
        echo '<input type="checkbox" name="olyos_concours_settings[concours_thankyoumail_checkbox]" '.checked($GLOBALS['olyos_concours_settings']['concours_thankyoumail_checkbox'], 'on', false).' value="on" id="concours_thankyoumail_checkbox">';
    }
    echo __('Automatically send an e-mail to participants when they fill the form and click on the submit button', 'wp-concours');
    echo '</label>';
}

function concours_thankyoumail_body_render() {
    $body = (isset($GLOBALS['olyos_concours_settings']['concours_thankyoumail_body'])) ? $GLOBALS['olyos_concours_settings']['concours_thankyoumail_body'] : '';

    wp_editor($body, 'concours_thankyoumail_body', array(
        'textarea_name' => 'olyos_concours_settings[concours_thankyoumail_body]',
        'editor_height' => 160,
    ));

    echo '<br>';
    echo '<button id="send-test-mail" class="button button-secondary" data-nonce="'.wp_create_nonce("my_email_ajax_nonce").'">'.__('Send me the saved email', 'wp-concours').'</button>';
}

function concours_thankyoumail_infos_render() {
    echo '<p>';
    _e('The test e-mail is sent to the address in the General settings. Be sure to save your modifications before sending the email.', 'wp-concours');
    echo '</p>';
    echo '<p>';
    _e('E-mails are sent using the WordPress function "wp_mail".', 'wp-concours');
    _e(' The time it gets to receive that e-mail depends on your server\'s configuration (usually 1 to 15min)', 'wp-concours');
    echo '</p>';
    echo '<p>';
    printf(wp_kses(__('Some questions ? You can leave a message on the support forum of our <a href="%s" target="_blank">WordPress Plugin page</a>.', 'wp-concours'), array('a'=>array('href'=>array(), 'target'=>array()))), esc_url('https://wordpress.org/support/plugin/wp-concours'));
    echo '</p>';
}

function concours_settings_section_mail_description() {
    echo '<p>';
    _e('Configure the mail that is being sent when someone participate in one of your contest.', 'wp-concours');
    echo '</p>';
}

function olyos_concours_settings_validation($input) {
    $output = array();

    foreach ($input as $key => $value) {
        switch ($key) {
            case 'concours_thankyoumail_body':
                $output[$key] = wp_kses_post($value);
                break;
            default:
                $output[$key] = $value;
        }
    }

    return $output;
}