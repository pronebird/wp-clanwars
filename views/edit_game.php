
<div class="wrap wp-clanwars-gameeditor">
    <h2><?php echo $page_title; ?></h2>

    <form name="team-editor" id="team-editor" method="post" action="<?php esc_attr_e($_SERVER['REQUEST_URI']); ?>" enctype="multipart/form-data">

        <input type="hidden" name="action" value="<?php esc_attr_e($page_action); ?>" />
        <input type="hidden" name="id" value="<?php esc_attr_e($game_id); ?>" />

        <?php wp_nonce_field($page_action); ?>

        <table class="form-table">

        <tr class="form-field form-required">
            <th scope="row" valign="top"><label for="title"><span class="alignleft"><?php _e('Title', WP_CLANWARS_TEXTDOMAIN); ?></span><span class="alignright"><abbr title="<?php _e('required', WP_CLANWARS_TEXTDOMAIN); ?>" class="required">*</abbr></span><br class="clear" /></label></th>
            <td>
                <input name="title" id="title" type="text" class="regular-text" value="<?php esc_attr_e($title); ?>" maxlength="200" autocomplete="off" aria-required="true" />
            </td>
        </tr>

        <tr class="form-field">
            <th scope="row" valign="top"><label for="abbr"><?php _e('Game tag', WP_CLANWARS_TEXTDOMAIN); ?></label></th>
            <td>
                <input name="abbr" id="abbr" type="text" class="regular-text" value="<?php esc_attr_e($abbr); ?>" maxlength="20" autocomplete="off" />
                <p class="description"><?php _e('For example: for Left 4 Dead 2 it can be L4D2.', WP_CLANWARS_TEXTDOMAIN); ?></p>
            </td>
        </tr>

        <tr>
            <th scope="row" valign="top"><label for="icon_file"><?php _e('Icon', WP_CLANWARS_TEXTDOMAIN); ?></label></th>
            <td>
                <input type="file" name="icon_file" id="icon_file" />

                <?php if(!empty($attach)) : ?>
                <div class="screenshot"><?php echo $attach; ?></div>
                <div>
                <label for="delete-image"><input type="checkbox" name="delete_image" id="delete-image" /> <?php _e('Delete Icon', WP_CLANWARS_TEXTDOMAIN); ?></label>
                </div>
                <?php endif; ?>
            </td>
        </tr>

        </table>

        <p class="submit"><input type="submit" class="button-primary" name="submit" value="<?php esc_attr_e($page_submit); ?>" /></p>

    </form>

</div><!-- .wrap -->