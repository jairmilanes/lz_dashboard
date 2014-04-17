<?php
class oOptions {
	
	// groups of options
	public $menu_groups;
	// groups of fields
	public $groups;
	// group felds
	public $field_groups;
	// Errors array
	public $errors;
	// db data
	protected $data;
	
	
	//protected static $instance;
	
	/**
	 * Class construct
	 */
	public function __construct(){
		$this->menu_groups 	= array();
		$this->groups 		= array();
		$this->field_groups = array();
		$this->errors       = array();
		$this->data         = array();
		return $this;
	}
	
	public function getObject(){
		$array = array_merge( $this->menu_groups, $this->groups );
		return json_decode(json_encode($array));
	}
	
	/**
	 * It creates a new LzFormBuilder object class ir if it has been created
	 * before, it return the previous object
	 *
	 * @access public
	 * @since 3.0
	 * @return LzFormBuilder
	 
	public static function newInstance(){
		if( !self::$instance instanceof self ) {
			self::$instance = new self;
		}
		return self::$instance ;
	}
	*/
	
	/**
	 * Sets the db data
	 * 
	 * @param array $data
	 * @return oOptions
	 */
	public function setData( array $data ){
		$this->data = $data;
		return $this;
	}
	
	/**
	 * gets data by group|menu_group name
	 * 
	 * @param string $group
	 * @param string $field
	 * @return array
	 */
	public function getFieldDataByGroup($group, $field){
		return (isset($this->data[$group][$field]) )? $this->data[$group][$field] : array();
	}
	
	/**
	 * Gets local data field by group name
	 *
	 * @param string $group
	 * @param string $menu_group
	 * @return array
	 */
	public function getDataField( $field, $group, $menu_group = null ){
		if( !empty($menu_group)){
			if( isset($this->data[$menu_group][$group][$field]) ){
				return $this->data[$menu_group][$group][$field];
			}
		} else {
			if(isset($this->data[$group][$field])){
				return 	$this->data[$group][$field];
			}
		}
		return array();
	}
	
	/**
	 * Gets local data group by group name
	 * 
	 * @param string $group
	 * @param string $menu_group
	 * @return array
	 */
	public function getDataGroup($group, $menu_group = null){
		if( !empty($menu_group)){
			if( isset($this->data[$menu_group][$group]) ){
				return $this->data[$menu_group][$group];
			}
		} else {
			if(isset($this->data[$group])){
				return 	$this->data[$group];
			}
		}
		return array();
	}
	
	/**
	 * Creates a new menugroup
	 *
	 * @param string $name
	 * @return LzOptions
	 */
	public function addMenuGroup($name, $groups = array() ){
		$this->menu_groups[$name] = $groups;
		return $this;
	}
	
	/**
	 * Creates a new group
	 * @param string $group
	 * @return LzOptions
	 */
	public function addGroup($group, $fields = array() ){
		$this->groups[$group] = $fields;
		return $this;
	}
	
	/**
	 * Creates a new field group
	 *
	 * @param string $group
	 * @param string $field_group
	 * @return LzOptions
	 */
	public function addFieldGroup($group, $field_group, $value = array() ){
		if( !isset($this->groups[$group])){
			$this->groups[$group] = array();
		}
		$this->groups[$group][$field_group] = $value;
		return $this;
	}
	
	/**
	 * Adds a group to a menu group, if menu group does not exist it creates one
	 *
	 * @param string $group
	 * @param string $menu_group
	 * @return LzOptions
	 */
	public function addGroupToMenuGroups($menu_group, $group, $groups ){
		$this->menu_groups[$menu_group][$group] = $groups;
		return $this;
	}
	
	/**
	 * Adds a field to a group, if group does not exist it creates one
	 *
	 * @param string $group
	 * @param string $field
	 * @return LzOptions
	 */
	public function addFieldToGroup($group, $field){
		if( isset($this->groups[$group])){
			$this->groups[$group] = array();
		}
		$this->groups[$group][] = $field;
		return $this;
	}
	
	/**
	 * Adds multiple fields to a group
	 *
	 * @param string $group
	 * @param string $fields
	 * @return LzOptions
	 */
	public function addFieldsToGroup($group, $fields){
		foreach ( $fields as $field ){
			$this->addFieldToGroup($group, $field);
		}
		return $this;
	}
	
	/**
	 * Adds a field to field group, if field group does not exist it creates one
	 *
	 * @param string $group
	 * @param string $field_group
	 * @param string $field
	 * @return LzOptions
	 */
	public function addFieldToFieldGroup($field_group, $field){
		$this->field_groups[$field_group] = $field;		
		return $this;
	}
	
	/**
	 * Add multiple fields to a field group
	 *
	 * @param string $group
	 * @param string $field_group
	 * @param string $fields
	 * @return LzOptions
	 */
	public function addFieldsToFieldGroup($group, $field_group, $fields){
		foreach( $fields as $field ){
			$this->addFieldToFieldGroup($group, $field_group, $field);
		}
		return $this;
	}	
	
	/**
	 * Validates a options field
	 * 
	 * @param string $group
	 * @param string $field
	 * @param mixed $value
	 * @return boolean
	 */
	public function validate($group, $field, $value){
		if( !$field->validate($value) ){
			$this->errors[$group] = $field->error;
			return false;
		}
		return true;
	}
	
	/**
	 * Validates a options group
	 * 
	 * @param string $group
	 * @param mixed $data
	 * @return boolean
	 */
	public function validateGroup($group, $data){
		foreach( $this->groups[$group] as $field ){
			$this->validate($group, $field, $data[$group][$field]);
		}
		if( !empty($this->errors) ){
			return false;
		}
		return true;
	}

}