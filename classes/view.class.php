<?php

namespace WP_Clanwars;

class View {

	static function render($view_name, $context = array()) {
		if(is_array($context)) {
			extract($context, EXTR_SKIP);
		}

		$view_path = realpath(dirname(__FILE__) . '/../views/' . $view_name . '.php');

		ob_start();
		include($view_path);
		return ob_get_clean();
	}

};

?>