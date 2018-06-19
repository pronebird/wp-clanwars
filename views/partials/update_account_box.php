<div id="wp-clanwars-cloud-update-account-holder" class="hidden">
    <div class="wp-clanwars-cloud-update-account">

        <form action="<?php echo admin_url( 'admin-post.php' ); ?>" method="post" class="wp-clanwars-cloud-update-account-wrap">
            <input type="hidden" name="action" value="<?php echo esc_attr('wp-clanwars-update-cloud-account'); ?>" />
            <?php wp_nonce_field( 'wp-clanwars-update-cloud-account' ); ?>

            <div class="wp-clanwars-cloud-update-account-header">
                <img src="<?php echo esc_attr(WP_CLANWARS_URL . '/images/cloud-icon.svg'); ?>" width="100" alt="" />
            </div>

            <div class="wp-clanwars-cloud-update-account-form">
                <img src="<?php echo esc_attr($cloud_account->photo); ?>"
                    alt="<?php echo esc_attr($cloud_account->fullname); ?>"
                    class="wp-clanwars-cloud-update-account-picture" />
                <input type="text"
                    name="fullname"
                    value="<?php echo esc_attr($cloud_account->fullname); ?>"
                    placeholder="<?php _e( 'Enter your display name', WP_CLANWARS_TEXTDOMAIN ); ?>"
                    class="wp-clanwars-cloud-update-account-fullname" />
            </div>

            <div class="wp-clanwars-cloud-update-account-actions">
                <button class="button button-primary" type="submit">Save changes</button>
            </div>

        </form>

    </div>
</div>