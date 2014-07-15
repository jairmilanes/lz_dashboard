<?php
class LzDashboardCacheHelper extends LzHelper {
	
	private $cache;
	
	public function __construct($plugin, $loader = null){
		parent::__construct($plugin, $loader);
		if( !class_exists('phpFastCache') ){
			require_once LZ_DASHBOARD_LIB_PATH.'cache/phpfastcache.php';
		}
		phpFastCache::setup( "storage", "auto" );
		$this->cache = phpFastCache();
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
	
	public function set($key, $value, $timeout = 300){
		return $this->cache->set($key, $value, $timeout);
	}
	
	public function get($key){
		$rs =$this->cache->get($key);
		if( !empty($rs)){
			return $rs;
		}
		return false;
	}

	public function delete($key){
		return $this->cache->delete($key);
	}
}