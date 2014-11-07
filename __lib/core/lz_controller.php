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
	 * Response data
	 * @var array
	 */
	protected $response;
	
	/**
	 * Instance of the log helper
	 * @var unknown
	 */
	protected $logger;
	
	/**
	 * Short name of the current plugin
	 * @var unknown
	 */
	protected $plugin;
	
	protected $loader;
	
	protected $config;
	
	protected $debug;
	
	/**
	 * Controller class constructor
	 */
	public function __construct($plugin){
		require_once LZ_DASHBOARD_APP_PATH.'helpers/loader.php';
		$this->data   = array();
		$this->plugin = $plugin;
		$this->config = $this->load()->helper('config', true);
		
		Session::newInstance()->_set('plugin', $this->plugin);
		$this->logger = LzLoaderHelper::newInstance($this->plugin)->helper('log', true);
		$this->logger->setDebug($this->debug);
		return true;
	}

	/**
	 * Sets page title
	 */
	protected function getTitle() {
		return $this->title;
	}
	
	/**
	 * Get current page title
	 *
	 * @param string $title
	 * @return LzController
	 */
	protected function setTitle($title) {
		$this->title = $title;
		return $this;
	}
	
	/**
	 * Gets current subtitle
	 */
	protected function getSubtitle() {
		return $this->subtitle;
	}
	
	/**
	 * Set page subtitle
	 *
	 * @param string $subtitle
	 * @return LzController
	 */
	protected function setSubtitle($subtitle) {
		$this->subtitle = $subtitle;
		return $this;
	}
	
	/**
	 * Get current data array
	 *
	 * @return array
	 */
	protected function getData() {
		return $this->data;
	}
	
	/**
	 * Add entry to data array
	 *
	 * @param string $key
	 * @param mixed $data
	 * @return LzController
	 */
	protected function addData($key, $data) {
		$this->data[$key] = $data;
		return $this;
	}
	
	protected function reponseSet($key, $value){
		$this->response[$key] = $value;
		return $this;
	}
	
	/**
	 * Gets current plugin name
	 */
	protected function getPlugin() {
		return $this->plugin;
	}
	
	/**
	 * Sets current plugin name
	 *
	 * @param string $plugin
	 * @return LzController
	 */
	protected function setPlugin($plugin) {
		$this->plugin = $plugin;
		return $this;
	}
	
	/**
	 * Loader method to load models, forms and helpers
	 *
	 * @return LzLoaderHelper
	 */
	protected function load(){
		if( !$this->loader instanceof LzLoaderHelper){
			$this->loader = LzLoaderHelper::newInstance($this->plugin);
		}
		return $this->loader;
	}

	/**
	 * Render´s a template file
	 *
	 * @param string $name
	 * @param boolean $ignore_plugin
	 */
	protected function render($name, $ignore_plugin = false){
		$this->addData('plugin', $this->plugin);
		$response = $this->load()->helper('response', true);
		$response->setData($this->data);
		$response->render($name, $ignore_plugin);
	}
	
	/**
	 * Render´s a default form template
	 *
	 * @param string $name
	 */
	protected function renderForm($name){
		$this->addData('plugin', $this->plugin);
		$response = $this->load()->helper('response', true);
		$response->setData($this->data);
		$response->render_form($name);
	}
	
	/**
	 * Render´s a custom form template
	 *
	 * @param string $name
	 * @param string $path
	 */
	protected function renderCustomForm($name, $path, $modal = false){
		$this->addData('plugin', $this->plugin);
		$response = $this->load()->helper('response', true);
		$response->setData($this->data);
		$response->render_custom_form($name, $path, $modal);
		
	}
	
	/**
	 * Render´s a specific form froma  group
	 *
	 * @param string $name
	 */
	protected function renderSubForm($name, $subform, $modal = false){
		$this->addData('plugin', $this->plugin);
		$response = $this->load()->helper('response', true);
		$response->setData($this->data);
		$response->render_subform($name, $subform, $modal);
	}
	
	/**
	 * Renders a default table template
	 */
	protected function renderTable(){
		$this->addData('plugin', $this->plugin);
		$response = $this->load()->helper('response', true);
		$response->setData($this->data);
		$response->renderTable(true);
	}
	
	/**
	 * Returns a json response with proper headers
	 *
	 * @param array $data
	 * @param number $code
	 */
	protected function returnJson($code = 200){
		$response = $this->load()->helper('response', true);
		$response->setData($this->data);
		$response->return_json($code);
	}
	
	/**
	 * Returns a xml response with proper headers
	 *
	 * @param array $data
	 * @param number $code
	 */
	protected function returnXml($code = 200){
		$response = $this->load()->helper('response', true);
		$response->setData($this->data);
		$response->return_xml($code);
	}
	
	/**
	 * Checks current request method
	 *
	 * @param string $type
	 * @return boolean
	 */
	protected function isRequestType($type){
		return ( strtolower($_SERVER['REQUEST_METHOD']) == strtolower($type) )? true : false;
	}
	
	/**
	 * Blocks access to pages if not admin
	 *
	 * @return boolean
	 */
	protected function checkIsAdminPage(){
		$isLoggedin = osc_is_admin_user_logged_in();
		if( !OC_ADMIN || !$isLoggedin ){
			$response = $this->load()->helper('response', true);
			$response->render('restricted');
		}
		return true;
	}
}