<div class="wrap wp-clanwars-import-page">
    <h2><?php _e('Publish game', WP_CLANWARS_TEXTDOMAIN); ?> <a href="<?php echo admin_url( 'admin.php?page=wp-clanwars-import&tab=upload' ); ?>" class="upload add-new-h2"><?php _e('Upload Game', WP_CLANWARS_TEXTDOMAIN); ?></a></h2>
    
    <?php $partial('partials/import_nav', compact('active_tab')); ?>

    <p class="wp-clanwars-install-help"><?php _e('You can share created games with others.', WP_CLANWARS_TEXTDOMAIN); ?></p>
    <p class="wp-clanwars-install-help small"><?php _e('Upload may take some time. Please do not refresh browser when in progress.', WP_CLANWARS_TEXTDOMAIN); ?></p>

<?php if($logged_into_cloud) : ?>

    <form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>" enctype="multipart/form-data" class="wp-clanwars-publish-form">

        <input type="hidden" name="action" value="<?php esc_attr_e( $publish_action ); ?>" />
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
            <?php _e( 'I confirm that I am the creator of the graphic assets submitted and/or legally own the rights to use and distribute them under <a rel="license" target="_blank" href="http://creativecommons.org/licenses/by-sa/4.0/">Creative Commons Attribution-ShareAlike 4.0 International License</a>.' ); ?><br/><img alt="Creative Commons License" vspace="5" style="border-width:0;" src="https://i.creativecommons.org/l/by-sa/4.0/80x15.png" />
            </label>
        </p>

        <p class="submit">
            <input type="submit" class="button" value="<?php _e('Publish Now', WP_CLANWARS_TEXTDOMAIN); ?>" />
        </p>

    </form>

<?php else : ?>

    <div class="wp-clanwars-signin-form">
    <p><strong><?php _e( 'You have to log in first. Choose one of available log in methods:' ); ?></strong></p>
    <ul class="wp-clanwars-choose-signin-method">
        <li><a href="javascript:window.open('<?php esc_attr_e($steam_login_url); ?>', 'login', 'width=500,height=300')"><img src="../wp-content/plugins/wp-clanwars/images/steam-login.png" alt=""></a></li>
        <li><a href="javascript:window.open('<?php esc_attr_e($facebook_login_url); ?>', 'login', 'width=500,height=300')"><img src="../wp-content/plugins/wp-clanwars/images/facebook-login.png" alt=""></a></li>
    </ul>
    </div>

<?php endif;?>

</div>