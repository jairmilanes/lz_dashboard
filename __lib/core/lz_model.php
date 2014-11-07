<?php
class LZModel extends DAO {
	
	protected $errors;

	/**
	 * Instance of the log helper
	 * @var unknown
	 */
	protected $logger;

	public function __construct( $options = array() ){
		parent::__construct();
		$this->logger = LzLoaderHelper::newInstance(Session::newInstance()->_get('plugin'))->helper('log', true);
		$this->logger->setDebug(true);
		return $this;
	}
	
	public function getErrors(){
		$errors = $this->errors;
		$this->errors = null;
		return $errors;
	}
	
}