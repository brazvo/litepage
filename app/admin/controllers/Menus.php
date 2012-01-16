<?php
/**
 * Project Lite Page
 * System controller: Menus
 * file: Menus.php
 *
 * 
 */
class Menus extends BaseAdmin
{

  // Properties
  
  // Constructor
  public function __construct()
  {
    $this->perm_mach_name = 'menus';
    parent::__construct();
  
  }
  
  /******************************************* ACTIONS */
  function actionSave($id)
  {
  
    $obj = new AdmMenusModel;
	
	$result = $obj->saveMenu($_POST, $id);
	
	if($result){
            redirect('admin/menus', 'Úprava bola uložená');
	}
	else{
            $this->template->setView('edit');
	}
  
  }
  
  function actionSaveNew()
  {
  
    $obj = new AdmMenusModel;
	
	$result = $obj->saveNewMenu($_POST);
	
	if($result){
            redirect('admin/menus', 'Nové menu bolo uložené, zadajte teraz položky');
	}
	else{
            $this->template->setView('add');
	}
  
  }
  
  function actionDelete($id)
  {
    if(Application::$delete){
		$obj = new AdmMenusModel;
		
		$result = $obj->delete($id);
		
		if($result){
		  redirect('admin/menus', 'Menu bolo vymazane.');
		}
		else{
		  Application::setError('Odstránenie menu sanepodarilo.');
		  $this->template->setView('default');
		}
	}
	else{
	   redirect('error/default/no_permision');
	}
  
  }
  
  /******************************************* RENDERERS */
  function renderDefault()
  {
	if(!Application::$view) redirect('error/show/403');
	$items = new AdmMenusModel;
	
	$result = $items->findAll();
	
        $this->template->title = MENUS_ADMIN_TITLE;
	$this->template->menus = $result;
	$this->template->items_perm = $this->getPermisionForAction(Application::$logged['role'], 'menu_items', 'view');
  
  }
  
  function renderAdd($id)
  {
    if(Application::$add){
		$this->template->setView('edit');
		$this->template->title = MENUS_ADMIN_ADD_TITLE;
		$this->template->form = $this['addMenuForm'];
	}
	else{
		redirect('error/default/no_permision');
	}
  
  }
  
  function renderEdit($id)
  {
        if(Application::$edit){
		                
		$this->template->title = MENUS_ADMIN_EDIT_TITLE;
		$this->template->form = $this['editMenuForm'];
        }
	else{
	    redirect('error/default/no_permision');
	}
  }
  
  
  /********************************************* FACTORIES */
  public function createControlEditMenuForm()
  {
        $obj = new AdmMenusModel;
	$values = $obj->find($this->id);
        
        $form = new Form('edit-menu', 'frm-edit-menu', Application::link('admin/menus/save/'.$this->id) );
	
	if($_POST){
	  $values = $_POST;
	}
	
	foreach($values as $key => $val){
	  $$key = $val;
	}
	
	isset($lang_code) ? $lang=$lang_code : $lang = $lang;
	
	$form->insertContent(Application::createLangInput($lang));
	$form->addHidden('system', $system);
	$form->addText('name', NAME, $name);
		$form->addRule('name', Form::FILLED, MENUS_FRM_RULE_NAME);
	if(!$system){
	$form->addText('machine_name', MACHINE_NAME, $machine_name);
		$form->addDescription('machine_name', MENUS_FRM_DESC_MACH_NAME);
    }
	else{
	$form->addHidden('machine_name', $machine_name);
	}
	$form->addSubmit('save', SAVE);
	
	return $form->render();
  
  }
  
  public function createControlAddMenuForm()
  {
  
    $form = new Form('add-menu', 'frm-add-menu', Application::link('admin/menus/saveNew') );
	
	$system = 0; $name = ''; $machine_name = ''; $lang = 'none';
	
	if($_POST){
	  foreach($_POST as $key => $val){
	    $$key = $val;
	  }
	}
	
	isset($lang_code) ? $lang=$lang_code : $lang = $lang;
	
	$form->insertContent(Application::createLangInput($lang));
	$form->addHidden('system', $system);
	$form->addText('name', NAME, $name);
		$form->addRule('name', Form::FILLED, MENUS_FRM_RULE_NAME);
	if(!$system){
	$form->addText('machine_name', MACHINE_NAME, $machine_name);
		$form->addDescription('machine_name', MENUS_FRM_DESC_MACH_NAME);
    }
	else{
	$form->addHidden('machine_name', $machine_name);
	}
	$form->addSubmit('save', SAVE);
	
	return $form->render();
  
  }

}