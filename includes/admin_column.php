<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
?>

<div id="side-sortables">
	<div class="postbox voter">
		<h3><?php _e('Rate this plugin', 'wp-concours'); ?></h3>
		<p class="stars">★★★★★</p>
		<p><?php _e('Thanks for rating this plugin to thank us ! It\'s free and we need you !', 'wp-concours'); ?></p>
		<a id="rate-plugin" href="https://wordpress.org/plugins/wp-concours/" target="_blank" title="Voter">
			<?php _e('Vote', 'wp-concours'); ?>
		</a>
	</div>

	<div class="postbox">
		<h3><?php _e('About us', 'wp-concours'); ?></h3>
		<div id="olyosfr">
			<a href="https://www.olyos.fr/?utm_source=ContestWP&utm_campaign=contestplugin&utm_medium=adminlink" target="_blank" title="<?php _e('Wordpress plugins creation - Nantes Web Agency', 'wp-concours'); ?>">
				<img src="<?php echo esc_url(plugins_url('img/icon_olyos.png', dirname(__FILE__))); ?>"/>
				<div>
					<h4>olyos.fr</h4>
					<p><strong><?php _e('Nantes Web Agency', 'wp-concours'); ?></strong> : <?php _e('Digital strategy, custom creation of corporate and e-commerce websites.', 'wp-concours');?></p>
				</div>
			</a>
			<ul>
				<li><?php _e('Expertise and support for your <strong>digital strategy</strong>', 'wp-concours'); ?></li>
				<li><?php _e('Creation of custom <strong>corporate</strong> and <strong>e-commerce</strong> websites', 'wp-concours'); ?></li>
				<li><?php _e('Wordpress & Prestashop <strong>Plugins</strong> Development', 'wp-concours'); ?></li>
				<li><?php _e('Social networks & SEO <strong>support</strong>', 'wp-concours'); ?></li>
			</ul>
			<a id="contact-us" class="side-button" href="https://www.olyos.fr/contact/?utm_source=ContestWP&utm_campaign=contestplugin&utm_medium=adminlink" target="_blank" title="<?php _e('Contact us', 'wp-concours'); ?>">
				<?php _e('Contact us', 'wp-concours'); ?>
			</a>
		</div>
		<hr>
		<div id="olybopfr">
			<a href="//olybop.fr/?utm_source=ContestWP&utm_campaign=contestplugin&utm_medium=adminlink" target="_blank" title="<?php _e('News web, webdesign, graphic design', 'wp-concours'); ?>">
				<img src="<?php echo esc_url(plugins_url('img/icon_olybop.png', dirname(__FILE__))); ?>"/>
				<div>
					<h4>olybop.fr</h4>
					<p>Découvrez l’actualités Webdesign / Graphisme et notre expertise corporate sur les tendances web.</p>
				</div>
			</a>
			<a id="visit-blog" class="side-button" href="//olybop.fr/?utm_source=ContestWP&utm_campaign=contestplugin&utm_medium=adminlink" target="_blank" title="<?php _e('Contact us', 'wp-concours'); ?>">
				<?php _e('Visit Blog', 'wp-concours'); ?>
			</a>
		</div>
	</div>

	<div class="postbox community">
		<h3><?php _e('Community', 'wp-concours'); ?></h3>
		<p><?php _e('Join a community of more than 20 000 people !', 'wp-concours'); ?></p>
		<p>
			<a class="facebook" href="https://www.facebook.com/Olybop" target="_blank" title="<?php _e('', 'wp-concours'); ?>">Devenez fan !</a>
			<a class="twitter" href="https://twitter.com/Olybop" target="_blank" title="<?php _e('', 'wp-concours'); ?>">Follow !</a>
		</p>
	</div>

	<div class="postbox">
		<h3><?php _e('Informations', 'wp-concours'); ?></h3>
		<p><?php _e('Developed and tested from WP version : 4.7 and above', 'wp-concours'); ?></p>
		<p><strong><?php _e('Warning', 'wp-concours'); ?></strong> : <?php _e('If you decide to suppress this plugin, it will also delete all contests and participants from your website.', 'wp-concours'); ?></p>
		<p><span class="olyos-green">PREMIUM</span> : <?php _e('A more advanced version of this plugin will soon be available. We\'ll make sure to inform you.', 'wp-concours'); ?></p>

	</div>
</div>