<?php
/*******************************************************
 * CORE
 ******************************************************/
$method = '';
$action = strtolower(Params::getParam('do'));
$plugin = Params::getParam('plugin');

if( Params::existParam('route')
	&& ( Params::getParam('route') == 'lz_dashboard/do'
		|| Params::getParam('route') == 'lz_dashboard/user/do') ){

	$params = Params::getParam('params');
	
	if( strlen($params) > 0){
		$pr = array_map(function($e){
			$rs = explode('_',$e);
			Params::setParam($rs[0], $rs[1]); 
			return $rs;
		}, explode('-', $params) );
		unset($_REQUEST['params']);
	}
} 

$act = explode('-',$action);

if( isset($act[1])){
	$action = $act[0];
	for($i=1;$i<count($act);$i++){
		$action .= ucfirst(strtolower($act[$i]));
	}
}

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
	http_response_code(404);
	die('Page not found!');
}