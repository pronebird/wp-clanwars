<div class="wrap wp-cw-games">
	<h2><?php _e('Games', WP_CLANWARS_TEXTDOMAIN); ?>
		<?php if($show_add_button) : ?> <a href="<?php echo admin_url('admin.php?page=wp-clanwars-games&act=add'); ?>" class="add-new-h2"><?php _e('Add New', WP_CLANWARS_TEXTDOMAIN); ?></a><?php endif; ?>
	</h2>

	<div id="poststuff" class="metabox-holder">

		<div id="post-body">
			<div id="post-body-content" class="has-sidebar-content">

			<form id="wp-clanwars-manageform" action="admin-post.php" method="post">
				<?php wp_nonce_field('wp-clanwars-gamesop'); ?>

				<input type="hidden" name="action" value="wp-clanwars-gamesop" />

				<div class="tablenav">

					<div class="alignleft actions">
						<select name="do_action">
							<option value="" selected="selected"><?php _e('Bulk Actions', WP_CLANWARS_TEXTDOMAIN); ?></option>
							<option value="delete"><?php _e('Delete', WP_CLANWARS_TEXTDOMAIN); ?></option>
						</select>
						<input type="submit" value="<?php _e('Apply', WP_CLANWARS_TEXTDOMAIN); ?>" name="doaction" id="wp-clanwars-doaction" class="button-secondary action" />
					</div>

					<div class="alignright actions" style="display: none;">
						<label class="screen-reader-text" for="games-search-input"><?php _e('Search Teams:', WP_CLANWARS_TEXTDOMAIN); ?></label>
						<input id="games-search-input" name="s" value="<?php if(isset($search_title)) esc_attr_e($search_title); ?>" type="text" />

						<input id="games-search-submit" value="<?php _e('Search Games', WP_CLANWARS_TEXTDOMAIN); ?>" class="button" type="button" />
					</div>

				<br class="clear" />

				</div>

				<div class="clear"></div>

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

				<?php foreach($games as $i => $item) : ?>

					<tr class="iedit<?php if($i % 2 == 0) echo ' alternate'; ?>">
						<th scope="row" class="check-column"><input type="checkbox" name="items[]" value="<?php echo $item->id; ?>" /></th>
						<td class="title column-title">
							<a class="row-title" href="<?php echo admin_url('admin.php?page=wp-clanwars-games&amp;act=edit&amp;id=' . $item->id); ?>" title="<?php echo sprintf(__('Edit &#8220;%s&#8221; Team', WP_CLANWARS_TEXTDOMAIN), esc_attr($item->title)); ?>"> <?php echo esc_html($item->title); ?></a><br />
							<div class="row-actions">
								<span class="edit"><a href="<?php echo admin_url('admin.php?page=wp-clanwars-games&amp;act=edit&amp;id=' . $item->id); ?>"><?php _e('Edit', WP_CLANWARS_TEXTDOMAIN); ?></a></span> |
								<span class="edit"><a href="<?php echo admin_url('admin.php?page=wp-clanwars-games&amp;act=maps&amp;game_id=' . $item->id); ?>"><?php _e('Maps', WP_CLANWARS_TEXTDOMAIN); ?></a></span> |
								<span class="export"><a href="<?php echo wp_nonce_url('admin-post.php?action=wp-clanwars-gamesop&amp;do_action=export&amp;items[]=' . $item->id . '&amp;_wp_http_referer=' . urlencode($_SERVER['REQUEST_URI']), 'wp-clanwars-gamesop'); ?>"><?php _e('Export', WP_CLANWARS_TEXTDOMAIN); ?></a></span> |
								<span class="delete"><a href="<?php echo wp_nonce_url('admin-post.php?action=wp-clanwars-gamesop&amp;do_action=delete&amp;items[]=' . $item->id . '&amp;_wp_http_referer=' . urlencode($_SERVER['REQUEST_URI']), 'wp-clanwars-gamesop'); ?>"><?php _e('Delete', WP_CLANWARS_TEXTDOMAIN); ?></a></span>
							</div>
						</td>
						<td class="abbr column-abbr">
							<?php echo esc_html($item->abbr); ?>
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

			</div>
		</div>
		<br class="clear"/>

	</div><!-- #poststuff -->
</div><!-- .wrap -->