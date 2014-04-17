<?php
class LzUploadHelper {

	const THUMB_WIDTH = 250;
	const THUMB_HEIGH = 150;
	
	protected $plugin;
	protected $uploader;
	protected static $instance;
	
	/**
	 * Class construct
	 */
	public function __construct(){
		return $this;
	}
	
	/**
	 * It creates a new Builder object class ir if it has been created
	 * before, it return the previous object
	 *
	 * @access public
	 * @since 3.0
	 * @return UploadHelper
	 */
	public static function newInstance(){
		if( !self::$instance instanceof self ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	public function setPlugin($plugin){
		$this->plugin = $plugin;
		return $this;
	}

	/**
	 * Saves new uploaded files
	 */
	public function saveFile(){

		$field_name = Params::getParam('field_name');
		$group      = Params::getParam('group');
		$uid        = Params::getParam('qquuid');
		$files      = $this->getSessionFiles(); //Session::newInstance()->_get('lz_dashboard_ajaxfiles');

		if( !empty($field_name) && !isset($files[$uid]) ){

			$this->uploader = $this->getUploader();
			
			$original   = pathinfo( $this->uploader->getOriginalName() );
			$filename   = $this->getUniqueFilename( $original['extension'] );

			if( !file_exists( $this->getUploadPath() ) ){
				@mkdir( $this->getUploadPath(), 0777 );
			}

			if( !file_exists( $this->getUploadThumbPath() ) ){
				@mkdir( $this->getUploadThumbPath(), 0777 );
			}

			$result = $this->uploader->handleUpload( $this->getUploadPath().$filename );

			if( isset( $result['success'] ) && $result['success'] == true && $this->saveAndResize( $field_name, $group, $uid, $filename ) ){
				$result['success'] = true;
				$result['thumbnailUrl'] = osc_uploads_url( sprintf( LZ_DASHBOARD_SHORTNAME.'/thumbnails/%s/%s', $this->plugin, $filename) );
				$result['uploadName'] = $filename;
				return $result;
			}
		}
		return 	$result = array('success' => false, 'message' => _m('File already exists, try another file or change the filename.','lz_theme_options') );
	}

	/**
	 * Saves the new file while it creates a thumbnail
	 */
	protected function saveAndResize( $field_name, $group, $uid, $filename ){
		
		$pref_name = $this->plugin.'_'.$group.'_'.$field_name;
		
		$current_file = osc_get_preference($pref_name, $this->getPreferenceKey() );
		$saved = osc_set_preference( $pref_name, $uid.'||'.$filename, $this->getPreferenceKey(), 'STRING' );

		if( $saved !== false ){
			
			if(!empty($current_file)){
				$current_file = explode('||', $current_file );
				
				if( file_exists($this->getUploadPath().$current_file[1])){
					@unlink($this->getUploadPath().$current_file[1]);
				}
				if( file_exists($this->getUploadThumbPath().$current_file[1])){
					@unlink($this->getUploadThumbPath().$current_file[1]);
				}
				
				$session_files = $this->getSessionFiles(); //Session::newInstance()->_get('lz_dashboard_ajaxfiles');
				if( isset( $session_files[$current_file[0]])){
					unset($session_files[$current_file[0]]);
					$this->setSessionFiles($session_files);
					//Session::newInstance()->_set('lz_dashboard_ajaxfiles', $session_files);
				}
			}

			$resize = ImageResizer::fromFile( $this->getUploadPath().$filename );
			$resize->resizeTo( self::THUMB_WIDTH, self::THUMB_HEIGH, true );

			try {
				$resize->saveToFile( $this->getUploadThumbPath().$filename );
			} catch( Exception $e){
				return false;
			}
			return true;
		}
		return false;
	}
	
	/**
	 * Gets a unique name for a new upload file
	 */
	protected function getUniqueFileName($ext){
		return uniqid("qqfile_").".".$ext;
	}
	
	/**
	 * Completly deletes the file from the database and filesystem
	 */
	public function delete($field_name, $group, $uuid = null ){
		try {

			$files = $this->getSessionFiles(); //Session::newInstance()->_get('lz_dashboard_ajaxfiles');

			if( empty($uuid)){

				$db_file = osc_get_preference( $this->plugin.'_'.$group.'_'.$field_name, $this->getPreferenceKey());

				if( !empty($db_file) ){
					$f = explode('||', $db_file );
					$uid = $f[0];
					$filename = $f[1];
				}
				
			} else {
				if( isset($files[$uuid]) ){
					$filename = $files[$uuid];
				}
			}
			
			
			if( file_exists( $this->getUploadPath().$filename ) ){
				@unlink( $this->getUploadPath().$filename );
				@unlink( $this->getUploadThumbPath().$filename );
			}

			osc_delete_preference( $this->plugin.'_'.$group.'_'.$field_name, $this->getPreferenceKey());

			if( isset( $files[$uid])){
				unset($files[$uid]);
				$this->setSessionFiles($files);
				//Session::newInstance()->_set('lz_dashboard_ajaxfiles', $files);
			}

		} catch( Exception $e ){
			return false;
		}
		return true;
	}
	
	/*
	public function cleanUpFile($user_file){
		
		$file = explode('||', $user_file);
		
		if( file_exists(LZO_UPLOAD_PATH.$file[1]) ){
			@unlink(LZO_DEMO_USER_PATH.$file[1]);
		}
		else if( file_exists(LZO_DEMO_USER_THUMB_PATH.$file[1]) ){
			@unlink(LZO_DEMO_USER_THUMB_PATH.$file[1]);
		}
		Preference::newInstance()->dao->delete(Preference::newInstance()->getTableName(),'s_section = \'lz_theme_options\'');
		Preference::newInstance()->dao->delete(Preference::newInstance()->getTableName(),'s_section = \'lz_theme_options_uploads\'');
		
		
	}
	*/

	/**
	 * get all uploads for the current template
	 */
	public function getFiles( $upload_fields = array() ){
		
		Preference::newInstance()->dao->select();
		Preference::newInstance()->dao->from( Preference::newInstance()->getTableName() );
		Preference::newInstance()->dao->where( 's_section', LZ_DASHBOARD_SHORTNAME.'_uploads' );

		$i = 0;
		$results = array();
		$files = array();
		
		if( count( $upload_fields ) > 0 ){
			foreach( $upload_fields as $field ){
				if( $i == 0 ){
					Preference::newInstance()->dao->where( 's_name', $this->plugin.'_'.$field );
				} else {
					Preference::newInstance()->dao->orWhere( 's_name', $this->plugin.'_'.$field );
				}
				$i++;
			}
			
		} else {
			Preference::newInstance()->dao->like( 's_name', $this->plugin.'_', 'after' );
		}	
		$files = Preference::newInstance()->dao->get();
	
		if( is_object($files) && $files->numRows() > 0 ){
			foreach( $files->resultArray() as $file ) {
				$field = str_replace( $this->plugin.'_', '', $file['s_name'] );
				$results[$field] = $file;
			}
		}
		return $results;
	}

	/**
	 * Ajax funtion to load existing uploaded files
	 */
	public function getFileByName($field_name, $group){
		$results 	= array();

		$file 		= osc_get_preference( $this->plugin.'_'.$group.'_'.$field_name, $this->getPreferenceKey() );

		if( !empty( $file ) ){
			$f 				 = explode( '||', $file );
			$uid 		     = $f[0];
			$filename 		 = $f[1];

			if( file_exists($this->getUploadPath().$filename ) ){
				$results['name'] 		 = $filename;
				$results['uuid'] 		 = $uid;
				$results['size'] 		 = $this->human_filesize( filesize( $this->getUploadPath().$filename ) );
				$results['url']  		 = osc_uploads_url( LZ_DASHBOARD_SHORTNAME.'/'.$this->plugin.'/'.$filename );
				$results['thumbnailUrl'] = osc_uploads_url( LZ_DASHBOARD_SHORTNAME.'/thumbnails/'.$this->plugin.'/'.$filename );
			} else {
				osc_delete_preference( $this->plugin.'_'.$group.'_'.$field_name, $this->getPreferenceKey() );
			}
		}
		return $results;
	}

	/**
	 * Ajax funtion to load existing uploaded files in json format
	 */
	public function getFilesAsJson(){
		if( Params::existParam('field_name') ){
			$field_name = Params::getParam('field_name');
			$group      = Params::getParam('group');
			$results    = $this->getFileByName( $field_name, $group );
		
			if( !empty($results) ){
				$files      = $this->getSessionFiles();//Session::newInstance()->_get('lz_dashboard_ajaxfiles');
				if( empty($files)){ $files = array(); }
				$files[$results['uuid']] = $results['name'];
				$this->setSessionFiles($files);
				return array( 'status' => true, $field_name => $results );
			}
		}
		return array( 'status' => false );
	}

	
	protected function getPreferenceKey(){
		return LZ_DASHBOARD_SHORTNAME.'_uploads';
	}
	
	protected function getUploader(){
		if( !class_exists('AjaxUploader') ){
			require_once(LIB_PATH."AjaxUploader.php");
		}
		return new AjaxUploader();
	}
	
	protected function getUploadPath(){
		return LZ_DASHBOARD_UPLOAD_PATH.$this->plugin.'/';
	}
	
	protected function getUploadThumbPath(){
		return LZ_DASHBOARD_UPLOAD_THUMB_PATH.$this->plugin.'/';
	}
	
	protected function getSessionFiles(){
		//return Session::newInstance()->_get( LZ_DASHBOARD_SHORTNAME.'_'.$this->plugin.'_ajaxfiles' );
		return Session::newInstance()->_get('ajax_files');
	}
	
	protected function setSessionFiles($files){
		//Session::newInstance()->_set( LZ_DASHBOARD_SHORTNAME.'_'.$this->plugin.'_ajaxfiles', $files);
		Session::newInstance()->_set( 'ajax_files', $files);
		return $this;
	}

	/**
	 * Returns a formatted filesize
	 */
	protected static function human_filesize($bytes, $decimals = 2) {
		$sz = 'BKMGTP';
		$factor = floor((strlen($bytes) - 1) / 3);
		return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
	}

}