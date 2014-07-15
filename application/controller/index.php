<?php
class LzDashboardIndexController extends LzController {

	public function __construct(){
		parent::__construct();
	}

	public function index(){
		$this->setTitle(_('Dashboard / home', 'lz_dashboard') );
		$this->render('index');
	}
}