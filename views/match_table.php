<div class="wrap wp-cw-matches">
	<h2><?php _e('Matches', WP_CLANWARS_TEXTDOMAIN); ?> <a href="<?php echo admin_url('admin.php?page=wp-clanwars-matches&act=add'); ?>" class="add-new-h2"><?php _e('Add New', WP_CLANWARS_TEXTDOMAIN); ?></a></h2>

	<div id="poststuff" class="metabox-holder">

		<div id="post-body">
			<div id="post-body-content" class="has-sidebar-content">

			<form id="wp-clanwars-manageform" action="admin-post.php" method="post">
				<?php wp_nonce_field('wp-clanwars-deletematches'); ?>

				<input type="hidden" name="action" value="wp-clanwars-deletematches" />

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

				<!-- .no-items  -->
				<?php if(empty($matches)) : ?>

				<tr class="no-items">
					<td class="colspanchange" colspan="7"><?php _e('No items found.', WP_CLANWARS_TEXTDOMAIN); ?></td>
				</tr>

				<?php endif; ?>

				<?php foreach($matches as $i => $item) : ?>

					<tr class="iedit<?php if($i % 2 == 0) echo ' alternate'; ?>">
						<th scope="row" class="check-column"><input type="checkbox" name="delete[]" value="<?php echo $item->id; ?>" /></th>
						<td class="title column-title">
							<a class="row-title" href="<?php echo admin_url('admin.php?page=wp-clanwars-matches&amp;act=edit&amp;id=' . $item->id); ?>" title="<?php echo sprintf(__('Edit &#8220;%s&#8221; Match', WP_CLANWARS_TEXTDOMAIN), esc_attr($item->title)); ?>"><?php echo esc_html($item->title); ?></a><br />
							<div class="row-actions">
								<span class="edit"><a href="<?php echo admin_url('admin.php?page=wp-clanwars-matches&amp;act=edit&amp;id=' . $item->id); ?>"><?php _e('Edit', WP_CLANWARS_TEXTDOMAIN); ?></a></span> | <span class="delete">
										<a href="<?php echo wp_nonce_url('admin-post.php?action=wp-clanwars-deletematches&amp;do_action=delete&amp;delete[]=' . $item->id . '&amp;_wp_http_referer=' . urlencode($_SERVER['REQUEST_URI']), 'wp-clanwars-deletematches'); ?>"><?php _e('Delete', WP_CLANWARS_TEXTDOMAIN); ?></a></span>
							</div>
						</td>
						<td class="game_title column-game_title">
							<?php if($item->game_icon_url !== false) : ?>
							<img src="<?php esc_attr_e($item->game_icon_url); ?>" alt="<?php echo esc_attr($item->game_title); ?>" class="icon" />
							<?php endif; ?>
							<?php esc_html_e($item->game_title); ?>
						</td>
						<td class="date column-date">
							<?php echo date('d.m.Y H:i', strtotime($item->date)); ?>
						</td>
						<td class="match_status column-match_status">
							<?php
							$n = $item->match_status;
							if(isset($match_statuses[$n])) {
								echo $match_statuses[$n];
							}
							?>
						</td>
						<td class="team1 column-team1">
							<?php echo $get_country_flag($item->team1_country, true); ?>
							<?php esc_html_e($item->team1_title); ?>
						</td>
						<td class="team2 column-team2">
							<?php echo $get_country_flag($item->team2_country, true); ?>
							<?php esc_html_e($item->team2_title); ?>
						</td>
						<td class="tickets column-tickets">
							<?php printf(__('%s:%s', WP_CLANWARS_TEXTDOMAIN), $item->team1_tickets, $item->team2_tickets); ?>
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

	</div>
</div> <!-- .wrap -->