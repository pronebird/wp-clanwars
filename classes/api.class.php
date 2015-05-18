<?php

namespace WP_Clanwars;

/**
 * API class.
 */
class API {

    protected static $api_url = 'http://localhost:3000/v1/';

    static function get_popular() {
        $response = wp_remote_get( self::$api_url . 'gamepacks/popular' );

        return self::get_response_payload($response);
    }

    static function search($term) {
        $response = wp_remote_get( self::$api_url, 'gamepacks/search?q=' . urlencode($term) );

        return self::get_response_payload($response);
    }

    protected static function get_response_payload($response) {
        if(is_wp_error($response)) {
            return $response;
        }

        $api_response = json_decode(wp_remote_retrieve_body($response), true);

        if(!$api_response['success']) {
            $error = $api_response['error'];
            return new \WP_Error( $error['code'], $error['message'] );
        }

        return $api_response['payload'];
    }

}