<div class="wrap wp-clanwars-cloud-page">
    <h1 class="wp-heading-inline"><?php _e('Install from ZIP', WP_CLANWARS_TEXTDOMAIN); ?></h1>
    <a href="<?php echo admin_url( 'admin.php?page=wp-clanwars-cloud' ); ?>" class="page-title-action"><?php _e('Back to Cloud', WP_CLANWARS_TEXTDOMAIN); ?></a>
    <hr class="wp-header-end" />
    <p class="wp-clanwars-install-help"><?php _e('If you have a game pack in a .zip format, you may install it by uploading it here.', WP_CLANWARS_TEXTDOMAIN); ?></p>
    <p class="wp-clanwars-install-help small"><?php _e('Import may take some time. Please do not refresh browser when in progress.', WP_CLANWARS_TEXTDOMAIN); ?></p>

    <form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>" enctype="multipart/form-data" class="wp-clanwars-upload-form">

        <input type="hidden" name="action" value="<?php echo esc_attr( $install_action ); ?>" />
        <?php wp_nonce_field( $install_action ); ?>

        <input type="file" name="userfile" />
        <input type="submit" class="button wp-clanwars-install-button" value="<?php _e('Install Now', WP_CLANWARS_TEXTDOMAIN); ?>" />

    </form>

</div>