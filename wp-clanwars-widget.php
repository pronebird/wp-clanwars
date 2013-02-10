<?php
/**
 * This is a part of  WP-ClanWars plugin
 * Description: WP-ClanWars Widget
 * Author: Andrej Mihajlov
 **/

/*
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if(!function_exists('add_action')) die('Cheatin&#8217; uh?');

class WP_ClanWars_Widget extends WP_Widget {
	
	var $default_settings = array();
	var $newer_than_options = array();

	function __construct()
	{
		$widget_ops = array('classname' => 'widget_clanwars', 'description' => __('ClanWars widget', WP_CLANWARS_TEXTDOMAIN));
		parent::__construct('clanwars', __('ClanWars', WP_CLANWARS_TEXTDOMAIN), $widget_ops);

		$this->default_settings = array('title' => __('ClanWars', WP_CLANWARS_TEXTDOMAIN),
				'show_limit' => 10,
				'hide_title' => false,
				'hide_older_than' => '1m',
                'visible_games' => array());

		$this->newer_than_options = array(
			'all' => array('title' => __('Show all', WP_CLANWARS_TEXTDOMAIN), 'value' => 0),
			'1w' => array('title' => __('1 week', WP_CLANWARS_TEXTDOMAIN), 'value' => 60*60*24*7),			
			'2w' => array('title' => __('2 weeks', WP_CLANWARS_TEXTDOMAIN), 'value' => 60*60*24*14),
			'3w' => array('title' => __('3 weeks', WP_CLANWARS_TEXTDOMAIN), 'value' => 60*60*24*21),
			'1m' => array('title' => __('1 month', WP_CLANWARS_TEXTDOMAIN), 'value' => 60*60*24*30),
			'2m' => array('title' => __('2 months', WP_CLANWARS_TEXTDOMAIN), 'value' => 60*60*24*30*2),
			'3m' => array('title' => __('3 months', WP_CLANWARS_TEXTDOMAIN), 'value' => 60*60*24*30*3),
			'6m' => array('title' => __('6 months', WP_CLANWARS_TEXTDOMAIN), 'value' => 60*60*24*30*6),
			'1y' => array('title' => __('1 year', WP_CLANWARS_TEXTDOMAIN), 'value' => 60*60*24*30*12)
		);

		wp_register_script('jquery-cookie', WP_CLANWARS_URL . '/js/jquery.cookie.pack.js', array('jquery'), WP_CLANWARS_VERSION);
		wp_register_script('wp-cw-tabs', WP_CLANWARS_URL . '/js/tabs.js', array('jquery', 'jquery-cookie'), WP_CLANWARS_VERSION);

		wp_enqueue_script('wp-cw-tabs');
	}

	function current_time_fixed( $type, $gmt = 0 ) {
		$t =  ( $gmt ) ? gmdate( 'Y-m-d H:i:s' ) : gmdate( 'Y-m-d H:i:s', ( time() + ( get_option( 'gmt_offset' ) * 3600 ) ) );
		switch ( $type ) {
			case 'mysql':
				return $t;
				break;
			case 'timestamp':
				return strtotime($t);
				break;
		}
	}

	function widget($args, $instance) {
		global $wpClanWars;

		extract($args);

		$now = $this->current_time_fixed('timestamp');

		$instance = wp_parse_args((array)$instance, $this->default_settings);

		$title = apply_filters('widget_title', empty($instance['title']) ? __('ClanWars', WP_CLANWARS_TEXTDOMAIN) : $instance['title']);

		$matches = array();
		$games = array();
		$_games = $wpClanWars->get_game(array(
                'id' => empty($instance['visible_games']) ? 'all' : $instance['visible_games'],
                'orderby' => 'title',
                'order' => 'asc'
            ));
		
		$from_date = 0;
		if(isset($this->newer_than_options[$instance['hide_older_than']])) {
			$age = (int)$this->newer_than_options[$instance['hide_older_than']]['value'];
			// 0 means show all matches
			if($age > 0)
				$from_date = $now - $age;
		}
			
		foreach($_games as $g) {
			$m = $wpClanWars->get_match(array('from_date' => $from_date, 'game_id' => $g->id, 'limit' => $instance['show_limit'], 'order' => 'desc', 'orderby' => 'date', 'sum_tickets' => true));
			
			if(sizeof($m)) {
				$games[] = $g;
				$matches = array_merge($matches, $m);
			}
		}

		usort($matches, create_function('$a, $b', '
			$t1 = mysql2date("U", $a->date);
			$t2 = mysql2date("U", $b->date);

			if($t1 == $t2) return 0;

			return $t1 > $t2 ? -1 : 1;
			'));

		?>

		<?php echo $before_widget; ?>
		<?php if ( $title && !$instance['hide_title'] )
			echo $before_title . $title . $after_title; ?>

<ul class="clanwar-list">

	<li>
		<ul class="tabs">
		<?php
		$obj = new stdClass();
		$obj->id = 0;
		$obj->title = __('All', WP_CLANWARS_TEXTDOMAIN);
		$obj->abbr = __('All');
		$obj->icon = 0;

		array_unshift($games, $obj);

		for($i = 0; $i < sizeof($games); $i++) :
			$game = $games[$i];
			$link = ($game->id == 0) ? 'all' : 'game-' . $game->id;
		?>
			<li<?php if($i == 0) echo ' class="selected"'; ?>><a href="#<?php echo $link; ?>" title="<?php echo esc_attr($game->title); ?>"><?php echo esc_html($game->abbr); ?></a></li>
		<?php endfor; ?>
		</ul>
	</li>

	<?php foreach($matches as $i => $match) :
			$is_upcoming = false;
			$t1 = $match->team1_tickets;
			$t2 = $match->team2_tickets;
			$wld_class = $t1 == $t2 ? 'draw' : ($t1 > $t2 ? 'win' : 'loose');
			$date = mysql2date(get_option('date_format') . ', ' . get_option('time_format'), $match->date);
			$timestamp = mysql2date('U', $match->date);

			$game_icon = wp_get_attachment_url($match->game_icon);

			$is_upcoming = $timestamp > $now;
			$is_playing = ($now > $timestamp && $now < $timestamp + 3600) && ($t1 == 0 && $t2 == 0);
	?>
	<li class="clanwar-item<?php if($i % 2 != 0) echo ' alt'; ?> game-<?php echo $match->game_id; ?>">

		<?php if($game_icon !== false) : ?>
		<img src="<?php echo $game_icon; ?>" alt="<?php echo esc_attr($match->game_title); ?>" class="icon" />
		<?php endif; ?>
		
		<div class="wrap">
			<?php if($is_upcoming) : ?>
			<div class="upcoming"><?php _e('Upcoming', WP_CLANWARS_TEXTDOMAIN); ?></div>
			<?php elseif($is_playing) : ?>
			<div class="playing"><?php _e('Playing', WP_CLANWARS_TEXTDOMAIN); ?></div>
			<?php else : ?>
			<div class="scores <?php echo $wld_class; ?>"><?php echo sprintf(__('%d:%d', WP_CLANWARS_TEXTDOMAIN), $t1, $t2); ?></div>
			<?php endif; ?>

			<div class="opponent-team"><?php echo $wpClanWars->get_country_flag($match->team2_country, true); ?>
			<?php 
				$team2_title = esc_html($match->team2_title);

				if($match->post_id != 0)
					$team2_title = '<a href="' . get_permalink($match->post_id) . '" title="' . esc_attr($match->title) . '">' . $team2_title . '</a>';

				echo $team2_title;
			?>
			</div>
			<div class="home-team"><?php echo esc_html($match->team1_title); ?></div>
			<div class="date"><?php echo $date; ?></div>
		</div>

	</li>
		<?php endforeach; ?>
</ul>

			<?php echo $after_widget; ?>

		<?php
	}

	function update($new_instance, $old_instance) {
		return $new_instance;
	}

	function form($instance) {
        global $wpClanWars;
        
		$instance = wp_parse_args((array)$instance, $this->default_settings);

		$show_limit = (int)$instance['show_limit'];
		$title = esc_attr($instance['title']);
        $visible_games = $instance['visible_games'];

        $games = $wpClanWars->get_game('id=all&orderby=title&order=asc');

		if(!isset($this->show_last_array[$show]))
			$show = 0;
	?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', WP_CLANWARS_TEXTDOMAIN); ?></label> <input class="widefat" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" value="<?php echo esc_attr($title); ?>" type="text" /></p>

		<p>
			<input class="checkbox" name="<?php echo $this->get_field_name('hide_title'); ?>" id="<?php echo $this->get_field_id('hide_title'); ?>" value="1" type="checkbox" <?php checked($instance['hide_title'], true)?>/> <label for="<?php echo $this->get_field_id('hide_title'); ?>"><?php _e('Hide title', WP_CLANWARS_TEXTDOMAIN); ?></label>
		</p>

        
        <p><?php _e('Show games:', WP_CLANWARS_TEXTDOMAIN); ?></p>
        <p style="overflow: auto; max-height: 100px; background: #fff;" class="widefat">
            <?php foreach($games as $item) : ?>
            <label for="<?php echo $this->get_field_id('visible_games-' . $item->id); ?>"><input type="checkbox" name="<?php echo $this->get_field_name('visible_games'); ?>[]" id="<?php echo $this->get_field_id('visible_games-' . $item->id); ?>" value="<?php echo esc_attr($item->id); ?>" <?php checked(true, in_array($item->id, $visible_games)); ?>/> <?php echo esc_html($item->title); ?></label><br/>
            <?php endforeach; ?>
        </p>
        <p class="description"><?php _e('Do not check any game if you want to show all games.', WP_CLANWARS_TEXTDOMAIN); ?></p>

		<p><label for="<?php echo $this->get_field_id('show_limit'); ?>"><?php _e('Show matches:', WP_CLANWARS_TEXTDOMAIN); ?></label> <input style="width: 45px;" name="<?php echo $this->get_field_name('show_limit'); ?>" id="<?php echo $this->get_field_id('show_limit'); ?>" value="<?php echo esc_attr($show_limit); ?>" type="text" /></p>
		<p><label for="<?php echo $this->get_field_id('hide_older_than'); ?>"><?php _e('Hide older than', WP_CLANWARS_TEXTDOMAIN); ?></label><br/><select name="<?php echo $this->get_field_name('hide_older_than'); ?>" id="<?php echo $this->get_field_id('hide_older_than'); ?>">
			<?php foreach($this->newer_than_options as $key => $option) : ?>
				<option value="<?php esc_attr_e($key); ?>"<?php selected($key, $instance['hide_older_than']); ?>><?php esc_html_e($option['title']); ?></option>
			<?php endforeach; ?>
		</select></p>
	<?php
	}
}

?>