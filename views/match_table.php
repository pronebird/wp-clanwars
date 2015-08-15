<div class="wrap wp-cw-matches">
	<h2><?php _e('Matches', WP_CLANWARS_TEXTDOMAIN); ?> <a href="<?php echo admin_url('admin.php?page=wp-clanwars-matches&act=add'); ?>" class="add-new-h2"><?php _e('Add New', WP_CLANWARS_TEXTDOMAIN); ?></a></h2>

	<form action="<?php echo admin_url('admin-post.php'); ?>" method="post">
		<?php wp_nonce_field('wp-clanwars-deletematches'); ?>

		<?php $wp_list_table->display(); ?>

	</form>

</div>