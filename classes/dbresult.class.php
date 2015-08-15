<?php

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