<?php
/**
 * Project Lite Page
 * System controller: Page Settings
 * file: PageSettings.php
 *
 * 
 */
class PageSettings extends BaseAdmin
{

  // Properties
  
  // Constructor
  public function __construct()
  {
  
    parent::__construct();

  
  }
  
  // Methods
  function actionSave()
  {
	
	  $obj = new AdmPageSettingsModel;

	  $res = $obj->save( Vars::get('POST')->getRaws() );
	  if($res){  
	    redirect('admin/page-settings', 'Nastavenia boli uložené.');
	  }
	  else{
	    Application::setError('Uloženie sa nepodarilo.');
	    $this->template->setView('default');
	  }
  
  }
  
    
  function renderDefault()
  {
    
	$obj = new AdmPageSettingsModel;
	
	$result = $obj->getValues();
	
  	$this->template->title = 'Nastavenie stránky';
	if(Application::$logged['role'] == 'admin'){
	  $this->template->content = $this->createSettingsForm($result);
	}
	else{
	  $this->template->content = 'Nemáte oprávnenie meniť nastavenie stránky';
	}
  
  }
  
  private function createSettingsForm($rows)
  {
    
	$obj = new AdmPageSettingsModel;
	
	$templates = $obj->findTemplates();
	
	$form = new Form('pageSettings', 'page-settings', BASEPATH.'/admin/page-settings/save');
	
	for($i=0; $i<count($rows);$i++){
	
	  foreach($rows[$i] as $field => $val){
	    $$field = $val;
	  }
	  
	  if($constant == 'MAIN_TEMPLATE'){
	    $form->addSelect($frm_name, $templates, $title, $value);
		  $form->addDescription($frm_name, $description);
	  }
	  elseif($constant == 'FANCY_LOGIN_FORM'){
	    $form->addRadio($frm_name, array(1=>Application::getVar('yes'), 0=>Application::getVar('no')), $title, $value, 'frm-radio', true);
			$form->addDescription($frm_name, $description);
	  }
	  else{
	  $form->addText($frm_name, $title, $value);
	    $form->addDescription($frm_name, $description);
	  }
	
	}
	
	$form->addSubmit('save', 'Uložiť nastavenia');
	
	return $form->render();
  
  }

}