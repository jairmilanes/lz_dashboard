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
require LZ_DASHBOARD_CORE_PATH.'lz_model.php';

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
	osc_enqueue_script('toggles');
	osc_enqueue_script('perfect_scroll');
	osc_enqueue_script('jquery-fineuploader');
	osc_enqueue_script('colpick');
	osc_enqueue_script('lz_dashboard');
	osc_enqueue_style('footable', LZ_DASHBOARD_BASE_URL.'assets/css/lz_dashboard-embedded.css' );
	osc_enqueue_style('icon-font', LZ_DASHBOARD_BASE_URL.'assets/css/footable/footable.core.css' );
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
	osc_register_script('colpick', 			LZ_DASHBOARD_BASE_URL.'assets/js/colpick.js' );
	osc_register_script('toggles', 			LZ_DASHBOARD_BASE_URL.'assets/js/toggles/toggles.min.js');
	osc_register_script('perfect_scroll', 	LZ_DASHBOARD_BASE_URL.'assets/js/perfect-scrollbar.js');
	osc_register_script('lz_dashboard', 	LZ_DASHBOARD_BASE_URL.'assets/js/lz_dashboard.js');
}

function lz_dashboard_ajax(){
	require LZ_DASHBOARD_APP_PATH.'index.php';
}

/**
 * Resgister a plugin to use the dashboard.
 * 
 * @param unknown $data
 * @return boolean
 */
function lz_dashboard_register( $data ){
	if( !isset($data['plugin_title']) || !isset($data['plugin_name']) ) {
		return false;
	}
	return osc_set_preference($data['plugin_name'], serialize($data), 'lz_plugins' );
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
	if( file_exists(LZ_DASHBOARD_UPLOAD_PATH) ){
		@mkdir(LZ_DASHBOARD_UPLOAD_PATH, 0777);
	}
	if( file_exists(LZ_DASHBOARD_UPLOAD_THUMB_PATH) ){
		@mkdir(LZ_DASHBOARD_UPLOAD_THUMB_PATH, 0777);
	}
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
	osc_redirect_to(osc_route_admin_url('lz_dashboard/index'));
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



osc_register_plugin(osc_plugin_path(__FILE__), 'lz_dashboard_install');
osc_add_hook(osc_plugin_path(__FILE__)."_configure", 'lz_dashboard_configure');
osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'lz_dashboard_uninstall');

if( lz_is_dashboard_page() ){
	osc_add_hook('init_admin',   'lz_dashboard_init');
	osc_add_hook('admin_header', 'lz_dashboard_header');
}

osc_add_hook('ajax_admin_lzds', 'lz_dashboard_ajax');


/*
lz_dashboard_register( array('plugin_title' => 'LZ Advanced Filters',   'plugin_name' => 'lz_advanced_filters') );
lz_dashboard_register( array('plugin_title' => 'LZ Likebox', 			'plugin_name' => 'lz_likebox') );
lz_dashboard_register( array('plugin_title' => 'LZ Captcha', 			'plugin_name' => 'lz_captcha') );
lz_dashboard_register( array('plugin_title' => 'LZ Theme Options', 		'plugin_name' => 'lz_theme_options') );
lz_dashboard_register( array('plugin_title' => 'LZ Payments', 			'plugin_name' => 'lz_payments') );
*/





















