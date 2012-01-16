<?php
class Administrate extends RatingsBaseController
{ 
  
	private $settings;
	
	private $frmValues;

	// Constructor
	public function __construct()
	{
		//$this->runSettings();
		// only action show may be used by unlogged user
		if(!Application::$logged['status']){
		  if(Application::$action != 'show'){
			redirect('error/default/no_permision');
		  }
		}
		parent::__construct();

	}

	protected function startUp()
	{
		if(!Application::$logged['status']){
			redirect('login');
		}
		parent::startUp();
	}


	// Methods
	/***************************************** ACTIONS ***/ 
	function actionDefault()
	{
	    $obj = new RatingsModel;
		$this->settings = $obj->getSettings();
		
		// set values
		foreach($this->settings as $row) {
			$this->frmValues[ $row['frm_name'] ] = $row['value'];
		}
	}
   
	/***************************************** RENDERERS ***/
	function renderDefault()
	{
		$this->template->title = RAV_RENDER_DEFAULT_TITLE;
		$this->template->form = $this['settingsForm'];
	}


	/***************************************** FACTORIES ***/
	public function createControlSettingsForm()
	{
		$form = new AppForm("rav_settings");
		
		foreach($this->settings as $row) {
			if(in_array($row['frm_name'], array('ratings_affected', 'views_affected'))) {
				$form->addTextarea( $row['frm_name'], constant( $row['title'] ), 30, 5 )
					->addDescription( constant($row['description']) );
			}
			else {
				$form->addCheckbox( $row['frm_name'], constant( $row['title'] ) )
					->addDescription( constant($row['description']) );
			}
		}
		
		$form->addSubmit('save', SAVE);
		$form->onSubmit("settingFormSubmitted", $this);
		$form->setDefaultVals($this->frmValues);
		$form->collect();
		
		return $form;
	}
	
	public function settingFormSubmitted(AppForm $form)
	{
		$values = $form->getValues();
		
		$mod = new RatingsModel();
		$res = $mod->saveSettings($values);
		if($res) {
			redirect('ratingsandviews', RAV_CHANGES_WERE_SAVED);
		}
		else {
			$this->flashError(RAV_SAVING_FAILED);
			$form->setDefaultVals();
		}
	}

	/*************************************** OTHER METHODS ***/
  
	private function runSettings()
	{
		$obj = new RatingsModel;

		$rows = $obj->getSettings();

		foreach($rows as $row){
			define($row['constant'], $row['value']);
		}
	}
  
}
