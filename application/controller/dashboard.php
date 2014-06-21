<?php
class LzDashboard extends LzController {

	public function __construct(){
		parent::__construct('lz_dashboard');
		$this->setTitle('LzDashboard');
	}

	public function index(){
		$this->setTitle('Lz Dashboard / home');
		
		$model = $this->load()->model('dashboard', true);
		$plugins = $model->get_plugins();

		$this->addData('plugins', $plugins);
		
		$this->render('dashboard', true);
	}
	
	
	
	public function save(){
		
		if( !Params::existParam('plugin') || !Params::existParam('name') ){
			die( json_encode( array('status' => false, 'message' => _m('Plugin or form not found!', 'lz_dashboard') ) ) );
		}
		
		$this->setPlugin( Params::getParam('plugin') );
		$params 	= Params::getParam('lzds');
		
		$form_name  = Params::getParam('name');
		$form_group = Params::getParam('group');
		$form 		= $this->load()->form_by_name( $form_name, $form_group );
		
		$data = array();
		$errors = array();
		
		if(false == $form){
			$errors['form'] = _m('Error: Form not found!', 'lz_dashboard');
		} else {
			if( !empty($form->subforms)){
				$rs     = $this->load()->helper('form')->process($form->subforms, $params);
				$data   = $rs['valid_data'];
				$errors = $rs['errors'];	
			}
		}
		
		if( count($errors) == 0 ){
			$form_data = serialize( $data );
			$status = osc_set_preference( $this->plugin, $form_data, 'lz_dashboard', 'STRING' );
		
			$message = ( !$status )?
			array('status' => false, 'errors' => _m('Error: Could not save to database.', 'lz_dashboard') ) :
			array('status' => true, 'message' => _m('Success: Settings updated!', 'lz_dashboard') );
		
			die( json_encode( $message ) );
		}
		
		die( json_encode( array('status' => false, 'message' => _m('There were some errors in the form.', 'lz_dashboard'), 'errors' => $errors ) ) );
	}
}