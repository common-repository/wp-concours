<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

// Get all contests
$contests = olyosconc_get_contests();
?>


<div class="wrap">
    <?php olyosconc_display_admin_tabs($_GET['page']); ?> 
    <h1><?php _e('Actions to realize on participants', 'wp-concours') ?></h1>

    <div id="poststuff" class="">
        <div id="post-body" class="metabox-holder">
            <div id="post-body-content">
                <div class="postbox choice-box" id="select-concours">
                    <div class="inside">
                        <form name="select_contest" method="get" action="">
                            <h3><?php _e('1 - Select your contest', 'wp-concours'); ?></h3>
                            <p><?php _e('Select a contest to pick winners or have a list of subscription to your newsletter.', 'wp-concours'); ?></p>
                            <select name="concours_id">
                                <option value=""><?php _e('All', 'wp-concours'); ?></option>
                                <?php foreach ($contests as $contest):?>
                                    <option 
                                        value="<?php echo $contest->id; ?>"
                                        <?php echo ((!empty($_REQUEST['concours_id']) && ($_REQUEST['concours_id'] == $contest->id)) ? 'selected' : ''); ?>
                                    >
                                        <?php echo($contest->id." : ".stripslashes($contest->name)); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" name="page" value="concours-participant-list" />
                            <input type="submit" name="pick" value="<?php _e('Select contest', 'wp-concours') ?>" class="button-primary" />
                        </form>
                    </div>
                </div>

                <div class="postbox choice-box" id="select-winners">
                    <div class="inside">
                        <form name="pick_winners" method="post" action="">
                            <h3><?php _e('2 - Pick winners', 'wp-concours'); ?></h3>
                            <p><?php _e('Insert in the field below the number of winners to your contest. Winners will be randomly picked.', 'wp-concours'); ?></p>
                            <input type="hidden" name="form-type" value="pick-winners" />
                            <input type="hidden" name="concours-select" value="<?php echo (!empty($_REQUEST['concours_id']) ? $_REQUEST['concours_id'] : '' ) ?>" />
                            <input type="number" name="winners-number" id="winners-number"/>
                            <input type="submit" name="pick" value="<?php _e('Pick winners', 'wp-concours') ?>" class="button-primary" />
                        </form>
                    </div>
                </div>

                <div class="postbox choice-box" id="select-newsletter">
                    <div class="inside">
                        <form name="newsletter_list" method="post" action="">
                            <h3><?php _e('3 - Generate a list "Optin" of Newsletter', 'wp-concours'); ?></h3>
                            <p><?php _e('Generate a list of subscriptions to your newsletter (from the contest selected in step 1)', 'wp-concours'); ?></p>
                            <input type="hidden" name="form-type" value="generate-newsletter" />
                            <input type="hidden" name="concours-select" value="<?php echo (!empty($_REQUEST['concours_id']) ? $_REQUEST['concours_id'] : '' ) ?>" />
                            <input type="submit" name="generate-newsletter" value="<?php _e('Generate', 'wp-concours') ?>" class="button-primary" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_REQUEST['form-type'])) {
            if ($_REQUEST['form-type'] == 'pick-winners') {
                // Generate a new winner list
                $id_concours = $_REQUEST['concours-select'];

                if ($id_concours === '') {
                    echo '<div class="message">'.__('You have to select a contest','olyos-concours').'</div>';
                }

                $nb_to_pick = absint($_REQUEST['winners-number']);
                if (!$nb_to_pick) {
                    $nb_to_pick = 1;
                }
                $winners = olyosconc_randomly_pick($id_concours, $nb_to_pick);

                echo olyosconc_generate_users_list_html($winners);

            } elseif ($_REQUEST['form-type'] == 'generate-newsletter') {
                // Generate list of participants who want the newsletter
                $id_concours = $_REQUEST['concours-select'];

                if ($id_concours === '') {
                    echo '<div class="message">'.__('You have to select a contest','olyos-concours').'</div>';
                } else {
                    $participants = olyosconc_get_participants($id_concours, true);

                    echo olyosconc_generate_users_list_html($participants);
                }
                
            }
        }
    }
?>

<h2><?php _e('List of participants to your contests', 'wp-concours') ?></h2>

<?php
    require_once( CONCOURS_PLUGIN_DIR . 'class/olyos_participants_list.php' );
    $participants_list = new OlyosParticipantsList();
    $participants_list->prepare_items();
    
    $participants_list->display();
?>

</div>





<?php
////////////////////////////
// Utilitary functions
////////////////////////////

function olyosconc_get_contests() {
    global $wpdb;

    $sql = "SELECT id, name FROM {$wpdb->prefix}olyos_concours";

    $result = $wpdb->get_results($sql);

    return $result;
}

function olyosconc_get_participants($id_concours, $only_newsletter = false) {
    global $wpdb;

    $sql = $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}olyos_concours_participation p
        LEFT JOIN {$wpdb->prefix}olyos_concours_user u
        ON p.id_user = u.id
        WHERE p.`id_concours` = %d",
        $id_concours
    );

    if ($only_newsletter) {
        $sql .= " AND `subscribe_newsletter` = 1";
    }

    $result = $wpdb->get_results($sql);

    return $result;

}

function olyosconc_randomly_pick($id_concours, $nb_to_pick = 1) {
    $pool = olyosconc_get_participants($id_concours);
    $winners_ids = [];
    if ($nb_to_pick >= count($pool)) {
        return $pool;
    }
    while ($nb_to_pick > 0) {
        $random_index = random_int(0, count($pool)-1);
        $new_winner = $pool[$random_index];
        if (in_array($new_winner->id, $winners_ids)) {
            // Already a Winner!
            continue;
        }
        $winners_ids[] = $new_winner->id;
        $nb_to_pick -= 1;
        $pick[] = $new_winner;
    }

    return $pick;
}

function olyosconc_generate_users_list_html($users) {
    $str ='';

     $str .= '<ul id="concours-participant-list-list" class="message">';

    foreach ($users as $user) {
        $str .= '<li>';
        $str .= esc_html('"'.$user->firstname.' '.$user->lastname.'" <'.$user->email.'>');
        $str .= '</li>';
    }

    $str .= '</ul>';

    return $str;
}