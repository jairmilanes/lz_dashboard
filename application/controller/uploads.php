<?php
class LzDashboardUploadsController extends LzController {

    protected $target_plugin;
	protected $helper;
	
	public function __construct(){
        $this->setTitle('LzDashboardUploads');
        parent::__construct( Params::getParam('plugin') );
		$this->helper = LzUploadHelper::newInstance()->setPlugin( $this->getPlugin() );

	}

	public function upload(){

        //Session::newInstance()->_set('ajax_files',null); exit;

        $field_name = Params::getParam('field_name');
        $group      = Params::getParam('group');
        $uid        = Params::getParam('qquuid');
        $form = $this->load()->form_by_name('import','import');

        $field = $form->getField('csv');
        $uploaded_file = Params::getFiles('qqfile');

        $ex = explode('.', $uploaded_file['name']);
        $extension = array_pop(array_filter($ex));

        if( $field->validate( $uploaded_file ) ){

            $allowed_extensions = osc_get_preference('allowedExt');
            
            $ex = array_filter(explode(',', $allowed_extensions), function($it){
                return ($it !== 'csv');
            });

            $allowed_extensions  = implode(',', $ex);

            $custom_allowed_extensions = $allowed_extensions;

            if( false === strpos($extension,$custom_allowed_extensions) ){
                $custom_allowed_extensions .= ','.$extension;
                osc_set_preference('allowedExt',$custom_allowed_extensions,'osclass');
            }

            $result = $this->helper->saveFile( $field->upload_type );

        } else {
            $result = array(
                'success' => false,
                'message' => _m('Invalid file.','lz_dashboard')
            );
        }

        osc_set_preference('allowedExt',$allowed_extensions,'osclass');

		die( json_encode($result) );
	}
	
	public function delete(){
		$filename = Params::getParam('field_name');
		$group    = Params::getParam('group');
		$uuid     = Params::getParam('qquuid');
		$success  = $this->helper->delete( $filename, $group, $uuid );
		die( json_encode( array( 'success' => $success, 'deletedFile' => $filename ) ) );
	}
	
	public function list_all(){
		return $this->helper->getFiles();
	}
	
	/**
	 * Ajax funtion to load existing uploaded files in json format
	 */
	public function load_files(){
		$result = $this->helper->getFilesAsJson();
		die( json_encode( $result ) );
	}
}