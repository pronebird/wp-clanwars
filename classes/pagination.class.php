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

class Pagination {

    private $_num_rows;
    private $_num_pages;
    private $_limit;

    function __construct($num_rows, $limit) {
        $limit = (int) $limit;
        $num_rows = (int) $num_rows;
        $num_pages = 1;

        if( $limit < 0) { $limit = 0; }
        if( $num_rows < 0 ) { $num_rows = 0; }

        if( $limit > 0 ) {
            $num_pages = ceil( $num_rows / $limit );
        }

        $this->_num_rows = $num_rows;
        $this->_num_pages = $num_pages;
        $this->_limit = $limit;
    }

    public function get_num_rows() {
        return $this->_num_rows;
    }

    public function get_num_pages() {
        return $this->_num_pages;
    }

    public function get_limit() {
        return $this->_limit;
    }

}