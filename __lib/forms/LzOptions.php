<?php
require_once LZ_DASHBOARD_FORMS_PATH."LZDashboardForm.php";
require_once LZ_DASHBOARD_FORMS_PATH."Field.php";
require_once LZ_DASHBOARD_FORMS_PATH."Field/BaseOptions.php";
require_once LZ_DASHBOARD_FORMS_PATH."Field/Options.php";
require_once LZ_DASHBOARD_FORMS_PATH."Field/MultipleOptions.php";
require_once LZ_DASHBOARD_FORMS_PATH."Field/Text.php";
require_once LZ_DASHBOARD_FORMS_PATH."Useful.php";

use Lib\LZDashboardForm;

class LzOptions {
	
	// Forms
	protected $forms;
	// Active form
	protected $form;
	// Plugin name
	protected $plugin;
	// Plugin options
	protected $options;
	// Menu structure
	protected $menu;
	
	protected $errors;
	
	protected $defaults;
	/**
	 * Options constructor
	 * 
	 * @param array $options
	 * @return LzOptions
	 */
	public function __construct( $plugin, $options ){
		$this->forms = array();
		$this->plugin = $plugin;
		$this->options = new oOptions;
		$this->data = $this->getData();
		$this->defaults = array();
		//$this->options->setData($this->getData());
		$this->menu  = new stdClass();
		$this->errors = array();
		return $this->prepare($options);
	}

	/**
	 * Prepares the hole form
	 */
	public function prepare($options){
		foreach( $options as $group => $option ){
			$this->organize($group, $option);
			$this->forms[$group] = $this->form;
			$this->form = null;
		}
		$this->prepareMenu($options);
		return $this;
	}
	
	/**
	 * Prepares tab menu structure
	 * 
	 * @param array $options
	 * @return boolean
	 */
	protected function prepareMenu($options){
		foreach( $options as $name => $option ){
			if( !isset($this->menu->$name)){
				$this->menu->$name = new stdClass();
				$this->menu->$name->title = $option['title'];
				$this->menu->$name->description = $option['description'];
			}
			if( $option['type'] == 'menu_group' ){
				$this->menu->$name->submenus = new stdClass();
				foreach( $option['groups'] as $group => $option ){
					$this->menu->$name->submenus->$group = new stdClass();
					$this->menu->$name->submenus->$group->title = $option['title'];
					$this->menu->$name->submenus->$group->description = $option['description'];
				}
			}
		}
		return true;
	}
	
	/**
	 * Organize options and instantiates all fields of a specific group
	 * 
	 * @param string $group
	 * @param array $options
	 */
	protected function organize($group, array $options){
		switch($options['type']){
			case 'menu_group':
				$this->prepareMenuGroup($group, $options);
				break;
			case 'group':
				$this->form = $this->getForm($group);
				if( isset($options['action']) ){
					$this->form->setDo($options['action']);
				}
				if( isset($options['trigger']) && !empty($options['trigger']) ){
					$this->form->setTrigger($options['trigger']);
				}
				$this->prepareGroup($group, $options);
				break;
		}
		return true;
	}
	
	/**
	 * Prepares a menu group
	 * 
	 * @param string $group
	 * @param array $options
	 * @return array Groups
	 */
	protected function prepareMenuGroup($group, array $options){
		$subforms = array();
		if( isset($this->defaults[$group])){
			$this->defaults[$group] = array();
		}
		foreach( $options['groups'] as $form => $opts ){
			$this->form = $this->getForm($form);
			$this->prepareGroup($form, $opts);
			$subforms[$form] = $this->form;
			
			$this->defaults[$group][$form] = $this->defaults[$form];
			unset($this->defaults[$form]);
		}
		$this->form = $this->getForm($group);
		if( isset($options['action']) && !empty($options['action']) ){
			$this->form->setDo($options['action']);
		}
		if( isset($options['trigger']) && !empty($options['trigger']) ){
			$this->form->setTrigger($options['trigger']);
		}
		$this->form->addSubForms($subforms);
		return true;
	}
	
	/**
	 * Prepares a group
	 * 
	 * @param string $group
	 * @param array $options
	 * @return array Fields
	 */
	protected function prepareGroup($group, array $options){
		$fields = array();
		foreach( $options['fields'] as $name => $opts ){
			
			if( isset($opts['default']) && !empty($opts['default'])){
				if( !isset( $this->defaults[$group] )){
					$this->defaults[$group] = array();
				}
				$this->defaults[$group][$name] = $opts['default'];
			}
			
			switch( $opts['type'] ){
				case 'fieldGroup':
					$this->form->newSubGroup($name);
					
					$opts['options']['action'] = 'open';
					$field = $this->prepareField($name, $opts);
					$this->form->addSubGroupField($name, 'open_group_'.$name, $field);
					
					$fdls = $this->prepareFieldGroup($name, $opts);
					$this->form->addSubGroupFields($name, $fdls);
					
					$opts['options']['action'] = 'close';
					$field = $this->prepareField($name, $opts);
					$this->form->addSubGroupField($name, 'close_group_'.$name, $field);
					
					
					break;
				default: 
					$fields[$name] = $this->prepareField($name, $opts);
					break;
			}
		}
		$this->form->addFields($fields);
		return $fields;
	}

	/**
	 * Prepares field group
	 * 
	 * @param string $group
	 * @param array $options
	 * @return array Fields
	 */
	protected function prepareFieldGroup($name, array $options){
		$fields = array();
		foreach( $options['fields'] as $name => $opts ){
			$fields[$name] = $this->prepareField($name, $opts);
		}
		return $fields;
	}
	
	/**
	 * Prepares a specific field given it´s type and options
	 * 
	 * @param string $field
	 * @param array $options
	 * @return object|null Field instance or null if field does not exist
	 */
	protected function prepareField( $field, array $options){	
		
		if( !isset($options['options']) || !is_array($options['options'])){
			$options['options'] = array();
		}
		
		$field_instance = null;
		$method = $this->getFieldTypeMethod( $options['type'] );

		if( method_exists($this, $method)){
			$field_instance = $this->$method( $options['type'],  $field,  $options['options'] );

			if( method_exists($field_instance, 'setDefault') && isset($options['default'])){
				$field_instance->setDefault($options['default']);
			}
			
			if( method_exists($field_instance, 'setDescription') && isset($options['description'])){
				$field_instance->setDescription($options['description']);
			}
			
		}
		
		return $field_instance;
	}
	
	
	/**
	 * Set the field type
	 *
	 * @param string $title
	 * @param string $options
	 * @param string $group_slug
	 * @param string $group_parent
	 * @return string
	 */
	protected function getFieldTypeMethod( $type ){
		$method = '';
		switch( $type ){
			case 'text':
			case 'textarea':
			case 'email':
			case 'number':
			case 'file':
			case 'url':
				$method = 'setOptionTypeText';
				break;
			case 'radio':
			case 'checkbox':
			case 'select':
			case 'multipleSelect':
			case 'colorSelector':
			case 'textureSelector':
			case 'countrySelector':
			case 'regionSelector':
			case 'citySelector':
			case 'countrySelector':
			case 'googleFont':
				$method = 'setOptionTypeOptions';
				break;
			case 'toggleSwitch':
				$method = 'setOptionTypeToggleSwitch';
				break;
			case 'slideRange':
				$method = 'setOptionTypeSlideRange';
				break;
			case 'ajaxFile':
				$method = 'setOptionTypeAjaxFile';
				break;
			case 'colorpicker':
				$method = 'setOptionTypeColorpicker';
				break;
			case 'password':
				$method = 'setOptionTypePassword';
				break;
			case 'pluginPage':
				$method = 'setOptionTypePluginPage';
				break;
			case 'fieldGroup':
				$method = 'setOptionTypeFieldGroup';
				break;
			case 'hidden':
				$method = 'setOptionTypeHidden';
				break;
			case "categories":
				$method = 'setOptionTypeCategories';
				break;
		}
		return $method;
	}
	
	/**
	 * Adds a text field to the form
	 *
	 * @param string $type
	 * @param string $title
	 * @param array $data
	 * @param string $group_slug
	 * @param string $group_parent
	 */
	protected function setOptionTypeText( $type, $title, array $attributes ){
		return $this->form->createField( $title, $type, array(
			'id'			=> 'field_'.strtolower( $title ),
			'class' 		=> 'text_field '.@$attributes['class'],
			'required' 		=> @$attributes['required'],
			'disabled' 		=> @$attributes['disabled'],
			'readonly' 		=> @$attributes['readonly'],
			'label'			=> @$attributes['label'],
			'max_length' 	=> @$attributes['max_length'],
			'min_length' 	=> @$attributes['min_length'],
			'value'			=> @$attributes['value'],
			'placeholder'	=> @$attributes['placeholder']
		));
	}
	
	/**
	 * Creates a colorpicker field instance
	 *
	 * @param string $type
	 * @param string $title
	 * @param array $data
	 */
	protected function setOptionTypeColorpicker( $type, $title, array $attributes ){	
		$attributes['id'] = 'colorpicker_id'; // id of the field * only used internally
		$attributes['class'] = 'colorpicker colorpicker_class'; // class of the field * only used internally
		return $this->setOptionTypeText( 'text', $title, $attributes );
	}
	
	/**
	 * Adds a slider range field to the form
	 *
	 * @param string $type
	 * @param string $title
	 * @param array $data
	 * @param string $group_slug
	 * @param string $group_parent
	 */
	protected function setOptionTypeSlideRange( $type, $title, array $attributes ){
		return $this->form->createField( $title, $type, array(
			'id'			=> 'field_'.strtolower( $title ),
			'class' 		=> 'text_field '.@$attributes['class'],
			'required' 		=> @$attributes['required'],
			'label'			=> @$attributes['label'],
			//'max_length' 	=> @$data['max_length'],
			//'min_length' 	=> @$data['min_length'],
			'max' 			=> @$attributes['max'],
			'min' 			=> @$attributes['min'],
			'type' 			=> @$attributes['type'],
			//'value'			=> @$attributes['value'],
			'step'			=> @$attributes['step']
		));
	}
	
	/**
	 * Adds a text field to the form
	 *
	 * @param string $type
	 * @param string $title
	 * @param array $attributes
	 * @param string $group_slug
	 * @param string $group_parent
	 */
	protected function setOptionTypeAjaxFile( $type, $title, array $attributes ){
		return $this->form->createField( $title, $type, array(
			'id'			=> 'field_'.strtolower( $title ),
			'class' 		=> 'text_field '.@$attributes['class'],
			'required' 		=> @$attributes['required'],
			'label'			=> @$attributes['label'],
			'value'			=> @$attributes['value']
		));
	}
	
	/**
	 * Creates a select, checkbox, radio type of field
	 *
	 * @param string $type
	 * @param string $title
	 * @param array $attributes
	 */
	protected function setOptionTypeOptions( $type, $title, array $attributes ){
		return $this->form->createField( $title, $type, array(
			'id'			=> 'field_'.strtolower( $title ),
			'class' 		=> 'options_field '.@$attributes['class'],
			'required' 		=> @$attributes['required'],
			'disabled' 		=> @$attributes['disabled'],
			'readonly' 		=> @$attributes['readonly'],
			'label'			=> @$attributes['label'],
			'false_values'  => array(),
			'value'			=> @$attributes['value'],
			'choices'       => @$attributes['choices'],
			'false_values'  => @$attributes['false_values'],
			'option_size'   => @$attributes['option_size']
		));
	}
	
	/**
	 * Creates a page wrapper 
	 * 
	 * @param string $type
	 * @param string $title
	 * @param array $attributes
	 */
	protected function setOptionTypePluginPage( $type, $title, array $attributes ){
		return $this->form->createField( $title, $type, array(
			'id'			=> 'field_'.strtolower( $title ),
			'class' 		=> 'plugin_page '.$title,
			'plugin'		=> $attributes['plugin'],
			'do'			=> $attributes['do'],
			'listen'		=> @$attributes['listen']
		));
	}
	
	/**
	 * Creates a toggleSwtch type of field
	 *
	 * @param string $type
	 * @param string $title
	 * @param array $attributes
	 */
	protected function setOptionTypeToggleSwitch( $type = 'checkbox', $title, array $attributes ){
		$attributes['class'] = 'toggleSwitch';
		return $this->setOptionTypeOptions('checkbox', $title, $attributes );
	}
	
	/**
	 * Creates a group type of field
	 *
	 * @param string $type
	 * @param string $title
	 * @param array $attributes
	 * @param string $group_slug
	 * @param string $group_parent
	 */
	protected function setOptionTypeFieldGroup( $type, $title, array $attributes ){
		return $this->form->createField( $title, $type, array(
			'id'			=> 'field_'.strtolower( $title ),
			'class' 		=> 'text_field',
			'title'			=> @$attributes['title'],
			'action'  		=> @$attributes['action'],
		));
	}
	
	/**
	 * Creates a hidden field
	 *
	 * @param string $type
	 * @param string $title
	 * @param array $attributes
	 * @param string $group_slug
	 * @param string $group_parent
	 */
	protected function setOptionTypeHidden( $type, $title, array $attributes ){
		return $this->form->createField( $title, $type, array(
				'id'			=> 'field_'.strtolower( $title ),
				'class' 		=> 'hidden_field',
				'required'		=> @$attributes['required']
		));
	}
	
	/**
	 * Creates a categories select field
	 *
	 * @param string $type
	 * @param string $title
	 * @param array $data
	 * @param string $group_slug
	 * @param string $group_parent
	 */
	protected function setOptionTypeCategories( $type, $title, array $attributes ){
		return $this->form->createField( $title, $type, array(
				'id'			=> 'field_'.strtolower( $title ),
				'class' 		=> 'text_field',
				'label'			=> @$attributes['label'],
				'title'			=> @$attributes['title']
		));
	}
	
	public function validate($params){
		$loader = LzLoaderHelper::newInstance($this->plugin);
		$rs = $loader->helper('form', true)->process($this->getForms(), $params);
		if( !empty($rs['errors'])){
			$this->errors = $rs['errors'];
			return false;
		}
		return $rs['valid_data'];
	}
	
	public function getErrors(){
		return $this->errors;
	}
	
	/**
	 * Return current data array
	 * 
	 * @throws Exception
	 * @return array
	 */
	protected function getData(){
		if(empty($this->plugin)){
			throw new Exception('Plugin not found!');
		}
	
		$data = osc_get_preference($this->plugin, 'lz_dashboard');
	
		if(!empty($data)){
			$data = unserialize($data);
		}
	
		if(is_object($data)){
			return json_decode(json_encode($data), true);
		}
	
		return $data;
	}
	
	public function setData($data){
		$this->data = $data;
		return $this;
	}
	
	/**
	 * Gets current tabs structure
	 * 
	 * @return stdClass
	 */
	public function getMenu(){
		return $this->menu;
	}
	
	/**
	 * Render the form fields given it´s name
	 *
	 * @param string $field Name of the field
	 * @param string $parent Name of the field parent
	 * @param string $group Name of the field group
	 * @return boolean|string false | field row html
	 */
	public function renderField( $group, $field, $menu_group = null ){
		$form = $this->getForm($group, $menu_group);
		if( !$form->checkField($field) ){
			return false;
		}
		return $form->renderRow($field);
	}
	
	/**
	 * Render multiple fields
	 * 
	 * @param array $fields
	 * @param string $group
	 * @param string $menu_group
	 */
	public function renderFields( $fields, $group, $menu_group = null ){
		foreach(  $fields as $par => $field ){
			// we are in a group
			if( is_array($field) ){
				$this->renderFields( $field, $par, $group );
			}
			// this is a single field
			else {
				echo $this->renderField( $field, $group, $menu_group );
			}
		}
		return;
	}
	
	/**
	 * Get´s a specific group form
	 *
	 * @param unknown $group
	 * @param string $menu_group
	 * @return Ambigous <\Lib\NibbleForm, multitype:>|boolean
	 */
	protected function getForm($group, $menu_group = null){
		$g = $group;
		if( !empty($menu_group)){
			$g = $menu_group;
		}
		$form = new Lib\LZDashboardForm($g);
		if( isset($this->data[$group])){
			$form->addData( $this->data[$group] );
		}
		return $form;
	}
	
	/**
	 * Gets all forms
	 * 
	 * @return array
	 */
	public function getForms(){
		return $this->forms;
	}
	
	public function setForms($forms){
		$this->forms = $forms;
		return $this;
	}
	
	/**
	 * Gets a specific form by group
	 * 
	 * @param string $group
	 * @return boolean|LZDashboardForm
	 */
	public function getFormByGroup($group){
		return (isset($this->forms[$group])? $this->forms[$group] : false);
	}
	
	/**
	 * Gets a single field value
	 * 
	 * @param string $field
	 * @param string $group
	 * @param string $menu_group
	 * @return 
	 */
	public function get( $group, $field, $menu_group = null ){
		return $this->options->getDataField($field, $group, $menu_group);
	}
	
	/**
	 * Gets a single field value
	 *
	 * @param string $group
	 * @param string $menu_group
	 * @return
	 */
	public function getGroup( $group, $menu_group = null ){
		return $this->options->getDataGroup($group, $menu_group);
	}

	/**
	 * Gets current plugin name
	 */
	public function getPlugin(){
		return $this->plugin;
	}
}