<div class="wrap wp-clanwars-teams">
	<h2><?php _e('Teams', WP_CLANWARS_TEXTDOMAIN); ?> <a href="<?php echo admin_url('admin.php?page=wp-clanwars-teams&act=add'); ?>" class="add-new-h2"><?php _e('Add New', WP_CLANWARS_TEXTDOMAIN); ?></a></h2>

	<form id="wp-clanwars-manageform" action="admin-post.php" method="post">
		<?php wp_nonce_field('wp-clanwars-deleteteams'); ?>

		<input type="hidden" name="action" value="wp-clanwars-deleteteams" />

		<div class="tablenav">

			<div class="alignleft actions">
				<select name="do_action">
					<option value="" selected="selected"><?php _e('Bulk Actions', WP_CLANWARS_TEXTDOMAIN); ?></option>
					<option value="delete"><?php _e('Delete', WP_CLANWARS_TEXTDOMAIN); ?></option>
				</select>
				<input type="submit" value="<?php _e('Apply', WP_CLANWARS_TEXTDOMAIN); ?>" name="doaction" id="wp-clanwars-doaction" class="button-secondary action" />
			</div>

			<div class="alignright actions" style="display: none;">
				<label class="screen-reader-text" for="teams-search-input"><?php _e('Search Teams:', WP_CLANWARS_TEXTDOMAIN); ?></label>
				<input id="teams-search-input" name="s" value="<?php if(isset($search_title)) esc_attr_e($search_title); ?>" type="text" />

				<input id="teams-search-submit" value="<?php _e('Search Teams', WP_CLANWARS_TEXTDOMAIN); ?>" class="button" type="button" />
			</div>

			<br class="clear" />

		</div>

		<table class="widefat fixed" cellspacing="0">
		<thead>
		<tr>
			<?php $print_table_header($table_columns); ?>
		</tr>
		</thead>

		<tfoot>
		<tr>
			<?php $print_table_header($table_columns, false); ?>
		</tr>
		</tfoot>

		<tbody>

		<?php if(empty($teams)) : ?>

		<tr class="no-items">
			<td class="colspanchange" colspan="2"><?php _e('No items found.', WP_CLANWARS_TEXTDOMAIN); ?></td>
		</tr>

		<?php endif; ?>

		<?php foreach($teams as $i => $item) : ?>

			<tr class="iedit<?php if($i % 2 == 0) echo ' alternate'; ?>">
				<th scope="row" class="check-column"><input type="checkbox" name="delete[]" value="<?php echo $item->id; ?>" /></th>
				<td class="column-icon media-icon">
					<?php if(!empty($item->attach)) echo $item->attach; ?>
				</td>
				<td class="title column-title">
					<a class="row-title" href="<?php echo admin_url('admin.php?page=wp-clanwars-teams&amp;act=edit&amp;id=' . $item->id); ?>" title="<?php echo sprintf(__('Edit &#8220;%s&#8221; Team', WP_CLANWARS_TEXTDOMAIN), esc_attr($item->title)); ?>"> <?php echo esc_html($item->title); ?> <?php if($item->home_team) _e('(Home Team)', WP_CLANWARS_TEXTDOMAIN); ?></a><br />
					<div class="row-actions">
						<span class="edit"><a href="<?php echo admin_url('admin.php?page=wp-clanwars-teams&amp;act=edit&amp;id=' . $item->id); ?>"><?php _e('Edit', WP_CLANWARS_TEXTDOMAIN); ?></a></span> | <span class="delete">
								<a href="<?php echo wp_nonce_url('admin-post.php?action=wp-clanwars-deleteteams&amp;do_action=delete&amp;delete[]=' . $item->id . '&amp;_wp_http_referer=' . urlencode($_SERVER['REQUEST_URI']), 'wp-clanwars-deleteteams'); ?>"><?php _e('Delete', WP_CLANWARS_TEXTDOMAIN); ?></a></span> | <span class="edit">
								<a href="<?php echo wp_nonce_url('admin-post.php?action=wp-clanwars-sethometeam&amp;do_action=sethometeam&amp;id=' . $item->id . '&amp;_wp_http_referer=' . urlencode($_SERVER['REQUEST_URI']), 'wp-clanwars-sethometeam'); ?>"><?php _e('Set As Home Team', WP_CLANWARS_TEXTDOMAIN); ?></a>
							</span>
					</div>
				</td>
				<td class="country column-country">
					<?php echo $get_country_flag($item->country, true); ?>
					<?php echo $get_country_title($item->country); ?>
				</td>
			</tr>

		<?php endforeach; ?>

		</tbody>

		</table>

		<div class="tablenav">

			<div class="tablenav-pages"><?php echo $page_links_text; ?></div>

			<div class="alignleft actions">
				<select name="do_action2">
					<option value="" selected="selected"><?php _e('Bulk Actions', WP_CLANWARS_TEXTDOMAIN); ?></option>
					<option value="delete"><?php _e('Delete', WP_CLANWARS_TEXTDOMAIN); ?></option>
				</select>
				<input type="submit" value="<?php _e('Apply', WP_CLANWARS_TEXTDOMAIN); ?>" name="doaction2" id="wp-clanwars-doaction2" class="button-secondary action" />
			</div>

			<br class="clear" />

		</div>

	</form>

</div><!-- .wrap -->