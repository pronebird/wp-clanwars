<?php

namespace WP_Clanwars;

class Maps {

	/**
	 * Get a database table
	 * @return String
	 */
	static function table() {
		static $table = null;
		global $wpdb;

		if($table === null) {
			$table = $wpdb->prefix . 'cw_maps';
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
`game_id` int(10) unsigned NOT NULL,
`title` varchar(200) NOT NULL,
`screenshot` bigint(20) unsigned DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `game_id` (`game_id`,`screenshot`)
) $charset_collate;

";

		return $schema;
	}

	static function get_map($options, $count = false)
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
		$where_conditions = array();

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
			$where_conditions[] = 'id IN (' . implode(', ', $id) . ')';
		}

		if($game_id != 'all' && $game_id !== false) {

			if(!is_array($game_id))
				$game_id = array($game_id);

			$game_id = array_map('intval', $game_id);
			$where_conditions[] = 'game_id IN (' . implode(', ', $game_id) . ')';
		}

		if($limit > 0) {
			$limit_query = $wpdb->prepare('LIMIT %d, %d', $offset, $limit);
		}

		if(!empty($where_conditions)) {
			$where_query = 'WHERE ' . implode(' AND ', $where_conditions);
		}

		if($count) {

			$rslt = $wpdb->get_row('SELECT COUNT(id) AS m_count FROM `' . self::table() . '` ' . $where_query);

			$ret = array('total_items' => 0, 'total_pages' => 1);

			$ret['total_items'] = (int) $rslt->m_count;

			if($limit > 0) {
				$ret['total_pages'] = ceil($ret['total_items'] / $limit);
			}

			return $ret;
		}

		$rslt = $wpdb->get_results('SELECT * FROM `' . self::table() . '` ' . implode(' ', array($where_query, $order_query, $limit_query)));

		return $rslt;
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

