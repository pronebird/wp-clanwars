<div class="wrap">
	<h2><?php echo $page_title; ?></h2>

	<form name="team-editor" id="team-editor" method="post" action="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>" enctype="multipart/form-data">

		<input type="hidden" name="action" value="<?php echo esc_attr($page_action); ?>" />
		<input type="hidden" name="id" value="<?php echo esc_attr($team_id); ?>" />

		<?php wp_nonce_field($page_action); ?>

		<table class="form-table">

		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="title"><span class="alignleft"><?php _e('Title', WP_CLANWARS_TEXTDOMAIN); ?></span><span class="alignright"><abbr title="<?php _e('required', WP_CLANWARS_TEXTDOMAIN); ?>" class="required">*</abbr></span><br class="clear" /></label></th>
			<td>
				<input name="title" id="title" type="text" class="regular-text" value="<?php echo esc_attr($title); ?>" maxlength="200" autocomplete="off" aria-required="true" />
			</td>
		</tr>

		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="title"><?php _e('Country', WP_CLANWARS_TEXTDOMAIN); ?></label></th>
			<td>
				<?php echo $country_select; ?>
			</td>
		</tr>

		</table>

		<p class="submit"><input type="submit" class="button-primary" name="submit" value="<?php echo $page_submit; ?>" /></p>

	</form>
</div>