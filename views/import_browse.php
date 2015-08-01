<div class="wrap wp-clanwars-import-page">

	<div class="clearfix">
		<?php $partial('partials/account_info', compact('cloud_account', 'logged_into_cloud')); ?>
		<h2><?php _e('Import games', WP_CLANWARS_TEXTDOMAIN); ?> <a href="<?php echo admin_url( 'admin.php?page=wp-clanwars-import&tab=upload' ); ?>" class="upload add-new-h2"><?php _e('Upload Game', WP_CLANWARS_TEXTDOMAIN); ?></a></h2>
	</div>
	
	<?php $partial('partials/import_nav', compact( 'active_tab', 'search_query' ) ); ?>

	<?php if ( isset( $api_error_message ) ) : ?>
	<?php $partial( 'partials/browse_games_error', compact( 'api_error_message' ) ) ?>
	<?php endif; ?>

	<?php if ( empty($api_games) ) : ?>
	<p class="wp-clanwars-api-error"><?php _e( 'No games found.', WP_CLANWARS_TEXTDOMAIN ); ?></p>
	<?php endif; ?>

	<ul class="wp-clanwars-gamepacks clearfix" id="wp-clanwars-gamepacks">

	<?php foreach ( $api_games as $game ) : ?>
	<li class="wp-clanwars-item">
		<?php $partial('partials/browse_game_item', compact('game', 'install_action')); ?>
	</li>
	<?php endforeach; ?>

	</ul>

</div>