<?php

namespace WP_Clanwars;

/**
 * Games class.
 */
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

CREATE TABLE `$table` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`title` varchar(200) NOT NULL,
`abbr` varchar(20) DEFAULT NULL,
`icon` bigint(20) unsigned DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `icon` (`icon`),
KEY `title` (`title`),
KEY `abbr` (`abbr`)
) $charset_collate;

";

		return $schema;
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

		$rslt = $wpdb->get_results( "SELECT * FROM `" . self::table() . "` $where_query $order_query $limit_query" );

		return $rslt;
	}

	static function add_game($options) {
		global $wpdb;

		$defaults = array('title' => '', 'abbr' => '', 'icon' => 0);
		$data = \WP_Clanwars\Utils::extract_args($options, $defaults);

		if($wpdb->insert(self::table(), $data, array('%s', '%s', '%d'))) {
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


