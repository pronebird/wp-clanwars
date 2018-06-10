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

class View {

    protected $template_file = '';
    protected $helpers = array();

    function __construct($template) {
        $this->template_file = $template;

        // use "use" keyword to pass $this in closures, php < 5.4 compatibility
        $self = $this;

        $this->helpers = array(
            // use "use" keyword to pass $this in closures, php < 5.4 compatibility
            'partial' => function($template, $args = array()) use ($self) {
                $self->partial($template, $args);
            }
        );
    }

    function resolve($template) {
        $viewDir = realpath(dirname(__FILE__) . '/../views');
        $viewTemplate = $viewDir . '/' . $template . '.php';

        return $viewTemplate;
    }

    function add_helper($name, $func) {
        $this->helpers[$name] = function () use ($func) {
            return call_user_func_array($func, func_get_args());
        };
    }

    function partial($template, $args = array()) {
        $partialTemplate = $this->resolve($template);

        if(file_exists($partialTemplate)) {
            // import view variables into scope
            extract(get_object_vars($this), EXTR_SKIP);

            if(is_array($args) && count($args) > 0) {
                extract($args, EXTR_OVERWRITE);
            }

            extract($this->helpers, EXTR_OVERWRITE);

            // include template
            include($partialTemplate);
        } else {
            throw new Exception('Partial "' . $template . '" does not exist.');
        }
    }

    function render($context = array(), $echo = true) {
        if(is_array($context)) {
            extract($context, EXTR_SKIP);
        }

        extract($this->helpers, EXTR_SKIP);

        $viewTemplate = $this->resolve($this->template_file);
        $viewOutput = '';

        if(file_exists($viewTemplate)) {
            ob_start();
            include($viewTemplate);
            $viewOutput = ob_get_clean();
        } else {
            throw new Exception('Template "' . $this->template_file . '" does not exist.');
        }

        if($echo) {
            echo $viewOutput;
            return;
        }

        return $viewOutput;
    }

};
