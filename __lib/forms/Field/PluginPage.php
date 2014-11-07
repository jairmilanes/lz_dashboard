<?php
namespace Lib\Field;
use Lib\Field;

class PluginPage extends Field {
	
	protected $label,
			  $content = '/.*/',
			  $attribute_string = '',
			  $class = '',
		      $required = true,
			  $attributes;
	public $error = array(),
		   $field_type = 'pluginPage';
	
	public function __construct($label, $attributes = array())
	{
		$this->label = $label;
		$this->attributes = $attributes;
	}

	public function returnField($form_name, $name, $value = '', $group = '')
	{
		$html = '';
		ob_start();
		\Params::setParam('do', $this->attributes['do']);
		\Params::setParam('plugin', $this->attributes['plugin']);
		if( $this->attributes['listen'] ){
			\Params::setParam('listen', $this->attributes['listen']);
		}
		osc_run_hook('ajax_admin_lzds');
		$html .= ob_get_contents();
		ob_end_clean();

		return array(
				'messages'  => '',
				'label' 	=> '',
				'field' 	=> '<div class="'.$group.'">'.$html.'</div>',
				'html' 		=> ''
		);
	}

	public function validate($val) {
		return true;
	}

}