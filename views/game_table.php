<div class="wrap wp-clanwars-games">
	<h2><?php _e('Games', WP_CLANWARS_TEXTDOMAIN); ?>
		<?php if($show_add_button) : ?> <a href="<?php echo admin_url('admin.php?page=wp-clanwars-games&act=add'); ?>" class="add-new-h2"><?php _e('Add New', WP_CLANWARS_TEXTDOMAIN); ?></a><?php endif; ?>
	</h2>

	<form method="post">

	<?php $wp_list_table->display(); ?>

	</form>

</div><!-- .wrap -->