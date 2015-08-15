<?php

namespace WP_Clanwars;

require_once( dirname(__FILE__) . '/dbresult.class.php' );
require_once( dirname(__FILE__) . '/pagination.class.php' );

class DB {

    /**
     * Runs wpdb->get_results() and converts the result into \WP_Clanwars\DBResult object.
     * @param  string $query
     * @param  string $output_type
     * @see wpdb::get_results()
     * @return \WP_Clanwars\DBResult
     */
    static function get_results( $query, $output_type = OBJECT ) {
        global $wpdb;

        $results = $wpdb->get_results( $query, $output_type );
        $pagination = static::_get_pagination();
        $dbresult = new DBResult( $results, $pagination );

        return $dbresult;
    }

    /**
     * Parse out max number of elements to fetch.
     * @param  string $query
     * @return int|boolean max number of elements to fetch or false if not specified.
     */
    private static function _parse_limit_clause( $query ) {
        $match = array();
        if(preg_match("#LIMIT\s+(\d+)(\s*[,]\s*(\d+))?#i", $query, $match)) {
            return (int) array_pop($match);
        }
        return false;
    }

    /**
     * Check if query has SQL_CALC_FOUND_ROWS
     * @param  string  $query
     * @return boolean
     */
    private static function _has_found_rows( $query ) {
        return (stristr($query, 'SQL_CALC_FOUND_ROWS') !== false);
    }

    /**
     * Run FOUND_ROWS query.
     * Use this method after running a query with SQL_CALC_FOUND_ROWS.
     * @return int
     */
    private static function _get_found_rows() {
        global $wpdb;

        return (int) $wpdb->get_var( 'SELECT FOUND_ROWS()' );
    }

    /**
     * Create pagination object for last query.
     * Use this method after running a query with SQL_CALC_FOUND_ROWS.
     * @return \WP_Clanwars\Pagination
     */
    private static function _get_pagination() {
        global $wpdb;

        $query = $wpdb->last_query;

        $found_rows = 0;
        $limit = static::_parse_limit_clause( $query );

        if( static::_has_found_rows( $query ) ) {
            $found_rows = static::_get_found_rows();
        }

        return new \WP_Clanwars\Pagination( $found_rows, $limit );
    }

}