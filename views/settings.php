<div class="wrap wp-clanwars-settings">

	<h2><?php _e('Settings', WP_CLANWARS_TEXTDOMAIN); ?></h2>

	<!-- Basic Settings -->
	<form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
		<?php wp_nonce_field('wp-clanwars-settings'); ?>
		<input type="hidden" name="action" value="wp-clanwars-settings" />

		 <table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _e('Matches Category', WP_CLANWARS_TEXTDOMAIN); ?></th>
				<td><?php echo $categories_dropdown; ?></td>
			</tr>

			<?php if(!$hide_default_styles) : ?>
			<tr valign="top">
				<th scope="row"><?php _e('Enable default styles', WP_CLANWARS_TEXTDOMAIN); ?></th>
				<td><input type="checkbox" name="enable_default_styles" value="true"<?php checked($enable_default_styles, true); ?> /></td>
			</tr>
			<?php endif; ?>

		 </table>

		<p class="submit">
			<input type="submit" class="button button-primary" value="<?php _e('Save Changes', WP_CLANWARS_TEXTDOMAIN); ?>" />
		</p>

	</form>

	<!-- User access -->
	<h2><?php _e('User Access', WP_CLANWARS_TEXTDOMAIN); ?></h2>

	<div id="col-container">

		<div id="col-right">
			<div class="col-wrap">

			<form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
				<?php wp_nonce_field('wp-clanwars-deleteacl'); ?>
				<input type="hidden" name="action" value="wp-clanwars-deleteacl" />

				<div class="tablenav">
					<div class="alignleft actions">
					<select name="do_action">
						<option value="" selected="selected"><?php _e('Actions', WP_CLANWARS_TEXTDOMAIN); ?></option>
						<option value="delete"><?php _e('Delete', WP_CLANWARS_TEXTDOMAIN); ?></option>
					</select>
					<input value="<?php _e('Apply'); ?>" class="button button-secondary action" type="submit" />
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

						<!-- .no-items  -->
						<?php if(empty($user_acl_info)) : ?>

						<tr class="no-items">
							<td class="colspanchange" colspan="3"><?php _e('No items found.', WP_CLANWARS_TEXTDOMAIN); ?></td>
						</tr>

						<?php endif; ?>

						<!-- body -->
						<?php foreach($user_acl_info as $index => $item) : ?>

						<tr<?php if($index % 2 == 0) : ?> class="alternate"<?php endif; ?>>
							<th class="check-column"><input type="checkbox" class="check" name="users[]" value="<?php echo $item->user->ID; ?>" /></th>
							<td><?php esc_html_e($item->user->user_login); ?></td>
							<td>
								<?php foreach($item->user_acl['permissions'] as $name => $is_allowed) : ?>
								<ul>
									<li><?php echo $acl_keys[$name]; ?>: <?php echo ($is_allowed) ? __('Yes', WP_CLANWARS_TEXTDOMAIN) : __('No', WP_CLANWARS_TEXTDOMAIN); ?></li>
								</ul>
								<?php endforeach; ?>

								<?php
									if($item->allowed_games == 'all') {
										_e('All', WP_CLANWARS_TEXTDOMAIN);
									}
								?>

								<?php foreach($item->user_games as $game) : ?>

								<?php if($game->icon_url !== false) : ?>
									<img src="<?php esc_attr_e($game->icon_url); ?>" alt="<?php esc_attr_e($game->title); ?>" class="icon" />
								<?php else : ?>
									<?php esc_html_e(empty($game->abbr) ? $game->title : $game->abbr); ?>
								<?php endif; ?>

								<?php endforeach; ?>
							</td>
						</tr>

						<?php endforeach; ?>
					</tbody>
				</table>

				<div class="tablenav">
					<div class="alignleft actions">
					<select name="do_action2">
						<option value="" selected="selected"><?php _e('Actions', WP_CLANWARS_TEXTDOMAIN); ?></option>
						<option value="delete"><?php _e('Delete', WP_CLANWARS_TEXTDOMAIN); ?></option>
					</select>
					<input value="<?php _e('Apply'); ?>" class="button button-secondary action" type="submit" />
					</div>
					<br class="clear" />
				</div>

			</form>

			</div> <!-- .col-wrap -->
		</div><!-- .col-right -->

		<div id="col-left">
			<div class="col-wrap">

			<!-- Add new user -->

			<h3><?php _e('Add New User', WP_CLANWARS_TEXTDOMAIN); ?></h3>

			<form class="form-wrap" method="post" action="<?php echo admin_url('admin-post.php'); ?>">
				<?php wp_nonce_field('wp-clanwars-acl'); ?>
				<input type="hidden" name="action" value="wp-clanwars-acl" />

				<div class="form-field">
					<label for="user"><?php _e('User', WP_CLANWARS_TEXTDOMAIN); ?></label>
					<?php wp_dropdown_users('name=user'); ?>
				</div>

				<div class="form-field">
					<label><?php _e('Allow user manage specified games only:', WP_CLANWARS_TEXTDOMAIN); ?></label>
					<ul>
						<?php foreach($games as $game) : ?>
						<li><label for="game_<?php esc_attr_e($game->id); ?>"><input type="checkbox" name="games[]" id="game_<?php esc_attr_e($game->id); ?>" value="<?php esc_attr_e($game->id); ?>" /> <?php esc_html_e($game->title); ?></label></li>
						<?php endforeach; ?>
					</ul>
					<p class="description"><?php _e('User can create new games <strong>only if &ldquo;All&rdquo; option is checked.</strong>', WP_CLANWARS_TEXTDOMAIN); ?></p>
				</div>

				<div class="form-field">
					<label><?php _e('Allow user:', WP_CLANWARS_TEXTDOMAIN); ?></label>
					<ul>
						<?php foreach($acl_keys as $key => $title) : ?>

						<li>
							<label for="<?php echo esc_attr($key); ?>"><input type="checkbox" class="check" name="permissions[<?php esc_attr_e($key); ?>]" value="1" id="<?php esc_attr_e($key); ?>" /> <?php echo $title; ?></label>
						</li>

						<?php endforeach; ?>
					</ul>
				</div>

				<input type="submit" class="button button-primary" value="<?php _e('Add User', WP_CLANWARS_TEXTDOMAIN); ?>" />
			</form>

			</div><!-- .col-wrap -->
		</div><!-- .col-left -->

	</div><!-- .col-container -->

</div><!-- .wrap -->