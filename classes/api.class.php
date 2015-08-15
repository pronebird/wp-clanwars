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

class API {

    protected static $api_url = 'http://localhost:3000/v1/';
    protected static $client_key_option_key = 'wp-clanwars-server-clientkey';
    protected static $access_token_usermeta_key = 'wp-clanwars-server-accesstoken';
    protected static $user_info_usermeta_key = 'wp-clanwars-server-userinfo';

    static function is_logged_in() {
        return !empty( static::get_access_token() );
    }

    static function get_login_url($service, $callbackUrl) {
        return static::$api_url . 'auth/' . $service . '?returnTo=' . urlencode($callbackUrl);
    }

    static function update_access_token($access_token) {
        $status = static::get_auth_status($token);

        if(!is_wp_error($status) && is_object($status) && isset($status->socialId)) {
            static::set_access_token($token);
            static::set_user_info($status);

            return true;
        }

        static::set_access_token('');
        static::set_user_info('');

        return false;
    }

    protected static function get_access_token() {
        global $current_user;
        return get_user_meta( $current_user->ID, static::$access_token_usermeta_key, true );
    }

    protected static function set_access_token($access_token) {
        global $current_user;
        update_user_meta( $current_user->ID, static::$access_token_usermeta_key, $access_token );
    }

    protected static function set_user_info($userInfo) {
        global $current_user;
        update_user_meta( $current_user->ID, static::$user_info_usermeta_key, $userInfo );
    }

    static function get_user_info() {
        global $current_user;
        return get_user_meta( $current_user->ID, static::$user_info_usermeta_key, true );
    }

    static function get_auth_status( $access_token = '' ) {
        $args = array();

        if(!empty($access_token)) {
            $args = array(
                'headers' => array(
                    'Authorization' => 'Bearer ' . $access_token
                )
            );
        }

        $response = static::remote_get( static::$api_url . 'auth/status', $args );

        return static::get_response_payload($response);
    }

    static function get_download_url($id) {
        return static::$api_url . 'games/download/' . $id;
    }

    static function get_game($id) {
        $response = static::remote_get( static::$api_url . 'games/' . $id );

        return static::get_response_payload($response);
    }

    static function get_popular() {
        $response = static::remote_get( static::$api_url . 'games/popular' );

        return static::get_response_payload($response);
    }

    static function search($term) {
        $response = static::remote_get( static::$api_url . 'games/search?q=' . urlencode($term) );

        return static::get_response_payload($response);
    }

    static function publish($zip_file) {
        if(!function_exists('curl_init')) {
            return new WP_Error( 'api-error', 0, 'Unable to locate cURL extension.' );
        }

        $zip_file = realpath($zip_file);
        $data = array();

        $headers = array( 
            'Content-Type: multipart/form-data',
            'Authorization: Bearer ' . static::get_access_token()
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, static::$api_url . 'games');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_USERAGENT, static::get_user_agent());

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

        return static::get_response_payload($response);
    }

    protected static function get_user_agent() {
        global $wp_version;

        return 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ) . '; WP-Clanwars/' . WP_CLANWARS_VERSION;
    }

    protected static function remote_get($url, $args = array()) {
        $headers = array();

        if(static::is_logged_in()) {
            $headers = array(
                'Authorization' => 'Bearer ' . static::get_access_token()
            );
        }

        $_args = array(
            'user-agent' => static::get_user_agent(),
            'headers' => $headers
        );

        return wp_remote_get( $url, array_merge_recursive($_args, (array)$args));
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