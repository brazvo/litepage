<?php

class Staff extends Controller
{

	// Properties
	private $staff;

	// Constructor
	public function __construct()
	{

		parent::__construct();

	}

	protected function beforeRender()
	{
		$this->template->bodyclass = 'staff';
	}

	///////////////////////////////actions Methods
	function actionShow()
	{
		$obj = new UserModel();
		
		$this->staff = $obj->findAllStaff();
	}
	
	function actionFreeStaff()
	{
		$obj = new UserModel();
		$this->staff = $obj->findFreeStaff();
	}
	
	///////////////////////////////////// renderers
	function renderShow()
	{
		
		$this->template->title = "Vedenie divízie";
		$this->template->staff = $this->staff;

	}
	
	function renderFreeStaff()
	{
		$this->template->title = "Voľné pozície";
		$this->template->staff = $this->staff;
	}
  
}