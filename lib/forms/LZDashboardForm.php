<?php

/**
 * Nibble Forms 2 library
 * Copyright (c) 2013 Luke Rotherfield, Nibble Development
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
namespace Lib;
use Lib\Useful;

require LZ_DASHBOARD_FORMS_PATH.'Formats/LzFormFormats.php';
require LZ_DASHBOARD_FORMS_PATH.'Formats/list.php';
require LZ_DASHBOARD_FORMS_PATH.'Formats/table.php';

class LZDashboardForm
{
	protected $name = 'lzds';
	protected $group;
    protected $action;
    protected $do;
    protected $method;
    protected $submit_value;
    protected $fields;
    protected $sticky;
    protected $format;
    protected $message_type;
    protected $multiple_errors;
    protected $html5;
    protected $valid = true;
    protected $trigger;
    protected $messages = array();
    
    public $subforms = array();
    public $subgroups= array();
    protected $data = array();
    /*
    protected $formats
        = array(
            'list'  => array(
                'open_form'       => '<ul>',
                'close_form'      => '</ul>',
                'open_form_body'  => '',
                'close_form_body' => '',
                'open_field'      => '',
                'close_field'     => '',
                'open_html'       => "<li>\n",
                'close_html'      => "</li>\n",
                'open_submit'     => "<li>\n",
                'close_submit'    => "</li>\n"
            ),
            'table' => array(
                'open_form'       => '<table>',
                'close_form'      => '</table>',
                'open_form_body'  => '<tbody>',
                'close_form_body' => '</tbody>',
                'open_field'      => "<tr>\n",
                'close_field'     => "</tr>\n",
                'open_html'       => "<td>\n",
                'close_html'      => "</td>\n",
                'open_submit'     => '<tfoot><tr><td>',
                'close_submit'    => '</td></tr></tfoot>'
            )
        );
        */
    protected $base_path;
    //protected $descriptions= array();
    private static $instance;

    /**
     * @param string  $action
     * @param string  $submit_value
     * @param string  $method
     * @param boolean $sticky
     * @param string  $message_type
     * @param string  $format
     * @param string  $multiple_errors
     *
     * @return NibbleForm
     */
    public function __construct(
    	$name = '',
        $action = '',
        $html5 = true,
        $method = 'post',
        $submit_value = 'Submit',
        $format = 'list',
        $sticky = true,
        $message_type = 'list',
        $multiple_errors = false
    ) {
    	$this->group = $name;
    	$this->base_path = dirname(__FILE__) . DIRECTORY_SEPARATOR;
        $this->fields = new \stdClass();
        $this->action = $action;
        $this->method = $method;
        $this->html5 = $html5;
        $this->submit_value = $submit_value;
        $this->sticky = $sticky;
        $this->format = $format;
        $this->message_type = $message_type;
        $this->multiple_errors = $multiple_errors;
        // spl_autoload_register(array($this, 'nibbleLoader'));
    }

    /**
     * Singleton method
     *
     * @param string  $action
     * @param string  $method
     * @param boolean $sticky
     * @param string  $submit_value
     * @param string  $message_type
     * @param string  $format
     * @param string  $multiple_errors
     *
     * @return NibbleForm
     
    public static function getInstance(
        $name = '',
        $action = '',
        $html5 = true,
        $method = 'post',
        $submit_value = 'Submit',
        $format = 'list',
        $sticky = true,
        $message_type = 'list',
        $multiple_errors = false
    ) {
    	/*
        if (!isset(self::$instance)) {
            self::$instance = new LZDashboardForm($name,$action, $submit_value, $html5, $method, $sticky, $message_type, $format, $multiple_errors);
        }
        
        return new self($name,$action, $submit_value, $html5, $method, $sticky, $message_type, $format, $multiple_errors);
    }
    */
    
    public function setDo($do){
    	$this->do = $do;
    	return $this;
    }
    
    public function getDo(){
    	return $this->do;
    }
    
    public function hasDo(){
    	return ( empty($this->do ) ? false : true );
    }
    
    public function getAction(){
    	return $this->action;
    }
    
    public function setTrigger($trigger){
    	$this->trigger = $trigger;
    	return $this;
    }
    
	public function getTrigger(){
    	return $this->trigger;
    }
   
    public function hasTrigger(){
    	return ( empty($this->trigger ) ? false : true );
    }

    /**
     * Add a field to the form instance
     *
     * @param string  $field_name
     * @param string  $type
     * @param array   $attributes
     * @param boolean $overwrite
     *
     * @return boolean
     */
    public function newField($field_name, $type = 'text', array $attributes = array(), $overwrite = false){
        return $this->fields->$field_name = $this->createField($field_name, $type, $attributes, $overwrite);
    }
    
    public function addField($field_name, $field){
    	$this->fields->$field_name = $field;
    	return $this;
    }
    
    public function addFields($fields){
    	foreach($fields as $field_name => $field ){
    		$this->addField($field_name, $field);
    	}
    	return true;
    }
    
    public function &newSubForm($name){
    	$this->subforms[$name] = new self($name);
    	return $this->subforms[$name];
    }
    
    public function addSubForm($name, $subform){
    	$this->subforms[$name] = $subform;
    	return true;
    }
    
    public function addSubForms($subforms){
    	foreach( $subforms as $name => $subform ){
    		$this->addSubForm($name, $subform);
    	}
    	return true;
    }
    
    public function addSubFormField($subform, $field_name, $field){
    	$this->subforms[$subform]->addField($field_name, $field);
    	return true;
    }
    
    public function addSubFormFields($subform, $fields){
    	foreach($fields as $field_name => $field ){
    		$this->addSubFormField($subform, $field_name, $field);
    	}
    	return true;
    }
    
    
    public function &newSubGroup($name){
    	$this->subgroups[$name] = new self($name);
    	return $this->subgroups[$name];
    }
    
    public function addSubGroup($name, $subform){
    	$this->subgroups[$name] = $subform;
    	return true;
    }
    
    public function addSubGroups($subforms){
    	foreach( $subforms as $name => $subform ){
    		$this->addSubGroup($name, $subform);
    	}
    	return true;
    }
    
    public function addSubGroupField($subform, $field_name, $field){
    	$this->subgroups[$subform]->addField($field_name, $field);
    	return true;
    }
    
    public function addSubGroupFields($subform, $fields){
    	foreach($fields as $field_name => $field ){
    		$this->addSubGroupField($subform, $field_name, $field);
    	}
    	return true;
    }
    
    /**
     * Create and return a new field 
     *
     * @param string  $field_name
     * @param string  $type
     * @param array   $attributes
     * @param boolean $overwrite
     *
     * @return boolean
     */
    public function createField($field_name, $type = 'text', array $attributes = array(), $overwrite = false)
    {
    	$namespace = "Lib\\Field\\" . ucfirst($type);
    
    	if (isset($attributes['label'])) {
    		$label = $attributes['label'];
    	} else {
    		$label = ucfirst(str_replace('_', ' ', $field_name));
    	}
    
    	$field_name = Useful::slugify($field_name, '_');
    
    	if (isset($this->fields->$field_name) && !$overwrite) {
    		return false;
    	}
    
    	$field = $this->getFieldTypeInstance( $type, $attributes['label'], $attributes );
    	
    	return $field;
    }
    
    
	/*
    public function addDescription( $field, $description ){
    	$this->descriptions[$field] = $description;
    }
	*/
    protected function getFieldTypeInstance( $type, $label, $attributes = array() ){
    	$file = $this->base_path.'Field'.DIRECTORY_SEPARATOR.ucfirst($type).'.php';
    	$namespace = "Lib\\Field\\" . ucfirst($type);
    	if( file_exists( $file ) ){
    		if( !class_exists($namespace) ){
    			require $file;
    		}
    		return new $namespace($label, $attributes);
    	}
    	return false;
    }

    /**
     * Set the name of the form
     *
     * @param string $name
     */
    public function setName($name){
        $this->name = $name;
    }

    /**
     * Get form name
     *
     * @return string
     */
    public function getName(){
        return $this->name;
    }

    /**
     * Get form method
     *
     * @return string
     */
    public function getMethod(){
        return $this->method;
    }

    public function setGroup($group){
    	$this->group = $group;
    }
    
    public function getGroup(){
    	return $this->group;
    }

    /**
     * Add data to populate the form
     *
     * @param array $data
     *
     **/
    public function addData(array $data){
        $this->data = array_merge($this->data, $data);
    }
   
    public function getData(){
    	return $this->data;
    }
    

    /**
     * Validate the submitted form
     *
     * @return boolean
     */
    public function validate( $request = array(), $returnData = false )
    {
    	$errors = array();
    	if( empty($request) ){
        	$request = strtoupper($this->method) == 'POST' ? $_POST : $_GET;
    	}

        if (isset($request[$this->group])) {
            $form_data = $request[$this->group];
        } else {
            $this->valid = false;
            return false;
        }

        if ($this->sticky) {
            $this->addData($form_data);
        }
        
        foreach ($this->fields as $key => $value) {
            if ( !$value->validate(
                (isset($form_data[$key])
                    ? $form_data[$key] : (isset($_FILES[$this->name][$key]) ? $_FILES[$this->name][$key] : '' ) ) ) ) {

                $this->valid = false;
            }
        }
        
        return ( false !== $returnData && $this->valid )? $form_data : $this->valid;
    }
    
    /**
     * Gets a specific field HTML string from the field class
     *
     * @param string $name
     * @param string $key
     *
     * @return string
     */
    private function getFieldData($name, $key)
    {
    	if (!$this->checkField($name)) {
    		return false;
    	}
    	$field = $this->fields->$name;
    	if (isset($this->data[$name])) {
    		$field = $field->returnField($this->name, $name, $this->data[$name], $this->group );
    	} else {
    		$field = $field->returnField($this->name, $name, '', $this->group );
    	}
    	return $field[$key];
    }

    /**
     * Render the entire form including submit button, errors, form tags etc
     *
     * @return string
     */
    public function render()
    {
        $fields = '';
        $error = $this->valid ? ''
            : '<p class="error">Sorry there were some errors in the form, problem fields have been highlighted</p>';
        $format = $this->getFormat($this->format); //$this->formats[$this->format];
        $this->setToken();

        foreach ($this->fields as $key => $value) {
            $format = $this->getFormat($this->format);//$format = (object) $this->formats[$this->format];
            $temp = isset($this->data[$key]) ? $value->returnField($this->name, $key, $this->data[$key])
                : $value->returnField($this->name, $key);
            $fields .= $format->open_field;
            if ($temp['label']) {
                $fields .= $format->open_html . $temp['label'] . $format->close_html;
            }
            if (isset($temp['messages'])) {
                foreach ($temp['messages'] as $message) {
                    if ($this->message_type == 'inline') {
                        $fields .= "$format->open_html <p class=\"error\">$message</p> $format->close_html";
                    } else {
                        $this->setMessages($message, $key);
                    }
                    if (!$this->multiple_errors) {
                        break;
                    }
                }
            }
            $fields .= $format->open_html . $temp['field'] . $format->close_html.$format->close_field;
        }

        if (!empty($this->messages)) {
            $this->buildMessages();
        } else {
            $this->messages = false;
        }
        self::$instance = false;
        $attributes = $this->getFormAttributes();

        return <<<FORM
            $error
            $this->messages
            <form class="form" role="form" action="$this->action" method="$this->method" {$attributes['enctype']} {$attributes['html5']}>
              $format->open_form
                $format->open_form_body
                  $fields
                $format->close_form_body
                $format->open_submit
                  <input type="submit" name="submit" value="$this->submit_value" />
                $format->close_submit
              $format->close_form
            </form>
FORM;
    }
    
    protected function getFormat($name){
    	$format_class = 'LzFormat'.ucfirst(strtolower($name));
    	if( class_exists($format_class)){
    		$format = new $format_class;
    		return $format;
    	}
    	throw new \Exception('Format class not found!');
    }

    /**
     * Returns the HTML for a specific form field ususally in the form of input tags
     *
     * @param string $name
     *
     * @return string
     */
    public function renderField($name)
    {
        return $this->getFieldData($name, 'field');
    }

    /**
     * Returns the HTML for a specific form field's label
     *
     * @param string $name
     *
     * @return string
     */
    public function renderLabel($name)
    {
        return $this->getFieldData($name, 'label');
    }

    /**
     * Returns the error string for a specific form field
     *
     * @param string $name
     *
     * @return string
     */
    public function renderError($name)
    {
        $error_string = '';
        if (!is_array($this->getFieldData($name, 'messages'))) {
            return false;
        }
        foreach ($this->getFieldData($name, 'messages') as $error) {
            $error_string .= "<li>$error</li>";
        }

        return $error_string === '' ? false : "<ul>$error_string</ul>";
    }

    /**
     * Returns the boolean depending on existance of errors for specified
     * form field
     *
     * @param string $name
     *
     * @return boolean
     */
    public function hasError($name)
    {
        $errors = $this->getFieldData($name, 'messages');
        if (!$errors || !is_array($errors)) {
            return false;
        }

        return true;
    }

    /**
     * Returns the entire HTML structure for a form field
     *
     * @param string $name
     *
     * @return string
     */
    public function renderRow($name)
    {
    	$row_string = '';
    	switch( $this->fields->$name->field_type ){
    		case 'hidden':
    		case 'fieldGroup':
    			$row_string .= 		$this->renderField($name);
    			break;
    		case 'pluginPage':
    			$row_string .= '<div class="form-group '.$this->fields->$name->field_type.'">';
    			$row_string .= 		$this->renderField($name);
    			$row_string .= '</div>';
    			break;
    		default:
    			$row_string .= '<div class="form-group '.$this->fields->$name->field_type.'">';
    			$row_string .= 		'<div class="form-label">'.$this->renderLabel($name).'</div>';
    			$row_string .= 		'<div class="form-controls">'.$this->renderField($name);
    			$desc = $this->fields->$name->getDescription();
    			if( !empty($desc) ){
    				$row_string .=   	'<p class="description" data-field="'.osc_esc_html($name).'">'.osc_esc_html($desc).'</p>';
    			}
    			$row_string .= 			'<p class="error">'.$this->renderError($name).'</p>';
    			$row_string .= 		'</div>';
    			
    			$row_string .= '</div>';
    			break;
    	}
        return $row_string;
    }
    
    public function renderRows($rows){
    	$html = '';
    	foreach( $rows as $f_name => $f_options){
    		$html .= $this->renderRow($f_name);
    	}
    	return $html;
    }
    
    public function renderGroup(){
    	$html = '';
    	$html .= $this->renderRows($this->fields);
    	
    	if( !empty($this->subgroups ) ){
    		foreach( $this->subgroups as $form ){
    			$html .= $form->renderRows($form->fields);
    		}
    	}
    	
    	return $html;
    }
    

    /**
     * Returns HTML for all hidden fields including crsf protection
     *
     * @return string
     */
    public function renderHidden()
    {
        $this->setToken();
        $fields = array();
        foreach ($this->fields as $name => $field) {
            if (get_class($field) == 'Lib\\Field\\Hidden') {
                if (isset($this->data[$name])) {
                    $field_data = $field->returnField($this->name, $name, $this->data[$name]);
                } else {
                    $field_data = $field->returnField($this->name, $name);
                }
                $fields[] = $field_data['field'];
            }
        }

        return implode("\n", $fields);
    }

    /**
     * Returns HTML string for all errors in the form
     *
     * @return string
     */
    public function renderErrors()
    {
        $error_string = '';
        foreach ($this->fields as $name => $value ) {
            foreach ($this->getFieldData($name, 'messages') as $error) {
                $error_string .= "<li>$name $error</li>\n";
            }
        }

        return $error_string === '' ? false : "<ul>$error_string</ul>";
    }

    public function getErrors(){
    	$errors = array();
    	foreach ( $this->fields as $name => $value ) {
    		$messages = $this->getFieldData( $name, 'messages' );
			if( !empty($messages) ){
	    		foreach ( $messages as $error) {
	    			$errors[$name] .= $error;
	    		}
			}
    	}
    	return empty($errors) ? false : $errors;
    }

    /**
     * Returns the HTML string for opening a form with the correct enctype, action and method
     *
     * @return string
     */
    public function openForm()
    {
        $attributes = $this->getFormAttributes();

        return "<form class=\"form\" action=\"$this->action\" method=\"$this->method\" {$attributes['enctype']} {$attributes['html5']}>";
    }

    /**
     * Return close form tag
     *
     * @return string
     */
    public function closeForm()
    {
        return "</form>";
    }

    /**
     * Check if a field exists
     *
     * @param string $field
     *
     * @return boolean
     */
    public function checkField($field)
    {
        return isset($this->fields->$field);
    }

    public function countFields(){
    	return count($this->fields);
    }

    /**
     * Get the attributes for the form tag
     *
     * @return array
     */
    private function getFormAttributes()
    {
        $enctype = '';
        foreach ($this->fields as $field) {
            if (get_class($field) == 'File') {
                $enctype = 'enctype="multipart/form-data"';
            }
        }
        $html5 = $this->html5 ? '' : 'novalidate';

        return array(
            'enctype' => $enctype,
            'html5'   => $html5
        );
    }

    /**
     * Adds a message string to the class messages array
     *
     * @param string $message
     * @param string $title
     */
    private function setMessages($message, $title)
    {
        $title = preg_replace('/_/', ' ', ucfirst($title));
        if ($this->message_type == 'list') {
            $this->messages[] = array('title' => $title, 'message' => ucfirst($message));
        }
    }

    /**
     * Sets the messages array as an HTML string
     */
    private function buildMessages()
    {
        $messages = '<ul class="error">';
        foreach ($this->messages as $message_array) {
            $messages .= sprintf(
                '<li>%s: %s</li>%s',
                ucfirst(preg_replace('/_/', ' ', $message_array['title'])),
                ucfirst($message_array['message']),
                "\n"
            );
        }
        $this->messages = $messages . '</ul>';
    }

    
    
    /**
    public function getFieldValue( $field ){
    	return ( isset( $this->data[$field] ) )? $this->data[$field] : '';
    }

    
     * Creates a new CRSF token
     *
     * @return string

    private function setToken()
    {
        if (!isset($_SESSION["nibble_forms"])) {
            $_SESSION["nibble_forms"] = array();
        }
        if (!isset($_SESSION["nibble_forms"]["_crsf_token"])) {
            $_SESSION["nibble_forms"]["_crsf_token"] = array();
        }
        $_SESSION["nibble_forms"]["_crsf_token"][$this->name] = Useful::randomString(20);
        $this->addField("_crsf_token", "hidden");
        $this->addData(array("_crsf_token" => $_SESSION["nibble_forms"]["_crsf_token"][$this->name]));
    }
	*/
}

