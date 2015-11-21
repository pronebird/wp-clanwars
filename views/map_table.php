<div class="wrap wp-clanwars-maps">
	<h2><?php printf(__('Maps / %s', WP_CLANWARS_TEXTDOMAIN), esc_html($game_title)); ?> <a href="<?php echo admin_url('admin.php?page=wp-clanwars-games&act=addmap&game_id=' . $game_id); ?>" class="add-new-h2"><?php _e('Add New', WP_CLANWARS_TEXTDOMAIN); ?></a></h2>

	<form method="post">

	<?php $wp_list_table->display(); ?>

	</form>

</div><!-- .wrap -->