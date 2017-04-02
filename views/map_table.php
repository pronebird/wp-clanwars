<div class="wrap wp-clanwars-maps">
	<h1 class="wp-heading-inline"><?php printf(__('Maps / %s', WP_CLANWARS_TEXTDOMAIN), esc_html($game_title)); ?></h1>
	<a href="<?php echo admin_url('admin.php?page=wp-clanwars-games&act=addmap&game_id=' . $game_id); ?>" class="page-title-action"><?php _e('Add New', WP_CLANWARS_TEXTDOMAIN); ?></a>
	<hr class="wp-header-end" />

	<form method="post">

	<?php $wp_list_table->display(); ?>

	</form>

</div><!-- .wrap -->