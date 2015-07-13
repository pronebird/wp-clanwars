<div class="wrap wp-cw-onboarding setup-games">

	<h2><?php _e( 'Get started with WP-Clanwars', WP_CLANWARS_TEXTDOMAIN ); ?></h2>
	<h3><?php _e( 'Install games', WP_CLANWARS_TEXTDOMAIN ); ?></h3>

	<form action="<?php echo admin_url('admin-post.php'); ?>" method="post" enctype="multipart/form-data">

		<input type="hidden" name="action" value="wp-clanwars-setupgames" />

		<?php wp_nonce_field('wp-clanwars-setupgames'); ?>

		<p><?php _e( 'Choose one of the options:', WP_CLANWARS_TEXTDOMAIN ); ?></p>

		<fieldset>
			<p><label for="upload"><input type="radio" name="import" id="upload" value="upload" checked="checked" /> <?php _e('Upload previously saved game (ZIP file)', WP_CLANWARS_TEXTDOMAIN); ?></label></p>
			<p><input type="file" name="userfile" /></p>
		</fieldset>

		<fieldset>
			<p><label for="create"><input type="radio" name="import" id="create" value="create" /> <?php _e('Create a new game', WP_CLANWARS_TEXTDOMAIN); ?></label></p>
			<p><?php _e("<i>Not finding your game?</i> You can create your own!<br/>We'll take you to map editing right away.", WP_CLANWARS_TEXTDOMAIN); ?></p>
			<p><input type="text" class="regular-text" name="new_game_name" placeholder="<?php _e("Type in a new game's name", WP_CLANWARS_TEXTDOMAIN); ?>" /></p>
		</fieldset>

		<p class="submit">
			<input type="submit" class="button button-primary" value="<?php esc_attr_e($page_submit); ?>" />
		</p>

	</form>

</div><!-- .wrap -->