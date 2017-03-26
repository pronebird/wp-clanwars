<?php
/*
    WP-Clanwars
    (c) 2011 Andrej Mihajlov

    This file is part of WP-Clanwars.

    WP-Clanwars is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    WP-Clanwars is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with WP-Clanwars.  If not, see <http://www.gnu.org/licenses/>.
*/

namespace WP_Clanwars;

class Maps {

	/**
	 * Get a database table
	 * @return String
	 */
	static function table() {
		global $wpdb;
		return $wpdb->prefix . 'cw_maps';
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
 game_id int(10) unsigned NOT NULL,
 title varchar(200) NOT NULL,
 screenshot bigint(20) unsigned DEFAULT NULL,
 PRIMARY KEY  (id),
 KEY game_id (game_id,screenshot)
) $charset_collate;

";

		return trim($schema);
	}

	static function get_map($options)
	{
		global $wpdb;

		extract(\WP_Clanwars\Utils::extract_args($options, array(
			'id' => false,
			'game_id' => false,
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

		$order_query = 'ORDER BY `' . $orderby . '` ' . $order;

		if($id != 'all' && $id !== false) {

			if(!is_array($id))
				$id = array($id);

			$id = array_map('intval', $id);
			$where_query[] = 'id IN (' . implode(', ', $id) . ')';
		}

		if($game_id != 'all' && $game_id !== false) {

			if(!is_array($game_id))
				$game_id = array($game_id);

			$game_id = array_map('intval', $game_id);
			$where_query[] = 'game_id IN (' . implode(', ', $game_id) . ')';
		}

		if($limit > 0) {
			$limit_query = $wpdb->prepare('LIMIT %d, %d', $offset, $limit);
		}

		if(!empty($where_query))
			$where_query = 'WHERE ' . implode(' AND ', $where_query);

		$maps_table = static::table();

$query = <<<SQL

	SELECT *
	FROM `$maps_table` 
	$where_query
	$order_query
	$limit_query

SQL;

		return \WP_Clanwars\DB::get_results( $query );
	}

	static function add_map($options)
	{
		global $wpdb;

		$defaults = array(
			'title' => '',
			'screenshot' => 0,
			'game_id' => 0
		);

		$data = \WP_Clanwars\Utils::extract_args($options, $defaults);

		if($wpdb->insert(self::table(), $data, array('%s', '%d', '%d'))) {
			return $wpdb->insert_id;
		}

		return false;
	}

	static function update_map($id, $options)
	{
		global $wpdb;

		$fields = array('title' => '%s', 'screenshot' => '%d', 'game_id' => '%d');

		$data = wp_parse_args($options, array());

		$update_data = array();
		$update_mask = array();

		foreach($fields as $fld => $mask) {
			if(isset($data[$fld])) {
				$update_data[$fld] = $data[$fld];
				$update_mask[] = $mask;
			}
		}

		$result = $wpdb->update(self::table(), $update_data, array('id' => $id), $update_mask, array('%d'));

		return $result;
	}

	static function delete_map($id)
	{
		global $wpdb;

		if(!is_array($id))
			$id = array($id);

		$id = array_map('intval', $id);

		return $wpdb->query('DELETE FROM `' . self::table() . '` WHERE id IN(' . implode(',', $id) . ')');
	}

	static function delete_map_by_game($id)
	{
		global $wpdb;

		if(!is_array($id))
			$id = array($id);

		$id = array_map('intval', $id);

		return $wpdb->query('DELETE FROM `' . self::table() . '` WHERE game_id IN(' . implode(',', $id) . ')');
	}

};

