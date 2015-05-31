<?php
//
// Flash
//

namespace WP_Clanwars;

class Flash {

    const FLASH_ERROR_KEY = "error";
    const FLASH_SUCCESS_KEY = "updated";

    const FLASH_CONTENT_KEY = "content";
    const FLASH_SAFE_KEY = "is_safe";
    const FLASH_SHOW_ABOVE_H2 = "above_h2";

    const FLASH_USER_META_KEY = "_wp-clanwars_flash_messages";

    protected static $_messages = array();
    protected static $_prev_messages = array();

    static function setup() {
        static::load_messages_usermeta();

        add_action('admin_notices', '\\WP_Clanwars\\Flash::_print_admin_notices');
        register_shutdown_function( '\\WP_Clanwars\\Flash::_shutdown' );
    }

    static function flash($message, $type = self::FLASH_SUCCESS_KEY, $is_safe = false, $show_above_h2 = false) {
        if(!isset(static::$_messages[$type])) {
            static::$_messages[$type] = array();
        }

        static::$_messages[$type][] = array(
            self::FLASH_CONTENT_KEY => (string)$message,
            self::FLASH_SAFE_KEY => (boolean)$is_safe,
            self::FLASH_SHOW_ABOVE_H2 => (boolean)$show_above_h2
        );
    }

    static function flash_error($message, $is_safe = false, $show_above_h2 = false) {
        return static::flash($message, self::FLASH_ERROR_KEY, $is_safe, $show_above_h2);
    }

    static function flash_success($message, $is_safe = false, $show_above_h2 = false) {
        return static::flash($message, self::FLASH_SUCCESS_KEY, $is_safe, $show_above_h2);
    }

    static function display_flash() {
        foreach(static::$_messages as $key => $items) {
            foreach($items as $index => $item) {
                $content = $item[ self::FLASH_CONTENT_KEY ];
                $is_safe = $item[ self::FLASH_SAFE_KEY ];
                $show_above_h2 = $item[ self::FLASH_SHOW_ABOVE_H2 ];

                // escape if needed
                if(!$is_safe) {
                    $content = esc_html($content);
                }

                echo '<div class="' . esc_attr($key) . (($show_above_h2) ? " below-h2": '') . '"><p>' . $content . '</p></div>';
            }
        }
    }

    static protected function mark_messages_displayed() {
        // reset messages once displayed
        static::$_messages = array();
    }

    static protected function load_messages_usermeta() {
        $current_user_id = get_current_user_id();
        $meta = get_user_meta($current_user_id, self::FLASH_USER_META_KEY, true);

        if(is_array($meta)) {
            static::$_messages = $meta;
        }

        static::$_prev_messages = $meta;
    }

    static protected function save_messages_usermeta() {
        $current_user_id = get_current_user_id();

        update_user_meta($current_user_id, self::FLASH_USER_META_KEY, static::$_messages, static::$_prev_messages);
    }

    static function _print_admin_notices() {
        static::display_flash();
        static::mark_messages_displayed();
    }

    static function _shutdown() {
        static::save_messages_usermeta();
    }
}