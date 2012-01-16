<?php

class Languages extends BaseAdmin
{

  // Properties
  
  // Constructor
  public function __construct()
  {
    $this->perm_mach_name = 'languages';
    parent::__construct();

  }
  
  // Methods
  /***************************************** ACTIONS ***/
  function actionSave()
  {
    if(Form::$isvalid){
		$obj = new AdmLanguagesModel;
		$result = $obj->save($_POST);
		if($result){
			redirect('admin/languages', CHANGES_WERE_SAVED);
		}
		else{
			Application::setError(SAVING_FAILED);
			Application::$id = $_POST['langid'];
			$this->template->setView('edit');
		}
	}
	else{
		Application::$id = $_POST['langid'];
		$this->template->setView('edit');
	}
  }
  
  function actionSaveNew()
  {
	if(Form::$isvalid){
		$obj = new AdmLanguagesModel;
		$result = $obj->saveNew($_POST);
		if($result){
			redirect('admin/languages', CHANGES_WERE_SAVED);
		}
		else{
			Application::setError(SAVING_FAILED);
			$this->template->setView('add');
		}
	}
	else{
		$this->template->setView('add');
	}
  }
  
  function actionSaveActive()
  {
	
	$obj = new AdmLanguagesModel;
	$result = $obj->saveActive($_POST);
	if($result){
		redirect('admin/languages', CHANGES_WERE_SAVED);
	}
	else{
		Application::setError(SAVING_FAILED);
		$this->template->setView('default');
	}
  }
  
  function actionSetDefault($id)
  {
    $obj = new AdmLanguagesModel;
	$result = $obj->setDefault($id);
	$result ? $error = null : $error = LANG_ADMIN_ERR_ONE;
	redirect(Application::$language['code'].'/admin/languages', $error);exit;
  }
  
  function actionDelete($id)
  {
    $obj = new AdmLanguagesModel;
	$result = $obj->delete($id);
	$result ? $error = DELETED : $error = LANG_ADMIN_ERR_TWO;
	redirect(Application::$language['code'].'/admin/languages', $error);exit;
  }
  
  /***************************************** RENDERERS ***/
  function renderDefault()
  {
    if(!Application::$view) redirect(Application::$language['code'].'/error/show/403');
  	$this->template->title = LANG_ADMIN_TITLE;
	$this->template->content = $this->createList();
  
  }
  
  function renderAdd()
  {
    if(!Application::$add) redirect(Application::$language['code'].'/error/show/403');
  	$this->template->setView('default');
	$this->template->title = LANG_ADMIN_ADD_TITLE;
	$this->template->content = $this->createAddForm();
  }
  
  function renderEdit($id)
  {
    if(!Application::$edit) redirect(Application::$language['code'].'/error/show/403');
	$this->template->setView('default');
	$this->template->title = LANG_ADMIN_EDIT_TITLE;
	$this->template->content = $this->createEditForm($id);
  }
  
  /***************************************** FACTORIES ***/
  private function createAddForm()
  {
    $form = new Form('addLanguage', 'admin-form', BASEPATH.'/admin/languages/saveNew');
	
	$_POST ? $values =$_POST : $values = $form->getZeroValues('languages');
	
	foreach($values as $key => $val){
		$$key = $val;
	}
	
	$form->addText('name', NAME, $name);
		$form->addRule('name', Form::FILLED, LANG_FRM_RULE_NAME);
	$form->addText('langid', CODE, $langid, 'frm-text', 2,2);
		$form->addRule('langid', Form::FILLED, LANG_FRM_RULE_LANGID);
		$form->addDescription('langid', LANG_FRM_DESC_LANGID);
	$form->addText('eng_machine_name', MACHINE_NAME, $eng_machine_name);
		$form->addRule('eng_machine_name', Form::FILLED, LANG_FRM_RULE_MACH_NAME);
		$form->addDescription('eng_machine_name', LANG_FRM_DESC_MACH_NAME);
	$form->addText('main_page_path', URL_ALIAS, $main_page_path);
		$form->addDescription('main_page_path', LANG_FRM_DESC_URL_ALIAS);
	$form->emptyLine();
	$form->addSubmit('save', SAVE);
	
	return $form->render();
	
  }
  
  private function createEditForm($langid)
  {
    $form = new Form('addLanguage', 'admin-form', Application::link('admin/languages/save') );
	
	$obj = new AdmLanguagesModel;
	
	$_POST ? $values = $_POST : $values = $obj->find($langid);
	
	foreach($values as $key => $val){
		$$key = $val;
	}
	
	$form->addHidden('langid', $langid);
	$form->addText('name', NAME, $name);
		$form->addRule('name', Form::FILLED, LANG_FRM_RULE_NAME);
	$form->addText('eng_machine_name', MACHINE_NAME, $eng_machine_name);
		$form->addRule('eng_machine_name', Form::FILLED, LANG_FRM_RULE_MACH_NAME);
		$form->addDescription('eng_machine_name', LANG_FRM_DESC_MACH_NAME);
	$form->addText('main_page_path', URL_ALIAS, $main_page_path);
		$form->addDescription('main_page_path', LANG_FRM_DESC_URL_ALIAS);
	$form->emptyLine();
	$form->addSubmit('save', SAVE);
	
	return $form->render();
	
  }
  
  private function createList()
  {
    $obj = new AdmLanguagesModel;
	
	$rows = $obj->findAll();
	
	$thead = '<thead>
			    <tr>
				  <td>'.NAME.'</td>
				  <td>'.CODE.'</td>
				  <td>'.DEF.'</td>
				  <td>'.ACTIVE.'</td>
				  <td colspan="2">'.ACTIONS.'</td>
				</tr>
			  </thead>';
	$trs = '';
	$ids = '';
	$form = new Form('language-list', 'admin-form-table', BASEPATH.'/admin/languages/saveActive');
	$output = $form->start();
	foreach($rows as $row){
	  $ids .= $row['langid'].':';
	  $form->addHidden($row['langid'].'_main_lang', $row['main_lang']);
	  if($row['system']){
	    $form->addHidden($row['langid'].'_active', 1);
	  }
	  else{
	    $form->addCheckbox($row['langid'].'_active', 1, '', '', $row['active']);
      }
	  
	  if(Application::$delete){
		if(!$row['system']){
	      $img = Html::elem('img', array('src'=>BASEPATH.'/images/icon-delete.jpg', 'alt'=>DELETE, 'title'=>DELETE));
		  $delete = Html::elem('a', array('href'=>BASEPATH.'/admin/languages/delete/'.$row['langid'], 'class'=>'delete'), $img);
		}
		else{
		  $img = Html::elem('img', array('src'=>BASEPATH.'/images/icon-delete-gr.jpg', 'alt'=>DELETE, 'title'=>DELETE));
		  $delete = Html::elem('span', null, $img);
		}
	  }
	  else{
		$img = Html::elem('img', array('src'=>BASEPATH.'/images/icon-delete-gr.jpg', 'alt'=>DELETE, 'title'=>DELETE));
		$delete = Html::elem('span', null, $img);
	  }
	  
	  if(Application::$edit){
	    $active = $form->renderSingle($row['langid'].'_main_lang') . $form->renderSingle($row['langid'].'_active');
		//if(!$row['system']){
		  $img = Html::elem('img', array('src'=>BASEPATH.'/images/icon-edit.jpg', 'alt'=>EDIT, 'title'=>EDIT));
		  $edit = Html::elem('a', array('href'=>Application::link('admin/languages/edit/'.$row['langid']), 'class'=>'edit'), $img);
		/*}
		else{
		  $img = Html::elem('img', array('src'=>BASEPATH.'/images/icon-edit-gr.jpg', 'alt'=>EDIT, 'title'=>EDIT));
		  $edit = Html::elem('span', null, $img);
		}*/
	  }
	  else{
		$row['active'] ? $active = Application::getVar('yes') : $active = Application::getVar('no');
		$img = Html::elem('img', array('src'=>BASEPATH.'/images/icon-edit-gr.jpg', 'alt'=>EDIT, 'title'=>EDIT));
		$edit = Html::elem('span', null, $img);
	  }
	  
	  if($row['main_lang']){
	    $default = DEF;
	  }
	  else{
	    if(Application::$edit && $row['active']){
	      $default = Html::elem('a', array('href'=>BASEPATH.'/admin/languages/setDefault/'.$row['langid']), LANG_ADMIN_SET_DEFAULT);
		}
		else{
		  $default = '&nbsp;';
		}
	  }
	  
	  $td = Html::elem('td', null, $row['name']);
	  $td .= Html::elem('td', array('style'=>'width:30px;text-align:center'), $row['langid']);
	  $td .= Html::elem('td', array('style'=>'width:180px;text-align:center'), $default);
	  $td .= Html::elem('td', array('style'=>'width:30px;text-align:center'), $active);
	  $td .= Html::elem('td', array('style'=>'width:30px;text-align:center'), $edit);
	  $td .= Html::elem('td', array('style'=>'width:30px;text-align:center'), $delete);
	  $trs .= Html::elem('tr', null, $td);
	}
	$tbody = "<tbody>$trs</tbody>";
	
	//Add link
	if(Application::$add){
	  $output .= Html::elem('a', array('class'=>'add-link', 'href'=>Application::link('admin/languages/add') ), '[ '.ADD_NEW.' ]');
	}
	
	$output .= Html::elem('table', array('class'=>'admin-table', 'cellspacing'=>'0', 'style'=>'width:100%'), $thead . $tbody);
	
	if(Application::$edit && count($rows) > 1){
	  $form->addHidden('ids', substr($ids, 0, -1));
	  $form->addSubmit('save', SAVE);
	  $output .= $form->renderSingle('ids');
	  $output .= '<br/>'.$form->renderSingle('save');
	}
	
	$output .= $form->end();
	
	return $output;
	
  }
  
}