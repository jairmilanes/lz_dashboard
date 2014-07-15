<?php
class LzDashboardConfigHelper extends LzHelper {
	
	private $config;
	
	public function __construct($plugin, $loader = null){
		parent::__construct($plugin, $loader);
		$this->config = osc_get_preference('settings', $plugin);
		if( !empty($this->config)){
			$this->config = unserialize($this->config);
		}
	}
	
	/**
	 * It creates a new LzFormBuilder object class ir if it has been created
	 * before, it return the previous object
	 *
	 * @access public
	 * @since 3.0
	 * @return LzFormBuilder
	 */
	public static function newInstance($plugin, $loader = null){
		if( !self::$instance instanceof self ) {
			self::$instance = new self($plugin, $loader);
		}
		return self::$instance ;
	}

	public function get($key){
		return ( isset($this->config[$key])? $this->config[$key] : null);
	}
	
	public function set($key, $value){
		$this->config[$key] = $value;
	}

	public function getBoll($key){
		if( isset($this->config[$key]) ){
			if( is_array($this->config[$key])){
				return (boolean)$this->config[$key][0];
			}
			return (boolean)$this->config[$key];
		}
		return null;
	}
	
	public function getFrom($key, $index){
		if( isset($this->config[$key][$index])){
			return $this->config[$key][$index];
		}
		return null;
	}
}