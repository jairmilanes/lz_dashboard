<?php
namespace Lib\Field;

class Html extends Text
{
    public $field_type = 'html';

    public function __construct($label, $attributes)
    {

        parent::__construct($label, $attributes);
    }

    public function returnField($form_name, $name, $value = '', $group = '')
    {

        return array(
            'messages' => !empty($this->custom_error) && !empty($this->error) ? $this->custom_error : $this->error,
            'label' => $this->label == false ? false : sprintf('<label for="%s_%s_%s" class="%s">%s</label>', $form_name, $group, $name, $this->class, $this->label),
            'field' => sprintf('<div class="html_field %s">'.$this->attributes['value'].'</div>', $this->attributes['class']),
            'html' => $this->html
        );
    }

    public function validate($val)
    {
        return true;
    }

}
