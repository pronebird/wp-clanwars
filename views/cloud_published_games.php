<div class="wrap wp-clanwars-cloud-page">

	<?php $partial('partials/cloud_nav', compact( 'active_tab', 'cloud_account', 'logged_into_cloud', 'search_query' )); ?>

	<?php if ( isset( $api_error_message ) ) : ?>
	<?php $partial( 'partials/browse_games_error', compact( 'api_error_message' ) ) ?>
	<?php endif; ?>

	<?php if ( empty($api_games) ) : ?>
	<p class="wp-clanwars-api-error"><?php _e( 'No games found.', WP_CLANWARS_TEXTDOMAIN ); ?></p>
	<?php endif; ?>

	<ul class="wp-clanwars-cloud-items wp-clanwars-clearfix" id="wp-clanwars-cloud-items">

	<?php

	foreach ( $api_games as $game ) :

		$item_classes = array( 'wp-clanwars-cloud-item' );

		if( $logged_into_cloud && !property_exists($game, 'vote') ) {
			$item_classes[] = 'wp-clanwars-cloud-item-votes-enabled';
		}

		if( $logged_into_cloud && property_exists($game, 'vote') ) {
			$item_classes[] = 'wp-clanwars-cloud-item-voted';
		}

	?>
	<li class="<?php esc_attr_e(join(' ', $item_classes)) ?>" data-remote-id="<?php esc_attr_e($game->_id); ?>">
		<?php $partial('partials/browse_game_item', compact('game', 'install_action', 'logged_into_cloud')); ?>
	</li>
	<?php endforeach; ?>

	</ul>

</div>