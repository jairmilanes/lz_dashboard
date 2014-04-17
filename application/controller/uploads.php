<?php
class LzDashboardUploads extends LzController {

	protected $helper;
	
	public function __construct(){
		parent::__construct();
		$this->setTitle('LzDashboardUploads');
		if( Params::existParam('plugin') ){
			$this->setPlugin( Params::getParam('plugin') );
		}
		$this->helper = LzUploadHelper::newInstance()->setPlugin( $this->getPlugin() );
	}

	public function upload(){
		$result = $this->helper->saveFile();
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
	public function load(){
		$result = $this->helper->getFilesAsJson();
		die( json_encode( $result ) );
	}
}