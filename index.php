<?php
/*
   Plugin Name:     LZ Dashboard
   Plugin URI:      http://www.layoutz.com.br/
   Description:     LZ Dashboard
   Version: 		1.0.0
   Author: 			LayoutzWeb
   Author URI: 		http://www.layoutz.com.br/
   Short Name: 		lz_dashboard
*/

/*******************************************************
 * DEFINES
******************************************************/
define('LZ_DASHBOARD_VERSION',   '1.0' );
define('LZ_DASHBOARD_SHORTNAME',   'lz_dashboard');

define('LZ_DASHBOARD_PATH',      osc_plugins_path(__FILE__).'lz_dashboard/' );

define('LZ_DASHBOARD_LIB_PATH',   LZ_DASHBOARD_PATH.'lib/');
define('LZ_DASHBOARD_CORE_PATH',  LZ_DASHBOARD_LIB_PATH.'core/');
define('LZ_DASHBOARD_FORMS_PATH', LZ_DASHBOARD_LIB_PATH.'forms/');

define('LZ_DASHBOARD_APP_PATH',   LZ_DASHBOARD_PATH.'application/');
define('LZ_DASHBOARD_ASSETS_PATH', LZ_DASHBOARD_APP_PATH.'assets/' );
define('LZ_DASHBOARD_MODEL_PATH', LZ_DASHBOARD_APP_PATH.'model/' );
define('LZ_DASHBOARD_VIEW_PATH',  LZ_DASHBOARD_APP_PATH.'view/' );
define('LZ_DASHBOARD_CONTROLLER_PATH',  LZ_DASHBOARD_APP_PATH.'controller/' );

define( 'LZ_DASHBOARD_UPLOAD_PATH', UPLOADS_PATH.'lz_dashboard/' );
define( 'LZ_DASHBOARD_UPLOAD_THUMB_PATH', LZ_DASHBOARD_UPLOAD_PATH.'thumbnails/' );

define('LZ_DASHBOARD_BASE_URL',   osc_plugin_url(osc_plugin_folder(__FILE__).'application/index.php') );
define('LZ_DASHBOARD_ASSETS_URL', LZ_DASHBOARD_BASE_URL.'assets/');

require LZ_DASHBOARD_CORE_PATH.'lz_controller.php';
require LZ_DASHBOARD_CORE_PATH.'lz_helper.php';
require LZ_DASHBOARD_CORE_PATH.'lz_model.php';
require LZ_DASHBOARD_CORE_PATH.'lz_object.php';
//require LZ_DASHBOARD_FORMS_PATH.'form_builder.php';
require LZ_DASHBOARD_LIB_PATH."helpers/upload.helper.php";

/*******************************************************
 * ROUTES
******************************************************/
osc_add_route('lz_dashboard/index', 'lz_dashboard/index', 'lz_dashboard/index', osc_plugin_folder(__FILE__).'application/index.php');


/********************************************************************************
 * CORE HOOKS
 *******************************************************************************/
/**
 * LZ Dashboard admin header
 */
function lz_dashboard_header(){
	
	osc_enqueue_script('footable');
	//osc_enqueue_script('footable-paginate');
	osc_enqueue_script('footable-filter');
	osc_enqueue_script('footable-sort');
	osc_enqueue_script('footable-template');
	osc_enqueue_script('footable-striping');
	osc_enqueue_script('infinite-scroll');
	osc_enqueue_script('toggles');
	osc_enqueue_script('perfect_scroll');
	osc_enqueue_script('jquery-fineuploader');
	osc_enqueue_script('colpick');
	osc_enqueue_script('lz_dashboard');
	osc_enqueue_style('footable',  LZ_DASHBOARD_BASE_URL.'assets/css/footable/footable.core.css' );
	osc_enqueue_style('footable-theme',  LZ_DASHBOARD_BASE_URL.'assets/css/footable/footable.metro.css' );
	osc_enqueue_style('icon-font', LZ_DASHBOARD_BASE_URL.'assets/css/lz_dashboard-embedded.css' );
	osc_enqueue_style('colpick', LZ_DASHBOARD_BASE_URL.'assets/css/colpick.css');
	osc_enqueue_style('toggles', LZ_DASHBOARD_BASE_URL.'assets/js/toggles/themes/toggles-dark.css' );
	osc_enqueue_style('perfect_scroll', LZ_DASHBOARD_BASE_URL.'assets/css/perfect-scrollbar.css' );
	osc_enqueue_style('lz_dashboard', LZ_DASHBOARD_BASE_URL.'assets/css/lz_dashboard.css');
}

/**
 * Sets plugin page title
 * @param string $string
 * @return string
 */
function lz_dashboard_plugin_title($string) {
	return sprintf(__('Lz Dashboard %s'), $string);
}

/**
 * Lz Dashboard init
 */
function lz_dashboard_init(){
	// ADD PLUGIN TITLE
	osc_add_filter('custom_plugin_title', 'lz_dashboard_plugin_title');
	osc_register_script('footable', 		LZ_DASHBOARD_BASE_URL.'assets/js/footable/footable.js' );
	osc_register_script('footable-paginate',LZ_DASHBOARD_BASE_URL.'assets/js/footable/footable.paginate.js' );
	osc_register_script('footable-filter', 	LZ_DASHBOARD_BASE_URL.'assets/js/footable/footable.filter.js' );
	osc_register_script('footable-sort', 	LZ_DASHBOARD_BASE_URL.'assets/js/footable/footable.sort.js' );
	osc_register_script('footable-template',LZ_DASHBOARD_BASE_URL.'assets/js/footable/footable.plugin.template.js' );
	osc_register_script('footable-striping',LZ_DASHBOARD_BASE_URL.'assets/js/footable/footable.striping.js' );
	osc_register_script('infinite-scroll', 	LZ_DASHBOARD_BASE_URL.'assets/js/infinite_scroll/jquery.infinitescroll.min.js' );
	osc_register_script('colpick', 			LZ_DASHBOARD_BASE_URL.'assets/js/colpick.js' );
	osc_register_script('toggles', 			LZ_DASHBOARD_BASE_URL.'assets/js/toggles/toggles.min.js');
	osc_register_script('perfect_scroll', 	LZ_DASHBOARD_BASE_URL.'assets/js/perfect-scrollbar.js');
	osc_register_script('lz_dashboard', 	LZ_DASHBOARD_BASE_URL.'assets/js/lz_dashboard.js');
}

/**
 * Run a controller action
 *
 * @param string $do
 * @param plugin $plugin
 * @param array $params
 */
function lz_dashboard_run($do, $plugin, array $params = array() ){
	Params::setParam('do', $do);
	Params::setParam('plugin', $plugin);
	Params::setParam('lzds', $params);
	osc_run_hook('ajax_admin_lzds');
}

/**
 * Router to Lz Dashboard Application
 */
function lz_dashboard_ajax(){
	require LZ_DASHBOARD_APP_PATH.'index.php';
}

function lz_dashboard_do($action, $plugin, $params = array()){
	if( preg_match('/\//', $action)){
		$parts = explode('/',$action);
		$action = $parts[0];
		$method = $parts[1];
	}

	$controller = lz_dashboard_get_action( $action, $plugin );
	
	if( !empty($controller)){
		return $controller->$method($params);
	}
	return false;
}

function lz_dashboard_url($do, $plugin, $params = array()){
	$url = osc_ajax_hook_url('lzds', array('&do'=>$do,'plugin'=>$plugin));
	if( !empty($params)){
		$url .= '&'.http_build_query($params);
	}
	return $url;
}

function lz_dashboard_get_action( $action, $plugin ){

	if( !empty($plugin) ){
		$controller = osc_plugin_path($plugin.'/').'controller/'.$action.'.php';
	} else {
		$controller = LZ_DASHBOARD_CONTROLLER_PATH.$action.'.php';
	}
	
	if( file_exists($controller) ){
	
		$className = 'LzDashboard';
		if( $action !== 'dashboard' ){
			if( !empty($plugin) ){
				$className = implode('', array_map( 'ucfirst', explode('_', $plugin) ) );
			}
			$className .= ucfirst($action);
		}
	
		if( !class_exists($className)){
			require $controller;
		}
		
		return new $className();
	}
	return false;
}


/**
 * Deletes a plugins settings from lz_dashboard
 *
 * @param string $plugin
 * @return boolean
 */
function lz_dashboard_unistall_plugin($plugin, $drops = array() ){
	$current_data = osc_get_preference('lz_plugins', 'lz_dashboard');
	if( !empty($current_data)){
		$current_data = unserialize($current_data);
		if( isset($current_data[$plugin])){
			unset($current_data[$plugin]);
		}
	} else {
		$current_data = array();
	}
	
	if( osc_set_preference('lz_plugins', serialize($current_data), 'lz_dashboard') ){
		if( !empty($drops) ){
			$connection = DBConnectionClass::newInstance() ;
			$var 		= $connection->getOsclassDb();
			$conn       = new DBCommandClass( $var ) ;
			foreach( $drops as $table_name ){
				$conn->query(sprintf('DROP TABLE IF EXISTS %s', DB_TABLE_PREFIX.$table_name ));
			}
		}
		return true;
	}
	return false;
}

/**
 * Resgister a plugin to use the dashboard.
 *
 * @param unknown $data
 * @return boolean
 */
function lz_dashboard_register( $name, $sql_file = '' ){
	$info = osc_plugin_get_info($name.'/index.php');
	if( empty($info) ){
		throw new Exception('Lz Dashboard did not find any plugin with the name "'.$name.'"');
	}

	$current_data = osc_get_preference('lz_plugins', 'lz_dashboard');
	if( !empty($current_data)){
		$current_data = unserialize($current_data);
	} else {
		$current_data = array();
	}

	$current_data[$info['short_name']] = $info['plugin_name'];
	$current_data = serialize($current_data);
	
	if( osc_set_preference( 'lz_plugins', $current_data, 'lz_dashboard' ) ){
	
		if( !empty($sql_file)){
			$connection = DBConnectionClass::newInstance() ;
			$var 		= $connection->getOsclassDb();
			$conn       = new DBCommandClass( $var ) ;
			$path 		= osc_plugin_resource($name.'/'.$sql_file);
			$sql 		= file_get_contents($path);
			
			if( !$conn->importSQL($sql) ){
				throw new Exception( $conn->getErrorLevel().' - '.$conn->getErrorDesc() ) ;
			}
		}
		
		return true;
	}

	return false;
	
}

function lz_get_plugin_path($name){
	return osc_plugin_path(osc_plugin_folder($name.'/index.php'));
}

/**
 * Install LZ Dashboard
 */
function lz_dashboard_install(){
	return;
}

/**
 * Uninstall LZ Dashboard
 */
function lz_dashboard_uninstall(){
	
	$current_data = osc_get_preference('lz_plugins', 'lz_dashboard');
	if( !empty($current_data)){
		$current_data = unserialize($current_data);
		if( !empty($current_data) ){
			osc_add_flash_error_message( _m('There still plugins using LzDashboard, please uninstall all the plugins using LzDashboard before unistall LzDashboard.','lz_dashboard'),'admin');
			osc_redirect_to(osc_admin_base_url(true).'?page=plugins');
		}
	}
	
	if( file_exists(LZ_DASHBOARD_UPLOAD_PATH) ){
		@mkdir(LZ_DASHBOARD_UPLOAD_PATH, 0777);
	}
	if( file_exists(LZ_DASHBOARD_UPLOAD_THUMB_PATH) ){
		@mkdir(LZ_DASHBOARD_UPLOAD_THUMB_PATH, 0777);
	}
	Preference::newInstance()->dao->query(sprintf('DELETE FROM %st_preference WHERE s_section LIKE "lz_dashboard%"', DB_TABLE_PREFIX));
	return;
}

/**
 * Is LZ Dashboard page
 */
function lz_is_dashboard_page(){
	return (boolean)( Params::existParam('route') && Params::getParam('route') == 'lz_dashboard/index' );
}

/**
 * Configure LZ Dashboard
 */
function lz_dashboard_configure(){
	osc_redirect_to(osc_route_admin_url(LZ_DASHBOARD_SHORTNAME.'/index'));
}

function lz_dashboard_admin_menu_init(){
	$plugins = osc_get_preference('lz_plugins', LZ_DASHBOARD_SHORTNAME);
	if( !empty($plugins) ){
		$plugins = unserialize($plugins);
	}
	osc_add_admin_menu_page(
		__('LZ Dashboard', LZ_DASHBOARD_SHORTNAME),
		osc_route_admin_url(LZ_DASHBOARD_SHORTNAME.'/index'),
		'menu_lz_dashboard',
		'administrator'
	);
	foreach( $plugins as $name => $plugin ){
		osc_add_admin_submenu_page(
			'menu_lz_dashboard',
			$plugin,
			osc_route_admin_url(LZ_DASHBOARD_SHORTNAME.'/index').'#'.$name,
			'menu_'.$name,
			'administrator'
		);
	}
}

if( !function_exists('printR')){
	function printR( $data, $exit = false ){
		echo'<pre>'.print_r($data, true ).'</pre>';
		if( $exit ) exit;
		return;
	}
	
}

function toObject($d) {
	if (is_array($d)) {
		return (object) array_map(__FUNCTION__, $d);
	}else {
		return $d;
	}
}

osc_add_hook( 'admin_menu_init', 						'lz_dashboard_admin_menu_init');

osc_register_plugin(osc_plugin_path(__FILE__), 			'lz_dashboard_install');
osc_add_hook(osc_plugin_path(__FILE__)."_configure", 	'lz_dashboard_configure');
osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 	'lz_dashboard_uninstall');

if( lz_is_dashboard_page() ){
	osc_add_hook('init_admin',   	'lz_dashboard_init');
	osc_add_hook('admin_header', 	'lz_dashboard_header');
}

osc_add_hook('ajax_admin_lzds', 	'lz_dashboard_ajax');
osc_add_hook('ajax_lzds', 			'lz_dashboard_ajax');



/*
lz_dashboard_register( array('plugin_title' => 'LZ Advanced Filters',   'plugin_name' => 'lz_advanced_filters') );
lz_dashboard_register( array('plugin_title' => 'LZ Likebox', 			'plugin_name' => 'lz_likebox') );
lz_dashboard_register( array('plugin_title' => 'LZ Captcha', 			'plugin_name' => 'lz_captcha') );
lz_dashboard_register( array('plugin_title' => 'LZ Theme Options', 		'plugin_name' => 'lz_theme_options') );
lz_dashboard_register( array('plugin_title' => 'LZ Payments', 			'plugin_name' => 'lz_payments') );
*/
