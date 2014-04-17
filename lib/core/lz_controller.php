<?php
/**
 * LZ Base controller class
 * 
 * @author Jair Milanes Junior
 *
 */
class LzController {
	
	/**
	 * Page title
	 * @var string
	 */
	protected $title;
	/**
	 * Page subtitle
	 * @var string
	 */
	protected $subtitle;
	/**
	 * Data array
	 * @var array
	 */
	protected $data;
	/**
	 * Short name of the current plugin
	 * @var unknown
	 */
	protected $plugin;
	
	/**
	 * Controller class constructor
	 */
	public function __construct(){
		$this->data = array();
		$this->plugin = false;
	}
	
	protected function load_model($name){
		$path = LZ_DASHBOARD_MODEL_PATH;
		if( !empty($this->plugin) ){
			$path = osc_plugin_path( $this->plugin.'/' ).'model/';
		} 
		
		if( file_exists($path.$name.'.php') ){
			
			$classname = 'LzDashboard';
			if( !empty($this->plugin) ){
				$classname = implode('', array_map( 'ucfirst', explode('_', $this->plugin) ) );
			}
			$classname .= ucfirst($name).'Model';
			
			if( !class_exists($classname)){
				require $path.$name.'.php';
			}

			return new $classname;
		}
		return false;
	}

	/**
	 * Sets page title
	 */
	public function getTitle() {
		return $this->title;
	}
	
	/**
	 * Get current page title
	 * 
	 * @param string $title
	 * @return LzController
	 */
	public function setTitle($title) {
		$this->title = $title;
		return $this;
	}
	
	/**
	 * Gets current subtitle
	 */
	public function getSubtitle() {
		return $this->subtitle;
	}
	
	/**
	 * Set page subtitle
	 * 
	 * @param string $subtitle
	 * @return LzController
	 */
	public function setSubtitle($subtitle) {
		$this->subtitle = $subtitle;
		return $this;
	}
	
	/**
	 * Get current data array
	 * 
	 * @return array
	 */
	public function getData() {
		return $this->data;
	}
	
	/**
	 * Add entry to data array
	 * 
	 * @param string $key
	 * @param mixed $data
	 * @return LzController
	 */
	public function addData($key, $data) {
		$this->data[$key] = $data;
		return $this;
	}
	
	public function getPlugin() {
		return $this->plugin;
	}
	
	public function setPlugin($plugin) {
		$this->plugin = $plugin;
		return $this;
	}
	
	public function loadForm($name){
		if( !empty($this->plugin)){
			$filepath = osc_plugin_path($this->plugin.'/form/'.$name.'.php');
		} else {
			$filepath = LZ_DASHBOARD_FORMS_PATH.$name.'.php';
		}
		if( file_exists( $filepath ) ){
			require_once $filepath;
			$function = 'form_'.$name;
			
			if( function_exists($function)){
				$options = $function();
				if(!class_exists('LzOptions')){
					require_once LZ_DASHBOARD_FORMS_PATH.'oOptions.php';
					require_once LZ_DASHBOARD_FORMS_PATH.'LzOptions.php';
				}
				
				$form = new LzOptions( $this->plugin, $options );
				//$form = new LzFormBuilder($this->plugin,$options);
				printR($form, true);
				return $form;
			}
		}
		return false;
	}
	
	/**
	 * Renders a dashboard page
	 * @param string $file path
	 * @return boolean
	 */
	protected function render($file, $ignore_plugin = false ){
		if( !empty($this->plugin) && !$ignore_plugin ){
			$filepath = osc_plugin_path($this->plugin.'/view/'.$file.'.php');
		} else {
			$filepath = LZ_DASHBOARD_VIEW_PATH.$file.'.php';
		}
		if( file_exists( $filepath ) ){
			View::newInstance()->_exportVariableToView('lz_data', toObject($this->data));
			$this->data = null;
			require_once $filepath;
		} else {
			$this->render404();
		}
	}
	
	protected function render_form($name, $action = 'lz_dashboard/index' ){
		$form = $this->loadForm($name);
		if( false !== $form ){
			$this->addData('form_action', $action );
			$this->addData('form_name', $name);
			$this->addData('form', $form );
			$this->render('form', true);
		} else {
			$this->render404();
		}
	}
	
	protected function render404(){
		require_once LZ_DASHBOARD_VIEW_PATH.'404.php';
	}
	
	protected function renderTable(){
		
	}

}