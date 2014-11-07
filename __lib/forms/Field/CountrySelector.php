<?php
namespace Lib\Field;

class CountrySelector extends Options
{
	protected $attrs;
	public $field_type = 'countryselector';
	
    public function __construct($label, array $attributes = array())
    {
    	$this->attrs = $attributes;
        parent::__construct($label, $attributes);
    }

    public function returnField($form_name, $name, $value = '', $group = '')
    {
    	$name = sprintf('%2$s[%3$s][%1$s]', $name, $form_name,$group);
    
    	$class = !empty($this->error) ? 'error choice_label' : 'choice_label';
    
    	$field = '<select name="' . $name . '" class="'.$class.'" id="' . trim(preg_replace('|([^_a-zA-Z0-9-]+)|', '_', $name),'_') . '">';
        $field .= '<option value="">' . __('Select a country...') . '</option>';
        $countrys = osc_get_countries();
        foreach($countrys as $i) {
            $field .= '<option value="' . osc_esc_html($i['pk_c_code']) . '"' . ( ($value == $i['pk_c_code']) ? 'selected="selected"' : '' ) . '>' . $i['s_name'] . '</option>';
        }
        $field .= '</select>';

    	return array(
    			'messages' => !empty($this->custom_error) && !empty($this->error) ? $this->custom_error : $this->error,
    			'label' => $this->label == false ? false : sprintf('<label for="%s_%s_%s" class="%s">%s</label>', $form_name, $group, $name, $class, $this->label),
    			'field' => $field,
    			'html' => $this->html
    	);
    }

}
