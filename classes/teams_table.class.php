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

require_once( dirname(__FILE__) . '/acl.class.php' );
require_once( dirname(__FILE__) . '/utils.class.php' );

if(!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

add_filter('set-screen-option', array('\WP_Clanwars\TeamsTable', 'handle_screen_option'), 10, 3);

class TeamsTable extends \WP_List_Table {

    const PER_PAGE_OPTION = 'wp_clanwars_teams_per_page';
    const PER_PAGE_DEFAULT = 10;

    static function handle_screen_option($status, $option, $value) {
        if($option === static::PER_PAGE_OPTION) {
            $value = (int)$value;
            if($value < 1 || $value > 999) {
                return;
            }
        }
        return $value;
    }

    function __construct($args = array()) {
        $base_args = array(
            'singular' => 'team',
            'plural' => 'teams'
        );

        parent::__construct( array_merge($base_args, $args) );

        // register screen options for match_table
        $screen_options = array(
            'label' => __('Teams per page', WP_CLANWARS_TEXTDOMAIN),
            'default' => static::PER_PAGE_DEFAULT,
            'option' => static::PER_PAGE_OPTION
        );
        add_screen_option('per_page', $screen_options);
    }

    function get_columns() {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => __('Title', WP_CLANWARS_TEXTDOMAIN),
            'country' => __('Country', WP_CLANWARS_TEXTDOMAIN)
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'title' => 'title',
            'country' => 'country'
        );
        return $sortable_columns;
    }

    function get_table_classes() {
        $classes = parent::get_table_classes();
        $classes[] = 'wp-clanwars-teams-table';
        return $classes;
    }

    protected function get_bulk_actions() {
        return array(
            'delete' => __('Delete', WP_CLANWARS_TEXTDOMAIN)
        );
    }

    function no_items() {
        _e('No teams found.', WP_CLANWARS_TEXTDOMAIN);
    }

    function column_cb($item) {
        return '<input type="checkbox" name="id[]" value="' . esc_attr($item->id) . '" />';
    }

    function column_default($item, $column_name) {
        if(isset($item->$column_name)) {
            return esc_html($item->$column_name);
        }
    }

    function column_title($item) {
        $output = '';
        $icon = wp_get_attachment_url($item->logo);

        if($icon !== false) {
            $output .= '<img src="' . esc_attr($icon) . '" alt="' . esc_attr($item->title) . '" class="icon" />';
        }
        else {
            $output .= '<div class="icon placeholder"></div>';
        }

        $output .= esc_html($item->title);

        if($item->home_team) {
            $output .= ' <span class="home-team">' . __('Home Team', WP_CLANWARS_TEXTDOMAIN) . '</span>';
        }

        return $output;
    }

    function column_country($item) {
        return \WP_Clanwars\Utils::get_country_flag($item->country) . ' ' . \WP_Clanwars\Utils::get_country_title($item->country) ;
    }

    protected function get_default_primary_column_name() {
        return 'title';
    }

    protected function handle_row_actions( $item, $column_name, $primary ) {
        if ( $primary !== $column_name ) {
            return '';
        }

        $actions = array();

        $edit_link = admin_url('admin.php?page=wp-clanwars-teams&amp;act=edit&amp;id=' . $item->id);
        $delete_link = wp_nonce_url('admin-post.php?action=wp-clanwars-deleteteams&amp;do_action=delete&amp;delete[]=' . $item->id . '&amp;_wp_http_referer=' . urlencode($_SERVER['REQUEST_URI']), 'wp-clanwars-deleteteams');
        $set_home_team_link = wp_nonce_url('admin-post.php?action=wp-clanwars-sethometeam&amp;do_action=sethometeam&amp;id=' . $item->id . '&amp;_wp_http_referer=' . urlencode($_SERVER['REQUEST_URI']), 'wp-clanwars-sethometeam');

        $actions['edit'] = '<a href="' . esc_attr($edit_link) . '">' . __('Edit', WP_CLANWARS_TEXTDOMAIN) . '</a>';
        $actions['delete'] = '<a href="' . esc_attr($delete_link) . '">' . __('Delete', WP_CLANWARS_TEXTDOMAIN) . '</a>';

        if(!$item->home_team) {
            $actions['set_home_team'] = '<a href="' . esc_attr($set_home_team_link) . '">' . __('Set As Home Team', WP_CLANWARS_TEXTDOMAIN) . '</a>';
        }

        return $this->row_actions( $actions );
    }

    function prepare_items() {
        $per_page = $this->get_items_per_page(static::PER_PAGE_OPTION, static::PER_PAGE_DEFAULT);
        $current_page = $this->get_pagenum();

        $orderby = ( isset( $_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : 'id';
        $order = ( isset( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'desc';

        $offset = ($current_page - 1) * $per_page;
        $limit = $per_page;

        $args = array(
            'id' => 'all',
            'order' => $order,
            'orderby' => $orderby,
            'order' => $order,
            'limit' => $limit,
            'offset' => ($limit * ($current_page-1))
        );

        $teams = \WP_Clanwars\Teams::get_team($args);

        $pagination = $teams->get_pagination();

        $this->set_pagination_args(array(
            'total_pages' => $pagination->get_num_pages(),
            'total_items' => $pagination->get_num_rows(),
            'per_page' => $per_page
        ));

        $this->items = $teams->getArrayCopy();
    }

}
