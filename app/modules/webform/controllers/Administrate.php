<?php
class Administrate extends WebformBaseController
{

  // Properties
  private $pages;
  
  // Constructor
  public function __construct()
  {
    
	$this->runSettings();
	$this->getPages();
	
        parent::__construct();

  }
  
  protected function beforeRender() {
      parent::beforeRender();
      
      $menu = Html::elem('div')->setClass('webform-adm-links');
      
      $menuItems['configure'] = Html::elem('a')
                               ->href( Application::link('webform/administrate') )
                               ->setCont("[&nbsp;".WF_ADMIN_TPL_MESSAGES_ADMINISTRATION."&nbsp;]");
      $menuItems['settings'] = Html::elem('a')
                               ->href( Application::link('webform/administrate/settings') )
                               ->setCont("[&nbsp;".WF_ADMIN_TPL_MESSAGES_SETTINGS."&nbsp;]");
      $menuItems['default'] = Html::elem('a')
                               ->href( Application::link('webform/administrate/default') )
                               ->setCont("[&nbsp;".WF_ADMIN_TPL_MESSAGES_HISTORY."&nbsp;]");
      $menuItems['add'] = Html::elem('a')
                               ->href( Application::link('webform/administrate/add') )
                               ->setCont("[&nbsp;".WF_ADMIN_TPL_ADD_FORM."&nbsp;]");
      
      // unset active menu item
      unset( $menuItems[Application::$action] );
      
      foreach ($menuItems as $item) {
          $menu->add( $item );
      }
      
      $this->template->navigation = $menu;
  }
  
  // Methods
  /***************************************** ACTIONS ***/
  function actionList()
  {
    if(!Application::$logged['status']){
	  redirect('error/show/403');
	}
	else{
	  $this->setRender('admin');
	}
  }
  
  function actionSend()
  {
	if(!$_POST) redirect('error/show/404');
	
	if(Form::$isvalid){
	  $obj = new WebformModel;
	  $result = $obj->send($_POST);
	  if($result){
	    redirect('webform/show/'.$_POST['webform_id'], WF_ACTION_SEND_SEND_MESSAGE);
	  }
	  else{
		Application::$id = $_POST['webform_id'];
	    Application::setError(WF_ACTION_SEND_SEND_FAILED_MESSAGE);
	    $this->setRender('show');
	  }
	}
	else{
	  Application::$id = $_POST['webform_id'];
	  $this->setRender('show');
	}
  }
  
  function actionSave()
  {
    if(!$_POST) redirect('error/show/404');
	
	if(!Application::$edit) redirect('error/show/1');
	
	if(Form::$isvalid){
	  $obj = new WebformModel;
	  $result = $obj->save($_POST);
	  if($result){
	    redirect(Application::$pathRequest, SAVE_OK);
	  }
	  else{
	    Application::$id = $_POST['id'];
	    //redirect(Application::$pathRequest, 'Uloženie úpravy zlyhalo.');
		$this->setRender('edit');
	  }
	}
	else{
	  $this->setRender('edit');
	}
  }
  
  function actionSavenew()
  {
    if(!$_POST) redirect('error/show/404');
	
	if(!Application::$add) redirect('error/show/1');
	
	if(Form::$isvalid){
	  $obj = new WebformModel;
	  $result = $obj->saveNew($_POST);
	  if($result){
	    redirect('webform/administrate', WF_SAVE_NEW_FORM_SAVED);
	  }
	  else{
	    //redirect('webform/admin', WF_SAVE_NEW_FORM_SAVE_FAILED);
		Application::setError(WF_SAVE_NEW_FORM_SAVE_FAILED);
		$this->setRender('add');
	  }
	}
	else{
	  $this->setRender('add');
	}
  }
  
  function actionSaveSettings()
  {
    if(!$_POST) redirect('error/show/404');
	
	if(Form::$isvalid){
	  $obj = new WebformModel;
	  $result = $obj->saveSettings($_POST);
	  if($result){
	     redirect('webform/administrate/settings', 'Nastavenia boli uložené.');
	  }
	  else{
	    Application::setError('Uloženie nastavení zlyhalo.');
		$this->setRender('settings');
	  }
	}
	else{
	  $this->setRender('settings');
	}
  }
  
  function actionDelete($id)
  {
    if(!$id) redirect('error/show/404');
	
	if(Application::$delete){
	  $obj = new WebformModel;
	  $result = $obj->delete($id);
	  if($result){
	    redirect('webform/administrate', DELETED);
            //$this->setRender('Configure');
	  }
	  else{
            redirect('webform/administrate', DELETE_FAILED);
	  }
	}
	else{
	  redirect('error/show/1');
	}
  }
  
  function actionDeleteMessage($id)
  {
    if(!$id) redirect('error/show/404');
	if(Application::$delete){
	  $obj = new WebformModel;
	  $result = $obj->deleteMessage($id);
	  if($result){
	    redirect('webform', DELETED);
	  }
	  else{
	    redirect('webform', DELETE_FAILED);
	  }
	}
	else{
	  redirect('webform/administrate', YOU_HAVE_NO_PERMISION);
	}
  }
  
  function actionAddField()
  {
	if(!$_POST) redirect('error/show/404');
	
	if(!Application::$add) redirect('error/show/1');
    if(Form::$isvalid){
	
	  $obj = new WebformModel;
	  
	  Application::$id = $_POST['webform_id'];
	  
	  $result = $obj->saveField($_POST);
	  
	  if($result){
	    redirect('webform/administrate/fields/'.$_POST['webform_id'], 'Nové pole bolo pridané.');
	  }
	  else{
	    Application::setError('Pri ukladaní došlo k chybe.');
		$this->setRender('fields');
	  }
	
	}
	else{
	
	  $this->setRender('fields');
	
	}
  
  }
  
  function actionSavefield()
  {
    if(!$_POST) redirect('error/show/404');
	
	if(!Application::$edit) redirect('error/show/1');
	
    if(Form::$isvalid){
	
	  $obj = new WebformModel;
	  
	  $result = $obj->updateField($_POST);
	  
	  if($result)
	    redirect($_SESSION['pathRequest'], 'Zmeny boli ulozené');
      else{
	    Application::setError('Pri ukladaní došlo k chybe.');
		Application::$id = $_POST['id'];
		$this->setRender('editfield');
	  }
	
	}
	else{
	  Application::$id = $_POST['id'];
	  $this->setRender('editfield');
	}
  
  }
  
  function actionDeletefield($id)
  {
    if(!$id) redirect('error/show/404');
	
	if(!Application::$delete) redirect('error/show/1');
	
    $obj = new WebformModel;
	
	$result = $obj->deleteField($id);
	
	if($result){
	  redirect($_SESSION['pathRequest'], DELETED);
	}
    else{
	  redirect($_SESSION['pathRequest'], DELETE_FAILED);
	}
  }
  
  function actionOrderFields()
  {
	
	if(!$_POST) redirect('error/show/404');
	
    $obj = new WebformModel;
	$result = $obj->orderFields($_POST);
	
	if($result){
	  redirect('webform/administrate/fields/'.$_POST['contid'], WF_ACTION_ORDER_FIELDS_OK);
	}
	else{
	  Application::setError(WF_ACTION_ORDER_FIELDS_FAILED);
	  Application::$id = $_POST['contid'];
	  $this->setRender('fields');
	}
  
  }
   
  /***************************************** RENDERERS ***/
  function renderDefault()
  {
    if(Application::$logged['role'] == 'admin' or Application::$logged['role'] == 'editor'){
	
		$obj = new WebformModel;
		$this->template->title = 'Databáza správ';
		$this->template->messages = $obj->findForPage();
		$this->template->frmnames = $obj->getFormNames();
		$this->template->paginator = $this->createPaginator(1);
    }
	else{
		redirect('error/show/403');exit;
	}
  }
  
  function renderPage($id)
  {
    if(Application::$logged['role'] == 'admin' or Application::$logged['role'] == 'editor'){
		$obj = new WebformModel;
		$this->template->view = 'default';
		$this->template->title = 'Databáza správ';
		$this->template->messages = $this->pages[$id];
		$this->template->frmnames = $obj->getFormNames();
		$this->template->paginator = $this->createPaginator($id);
	}
	else{
		redirect('error/show/403');exit;
	}
  
  }
  
  function renderShow($id)
  {
    if(Application::$view){
		if(isset($_REQUEST['q'])) $_SESSION['pathRequest'] = $_REQUEST['q'];
		
		$obj = new WebformModel;
		$row = $obj->find($id);
		
		//check if language only content
		if(!$row['lang']){
			//do nothing
		}
		elseif($row['lang'] && $row['lang'] == 'none'){
			//do nothing
		}
		elseif($row['lang'] && $row['lang'] != Application::$language['code']){
			redirect('error/show/2');exit;
		}
		
		$body = $this->texy->process($row['body']);
		$form = $this->createWebForm($id);
		if($row['hide_on_load']){
		  $formlink = Html::elem('div', array('class'=>'wf-show-lnk'), '[ '.WF_RENDER_SHOW_OPEN_FORM.' &#9660; ]');
		}
		else{
		  $formlink = '';
		}
		if($row['form_after_text']){
		  $content = $body . $formlink . $form;
		}
		else{
		  $content = $formlink . $form . $body;
		}
		$this->template->title = $row['title'];
		$this->template->content = $content;
		$this->template->hide = $row['hide_on_load'];
	}
	else{
	    redirect('error/show/404');
	}
  }
  
  function renderAdd()
  {
    if(!Application::$add) redirect('error/show/1');
	
	$this->template->setView('settings');
	$this->template->title = WF_RENDER_ADD_TITLE;
	$this->template->content = $this->createAddForm();
  }
  
  function renderEdit($id)
  {
        if(Application::$edit){
	  if(isset($_SESSION['destination'])) $_SESSION['pathRequest'] = $_SESSION['destination'];
  	  $this->template->setView('settings');
	  $this->template->title = WF_RENDER_EDIT_TITLE;
	  $this->template->content = $this->createEditForm($id);
	}
	else{
	  redirect('webform/admin', YOU_HAVE_NO_PERMISION);
	}
  }
  
  function renderConfigure()
  {
	
	if(Application::$logged['role'] == 'admin' or Application::$logged['role'] == 'editor'){
		$obj = new WebformModel;
		$rows = $obj->findAll();
		$this->template->title = WF_ADMIN_TITLE;
		$this->template->content = $rows;
	}
	else{
		redirect('error/show/403');
	}
  }
  
  function renderSettings()
  {
    if(Application::$logged['role'] == 'admin' or Application::$logged['role'] == 'editor'){
  	$this->template->title = WF_RENDER_SETTINGS_TITLE;
	$this->template->content = $this->createSettingsForm();
	}
	else{
		redirect('error/show/403');exit;
	}
  
  }
  
  function renderFields($id)
  {
    // Set last path request
	$_SESSION['pathRequest'] = $_REQUEST['q'];
	
	if(!$id) redirect('error/show/404');
	
	if(!Application::$edit) redirect('error/show/403');
	
	$obj = new WebformModel;
	$type = $obj->findOne($id);
	$table = $obj->findAllFields($id);
		
	$this->template->title = WF_FIELDS_TITLE.$type['title'];
	$this->template->id = $id;
	$this->template->table = $table;
	$this->template->form = $this->createAddFieldForm($id);
  
  }
  
  function renderEditfield($id)
  {
    if(!$id) redirect('error/show/404');
	
	if(!Application::$edit) redirect('error/show/403');
	
    $obj = new WebformModel;
	$result = $obj->findField($id);
	
	$this->template->title = WF_RENDER_EDIT_FIELD_TITLE.$result['label'];
	$this->template->form = $this->createEditFieldForm($result);
  
  }
  
  /***************************************** FACTORIES ***/
  private function createSettingsForm()
  {
    $obj = new WebformModel;
	$rows = $obj->getSettings();
	
	foreach($rows as $row){
	  $$row['frm_name'] = $row['value'];
	  $label[$row['frm_name']] = $row['title'];
	  $desc[$row['frm_name']] = $row['description'];
	}
	if($_POST){
	  foreach($_POST as $key => $val){
	    $$key = $val;
	  }
	  if(!isset($auto_clean)) $auto_clean = 0;
	}
	
	$form = new Form('wf-settings', 'frm-wf-settings', Application::link('webform/administrate/save-settings') );
	$form->addText('messages_per_page', $label['messages_per_page'], $messages_per_page, 2, 2);
	    $form->addDescription('messages_per_page', $desc['messages_per_page']);
	$form->addSelect('order', array('ASC' => 'Staršie >> Novšie', 'DESC'=>'Novšie >> Staršie'), $label['order'], $order);
		$form->addDescription('order', $desc['order']);
	$form->addText('notificate', $label['notificate'], $notificate);
		$form->addDescription('notificate', $desc['notificate']);
		$form->addRule('notificate', Form::EMAIL, 'Zadajte korektný email.');
	$form->addCheckbox('auto_clean', 1, '', $label['auto_clean'], $auto_clean);
		$form->addDescription('auto_clean', $desc['auto_clean']);
	$form->addText('delete_older_then', $label['delete_older_then'], $delete_older_then, 'frm-text', 5, 5);
		$form->addDescription('delete_older_then', $desc['delete_older_then']);
		$form->addRule('delete_older_then', Form::MIN, 1,'Zadajte číslo s minimálnou hodnotou 1.');
	$form->emptyLine();
	$form->addSubmit('save', 'Uložiť');
	
	return $form->render();
	
  }
  
  private function createAddForm()
  {
    
    $form = new Form('add-form', 'wf-add-form', Application::link('webform/administrate/savenew') );
	
	if($_POST){
	  foreach($_POST as $key => $val){
	    $$key = $val;
	  }
	  if(!isset($form_after_text)) $form_after_text = 0;
	  if(!isset($hide_on_load)) $hide_on_load = 0;
	}
	else{
	  $values = $form->getZeroValues('webform');
	  foreach($values as $key => $val){
	    $$key = $val;
	  }
	}
	
	// add language selector
	isset($lang_code) ? $lang_id = $lang_code : $lang_id = 'none';
	$form->insertContent(Application::createLangInput($lang_id));
	
	//form part
	$form->addText('title', TITLE, $title);
	  $form->addRule('title', Form::FILLED, WF_FRM_TITLE_RULE);
	$form->addText('form_title', WF_FRM_FORM_TITLE_LABEL, $form_title);
		$form->addDescription('form_title', WF_FRM_FORM_TITLE_DESC);
	$form->addTextarea('body', BODY, $body);
	  $form->addDescription('body', WF_FRM_BODY_DESC);
	$form->addText('path_alias', 'URL Alias', $path_alias, 'frm-text', 80, 128);
	$form->emptyLine();
	
	//settings part
	$form->addCheckbox('form_after_text', 1, WF_FRM_FORM_AFTER_TEXT_LABEL, null,$form_after_text, 'frm-checkbox', false);
	$form->addCheckbox('hide_on_load', 1, WF_FRM_HIDE_ON_LOAD_LABEL, null,$hide_on_load, 'frm-checkbox', false);
	$form->addText('email', EMAIL, $email, 'frm-text', 80, 256);
	$form->emptyLine();
	
	// if user has rights, create fanthom form for menu insert
	if(Application::$logged['role'] == 'admin' or Application::$logged['role'] == 'editor'){
		
		include(APP_DIR.'/admin/models/AdmMenusModel.php');
		
		$menuobj = new AdmMenusModel;
		$menus = $menuobj->findAll();
		
		$child_of = 0;
		$menu_id = 2;
		isset($values['menu_title']) ? $menu_title = $values['menu_title'] : $menu_title = '';
		isset($values['menu_items']) ? $defaultSelect = $values['menu_items'] : $defaultSelect = $menu_id.':'.$child_of;
		
		$selArray = array();
		foreach($menus as $menu){
			$selArray += array($menu['id'].':0'=>'['.$menu['name'].']');
			$structure = $menuobj->getMenuStructure($menu['id']);
			if($structure) $selArray += $menuobj->getSelectArray($menu['id'], $structure);
		}
		$form4 = new Form('add-form', 'wf-add-form');
		$form4->addText('menu_title', MENU_TITLE_LABEL, $menu_title);
			$form4->addDescription('menu_title', MENU_TITLE_DESC);
		$form4->addSelect('menu_items', $selArray, MENU_ITEMS_LABEL, $defaultSelect);
			$form4->addDescription('menu_items', MENU_ITEMS_DESC);
		$form->insertContent('<fieldset><legend>'.MENU_SETTINGS.'</legend>'.$form4->renderSingle('menu_title').$form4->renderSingle('menu_items').'</fieldset>');
                
                
	}
	if($this->modulesHtmlContent) $form->insertContent($this->modulesHtmlContent);
        
	$form->addSubmit('save', SAVE_CHANGES);
	
    return $form->render();
  }
  
  
  
  private function createEditForm($id)
  {
    
    $form = new Form('wf-editform', 'wf-editform', Application::link('webform/administrate/save') );
	
	if($_POST){
	  foreach($_POST as $key => $val){
	    $$key = $val;
	  }
	  if(!isset($form_after_text)) $form_after_text = 0;
	  if(!isset($hide_on_load)) $hide_on_load = 0;
	}
	else{
	  $obj = new WebformModel;
	  $values = $obj->find($id);
	  foreach($values as $key => $val){
	    $$key = $val;
	  }
	}
	
	// add language selector
	isset($lang_code) ? $lang_id = $lang_code : $lang_id = $lang;
	$form->insertContent(Application::createLangInput($lang_id));
	
	//form part
	$form->addHidden('id', $id);
        $form->addHidden('cid', $this->cid);
	
	isset($old_path_alias) ? $path_alias = $old_path_alias : $path_alias = $path_alias;
	$form->addHidden('old_path_alias', $path_alias);
	
	$form->addText('title', TITLE, $title);
	  $form->addRule('title', Form::FILLED, WF_FRM_TITLE_RULE);
	$form->addText('form_title', WF_FRM_FORM_TITLE_LABEL, $form_title);
		$form->addDescription('form_title', WF_FRM_FORM_TITLE_DESC);
	$form->addTextarea('body', BODY, $body);
	  $form->addDescription('body', WF_FRM_BODY_DESC);
	$form->addText('path_alias', 'URL Alias', $path_alias, 'frm-text', 80, 128);
		$form->addDescription('path_alias', WF_FRM_PATH_ALIAS_RULE);
		$form->addRule('path_alias', Form::ISFILLED, '/^[a-zA-Z0-9_-]+$/', WF_FRM_PATH_ALIAS_RULE);
	$form->emptyLine();
	
	//settings part
	$form->addCheckbox('form_after_text', 1, WF_FRM_FORM_AFTER_TEXT_LABEL, null,$form_after_text, 'frm-checkbox', false);
	$form->addCheckbox('hide_on_load', 1, WF_FRM_HIDE_ON_LOAD_LABEL, null,$hide_on_load, 'frm-checkbox', false);
	$form->addText('email', EMAIL, $email, 'frm-text', 80, 256);
		$form->addRule('email', Form::ISFILLED, '/^[^0-9][A-z0-9_-]+([.][A-z0-9_-]+)*[@][A-z0-9_-]+([.][A-z0-9_-]+)*[.][A-z]{2,4}$/', WF_FRM_EMAIL_RULE);
	$form->emptyLine();
	
	// if user has rights, create fanthom form for menu insert
	if(Application::$logged['role'] == 'admin' or Application::$logged['role'] == 'editor'){
		
		include(APP_DIR.'/admin/models/AdmMenusModel.php');
		
		$menuobj = new AdmMenusModel;
		$menus = $menuobj->findAll();
		// get default value
		$row = $menuobj->findItemByContentId('webform',$this->id);
		if($row){
			$child_of = $row['child_of'];
			$menu_id = $row['menu_id'];
			$menu_title = $row['title'];
		}
		else{
			$child_of = 0;
			$menu_id = 2;
			$menu_title = '';
		}
		
		$defaultSelect = $menu_id.':'.$child_of;
		
		$selArray = array();
		foreach($menus as $menu){
			$selArray += array($menu['id'].':0'=>'['.$menu['name'].']');
			$structure = $menuobj->getMenuStructure($menu['id']);
			if($structure) $selArray += $menuobj->getSelectArray($menu['id'], $structure);
		}
		$form4 = new Form('wf-editform', 'wf-editform');
		$form4->addText('menu_title', MENU_TITLE_LABEL, $menu_title);
			$form4->addDescription('menu_title', MENU_TITLE_DESC);
		$form4->addSelect('menu_items', $selArray, MENU_ITEMS_LABEL, $defaultSelect);
			$form4->addDescription('menu_items', MENU_ITEMS_DESC);
		$form->insertContent('<fieldset><legend>'.MENU_SETTINGS.'</legend>'.$form4->renderSingle('menu_title').$form4->renderSingle('menu_items').'</fieldset>');
                
	}
        
        if($this->modulesHtmlContent) $form->insertContent($this->modulesHtmlContent);
	
	$form->addSubmit('save', SAVE);
	
    return $form->render();
  }
  
  private function createWebForm($id)
  {
    $form = new Form('webform-'.$id, 'web-form', Application::link('webform/administrate/send') );
	
	$cont_frm_id = $id;
	
    $obj = new WebformModel;
	$row = $obj->find($id);
	$form_title = $row['form_title'];
	
	$fields = $obj->findAllFields($id);
	
	if($fields){
		
		if($_POST){
		  $values = $_POST;
		}
		else{
		  // get default vals
		  foreach($fields as $field){
			$values[$field['frm_name']] = $field['default'];
		  }
		}
		
		$form->addHidden('id', $id);
		
		//Create fields
		foreach($fields as $field){
			
			// Set value
			if(isset($values[$field['frm_name']])){
				$value = $values[$field['frm_name']];
			}
			else{
				$value = '';
			}
			
			//set attributes
			if($field['attributes']) {
				$attrs = $this->parseAttributes($field['attributes']);
				foreach($attrs as $key => $val){
				  $$key = $val;
				}
			}
			
			$type = $field['type'];
			
			$machine_type = $field['machine_field_type'];
			
			$action = 'add'.ucfirst($field['type']);
		  
			$label = $field['label'];
			
			$webform_id = $id;
		  
			if($type == 'text'){
				$form->$action($field['frm_name'], $label, $value, 'frm-'.$field['type'],$size, $maxlength);
				//for numbers
				if(isset($min) && $min > 0){
					$form->addRule($field['frm_name'], Form::MIN, $min, ITEM.' '.$field['label'].' '.WF_CREATE_WEB_FORM_MIN_RULE.$min);
					unset($min);
				}
				if(isset($max) && $max > 0){
					$form->addRule($field['frm_name'], Form::MAX, $max, ITEM.' '.$field['label'].' '.WF_CREATE_WEB_FORM_MAN_RULE.$max);
					unset($max);
				}
				if(isset($min) && $min > 0 && isset($max) && $max > 0){
					$form->addRule($field['frm_name'], Form::RANGE, $min ,$max, ITEM.' '.$field['label'].' '.WF_CREATE_WEB_FORM_RANGE_RULE.$min.' - '.$max);
					unset($min);
					unset($max);
				}
				unset($size);
				unset($maxlength);
			}
			elseif($type == 'textarea'){
				$form->$action($field['frm_name'], $label, $value, 'frm-'.$field['type'],$cols, $rows);
				unset($cols);
				unset($rows);
			}
			elseif($type == 'select' or $type == 'radio'){
				$vals = explode(';', $field['default']);
				$selected = '';
				foreach($vals as $val){
					$valandops[$val] = $val;
				}
				foreach($valandops as $key => $val){
					if(preg_match('/:default/', $key)){
						$key = preg_replace('/:default/', '', $key);
						$val = preg_replace('/:default/', '', $val);
						$selected = $val;
					}
					$valops[$key] = $val;
				}
				
				$form->$action($field['frm_name'], $valops, $label, $selected);
				
				unset($valops);
				unset($selected);
			}
			elseif($type == 'checkbox'){
				if($field['default']) $check = true;
				  else $check = false;
				$form->$action($field['frm_name'], 1, $label, null, $check);
				unset($check);
			}
			elseif($machine_type == 'image'){
				$form->addHidden('image_machine_type', $machine_type);
				$form->addHidden('image_frm_name', $field['frm_name']);
				$form->addHidden('img_max_file_size', $max_file_size*1024000);
				$form->addHidden('preview_size', $preview_size);
				$form->addHidden('icon_size', $icon_size);
				$form->addHidden('thumb_create', $thumb_create);
				$form->$action($field['frm_name'], $label);
			}
			elseif($machine_type == 'file'){
				$form->addHidden('file_machine_type', $machine_type);
				$form->addHidden('file_frm_name', $field['frm_name']);
				$form->addHidden('file_max_file_size', $max_file_size*1024000);
				$form->$action($field['frm_name'], $label);
			}
			if($field['description']){
				$form->addDescription($field['frm_name'], $field['description']);
			}
			if($field['required']){
				$form->addRule($field['frm_name'], Form::FILLED, ITEM.' '.$field['label'].' '.IS_REQUIRED);
			}
			if($machine_type == 'number') $form->addRule($field['frm_name'], Form::NUMERIC, ITEM.' '.$field['label'].' '.WF_CREATE_WEB_FORM_NUMERIC_RULE);
			// if element name contains email add email rule
			if(preg_match('/^[a-z_]*email[a-z_]*$/', $field['frm_name'])) $form->addRule($field['frm_name'], Form::EMAIL, ITEM.' '.$field['label'].' '.WF_CREATE_WEB_FORM_EMAIL_RULE);

		}
		
		$form->addCaptcha('sec_code', WF_CREATE_WEB_FORM_CAPTCHA_LABEL);
			$form->addRule('sec_code', Form::CAPTCHA, WF_CREATE_WEB_FORM_CAPTCHA_RULE);
		$form->addHidden('webform_id', $webform_id);
		
		$form->emptyLine();
		$form->addSubmit('send', SEND);
		$frm = $form->render();

		if($form_title){
		  $header = Html::elem('h3', array('class'=>'wf-form-title title'), $form_title);
		}
		else{
		  $header = '';
		}
		
		$output = Html::elem('div', array('id'=>'wf-form-'.$webform_id, 'class'=>'wf-form-container'), $header.$frm);
		
	}
	else{
		$output = WF_CREATE_WEB_FORM_NO_FIELDS;
	}
	
	return $output;
	
  }
  
  
  private function createAddFieldForm($id)
  {
    //set default values
	$label = ''; $frm_name = ''; $fieldtype = '';
	if($_POST){
	  foreach($_POST as $key => $value){
	    $$key = $value;
	  }
	}
	
	$form = new Form('newField', 'new-field-frm', Application::link('webform/administrate/add-field') );
	
	$obj = new WebformModel;
	$fieldtypes = $obj->getFieldTypes();
	
	$form->addHidden('webform_id', $id);
	$form->addText('label', TITLE, $label);
	  $form->addRule('label', Form::FILLED, WF_FRM_FIELDS_TITLE_RULE);
	$form->addSelect('fieldtype', $fieldtypes, WF_FRM_FIELDS_FIELDTYPE_LABEL, $fieldtype);
	$form->addText('frm_name', WF_FRM_FIELDS_FRM_NAME_LABEL, $frm_name);
	  $form->addRule('frm_name', Form::FILLED, WF_FRM_FIELDS_FRM_NAME_RULE);
	  $form->addRule('frm_name', Form::REGEX, '/^[a-z0-9_]+$/', WF_FRM_FIELDS_FRM_NAME_REGEX_RULE);
	  $form->addDescription('frm_name', WF_FRM_FIELDS_FRM_NAME_DESC);
	$form->addSubmit('save', WF_FRM_FIELDS_SAVE_LABEL);
	
    return $form->render();
  
  }
  
  private function createEditFieldForm($values)
  {
    $id = $values['id'];
	$fld_type = $values['type'];
	$mach_fld_type = $values['machine_field_type'];
	
	if($values['attributes']){
	  $exp_attrs = explode(';', $values['attributes']);
	  foreach($exp_attrs as $attr){
	    list($idx, $val) = explode(':', $attr);
		${"attr_$idx"} = $val;
	  }
	  
	  $render_attrs = true;
	  if($mach_fld_type == 'text'){
	    $attrs_for = 'text';
	  }
	  elseif($mach_fld_type == 'textarea'){
	    $attrs_for = 'textarea';
	  }
	  elseif($mach_fld_type == 'file'){
	    $attrs_for = 'file';
	  }
	  elseif($mach_fld_type == 'image'){
	    $attrs_for = 'image';
	  }
	  elseif($mach_fld_type == 'datetime'){
	    $attrs_for = 'text';
	  }
	  elseif($mach_fld_type == 'date'){
	    $attrs_for = 'text';
	  }
	  elseif($mach_fld_type == 'number'){
	    $attrs_for = 'number';
	  }
	}
	else{
	  $render_attrs = false;
	}
	
	if($fld_type == 'text') $text_as_def = true;
	  else $text_as_def = false;
	
	if($fld_type == 'file') $no_default = true;
	  else $no_default = false;
	
	if($fld_type == 'checkbox'){
      $check_as_def = true;
	  $check = (int)$values['default'];
	}
	else{
      $check_as_def = false;
	}
	
    if($_POST){
	  $values = $_POST;
	}
	
	foreach($values as $key => $val){
	  $$key = $val;
	}
	
	if(isset($required) && $required == 1) $checked = true;
	  else $checked = false;
	
	$form = new Form('edit-field', 'frm-edit-field', Application::link('webform/administrate/savefield') );
	
	$form->addHidden('id', $id);
	$form->addText('label', 'Názov poľa (titulok)', $label);
	  $form->addRule('label', Form::FILLED, 'Zadajte názov poľa.');
	$form->addHidden('webform_label',$webform_label);
	//$form->addText('webform_label', 'Popisok na stránke', $webform_label);
	  //$form->addDescription('webform_label', 'Ak zadáte popisok, bude sa tento zobrazovať pred hodnotou poľa. Ak ponecháte prázdne, popisok sa nezobrazí.');
	$form->addTextarea('description', 'Popis', $description, 'frm-textarea' ,30, 3);
	if($text_as_def){
	  $form->addText('default', 'Výchozia hodnota', $default);
	}
	elseif($check_as_def){
	  $form->addRadio('default', array('nie', 'ano'), 'Výchozia hodnota - zaškrtnuté', $check, true);
	}
	elseif($no_default){
	  // do nothing
	  $form->addHidden('default', null);
	}
	else{
	  if($type == 'select' or $type == 'radio' or $type == 'checkboxgroup'){
	    $default = preg_replace('/;/', "\r\n", $default);
		$form->addTextarea('default', 'Výchozia hodnota', $default);
	    $form->addDescription('default', 'Vložte každú položku zoznamu na nový riadok. Za výchoziu položku dajte slovíčko default oddelené dvojbodkou:<br/><i>Položka 1:default<br/>Položka 2<br/>Položka 3</i>');
		$form->addRule('default', Form::FILLED, 'Výchozia hodnota - Zoznam musí buť zadaný.');
	  }
	  else{
	    $form->addTextarea('default', 'Výchozia hodnota', $default);
	  }
	}
	
	// required
	if($fld_type != 'file'){
      $form->addCheckbox('required', 1, 'povinná položka', null, $checked);
	}
	else{
	  $form->addHidden('required', 0);
	}
	// email
	isset($email) ? $email = $email : $email = 0;
	$form->addCheckbox('email', 1, 'Obsah poľa zaradiť do emailu', null, $email);
	
	if($render_attrs){
	  if($attrs_for == 'text'){
	    $form->addText('attr_size', 'Počet zobrazených znakov', $attr_size, 'frm-text',5, 5);
		$form->addText('attr_maxlength', 'Maximálny počet znakov', $attr_maxlength, 'frm-text',5, 5);
	  }
	  elseif($attrs_for == 'textarea'){
	    $form->addText('attr_cols', 'Počet stĺpcov', $attr_cols, 'frm-text',5, 5);
		$form->addText('attr_rows', 'Počet riadkov', $attr_rows, 'frm-text',5, 5);
	  }
	  elseif($attrs_for == 'number'){
	    $form->addText('attr_size', 'Počet zobrazených znakov', $attr_size, 'frm-text',5, 5);
		$form->addText('attr_maxlength', 'Maximálny počet znakov', $attr_maxlength, 'frm-text',5, 5);
		$form->addText('attr_min', 'Minimálna hodnota poľa', $attr_min, 'frm-text',5, 5);
			$form->addDescription('attr_min', 'Ak ponecháte nulu, pole može mať akúkoľvek celočiselnú hodnotu.');
		$form->addText('attr_max', 'Maximálna hodnota poľa', $attr_max, 'frm-text',5, 5);
			$form->addDescription('attr_max', 'Ak ponecháte nulu, pole može mať akúkoľvek celočiselnú hodnotu.');
 	  } 
	  elseif($attrs_for == 'file'){
	    $form->insertContent('Maximálna veľkosť súboru môže byť '.(int)ini_get("upload_max_filesize").'MB. Ak chcete zvýšiť túto hodnotu, musíte zmeniť nastavenie php.ini, alebo požiadajte svojho administrátora');
		$form->addText('attr_max_file_size', 'Maximálna veľkosť súboru v megabajtoch', $attr_max_file_size, 'frm-text',2, 2);
			$form->addRule('attr_max_file_size', Form::MIN, 1, (int)ini_get("upload_max_filesize"),'Max. veľkosť súboru: Číslo musí mať min. hodnotu 1 a max. hodnotu '.(int)ini_get("upload_max_filesize"));
		$form->addText('attr_max_files', 'Maximálny počet súborov', $attr_max_files, 'frm-text',2, 2);
			$form->addDescription('attr_max_files', 'Ak ponecháte nulu, tak počet súborov na stránku bude neobmedzený.');
		$form->addSelect('attr_order_by', array('description'=>'Popisu', 'priority'=>'Priority','datetime'=>'Dátum a času pridania'), 'Zoradiť súbory podľa', $attr_order_by);
	  }
	  elseif($attrs_for == 'image'){
	    $form->insertContent('Maximálna veľkosť súboru môže byť '.(int)ini_get("upload_max_filesize").'MB. Ak chcete zvýšiť túto hodnotu, musíte zmeniť nastavenie php.ini, alebo požiadajte svojho administrátora');
	    $form->addText('attr_max_file_size', 'Maximálna veľkosť súboru v megabajtoch', $attr_max_file_size, 'frm-text',2, 2);
			$form->addRule('attr_max_file_size', Form::RANGE, 1, (int)ini_get("upload_max_filesize"),'Max. veľkosť súboru: Číslo musí mať min. hodnotu 1 a max. hodnotu '.(int)ini_get("upload_max_filesize"));
		$form->addText('attr_max_files', 'Maximálny počet súborov', $attr_max_files, 'frm-text',2, 2);
			$form->addDescription('attr_max_files', 'Ak ponecháte nulu, tak počet súborov na stránku bude neobmedzený.');
		$form->addSelect('attr_order_by', array('description'=>'Popisu', 'priority'=>'Priority','datetime'=>'Dátum a času pridania'), 'Zoradiť obrázky podľa', $attr_order_by);
		$form->addText('attr_preview_size', 'Veľkosť obrázku v pixeloch', $attr_preview_size, 'frm-text', 3, 3);
			$form->addDescription('attr_preview_size', 'Zadajte hodnotu v intervale 300 - 800');
			$form->addRule('attr_preview_size', Form::RANGE, 300, 800, 'Veľkosť obrázku v pixeloch: Zadajte číslo v rozpätí 300 až 800');
		$form->addText('attr_icon_size', 'Veľkosť ikony v pixeloch', $attr_icon_size, 'frm-text', 3, 3);
			$form->addDescription('attr_icon_size', 'Zadajte hodnotu v intervale 50 - 450');
			$form->addRule('attr_icon_size', Form::RANGE, 50, 450, 'Veľkosť ikony v pixeloch: Zadajte číslo v rozpätí 50 až 450');
		$form->addSelect('attr_thumb_create', array('ratio'=>'Zmenšiť a zachovať pomer strán', 'cut'=>'Zmenšiť a orezať na štvorec'), 'Spôsob nahrania ikony', $attr_thumb_create);
	  }
	}
		
	$form->addSubmit('save', 'Uložiť');
	
	return $form->render();
  
  }
  
   
  private function createPaginator($pgNumber)
	{
		if(count($this->pages)>1){
          $limit = 10; // limit of paginator page links
		  
		  $lastPage = count($this->pages); // last page number
		  
		  $startFrom = $pgNumber - ($limit/2); // start paginator from page
		  
		  $startFrom < 1 ? $startFrom = 1 : $startFrom = $startFrom; // if less than 1 set to 1
		  
		  $countTo = $startFrom + ($limit-1); // end paginator with page...
		  
		  if($countTo > $lastPage){ // if more than last then set to last
			 $countTo = $lastPage;
			 $startFrom = $countTo - $limit;
			 $startFrom < 1 ? $startFrom = 1 : $startFrom = $startFrom; // if less than 1 set to 1
		  }
		  
		  $pagesElems = '';
		  
		  $pagesElems .= Html::elem('a', array('href'=>BASEPATH.'/webform/page/1', 'class'=>'cf-page-link first'), "&#9668;&nbsp;prvá&nbsp;").'|';
		  
		  for($i = $startFrom; $i <= $countTo; $i++)
		  {

			if($pgNumber == $i){
			  $pagesElems .= Html::elem('span', array('class'=>'cf-page-active'), "&nbsp;$i&nbsp;").'|';
			}
			else{
			  $pagesElems .= Html::elem('a', array('href'=>BASEPATH.'/webform/page/'.$i, 'class'=>'cf-page-link'), "&nbsp;$i&nbsp;").'|';
			}
		  
		  }
		  
		  $pagesElems .= Html::elem('a', array('href'=>BASEPATH.'/webform/page/'.$lastPage, 'class'=>'cf-page-link last'), "&nbsp;posledná&nbsp;&#9658;");
		  
		  return Html::elem('div', array('class'=>'cf-paginator'), 'Stránka: '.$pagesElems);
		}
		else{
		  return '';
		}
	
	}
  

  /*************************************** OTHER METHODS ***/
  
  private function runSettings()
  {
    $obj = new WebformModel;
  
    $rows = $obj->getSettings();
  
    foreach($rows as $row){
      define($row['constant'], $row['value']);
    }
	
	if(WF_AUTO_CLEAN){
	  $clean_date = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m")  , date("d")-WF_DELETE_OLDER_THEN, date("Y")));
	  db::exec("DELETE FROM webform_messages WHERE datetime < '$clean_date'");
	}
  }
  
  private function getPages()
  {
    $obj = new WebformModel;
	
	$records = $obj->findAllMessages();
	$rec_num = count($records);
	if($rec_num){
	  $pageidx = 1;
	  $idx = 1;
	  $lastidx = (int)WF_MESSAGES_PER_PAGE;
	  foreach($records as $record){
		$messages[$record['id']] = $record;
		if($idx == $lastidx){
		  $idx = 0;
		  $this->pages[$pageidx] = $messages;
		  unset($messages);
		  $pageidx++;
		}
		$idx++;
	  }
	  // Place the rest of messages to last page
	  if(isset($messages) && count($messages)>0){
	    $this->pages[$pageidx] = $messages;
	  }
	}
	else{
	  $this->pages = false;
	}
  }
  
  //******** Internal Function parseAttributes() ****************
  private function parseAttributes($attrs){
	
	  $attritems = explode(';', $attrs);
	  
	  foreach($attritems as $attritem){
	  
	    list($idx, $value) = explode(':',$attritem);
		
		$return[$idx] = $value;
	  
	  }
	  
	  return $return;
	
  }
}
