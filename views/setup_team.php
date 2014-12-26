<div class="wrap wp-cw-onboarding setup-team">

	<h2><?php _e( 'Get started with WP-Clanwars', WP_CLANWARS_TEXTDOMAIN ); ?></h2>
	<h3><?php _e( 'Setup your team', WP_CLANWARS_TEXTDOMAIN ); ?></h3>

	<form action="<?php echo admin_url('admin-post.php'); ?>" method="post">

		<input type="hidden" name="action" value="wp-clanwars-setupteam" />

		<?php wp_nonce_field('wp-clanwars-setupteam'); ?>

		<p><?php _e( 'Hello there! Just a couple of things to complete your installation.', WP_CLANWARS_TEXTDOMAIN ); ?></p>

		<p><?php _e( 'What is the name of your team?', WP_CLANWARS_TEXTDOMAIN ); ?></p>
		<p>
			<input type="text" class="regular-text" name="title" required="true" placeholder="<?php _e("Type in your team's name", WP_CLANWARS_TEXTDOMAIN); ?>" />
		</p>

		<p><?php _e( 'Where is it from?', WP_CLANWARS_TEXTDOMAIN ); ?></p>
		<p>
			<?php $html_country_select_helper('name=country'); ?>
		</p>

		<p class="submit">
			<input type="submit" class="button button-primary" value="<?php esc_attr_e($page_submit); ?>" />
		</p>

	</form>

</div><!-- .wrap -->