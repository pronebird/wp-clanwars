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

require_once( dirname(__FILE__) . '/pagination.class.php' );

class DBResult extends \ArrayObject {

    private $_pagination = null;

    function __construct($results, $pagination = null) {
        parent::__construct($results);

        if( $pagination instanceof \WP_Clanwars\Pagination ) {
            $this->_pagination = $pagination;
        }
    }

    public function get_pagination() {
        return $this->_pagination;
    }

}