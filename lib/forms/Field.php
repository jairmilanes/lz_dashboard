<?php
namespace Lib;

use Lib\Useful;

abstract class Field
{
    public $custom_error = array();
    public $html = array(
        'open_field' => false,
        'close_field' => false,
        'open_html' => false,
        'close_html' => false
    );
    
    protected $form;
    protected $default;
    protected $description;

    public function setForm($form){
        $this->form = $form;
    }
    
    public function setDefault($value){
    	$this->default = $value;
    	return $this;
    }
    
    public function getDefault(){
    	return $this->default;
    }
    
    public function setDescription($value){
    	$this->description = $value;
    	return $this;
    }
    
    public function getDescription(){
    	return $this->description;
    }

    /**
     * Return the current field, i.e label and input
     */
    abstract public function returnField($form_name, $name, $value = '', $group = '' );

    /**
     * Validate the current field
     */
    abstract public function validate($val);

    /**
     * Apply custom error message from user to field
     */
    public function errorMessage($message)
    {
        $this->custom_error[] = $message;
    }

}
