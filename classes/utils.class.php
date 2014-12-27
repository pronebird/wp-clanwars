<?php

namespace WP_Clanwars;

class Utils {

	/**
	 * Parse arguments and restrict a list of values to keys defined in defaults
	 *
	 * @param array|string $args Input values
	 * @param array $defaults Array of default values
	 * @return array Merged array. Same behaviour as wp_parse_args except it generates array which only consists of keys from $defaults array
	 */
	static function extract_args($args, $defaults) {
		$options = wp_parse_args($args, $defaults);
		$result = array();

		if(is_array($defaults)) {
			foreach(array_keys($defaults) as $key) {
				$result[$key] = $options[$key];
			}
		}

		return $result;
	}

};

