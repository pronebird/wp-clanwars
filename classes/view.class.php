<?php

namespace WP_Clanwars;

class View {

	protected $template_path = '';
	protected $helpers = array();

	function __construct($name) {
		$this->template_path = realpath(dirname(__FILE__) . '/../views/' . $name . '.php');
	}

	function add_helper($name, $func) {
		$this->helpers[$name] = function () use ($func) {
			return call_user_func_array($func, func_get_args());
		};
	}

	function render($context = array(), $echo = true) {
		extract($this->helpers, EXTR_SKIP);

		if(is_array($context)) {
			extract($context, EXTR_SKIP);
		}

		ob_start();
		include($this->template_path);
		$output = ob_get_clean();

		if($echo) {
			echo $output;
			return;
		}

		return $output;
	}

};

?>