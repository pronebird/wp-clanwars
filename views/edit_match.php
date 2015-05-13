<script type="text/javascript">
jQuery(document).ready(function ($) {
	// payload
	var payload = <?php echo json_encode($scores); ?>;
	$.each(payload, function (i, item) {
		var match = wpMatchManager.addMap(i, item.map_id);
		var len = item.team1.length;
		for(var j = 0; j < len; j++) {
			match.addRound(item.team1[j], item.team2[j], item.round_id[j]);
		}
	});
});
</script>

<div class="wrap wp-cw-matcheditor">

	<h2><?php echo $page_title; ?>

	<?php if($post_id) : ?>
	<ul class="linkbar">
		<li class="edit-post"><a href="<?php echo esc_attr(admin_url('post.php?post=' . $post_id . '&action=edit')); ?>" target="_blank"><?php _e('Edit post', WP_CLANWARS_TEXTDOMAIN); ?></a></li>
		<li class="view-post"><a href="<?php echo esc_attr(get_permalink($post_id)); ?>" target="_blank"><?php _e('View post', WP_CLANWARS_TEXTDOMAIN); ?></a></li>
		<li class="post-comments"><a href="<?php echo get_comments_link($post_id); ?>" target="_blank"><?php printf( _n( '%d Comment', '%d Comments', $num_comments, WP_CLANWARS_TEXTDOMAIN), $num_comments ); ?></a></li>
	</ul>
	<?php endif; ?>

	</h2>

	<form name="match-editor" id="match-editor" method="post" action="<?php esc_attr_e($_SERVER['REQUEST_URI']); ?>" enctype="multipart/form-data">

		<input type="hidden" name="action" value="<?php esc_attr_e($page_action); ?>" />
		<input type="hidden" name="id" value="<?php esc_attr_e($id); ?>" />

		<?php wp_nonce_field($page_action); ?>

		<table class="form-table">

		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="game_id"><?php _e('Game', WP_CLANWARS_TEXTDOMAIN); ?></label></th>
			<td>
				<select id="game_id" name="game_id">
					<?php foreach($games as $item) : ?>
					<option value="<?php esc_attr_e($item->id); ?>"<?php selected($item->id, $game_id); ?>><?php esc_html_e($item->title); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>

		<tr class="form-field form-required">
			<th scope="row" valign="top"><label for="title"><?php _e('Title', WP_CLANWARS_TEXTDOMAIN); ?></label></th>
			<td>
				<input name="title" id="title" type="text" value="<?php esc_attr_e($title); ?>" placeholder="<?php _e('For example: ESL Winter League', WP_CLANWARS_TEXTDOMAIN); ?>" maxlength="200" autocomplete="off" aria-required="true" />
			</td>
		</tr>

		<tr class="form-field">
			<th scope="row" valign="top"><label for="description"><?php _e('Description', WP_CLANWARS_TEXTDOMAIN); ?></label></th>
			<td>
				<textarea name="description" id="description" placeholder="<?php _e('Optional: Drop a line or two about match. You can always edit post directly and add screenshots to gallery.', WP_CLANWARS_TEXTDOMAIN); ?>"><?php esc_html_e($description); ?></textarea>
			</td>
		</tr>

		<tr class="form-field">
			<th scope="row" valign="top"><label for="external_url"><?php _e('External URL', WP_CLANWARS_TEXTDOMAIN); ?></label></th>
			<td>
				<input type="text" name="external_url" id="external_url" value="<?php esc_attr_e($external_url); ?>" />

				<p class="description"><?php _e('Enter league or external match URL.', WP_CLANWARS_TEXTDOMAIN); ?></p>
			</td>
		</tr>

		<tr class="form-required">
			<th scope="row" valign="top"><label for=""><?php _e('Match status', WP_CLANWARS_TEXTDOMAIN); ?></label></th>
			<td>
				<?php foreach($match_statuses as $index => $text) : ?>

				<p>
					<label for="match_status_<?php esc_attr_e($index); ?>"><input type="radio" value="<?php esc_attr_e($index); ?>" name="match_status" id="match_status_<?php esc_attr_e($index); ?>"<?php checked($index, $match_status, true); ?> /> <?php echo $text; ?></label>
				</p>

				<?php endforeach; ?>
			</td>
		</tr>

		<tr class="form-required">
			<th scope="row" valign="top"><label for=""><?php _e('Date', WP_CLANWARS_TEXTDOMAIN); ?></label></th>
			<td>
				<?php $html_date_helper('date', $date); ?>
			</td>
		</tr>

		<tr class="form-required">
			<th scope="row" valign="top"></th>
			<td>
				<div class="match-results" id="matchsite">

					<div class="teams">
					<select name="team1" class="team-select">
					<?php foreach($teams as $team) : ?>
						<option value="<?php echo $team->id; ?>"<?php selected(true, $team1 > 0 ? ($team->id == $team1) : $team->home_team, true); ?>><?php esc_html_e($team->title); ?></option>
					<?php endforeach; ?>
					</select>&nbsp;<?php _e('vs', WP_CLANWARS_TEXTDOMAIN); ?>&nbsp;<select name="team2" class="team-select">
					<?php foreach($teams as $team) : ?>
						<option value="<?php echo $team->id; ?>"<?php selected(true, $team->id==$team2, true); ?>><?php esc_html_e($team->title); ?></option>
					<?php endforeach; ?>
					</select>
					</div>

					<div class="team2-inline">
						<p><label for="new_team_title"><?php _e('or quickly add new opponent:', WP_CLANWARS_TEXTDOMAIN); ?></label></p>
						<p class="clearfix">
						<input name="new_team_title" id="new_team_title" type="text" value="" placeholder="<?php _e('New Team', WP_CLANWARS_TEXTDOMAIN); ?>" maxlength="200" autocomplete="off" aria-required="true" />
						<?php $html_country_select_helper('name=new_team_country&show_popular=1&id=country'); ?>
						</p>
					</div>
					<div id="mapsite"></div>
					<div class="add-map" id="wp-cw-addmap">
						<button class="button button-secondary"><span class="dashicons dashicons-plus"></span> <?php _e('Add map', WP_CLANWARS_TEXTDOMAIN); ?></button>
					</div>

				</div>
			</td>
		</tr>

		</table>

		<p class="submit"><input type="submit" class="button button-primary" id="wp-cw-submit" name="submit" value="<?php esc_attr_e($page_submit); ?>" /></p>

	</form>

</div><!-- .wrap -->