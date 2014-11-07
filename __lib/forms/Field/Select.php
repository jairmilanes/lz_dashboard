<?php
namespace Lib\Field;

class Select extends Options
{
	protected $level = 0;
	public $field_type = 'select';
	
    public function __construct($label, array $attributes = array())
    {
        parent::__construct($label, $attributes);
    }

    public function returnField($form_name, $name, $value = '', $group = '')
    {
        $field = sprintf('<select name="%2$s[%3$s][%1$s]" id="%2$s_%3$s_%1$s">', $name, $form_name,$group);
        $field .= $this->get_options($value);
        $field .= '</select>';
        $class = !empty($this->error) ? 'error choice_label' : 'choice_label';

        return array(
            'messages' => !empty($this->custom_error) && !empty($this->error) ? $this->custom_error : $this->error,
            'label' => $this->label == false ? false : sprintf('<label for="%s_%s_%s" class="%s">%s</label>', $form_name, $group, $name, $class, $this->label),
            'field' => $field,
            'html' => $this->html
        );
    }
    
    protected function _get_options($options, $value, $group = true ){
    	$html = '';
    	foreach ($options as $key => $val) {
    		if( is_array($val) ){
    			if( $group ){ $html .= '<optgroup label="'.$val['title'].'">'; }
    				$html .= $this->_get_options( $val['items'], $value, false );
    			if( $group ){ $html .= '</optgroup>'; }
    			$this->level++;
    		} else {
    			$attributes = $this->getAttributeString($val);
    			$html .= $this->get_option($key, $value, $attributes);
    			$this->level = 0;
    		}
    	}
    	return $html;
    }
    
    protected function get_options($value = ''){
    	$html = $this->_get_options( $this->options, $value );
    	return $html;
    }
    
    protected function get_option($key, $value, $attributes){
    	$space = '';
    	for( $i=0; $i<$this->level; $i++){
    		$space .= '&nbsp;&nbsp;';
    	}
    	return sprintf('<option value="%s" %s>'.$space.'%s</option>', $key, ((string) $key === (string) $value ? 'selected="selected"' : '') . $attributes['string'], $attributes['val']);
    }

}
