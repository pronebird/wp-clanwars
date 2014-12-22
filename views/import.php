<div class="wrap">
	<div id="icon-tools" class="icon32"><br></div>
	<h2><?php _e('Import games', WP_CLANWARS_TEXTDOMAIN); ?></h2>
	<p><?php _e('Import may take some time. Please do not refresh browser when in progress.', WP_CLANWARS_TEXTDOMAIN); ?></p>

	<form id="wp-cw-import" method="post" action="admin-post.php" enctype="multipart/form-data">

		<input type="hidden" name="action" value="wp-clanwars-import" />
		<?php wp_nonce_field('wp-clanwars-import'); ?>

		<?php if(!empty($import_list)) : ?>

		<fieldset>
			<p><label for="available"><input type="radio" name="import" id="available" value="available" checked="checked" /> <?php _e('Import available games', WP_CLANWARS_TEXTDOMAIN); ?></label></p>

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
			<p><label for="upload"><input type="radio" name="import" id="upload" value="upload" /> <?php _e('Upload package (ZIP file)', WP_CLANWARS_TEXTDOMAIN); ?></label></p>
			<p><input type="file" name="userfile" /></p>
		</fieldset>

		<p class="submit"><input type="submit" class="button button-primary" value="<?php _e('Import', WP_CLANWARS_TEXTDOMAIN); ?>" /></p>

	</form>

</div>