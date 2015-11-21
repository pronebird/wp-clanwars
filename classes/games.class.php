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

class Games {

	/**
	 * Get a database table
	 * @return String
	 */
	static function table() {
		static $table = null;
		global $wpdb;

		if($table === null) {
			$table = $wpdb->prefix . 'cw_games';
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
 store_id varchar(64) DEFAULT NULL,
 title varchar(200) NOT NULL,
 abbr varchar(20) DEFAULT NULL,
 icon bigint(20) unsigned DEFAULT NULL,
 PRIMARY KEY  (id),
 KEY store_id (store_id),
 KEY icon (icon),
 KEY title (title),
 KEY abbr (abbr)
) $charset_collate;

";

		return trim($schema);
	}

	static function get_game($options, $count = false) {
		global $wpdb;

		$defaults = array(
			'id' => false,
			'limit' => 0,
			'offset' => 0,
			'orderby' => 'id',
			'order' => 'ASC'
		);

		extract( \WP_Clanwars\Utils::extract_args($options, $defaults) );

		$where_query = '';
		$limit_query = '';
		$order_query = '';

		$order = strtolower($order);
		if($order != 'asc' && $order != 'desc') {
			$order = 'asc';
		}

		$order_query = "ORDER BY `$orderby` $order";

		if($id != 'all' && $id !== false) {
			if(!is_array($id)) {
				$id = array($id);
			}
			$id = array_map('intval', $id);
			$where_query[] = 'id IN (' . implode(', ', $id) . ')';
		}

		if($limit > 0) {
			$limit_query = $wpdb->prepare('LIMIT %d, %d', $offset, $limit);
		}

		if(!empty($where_query)) {
			$where_query = 'WHERE ' . implode(' AND ', $where_query);
		}

		if($count) {
			$rslt = $wpdb->get_row( "SELECT COUNT(id) AS m_count FROM `" . self::table() . "` $where_query" );
			$ret = array('total_items' => 0, 'total_pages' => 1);
			$ret['total_items'] = (int) $rslt->m_count;

			if($limit > 0) {
				$ret['total_pages'] = ceil($ret['total_items'] / $limit);
			}

			return $ret;
		}

		$games_table = static::table();

$query = <<<SQL

	SELECT SQL_CALC_FOUND_ROWS *
	FROM `$games_table` 
	$where_query
	$order_query
	$limit_query

SQL;

		return \WP_Clanwars\DB::get_results( $query );
	}

	static function add_game($options) {
		global $wpdb;

		$defaults = array('title' => '', 'abbr' => '', 'icon' => 0, 'store_id' => '');
		$data = \WP_Clanwars\Utils::extract_args($options, $defaults);

		if( $wpdb->insert( self::table(), $data, array('%s', '%s', '%d', '%s') ) ) {
			return $wpdb->insert_id;
		}

		return false;
	}

	static function update_game($id, $options) {
		global $wpdb;

		$fields = array('title' => '%s', 'abbr' => '%s', 'icon' => '%d');
		$data = wp_parse_args($options, array());

		$update_data = array();
		$update_mask = array();

		foreach($fields as $fld => $mask) {
			if(isset($data[$fld])) {
				$update_data[$fld] = $data[$fld];
				$update_mask[] = $mask;
			}
		}

		return $wpdb->update(self::table(), $update_data, array('id' => $id), $update_mask, array('%d'));
	}

	static function delete_game($id) {
		global $wpdb;
		
		$table = self::table();

		if(!is_array($id)) {
			$id = array($id);
		}

		$id = array_map('intval', $id);

		\WP_Clanwars\Maps::delete_map_by_game($id);
		\WP_Clanwars\Matches::delete_match_by_game($id);

		$id_list = implode(',', $id);
		return $wpdb->query( "DELETE FROM `$table` WHERE id IN($id_list)" );
	}

};


