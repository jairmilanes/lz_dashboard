<?php
class LzDashboard extends LzController {

	public function __construct(){
		parent::__construct();
		$this->setTitle('LzDashboard');
		
	}

	public function index(){
		$this->setTitle('Lz Dashboard / home');
		
		$model = $this->load_model('dashboard');
		$plugins = $model->get_plugins();
		
		$this->addData('plugins', $plugins);
		
		$this->render('dashboard');
	}
	
	public function save(){
		
		if( !Params::existParam('plugin') || !Params::existParam('name') ){
			die( json_encode( array('status' => false, 'message' => _m('Plugin or form not found!', 'lz_dashboard') ) ) );
		}
		$this->setPlugin( Params::getParam('plugin') );
		$forms = $this->loadForm( Params::getParam('name') );
		
		$groups = $forms->getFields();
		$params = Params::getParam('lzds');
		
		$data   = array();
		$errors = array();
		
		foreach( $groups as $parent => $fields ){
			
			$form = $forms->getSubForm($parent);
			$name = $form->getName();
			$group = $form->getGroup();

			if( isset($params[$parent]) ){
				$pars = array_filter( $params[$parent] );

				if( !empty($pars) ){
					$isValid = $form->validate( array( $name => $pars ), true );
					
					if(!$isValid){
						$errors[$parent] = $form->getErrors();
					} else {
						if( !empty($group) ){
							if( !isset($data[$group])){
								$data[$group] = array();
							}
							$data[$group][$parent] = $isValid;
						} else {
							$data[$parent] = $isValid;
							
						}
					}
				}
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