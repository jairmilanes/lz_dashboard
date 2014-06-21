<?php
class LzAutoloader {

	public static $loader;

	public static function init()
	{
		if (self::$loader == NULL)
			self::$loader = new self();

		return self::$loader;
	}

	public function __construct()
	{
		spl_autoload_register(array($this,'model'));
		spl_autoload_register(array($this,'helper'));
		spl_autoload_register(array($this,'controller'));
		spl_autoload_register(array($this,'objects'));
		spl_autoload_register(array($this,'lib'));
	}

	public function lib($class)
	{
		$path = LZ_DASHBOARD_LIB_PATH;
		set_include_path(get_include_path().PATH_SEPARATOR.$path);
		spl_autoload_extensions('.php');
		spl_autoload($class);
	}

	public function controller($class)
	{
		//$class = preg_replace('/_controller$/ui','',$class);
		$path = LZ_DASHBOARD_CONTROLLER_PATH;
		set_include_path(get_include_path().PATH_SEPARATOR.$path);
		spl_autoload_extensions('.php');
		spl_autoload($class);
	}

	public function model($class)
	{
		//$class = preg_replace('/_model$/ui','',$class);
		$path = LZ_DASHBOARD_MODEL_PATH;
		set_include_path(get_include_path().PATH_SEPARATOR.$path);
		spl_autoload_extensions('.php');
		spl_autoload($class);
	}

	public function helper($class)
	{
		//$class = preg_replace('/_helper$/ui','',$class);
		$path = LZ_DASHBOARD_APP_PATH.'helpers/';
		set_include_path(get_include_path().PATH_SEPARATOR.$path);
		spl_autoload_extensions('.php');
		spl_autoload($class);
	}

}

//call
LzAutoloader::init();