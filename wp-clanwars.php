<?php
/**
 * Plugin Name: WP-ClanWars
 * Author URI: http://www.codeispoetry.ru/
 * Plugin URI: https://bitbucket.org/and/wp-clanwars
 * Description: ClanWars plugin for a cyber-sport team website
 * Author: Andrej Mihajlov
 * Version: 1.7.0
 *
 * Tags: cybersport, clanwar, team, clan, cyber, sport, match
 **/

/*
	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if(!function_exists('add_action')) die('Cheatin&#8217; uh?');

global $wpClanWars;

define('WP_CLANWARS_VERSION', '1.7.0');

define('WP_CLANWARS_TEXTDOMAIN', 'wp-clanwars');
define('WP_CLANWARS_CATEGORY', '_wp_clanwars_category');
define('WP_CLANWARS_DEFAULTCSS', '_wp_clanwars_defaultcss');
define('WP_CLANWARS_ACL', '_wp_clanwars_acl');
define('WP_CLANWARS_URL', WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__)));

define('WP_CLANWARS_IMPORTDIR', 'import');
define('WP_CLANWARS_IMPORTPATH', dirname(__FILE__) . '/' . WP_CLANWARS_IMPORTDIR);
define('WP_CLANWARS_IMPORTURL', WP_CLANWARS_URL . '/' . WP_CLANWARS_IMPORTDIR);

// this folder is created in wp-content/
define('WP_CLANWARS_EXPORTDIR', 'wp-clanwars');
define('WP_CLANWARS_ZIPINDEX', 'index.json');

require_once (dirname(__FILE__) . '/classes/view.class.php');
require_once (dirname(__FILE__) . '/classes/utils.class.php');
require_once (dirname(__FILE__) . '/classes/games.class.php');
require_once (dirname(__FILE__) . '/classes/teams.class.php');
require_once (dirname(__FILE__) . '/classes/maps.class.php');
require_once (dirname(__FILE__) . '/classes/rounds.class.php');
require_once (dirname(__FILE__) . '/classes/matches.class.php');

require_once (dirname(__FILE__) . '/wp-clanwars-widget.php');
require_once (ABSPATH . 'wp-admin/includes/class-pclzip.php');

class WP_ClanWars {

	var $match_status = array();
	var $acl_keys = array();
	var $page_hooks = array();
	var $page_notices = array();

	const ErrorOK = 0;
	const ErrorDatabase = -199;
	const ErrorUploadMaxFileSize = -208;
	const ErrorUploadHTMLMaxFileSize = -209;
	const ErrorUploadPartially = -210;
	const ErrorUploadNoFile = -211;
	const ErrorUploadMissingTemp = -212;
	const ErrorUploadDiskWrite = -213;
	const ErrorUploadStoppedByExt = -214;
	const ErrorUploadFileTypeNotAllowed = -215;

	function __construct() {
		load_plugin_textdomain(WP_CLANWARS_TEXTDOMAIN, PLUGINDIR . '/' . dirname(plugin_basename(__FILE__)) . '/langs/', //2.5 Compatibility
							   dirname(plugin_basename(__FILE__)) . '/langs/'); //2.6+, Works with custom wp-content dirs.

		add_action('widgets_init', array($this, 'on_widgets_init'));
		add_action('init', array($this, 'on_init'));
	}

	/**
	 * Check if plugin runs within jumpstarter instance
	 *
	 * @return bool true if plugin runs within jumpstarter instance, otherwise false
	 */
	function is_jumpstarter() {
		// JS_WP_User is defined in wp-jumpstarter:
		// see: https://github.com/jumpstarter-io/wp-jumpstarter/blob/master/jumpstarter.php
		return class_exists('JS_WP_User');
	}

	/**
	 * Plugin activation hook
	 *
	 * Creates tables if needed
	 *
	 * @return void
	 */

	public function on_activate()
	{
		global $wpdb;

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		$charset_collate = $wpdb->get_charset_collate();

		$dbstruct = '';
		$dbstruct .= \WP_Clanwars\Games::schema();
		$dbstruct .= \WP_Clanwars\Maps::schema();
		$dbstruct .= \WP_Clanwars\Matches::schema();
		$dbstruct .= \WP_Clanwars\Rounds::schema();
		$dbstruct .= \WP_Clanwars\Teams::schema();

		add_option(WP_CLANWARS_CATEGORY, -1);
		add_option(WP_CLANWARS_DEFAULTCSS, true);
		add_option(WP_CLANWARS_ACL, array());

		// update database
		dbDelta($dbstruct);
	}

	/**
	 * Plugin deactivation hook
	 *
	 * @return void
	 */

	public function on_deactivate()
	{
	}

	public function on_uninstall()
	{
		global $wpdb;

		delete_option(WP_CLANWARS_CATEGORY);
		delete_option(WP_CLANWARS_DEFAULTCSS);
		delete_option(WP_CLANWARS_ACL);

		$tables = array();

		array_push( \WP_Clanwars\Games::table() );
		array_push( \WP_Clanwars\Maps::table() );
		array_push( \WP_Clanwars\Rounds::table() );
		array_push( \WP_Clanwars\Matches::table() );
		array_push( \WP_Clanwars\Teams::table() );

		foreach($tables as $table) {
			$wpdb->query( "DROP TABLE `$table`" );
		}
	}

	/**
	 * WP init hook
	 *
	 * Plugin initialization method used to load textdomain,
	 * register hooks, scripts and styles.
	 *
	 * @return void
	 */

	function on_init()
	{
		$this->acl_keys = array(
			'manage_matches' => __('Manage matches', WP_CLANWARS_TEXTDOMAIN),
			'manage_games' => __('Manage games', WP_CLANWARS_TEXTDOMAIN),
			'manage_teams' => __('Manage teams', WP_CLANWARS_TEXTDOMAIN)
		);

		$this->match_status = array(
			__('PCW', WP_CLANWARS_TEXTDOMAIN),
			__('Official', WP_CLANWARS_TEXTDOMAIN)
		);

		add_action('admin_print_styles', array($this, 'on_admin_print_styles'));
		add_action('admin_menu', array($this, 'on_admin_menu'));
		add_action('template_redirect', array($this, 'on_template_redirect'));
		add_action('wp_footer', array($this, 'on_wp_footer'));

		add_action('admin_post_wp-clanwars-deleteteams', array($this, 'on_admin_post_deleteteams'));
		add_action('admin_post_wp-clanwars-sethometeam', array($this, 'on_admin_post_sethometeam'));
		add_action('admin_post_wp-clanwars-gamesop', array($this, 'on_admin_post_gamesop'));
		add_action('admin_post_wp-clanwars-deletemaps', array($this, 'on_admin_post_deletemaps'));
		add_action('admin_post_wp-clanwars-deletematches', array($this, 'on_admin_post_deletematches'));

		add_action('admin_post_wp-clanwars-settings', array($this, 'on_admin_post_settings'));
		add_action('admin_post_wp-clanwars-acl', array($this, 'on_admin_post_acl'));
		add_action('admin_post_wp-clanwars-deleteacl', array($this, 'on_admin_post_deleteacl'));
		add_action('admin_post_wp-clanwars-import', array($this, 'on_admin_post_import'));

		add_action('admin_post_wp-clanwars-setupteam', array($this, 'on_admin_post_setupteam'));
		add_action('admin_post_wp-clanwars-setupgames', array($this, 'on_admin_post_setupgames'));

		add_action('wp_ajax_get_maps', array($this, 'on_ajax_get_maps'));
		add_shortcode('wp-clanwars', array($this, 'on_shortcode'));

		$this->register_cssjs();
	}

	function on_admin_print_styles() {
echo <<<EOT
<style type="text/css">
#toplevel_page_wp-clanwars-matches .wp-menu-image {
	background-size: 16px 16px !important;
}
</style>
EOT;
	}

	/**
	 * WP admin_menu hook
	 *
	 * Page, Assets registration, load-* action hooks
	 *
	 * @return void
	 */
	function on_admin_menu()
	{
		global $current_user;

		$acl_table = array(
			'manage_matches' => 'manage_options',
			'manage_teams' => 'manage_options',
			'manage_games' => 'manage_options',
		);

		$routes = array(
			'manage_matches' => 'wp-clanwars-matches',
			'manage_teams' => 'wp-clanwars-teams',
			'manage_games' => 'wp-clanwars-games'
		);

		$keys = array_keys($acl_table);
		$user_role = $current_user->roles[0];
		$top_level_slug = '';

		for($i = 0; $i < sizeof($keys); $i++) {
			if($this->acl_user_can($keys[$i])) {
				$acl_table[$keys[$i]] = $user_role;

				// point top level slug to first menu user has access to
				if($top_level_slug === '') {
					$top_level_slug = $routes[$keys[$i]];
				}
			}
		}

		// do not register admin menu becuase user doesn't have any permissions
		if($top_level_slug === '') {
			return;
		}

		// place plugin below dashboard on jumpstarter
		$menu_position = $this->is_jumpstarter() ? 3 : null;

		// prepare SVG data URI image for menu
		$iconData = file_get_contents( dirname(__FILE__) . '/images/plugin-icon.svg' );
		$iconDataURI = 'data:image/svg+xml;base64,' . base64_encode($iconData);

		$top = add_menu_page(
			__('ClanWars', WP_CLANWARS_TEXTDOMAIN),
			__('ClanWars', WP_CLANWARS_TEXTDOMAIN),
			$user_role,
			$top_level_slug,
			null,
			$iconDataURI,
			$menu_position
		);

		$this->page_hooks['matches'] = add_submenu_page(
			$top_level_slug,
			__('Matches', WP_CLANWARS_TEXTDOMAIN),
			__('Matches', WP_CLANWARS_TEXTDOMAIN),
			$acl_table['manage_matches'],
			$routes['manage_matches'],
			$this->onboarding_or_page( 'on_manage_matches' )
		);

		$this->page_hooks['teams'] = add_submenu_page(
			$top_level_slug,
			__('Teams', WP_CLANWARS_TEXTDOMAIN),
			__('Teams', WP_CLANWARS_TEXTDOMAIN),
			$acl_table['manage_teams'],
			$routes['manage_teams'],
			$this->onboarding_or_page( 'on_manage_teams' )
		);

		$this->page_hooks['games'] = add_submenu_page(
			$top_level_slug,
			__('Games', WP_CLANWARS_TEXTDOMAIN),
			__('Games', WP_CLANWARS_TEXTDOMAIN),
			$acl_table['manage_games'],
			$routes['manage_games'],
			$this->onboarding_or_page( 'on_manage_games' )
		);

		$this->page_hooks['import'] = add_submenu_page(
			$top_level_slug,
			__('Import', WP_CLANWARS_TEXTDOMAIN),
			__('Import', WP_CLANWARS_TEXTDOMAIN),
			'manage_options',
			'wp-clanwars-import',
			$this->onboarding_or_page( 'on_import' )
		);

		$this->page_hooks['settings'] = add_submenu_page(
			$top_level_slug,
			__('Settings', WP_CLANWARS_TEXTDOMAIN),
			__('Settings', WP_CLANWARS_TEXTDOMAIN),
			'manage_options',
			'wp-clanwars-settings',
			$this->onboarding_or_page( 'on_settings' )
		);

		if(!$this->should_onboard_user()) {
			add_action('load-' . $this->page_hooks['matches'], array($this, 'on_load_manage_matches'));
			add_action('load-' . $this->page_hooks['teams'], array($this, 'on_load_manage_teams'));
			add_action('load-' . $this->page_hooks['games'], array($this, 'on_load_manage_games'));
		}

		foreach($this->page_hooks as $page_hook) {
			add_action('load-' . $page_hook, array($this, 'on_load_any'));
		}
	}

	function register_cssjs()
	{
		wp_register_script('wp-cw-matches', WP_CLANWARS_URL . '/js/matches.js', array('jquery'), WP_CLANWARS_VERSION);
		wp_register_script('wp-cw-screenshots', WP_CLANWARS_URL . '/js/screenshots.js', array('jquery', 'media-upload'), WP_CLANWARS_VERSION);
		wp_register_script('wp-cw-admin', WP_CLANWARS_URL . '/js/admin.js', array('jquery'), WP_CLANWARS_VERSION);

		wp_register_style('wp-cw-admin', WP_CLANWARS_URL . '/css/admin.css', array(), WP_CLANWARS_VERSION);
		wp_register_style('wp-cw-flags', WP_CLANWARS_URL . '/css/flags.css', array(), '1.01');

		wp_register_script('jquery-tipsy', WP_CLANWARS_URL . '/js/tipsy/jquery.tipsy.js', array('jquery'), '0.1.7');
		wp_register_style('jquery-tipsy', WP_CLANWARS_URL . '/js/tipsy/tipsy.css', array(), '0.1.7');

		wp_register_script('wp-cw-public', WP_CLANWARS_URL . '/js/public.js', array('jquery-tipsy'), WP_CLANWARS_VERSION);

		wp_register_style('wp-cw-sitecss', WP_CLANWARS_URL . '/css/site.css', array(), WP_CLANWARS_VERSION);
		wp_register_style('wp-cw-widgetcss', WP_CLANWARS_URL . '/css/widget.css', array(), WP_CLANWARS_VERSION);
	}

	function onboarding_or_page($page_method) {
		if($this->should_onboard_user()) {
			return array( $this, 'onboarding_page');
		}
		return array( $this, $page_method );
	}

	function should_onboard_user() {
		static $flag = null;

		if($flag === null) {
			$has_hometeam = is_object( \WP_Clanwars\Teams::get_hometeam() );
			$games_result = \WP_Clanwars\Games::get_game(array(), true);

			$flag = ($games_result['total_items'] === 0 || !$has_hometeam) && current_user_can('manage_options');
		}

		return $flag;
	}

	function onboarding_page() {
		$games_result = \WP_Clanwars\Games::get_game(array(), true);

		$has_hometeam = is_object( \WP_Clanwars\Teams::get_hometeam() );
		$has_games = ($games_result['total_items'] > 0);

		if(!$has_hometeam && !$has_games) {
			$page_submit = __( 'Continue', WP_CLANWARS_TEXTDOMAIN );
		} 
		else {
			$page_submit = __( 'Get started', WP_CLANWARS_TEXTDOMAIN );
		}

		if(!$has_hometeam) {
			$this->onboarding_setup_team_page($page_submit);
		} 
		else if(!$has_games) {
			$this->onboarding_setup_games_page($page_submit);
		}
	}

	function onboarding_setup_team_page($page_submit) {
		$view = new \WP_Clanwars\View( 'setup_team' );
		$view->add_helper('html_country_select_helper', array('\WP_Clanwars\Utils', 'html_country_select_helper'));
		$context = compact('page_submit');

		if(isset($_GET['error'])) {
			$this->add_notice(__('Please fill in all required fields.', WP_CLANWARS_TEXTDOMAIN), 'error');
		}

		$this->print_notices();

		$view->render( $context );
	}

	function onboarding_setup_games_page($page_submit) {
		$view = new \WP_Clanwars\View( 'setup_games' );
		$context = compact('page_submit');
		$import_list = $this->get_available_games();
		$installed_games = \WP_Clanwars\Games::get_game('');

		// mark installed games
		foreach($import_list as $game) {
			$game->is_installed = ($this->is_game_installed($game->title, $game->abbr, $installed_games) !== false);
		}

		$context += compact('import_list');

		if(isset($_GET['upload'])) {
			$this->add_notice(__('An upload error occurred while import.', WP_CLANWARS_TEXTDOMAIN), 'error');
		}

		if(isset($_GET['import'])) {
			$this->add_notice($_GET['import'] === 'success' ? __('File(s) successfully imported.', WP_CLANWARS_TEXTDOMAIN) : __('An error occurred while import.', WP_CLANWARS_TEXTDOMAIN), $_GET['import'] === 'success' ? 'updated' : 'error');
		}

		if(isset($_GET['create'])) {
			$this->add_notice($_GET['create'] === 'success' ? __('Done.', WP_CLANWARS_TEXTDOMAIN) : __('An unknown error occurred while creating a new game.', WP_CLANWARS_TEXTDOMAIN), $_GET['create'] === 'success' ? 'updated' : 'error');
		}

		$this->print_notices();

		$view->render( $context );
	}

	function on_admin_post_setupteam() {
		if(!current_user_can('manage_options')) {
			wp_die(__('Cheatin&#8217; uh?'));
		}

		check_admin_referer('wp-clanwars-setupteam');

		$referer = $_REQUEST['_wp_http_referer'];

		$data = \WP_Clanwars\Utils::extract_args($_POST, array(
			'title' => '',
			'country' => ''
		));

		$data['home_team'] = 1;

		if(!empty($data['title']) && !empty($data['country'])) {
			\WP_Clanwars\Teams::add_team( $data );
		}
		else {
			$referer = add_query_arg('error', true, $referer);
		}

		wp_redirect($referer);
	}

	function on_admin_post_setupgames() {
		if(!current_user_can('manage_options')) {
			wp_die(__('Cheatin&#8217; uh?'));
		}

		check_admin_referer('wp-clanwars-setupgames');

		extract(\WP_Clanwars\Utils::extract_args($_POST, array(
				'import' => '',
				'items' => array(),
				'new_game_name' => ''
			)
		));

		$redirect_url = remove_query_arg(array('upload', 'import'), $_POST['_wp_http_referer']);

		switch($import) {
			case 'upload':

				if(isset($_FILES['userfile'])) {
					$file = $_FILES['userfile'];

					if($file['error'] == 0) {
						$result = $this->import_game($file['tmp_name']);
						$redirect_url = add_query_arg('import', ($result === true ? 'success' : 'error'), $redirect_url);
					} else {
						$redirect_url = add_query_arg('upload', 'error', $redirect_url);
					}
				}

				break;

			case 'available':

				$available_games = $this->get_available_games();
				$result = true;

				foreach($items as $item) {
					if(isset($available_games[$item])) {
						$r = $available_games[$item];
						$filename = trailingslashit(WP_CLANWARS_IMPORTPATH) . $r->package;
						$result = $this->import_game($filename);
						if($result !== true) {
							break;
						}
					}
				}

				$redirect_url = add_query_arg('import', ($result === true ? 'success' : 'error'), $redirect_url);

				break;

			case 'create':

				$new_game_id = \WP_Clanwars\Games::add_game(array(
					'title' => $new_game_name,
					'abbr' => strtoupper($new_game_name)
				));

				if(empty($new_game_id)) {
					$redirect_url = add_query_arg('create', 'error', $redirect_url);
				} else {
					// take user to maps management
					$redirect_url = admin_url('admin.php?page=wp-clanwars-games&act=maps&game_id=' . $new_game_id);
				}

				break;
		}

		wp_redirect( $redirect_url );
	}

	function acl_user_can($action, $value = false, $user_id = false)
	{
		global $user_ID;

		$acl = $this->acl_get();
		$is_super = false;
		$caps = array(
			'games' => array(),
			'permissions' => array_fill_keys(array_keys($this->acl_keys), false)
		);

		if(empty($user_id))
			$user_id = $user_ID;

		if(!empty($acl) && isset($acl[$user_id]))
			$caps = $acl[$user_id];

		$user = new WP_User($user_id);
		if(!empty($user))
			$is_super = $user->has_cap('manage_options');

		if($is_super) {
			$caps['games'] = array('all');
			array_walk($caps['permissions'], create_function('&$v, &$k', '$v = true;'));
		}

		switch($action)
		{
			case 'which_games':

				$where = array_search(0, $caps['games']);

				if($where === false)
					return $caps['games'];

				return 'all';

			break;

			case 'manage_game':

				if($value == 'all')
					$value = 0;

				$ret = array_search($value, $caps['games']) !== false;

				if(!$ret) {
					$ret = array_search(0, $caps['games']) !== false;
				}

				return $ret;

			break;
		}

		return isset($caps['permissions'][$action]) && $caps['permissions'][$action];
	}

	function acl_get() {
		$acl = get_option(WP_CLANWARS_ACL);

		if(!is_array($acl))
			$acl = array();

		return $acl;
	}

	function acl_update($user_id, $data) {

		$acl = $this->acl_get();

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
		$acl[$user_id]['permissions'] = isset($data['permissions']) ? \WP_Clanwars\Utils::extract_args($data['permissions'], $default_perms) : $default_perms;

		update_option(WP_CLANWARS_ACL, $acl);

		return true;
	}

	function acl_delete($user_id) {

		$acl = $this->acl_get();

		if(isset($acl[$user_id])) {
			unset($acl[$user_id]);
			update_option(WP_CLANWARS_ACL, $acl);

			return true;
		}

		return false;
	}

	function on_template_redirect() {
		wp_enqueue_script('wp-cw-public');
		wp_enqueue_style('jquery-tipsy');
		wp_enqueue_style('wp-cw-flags');
	}

	function on_wp_footer() {
		if(get_option(WP_CLANWARS_DEFAULTCSS)) {
			wp_enqueue_style('wp-cw-sitecss');
			wp_enqueue_style('wp-cw-widgetcss');
		}
	}

	function on_load_any() {
		wp_enqueue_style('wp-cw-admin');
		wp_enqueue_style('wp-cw-flags');

		wp_enqueue_script('wp-cw-admin');
		wp_localize_script('wp-cw-admin',
				'wpCWAdminL10n',
				array(
					'confirmDeleteMap' => __('Are you sure you want to delete this map?', WP_CLANWARS_TEXTDOMAIN),
					'confirmDeleteGame' => __('Are you sure you want to delete this game?', WP_CLANWARS_TEXTDOMAIN),
					'confirmDeleteTeam' => __('Are you sure you want to delete this team?', WP_CLANWARS_TEXTDOMAIN),
					'confirmDeleteMatch' => __('Are you sure you want to delete this match?', WP_CLANWARS_TEXTDOMAIN)
				)
			);
	}

	function on_widgets_init()
	{
		return register_widget('WP_ClanWars_Widget');
	}

	function html_notice_helper($message, $type = 'updated', $echo = true) {

		$text = '<div class="' . $type . ' fade"><p>' . $message . '</p></div>';

		if($echo) echo $text;

		return $text;
	}

	function print_table_header($columns, $id = true)
	{
		foreach ( $columns as $column_key => $column_display_name ) {
				$class = ' class="manage-column';

				$class .= " column-$column_key";

				if ( 'cb' == $column_key )
					$class .= ' check-column';
				elseif ( in_array($column_key, array('posts', 'comments', 'links')) )
					$class .= ' num';

				$class .= '"';
		?>
			<th scope="col" <?php echo $id ? "id=\"$column_key\"" : ""; echo $class; ?>><?php echo $column_display_name; ?></th>
		<?php }
	}

	function add_notice($message, $type = 'updated') {

		if(empty($type)) $type = 'updated';

		if(!isset($this->page_notices[$type])) {
			$this->page_notices[$type] = array();
		}

		$this->page_notices[$type][] = $message;
	}

	function print_notices() {
		foreach($this->page_notices as $type => $e) {
			foreach($e as $msg) {
				$this->html_notice_helper($msg, $type, true);
			}
		}
	}

	/**
	 * Image uploading handling, used internally by plugin
	 *
	 * @param string $name $_FILES array key for a file which should be uploaded
	 *
	 * @return void
	 */

	function handle_upload($name)
	{
		$mimes = apply_filters('upload_mimes',
				array('jpg|jpeg|jpe' => 'image/jpeg',
					  'gif' => 'image/gif',
					  'png' => 'image/png'));

		$upload = isset($_FILES[$name]) ? $_FILES[$name] : false;
		$upload_errors = array(self::ErrorOK,
							   self::ErrorUploadMaxFileSize,
							   self::ErrorUploadHTMLMaxFileSize,
							   self::ErrorUploadPartially,
							   self::ErrorUploadNoFile,
							   self::ErrorOK,
							   self::ErrorUploadMissingTemp,
							   self::ErrorUploadDiskWrite,
							   self::ErrorUploadStoppedByExt);

		if(!empty($upload))
		{
			if($upload['error'] > 0)
				return $upload_errors[$upload['error']];

			extract(wp_check_filetype($upload['name'], $mimes));

			if(!$type || !$ext)
			{
				return self::ErrorUploadFileTypeNotAllowed;
			}
			else
			{
				$file_data = wp_handle_upload($upload,
					array('test_type' => false,
						  'test_form' => false,
						  'upload_error_handler' =>
						   create_function('&$file, $message',
										   '$code = $file["error"];
										   $errors = array(' . implode(', ', $upload_errors) . ');
										   if(isset($errors[$code]))
											  return $errors[$code];
										   return $errors[0];')));

				if(!empty($file_data) && is_array($file_data))
				{
					$file_data['type'] = $type;
					if(!isset($file_data['error']))
					{
						$fileinfo = pathinfo($file_data['file']);
						$attach_title = basename($fileinfo['basename'], '.' . $fileinfo['extension']);
						$attach_id = wp_insert_attachment(array('guid' => $file_data['url'],
																'post_title' => $attach_title,
																'post_content' => '',
																'post_status' => 'publish',
																'post_mime_type' => $file_data['type']),
															$file_data['file']);

						$metadata = wp_generate_attachment_metadata($attach_id, $file_data['file']);

						if(!empty($metadata))
							wp_update_attachment_metadata($attach_id, $metadata);

						if(!empty($attach_id) && is_int($attach_id))
							return $attach_id;
					} else {
						return $upload_errors[$file_data['error']];
					}
				}
			}
		}

		return self::ErrorOK;
	}

	function on_admin_post_deleteteams()
	{
		if(!$this->acl_user_can('manage_teams'))
			wp_die( __('Cheatin&#8217; uh?') );

		check_admin_referer('wp-clanwars-deleteteams');

		$referer = remove_query_arg(array('add', 'update'), $_REQUEST['_wp_http_referer']);

		if($_REQUEST['do_action'] == 'delete' || $_REQUEST['do_action2'] == 'delete') {
			extract(\WP_Clanwars\Utils::extract_args($_REQUEST, array('delete' => array())));

			$error = \WP_Clanwars\Teams::delete_team($delete);
			$referer = add_query_arg('delete', $error, $referer);
		}

		wp_redirect($referer);
	}

	function on_admin_post_sethometeam()
	{
		if(!$this->acl_user_can('manage_teams'))
			wp_die( __('Cheatin&#8217; uh?') );

		check_admin_referer('wp-clanwars-sethometeam');

		$referer = $_REQUEST['_wp_http_referer'];

		extract(\WP_Clanwars\Utils::extract_args($_REQUEST, array('id' => array())));

		$error = \WP_Clanwars\Teams::set_hometeam($id);

		wp_redirect($referer);
	}

	function on_add_team()
	{
		return $this->team_editor(__('New Team', WP_CLANWARS_TEXTDOMAIN), 'wp-clanwars-addteam', __('Add Team', WP_CLANWARS_TEXTDOMAIN));
	}

	function on_edit_team()
	{
		$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

		return $this->team_editor(__('Edit Team', WP_CLANWARS_TEXTDOMAIN), 'wp-clanwars-editteam', __('Update Team', WP_CLANWARS_TEXTDOMAIN), $id);
	}

	function team_editor($page_title, $page_action, $page_submit, $team_id = 0)
	{
		$defaults = array('title' => '', 'logo' => 0, 'country' => '', 'home_team' => 0, 'action' => '');
		$data = array();

		if($team_id > 0) {
			$t = \WP_Clanwars\Teams::get_team(array('id' => $team_id));
			if(!empty($t)) {
				$data = (array)$t[0];
			}
		}

		extract(\WP_Clanwars\Utils::extract_args(stripslashes_deep($_POST), \WP_Clanwars\Utils::extract_args($data, $defaults)));

		$country_select = \WP_Clanwars\Utils::html_country_select_helper('name=country&id=country&show_popular=1&select=' . $country, false);

		$this->print_notices();

		$view = new \WP_Clanwars\View( 'edit_team' );
		$context = compact('page_title', 'page_action', 'page_submit',
					'team_id', 'title', 'logo', 'country', 'home_team', 'action',
					'country_select');
		$view->render( $context );
	}

	function on_load_manage_teams()
	{
		$act = isset($_GET['act']) ? $_GET['act'] : '';
		$id = isset($_GET['id']) ? $_GET['id'] : 0;

		// ACL checks on edit
		if($act == 'edit') {
			$t = \WP_Clanwars\Teams::get_team(array('id' => $id));

			if($id != 0 && empty($t))
				wp_die( __('Cheatin&#8217; uh?') );
		}

		if(sizeof($_POST)) {

			if(isset($_POST['title']) && !empty($_POST['title'])) {

				switch($act) {
					case 'add':
						if(\WP_Clanwars\Teams::add_team(stripslashes_deep($_POST))) {
							wp_redirect(admin_url('admin.php?page=wp-clanwars-teams&add=1'));
							exit();
						} else
							$this->add_notice(__('An error occurred.', WP_CLANWARS_TEXTDOMAIN), 'error');
					break;

					case 'edit':
						if(\WP_Clanwars\Teams::update_team($id, stripslashes_deep($_POST)) !== false) {
							wp_redirect(admin_url('admin.php?page=wp-clanwars-teams&update=1'));
							exit();
						} else
							$this->add_notice(__('An error occurred.', WP_CLANWARS_TEXTDOMAIN), 'error');
						break;
				}

			} else
				$this->add_notice(__('Team title is required field.', WP_CLANWARS_TEXTDOMAIN), 'error');

		}
	}

	function on_manage_teams()
	{
		$act = isset($_GET['act']) ? $_GET['act'] : '';
		$current_page = isset($_GET['paged']) ? $_GET['paged'] : 1;
		$limit = 10;

		switch($act) {
			case 'add':
				return $this->on_add_team();
				break;
			case 'edit':
				return $this->on_edit_team();
				break;
		}

		$teams = \WP_Clanwars\Teams::get_team('id=all&order=asc&orderby=title&limit=' . $limit . '&offset=' . ($limit * ($current_page-1)));
		$stat = \WP_Clanwars\Teams::get_team('id=all&limit=' . $limit, true);

		$page_links = paginate_links( array(
				'base' => add_query_arg('paged', '%#%'),
				'format' => '',
				'prev_text' => __('&laquo;'),
				'next_text' => __('&raquo;'),
				'total' => $stat['total_pages'],
				'current' => $current_page
		));

		$page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
				number_format_i18n( (($current_page - 1) * $limit) + 1 ),
				number_format_i18n( min( $current_page * $limit, $stat['total_items'] ) ),
				'<span class="total-type-count">' . number_format_i18n( $stat['total_items'] ) . '</span>',
				$page_links
		);

		$table_columns = array('cb' => '<input type="checkbox" />',
					'title' => __('Title', WP_CLANWARS_TEXTDOMAIN),
					'country' => __('Country', WP_CLANWARS_TEXTDOMAIN));

		if(isset($_GET['add'])) {
			$this->add_notice(__('Team is successfully added.', WP_CLANWARS_TEXTDOMAIN), 'updated');
		}

		if(isset($_GET['update'])) {
			$this->add_notice(__('Team is successfully updated.', WP_CLANWARS_TEXTDOMAIN), 'updated');
		}

		if(isset($_GET['delete'])) {
			$deleted = (int)$_GET['delete'];
			$this->add_notice(sprintf(_n('%d Team deleted.', '%d Teams deleted', $deleted, WP_CLANWARS_TEXTDOMAIN), $deleted), 'updated');
		}

		$this->print_notices();

		$view = new \WP_Clanwars\View( 'team_table' );

		$view->add_helper( 'print_table_header', array($this, 'print_table_header') );
		$view->add_helper( 'get_country_flag', array('\WP_Clanwars\Utils', 'get_country_flag') );
		$view->add_helper( 'get_country_title', array('\WP_Clanwars\Utils', 'get_country_title') );

		$context = compact('teams', 'page_links_text', 'table_columns');

		$view->render( $context );
	}

	/*
	 * Games Managment
	 */

	function on_admin_post_gamesop()
	{
		if(!$this->acl_user_can('manage_games')) {
			wp_die( __('Cheatin&#8217; uh?') );
		}

		check_admin_referer('wp-clanwars-gamesop');

		$referer = remove_query_arg(array('add', 'update', 'export'), $_REQUEST['_wp_http_referer']);

		$args = \WP_Clanwars\Utils::extract_args($_REQUEST, array('do_action' => '', 'do_action2' => '', 'items' => array()));
		extract($args);

		$action = !empty($do_action) ? $do_action : (!empty($do_action2) ? $do_action2 : '');

		if(!empty($items)) {

			switch($action) {
				case 'delete':
					$error = \WP_Clanwars\Games::delete_game($items);
					$referer = add_query_arg('delete', $error, $referer);
				break;
				case 'export':
					$game_id = current($items);
					$zip_archive = $this->export_game($game_id);

					if(is_wp_error($zip_archive)) {
						var_dump($zip_archive);
						die();
					}

					$zip_url = trailingslashit(site_url()) . str_replace(ABSPATH, '', $zip_archive);

					wp_redirect($zip_url);
					die();
				break;
			}

		}

		wp_redirect($referer);
	}

	function export_game($id)
	{
		global $wp_filesystem;
		WP_Filesystem();

		$id = (int)$id;
		$games = \WP_Clanwars\Games::get_game(array('id' => $id));
		$game = current($games);

		if(!$game) {
			return new WP_Error('plugin-error', 'Unable to find game.');
		}

		$upload_dir = wp_upload_dir();
		$export_dir = trailingslashit($upload_dir['basedir']) . WP_CLANWARS_EXPORTDIR;

		$game_data = \WP_Clanwars\Utils::extract_args($game, array(
			'title' => '', 'abbr' => '',
			'icon' => '', 'maplist' => array()
		));
		$zip_files = array();

		$maplist = \WP_Clanwars\Maps::get_map(array('game_id' => $game->id));

		if($game->icon != 0) {
			$attach = get_attached_file($game->icon);
			$mimetype = get_post_mime_type($game->icon);

			if(!empty($attach)) {
				$game_data['icon'] = array(
					'filename' => trim(str_replace($upload_dir['basedir'], '', $attach), '/\\'),
					'mimetype' => $mimetype
				);
				$zip_files[] = $attach;
			}
		}

		foreach($maplist as $map) {
			$map_data = array('title' => $map->title, 'screenshot' => '');

			if($map->screenshot != 0) {
				$attach = get_attached_file($map->screenshot);
				$mimetype = get_post_mime_type($map->screenshot);

				if(!empty($attach)) {
					$map_data['screenshot'] = array(
						'filename' => trim(str_replace($upload_dir['basedir'], '', $attach), '/\\'),
						'mimetype' => $mimetype
					);
					$zip_files[] = $attach;
				}
			}

			$game_data['maplist'][] = $map_data;
		}

		// define a folder for temporary index.json that we need to add into zip archive
		$index_file_dir = sprintf('%s/zip-' . md5(microtime(true)), $export_dir);

		// create folders for zip file
		$wp_filesystem->mkdir($export_dir);

		// create zip file
		$zip_path = sprintf('%s/gamepack-%s.zip', $export_dir, (strlen($game->abbr) ? $game->abbr : $game->id));

		// encode game data as JSON
		$index_json = json_encode($game_data);

		// clean up existing file first
		$wp_filesystem->delete($zip_path);

		// Zip can use a lot of memory, but not this much hopefully
		/** This filter is documented in wp-admin/admin.php */
		@ini_set( 'memory_limit', apply_filters( 'admin_memory_limit', WP_MAX_MEMORY_LIMIT ) );

		// use pecl ZipArchive if available
		if(class_exists('ZipArchive')) {
			$zip_acrhive = new ZipArchive();

			// open archive
			if($zip_acrhive->open($zip_path, ZIPARCHIVE::CREATE) !== true) {
				return new WP_Error('plugin-error', 'Failed to open ZIP file. Reason: ' . $zip_acrhive->getStatusString());
			}

			// zip index.json first
			$zip_acrhive->addFromString(WP_CLANWARS_ZIPINDEX, $index_json);

			// zip all images
			foreach($zip_files as $file) {
				$localname = trim(str_replace($upload_dir['basedir'], '', $file), '/\\');
				$zip_acrhive->addFile($file, $localname);
			}

			// close archive
			$zip_acrhive->close();
		} else {
			// fallback to PclZip
			$zip_acrhive = new PclZip($zip_path);

			// PclZip does not support adding files from memory
			$index_file_json = trailingslashit($index_file_dir) . WP_CLANWARS_ZIPINDEX;
			$wp_filesystem->mkdir($index_file_dir);
			$wp_filesystem->put_contents($index_file_json, $index_json);

			// zip index.json first
			$zip_status = $zip_acrhive->create($index_file_json, PCLZIP_OPT_REMOVE_PATH, $index_file_dir);

			// zip all images
			$zip_status = $zip_acrhive->add($zip_files, PCLZIP_OPT_REMOVE_PATH, $upload_dir['basedir']);

			// remove temp folder
			$wp_filesystem->rmdir($index_file_dir, true);

			if($zip_status === 0) {
				return new WP_Error('zip-error', 'Failed to ZIP files. Reason: ' . $zip_acrhive->errorInfo(true));
			}
		}

		return $zip_path;
	}

	function _import_image($p, $zip_dir) {
		global $wp_filesystem;

		if(empty($p)) return 0;

		$upload_dir = wp_upload_dir();
		$pathinfo = pathinfo($p['filename']);
		$file_name = $pathinfo['basename'];

		$zip_file_path = trailingslashit($zip_dir) . $p['filename'];
		$save_file_path = trailingslashit($upload_dir['path']) . wp_unique_filename($upload_dir['path'], $file_name);
		$file_url = trailingslashit(site_url()) . str_replace(ABSPATH, '', $save_file_path);

		if(!$wp_filesystem->move( $zip_file_path, $save_file_path )) {
			return 0;
		}

		$title = basename($file_name, $pathinfo['extension']);
		$attach = array('guid' => $file_url,
						'post_title' => sanitize_title($title),
						'post_status' => 'publish',
						'post_content' => '',
						'post_mime_type' => $p['mimetype']);
		$attach_id = wp_insert_attachment($attach, $save_file_path);

		if(!empty($attach_id)) {
			$metadata = wp_generate_attachment_metadata($attach_id, $save_file_path);

			if(!empty($metadata))
				wp_update_attachment_metadata($attach_id, $metadata);

			return $attach_id;
		}
		return 0;
	}

	function import_game($zip_file) {
		global $wp_filesystem;
		WP_Filesystem();

		$upload_dir = wp_upload_dir();
		$export_dir = trailingslashit($upload_dir['basedir']) . WP_CLANWARS_EXPORTDIR;
		$unzip_dir = sprintf('%s/unzip-' . md5(microtime(true)), $export_dir);

		$clean_unzip_dir = function () use ($wp_filesystem, $unzip_dir) {
			$wp_filesystem->rmdir($unzip_dir, true);
		};

		$wp_filesystem->mkdir($export_dir);
		$wp_filesystem->mkdir($unzip_dir);

		$result = unzip_file($zip_file, $unzip_dir);

		if(!$result) {
			$clean_unzip_dir();
			return new WP_Error('plugin-error', 'Unable to unzip file.');
		}

		$index_file = trailingslashit($unzip_dir) . WP_CLANWARS_ZIPINDEX;
		if(!file_exists($index_file)) {
			$clean_unzip_dir();
			return new WP_Error('plugin-error', 'Index file is not found in ZIP.');
		}

		$game_data = @json_decode( $wp_filesystem->get_contents($index_file) );

		if(!is_object($game_data)) {
			$clean_unzip_dir();
			return new WP_Error('plugin-error', 'Corrupted or missing contents from ZIP file.');
		}

		$game_data = \WP_Clanwars\Utils::extract_args($game_data, array(
			'title' => '', 'abbr' => '',
			'icon' => '', 'maplist' => array()
		));

		if(empty($game_data['title'])) {
			$clean_unzip_dir();
			return new WP_Error('plugin-error', 'Corrupted or missing contents from ZIP file.');
		}

		$p = $game_data;
		$p['icon'] = $this->_import_image((array)$p['icon'], $unzip_dir);
		$maplist = $p['maplist'];
		unset($p['maplist']);

		$game_id = \WP_Clanwars\Games::add_game($p);

		if(empty($game_id)) {
			$clean_unzip_dir();
			return new WP_Error('plugin-error', 'Failed to add game.');
		}

		foreach($maplist as $map) {
			$p = (array)$map;
			$p['screenshot'] = $this->_import_image((array)$p['screenshot'], $unzip_dir);
			$p['game_id'] = $game_id;

			if(!empty($p['title'])) {
				\WP_Clanwars\Maps::add_map($p);
			}
		}

		$clean_unzip_dir();

		return true;
	}

	function on_add_game()
	{
		return $this->game_editor(__('New Game', WP_CLANWARS_TEXTDOMAIN), 'wp-clanwars-addgame', __('Add Game', WP_CLANWARS_TEXTDOMAIN));
	}

	function on_edit_game()
	{
		$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

		return $this->game_editor(__('Edit Game', WP_CLANWARS_TEXTDOMAIN), 'wp-clanwars-editgame', __('Update Game', WP_CLANWARS_TEXTDOMAIN), $id);
	}

	function on_load_manage_games()
	{
		$act = isset($_GET['act']) ? $_GET['act'] : '';
		$id = isset($_GET['id']) ? $_GET['id'] : 0;
		$game_id = isset($_GET['game_id']) ? $_GET['game_id'] : 0;
		$die = false;

		// Check game or map is really exists
		if($act == 'add' && !$this->acl_user_can('manage_game', 'all')) {
			$die = true;
		}
		else if($act == 'edit' || $act == 'maps' || $act == 'addmap') {

			$g = \WP_Clanwars\Games::get_game(array('id' =>
					($act == 'maps' || $act == 'addmap' ? $game_id : $id)
				));

			$die = empty($g) || !$this->acl_user_can('manage_game', $g[0]->id);

		} else if($act == 'editmap') {

			$m = \WP_Clanwars\Maps::get_map(array('id' => $id));
			$die = empty($m);
		}

		if($die)
			wp_die( __('Cheatin&#8217; uh?') );

		if(sizeof($_POST)) {

			$edit_maps_errors = array(
				self::ErrorDatabase => __('Database error.', WP_CLANWARS_TEXTDOMAIN),
				self::ErrorOK => __('The game is updated.', WP_CLANWARS_TEXTDOMAIN),
				self::ErrorUploadMaxFileSize => __('The uploaded file exceeds the <code>upload_max_filesize</code> directive in <code>php.ini</code>.', WP_CLANWARS_TEXTDOMAIN),
				self::ErrorUploadHTMLMaxFileSize => __('The uploaded file exceeds the <em>MAX_FILE_SIZE</em> directive that was specified in the HTML form.', WP_CLANWARS_TEXTDOMAIN),
				self::ErrorUploadPartially => __('The uploaded file was only partially uploaded.', WP_CLANWARS_TEXTDOMAIN),
				self::ErrorUploadNoFile => __('No file was uploaded.', WP_CLANWARS_TEXTDOMAIN),
				self::ErrorUploadMissingTemp => __('Missing a temporary folder.', WP_CLANWARS_TEXTDOMAIN),
				self::ErrorUploadDiskWrite => __('Failed to write file to disk.', WP_CLANWARS_TEXTDOMAIN),
				self::ErrorUploadStoppedByExt => __('File upload stopped by extension.', WP_CLANWARS_TEXTDOMAIN),
				self::ErrorUploadFileTypeNotAllowed => __('File type does not meet security guidelines. Try another.', WP_CLANWARS_TEXTDOMAIN)
			);

			switch($act) {
				case 'add':

					$defaults = array('title' => '', 'abbr' => '', 'icon' => 0);
					$data = \WP_Clanwars\Utils::extract_args(stripslashes_deep($_POST), $defaults);
					extract($data);

					if(!empty($title)) {

						$data['icon'] = $this->handle_upload('icon_file');

						if($data['icon'] == self::ErrorUploadNoFile)
							$data['icon'] = 0;

						if($data['icon'] >= 0) {

							if(\WP_Clanwars\Games::add_game($data)) {
								wp_redirect(admin_url('admin.php?page=wp-clanwars-games&add=1'));
								exit();
							} else
								$this->add_notice(__('An error occurred.', WP_CLANWARS_TEXTDOMAIN), 'error');
						} else
							$this->add_notice($edit_maps_errors[$attach_id], 'error');


					} else
						$this->add_notice(__('Game title is required field.', WP_CLANWARS_TEXTDOMAIN), 'error');
				break;

				case 'edit':
					$defaults = array('title' => '', 'abbr' => '', 'delete_image' => false);
					$data = \WP_Clanwars\Utils::extract_args(stripslashes_deep($_POST), $defaults);
					extract($data);

					unset($data['delete_image']);

					if(!empty($title)) {

						if(!empty($delete_image))
							$data['icon'] = 0;

						$attach_id = $this->handle_upload('icon_file');

						if($attach_id == self::ErrorUploadNoFile)
							$attach_id = 0;
						else if($attach_id > 0)
							$data['icon'] = $attach_id;

						if($attach_id >= 0) {

							if(\WP_Clanwars\Games::update_game($id, $data) !== false) {
								wp_redirect(admin_url('admin.php?page=wp-clanwars-games&update=1'));
								exit();
							} else
								$this->add_notice(__('An error occurred.', WP_CLANWARS_TEXTDOMAIN), 'error');

						} else
							$this->add_notice($edit_maps_errors[$attach_id], 'error');

					} else
						$this->add_notice(__('Game title is required field.', WP_CLANWARS_TEXTDOMAIN), 'error');
					break;

				case 'addmap':
					$defaults = array('title' => '', 'game_id' => 0, 'id' => 0);
					$data = \WP_Clanwars\Utils::extract_args(stripslashes_deep($_POST), $defaults);
					extract($data);

					if(!empty($title)) {

						$attach_id = $this->handle_upload('screenshot_file');

						if($attach_id == self::ErrorUploadNoFile)
							$attach_id = 0;

						if($attach_id >= 0) {

							if(\WP_Clanwars\Maps::add_map(array('title' => $title, 'screenshot' => $attach_id, 'game_id' => $game_id)) !== false) {
								wp_redirect(admin_url(sprintf('admin.php?page=wp-clanwars-games&act=maps&game_id=%d&add=1', $game_id)));
								exit();
							} else
								$this->add_notice(__('An error occurred.', WP_CLANWARS_TEXTDOMAIN), 'error');

						} else
							$this->add_notice($edit_maps_errors[$attach_id], 'error');

					} else
						$this->add_notice(__('Map title is required field.', WP_CLANWARS_TEXTDOMAIN), 'error');

					break;

				case 'editmap':
					$defaults = array('title' => '', 'game_id' => 'all', 'id' => 0, 'delete_image' => false);
					$data = \WP_Clanwars\Utils::extract_args(stripslashes_deep($_POST), $defaults);
					extract($data);

					$update_data = array('title' => $title);

					if(!empty($title)) {

						if(!empty($delete_image))
							$update_data['screenshot'] = 0;

						$attach_id = $this->handle_upload('screenshot_file');

						if($attach_id == self::ErrorUploadNoFile)
							$attach_id = 0;
						else if($attach_id > 0)
							$update_data['screenshot'] = $attach_id;

						if($attach_id >= 0) {

							if(\WP_Clanwars\Maps::update_map($id, $update_data) !== false) {
								wp_redirect(admin_url(sprintf('admin.php?page=wp-clanwars-games&act=maps&game_id=%d&update=1', $game_id)));
								exit();
							} else
								$this->add_notice(__('An error occurred.', WP_CLANWARS_TEXTDOMAIN), 'error');

						} else
							$this->add_notice($edit_maps_errors[$attach_id], 'error');

					} else
						$this->add_notice(__('Map title is required field.', WP_CLANWARS_TEXTDOMAIN), 'error');

					break;
			}

		}
	}

	function on_manage_games()
	{
		$act = isset($_GET['act']) ? $_GET['act'] : '';
		$current_page = isset($_GET['paged']) ? $_GET['paged'] : 1;
		$filter_games = $this->acl_user_can('which_games');
		$limit = 10;

		switch($act) {
			case 'add':
				return $this->on_add_game();
				break;
			case 'edit':
				return $this->on_edit_game();
				break;
			case 'maps':
				return $this->on_edit_maps();
				break;
			case 'addmap':
				return $this->on_add_map();
				break;
			case 'editmap':
				return $this->on_edit_map();
				break;
		}

		$games = \WP_Clanwars\Games::get_game(array(
			'id' => $filter_games,
			'orderby' => 'title', 'order' => 'asc',
			'limit' => $limit, 'offset' => ($limit * ($current_page-1))
		));
		$stat = \WP_Clanwars\Games::get_game(array('id' => $filter_games, 'limit' => $limit), true);

		$show_add_button = $this->acl_user_can('manage_game', 'all');

		// pre-populate games with icons
		foreach ($games as $game) {
			$game->icon_url = wp_get_attachment_url($game->icon);
		}

		$page_links = paginate_links( array(
				'base' => add_query_arg('paged', '%#%'),
				'format' => '',
				'prev_text' => __('&laquo;'),
				'next_text' => __('&raquo;'),
				'total' => $stat['total_pages'],
				'current' => $current_page
		));

		$page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
				number_format_i18n( (($current_page - 1) * $limit) + 1 ),
				number_format_i18n( min( $current_page * $limit, $stat['total_items'] ) ),
				'<span class="total-type-count">' . number_format_i18n( $stat['total_items'] ) . '</span>',
				$page_links
		);

		$table_columns = array('cb' => '<input type="checkbox" />',
					  'title' => __('Title', WP_CLANWARS_TEXTDOMAIN),
					  'abbr' => __('Game tag', WP_CLANWARS_TEXTDOMAIN));

		if(isset($_GET['add'])) {
			$this->add_notice(__('Game is successfully added.', WP_CLANWARS_TEXTDOMAIN), 'updated');
		}

		if(isset($_GET['update'])) {
			$this->add_notice(__('Game is successfully updated.', WP_CLANWARS_TEXTDOMAIN), 'updated');
		}

		if(isset($_GET['delete'])) {
			$deleted = (int)$_GET['delete'];
			$this->add_notice(sprintf(_n('%d Game deleted.', '%d Games deleted', $deleted, WP_CLANWARS_TEXTDOMAIN), $deleted), 'updated');
		}

		$this->print_notices();

		$view = new \WP_Clanwars\View( 'game_table' );

		$view->add_helper( 'print_table_header', array($this, 'print_table_header') );

		$context = compact( 'show_add_button', 'games', 'table_columns', 'page_links_text' );

		$view->render( $context );
	}

	function game_editor($page_title, $page_action, $page_submit, $game_id = 0)
	{
		$defaults = array('title' => '', 'icon' => 0, 'abbr' => '', 'action' => '');
		$game = new stdClass();

		if($game_id > 0) {
			$result = \WP_Clanwars\Games::get_game(array('id' => $game_id));
			if(!empty($result)) {
				$game = $result[0];
			}
		}

		$this->print_notices();

		$view = new \WP_Clanwars\View( 'edit_game' );

		$context = \WP_Clanwars\Utils::extract_args(stripslashes_deep($_POST), \WP_Clanwars\Utils::extract_args($game, $defaults));
		$context['attach'] = isset($game->icon) ? wp_get_attachment_image($game->icon, 'thumbnail') : '';
		$context += compact( 'page_title', 'page_action', 'page_submit', 'game_id' );

		$view->render( $context );
	}

	/*
	 * Maps managment
	 */

	function on_admin_post_deletemaps()
	{
		if(!$this->acl_user_can('manage_games'))
			wp_die( __('Cheatin&#8217; uh?') );

		check_admin_referer('wp-clanwars-deletemaps');

		$referer = remove_query_arg(array('add', 'update'), $_REQUEST['_wp_http_referer']);

		if($_REQUEST['do_action'] == 'delete' || $_REQUEST['do_action2'] == 'delete') {
			extract(\WP_Clanwars\Utils::extract_args($_REQUEST, array('delete' => array())));

			$error = \WP_Clanwars\Maps::delete_map($delete);
			$referer = add_query_arg('delete', $error, $referer);
		}

		wp_redirect($referer);
	}

	function on_edit_maps()
	{
		$game_id = isset($_GET['game_id']) ? (int)$_GET['game_id'] : 0;
		$current_page = isset($_GET['paged']) ? $_GET['paged'] : 1;
		$limit = 10;

		$maps = \WP_Clanwars\Maps::get_map('id=all&orderby=title&order=asc&game_id=' . $game_id . '&limit=' . $limit . '&offset=' . ($limit * ($current_page-1)));
		$stat = \WP_Clanwars\Maps::get_map('id=all&game_id=' . $game_id . '&limit=' . $limit, true);
		$game = current(\WP_Clanwars\Games::get_game(array('id' => $game_id)));

		foreach($maps as $map) {
			$map->attach = wp_get_attachment_image($map->screenshot, 'thumbnail');
		}

		$page_links = paginate_links( array(
				'base' => add_query_arg('paged', '%#%'),
				'format' => '',
				'prev_text' => __('&laquo;'),
				'next_text' => __('&raquo;'),
				'total' => $stat['total_pages'],
				'current' => $current_page
		));

		$page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
				number_format_i18n( (($current_page - 1) * $limit) + 1 ),
				number_format_i18n( min( $current_page * $limit, $stat['total_items'] ) ),
				'<span class="total-type-count">' . number_format_i18n( $stat['total_items'] ) . '</span>',
				$page_links
		);

		$table_columns = array(
			'cb' => '<input type="checkbox" />',
				'icon' => '',
				'title' => __('Title', WP_CLANWARS_TEXTDOMAIN)
		);

		if(isset($_GET['add'])) {
			$this->add_notice(__('Map is successfully added.', WP_CLANWARS_TEXTDOMAIN), 'updated');
		}

		if(isset($_GET['update'])) {
			$this->add_notice(__('Map is successfully updated.', WP_CLANWARS_TEXTDOMAIN), 'updated');
		}

		if(isset($_GET['delete'])) {
			$deleted = (int)$_GET['delete'];
			$this->add_notice(sprintf(_n('%d Map deleted.', '%d Maps deleted', $deleted, WP_CLANWARS_TEXTDOMAIN), $deleted), 'updated');
		}

		$this->print_notices();

		$view = new \WP_Clanwars\View( 'map_table' );

		$view->add_helper( 'print_table_header', array($this, 'print_table_header') );

		$context = compact( 'table_columns', 'page_links_text', 'maps', 'game_id', 'game' );

		$view->render( $context );
	}

	function on_add_map()
	{
		$game_id = isset($_GET['game_id']) ? (int)$_GET['game_id'] : 0;

		$this->map_editor(__('Add Map', WP_CLANWARS_TEXTDOMAIN), 'wp-clanwars-addmap', __('Add Map', WP_CLANWARS_TEXTDOMAIN), $game_id);
	}

	function on_edit_map()
	{
		$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

		$this->map_editor(__('Edit Map', WP_CLANWARS_TEXTDOMAIN), 'wp-clanwars-editmap', __('Update Map', WP_CLANWARS_TEXTDOMAIN), 0, $id);
	}

	function map_editor($page_title, $page_action, $page_submit, $game_id, $id = 0)
	{
		$defaults = array('title' => '', 'screenshot' => 0, 'abbr' => '', 'action' => '');
		$data = array();

		if($id > 0) {
			$t = \WP_Clanwars\Maps::get_map(array('id' => $id, 'game_id' => $game_id));

			if(!empty($t)){
				$data = (array)$t[0];
				$game_id = $data['game_id'];
			}
		}

		extract(\WP_Clanwars\Utils::extract_args(stripslashes_deep($_POST), \WP_Clanwars\Utils::extract_args($data, $defaults)));

		$attach = wp_get_attachment_image($screenshot, 'thumbnail');

		$this->print_notices();

		$view = new \WP_Clanwars\View( 'edit_map' );

		$context = compact('page_title', 'page_action', 'page_submit', 'game_id', 'id',
			'attach', 'title', 'screenshot', 'abbr', 'action');

		$view->render( $context );
	}

	/*
	 * Matches managment
	 */

	function on_admin_post_deletematches()
	{
		if(!$this->acl_user_can('manage_matches'))
			wp_die( __('Cheatin&#8217; uh?') );

		check_admin_referer('wp-clanwars-deletematches');

		$referer = remove_query_arg(array('add', 'update'), $_REQUEST['_wp_http_referer']);

		if($_REQUEST['do_action'] == 'delete' || $_REQUEST['do_action2'] == 'delete') {
			extract(\WP_Clanwars\Utils::extract_args($_REQUEST, array('delete' => array())));

			$error = \WP_Clanwars\Matches::delete_match($delete);
			$referer = add_query_arg('delete', $error, $referer);
		}

		wp_redirect($referer);
	}

	// Get available games bundled with plugin
	// This function simply reads and decodes import/import.json
	// @return an array of avaialable games on success, otherwise false
	function get_available_games() {
		$content = file_get_contents(trailingslashit(WP_CLANWARS_IMPORTPATH) . 'import.json');

		if($content !== false) {
			return json_decode($content);
		}

		return false;
	}

	// Match game by title and or abbreviation
	// @param $title a game title
	// @param $abbr a game abbreviation (optional)
	// @param $objects a list of all available games (optional)
	// @return bool a game object matching $title or $abbr, otherwise false
	function is_game_installed($title, $abbr = '', $objects = false) {
		if(!is_array($objects)) {
			$objects = \WP_Clanwars\Games::get_game('');
		}

		foreach($objects as $p) {
			if(!empty($abbr) && preg_match('#' . preg_quote($abbr, '#') . '#i', $p->abbr)) {
				return $p;
			}

			if(!empty($title) && preg_match('#' . preg_quote($title, '#') . '#i', $p->title)) {
				return $p;
			}
		}

		return false;
	}

	function on_add_match()
	{
		return $this->match_editor(__('Add Match', WP_CLANWARS_TEXTDOMAIN), 'wp-clanwars-matches', __('Add Match', WP_CLANWARS_TEXTDOMAIN));
	}

	function on_edit_match()
	{
		$id = isset($_GET['id']) ? $_GET['id'] : 0;

		return $this->match_editor(__('Edit Match', WP_CLANWARS_TEXTDOMAIN), 'wp-clanwars-matches', __('Update Match', WP_CLANWARS_TEXTDOMAIN), $id);
	}

	function on_ajax_get_maps()
	{
		if(!$this->acl_user_can('manage_games') && !$this->acl_user_can('manage_matches')) {
			wp_die( __('Cheatin&#8217; uh?') );
		}

		$game_id = isset($_POST['game_id']) ? (int)$_POST['game_id'] : 0;

		if($game_id > 0) {
			$maps = \WP_Clanwars\Maps::get_map(array('game_id' => $game_id, 'order' => 'asc', 'orderby' => 'title'));

			for($i = 0; $i < sizeof($maps); $i++) {
				$url = wp_get_attachment_thumb_url($maps[$i]->screenshot);

				$maps[$i]->screenshot_url = !empty($url) ? $url : '';
			}

			echo json_encode($maps); die();
		}
	}

	function match_editor($page_title, $page_action, $page_submit, $id = 0)
	{
		$match = new stdClass();
		$current_time = \WP_Clanwars\Utils::current_time_fixed('timestamp', 0);

		$defaults = array('game_id' => 0,
			'title' => '',
			'post_id' => 0,
			'team1' => 0,
			'team2' => 0,
			'scores' => array(),
			'match_status' => 0,
			'action' => '',
			'description' => '',
			'external_url' => '',
			'date' => array('mm' => date('m', $current_time),
							'yy' => date('Y', $current_time),
							'jj' => date('j', $current_time),
							'hh' => date('H', $current_time),
							'mn' => date('i', $current_time)
				));

		if($id > 0) {
			$result = \WP_Clanwars\Matches::get_match(array('id' => $id));
			if(!empty($result)) {
				$match = $result[0];
				$match->date = mysql2date('U', $match->date);
				$match->scores = array();

				$rounds = \WP_Clanwars\Rounds::get_rounds($match->id);

				foreach($rounds as $round) {
					$match->scores[$round->group_n]['map_id'] = $round->map_id;
					$match->scores[$round->group_n]['round_id'][] = $round->id;
					$match->scores[$round->group_n]['team1'][] = $round->tickets1;
					$match->scores[$round->group_n]['team2'][] = $round->tickets2;
				}
			}
		}

		$num_comments = isset($match->post_id) ? get_comments_number($match->post_id) : 0;
		$match_statuses = $this->match_status;

		$games = \WP_Clanwars\Games::get_game(array('id' => $this->acl_user_can('which_games'), 'orderby' => 'title', 'order' => 'asc'));
		$teams = \WP_Clanwars\Teams::get_team('id=all&orderby=title&order=asc');

		$merged_data = \WP_Clanwars\Utils::extract_args(stripslashes_deep($_POST), \WP_Clanwars\Utils::extract_args($match, $defaults));
		$merged_data['date'] = \WP_Clanwars\Utils::date_array2time_helper($merged_data['date']);

		if(isset($_GET['update'])) {
			$this->add_notice(__('Match is successfully updated.', WP_CLANWARS_TEXTDOMAIN), 'updated');
		}

		$this->print_notices();

		$view = new \WP_Clanwars\View( 'edit_match' );

		$view->add_helper('html_date_helper', array('\WP_Clanwars\Utils', 'html_date_helper'));
		$view->add_helper('html_country_select_helper', array('\WP_Clanwars\Utils', 'html_country_select_helper'));

		// get screenshots
		$screenshots = get_post_gallery($match->post_id, false);
		if(!is_array($screenshots)) {
			$screenshots = new stdClass();
		}

		$context = compact('page_title', 'page_action', 'page_submit', 'num_comments', 'match_statuses', 'id', 'games', 'teams', 'screenshots');
		$context += $merged_data;

		$view->render( $context );
	}

	function quick_pick_team($title, $country) {
		$team = \WP_Clanwars\Teams::get_team(array('title' => $title, 'limit' => 1));
		$team_id = 0;
		if(empty($team)) {
			$new_team_id = \WP_Clanwars\Teams::add_team(array('title' => $title, 'country' => $country));
			if($new_team_id !== false)
				$team_id = $new_team_id;
		} else {
			$team_id = $team[0]->id;
		}

		return $team_id;
	}

	function on_load_manage_matches()
	{
		$id = isset($_GET['id']) ? $_GET['id'] : 0;
		$act = isset($_GET['act']) ? $_GET['act'] : '';
		$media_options = array();

		// Check match is really exists
		if($act == 'edit') {
			$m = \WP_Clanwars\Matches::get_match(array('id' => $id));

			if($id != 0 && empty($m))
				wp_die( __('Cheatin&#8217; uh?') );

			if(!$this->acl_user_can('manage_game', $m[0]->game_id))
				wp_die( __('Cheatin&#8217; uh?') );

			$media_options['post'] = $m[0]->post_id;
		}

		wp_enqueue_media($media_options);
		wp_enqueue_script('wp-cw-matches');
		wp_enqueue_script('wp-cw-screenshots');
		wp_localize_script('wp-cw-matches',
				'wpCWL10n',
				array(
					'plugin_url' => WP_CLANWARS_URL,
					'addRound' => __('Add Round', WP_CLANWARS_TEXTDOMAIN),
					'excludeMap' => __('Exclude map from match', WP_CLANWARS_TEXTDOMAIN),
					'removeRound' => __('Remove round', WP_CLANWARS_TEXTDOMAIN),
					'addScreenshots' => __('Add screenshots', WP_CLANWARS_TEXTDOMAIN)
				)
			);

		if(sizeof($_POST) > 0)
		{

			if(isset($_POST['game_id']) && !$this->acl_user_can('manage_game', $_POST['game_id']))
				wp_die( __('Cheatin&#8217; uh?') );

			switch($act) {

				case 'add':

					extract(\WP_Clanwars\Utils::extract_args(stripslashes_deep($_POST), array(
						'game_id' => 0,
						'title' => '',
						'description' => '',
						'external_url' => '',
						'date' => \WP_Clanwars\Utils::current_time_fixed('timestamp', 0),
						'team1' => 0,
						'team2' => 0,
						'scores' => array(),
						'new_team_title' => '',
						'new_team_country' => '',
						'match_status' => 0,
						'screenshots' => array()
						)));

					$date = \WP_Clanwars\Utils::date_array2time_helper($date);

					if(!empty($new_team_title) && !empty($new_team_country)) {
						$pickteam = $this->quick_pick_team($new_team_title, $new_team_country);

						if($pickteam > 0)
							$team2 = $pickteam;
					}

					$match_id = \WP_Clanwars\Matches::add_match(array(
							'title' => $title,
							'description' => $description,
							'external_url' => $external_url,
							'date' => date('Y-m-d H:i:s', $date),
							'post_id' => 0,
							'team1' => $team1,
							'team2' => $team2,
							'game_id' => $game_id,
							'match_status' => $match_status,
							'description' => $description
					));

					if($match_id) {
						foreach($scores as $round_group => $r) {
							for($i = 0; $i < sizeof($r['team1']); $i++) {
								\WP_Clanwars\Rounds::add_round(array('match_id' => $match_id,
									'group_n' => abs($round_group),
									'map_id' => $r['map_id'],
									'tickets1' => $r['team1'][$i],
									'tickets2' => $r['team2'][$i]
									));
							}
						}

						\WP_Clanwars\Matches::update_match_post($match_id, $screenshots);

						wp_redirect(admin_url('admin.php?page=wp-clanwars-matches&add=1'));
						exit();
					} else {
						$this->add_notice(__('An error occurred.', WP_CLANWARS_TEXTDOMAIN), 'error');
					}

				break;

			case 'edit':

					extract(\WP_Clanwars\Utils::extract_args(stripslashes_deep($_POST), array(
						'id' => 0,
						'game_id' => 0,
						'title' => '',
						'description' => '',
						'external_url' => '',
						'date' => \WP_Clanwars\Utils::current_time_fixed('timestamp', 0),
						'team1' => 0,
						'team2' => 0,
						'new_team_title' => '',
						'new_team_country' => '',
						'match_status' => 0,
						'scores' => array(),
						'screenshots' => array()
						)));

					$date = \WP_Clanwars\Utils::date_array2time_helper($date);

					if(!empty($new_team_title) && !empty($new_team_country)) {
						$pickteam = $this->quick_pick_team($new_team_title, $new_team_country);

						if($pickteam > 0)
							$team2 = $pickteam;
					}

					\WP_Clanwars\Matches::update_match($id, array(
							'title' => $title,
							'date' => date('Y-m-d H:i:s', $date),
							'team1' => $team1,
							'team2' => $team2,
							'game_id' => $game_id,
							'match_status' => $match_status,
							'description' => $description,
							'external_url' => $external_url
						));

					$rounds_not_in = array();

					foreach($scores as $round_group => $r) {
						for($i = 0; $i < sizeof($r['team1']); $i++) {
							$round_id = $r['round_id'][$i];
							$round_data = array('match_id' => $id,
									'group_n' => abs($round_group),
									'map_id' => $r['map_id'],
									'tickets1' => $r['team1'][$i],
									'tickets2' => $r['team2'][$i]
									);

							if($round_id > 0) {
								\WP_Clanwars\Rounds::update_round($round_id, $round_data);
								$rounds_not_in[] = $round_id;
							} else {
								$new_round = \WP_Clanwars\Rounds::add_round($round_data);
								if($new_round !== false)
									$rounds_not_in[] = $new_round;
							}
						}
					}

					\WP_Clanwars\Rounds::delete_rounds_not_in($id, $rounds_not_in);

					\WP_Clanwars\Matches::update_match_post($id, $screenshots);

					wp_redirect(admin_url('admin.php?page=wp-clanwars-matches&act=edit&id=' . $id . '&update=1'));
					exit();

				break;
			}
		}
	}

	function on_shortcode($atts) {
		extract(shortcode_atts(array('match_id' => 0), $atts));

		$match_id = (int)$match_id;
		if($match_id > 0) {
			return $this->on_match_shortcode($match_id);
		}

		return $this->on_browser_shortcode($atts);
	}

	function on_match_shortcode($match_id) {
		$matches = \WP_Clanwars\Matches::get_match(array('id' => $match_id, 'sum_tickets' => true));

		if(empty($matches)) {
			return __("<p>Match with id = $match_id has been removed.</p>", WP_CLANWARS_TEXTDOMAIN);
		}

		$match = $matches[0];
		$r = \WP_Clanwars\Rounds::get_rounds($match->id);
		$rounds = array();

		// group rounds by map
		foreach($r as $v) {
			if(!isset($rounds[$v->group_n])) {
				$rounds[$v->group_n] = array();
			}
			array_push($rounds[$v->group_n], $v);
		}

		$match_status_text = $this->match_status[$match->match_status];
		$team1_flag = \WP_Clanwars\Utils::get_country_flag($match->team1_country);
		$team2_flag = \WP_Clanwars\Utils::get_country_flag($match->team2_country);

		$view = new \WP_Clanwars\View( 'match_view' );

		$context = compact('match', 'rounds', 'match_status_text', 'team1_flag', 'team2_flag');

		return $view->render( $context, false );
	}

	function on_browser_shortcode($atts) {
		$output = '';

		extract(shortcode_atts(array('per_page' => 1), $atts));

		$per_page = abs($per_page);
		$current_page = max( 1, get_query_var('paged') );
		$now = \WP_Clanwars\Utils::current_time_fixed('timestamp');
		$current_game = isset($_GET['game']) ? $_GET['game'] : false;

		$games = \WP_Clanwars\Games::get_game('id=all&orderby=title&order=asc');

		$p = array(
			'limit' => $per_page,
			'order' => 'desc',
			'orderby' => 'date',
			'sum_tickets' => true,
			'game_id' => $current_game,
			'offset' => ($current_page-1) * $per_page
		);

		$matches = \WP_Clanwars\Matches::get_match($p, false);
		$stat = \WP_Clanwars\Matches::get_match($p, true);
		$page_links = paginate_links(array(
			'prev_text' => __('&larr;'),
			'next_text' => __('&rarr;'),
			'total' => $stat['total_pages'],
			'current' => $current_page
		));

		$page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
				number_format_i18n( (($current_page - 1) * $per_page) + 1 ),
				number_format_i18n( min( $current_page * $per_page, $stat['total_items'] ) ),
				'<span class="total-type-count">' . number_format_i18n( $stat['total_items'] ) . '</span>',
				$page_links
		);

		$output .= '<ul class="wp-clanwars-filter">';

		$obj = new stdClass();
		$obj->id = 0;
		$obj->title = __('All', WP_CLANWARS_TEXTDOMAIN);
		$obj->abbr = __('All');
		$obj->icon = 0;

		array_unshift($games, $obj);

		$this_url = remove_query_arg(array('paged', 'game'));
		for($i = 0; $i < sizeof($games); $i++) :
			$game = $games[$i];
			$link = ($game->id == 0) ? $this_url : add_query_arg('game', $game->id, $this_url);

			$output .= '<li' . ($game->id == $current_game ? ' class="selected"' : '') . '><a href="' . $link . '" title="' . esc_attr($game->title) . '">' . esc_html($game->abbr) . '</a></li>';
		endfor;

		$output .= '</ul>';

		$output .= '<ul class="wp-clanwars-list">';

		// generate table content
		foreach($matches as $index => $match) {

			$output .= '<li class="match ' . ($index % 2 == 0 ? 'even' : 'odd') . '">';

			// output match status
			$is_upcoming = false;
			$t1 = $match->team1_tickets;
			$t2 = $match->team2_tickets;
			$wld_class = $t1 == $t2 ? 'draw' : ($t1 > $t2 ? 'win' : 'loss');
			$date = mysql2date(get_option('date_format') . ', ' . get_option('time_format'), $match->date);
			$timestamp = mysql2date('U', $match->date);

			$is_upcoming = $timestamp > $now;
			$is_playing = ($now > $timestamp && $now < $timestamp + 3600) && ($t1 == 0 && $t2 == 0);

			if($is_upcoming) :
				$output .= '<div class="upcoming">' . __('Upcoming', WP_CLANWARS_TEXTDOMAIN) . '</div>';
			elseif($is_playing) :
				$output .= '<div class="playing">' . __('Playing', WP_CLANWARS_TEXTDOMAIN) . '</div>';
			else :
				$output .= '<div class="scores ' . $wld_class . '">' . sprintf(__('%d:%d', WP_CLANWARS_TEXTDOMAIN), $t1, $t2) . '</div>';
			endif;

			// teams
			$output .= '<div class="wrap">';

			// output game icon
			$game_icon = wp_get_attachment_url($match->game_icon);

			if($game_icon !== false) {
				$output .= '<img src="' . $game_icon . '" alt="' . esc_attr($match->game_title) . '" class="icon" /> ';
			}

			$team2_title = esc_html($match->team2_title);

			if($match->post_id != 0)
				$team2_title = '<a href="' . get_permalink($match->post_id) . '" title="' . esc_attr($match->title) . '">' . $team2_title . '</a>';

			$output .= '<div class="opponent-team">' .
						\WP_Clanwars\Utils::get_country_flag($match->team2_country) . ' ' . $team2_title .
					'</div>';
			//$output .= '<div class="home-team">' . \WP_Clanwars\Utils::get_country_flag($match->team1_country, true) . ' ' . esc_html($match->team1_title) . '</div>';

			$output .= '<div class="date">' . esc_html($date)  . '</div>';

			$rounds = array();
			$r = \WP_Clanwars\Rounds::get_rounds($match->id);
			foreach($r as $v) {
				if(isset($rounds[$v->group_n]))
					continue;

				$image = wp_get_attachment_image_src($v->screenshot);

				if(!empty($image)) {
					$rounds[$v->group_n] = '<a href="' . esc_attr($image[0]) . '#' . $image[1] . 'x' . $image[2] . '" title="' . esc_attr($v->title) . '">' . esc_html($v->title) . '</a>';
				} else
					$rounds[$v->group_n] = $v->title;
			}

			if(!empty($rounds)) {
				$maplist = implode(', ', array_values($rounds));
				$output .= '<div class="maplist">' . $maplist . '</div>';
			}

			$output .= '</div>';

			$output .= '</li>';

		}

		$output .= '</ul>';

		$output .= '<div class="wp-clanwars-pagination">' .$page_links_text . '</div>';

		return $output;
	}

	function on_manage_matches()
	{
		$act = isset($_GET['act']) ? $_GET['act'] : '';
		$current_page = isset($_GET['paged']) ? $_GET['paged'] : 1;
		$limit = 10;
		$game_filter = $this->acl_user_can('which_games');

		switch($act) {
			case 'add':
				return $this->on_add_match();
				break;
			case 'edit':
				return $this->on_edit_match();
				break;
		}

		$stat_condition = array(
			'id' => 'all',
			'game_id' => $game_filter,
			'limit' => $limit
		);

		$condition = array(
			'id' => 'all', 'game_id' => $game_filter, 'sum_tickets' => true,
			'orderby' => 'date', 'order' => 'desc',
			'limit' => $limit, 'offset' => ($limit * ($current_page-1))
		);

		$matches = \WP_Clanwars\Matches::get_match($condition);
		$match_statuses = $this->match_status;
		$stat = \WP_Clanwars\Matches::get_match($stat_condition, true);

		// populate games with urls for icons
		foreach ($matches as $match) {
			$match->game_icon_url = wp_get_attachment_url($match->game_icon);
		}

		$page_links = paginate_links( array(
				'base' => add_query_arg('paged', '%#%'),
				'format' => '',
				'prev_text' => __('&laquo;'),
				'next_text' => __('&raquo;'),
				'total' => $stat['total_pages'],
				'current' => $current_page
		));

		$page_links_text = sprintf( '<span class="displaying-num">' . __( 'Displaying %s&#8211;%s of %s' ) . '</span>%s',
				number_format_i18n( (($current_page - 1) * $limit) + 1 ),
				number_format_i18n( min( $current_page * $limit, $stat['total_items'] ) ),
				'<span class="total-type-count">' . number_format_i18n( $stat['total_items'] ) . '</span>',
				$page_links
		);

		$table_columns = array('cb' => '<input type="checkbox" />',
				'title' => __('Title', WP_CLANWARS_TEXTDOMAIN),
				'game_title' => __('Game', WP_CLANWARS_TEXTDOMAIN),
				'date' => __('Date', WP_CLANWARS_TEXTDOMAIN),
				'match_status' => __('Match status', WP_CLANWARS_TEXTDOMAIN),
				'team1' => __('Team 1', WP_CLANWARS_TEXTDOMAIN),
				'team2' => __('Team 2', WP_CLANWARS_TEXTDOMAIN),
				'tickets' => __('Tickets', WP_CLANWARS_TEXTDOMAIN));

		if(isset($_GET['add'])) {
			$this->add_notice(__('Match is successfully added.', WP_CLANWARS_TEXTDOMAIN), 'updated');
		}

		if(isset($_GET['delete'])) {
			$deleted = (int)$_GET['delete'];
			$this->add_notice(sprintf(_n('%d Match deleted.', '%d Matches deleted', $deleted, WP_CLANWARS_TEXTDOMAIN), $deleted), 'updated');
		}

		$this->print_notices();

		$view = new \WP_Clanwars\View( 'match_table' );

		$view->add_helper( 'print_table_header', array($this, 'print_table_header') );
		$view->add_helper( 'get_country_flag', array('\WP_Clanwars\Utils', 'get_country_flag') );

		$context = compact('table_columns', 'page_links_text', 'matches', 'match_statuses');
		$view->render($context);
	}

	function on_admin_post_settings() {
		global $wpdb;

		if(!current_user_can('manage_options'))
			wp_die(__('Cheatin&#8217; uh?'));

		check_admin_referer('wp-clanwars-settings');

		if(isset($_POST['category'])) {
			update_option(WP_CLANWARS_CATEGORY, (int)$_POST['category']);
		}

		// keep default styles always enabled on jumpstarter
		$enable_default_styles = isset($_POST['enable_default_styles']) || $this->is_jumpstarter();

		update_option(WP_CLANWARS_DEFAULTCSS, $enable_default_styles);

		$url = add_query_arg('saved', 'true', $_POST['_wp_http_referer']);

		wp_redirect($url);
	}

	function on_admin_post_acl() {
		global $wpdb;

		if(!current_user_can('manage_options'))
			wp_die(__('Cheatin&#8217; uh?'));

		check_admin_referer('wp-clanwars-acl');

		if(isset($_POST['user'])) {
			$user_id = (int)$_POST['user'];
			$data = array();

			if(isset($_POST['permissions']))
				$data['permissions'] = $_POST['permissions'];

			if(isset($_POST['games']))
				$data['games'] = $_POST['games'];

			$this->acl_update($user_id, $data);
		}

		$url = add_query_arg('saved', 'true', $_POST['_wp_http_referer']);

		wp_redirect($url);
	}

	function on_admin_post_deleteacl() {
		global $wpdb;

		if(!current_user_can('manage_options'))
			wp_die(__('Cheatin&#8217; uh?'));

		check_admin_referer('wp-clanwars-deleteacl');

		extract(\WP_Clanwars\Utils::extract_args($_POST, array(
			'doaction' => '', 'doaction2' => '',
			'users' => array()
			)));

		$url = $_POST['_wp_http_referer'];

		if($doaction == 'delete' || $doaction2 == 'delete') {

			$users = array_unique(array_values($users));

			foreach($users as $key => $user_id)
				$this->acl_delete($user_id);

			$url = add_query_arg('saved', 'true', $url);
		}

		wp_redirect($url);
	}

	function on_admin_post_import() {
		if(!current_user_can('manage_options'))
			wp_die(__('Cheatin&#8217; uh?'));

		check_admin_referer('wp-clanwars-import');

		extract(\WP_Clanwars\Utils::extract_args($_POST, array('import' => '', 'items' => array())));

		$url = remove_query_arg(array('upload', 'import'), $_POST['_wp_http_referer']);

		switch($import) {
			case 'upload':
				if(isset($_FILES['userfile'])) {
					$file = $_FILES['userfile'];

					if($file['error'] == 0) {
						$result = $this->import_game($file['tmp_name']);
						$url = add_query_arg('import', ($result === true ? 'success' : 'error'), $url);
					} else {
						$url = add_query_arg('upload', 'error', $url);
					}
				}
				break;

			case 'available':

				$available_games = $this->get_available_games();
				$result = true;

				foreach($items as $item) {
					if(isset($available_games[$item])) {
						$r = $available_games[$item];
						$filename = trailingslashit(WP_CLANWARS_IMPORTPATH) . $r->package;
						$result = $this->import_game($filename);
						if($result !== true) {
							break;
						}
					}
				}

				$url = add_query_arg('import', ($result === true ? 'success' : 'error'), $url);

				break;
		}

		wp_redirect($url);
	}

	// Settings page hook
	function on_settings() {
		$table_columns = array(
			'cb' => '<input type="checkbox" />',
			'user_login' => __('User Login', WP_CLANWARS_TEXTDOMAIN),
			'user_permissions' => __('Permissions', WP_CLANWARS_TEXTDOMAIN)
		);

		$categories_dropdown = wp_dropdown_categories(array(
			'name' => 'category',
			'hierarchical' => true,
			'show_option_none' => __('None'),
			'hide_empty' => 0,
			'hide_if_empty' => 0,
			'selected' => get_option(WP_CLANWARS_CATEGORY, -1),
			'echo' => false
		));

		$enable_default_styles = get_option(WP_CLANWARS_DEFAULTCSS);

		// hide default styles checkbox on jumpstarter
		$hide_default_styles = $this->is_jumpstarter();

		$games = \WP_Clanwars\Games::get_game('id=all');
		$acl = $this->acl_get();
		$acl_keys = $this->acl_keys;

		$obj = new stdClass();
		$obj->id = 0;
		$obj->title = __('All', WP_CLANWARS_TEXTDOMAIN);
		$obj->abbr = __('All');
		$obj->icon = 0;

		array_unshift($games, $obj);

		$user_acl_info = array();

		foreach($acl as $user_id => $user_acl) {
			$user = get_userdata($user_id);
			$allowed_games = $this->acl_user_can('which_games', false, $user_id);
			$user_games = \WP_Clanwars\Games::get_game(array('id' => $allowed_games, 'orderby' => 'title', 'order' => 'asc'));

			// populate games with urls for icons
			foreach ($user_games as $game) {
				$game->icon_url = wp_get_attachment_url($game->icon);
			}

			$item = new stdClass();
			$item->user = $user;
			$item->user_acl = $user_acl;
			$item->user_games = $user_games;
			$item->allowed_games = $allowed_games;

			array_push($user_acl_info, $item);
		}

		$view = new \WP_Clanwars\View( 'settings' );
		$view->add_helper( 'print_table_header', array($this, 'print_table_header') );

		$context = compact('table_columns', 'games', 'acl_keys', 'user_acl_info',
							'categories_dropdown', 'enable_default_styles', 'hide_default_styles');

		$view->render( $context );
	}

	// Import page hook
	function on_import() {
		$import_list = $this->get_available_games();
		$installed_games = \WP_Clanwars\Games::get_game('');

		// mark installed games
		foreach($import_list as $game) {
			$game->is_installed = ($this->is_game_installed($game->title, $game->abbr, $installed_games) !== false);
		}

		if(isset($_GET['upload'])) {
			$this->add_notice(__('An upload error occurred while import.', WP_CLANWARS_TEXTDOMAIN), 'error');
		}

		if(isset($_GET['import'])) {
			$this->add_notice($_GET['import'] === 'success' ? __('File(s) successfully imported.', WP_CLANWARS_TEXTDOMAIN) : __('An error occurred while import.', WP_CLANWARS_TEXTDOMAIN), $_GET['import'] === 'success' ? 'updated' : 'error');
		}

		$this->print_notices();

		$view = new \WP_Clanwars\View( 'import' );

		$context = compact('import_list');

		$view->render( $context );
	}

}

/*
 * Initialization
 */

$wpClanWars = new WP_ClanWars();

register_activation_hook( __FILE__, array(&$wpClanWars, 'on_activate'));
register_deactivation_hook( __FILE__, array(&$wpClanWars, 'on_deactivate'));

/**
 * Uninstall function
 *
 * Proxing on_uninstall call of wpClanWars class
 * to prevent 'The script tried to execute a method or access a property of an
 * incomplete object.' error in the case of direct call to the class
 */

function wp_clanwars_uninstall()
{
	global $wpClanWars;

	$wpClanWars->on_uninstall();
}

register_uninstall_hook(__FILE__, 'wp_clanwars_uninstall');
?>