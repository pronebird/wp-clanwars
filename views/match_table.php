<div class="wrap wp-clanwars-matches">
	<h2><?php _e('Matches', WP_CLANWARS_TEXTDOMAIN); ?> <a href="<?php echo admin_url('admin.php?page=wp-clanwars-matches&act=add'); ?>" class="add-new-h2"><?php _e('Add New', WP_CLANWARS_TEXTDOMAIN); ?></a></h2>

	<form method="post">
    
		<?php $wp_list_table->display(); ?>

	</form>

</div>