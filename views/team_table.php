<div class="wrap wp-clanwars-teams">
	<h1 class="wp-heading-inline"><?php _e('Teams', WP_CLANWARS_TEXTDOMAIN); ?></h1>
	<a href="<?php echo admin_url('admin.php?page=wp-clanwars-teams&act=add'); ?>" class="page-title-action"><?php _e('Add New', WP_CLANWARS_TEXTDOMAIN); ?></a>

	<form method="post">
	
	<?php $wp_list_table->display(); ?>

	</form>

</div><!-- .wrap -->