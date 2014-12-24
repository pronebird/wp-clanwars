<div class="wrap wp-cw-onboarding">

	<h2><?php _e( 'Welcome to WP-Clanwars', WP_CLANWARS_TEXTDOMAIN ); ?></h2>

	<form action="<?php echo admin_url('admin-post.php'); ?>" method="post">

		<input type="hidden" name="action" value="wp-clanwars-onboarding" />

		<?php wp_nonce_field('wp-clanwars-onboarding'); ?>

		<p><?php _e( 'Hi there! Just a couple of things to finish the installation.', WP_CLANWARS_TEXTDOMAIN ); ?></p>

		<p><?php _e( 'What is your team name?', WP_CLANWARS_TEXTDOMAIN ); ?></p>
		<p>
			<input type="text" class="regular-text" name="title" />
		</p>

		<p><?php _e( 'Where are you from?', WP_CLANWARS_TEXTDOMAIN ); ?></p>
		<p>
			<?php $html_country_select_helper(); ?>
		</p>

		<p class="submit">
			<input type="submit" class="button button-primary" value="Continue" />
		</p>

	</form>

</div><!-- .wrap -->