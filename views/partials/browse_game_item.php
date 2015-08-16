<div class="wp-clanwars-cloud-item-top">
    <div class="wp-clanwars-cloud-item-header wp-clanwars-clearfix">
        <div class="wp-clanwars-cloud-item-column-title">
            <h4>
                <img src="<?php esc_attr_e($game->iconUrl); ?>" alt="<?php esc_attr_e($game->title); ?>" class="wp-clanwars-cloud-item-icon" />
                <span class="game-title"><?php esc_html_e($game->title); ?></span>
            </h4>
        </div>
        <div class="wp-clanwars-cloud-item-column-install">
        <?php if($game->is_installed) : ?>
            <button type="button" class="button wp-clanwars-install-button" disabled="disabled"><?php _e( 'INSTALLED', WP_CLANWARS_TEXTDOMAIN ); ?></button>
        <?php else : ?>
            <form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>">
            
            <input type="hidden" name="action" value="<?php esc_attr_e( $install_action ); ?>" />
            <input type="hidden" name="remote_id" value="<?php esc_attr_e( $game->_id ); ?>" />

            <?php wp_nonce_field( $install_action ); ?>

            <button type="submit" class="button wp-clanwars-install-button" data-text-toggle="<?php esc_attr_e( __( 'INSTALL NOW', WP_CLANWARS_TEXTDOMAIN ) ); ?>"><?php _e( 'GET', WP_CLANWARS_TEXTDOMAIN ); ?></button>
            
            </form>
        <?php endif; ?>
        </div>
    </div>
    <ul class="maps">
    <?php foreach($game->maps as $map) : ?>
        <li>
            <img class="screenshot" src="<?php esc_attr_e($map->imageUrl); ?>" alt="<?php esc_attr_e($map->title); ?>" draggable="false" />
            <div class="title"><?php esc_html_e($map->title); ?></div>
        </li>
    <?php endforeach; ?>
    </ul>
</div>
<div class="wp-clanwars-cloud-item-bottom">
    <div class="wp-clanwars-cloud-item-column-rating">
        <div class="star-rating">
            <?php
            for($i = 1; $i <= 5; $i++) :
                $star_class = 'empty';
                
                if( $game->rating >= $i ) {
                    $star_class = 'full';
                }
                else if( ceil($game->rating) >= $i ) {
                    $star_class = 'half';
                }
            ?>
            <div class="star star-<?php echo $star_class; ?>"></div>
            <?php endfor; ?>
        </div>
        <span class="num-ratings"><?php echo sprintf( _x('(%d)', 'Number of ratings', WP_CLANWARS_TEXTDOMAIN), $game->votes ); ?></span>
    </div>
    <div class="wp-clanwars-cloud-item-column-published">
        <strong><?php _e('Published:', WP_CLANWARS_TEXTDOMAIN); ?></strong>
        <span><?php esc_html_e( mysql2date(get_option('date_format'), $game->updatedAt, true) ); ?></span>
    </div>
    <div class="wp-clanwars-cloud-item-column-downloaded"><?php echo sprintf( _nx('%d install', '%d installs', $game->downloads, 'Number of downloads', WP_CLANWARS_TEXTDOMAIN ), $game->downloads ); ?></div>
    <div class="wp-clanwars-cloud-item-column-author">
        <?php esc_html_e($game->owner->fullname); ?>
    </div>
</div>