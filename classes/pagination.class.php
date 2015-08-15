<?php

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