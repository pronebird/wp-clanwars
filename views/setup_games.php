<div class="wrap wp-cw-onboarding setup-games">

	<h2><?php _e( 'Get started with WP-Clanwars', WP_CLANWARS_TEXTDOMAIN ); ?></h2>
	<h3><?php _e( 'Install games', WP_CLANWARS_TEXTDOMAIN ); ?></h3>

	<form action="<?php echo admin_url('admin-post.php'); ?>" method="post">

		<input type="hidden" name="action" value="wp-clanwars-setupgames" />

		<?php wp_nonce_field('wp-clanwars-setupgames'); ?>

		<p><?php _e( 'Choose one of the options:', WP_CLANWARS_TEXTDOMAIN ); ?></p>

		<?php if(!empty($import_list)) : ?>

		<fieldset>
			<p><label for="available"><input type="radio" name="import" id="available" value="available" checked="checked" /> <?php _e('Import from available games', WP_CLANWARS_TEXTDOMAIN); ?></label></p>

			<ul class="available-games">

			<?php foreach($import_list as $index => $game) : ?>

				<li>
					<label for="game-<?php esc_attr_e($index); ?>">
						<input type="checkbox" name="items[]" id="game-<?php esc_attr_e($index); ?>" value="<?php esc_attr_e($index); ?>" /> <img src="<?php esc_attr_e(trailingslashit(WP_CLANWARS_IMPORTURL) . $game->icon); ?>" alt="<?php esc_attr_e($game->title); ?>" /> <?php esc_html_e($game->title); ?>
						<?php if($game->is_installed) : ?>
						<span class="description"><?php _e('installed', WP_CLANWARS_TEXTDOMAIN); ?></span>
						<?php endif; ?>
					</label>
				</li>

			<?php endforeach; ?>

			</ul>
		</fieldset>

		<?php endif; ?>

		<fieldset>
			<p><label for="upload"><input type="radio" name="import" id="upload" value="upload" /> <?php _e('Upload previously saved game (ZIP file)', WP_CLANWARS_TEXTDOMAIN); ?></label></p>
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