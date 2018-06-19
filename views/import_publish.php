<div class="wrap wp-clanwars-cloud-page">

    <?php $partial('partials/cloud_nav', compact('active_tab', 'cloud_account', 'logged_into_cloud')); ?>

    <p class="wp-clanwars-install-help"><?php _e('You can share created games with others.', WP_CLANWARS_TEXTDOMAIN); ?></p>

<?php if($logged_into_cloud) : ?>

    <p class="wp-clanwars-install-help small"><?php _e('Upload may take some time. Please do not refresh browser when in progress.', WP_CLANWARS_TEXTDOMAIN); ?></p>

    <form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>" enctype="multipart/form-data" class="wp-clanwars-publish-form">

        <input type="hidden" name="action" value="<?php echo esc_attr( $publish_action ); ?>" />
        <?php wp_nonce_field( $publish_action ); ?>

        <table class="form-table">
            <tr>
                <th><label for="userfile"><?php _e( 'ZIP file', WP_CLANWARS_TEXTDOMAIN ); ?></label></th>
                <td><input type="file" name="userfile" id="userfile" /></td>
            </tr>
        </table>

        <p>
            <label for="terms_confirm">
            <input type="checkbox" name="terms_confirm" id="terms_confirm" value="yes" />
            <?php _e('I agree that the contents I share will be available to other users free of charge.' ); ?>
            </label>
        </p>

        <p class="submit">
            <input type="submit" class="button" value="<?php _e('Publish Now', WP_CLANWARS_TEXTDOMAIN); ?>" />
        </p>

    </form>

<?php else : ?>

<p class="wp-clanwars-install-help small"><?php _e('Please log in first.', WP_CLANWARS_TEXTDOMAIN); ?></p>

<?php endif;?>

</div>
