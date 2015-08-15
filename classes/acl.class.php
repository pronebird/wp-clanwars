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

define('WP_CLANWARS_ACL', '_wp_clanwars_acl');

class ACL {

    /**
     * Destroys the entire ACL storage
     */
    static function destroy() {
        delete_option(WP_CLANWARS_ACL);
    }

    /**
     * Get contents of entire ACL storage
     * @return array
     */
    static function get() {
        $acl = get_option(WP_CLANWARS_ACL);

        if(!is_array($acl)) {
            $acl = array();
        }

        return $acl;
    }

    /**
     * Update record for single user
     * @param  int $user_id
     * @param  array $data
     * @return bool
     */
    static function update($user_id, $data) {
        $acl = static::get();

        $acl[$user_id] =  array(
            'games' => array(0),
            'permissions' => array('manage_matches')
        );

        $default_perms = array(
            'manage_matches' => false,
            'manage_teams' => false,
            'manage_games' => false
        );

        $acl[$user_id]['games'] = isset($data['games']) ? array_unique(array_values($data['games'])) : array(0);
        $acl[$user_id]['permissions'] = isset($data['permissions']) ? Utils::extract_args($data['permissions'], $default_perms) : $default_perms;

        update_option(WP_CLANWARS_ACL, $acl);

        return true;
    }

    /**
     * Delete permissions for a single user
     * @param  int $user_id
     * @return bool
     */
    static function delete($user_id) {
        $acl = static::get();

        if(isset($acl[$user_id])) {
            unset($acl[$user_id]);
            update_option(WP_CLANWARS_ACL, $acl);

            return true;
        }

        return false;
    }

    /**
     * Get all capabilities
     * @return array
     */
    static function all_caps() {
        return array(
            'manage_matches' => __('Manage matches', WP_CLANWARS_TEXTDOMAIN),
            'manage_games' => __('Manage games', WP_CLANWARS_TEXTDOMAIN),
            'manage_teams' => __('Manage teams', WP_CLANWARS_TEXTDOMAIN)
        );
    }

    /**
     * Check if user has enough permissions to perform action
     * @param  string  $action
     * @param  mixed   $value
     * @param  int     $user_id
     * @return mixed
     */
    static function user_can($action, $value = false, $user_id = false) {
        global $user_ID;

        $acl = static::get();
        $all_caps = static::all_caps();
        $is_super = false;
        $caps = array(
            'games' => array(),
            'permissions' => array_fill_keys(array_keys($all_caps), false)
        );

        if(empty($user_id)) {
            $user_id = $user_ID;
        }

        if(!empty($acl) && isset($acl[$user_id])) {
            $caps = $acl[$user_id];
        }

        $user = new \WP_User($user_id);
        if(!empty($user)) {
            $is_super = $user->has_cap('manage_options');
        }

        if($is_super) {
            $caps['games'] = array('all');
            $caps['permissions'] = array_fill_keys(array_keys($caps['permissions']), true);
        }

        if($action === 'which_games') {
            $where = array_search(0, $caps['games']);

            if($where === false) {
                return $caps['games'];
            }

            return 'all';
        }
        else if($action === 'manage_game') {
            if($value == 'all') {
                $value = 0;
            }

            $ret = array_search($value, $caps['games']) !== false;

            if(!$ret) {
                $ret = array_search(0, $caps['games']) !== false;
            }

            return $ret;
        }

        return isset($caps['permissions'][$action]) && $caps['permissions'][$action];
    }

}