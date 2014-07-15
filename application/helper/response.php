<?php
/**
 * Response helper
 *
 * @author Jair Milanes Junior
 *
 */
class LzDashboardResponseHelper extends LzHelper {
	
	/**
	 * Valid response codes
	 * @var array
	 */
	protected $codes = array(200,301,302,400,401,402);
	
	/**
	 * Current response code
	 * @var number
	 */
	protected $code;
	
	/**
	 * Current content type
	 * @var string
	 */
	protected $content_type;
	
	/**
	 * View data
	 * @var array
	 */
	protected $data = array();

	/**
	 * LzResponseHelper class construct
	 *
	 * @param string $plugin
	 * @param string $loader
	 */
	public function __construct($plugin, $loader = null ){
		parent::__construct($plugin, $loader);
	}
	
	/**
	 * Adds to response data array
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return LzResponseHelper
	 */
	public function addData($key, $value){
		$this->data[$key] = $value;
		return $this;
	}
	
	/**
	 * Sets response data, overwrites existing data
	 *
	 * @param array $data
	 * @return LzResponseHelper
	 */
	public function setData($data){
		$this->data = $data;
		return $this;
	}
	
	/**
	 * Gets response data in stdClass instance
	 *
	 * @return stdClass
	 */
	protected function getData($associative = false){
		if( !$associative ){
			return $this->toObject($this->data);
		}
		return $this->data;
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
	
	/**
	 * Returns response data in XML format
	 *
	 * @param number $code
	 */
	public function return_xml($code = 200){
		if( $this->is_code_valid($code) ){
			http_response_code($code);
		}
		$xml = new SimpleXMLElement('<root/>');
		array_walk_recursive( $this->getData(true), array($xml, 'addChild'));
		header('Content-type: application/xml');
		die( $xml->asXML() );
	}
	
	/**
	 * Returns response data in JSON format
	 *
	 * @param number $code
	 */
	public function return_json($code = 200){
		if( $this->is_code_valid($code) ){
			http_response_code($code);
		}
		header('Content-type: application/json');
		die( json_encode($this->data) );
	}
	
	/**
	 * Renders a dashboard page
	 *
	 * @param string $file path
	 * @return boolean
	 */
	public function render($file, $ignore_plugin = false ){
		if( !empty($this->plugin) && !$ignore_plugin ){
			$filepath = osc_plugin_path($this->plugin.'/view/'.$file.'.php');
		} else {
			$filepath = LZ_DASHBOARD_VIEW_PATH.$file.'.php';
		}
		if( file_exists( $filepath ) ){
			View::newInstance()->_exportVariableToView('lz_data', $this->getData());
			$this->data = null;
			require $filepath;
		} else {
			$this->render404();
		}
	}

	/**
	 * Render a form
	 *
	 * @param string $name
	 */
	public function render_form($name){
		$form = $this->loader->form($name);
		$forms = $form->getForms();
		foreach( $forms as $n => &$f ){
			if( isset($this->data[$n])){
				$f->addData($this->data[$n]);
			}
		}
		$form->setForms($forms);
		$this->_render_form($name, $form);
	}

	/**
	 * Private method to render a given loaded form
	 *
	 * @param string $name
	 * @param string $form
	 */
	private function _render_form($name, $form){
		if( false !== $form ){
			$this->addData('plugin', $this->data['plugin']);
			$this->addData('form_name', $name);
			$this->addData('form', $form );
			$this->render('form', true);
		} else {
			$this->render404();
		}
	}
	
	/**
	 * Render a subform
	 *
	 * @param string $form_name
	 * @param string $subform
	 */
	public function render_subform($form_name, $subform, $modal = false){

		$form = $this->loader->form($form_name);
		$forms = $form->getForms();
		if( isset( $forms[$subform])){
			if( !empty($this->data[$subform]) ){
				$forms[$subform]->addData( $this->data[$subform] );
			}
			
			if( isset($this->data['do'])){
				$forms[$subform]->setDo($this->data['do']);
			}
			
			$form->setForms(array($subform => $forms[$subform] ));
		}
		
		if( false !== $form ){
			$this->addData('plugin', $this->data['plugin']);
			$this->addData('form_name', $form_name);
			$this->addData('group', $subform);
			$this->addData('form', $form );
			if( $modal ){
				$this->render('floating_form', true);
			} else {
				$this->render('single_form', true);
			}
		} else {
			$this->render404();
		}
	}
	
	/**
	 * Rednder a custom form given it´s name and path
	 *
	 * @param string $name
	 * @param string $path
	 */
	public function render_custom_form($name, $path, $modal = false){
		$form = $this->loader->customForm($name, $path);
		$forms = $form->getForms();
		$this->loader->helper('form', true)->load_forms_data($forms, $this->data);
		if( false !== $form ){
			$this->addData('plugin', $this->data['plugin']);
			$this->addData('form_name', $name);
			$this->addData('form', $form );
			if( $modal ){
				$this->render('floating_form', true);
			} else {
				$this->render('single_form', true);
			}
		} else {
			$this->render404();
		}
	}

	/**
	 * Render a table
	 *
	 * @param string $ignore_plugin
	 */
	public function renderTable($ignore_plugin = true){
		$this->addData('plugin', $this->plugin);
		View::newInstance()->_exportVariableToView('lz_data', $this->getData());
		$this->render('table', $ignore_plugin);
	}
	
	/**
	 * Render a 404 page
	 */
	public function render404(){
		$this->render('404', true);
	}

	/**
	 * Check response code
	 *
	 * @param int $code
	 * @return boolean
	 */
	protected function is_code_valid($code){
		return (in_array($code, $this->codes))? true : false;
	}
	
	/**
	 * Converts a array to object
	 *
	 * @param array $d
	 * @return StdClass
	 */
	protected function toObject($d) {
		if (is_array($d)) {
			return (object) array_map(array($this, __FUNCTION__), $d);
		}else {
			return $d;
		}
	}
}