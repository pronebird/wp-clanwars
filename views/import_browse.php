<div class="wrap wp-clanwars-import-page">
	<h2><?php _e('Import games', WP_CLANWARS_TEXTDOMAIN); ?> <a href="<?php echo admin_url( 'admin.php?page=wp-clanwars-import&tab=upload' ); ?>" class="upload add-new-h2"><?php _e('Upload Game', WP_CLANWARS_TEXTDOMAIN); ?></a></h2>

	<?php $partial('partials/import_nav', compact('active_tab', 'search_query')); ?>

	<?php if ( isset( $api_error_message ) ) : ?>
	
	<p class="wp-clanwars-api-error"><?php _e( 'Cannot connect to API.', WP_CLANWARS_TEXTDOMAIN ); ?></p>
	<p class="wp-clanwars-api-error small"><?php echo sprintf( __( 'Error: %s.', WP_CLANWARS_TEXTDOMAIN ), $api_error_message ); ?></p>

	<?php endif; ?>

	<ul class="wp-clanwars-gamepacks clearfix" id="wp-clanwars-gamepacks">

	<?php foreach ( $api_games as $game ) : ?>
		<li class="wp-clanwars-item">
			<div class="wp-clanwars-item-top">
				<div class="wp-clanwars-item-header clearfix">
					<div class="wp-clanwars-column-title">
						<h4>
							<img src="<?php esc_attr_e($game->iconUrl); ?>" alt="<?php esc_attr_e($game->title); ?>" class="wp-clanwars-item-icon" />
							<span class="game-title"><?php esc_html_e($game->title); ?></span>
						</h4>
					</div>
					<div class="wp-clanwars-column-install">
					<?php if($game->is_installed) : ?>
						<button type="button" class="button wp-clanwars-install-button" disabled="disabled"><?php _e( 'INSTALLED', WP_CLANWARS_TEXTDOMAIN ); ?></button>
					<?php else : ?>
						<form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>">
						
						<input type="hidden" name="action" value="<?php esc_attr_e( $install_action ); ?>" />
						<input type="hidden" name="remote_url" value="<?php esc_attr_e( $game->zipUrl ); ?>" />

						<?php wp_nonce_field( $install_action ); ?>

						<button type="submit" class="button wp-clanwars-install-button" data-text-toggle="<?php esc_attr_e( __( 'INSTALL NOW', WP_CLANWARS_TEXTDOMAIN ) ); ?>"><?php _e( 'GET', WP_CLANWARS_TEXTDOMAIN ); ?></button>
						
						</form>
					<?php endif; ?>
					</div>
				</div>
				<ul class="maps">
				<?php foreach($game->maps as $map) : ?>
					<li>
						<img class="screenshot" src="<?php esc_attr_e($map->imageUrl); ?>" alt="<?php esc_attr_e($map->title); ?>" draggable="false" />
						<div class="title"><?php esc_html_e($map->title); ?></div>
					</li>
				<?php endforeach; ?>
				</ul>
			</div>
			<div class="wp-clanwars-item-bottom">
				<div class="wp-clanwars-column-rating">
					<div class="star-rating">
						<div class="star star-empty"></div>
						<div class="star star-empty"></div>
						<div class="star star-empty"></div>
						<div class="star star-empty"></div>
						<div class="star star-empty"></div>
					</div>
					<span class="num-ratings"><?php echo sprintf( _x('(%d)', 'Number of ratings', WP_CLANWARS_TEXTDOMAIN), $game->votes ); ?></span>
				</div>
				<div class="wp-clanwars-column-published">
					<strong><?php _e('Published:', WP_CLANWARS_TEXTDOMAIN); ?></strong>
					<span><?php esc_html_e( mysql2date(get_option('date_format'), $game->date, true) ); ?></span>
				</div>
				<div class="wp-clanwars-column-downloaded"><?php echo sprintf( _nx('%d install', '%d installs', $game->downloads, 'Number of downloads', WP_CLANWARS_TEXTDOMAIN ), $game->downloads ); ?></div>
				<div class="wp-clanwars-column-author">
					<img src="<?php esc_attr_e( $game->gravatar_url ); ?>" alt="" class="gravatar" /> <?php esc_html_e($game->author); ?>
				</div>
			</div>
		</li>
	<?php endforeach; ?>

	</ul>

</div>