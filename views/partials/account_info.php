<div class="wp-clanwars-cloud-account-wrap">

    <?php if($logged_into_cloud) : ?>

    <div class="wp-clanwars-cloud-account">
        <div class="username">
            <img src="<?php esc_attr_e($cloud_account->photo); ?>" /> 
            <?php esc_html_e($cloud_account->name); ?> 
        </div>
        <div class="logout">
            <span>|</span> <a href="#"><?php _e( 'Log out', WP_CLANWARS_TEXTDOMAIN ); ?></a>
        </div>
    </div>

    <?php else : ?>

    <div class="wp-clanwars-cloud-account-why-login">
        <?php _e( 'You are not logged in.', WP_CLANWARS_TEXTDOMAIN ); ?> <a href="#"><?php _e( 'Why should I?', WP_CLANWARS_TEXTDOMAIN ); ?></a>
    </div>

    <?php endif; ?>

</div>