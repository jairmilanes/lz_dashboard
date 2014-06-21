<?php
class LZDashboardDashboardModel extends LZModel {
	
	/* (non-PHPdoc)
	 * @see LZModel::__construct()
	 */
	public function __construct() {
		parent::__construct();
	}

	public function getPluginData($plugin){
		$dbdata = osc_get_preference( $plugin, 'lz_dashboard' );
		if( false !== $dbdata ){
			return unserialize($dbdata);
		}
		return false;
	}
	
	public function get_plugins(){
		$results = array();
		$s_plugins =  Preference::newInstance()->get('lz_plugins', 'lz_dashboard');
		if( !empty($s_plugins) ){
			$plugins = unserialize($s_plugins);
			foreach( $plugins as $name => $plugin ){
				$results[$name] = array(
						'name' => $plugin,
						'shortname' => $name,
						'path' => osc_plugin_path( osc_plugin_folder($name.'/index.php') )
				);
				$icon = osc_plugin_path( osc_plugin_folder($plugin.'/index.php') ).'assets/img/dashboard.png';
				if( file_exists($icon) ){
					$results[$name]['icon'] = $icon;
				}
			}
		}
		return $results;
	}
	
}