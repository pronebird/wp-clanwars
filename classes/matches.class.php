<?php

namespace WP_Clanwars;

class Matches {

	static function table() {
		static $table = null;
		global $wpdb;

		if($table === null) {
			$table = $wpdb->prefix . 'cw_matches';
		}

		return $table;
	}

	static function schema() {
		global $wpdb;

		$table = self::table();
		$charset_collate = $wpdb->get_charset_collate();

		$schema = "

CREATE TABLE `$table` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`title` varchar(200) DEFAULT NULL,
`date` datetime NOT NULL,
`post_id` bigint(20) unsigned DEFAULT NULL,
`team1` int(10) unsigned NOT NULL,
`team2` int(10) unsigned NOT NULL,
`game_id` int(10) unsigned NOT NULL,
`match_status` tinyint(1) DEFAULT '0',
`description` text,
`external_url` varchar(200) DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `post_id` (`post_id`),
KEY `post_title` (`title`),
KEY `game_id` (`game_id`),
KEY `team1` (`team1`),
KEY `team2` (`team2`),
KEY `match_status` (`match_status`),
KEY `date` (`date`)
) $charset_collate;

";

		return $schema;
	}

	static function get_match($p, $count = false)
	{
		global $wpdb;

		extract(\WP_Clanwars\Utils::extract_args($p, array(
			'from_date' => 0,
			'id' => false,
			'game_id' => false,
			'sum_tickets' => false,
			'limit' => 0,
			'offset' => 0,
			'orderby' => 'id',
			'order' => 'ASC')));

		$where_query = '';
		$limit_query = '';
		$order_query = '';

		$order = strtolower($order);
		if($order != 'asc' && $order != 'desc')
			$order = 'asc';

		$order_query = 'ORDER BY t1.`' . $orderby . '` ' . $order;

		if($id != 'all' && $id !== false) {

			if(!is_array($id))
				$id = array($id);

			$id = array_map('intval', $id);
			$where_query[] = 't1.id IN (' . implode(', ', $id) . ')';
		}

		if($game_id != 'all' && $game_id !== false) {

			if(!is_array($game_id))
				$game_id = array($game_id);

			$game_id = array_map('intval', $game_id);
			$where_query[] = 't1.game_id IN (' . implode(', ', $game_id) . ')';
		}

		if($from_date > 0) {
			$where_query[] = 't1.date >= FROM_UNIXTIME(' . intval($from_date) . ')';
		}

		if($limit > 0) {
			$limit_query = $wpdb->prepare('LIMIT %d, %d', $offset, $limit);
		}

		if(!empty($where_query)) {
			$where_query = 'WHERE ' . implode(' AND ', $where_query);
		}

		if($count) {
			$rslt = $wpdb->get_row('SELECT COUNT(id) AS m_count FROM `' . self::table() . '` AS t1 ' . $where_query);
			$ret = array('total_items' => 0, 'total_pages' => 1);
			$ret['total_items'] = (int) $rslt->m_count;

			if($limit > 0) {
				$ret['total_pages'] = ceil($ret['total_items'] / $limit);
			}

			return $ret;
		}

		if($sum_tickets) {
			$rslt = $wpdb->get_results(
					'SELECT t1.*, t2.title AS game_title, t2.abbr AS game_abbr, t2.icon AS game_icon,
							tt1.title AS team1_title, tt2.title AS team2_title,
							tt1.country AS team1_country, tt2.country AS team2_country,
							(SELECT SUM(sumt1.tickets1) FROM `' . \WP_Clanwars\Rounds::table() . '` AS sumt1 WHERE sumt1.match_id = t1.id) AS team1_tickets,
							(SELECT SUM(sumt2.tickets2) FROM `' . \WP_Clanwars\Rounds::table() . '` AS sumt2 WHERE sumt2.match_id = t1.id) AS team2_tickets

					 FROM `' . self::table() . '` AS t1
					 LEFT JOIN `' . \WP_Clanwars\Games::table() . '` AS t2 ON t1.game_id=t2.id
					 LEFT JOIN `' . \WP_Clanwars\Teams::table() . '` AS tt1 ON t1.team1=tt1.id
					 LEFT JOIN `' . \WP_Clanwars\Teams::table() . '` AS tt2 ON t1.team2=tt2.id ' .
					 implode(' ', array($where_query, $order_query, $limit_query)));

		} else {
			$rslt = $wpdb->get_results(
					'SELECT t1.*, t2.title AS game_title, t2.abbr AS game_abbr, t2.icon AS game_icon,
							tt1.title AS team1_title, tt2.title AS team2_title,
							tt1.country AS team1_country, tt2.country AS team2_country
					 FROM `' . self::table() . '` AS t1
					 LEFT JOIN `' . \WP_Clanwars\Games::table() . '` AS t2 ON t1.game_id=t2.id
					 LEFT JOIN `' . \WP_Clanwars\Teams::table() . '` AS tt1 ON t1.team1=tt1.id
					 LEFT JOIN `' . \WP_Clanwars\Teams::table() . '` AS tt2 ON t1.team2=tt2.id ' .
					 implode(' ', array($where_query, $order_query, $limit_query)));
		}

		return $rslt;
	}

	static function update_match_post($match_id) {
		global $wpdb;

		// Get match by ID
		$matches = self::get_match(array(
			'id' => $match_id,
			'sum_tickets' => true
		));

		if(empty($matches)) {
			return false;
		}

		$match = $matches[0];

		// Get post category
		$post_category = get_option(WP_CLANWARS_CATEGORY, -1);

		// New post data
		$postarr = array(
			'post_status' => 'publish',
			'post_excerpt' => '',
			'post_title' => ''
		);

		if($post_category !== -1) {
			$postarr['post_category'] = array( (int)$post_category );
		}

		$post = get_post($match->post_id);

		if(!is_null($post)) {
			$postarr['ID'] = $post->ID;
		}

		$post_title = $match->title;
		if(empty($post_title)) {
			$post_title = sprintf(__('%s vs. %s', WP_CLANWARS_TEXTDOMAIN), $match->team1_title, $match->team2_title);
		}

		$post_excerpt = sprintf(_x('%s vs. %s', 'match_excerpt', WP_CLANWARS_TEXTDOMAIN), $match->team1_title, $match->team2_title) . "\n";
		$post_excerpt .= sprintf(__('%d:%d'), $match->team1_tickets, $match->team2_tickets) . "\n";

		if(!empty($match->description)) {
			$description = nl2br(esc_html($match->description));
			$description = make_clickable($description);
			$description = wptexturize($description);
			$description = convert_smilies($description);

			// add target=_blank to all links
			$description = preg_replace('#(<a.*?)(>.*?</a>)#i', '$1 target="_blank"$2', $description);

			$post_excerpt .= $description . "\n";
		}

		$postarr['post_title'] = $post_title;
		$postarr['post_excerpt'] = wp_trim_excerpt($post_excerpt);

		if(!isset($postarr['ID'])) {
			// generate shortcode only when creating post
			$postarr['post_content'] = '[wp-clanwars match_id="' . $match->id . '"]';

			$new_post_ID = wp_insert_post($postarr);
		} else {
			$new_post_ID = wp_update_post($postarr);
		}

		$result = $wpdb->update(self::table(), array('post_id' => $new_post_ID), array('id' => $match_id), array('%d'), array('%d'));

		return $new_post_ID;
	}

	static function add_match($p)
	{
		global $wpdb;

		$data = \WP_Clanwars\Utils::extract_args($p, array(
					'title' => '',
					'date' => \WP_Clanwars\Utils::current_time_fixed('timestamp', 0),
					'post_id' => 0,
					'team1' => 0,
					'team2' => 0,
					'game_id' => 0,
					'match_status' => 0,
					'description' => ''
			));

		if($wpdb->insert(self::table(), $data, array('%s', '%s', '%d', '%d', '%d', '%d', '%s')))
		{
			$insert_id = $wpdb->insert_id;

			return $insert_id;
		}

		return false;
	}

	static function update_match($id, $p)
	{
		global $wpdb;

		$fields = array(
			'title' => '%s',
			'date' => '%s',
			'post_id' => '%d',
			'team1' => '%d',
			'team2' => '%d',
			'game_id' => '%d',
			'match_status' => '%d',
			'description' => '%s',
			'external_url' => '%s'
		);

		$data = wp_parse_args($p, array());

		$update_data = array();
		$update_mask = array();

		foreach($fields as $fld => $mask) {
			if(isset($data[$fld])) {
				$update_data[$fld] = $data[$fld];
				$update_mask[] = $mask;
			}
		}

		// filter external_url field
		if(isset($update_data['external_url'])) {
			$update_data['external_url'] = esc_url_raw($update_data['external_url']);
		}

		$result = $wpdb->update(self::table(), $update_data, array('id' => $id), $update_mask, array('%d'));

		return $result;
	}

	// @TODO: remove post
	static function delete_match($id)
	{
		global $wpdb;

		if(!is_array($id))
			$id = array($id);

		$id = array_map('intval', $id);

		\WP_Clanwars\Rounds::delete_rounds_by_match($id);

		return $wpdb->query('DELETE FROM `' . self::table() . '` WHERE id IN(' . implode(',', $id) . ')');
	}

	static function delete_match_by_team($id) {
		global $wpdb;

		if(!is_array($id))
			$id = array($id);

		$id = array_map('intval', $id);
		$id_list = implode(',', $id);

		return $wpdb->query('DELETE FROM `' . self::table() . '` WHERE team1 IN(' . $id_list . ') OR team2 IN(' . $id_list . ')');
	}

	static function delete_match_by_game($id) {
		global $wpdb;

		if(!is_array($id))
			$id = array($id);

		$id = array_map('intval', $id);

		return $wpdb->query('DELETE FROM `' . self::table() . '` WHERE game_id IN(' . implode(',', $id) . ')');
	
	}

}
