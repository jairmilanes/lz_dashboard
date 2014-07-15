<?php
class LzDashboardFormHelper extends LzHelper {
	
	public function __construct($plugin, $loader = null){
		parent::__construct($plugin, $loader);
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
	 * Validates a complete form, with subforms an subgroups
	 * 
	 * @param array $forms
	 * @param array $params
	 * @return array Containing valid_data & errors
	 */
	public function process($forms, $params){
		$errors = array();
		$data = array();
		foreach( $forms as $name => $form ){

			if( isset($params[$name])){
				$pars = array_filter( $params[$name] );
				if( !empty($pars) ){

					$isValid = $form->validate( array( $name => $pars ), true );

					if(!$isValid){
						$errors[$name] = $form->getErrors();
					} else {
						$data[$name] = $isValid;
					}
				}
			}
			
			if( !empty($form->subgroups)){
				foreach( $form->subgroups as $subname => $subgroup ){
					if(isset($params[$name][$subname])){
						$pars = array_filter( $params[$name][$subname] );
						if( !empty($pars) ){
							$isValid = $subgroup->validate( array( $subname => $pars ), true );
							
							if(!$isValid){
								$errors[$name][$subname] = $form->getErrors();
							} else {
								$data[$name][$subname] = $isValid;
							}
						}
					}		
				}
			}
		}
		return array('valid_data' => $data, 'errors' => $errors );
	}
	
	
	/**
	 * Loads data into multiple forms
	 * 
	 * @param array $forms
	 * @param array $params
	 * @return array
	 */
	public function load_forms_data($forms, $params){
		foreach($forms as $name => &$form){
			if( isset($params[$name])){
				$form->addData($params[$name]);
			}
			if( !empty($form->subgroups)){
				foreach( $form->subgroups as $subname => &$subform ){
					if( isset( $params[$name][$subname])){
						$subform->addData($params[$name][$subname]);
					}
				}
			}
		}
		return $forms;
	}
	
}