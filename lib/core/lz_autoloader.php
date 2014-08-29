<?php
/**
 * LZ Autoload class
 * @author Jair Milanes Junior
 */
class LzAutoloader {

	public static $loader;
	protected $paths = array();
	protected $plugins = array();
	protected $files = array();
	protected $autoload_folders = array(
		'helper', 'model', 'controller', 'objects'
	);
	
	const EXT = '.php';
	const PREFIX = 'Lz';

	/**
	 * Autoload Init
	 * @param string $class
	 */
	public static function init(){
		if (self::$loader == NULL)
			self::$loader = new self();
		return self::$loader;
	}

	/**
	 * Autoload COnstruct
	 * @param string $class
	 */
	public function __construct(){
		$this->load_plugins();
		
		spl_autoload_extensions('.php');
		spl_autoload_register(array($this,'model'));
		spl_autoload_register(array($this,'helper'));
		spl_autoload_register(array($this,'controller'));
		spl_autoload_register(array($this,'objects'));
		//spl_autoload_register(array($this,'lib'));
		
		//var_dump($this->files);
	}
	
	/**
	 * Autoload Controllers
	 * @param string $class
	 */
	public function controller($class){
		return $this->try_loading('controller', $class);
	}

	/**
	 * Autoload Models
	 * @param string $class
	 */
	public function model($class){
		return $this->try_loading('model', $class);
	}

	/**
	 * Autoload Helpers
	 * @param string $class
	 */
	public function helper($class){
		return $this->try_loading('helper', $class);
	}
	
	/**
	 * Autoload Objects
	 * @param string $class
	 */
	public function objects($class){
		if( preg_match('/^oLz.*/', $class) && !class_exists($class)){
			foreach ($this->paths as $path ){
				$p = $path.'/objects';
				if( file_exists($p)){
					$file = $p.'/'.$class.'.php';
					if( file_exists($file)){
						return require $file;
						break;
					}
				}
			}
		}
		return false;
	}
	
	/**
	 * Autoloader helper method
	 * @param string $type
	 * @param string $class
	 */
	protected function try_loading($type, $class){
		$type = strtolower($type);
		if( preg_match('/'.self::PREFIX.'/', $class) && preg_match("/".ucfirst($type)."/", $class) && !class_exists($class)){
			return $this->check_paths($class, $type);
		}
		return false;
	}
	
	/**
	 * Autoloader helper method to sanitize the class filename
	 * @param string $class
	 * @param string $plugin
	 * @param string $type
	 */
	protected function get_filename($class, $plugin, $type){
		$class = str_replace(ucfirst(strtolower($type)),'',$class);
		$class = str_replace($plugin,'',$class);
		$parts = preg_split('/(?=[A-Z])/',$class);
		$parts = array_filter($parts);
		$filename = implode('_', array_map('strtolower', $parts));
		return $filename;
	}
	
	/**
	 * Autoloader helper method to check the plugins paths
	 * @param string $class
	 * @param string $type
	 */
	protected function check_paths($class, $type){
		$path = '';
		$dash = 'LzDashboard';
		if( preg_match("/$dash/", $class) ){
			$path = LZ_DASHBOARD_APP_PATH.$type;
			$path .= '/'.$this->get_filename($class, $dash, $type).'.php';
		} else {
			foreach( $this->plugins as $plugin => $title ){
				$p = implode('', array_map('ucfirst',explode('_', $plugin)));
				if( preg_match("/$p/", $class) ){
					$path = $this->paths[$plugin].'/'.$type;
					$path .= '/'.$this->get_filename($class, $p, $type).'.php';
					break;
				}
			}
		}
		if( file_exists($path)){
			return require $path;
		}
		return false;
	}
	
	/**
	 * Autoloader helper method to load all existing plugins
	 */
	protected function load_plugins(){
		$plugins = osc_get_preference('lz_plugins', 'lz_dashboard');
		if( !empty($plugins)){
			$this->plugins = unserialize($plugins);
			foreach( $this->plugins as $plugin => $title ){
	
				$p = osc_plugin_path($plugin);
				if( !isset($this->files[$plugin])){
					$this->files[$plugin] = array();
				}
	
				foreach( $this->autoload_folders as $folder ){
					$fullpath = $p.'/'.$folder;
					if( file_exists($fullpath)){
						$files = scandir($fullpath);
							
						if( !isset($this->files[$folder])){
							$this->files[$plugin][$folder] = array();
						}
							
						foreach( $files as $file ){
							if( $file !== '.' && $file !== '..' ){
								$this->files[$plugin][$folder][str_replace(self::EXT,'',$file)] = $fullpath;
							}
						}
					}
				}
				$this->paths[$plugin] = $p;
			}
		}
	
		Session::newInstance()->_set('lz_plugins_path', $this->paths);
		return true;
	}
}