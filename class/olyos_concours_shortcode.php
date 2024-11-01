<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

class OlyosConcoursShortcode {
    public $concours_table_name;
    public $user_table_name;
    public $participation_table_name;
    public $concours_id;
    private $item;

    function __construct($id) {
        global $wpdb;

        if (!absint($id)) {
            throw new Exception(__('Id is not an int: ', 'wp-concours'). $id);
        }

        $this->concours_table_name = $wpdb->prefix . "olyos_concours";
        $this->user_table_name = $wpdb->prefix . "olyos_concours_user";
        $this->participation_table_name = $wpdb->prefix . "olyos_concours_participation";

        $sql = $wpdb->prepare(
            "SELECT * FROM $this->concours_table_name WHERE id = %d",
            $id
        );
        $this->item = $wpdb->get_row($sql, 'ARRAY_A');

        if (!$this->item) {
            throw new Exception(__('Id not in database: ', 'wp-concours'). $id);
        }

        $this->concours_id = $id;
    }

    public function get_shortcode_html() {
        $str = '';
        $str .= '<section class="olyosconc stitched">';
        // Header
        $str .= '<h2>'.stripslashes($this->item['name']).'</h2>';
        $str .= wpautop(stripslashes($this->item['description']));

        $time_zone = get_option('timezone_string');
        if ($time_zone == '') {
            $time_zone = 'utc';
        }
        $now = new Datetime('now', new DateTimeZone($time_zone));
        $start_date = new Datetime($this->item['date_start'], new DateTimeZone($time_zone));
        $end_date = new Datetime($this->item['date_end'], new DateTimeZone($time_zone));
		$str .= '<hr id="description_separator">';
        $str .= '<span class="concours-date">'. sprintf(__('Contest from %s to %s', 'wp-concours'), $start_date->format('d-m-Y G:i'), $end_date->format('d-m-Y G:i')) .'</span>';
		$str .= '<hr>';

        // Social links
        $str .= '<h3>'.stripslashes($this->item['social_title']).'</h3>';
        $str .= '<div class="social-box facebook">'.stripslashes($this->item['social_facebook']).'</div>';

        // Twitter
        if ($this->item['social_twitter']) {
            $twitterurl = 'https://api.twitter.com/1/statuses/oembed.json?url='.$this->item['social_twitter'].'&hide_media=true&hide_thread=true';

            if ($this->get_http_response_code($twitterurl) != '200') {
                $str .= '<p id="olyosconc-message" class="olyosconc-message warning">'.__('Could not fetch twitter url.', 'wp-concours').'</p>';
            } else {
                $json = file_get_contents($twitterurl);
                $data = json_decode($json);
                $str .= '<div class="social-box twitter">'.$data->html.'</div>';
            }
        }

		$str .= ($this->item['social_facebook'] || $this->item['social_twitter']) ? '<hr>' : '';

        // Check time to see if it's too late or too early
        if ($now < $start_date) {
            $str .= $this->get_too_early_html();
        } elseif ($now > $end_date) {
            $str .= $this->get_too_late_html();
        } else {
            // Check if already played
            if ($this->has_already_tried()) {
                $str .= $this->get_already_tried_html();
            } else {
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    // Save new participation
                    if ($this->save()) {
                        $str .= $this->get_confirmation_html();
                        $this->send_confirmation_email();
                    } else {
                        $str .= $this->get_form_html();
                        $str .= '<p id="olyosconc-message" class="olyosconc-message error">'.__('Could not register your participation, try again.', 'wp-concours').'</p>';
                    }
                } else {
                    $str .= $this->get_form_html();
                }
            }
        }

        if ($this->item['terms'] !== '') {
            $str .= '<p class="contest-terms">'. $this->item['terms'] .'</p>';
        }
        
        $str .= '</section>';
        $str .= '<p class="olyos-copyright"><img src="' . esc_url(plugins_url('img/icon_small_olyos.png', dirname(__FILE__))) . '"/>'.__('By ', 'wp-concours').'
            <a target="_blank" title="'.__('Website creation agency - Nantes', 'wp-concours').'"href="https://www.olyos.fr/?utm_source=ContestWP&utm_campaign=contestplugin&utm_medium=frontlink">Olyos</a>
            / <a target="_blank" title="'.__('Blog news design', 'wp-concours').'"href="http://olybop.fr/?utm_source=ContestWP&utm_campaign=contestplugin&utm_medium=frontlink">Olybop</a>
        </p>';

        return $str;
    }

    private function get_form_html() {
        $str = '';

        // Form
        $str .= '<h3>'.stripslashes($this->item['form_title']).'</h3>';
        $str .= '<form id="concours-form" name="concours_form" method="post" action="#olyosconc-message">';
        $str .= '<div class="firstname-field"><label for="firstname-input">'.__('Firstname', 'wp-concours').'</label><input required type="text" name="firstname" id="firstname-input" value=""/></div>';
        $str .= '<div class="lastname-field"><label for="lastname-input">'.__('Lastname', 'wp-concours').'</label><input required type="text" name="lastname" id="lastname-input" value=""/></div>';
        
        $str .= '<div class="email-field"><label for="email-input">'.__('Email', 'wp-concours').'</label><input required type="text" name="email" id="email-input" value=""/></div>';

        if ($this->item['newsletter_option']) {
            $str .= '<div class="newsletter-field"><input type="checkbox" name="newsletter" id="newsletter-input"/><label for="newsletter-input">'.__('Subscribe to our newsletter', 'wp-concours').'</label></div>';
        }

        $str.= '<div class="submit-field"><input type="submit" name="Save" value="'.__('Try to win!', 'wp-concours').'" class="button-primary" /></div>';

        $str .= '</form>';

        return $str;
    }

    private function get_already_tried_html() {
        $str = '';
        $str .= '<p id="olyosconc-message" class="olyosconc-message warning">'.__('You already tried.', 'wp-concours').'</p>';

        return $str;
    }

    private function get_confirmation_html() {
        $str = '';
        $str .= '<p id="olyosconc-message" class="olyosconc-message success">'.__('Your subscription has been received.', 'wp-concours').'</p>';

        return $str;
    }

    private function get_too_early_html() {
        $str = '';
        $str .= '<p id="olyosconc-message" class="olyosconc-message warning">'.__('Contest not yet started.', 'wp-concours').'</p>';

        return $str;
    }
    
    private function get_too_late_html() {
        $str = '';
        $str .= '<p id="olyosconc-message" class="olyosconc-message warning">'.__('Contest already finished.', 'wp-concours').'</p>';

        return $str;
    }

    private function has_already_tried() {
        global $wpdb;
        // NOT the same ip
        $visitor_ip = filter_var($_SERVER["REMOTE_ADDR"], FILTER_VALIDATE_IP);
        $sql = $wpdb->prepare(
            "SELECT 1 FROM $this->participation_table_name
            WHERE id_concours = %d
            AND ip_address = '%s'",
            $this->concours_id,
            $visitor_ip
        );
        $result = $wpdb->get_results($sql);
        if (!empty($result)) {
            return true;
        }

        // NOT the same email
        if (isset($_REQUEST['email'])) {
            $email = sanitize_email($_REQUEST['email']);
            if ($email != '') {
                $sql = $wpdb->prepare(
                    "SELECT 1 FROM $this->participation_table_name p
                    LEFT JOIN $this->user_table_name u
                    ON p.id_user = u.id
                    WHERE id_concours = %d
                    AND email = '%s'",
                    $this->concours_id,
                    $email
                );

                $result = $wpdb->get_results($sql);

                if (!empty($result)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function save() {
        global $wpdb;
        
        if (!$this->check_values()) {
            return false;
        }

        $user_id = $this->get_user_from_email(sanitize_email($_REQUEST['email']));
        // New user?
        if ($user_id === false) {
            $user_data = array(
                'firstname' => esc_html($_REQUEST['firstname']),
                'lastname' => esc_html($_REQUEST['lastname']),
                'email' => sanitize_email($_REQUEST['email']),
            );
            $wpdb->insert($this->user_table_name, $user_data);
            $user_id = $wpdb->insert_id;
        }
        // Register participation
        $participation_data = array(
            'id_user' => $user_id,
            'id_concours' => $this->concours_id,
            'subscribe_newsletter' => ((isset($_REQUEST['newsletter']) && ($_REQUEST['newsletter'])) ? 1 : 0),
            'ip_address' => filter_var($_SERVER["REMOTE_ADDR"], FILTER_VALIDATE_IP)
        );

        return $wpdb->insert($this->participation_table_name, $participation_data);
    }

    private function check_values() {
        if (sanitize_email($_REQUEST['email']) == '') {
            return false;
        }
        return true;
    }

    private function get_user_from_email($email) {
        global $wpdb;

        $sql = $wpdb->prepare(
            "SELECT `id` FROM $this->user_table_name
            WHERE email = '%s';",
            $email
        );

        $result = $wpdb->get_results($sql);
        if (!empty($result)) {
            return $result[0]->id;
        }

        return false;
    }

    private function get_http_response_code($url) {
        $headers = get_headers($url);
        return substr($headers[0], 9, 3);
    }

    private function send_confirmation_email() {
        if (!isset($GLOBALS['olyos_concours_settings']['concours_thankyoumail_checkbox'])) {
            return;
        }

        $email_target = sanitize_email($_REQUEST['email']);
		$email_title = __('Thanks for your participation', 'wp-concours');
		$email_content = file_get_contents( CONCOURS_PLUGIN_DIR . "/includes/thankyou-mail.html");

        $email_content = str_replace('{{plugin_url}}', plugin_dir_url(__FILE__).'../', $email_content);
        $email_content = str_replace('{{message_title}}', __('Your participation to this contest have been registered !', 'wp-concours'), $email_content);

        $message_body = (isset($GLOBALS['olyos_concours_settings']['concours_thankyoumail_body'])) ? $GLOBALS['olyos_concours_settings']['concours_thankyoumail_body'] : '';
        $email_content = str_replace('{{message_body}}', $message_body, $email_content);

        add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));

		wp_mail($email_target, $email_title, $email_content);
    }
}