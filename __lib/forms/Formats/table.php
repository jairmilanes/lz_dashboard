<?php
namespace Lib\Formats;

use Lib\Formats;

class LzFormFormatTable extends LzFormFormats {
	
	protected $format = array(
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
            );
	
	public function __construct(){
		return parent::__construct();
	}

}