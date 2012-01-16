<?php
class Kontakt extends Controller
{

  // Properties
  private $pages;
  
  // Constructor
  public function __construct()
  {
    $this->perm_mach_name = 'kontakt'; //
	$this->runSettings();
	$this->getPages();
	// only action show may be used by unlogged user
	if(!Application::$logged['status']){
	  if(Application::$action != 'show'){
	    redirect('error/default/no_permision');
	  }
	}
    parent::__construct();

  }
  
  // Methods
  /***************************************** ACTIONS ***/
  function actionAdmin()
  {
    if(!Application::$logged['status']){
	  redirect('login');
	}
	else{
	  $this->render('admin');
	}
  }
  
  function actionSend()
  {

	if(Form::$isvalid){
	  $obj = new AdmKontaktModel;
	  $result = $obj->send($_POST);
	  if($result){
		$redirect = $_SESSION['destination'];
		$_SESSION['destination'] = null;
	    redirect($redirect, KONTAKT_ACT_SEND_MESSAGE_SENT);
	  }
	  else{
		Application::$id = $_POST['contact_frm_id'];
	    Application::setError(KONTAKT_ACT_SEND_MESSAGE_SENT_ERROR);
	    $this->render('show');
	  }
	}
	else{
	  Application::$id = $_POST['contact_frm_id'];
	  $this->render('show');
	}
  }
  
  function actionSave()
  {
    if(Form::$isvalid){
	  $obj = new AdmKontaktModel;
	  $result = $obj->save($_POST);
	  if($result){
	    redirect(Application::$pathRequest, SAVE_OK);
	  }
	  else{
	    redirect(Application::$pathRequest, SAVE_FAILED);
	  }
	}
	else{
	  $this->render('edit');
	}
  }
  
  function actionSavenew()
  {
    if(Form::$isvalid){
	  $obj = new AdmKontaktModel;
	  $result = $obj->saveNew($_POST);
	  if($result){
	    redirect('kontakt/admin', SAVE_OK);
	  }
	  else{
	    redirect('kontakt/admin', SAVE_FAILED);
	  }
	}
	else{
	  $this->render('add');
	}
  }
  
  function actionSaveSettings()
  {
    if(Form::$isvalid){
	  $obj = new AdmKontaktModel;
	  $result = $obj->saveSettings($_POST);
	  if($result){
	    redirect('kontakt/settings', KONTAKT_ACT_SAVE_SETTINGS_SAVE_OK);
	  }
	  else{
	    Application::setError(KONTAKT_ACT_SAVE_SETTINGS_SAVE_FAILED);
		$this->render('settings');
	  }
	}
	else{
	  $this->render('settings');
	}
  }
  
  function actionDelete($id)
  {
    if(Application::$delete){
	  $obj = new AdmKontaktModel;
	  $result = $obj->delete($id);
	  if($result){
	    redirect('kontakt/admin', DELETED);
	  }
	  else{
	    redirect('kontakt/admin', DELETE_FAILED);
	  }
	}
	else{
	  redirect('kontakt/admin', YOU_HAVE_NO_PERMISION);
	}
  }
  
  function actionDeleteMessage($id)
  {
    if(Application::$delete){
	  $obj = new AdmKontaktModel;
	  $result = $obj->deleteMessage($id);
	  if($result){
	    redirect('kontakt', DELETED);
	  }
	  else{
	    redirect('kontakt', DELETE_FAILED);
	  }
	}
	else{
	  redirect('kontakt/admin', YOU_HAVE_NO_PERMISION);
	}
  }
   
  /***************************************** RENDERERS ***/
  function renderDefault()
  {
    $obj = new AdmKontaktModel;
  	$this->template['title'] = KONTAKT_RND_DEFAULT_TITLE;
	$this->template['messages'] = $obj->findForPage();
	$this->template['frmnames'] = $obj->getFormNames();
	$this->template['paginator'] = $this->createPaginator(1);
  
  }
  
  function renderPage($id)
  {
    $obj = new AdmKontaktModel;
	$this->template['view'] = 'default';
  	$this->template['title'] = KONTAKT_RND_DEFAULT_TITLE;
	$this->template['messages'] = $this->pages[$id];
	$this->template['frmnames'] = $obj->getFormNames();
	$this->template['paginator'] = $this->createPaginator($id);
  
  }
  
  function renderShow($id)
  {
	
	if(Application::$view){
		if(isset($_REQUEST['q'])) $destination = $_REQUEST['q'];
			else $destination = '';
		
		$obj = new AdmKontaktModel;
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
		
		
		$settings = $obj->getFormSettings($id);
		$body = $this->texy->process($row['body']);
		$form = $this->createContactForm($id, $destination);
		if($settings['hide_on_load']){
		  $formlink = Html::elem('div', array('class'=>'cf-show-lnk'), '[ ' . KONTAKT_RND_SHOW_OPEN_FORM . ' &#9660; ]');
		}
		else{
		  $formlink = '';
		}
		if($settings['form_after_text']){
		  $content = $body . $formlink . $form;
		}
		else{
		  $content = $formlink . $form . $body;
		}
		$this->template['title'] = $row['title'];
		$this->template['content'] = $content;
		$this->template['hide'] = $settings['hide_on_load'];
		$this->template['destination'] = $destination;
	}
	else{
	    redirect('error/default/no_content');
	}
  }
  
  function renderAdd()
  {
    $this->template['view'] = 'settings';
	$this->template['title'] = KONTAKT_RND_ADD_TITLE;
	$this->template['content'] = $this->createAddForm();
  }
  
  function renderEdit($id)
  {

	if(Application::$edit){
	  if(isset($_SESSION['destination'])){
		$_SESSION['pathRequest'] = $_SESSION['destination'];
		$_SESSION['destination'] = null;
	  }
	
  	  $this->template['view'] = 'settings';
	  $this->template['title'] = KONTAKT_RND_EDIT_TITLE;
	  $this->template['content'] = $this->createEditForm($id);
	}
	else{
	  redirect('kontakt', YOU_HAVE_NO_PERMISION);
	}
  }
  
  function renderAdmin()
  {
	
	if(Application::$logged['status']){
	$obj = new AdmKontaktModel;
	$rows = $obj->findAll();
  	$this->template['title'] = KONTAKT_RND_ADMIN_TITLE;
	$this->template['content'] = $rows;
	}
	else{
	redirect('error/show/403'); exit;
	}
  }
  
  function renderSettings()
  {
    
  	$this->template['title'] = KONTAKT_RND_SETTINGS_TITLE;
	$this->template['content'] = $this->createSettingsForm();
  
  }
  
  /***************************************** FACTORIES ***/
  private function createSettingsForm()
  {
    $obj = new AdmKontaktModel;
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
	
	$form = new Form('cf-settings', 'frm-cf-settings', BASEPATH.'/kontakt/saveSettings');
	$form->addText('messages_per_page', KONTAKT_FRM_SETTINGS_MESAGES_PER_PAGE_LABEL, $messages_per_page, 2, 2);
	    $form->addDescription('messages_per_page', KONTAKT_FRM_SETTINGS_MESAGES_PER_PAGE_DESC);
	$form->addSelect('order', array('ASC' => KONTAKT_FRM_SETTINGS_ORDER_OLDER.' >> '.KONTAKT_FRM_SETTINGS_ORDER_NEWER, 'DESC'=>KONTAKT_FRM_SETTINGS_ORDER_NEWER.' >> '.KONTAKT_FRM_SETTINGS_ORDER_OLDER), KONTAKT_FRM_SETTINGS_ORDER_LABEL, $order);
		$form->addDescription('order', KONTAKT_FRM_SETTINGS_ORDER_DESC);
	$form->addText('notificate', KONTAKT_FRM_SETTINGS_NOTIFICATE_LABEL, $notificate);
		$form->addDescription('notificate', KONTAKT_FRM_SETTINGS_NOTIFICATE_DESC);
		$form->addRule('notificate', Form::EMAIL, KONTAKT_FRM_SETTINGS_NOTIFICATE_RULE);
	$form->addCheckbox('auto_clean', 1, '', KONTAKT_FRM_SETTINGS_AUTO_CLEAN_LABEL, $auto_clean);
		$form->addDescription('auto_clean', KONTAKT_FRM_SETTINGS_AUTO_CLEAN_DESC);
	$form->addText('delete_older_then', KONTAKT_FRM_SETTINGS_DELETE_OLDER_LABEL, $delete_older_then, 'frm-text', 5, 5);
		$form->addDescription('delete_older_then', KONTAKT_FRM_SETTINGS_DELETE_OLDER_DESC);
		$form->addRule('delete_older_then', Form::MIN, 1,KONTAKT_FRM_SETTINGS_DELETE_OLDER_RULE);
	$form->emptyLine();
	$form->addSubmit('save', SAVE);
	
	return $form->render();
	
  }
  
  private function createAddForm()
  {
    
    $form = new Form('add-form', 'cf-add-form', BASEPATH.'/kontakt/savenew');
	
	if($_POST){
	  foreach($_POST as $key => $val){
	    $$key = $val;
	  }
	  if(!isset($company_fld)) $company_fld = 0;
	  if(!isset($company_fld_req)) $company_fld_req = 0;
	  if(!isset($address_fld)) $address_fld =0;
	  if(!isset($address_fld_req)) $address_fld_req = 0;
	  if(!isset($phone_fld)) $phone_fld = 0;
	  if(!isset($phone_fld_req)) $phone_fld_req = 0;
	  if(!isset($email_fld)) $email_fld = 0;
	  if(!isset($email_fld_req)) $email_fld_req = 0;
	  if(!isset($subject_fld)) $subject_fld = 0;
	  if(!isset($subject_fld_req)) $subject_fld_req = 0;
	  if(!isset($form_after_text)) $form_after_text = 0;
	  if(!isset($hide_on_load)) $hide_on_load = 0;
	}
	else{
	  $values = $form->getZeroValues('contact_forms');
	  foreach($values as $key => $val){
	    $$key = $val;
	  }
	  $values = $form->getZeroValues('contact_form_settings');
	  foreach($values as $key => $val){
		$$key = $val;
	  }
	}
	
	// add language selector
	isset($lang_code) ? $lang_id = $lang_code : $lang_id = 'none';
	$form->insertContent(Application::createLangInput($lang_id));
	
	//form part
	$form->addText('title', TITLE, $title);
	  $form->addRule('title', Form::FILLED, KONTAKT_FRM_TITLE_RULE);
	$form->addText('form_title', KONTAKT_FRM_FORM_TITLE_LABEL, $form_title);
		$form->addDescription('form_title', KONTAKT_FRM_FORM_TITLE_DESC);
	$form->addTextarea('body', BODY, $body);
	  $form->addDescription('body', KONTAKT_FRM_BODY_DESC);
	$form->addText('path_alias', 'URL Alias', $path_alias);
	$form->emptyLine();
	
	//settings part
	$form->addCheckbox('company_fld', 1, KONTAKT_FRM_COMPANY_SHOW, null, $company_fld, 'frm-checkbox', true);
	$form->addCheckbox('company_fld_req', 1, KONTAKT_FRM_COMPANY_REQUIRED, null, $company_fld_req, 'frm-checkbox', false);
	$form->addCheckbox('address_fld', 1, KONTAKT_FRM_ADDRESS_SHOW, null, $address_fld, 'frm-checkbox', true);
	$form->addCheckbox('address_fld_req', 1, KONTAKT_FRM_ADDRESS_REQUIRED,null, $address_fld_req, 'frm-checkbox', false);
	$form->addCheckbox('phone_fld', 1, KONTAKT_FRM_PHONE_SHOW, null,$phone_fld, 'frm-checkbox', true);
	$form->addCheckbox('phone_fld_req', 1, KONTAKT_FRM_PHONE_REQUIRED, null, $phone_fld_req, 'frm-checkbox', false);
	$form->addCheckbox('email_fld', 1, KONTAKT_FRM_EMAIL_SHOW, null,$email_fld, 'frm-checkbox', true);
	$form->addCheckbox('email_fld_req', 1, KONTAKT_FRM_EMAIL_REQUIRED, null, $email_fld_req, 'frm-checkbox', false);
	$form->addCheckbox('subject_fld', 1, KONTAKT_FRM_SUBJECT_SHOW, null, $subject_fld, 'frm-checkbox', true);
	$form->addCheckbox('subject_fld_req', 1, KONTAKT_FRM_SUBJECT_REQUIRED, null,$subject_fld_req, 'frm-checkbox', false);
	$form->addCheckbox('form_after_text', 1, KONTAKT_FRM_FORM_AFTER_TEXT, null,$form_after_text, 'frm-checkbox', false);
	$form->addCheckbox('hide_on_load', 1, KONTAKT_FRM_HIDE_ON_LOAD, null,$hide_on_load, 'frm-checkbox', false);
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
	
	$form->addSubmit('save', SAVE);
	
    return $form->render();
  }
  
  
  
  private function createEditForm($id)
  {
    
    $form = new Form('cf-editform', 'cf-editform', BASEPATH.'/kontakt/save');
	
	if($_POST){
	  foreach($_POST as $key => $val){
	    $$key = $val;
	  }
	  if(!isset($company_fld)) $company_fld = 0;
	  if(!isset($company_fld_req)) $company_fld_req = 0;
	  if(!isset($address_fld)) $address_fld =0;
	  if(!isset($address_fld_req)) $address_fld_req = 0;
	  if(!isset($phone_fld)) $phone_fld = 0;
	  if(!isset($phone_fld_req)) $phone_fld_req = 0;
	  if(!isset($email_fld)) $email_fld = 0;
	  if(!isset($email_fld_req)) $email_fld_req = 0;
	  if(!isset($subject_fld)) $subject_fld = 0;
	  if(!isset($subject_fld_req)) $subject_fld_req = 0;
	  if(!isset($form_after_text)) $form_after_text = 0;
	  if(!isset($hide_on_load)) $hide_on_load = 0;
	}
	else{
	  $obj = new AdmKontaktModel;
	  $values = $obj->find($id);
	  
	  foreach($values as $key => $val){
	    $$key = $val;
	  }
	  $values = $obj->getFormSettings($id);
	  foreach($values as $key => $val){
	    $$key = $val;
	  }
	}
	
	// add language selector
	isset($lang_code) ? $lang_id = $lang_code : $lang_id = $lang;
	$form->insertContent(Application::createLangInput($lang_id));
	
	//form part
	$form->addHidden('id', $id);
	$form->addText('title', TITLE, $title);
	  $form->addRule('title', Form::FILLED, KONTAKT_FRM_TITLE_RULE);
	$form->addText('form_title', KONTAKT_FRM_FORM_TITLE_LABEL, $form_title);
		$form->addDescription('form_title', KONTAKT_FRM_FORM_TITLE_DESC);
	$form->addTextarea('body', BODY, $body);
	  $form->addDescription('body', KONTAKT_FRM_BODY_DESC);
	$form->addText('path_alias', 'URL Alias', $path_alias);
	$form->emptyLine();
	
	//settings part
	$form->addCheckbox('company_fld', 1, KONTAKT_FRM_COMPANY_SHOW, null, $company_fld, 'frm-checkbox', true);
	$form->addCheckbox('company_fld_req', 1, KONTAKT_FRM_COMPANY_REQUIRED, null, $company_fld_req, 'frm-checkbox', false);
	$form->addCheckbox('address_fld', 1, KONTAKT_FRM_ADDRESS_SHOW, null, $address_fld, 'frm-checkbox', true);
	$form->addCheckbox('address_fld_req', 1, KONTAKT_FRM_ADDRESS_REQUIRED,null, $address_fld_req, 'frm-checkbox', false);
	$form->addCheckbox('phone_fld', 1, KONTAKT_FRM_PHONE_SHOW, null,$phone_fld, 'frm-checkbox', true);
	$form->addCheckbox('phone_fld_req', 1, KONTAKT_FRM_PHONE_REQUIRED, null, $phone_fld_req, 'frm-checkbox', false);
	$form->addCheckbox('email_fld', 1, KONTAKT_FRM_EMAIL_SHOW, null,$email_fld, 'frm-checkbox', true);
	$form->addCheckbox('email_fld_req', 1, KONTAKT_FRM_EMAIL_REQUIRED, null, $email_fld_req, 'frm-checkbox', false);
	$form->addCheckbox('subject_fld', 1, KONTAKT_FRM_SUBJECT_SHOW, null, $subject_fld, 'frm-checkbox', true);
	$form->addCheckbox('subject_fld_req', 1, KONTAKT_FRM_SUBJECT_REQUIRED, null,$subject_fld_req, 'frm-checkbox', false);
	$form->addCheckbox('form_after_text', 1, KONTAKT_FRM_FORM_AFTER_TEXT, null,$form_after_text, 'frm-checkbox', false);
	$form->addCheckbox('hide_on_load', 1, KONTAKT_FRM_HIDE_ON_LOAD, null,$hide_on_load, 'frm-checkbox', false);
	$form->emptyLine();
	
	// if user has rights, create fanthom form for menu insert
	if(Application::$logged['role'] == 'admin' or Application::$logged['role'] == 'editor'){
		
		include(APP_DIR.'/admin/models/AdmMenusModel.php');
		
		$menuobj = new AdmMenusModel;
		$menus = $menuobj->findAll();
		// get default value
		$row = $menuobj->findItemByContentId('kontakt',$this->id);
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
	
	$form->addSubmit('save', SAVE_CHANGES);
	
    return $form->render();
  }
  
  private function createContactForm($id, $destination)
  {
    $form = new Form('contact', 'contact-form', BASEPATH.'/kontakt/send');
	
	$cont_frm_id = $id;
	
    $obj = new AdmKontaktModel;
	$settings = $obj->getFormSettings($id);
	$row = $obj->find($id);
	$form_title = $row['form_title'];
	foreach($settings as $key => $val){
	  $$key = $val;
	}
	
	if($_POST){
	  foreach($_POST as $key => $val){
	    $$key = $val;
	  }
	}
	else{
	  $values = $form->getZeroValues('contact_messages');
	  foreach($values as $key => $val){
	    $$key = $val;
	  }
	  $contact_frm_id = $cont_frm_id;
	}

	$form->addText('name', KONTAKT_FRM_NAME_LABEL, $name);
	    $form->addRule('name', Form::FILLED, KONTAKT_FRM_NAME_RULE);
	if($company_fld){
	    $form->addText('company', KONTAKT_FRM_COMPANY_LABEL, $company);
		if($company_fld_req){
			$form->addRule('company', Form::FILLED, KONTAKT_FRM_COMPANY_RULE);
		}
	}
	else{
		$form->addHidden('company', $company);
	}
	if($address_fld){
	    $form->addTextarea('address', KONTAKT_FRM_ADDRESS_LABEL, $address, 'frm-textarea', 30, 3);
		if($address_fld_req){
			$form->addRule('address', Form::FILLED, KONTAKT_FRM_ADDRESS_RULE);
		}
	}
	else{
		$form->addHidden('address', $address);
	}
	if($phone_fld){
	    $form->addText('phone', KONTAKT_FRM_PHONE_LABEL, $phone);
		if($phone_fld_req){
			$form->addRule('phone', Form::FILLED, KONTAKT_FRM_PHONE_RULE);
		}
	}
	else{
		$form->addHidden('phone', $phone);
	}
	if($email_fld){
	    $form->addText('email', 'Email', $email);
			$form->addRule('email', Form::EMAIL, KONTAKT_FRM_EMAIL_RULE_EMAIL);
		if($email_fld_req){
			$form->addRule('email', Form::FILLED, KONTAKT_FRM_EMAIL_RULE_FILLED);
		}
	}
	else{
		$form->addHidden('email', $email);
	}
	if($subject_fld){
	    $form->addText('subject', KONTAKT_FRM_SUBJECT_LABEL, $subject);
		if($subject_fld_req){
			$form->addRule('subject', Form::FILLED, KONTAKT_FRM_SUBJECT_RULE);
		}
	}
	else{
		$form->addHidden('subject', $subject);
	}
	$form->addTextarea('message', KONTAKT_FRM_MESSAGE_LABEL, $message, 'cf-form-messagebox');
		$form->addRule('message', Form::FILLED, KONTAKT_FRM_MESSAGE_RULE);
	$form->addCaptcha('sec_code', KONTAKT_FRM_CAPTCHA_LABEL);
		$form->addRule('sec_code', Form::CAPTCHA, KONTAKT_FRM_CAPTCHA_RULE);
	$form->addHidden('contact_frm_id', $contact_frm_id);
	$form->addHidden('destination', $destination);
	
	$form->emptyLine();
	$form->addSubmit('send', KONTAKT_FRM_SEND_LABEL);
	$frm = $form->render('table');
	
	if($form_title){
	  $header = Html::elem('h3', array('class'=>'cf-form-title title'),$form_title);
	}
	else{
	  $header = '';
	}
	
	$output = Html::elem('div', array('id'=>'cf-form-'.$contact_frm_id, 'class'=>'cf-form-container'), $header.$frm);
	return $output;
	
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
		  
		  $pagesElems .= Html::elem('a', array('href'=>BASEPATH.'/kontakt/page/1', 'class'=>'cf-page-link first'), "&#9668;&nbsp;".FIRST_FEMALE."&nbsp;").'|';
		  
		  for($i = $startFrom; $i <= $countTo; $i++)
		  {

			if($pgNumber == $i){
			  $pagesElems .= Html::elem('span', array('class'=>'cf-page-active'), "&nbsp;$i&nbsp;").'|';
			}
			else{
			  $pagesElems .= Html::elem('a', array('href'=>BASEPATH.'/kontakt/page/'.$i, 'class'=>'cf-page-link'), "&nbsp;$i&nbsp;").'|';
			}
		  
		  }
		  
		  $pagesElems .= Html::elem('a', array('href'=>BASEPATH.'/kontakt/page/'.$lastPage, 'class'=>'cf-page-link last'), "&nbsp;".LAST_FEMALE."&nbsp;&#9658;");
		  
		  return Html::elem('div', array('class'=>'cf-paginator'), PAGE.': '.$pagesElems);
		}
		else{
		  return '';
		}
	
	}
  

  /*************************************** OTHER METHODS ***/
  
  private function runSettings()
  {
    $obj = new AdmKontaktModel;
  
    $rows = $obj->getSettings();
  
    foreach($rows as $row){
      define($row['constant'], $row['value']);
    }
	
	if(AUTO_CLEAN){
	  $clean_date = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m")  , date("d")-DELETE_OLDER_THEN, date("Y")));
	  db::exec("DELETE FROM contact_messages WHERE datetime < '$clean_date'");
	}
  }
  
  private function getPages()
  {
    $obj = new AdmKontaktModel;
	
	$records = $obj->findAllMessages();
	$rec_num = count($records);
	if($rec_num){
	  $pageidx = 1;
	  $idx = 1;
	  $lastidx = (int)MESSAGES_PER_PAGE;
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
}
