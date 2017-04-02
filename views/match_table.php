<div class="wrap wp-clanwars-matches">
	<h1 class="wp-heading-inline"><?php _e('Matches', WP_CLANWARS_TEXTDOMAIN); ?></h1>
	<a href="<?php echo admin_url('admin.php?page=wp-clanwars-matches&act=add'); ?>" class="page-title-action"><?php _e('Add New', WP_CLANWARS_TEXTDOMAIN); ?></a>
	<hr class="wp-header-end" />

	<form method="post">
    
		<?php $wp_list_table->display(); ?>

	</form>

</div>