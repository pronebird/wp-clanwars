<?php

namespace WP_Clanwars;

require_once( dirname(__FILE__) . '/db.class.php' );

class Teams {

	/**
	 * Get a database table
	 * @return String
	 */
	static function table() {
		static $table = null;
		global $wpdb;

		if($table === null) {
			$table = $wpdb->prefix . 'cw_teams';
		}

		return $table;
	}

	/**
	 * Get a database schema SQL
	 * @return String
	 */
	static function schema() {
		global $wpdb;

		$table = self::table();
		$charset_collate = $wpdb->get_charset_collate();

		$schema = "

CREATE TABLE $table (
 id int(10) unsigned NOT NULL AUTO_INCREMENT,
 title varchar(200) NOT NULL,
 logo bigint(20) unsigned DEFAULT NULL,
 country varchar(20) DEFAULT NULL,
 home_team tinyint(1) DEFAULT '0',
 PRIMARY KEY  (id),
 KEY country (country),
 KEY home_team (home_team),
 KEY title (title)
) $charset_collate;

";

		return trim($schema);
	}

	static function get_team( $args )
	{
		global $wpdb;

		$defaults = array(
			'id' => false,
			'title' => false,
			'limit' => 0,
			'offset' => 0,
			'orderby' => 'id',
			'order' => 'ASC'
		);
		$args = \WP_Clanwars\Utils::extract_args($args, $defaults);
		extract($args);

		$where_query = '';
		$limit_query = '';
		$order_query = '';

		$order = strtolower($order);
		if($order != 'asc' && $order != 'desc') {
			$order = 'asc';
		}

		$order_query = 'ORDER BY `' . $orderby . '` ' . $order;

		if($id !== 'all' && $id !== false) {
			if(!is_array($id)) {
				$id = array($id);
			}

			$id = array_map('intval', $id);
			$where_query[] = 'id IN (' . implode(', ', $id) . ')';
		}

		if($title !== false) {
			$where_query[] = $wpdb->prepare('title=%s', $title);
		}

		if($limit > 0) {
			$limit_query = $wpdb->prepare('LIMIT %d, %d', $offset, $limit);
		}

		if(!empty($where_query)) {
			$where_query = 'WHERE ' . implode(' AND ', $where_query);
		}

		return \WP_Clanwars\DB::get_results('SELECT SQL_CALC_FOUND_ROWS * FROM `' . self::table() . '` ' . implode(' ', array($where_query, $order_query, $limit_query)));
	}

	static function add_team( $args )
	{
		global $wpdb;

		$defaults = array(
			'title' => '',
			'logo' => 0,
			'country' => '',
			'home_team' => 0
		);

		$data = \WP_Clanwars\Utils::extract_args($args, $defaults);

		if($wpdb->insert(self::table(), $data, array('%s', '%d', '%s', '%d')))
		{
			$insert_id = $wpdb->insert_id;

			if($data['home_team']) {
				self::set_hometeam($insert_id);
			}
			return $insert_id;
		}

		return false;
	}

	static function set_hometeam($id) {
		global $wpdb;

		$wpdb->update(self::table(), array('home_team' => 0), array('home_team' => 1), array('%d'), array('%d'));
		return $wpdb->update(self::table(), array('home_team' => 1), array('id' => $id), array('%d'), array('%d'));
	}

	static function get_hometeam() {
		global $wpdb;

		return $wpdb->get_row("SELECT * FROM `" . self::table() . "` WHERE home_team = 1");
	}

	static function update_team($id, $args)
	{
		global $wpdb;

		$fields = array('title' => '%s', 'country' => '%s', 'home_team' => '%d', 'logo' => '%d');

		$data = wp_parse_args($args, array());

		$update_data = array();
		$update_mask = array();

		foreach($fields as $fld => $mask) {
			if(isset($data[$fld])) {
				$update_data[$fld] = $data[$fld];
				$update_mask[] = $mask;
			}
		}

		$result = $wpdb->update(self::table(), $update_data, array('id' => $id), $update_mask, array('%d'));

		if(isset($update_data['home_team'])) {
			self::set_hometeam($id);
		}

		return $result;
	}

	static function delete_team($id) 
	{
		global $wpdb;
		
		if(!is_array($id))
			$id = array($id);
		
		$id = array_map('intval', $id);

		// delete matches belongs to this team
		\WP_Clanwars\Matches::delete_match_by_team($id);
		
		return $wpdb->query('DELETE FROM `' . self::table() . '` WHERE id IN(' . implode(',', $id) . ')');
	}

	static function most_popular_countries()
	{
		global $wpdb;

		static $cache = false;

		$limit = 10;

		if($cache !== false) {
			return $cache;
		}

		$teams_table = \WP_Clanwars\Teams::table();
		$matches_table = \WP_Clanwars\Matches::table();

		$query = $wpdb->prepare(
			"(SELECT t1.country, COUNT(t2.id) AS cnt
			FROM $teams_table AS t1, $matches_table AS t2
			WHERE t1.id = t2.team1
			GROUP BY t1.country
			LIMIT %d)
			UNION
			(SELECT t1.country, COUNT(t2.id) AS cnt
			FROM $teams_table AS t1, $matches_table AS t2
			WHERE t1.id = t2.team2
			GROUP BY t1.country
			LIMIT %d)
			ORDER BY cnt DESC
			LIMIT %d", $limit, $limit, $limit
		);

		$cache = $wpdb->get_results( $query, ARRAY_A );

		return $cache;
	}

};