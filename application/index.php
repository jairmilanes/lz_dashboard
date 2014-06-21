<?php
/*******************************************************
 * CORE
 ******************************************************/
$method = '';
$action = strtolower(Params::getParam('do'));
$plugin = Params::getParam('plugin');

if( empty($action)){
	$action = 'dashboard';
}

if( preg_match('/\//', $action)){
	$parts = explode('/',$action);
	$action = $parts[0];
	$method = $parts[1];
}

$controller = lz_dashboard_get_action( $action, $plugin );
if( !empty($controller)){
	try {
		if( !empty($method)){
			$controller->$method();
		} else {
			$controller->index();
		}
	} catch( Exception $e ) {}
} else {
	die('Invalid request!');
}