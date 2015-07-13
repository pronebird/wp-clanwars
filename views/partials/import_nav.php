<div class="wp-filter">
    <ul class="filter-links">
        <li>
        <?php if( $active_tab == 'search' ) : ?>
            <a href="<?php esc_attr_e( $_SERVER['REQUEST_URI'] ); ?>" class="current"><?php _e( 'Search Results', WP_CLANWARS_TEXTDOMAIN ); ?></a>
        <?php endif; ?>

            <a href="<?php echo admin_url('admin.php?page=wp-clanwars-import'); ?>"<?php if($active_tab == 'popular') : ?> class="current"<?php endif; ?>><?php _e( 'Popular', WP_CLANWARS_TEXTDOMAIN ); ?></a>
            <a href="<?php echo admin_url('admin.php?page=wp-clanwars-import&tab=publish'); ?>"<?php if($active_tab == 'publish') : ?> class="current"<?php endif; ?>><?php _e( 'Publish', WP_CLANWARS_TEXTDOMAIN ); ?></a>
        </li>
    </ul>
    <form class="search-form" method="get" action="<?php echo admin_url( 'admin.php' ); ?>">
        <input type="hidden" name="page" value="wp-clanwars-import" />
        <input type="search" name="q" value="<?php if(isset($search_query)) esc_attr_e($search_query); ?>" class="wp-filter-search" placeholder="<?php esc_attr_e(__('Search Games', WP_CLANWARS_TEXTDOMAIN)); ?>" />
    </form>
</div>