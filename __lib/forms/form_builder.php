<?php
require LZ_DASHBOARD_FORMS_PATH."LZDashboardForm.php";
require LZ_DASHBOARD_FORMS_PATH."Field.php";
require LZ_DASHBOARD_FORMS_PATH."Field/BaseOptions.php";
require LZ_DASHBOARD_FORMS_PATH."Field/Options.php";
require LZ_DASHBOARD_FORMS_PATH."Field/MultipleOptions.php";
require LZ_DASHBOARD_FORMS_PATH."Field/Text.php";
require LZ_DASHBOARD_FORMS_PATH."Useful.php";
require LZ_DASHBOARD_FORMS_PATH."options.php";
require LZ_DASHBOARD_FORMS_PATH."oOption.php";

class LzFormBuilder {
	
	/**
	 * It references to self object: LzFormBuilder.
	 * It is used as a singleton
	 *
	 * @access private
	 * @since 1.0
	 * @var ModelProducts
	 */
	private static $instance;
	
	/**
	 * It is a instance of our form object
	 *
	 * @access protected
	 * @since 1.0
	 */
	protected $form;
	
	/**
	 * Instance of the options object
	 *
	 * @access protected
	 * @since 1.0
	 */
	protected $options;
	
	
	protected $plugin;
	/**
	 * Log file path
	 *
	 * @access protected
	 * @since 1.0
	 */
	protected $log_file;
	
	/**
	 * Class construct
	 */
	public function __construct($plugin, $options){
		$this->log_file = LZ_DASHBOARD_PATH.'logs/error.log';
		$this->plugin = $plugin;
		$this->setOptions($options);
		return $this;
	}
	
	/**
	 * It creates a new LzFormBuilder object class ir if it has been created
	 * before, it return the previous object
	 *
	 * @access public
	 * @since 3.0
	 * @return LzFormBuilder
	 */
	public static function newInstance(){
		if( !self::$instance instanceof self ) {
			self::$instance = new self;
		}
		return self::$instance ;
	}

	/**
	 * Loads and prepares the available theme options
	 *
	 * @param array $options Array containing the theme options
	 * @return boolean True on success false otherwise
	 */
	public function setOptions($options){
		$data = null;
		
		if( empty($this->plugin)){
			return false;
		}
		
		$data = osc_get_preference( $this->plugin, 'lz_dashboard' );
	
		if( !empty($data)){
			$data = unserialize($data);
		}
	
		if( is_object($data)){
			$data = json_decode(json_encode($data), true);
		}

		//$this->form =  Lib\LZDashboardForm::getInstance($this->plugin, osc_admin_base_url(true), true, 'POST' );
		
		$options_obj = new LzOptions( $options, $data );
		$this->options = $options_obj->prepare($options);
		
		return true;
	}
	
	public function getPlugin(){
		return $this->plugin;
	}
	
	/*
	public function getAllFormInstances(){
		return Lib\LZDashboardForm::getAllInstances();
	}
	*/
	/**
	 * Gets a new form instance
	 
	public function getSubForm( $group, $menu_group = null ){
		return $this->options->getForm($group, $menu_group);
	}
	*/
	/**
	 * Get all the available fields
	 
	public function getFields( $group = null ){
		return $this->options->getFields($group);
	}
	*/

	/**
	 * Returns a specific group name given its slug
	 *
	 * @param string $group Group slug
	 * @return Ambigous <string, multitype:>
	 */
	public function getGroupName( $group_slug ){
		return $this->options->getGroupName( $group_slug );
	}
	
	/**
	 * Gets a single field value
	 *
	 * @param string $field Name of the field
	 */
	public function getOption( $group, $field, $menu_group = null ){
		return $this->options->get( $group, $field, $menu_group );
	}
	
	/**
	 * Get all the options for a specific group given the group slug
	 *
	 * @param string $group_slug Slug of the disired group
	 * @return array|false Group fields or false if it fails
	 */
	public function getOptionsByGroup( $group_slug, $menu_group = null ){
		return $this->options->getGroup($group_slug, $menu_group);
	}
	


	/**********************************************************************
	 * RENDERING FUNCTIONS
	**********************************************************************/
	/**
	 * Render the form fields given it�s name
	 *
	 * @param string $field Name of the field
	 * @param string $parent Name of the field parent
	 * @param string $group Name of the field group
	 * @return boolean|string false | field row html
	 */
	public function renderField( $field, $group, $menu_group = null ){
		return $this->options->renderField($group, $field, $menu_group);
	}
	
	/**
	 * Open form for rendering
	 */
	public function openForm(){
		return $this->options->openForm();
		//return $this->form->openForm();
	}
	
	/**
	 * Close form for rendering
	 */
	public function closeForm(){
		return $this->form->closeForm();
	}
	
	public function renderFields( $fields, $parent, $group = null ){
		foreach(  $fields as $par => $field ){
			// we are in a group
			if( is_array($field) ){
				$this->renderFields( $field, $par, $parent );
			}
			// this is a single field
			else {
				echo $this->renderField( $field, $parent, $group );
			}
		}
		return;
	}
	
}