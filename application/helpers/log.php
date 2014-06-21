<?php
class LzLogHelper extends LzHelper {
	
	const TEMPLATE = '[{type}][{group}]|{time}|{message}';
	
	protected $debug;
	
	/**
	 * LzLogHelper class construct
	 *
	 * @param string $plugin
	 * @param string $loader
	 * @return LzLogHelper
	 */
	public function __construct($plugin, $loader = null) {
		$this->debug = false;
		parent::__construct($plugin, $loader);
		return $this;
	}

	/**
	 * @return the $debug
	 */
	public function getDebug() {
		return $this->debug;
	}

	/**
	 * @param field_type $debug
	 */
	public function setDebug($debug) {
		$this->debug = $debug;
	}

	/**
	 * It creates a new LzLogHelper object
	 *
	 * @param string $plugin
	 * @param string $loader
	 * @return LzLogHelper
	 */
	public static function newInstance($plugin, $loader = null){
		if( !self::$instance instanceof self ) {
			self::$instance = new self($plugin, $loader);
		}
		return self::$instance;
	}

	public function log( $type, $msg, $group = 'general'){
		if( $this->debug ){
			$method = 'log'.ucfirst(strtolower($type));
			$log = str_replace('{type}', strtoupper($type), self::TEMPLATE);
			$log = str_replace('{group}', strtoupper($group), $log);
			$log = str_replace('{time}', date('Y-M-d H:i:s'), $log);
			$log = str_replace('{message}', $msg, $log);
			return $this->_log($log);
		}
		return true;
	}
	
	private function _log($log){
		return $this->loader->model('logs')->insert(array( 's_log' => $log ) );
	}
	
}