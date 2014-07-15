<?php
/**
 * LzLoader class
 * 
 * @author Jair Milanes Junior | LayoutzWeb
 */
class LzDashboardLoaderHelper extends LzHelper {

	/**
	 * LzDashboardLoaderHelper class construct
	 * 
	 * @param string $plugin
	 * @param string $loader
	 * @return LzDashboardLoaderHelper
	 */
	public function __construct($plugin, $loader = null) {
		parent::__construct($plugin, $loader);
		return $this;
	}
	
	/**
	 * It creates a new LzDashboardLoaderHelper object class ir if it has been created
	 * before, it return the previous object
	 *
	 * @access public
	 * @since 3.0
	 * @return LzDashboardLoaderHelper
	 */
	public static function newInstance($plugin){
		if( !self::$instance instanceof self ) {
			self::$instance = new self($plugin);
		}
		return self::$instance ;
	}

	/**
	 * Loads a helper instance
	 * 
	 * @param string $name
	 * @return boolean
	 */
	public function helper($name, $ignore_plugin = false){
		$name = implode('', array_map( 'ucfirst', explode('_', $name) ) );
		$classname = 'LzDashboard';
		if( !empty($this->plugin) && !$ignore_plugin ){
			$classname = implode('', array_map( 'ucfirst', explode('_', $this->plugin) ) );
		}
		$classname = $classname.ucfirst(strtolower($name)).'Helper';
		if( class_exists($classname, true) ){
			return $classname::newInstance($this->plugin, $this );
		}
		return false;
	}
	
	/**
	 * Loads a LzModel instance
	 * 
	 * @param string $name
	 * @return LzModel|boolean
	 */
	public function model($name, $ignore_plugin = false){
		$classname = 'LzDashboard';
		if( !empty($this->plugin) && !$ignore_plugin ){
			$classname = implode('', array_map( 'ucfirst', explode('_', $this->plugin) ) );
		}
		$name = implode('', array_map( 'ucfirst', explode('_', $name) ) );
		$classname .= ucfirst($name).'Model';
		if( class_exists($classname, true) ){
			return new $classname;
		}
		return false;
	}
	
	/**
	 * Loads a specific object
	 *
	 * @param string $filename
	 * @param string $classname
	 * @param boolean $ignore_plugin
	 * @return object|boolean
	 */
	public function object($classname, $params = array(), $ignore_plugin = false){
		$class = 'o';
		if( !empty($this->plugin) 
				&& !$ignore_plugin ){
			$class .= implode('', array_map( 'ucfirst', explode('_', $this->plugin) ) );
		} else {
			$class .= 'LzDashboard';
		}
		$class .= ucfirst(strtolower($classname));
		if( class_exists($class, true)){
			return new $class($params);
		}
		return false;
	}

	/**
	 * Loads a LzForm instance
	 * 
	 * @param string $name
	 * @return LzOptions|boolean
	 */
	public function form($name){
		if( !empty($this->plugin)){
			$filepath = osc_plugin_path($this->plugin.'/form/'.$name.'.php');
		} else {
			$filepath = LZ_DASHBOARD_FORMS_PATH.$name.'.php';
		}
		return $this->loadFormOptions($name, $filepath);
	}
	
	/**
	 * Loads a custom form given it´s name and path
	 * 
	 * @param string $name
	 * @param string $path
	 * @return Ambigous LzOptions|boolean
	 */
	public function customForm($name, $path){
		return $this->loadFormOptions($name, $path.$name.'.php');
	}
	
	/**
	 * Returns a new instance of LzOptions loaded with the desired form fields.
	 * 
	 * @param string $name
	 * @param string $filepath
	 * @return LzOptions|boolean
	 */
	private function loadFormOptions($name, $filepath){
		if( file_exists( $filepath ) ){
			require_once $filepath;
			$function = 'form_'.$name;
			if( function_exists($function)){
				$options = $function();
				if(!class_exists('LzOptions')){
					require_once LZ_DASHBOARD_FORMS_PATH.'oOptions.php';
					require_once LZ_DASHBOARD_FORMS_PATH.'LzOptions.php';
				}
				$options = new LzOptions( $this->plugin, $options );
				
				return $options;
			}
		}
		return false;
	}
	
	/**
	 * Gets a form by group & name
	 * 
	 * @param string $name
	 * @param string $group
	 * @return LzForm|boolean
	 */
	public function form_by_name($name, $group){
		$options = $this->form($name);
		$form_group = $options->getFormByGroup($group);
		if( !empty($form_group)){
			return $form_group;
		}
		return false;
	}

}