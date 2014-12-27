<?php

namespace WP_Clanwars;

class Rounds {

	static function table() {
		static $table = null;
		global $wpdb;

		if($table === null) {
			$table = $wpdb->prefix . 'cw_rounds';
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
`match_id` int(10) unsigned NOT NULL,
`group_n` int(10) NOT NULL,
`map_id` int(10) unsigned NOT NULL,
`tickets1` int(10) NOT NULL,
`tickets2` int(10) NOT NULL,
PRIMARY KEY (`id`),
KEY `match_id` (`match_id`),
KEY `group_n` (`group_n`),
KEY `map_id` (`map_id`)
) $charset_collate;

";

		return $schema;
	}

	static function get_rounds($match_id)
	{
		global $wpdb;

		return $wpdb->get_results(
				$wpdb->prepare(
						'SELECT t1.*, t2.title, t2.screenshot FROM `' . self::table() . '` AS t1
						 LEFT JOIN `' . \WP_Clanwars\Maps::table() . '` AS t2
						 ON t2.id = t1.map_id
						 WHERE t1.match_id=%d ORDER BY t1.id ASC, t1.group_n ASC',
						$match_id)
				);
	}

	static function add_round($p)
	{
		global $wpdb;

		$data = \WP_Clanwars\Utils::extract_args($p, array(
					'match_id' => 0,
					'group_n' => 0,
					'map_id' => 0,
					'tickets1' => 0,
					'tickets2' => 0
			));

		if($wpdb->insert(self::table(), $data, array('%d', '%d', '%d', '%d', '%d')))
		{
			$insert_id = $wpdb->insert_id;

			return $insert_id;
		}

		return false;
	}

	static function update_round($id, $p)
	{
		global $wpdb;

		$fields = array(
			'match_id' => '%d',
			'group_n' => '%d',
			'map_id' => '%d',
			'tickets1' => '%d',
			'tickets2' => '%d'
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

		$result = $wpdb->update(self::table(), $update_data, array('id' => $id), $update_mask, array('%d'));

		return $result;
	}

	static function delete_round($id)
	{
		global $wpdb;

		if(!is_array($id))
			$id = array($id);

		$id = array_map('intval', $id);

		return $wpdb->query('DELETE FROM `' . self::table() . '` WHERE id IN(' . implode(',', $id) . ')');
	}

	static function delete_rounds_not_in($match_id, $id)
	{
		global $wpdb;

		if(!is_array($id))
			$id = array($id);

		$id = array_map('intval', $id);

		return $wpdb->query($wpdb->prepare('DELETE FROM `' . self::table() . '` WHERE match_id=%d AND id NOT IN(' . implode(',', $id) . ')', $match_id));
	}

	static function delete_rounds_by_match($match_id)
	{
		global $wpdb;

		if(!is_array($match_id))
			$match_id = array($match_id);

		$match_id = array_map('intval', $match_id);

		return $wpdb->query('DELETE FROM `' . self::table() . '` WHERE match_id IN(' . implode(',', $match_id) . ')');
	}

}