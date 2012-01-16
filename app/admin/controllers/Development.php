<?php
/**
 * Project Lite Page
 * System controller: Page Settings
 * file: Development.php
 *
 * 
 */
class Development extends BaseAdmin
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
	
	  $obj = new AdmDevelopmentModel;
	  
	  $res = $obj->save($_POST);
	  if($res){  
	    redirect('admin/development', 'Nastavenia boli uložené.');
            exit;
	  }
	  else{
	    Application::setError('Uloženie sa nepodarilo.');
	    $this->template->setView('default');
	  }
  
  }
  
  function actionFlushcache()
  {
      $obj = new AdmDevelopmentModel();
      
      $res = $obj->flushCache();
      if($res){  
	    redirect('admin/development', 'Cache bola vymazaná.');
            exit;
      }
      else{
	    Application::setError('Cache nebola vymazaná.');
	    $this->template->setView('default');
      }
  }
  
    
  function renderDefault()
  {
    
	$obj = new AdmDevelopmentModel;
	
	$result = $obj->getValues();
	
  	$this->template->title = DEV_ADMIN_DEFAULT_TITLE;
	if(Application::$logged['role'] == 'admin'){
	  $this->template->content = $this->createSettingsForm($result);
          $this->template->flushForm = $this['flushForm'];
	}
	else{
	  $this->template->content = DEV_ADMIN_DEFAULT_ERROR;
          $this->template->flushForm = '';
	}
  
  }
  
  private function createSettingsForm($rows)
  {
    
	$obj = new AdmDevelopmentModel;
	
	$form = new Form('DevSettings', 'dev-settings', BASEPATH.'/admin/development/save');
	
	for($i=0; $i<count($rows);$i++){
	
	  foreach($rows[$i] as $field => $val){
	    $$field = $val;
	  }
	  
	  $form->addRadio($frm_name, array(1=>Application::getVar('yes'), 0=>Application::getVar('no')), constant($title), $value, 'frm-radio', true);
            $form->addGroupDescription($frm_name, constant($description));
	  
	}
	
	$form->addSubmit('save', SAVE_CHANGES);
	
	return $form->render();
  
  }
  
  
  public function createControlFlushForm()
  {
    
	$obj = new AdmDevelopmentModel;
	
	$form = new Form('DevFlushCache', 'dev-settings', BASEPATH.'/admin/development/flushcache');
	
	$form->addSubmit('flush', FRM_DEV_ADMIN_FLUSH_CACHE);
	
	return $form->render();
  
  }

}