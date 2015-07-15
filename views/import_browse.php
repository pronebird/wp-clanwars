<div class="wrap wp-clanwars-import-page">
	<h2><?php _e('Import games', WP_CLANWARS_TEXTDOMAIN); ?> <a href="<?php echo admin_url( 'admin.php?page=wp-clanwars-import&tab=upload' ); ?>" class="upload add-new-h2"><?php _e('Upload Game', WP_CLANWARS_TEXTDOMAIN); ?></a></h2>

	<?php $partial('partials/import_nav', compact( 'active_tab', 'search_query' ) ); ?>

	<?php if ( isset( $api_error_message ) ) : ?>
	<?php $partial( 'partials/browse_games_error', compact( 'api_error_message' ) ) ?>
	<?php endif; ?>

	<ul class="wp-clanwars-gamepacks clearfix" id="wp-clanwars-gamepacks">

	<?php foreach ( $api_games as $game ) : ?>
	<li class="wp-clanwars-item">
		<?php $partial('partials/browse_game_item', compact('game', 'install_action')); ?>
	</li>
	<?php endforeach; ?>

	</ul>

</div>