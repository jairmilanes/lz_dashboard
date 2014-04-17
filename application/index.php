<?php
/*******************************************************
 * CORE
 ******************************************************/
$method = '';
$action = strtolower(Params::getParam('do'));
$plugin = Params::getParam('lz_mod');

if( empty($action)){
	$action = 'dashboard';
}

if( preg_match('/\//', $action)){
	$parts = explode('/',$action);
	$action = $parts[0];
	$method = $parts[1];
}

if( !empty($plugin) ){
	$controller = osc_plugin_path($plugin.'/').'controller/'.$action.'.php';
} else {
	$controller = LZ_DASHBOARD_CONTROLLER_PATH.$action.'.php';
}

if( file_exists($controller) ){

	require $controller;
	
	$className = 'LzDashboard';
	if( $action !== 'dashboard' ){
		if( !empty($plugin) ){
			$className = implode('', array_map( 'ucfirst', explode('_', $plugin) ) );
		}
		$className .= ucfirst($action);
	}
	
	if( class_exists($className)){
		$controller = new $className();
	
		if( !empty($method)){
			$controller->$method();
		} else {
			$controller->index();
		}
	}
}