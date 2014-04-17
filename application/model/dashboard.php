<?php
class LZDashboardDashboardModel extends LZModel {
	
	/* (non-PHPdoc)
	 * @see LZModel::__construct()
	 */
	public function __construct() {
		parent::__construct();
	}

	
	
	public function get_plugins(){
		$results = array();
		$plugins =  Preference::newInstance()->findBySection('lz_plugins');
		if( !empty($plugins) ){
			foreach( $plugins as $plugin ){
				$value = unserialize($plugin['s_value']);
				$results[$plugin['s_name']] = array(
						'name' => $value['plugin_title'],
						'shortname' => $value['plugin_name'],
						'path' => osc_plugin_path( osc_plugin_folder($value['plugin_name'].'/index.php') )
				);
				if( file_exists(osc_plugin_path( osc_plugin_folder($value['plugin_name'].'/index.php') ).'assets/img/dashboard.png') ){
					$results[$plugin['s_name']]['icon'] = osc_plugin_url($value['plugin_name'].'/index.php').'assets/img/dashboard.png';
				}
			}
		}
		return $results;
	}
	
}