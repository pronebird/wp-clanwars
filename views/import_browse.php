<div class="wrap wp-clanwars-cloud-page">

	<?php $partial('partials/cloud_nav', compact( 'active_tab', 'cloud_account', 'logged_into_cloud', 'search_query' )); ?>

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