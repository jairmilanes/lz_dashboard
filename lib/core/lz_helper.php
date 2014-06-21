<?php
class LzHelper {
	
	 protected static $instance;
	 
	 protected $plugin;
	 protected $loader;
	
	 public function __construct($plugin, $loader = null){
	 	$this->plugin = $plugin;
	 	if( !is_null($loader)){
	 		$this->loader = $loader;
	 	}
	 	return true;
	 }
	 
	 
}