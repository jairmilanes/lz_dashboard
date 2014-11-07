<?php
namespace Lib\Formats;

use Lib\Formats;

class LzFormFormatList extends LzFormFormats {
	
	protected $format = array(
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
            );
	
	public function __construct(){
		return parent::__construct();
	}

}
