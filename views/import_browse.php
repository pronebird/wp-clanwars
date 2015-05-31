<div class="wrap wp-clanwars-import-page">
	<h2><?php _e('Import games', WP_CLANWARS_TEXTDOMAIN); ?> <a href="<?php echo admin_url( 'admin.php?page=wp-clanwars-import&tab=upload' ); ?>" class="upload add-new-h2"><?php _e('Upload Game', WP_CLANWARS_TEXTDOMAIN); ?></a></h2>

	<div class="wp-filter">
		<ul class="filter-links">
			<li>
				<a href="<?php echo admin_url('admin.php?page=wp-clanwars-import'); ?>" class="current"><?php _e('Popular', WP_CLANWARS_TEXTDOMAIN); ?></a>
			</li>
		</ul>
		<form class="search-form" method="get" action="<?php echo admin_url( 'admin.php' ); ?>">
			<input type="hidden" name="page" value="wp-clanwars-import" />
			<input type="search" name="q" value="<?php esc_attr_e( $search_query ); ?>" class="wp-filter-search" placeholder="<?php esc_attr_e(__('Search Games', WP_CLANWARS_TEXTDOMAIN)); ?>" />
		</form>
	</div>

	<?php if ( isset( $api_error_message ) ) : ?>
	<p class="wp-clanwars-api-error"><?php _e( 'Cannot connect to API.', WP_CLANWARS_TEXTDOMAIN ); ?></p>
	<p class="wp-clanwars-api-error small"><?php echo sprintf( __( 'Error: %s.', WP_CLANWARS_TEXTDOMAIN ), $api_error_message ); ?></p>
	<?php endif; ?>

	<ul class="wp-clanwars-gamepacks clearfix">

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
						<button type="button" class="button" disabled="disabled">Installed</button>
					<?php else : ?>
						<a href="#" class="button"><?php _e('Install Now', WP_CLANWARS_TEXTDOMAIN); ?></a>
					<?php endif; ?>
					</div>
				</div>
				<ul class="maps">
				<?php foreach($game->maps as $map) : ?>
					<li>
						<img class="screenshot" src="<?php esc_attr_e($map->imageUrl); ?>" alt="<?php esc_attr_e($map->title); ?>" />
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
					<strong><?php _e('Author:', WP_CLANWARS_TEXTDOMAIN); ?></strong> <?php esc_html_e($game->author); ?>
				</div>
			</div>
		</li>
	<?php endforeach; ?>

	</ul>

</div>