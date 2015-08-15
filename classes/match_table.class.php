<?php

namespace WP_Clanwars;

require_once( dirname(__FILE__) . '/acl.class.php' );
require_once( dirname(__FILE__) . '/utils.class.php' );

if(!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class MatchTable extends \WP_List_Table {

    const PER_PAGE_OPTION = 'wp_clanwars_matches_per_page';
    const PER_PAGE_DEFAULT = 10;
    const DATE_FORMAT = 'd.m.Y';
    const DATETIME_FORMAT = 'd.m.Y @ H:i';

    //
    // Screen options validation handler
    // You have to setup a filter externally as it must be executed before load-$page. Use plugin constructor for that.
    //
    // Example:
    // add_filter('set-screen-option', array('\WP_Clanwars\MatchTable', 'handle_screen_option'), 10, 3);
    //
    static function handle_screen_option($status, $option, $value) {
        if($option === static::PER_PAGE_OPTION) {
            $value = (int)$value;
            if($value < 1 || $value > 999) {
                return;
            }
        }
        return $value;
    }

    static function add_screen_filter() {
        add_filter('set-screen-option', array('\WP_Clanwars\MatchTable', 'handle_screen_option'), 10, 3);
    }

    function __construct($args = array()) {
        parent::__construct($args);

        // register screen options for match_table
        $args = array(
            'label' => __('Matches per page', WP_CLANWARS_TEXTDOMAIN),
            'default' => static::PER_PAGE_DEFAULT,
            'option' => static::PER_PAGE_OPTION
        );
        add_screen_option('per_page', $args);
    }

    function get_columns() {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => __('Title', WP_CLANWARS_TEXTDOMAIN),
            'game' => __('Game', WP_CLANWARS_TEXTDOMAIN),
            'date' => __('Date', WP_CLANWARS_TEXTDOMAIN),
            'match_status' => __('Status', WP_CLANWARS_TEXTDOMAIN),
            'team1' => __('Team 1', WP_CLANWARS_TEXTDOMAIN),
            'team2' => __('Team 2', WP_CLANWARS_TEXTDOMAIN),
            'tickets' => __('Tickets', WP_CLANWARS_TEXTDOMAIN)
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array();
        return $sortable_columns;
    }

    function get_table_classes() {
        $classes = parent::get_table_classes();
        $classes[] = 'wp-clanwars-match-table';
        return $classes;
    }

    protected function get_bulk_actions() {
        return array(
            'delete' => __('Delete', WP_CLANWARS_TEXTDOMAIN)
        );
    }

    function no_items() {
        _e('No matches found.', WP_CLANWARS_TEXTDOMAIN);
    }

    function column_cb($item) {
        return '<input type="checkbox" name="delete[]" value="' . esc_attr($item->id) . '" />';
    }

    function column_date($item) {
        return mysql2date(get_option('date_format') . ' ' . get_option('time_format'), $item->date);
    }

    function column_default($item, $column_name) {
        if($column_name == '_action_column') {
            return '<a href="' . admin_url('admin.php?page=mw_reservations&id=' . (int)$item->reservation_id) . '">View Reservation<span class="dashicons dashicons-arrow-right-alt2"></span></a>';
        }

        if(isset($item->$column_name)) {
            return esc_html($item->$column_name);
        }
    }

    function column_game($item) {
        $output = '';

        $icon = wp_get_attachment_url($item->game_icon);

        if($icon !== false) {
            $output .= '<img src="' . esc_attr($icon) . '" alt="' . esc_attr($item->game_title) . '" class="icon" /> ';
        }

        $output .= esc_html($item->game_title);

        return $output;
    }

    function column_team1($item) {
        return \WP_Clanwars\Utils::get_country_flag($item->team1_country) . ' ' . $item->team1_title;
    }

    function column_team2($item) {
        return \WP_Clanwars\Utils::get_country_flag($item->team2_country) . ' ' . $item->team2_title;
    }

    function column_match_status($item) {
        $status = array(
            __('PCW', WP_CLANWARS_TEXTDOMAIN),
            __('Official', WP_CLANWARS_TEXTDOMAIN)
        );
        return $status[ $item->match_status ];
    }

    function column_tickets($item) {
        return sprintf(__('%s:%s', WP_CLANWARS_TEXTDOMAIN), $item->team1_tickets, $item->team2_tickets);
    }

    protected function handle_row_actions( $item, $column_name, $primary ) {
        if ( $primary !== $column_name ) {
            return '';
        }

        $actions = array();

        $edit_link = admin_url('admin.php?page=wp-clanwars-matches&act=edit&id=' . $item->id);
        $delete_link = wp_nonce_url('admin-post.php?action=wp-clanwars-deletematches&do_action=delete&delete[]=' . $item->id . '&_wp_http_referer=' . urlencode($_SERVER['REQUEST_URI']), 'wp-clanwars-deletematches');

        $actions['edit'] = '<a href="' . esc_attr($edit_link) . '">' . __('Edit', WP_CLANWARS_TEXTDOMAIN) . '</a>';
        $actions['delete'] = '<a href="' . esc_attr($delete_link) . '">' . __('Delete', WP_CLANWARS_TEXTDOMAIN) . '</a>';

        return $this->row_actions( $actions );
    }

    function prepare_items() {
        $per_page = $this->get_items_per_page(static::PER_PAGE_OPTION, static::PER_PAGE_DEFAULT);
        $current_page = $this->get_pagenum();

        $offset = ($current_page - 1) * $per_page;
        $limit = $per_page;

        $game_filter = \WP_Clanwars\ACL::user_can('which_games');
        $condition = array(
            'id' => 'all', 
            'game_id' => $game_filter, 
            'sum_tickets' => true,
            'orderby' => 'date', 
            'order' => 'desc',
            'limit' => $limit, 
            'offset' => ($limit * ($current_page-1))
        );

        $matches = \WP_Clanwars\Matches::get_match($condition);
        $pagination = $matches->get_pagination();

        $this->set_pagination_args(array(
            'total_items' => $pagination->get_num_pages(),
            'per_page' => $per_page
        ));

        $this->items = (array)$matches;
    }

}
