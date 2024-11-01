<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<div class="wrap">
    <?php olyosconc_display_admin_tabs($_GET['page']); ?>
    <h1><?php _e('Plugin informations', 'wp-concours'); ?></h1>

    <div id="concours-content">
        <div id="concours-content-main">
            <div class="postbox">
                <div class="inside">
                    <p><?php _e('This plugin allows you to easily generate contests in WordPress. You can easily create a marketing game for your visitors and integrate it wherever you like on your website in a few clicks. Whether it\'s in a page or in a post, you can simply integrate it with a shortcode.', 'wp-concours'); ?></p>
                    <h3><?php _e('Functionality', 'wp-concours'); ?></h3>
                    <ul>
                        <li><?php printf(wp_kses(__('You can create as many contest as you want and see the list in the "<a href="%s">Contests list</a>" tab', 'wp-concours'), array('a'=>array('href'=>array()))), esc_url('?page=concours-list')); ?></li>
                        <li><?php _e('You can customize your "contest block" as you want', 'wp-concours'); ?></li>
                        <li><?php _e('Insert your Facebook and Twitter to maximize your community', 'wp-concours'); ?></li>
                        <li><?php _e('Choose a validity duration for your contest', 'wp-concours'); ?></li>
                        <li><?php printf(wp_kses(__('When your contest is closed, you can <a href="%s">Pick winners</a>', 'wp-concours'), array('a'=>array('href'=>array()))), esc_url('/wp-admin/admin.php?page=concours-participant-list')); ?></li>
                        <li><?php _e('If you want to go further in your digital strategy, you can contact us at ', 'wp-concours'); ?> <a href="https://www.olyos.fr/?utm_source=ContestWP&utm_campaign=contestplugin&utm_medium=adminlink" title="Agence web Nantes" target="_blank">Olyos</a></li>
                    </ul>
                    <h3><?php _e('Tutorial', 'wp-concours'); ?></h3>
                    <iframe src="https://www.youtube.com/embed/47ds42123XQ" frameborder="0" allowfullscreen></iframe>
                    <h4><?php _e('French laws concerning contests :', 'wp-concours'); ?></h4>
                    <ul>
                        <li>Depuis le 20 décembre 2014, d'après l'article <a href="https://www.legifrance.gouv.fr/affichCodeArticle.do?cidTexte=LEGITEXT000006069565&idArticle=LEGIARTI000006292105" target="_blank">L121-36</a> du code de la consommation, vous n'êtes plus obligé de déposer un réglement auprès d'un huissier de justice. Par contre il est préférable de le faire pour vous prévenir d'un potentiel litige avec un participant.</li>
                        <li>Déclarer votre base de donnée sur la <a href="https://www.cnil.fr/fr/declarer-un-fichier" target="_blank">CNIL</a></li>
                    </ul>
                    <h4><?php _e('Laws concerning contests :', 'wp-concours'); ?></h4>
                    <ul>
                        <li><?php _e('Please follow the rules of your own country regarding contests creation and participation.', 'wp-concours'); ?></li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="concours-content-aside">
            <?php require_once( CONCOURS_PLUGIN_DIR . 'includes/admin_column.php' ); ?>
        </div>
    </div>
</div>