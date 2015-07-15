<?php

namespace WP_Clanwars;

/**
 * API class.
 */
class API {

    protected static $api_url = 'http://localhost:3000/v1/';

    static function get_download_url($id) {
        return self::$api_url . 'games/download/' . $id;
    }

    static function get_game($id) {
        $response = self::remote_get( self::$api_url . 'games/' . $id );

        return self::get_response_payload($response);
    }

    static function get_popular() {
        $response = self::remote_get( self::$api_url . 'games/popular' );

        return self::get_response_payload($response);
    }

    static function search($term) {
        $response = self::remote_get( self::$api_url . 'games/search?q=' . urlencode($term) );

        return self::get_response_payload($response);
    }

    static function publish($zip_file) {
        if(!function_exists('curl_init')) {
            return new WP_Error( 'api-error', 0, 'Unable to locate cURL extension.' );
        }

        $zip_file = realpath($zip_file);
        $data = array();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$api_url . 'games');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: multipart/form-data' ));
        curl_setopt($ch, CURLOPT_USERAGENT, self::get_user_agent());

        // use safe cURL uploads when possible
        if( function_exists( 'curl_file_create' ) ) { // php 5.5+
            // disable unsafe uploads (true is default in php 5.6+)
            curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);

            $data['file'] = curl_file_create($zip_file, 'application/zip', 'file');
        }
        else { // php 5.2+
            // filter out attempts to upload files from server
            // by prefixing text fields with @
            // Remove @ from the beginning of each value.
            array_walk($data, function (&$val) {
                $val = preg_replace('#^@#i', '', trim( (string)$val ));
            });

            $data['file'] = '@' . $zip_file . ';type=application/zip';
        }

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($ch);

        if($response === false) {
            $err = curl_error($ch);
            $code = curl_errno($ch);
            return new WP_Error( 'api-error-curl', $code, $err );
        }

        $info = curl_getinfo($ch);
        $http_code = (int) $info['http_code'];
        if( $http_code === 401 ) {
            return new \WP_Error( 'api-error-authorization', __( 'Authorization required.', WP_CLANWARS_TEXTDOMAIN ) );
        }

        return self::get_response_payload($response);
    }

    protected static function get_user_agent() {
        global $wp_version;

        return 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ) . '; WP-Clanwars/' . WP_CLANWARS_VERSION;
    }

    protected static function remote_get($url, $args = array()) {
        $_args = array(
            'user-agent' => self::get_user_agent()
        );

        return wp_remote_get( $url, (array)$args + $_args);
    }

    protected static function get_response_payload($response) {
        if(is_wp_error($response)) {
            return $response;
        }

        $api_response = json_decode( is_string($response) ? $response : wp_remote_retrieve_body($response) );

        if(!$api_response->success) {
            $response_error = $api_response->error;
            return new \WP_Error( 'api-error-' . $response_error->code, $response_error->message );
        }

        return $api_response->payload;
    }

}