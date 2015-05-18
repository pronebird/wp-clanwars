<div class="wrap wp-clanwars-import-page">
	<div id="icon-tools" class="icon32"><br></div>
	<h2><?php _e('Import games', WP_CLANWARS_TEXTDOMAIN); ?></h2>
	<!-- <p><?php _e('Import may take some time. Please do not refresh browser when in progress.', WP_CLANWARS_TEXTDOMAIN); ?></p> -->

	<div class="wp-filter">
		<ul class="filter-links">
			<li>
				<a href="<?php echo admin_url('admin.php?page=wp-clanwars-import'); ?>" class="current"><?php _e('Popular', WP_CLANWARS_TEXTDOMAIN); ?></a>
			</li>
		</ul>
		<form class="search-form">
			<input type="search" name="q" value="" class="wp-filter-search" placeholder="<?php esc_attr_e(__('Search Gamepacks', WP_CLANWARS_TEXTDOMAIN)); ?>"
		</form>
	</div>

	<ul class="wp-clanwars-gamepacks clearfix">

	<?php foreach($popular as $game) : ?>
		<li class="item">
			<div class="top">
				<div class="column-install"><a href="#" class="button">Install</a></div>
				<h4>
					<img src="<?php esc_attr_e($game['iconUrl']); ?>" alt="<?php esc_attr_e($game['title']); ?>" class="icon" />
					<span class="game-title"><?php esc_html_e($game['title']); ?></span>
				</h4>
				
				<ul class="maps">
				<?php foreach($game['maps'] as $map) : ?>
					<li>
						<img class="screenshot" src="<?php esc_attr_e($map['imageUrl']); ?>" alt="<?php esc_attr_e($map['title']); ?>" />
						<div class="title"><?php esc_html_e($map['title']); ?></div>
					</li>
				<?php endforeach; ?>
				</ul>
			</div>
			<div class="bottom">
				<div class="column-rating">
					<div class="star-rating">
						<div class="star star-full"></div>
						<div class="star star-full"></div>
						<div class="star star-full"></div>
						<div class="star star-full"></div>
						<div class="star star-empty"></div>
					</div>
					<span class="num-ratings"><?php echo sprintf( _x('(%d)', 'Number of ratings', WP_CLANWARS_TEXTDOMAIN), 599+$game['votes'] ); ?></span>
				</div>
				<div class="column-published">
					<strong><?php _e('Published:', WP_CLANWARS_TEXTDOMAIN); ?></strong>
					<span><?php esc_html_e( mysql2date(get_option('date_format'), $game['date'], true) ); ?></span>
				</div>
				<div class="column-downloaded"><?php echo sprintf( _nx('%d install', '%d installs', $game['downloads'], 'Number of downloads', WP_CLANWARS_TEXTDOMAIN ), 1000+$game['downloads'] ); ?></div>
				<div class="column-author">
					<strong><?php _e('Author:', WP_CLANWARS_TEXTDOMAIN); ?></strong> <?php esc_html_e($game['author']); ?>
				</div>
			</div>
		</li>
	<?php endforeach; ?>

	</ul>

	<form id="wp-cw-import" method="post" action="admin-post.php" enctype="multipart/form-data">

		<input type="hidden" name="action" value="wp-clanwars-import" />
		<?php wp_nonce_field('wp-clanwars-import'); ?>

		<?php if(!empty($import_list)) : ?>

		<fieldset>
			<p><label for="available"><input type="radio" name="import" id="available" value="available" checked="checked" /> <?php _e('Import available games', WP_CLANWARS_TEXTDOMAIN); ?></label></p>

			<ul class="available-games">

			<?php foreach($import_list as $index => $game) : ?>

				<li>
					<label for="game-<?php esc_attr_e($index); ?>">
						<input type="checkbox" name="items[]" id="game-<?php esc_attr_e($index); ?>" value="<?php esc_attr_e($index); ?>" /> <img src="<?php esc_attr_e(trailingslashit(WP_CLANWARS_IMPORTURL) . $game->icon); ?>" alt="<?php esc_attr_e($game->title); ?>" /> <?php esc_html_e($game->title); ?>
						<?php if($game->is_installed) : ?>
						<span class="description"><?php _e('installed', WP_CLANWARS_TEXTDOMAIN); ?></span>
						<?php endif; ?>
					</label>
				</li>

			<?php endforeach; ?>

			</ul>
		</fieldset>

		<?php endif; ?>

		<fieldset>
			<p><label for="upload"><input type="radio" name="import" id="upload" value="upload" /> <?php _e('Upload previously saved game (ZIP file)', WP_CLANWARS_TEXTDOMAIN); ?></label></p>
			<p><input type="file" name="userfile" /></p>
		</fieldset>

		<p class="submit"><input type="submit" class="button button-primary" value="<?php _e('Import', WP_CLANWARS_TEXTDOMAIN); ?>" /></p>

	</form>

</div>