<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<form name="concours_add" method="post" action="<?php echo admin_url( 'admin.php' ); ?>">

<div id="titlediv">
    <div id="titlewrap">
        <input name="input-name" autofocus size="30" value="<?php echo $name; ?>" id="title" spellcheck="true" autocomplete="off" type="text" placeholder="<?php _e('Your contest name', 'wp-concours'); ?>">
    </div>
</div>

<?php if ($id !== ''): ?>
    <div id="concours-shortcode">
        <strong><?php _e('Shortcode : ', 'wp-concours'); ?></strong>
        <input type="text" readonly value="[concours id=<?php echo $id ?>]"></input>
    </div>
<?php endif; ?>

<?php wp_editor($description, 'description'); ?>


<section id="general_options" class="postbox">
<div class="inside">
    <table class="form-table">        
        <tr>
        <th scope="row"><label><?php _e('Contest date : ', 'wp-concours'); ?></label></th>
        <td>
            <label for="date-start"><?php _e('From : ', 'wp-concours'); ?></label><input type="text" id="date-start" class="mytimedatepicker" name="date-start" value="<?php echo $date_start?>" />
            <label for="date-end"><?php _e('To : ', 'wp-concours'); ?></label><input type="text" id="date-end" class="mytimedatepicker" name="date-end" value="<?php echo $date_end?>" />
        </td>
        </tr>
    </table>
</div>
</section>

<section id="social_options" class="postbox">
<div class="inside">
    <table class="form-table">
        <tr>
            <th scope="row"><label for="social-title"><?php _e('Social block title : ', 'wp-concours'); ?></label></th>
            <td><input type="text" id="social-title" name="social-title" value="<?php echo $social_title_content; ?>"/></td>
        </tr>
        <tr>
            <th scope="row"><label for=""><?php _e('Facebook : ', 'wp-concours'); ?></label></th>
            <td>
                <textarea name="input-social1" rows="4"><?php echo $social1; ?></textarea>
                <p class="description"><?php _e('Insert the iframe of your Facebook page. ', 'wp-concours'); ?><a href="https://developers.facebook.com/docs/plugins/page-plugin" target="_blank"><?php _e('See documentation', 'wp-concours'); ?></a></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for=""><?php _e('Twitter : ', 'wp-concours'); ?></label></th>
            <td>
                <textarea name="input-social2" rows="4"><?php echo $social2; ?></textarea>
                <p class="description"><?php _e('Insert the url of your tweet.', 'wp-concours'); ?> ex: https://twitter.com/Olybop/status/827114532921339904</p>
            </td>
        </tr>
        
    </table>
</div>
</section>

<section id="form_options" class="postbox">
<div class="inside">
    <table class="form-table">
        <tr>
        <th scope="row"><label for="form-title"><?php _e('Form block title : ', 'wp-concours'); ?></label></th>
        <td><input type="text" id="form-title" name="form-title" value="<?php echo $form_title_content; ?>"/></td>
        </tr>

        <tr>
        <th scope="row"><label for="newsletter-chb"><?php _e('Display newsletter checkbox : ', 'wp-concours'); ?></label></th>
        <td><input type="checkbox" id="newsletter-chb" name="newsletter-chb" value="checked" <?php echo($newsletter_checkbox == '1' ? 'checked="checked"' : '') ?>/></td>
        </tr>
    </table>
</div>
</section>

<section id="minor_options" class="postbox">
<div class="inside">
    <table class="form-table">
        <tr>
        <th scope="row"><label for="contest-terms"><?php _e('Contest terms : ', 'wp-concours'); ?></label></th>
        <td><textarea id="contest-terms" name="contest-terms" rows="4"><?php echo $terms; ?></textarea></td>
        </tr>
    </table>
</div>
</section>




<input type="hidden" name="concours_id" value="<?php echo $id; ?>"/>
<input type="hidden" name="insert_type" value="<?php echo $insert_type; ?>"/>
<input type="hidden" name="action" value="process_concours_form"/>

<p class="submit"><input type="submit" name="Save" value="<?php _e('Save contest', 'wp-concours'); ?>" class="button-primary" /></p>
</form>