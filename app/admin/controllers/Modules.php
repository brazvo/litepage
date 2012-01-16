<?php

class Modules extends BaseAdmin
{

  // Properties
  
  // Constructor
  public function __construct()
  {
    /* $this->perm_mach_name = 'content'; */ //
    parent::__construct();

  }
  
  // Methods
  /***************************************** ACTIONS ***/
  function actionSave()
  {
    
  }
  
  function actionSavenew()
  {
    
  }
  
  function actionDelete()
  {
    
  }
  
  function actionInstall()
  {
    $obj = new AdmModulesModel;
	$obj->installModule($_POST);
	redirect('admin/modules/list', 'Modul bol nainštalovaný.');
  }
  
  function actionUninstall()
  {
    $obj = new AdmModulesModel;
	$obj->uninstallModule($_POST);
	redirect('admin/modules/list', 'Modul bol odinštalovaný.');
  }
  
  /***************************************** RENDERERS ***/
  function renderDefault()
  {
    /*if(Application::$logged['role'] != 'admin'){
		redirect('error/default/403');exit;
	}*/
	$obj  = new AdmModulesModel;
        $rows = $obj->findAll();
	$this->template->setView('list');
	if($rows){
		$this->template->title = MODULES_ADMIN_DEFAULT_TITLE;
		$this->template->content = $this->createModulesList($rows);
	}
	else{
		$this->template->title = MODULES_ADMIN_DEFAULT_TITLE;
		$this->template->content = MODULES_ADMIN_ERR_DEFAULT;
	}
  
  }
  
  function renderAdd()
  {
  
  }
  
  function renderEdit()
  {
  
  }
  
  function renderList()
  {
    if(Application::$logged['role'] == 'admin'){
	$this->template->title = MODULES_ADMIN_LIST_TITLE;
	$this->template->content = $this->createModulesListForm();
	}
	else{
	$this->template->title = MODULES_ADMIN_LIST_TITLE;
	$this->template->content = MODULES_ADMIN_ERR_LIST;
	}
  }
  
  /***************************************** FACTORIES ***/
  private function createModulesListForm()
  {
    $obj  = new AdmModulesModel;
    $rows = $obj->findAll();
	
	$output = '<table class="admin-table" cellspacing="0">
	             <thead>
				   <tr>
				     <td>'.NAME.'</td>
					 <td>'.VERSION.'</td>
					 <td>'.DESCRIPTION.'</td>
					 <td>'.INSTALL_UNINSTALL.'</td>
				   </tr>
				 </thead>
				 <tbody>';
	foreach($rows as $module){
	  if($module['installed']) $style = 'style="color:black;background-color:white"';
	  if(!$module['installed']) $style = 'style="color:#333333;background-color:silver"';
	  $output .= '<tr '.$style.'>
	                <td>'.$module['name'].'</td>
					<td>'.$module['version'].'</td>
					<td>'.$module['description'].'</td>';
	  $output .= '<td style="text-align:center">';
	  if(!$module['installed']){
	    $form = new Form('module-'.$module['machine_name'], 'frm-modules-list', BASEPATH.'/admin/modules/install');
		$form->addHidden('machine_name', $module['machine_name']);
		$form->addSubmit('install', INSTALL);
		if($module['machine_name'] !== 'template') $output .= $form->render(); // do not allow install module sceleton
	  }
	  else{
	    $form = new Form('module-'.$module['machine_name'], 'frm-modules-list', BASEPATH.'/admin/modules/uninstall');
		$form->addHidden('machine_name', $module['machine_name']);
		$form->addSubmit('uninstall', UNINSTALL);
		$output .= $form->render();
	  }
	  $output .= '</td>
	             </tr>';
	}
	$output .= '   </tbody>
	              </table>';
				  
	return $output;
	            
  }
  
  private function createModulesList($rows)
  {
  
    $divs = null;
	foreach($rows as $module){
		if($module['installed']){
			//$a = Html::elem('a', array('href'=>BASEPATH.'/'.$module['machine_name'].'/admin'), $module['name']);
			$h3 = Html::elem('h3', array('class'=>'module-list-title'), $module['name']);
			$desc = Html::elem('div', array('class'=>'module-list-desc'), $module['description']);
			$divs .= Html::elem('div', array('class'=>'module-list-container'), $h3.$desc);
		}
	}
	if(!$divs){
		$divs = Html::elem('div', array('class'=>'module-list-container'), 'Nie sú aktivované žiadne moduly.');
	}
	
	return $divs;
  
  }

}