<?php
namespace Lib\Field;

class Categories extends Options
{
	protected $level = 0;
	public $field_type = 'categories';

	public function __construct($label, array $attributes = array())
	{
		parent::__construct($label, $attributes);
	}
	
	public function returnField($form_name, $name, $value = '', $group = '')
	{
		$name = sprintf('%2$s[%3$s][%1$s]', $name, $form_name,$group);
		
		$class = !empty($this->error) ? 'error choice_label' : 'choice_label';
		
		ob_start();
			osc_categories_select($name, null, __('Select a category', 'lz_dashboard'));
		$field = ob_get_contents();
		ob_end_clean();
		
		$field = str_replace('name=\"', 'class="'.$class.'" name=', $field);
		
		return array(
				'messages' => !empty($this->custom_error) && !empty($this->error) ? $this->custom_error : $this->error,
				'label' => $this->label == false ? false : sprintf('<label for="%s_%s_%s" class="%s">%s</label>', $form_name, $group, $name, $class, $this->label),
				'field' => $field,
				'html' => $this->html
		);
	}
	
}