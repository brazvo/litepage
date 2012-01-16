<?php
class Administrate extends Controller
{

  // Properties
  
  // Constructor
  public function __construct()
  {
    $this->perm_mach_name = 'texyla';
    if(!Application::$logged['role'] == 'admin') redirect('error/show/403');
    parent::__construct();

  }
  
  
    protected function startUp()
    {
        Application::setPermissions(Application::$logged['role'], 'texyla');
        
        $this->template->bodyclass = 'module_texyla';
        
        if(Application::$action == 'add' and !Application::$add) redirect('error/default/no_permision');
	if(Application::$action == 'edit' and !Application::$edit) redirect('error/default/no_permision');
	if(Application::$action == 'delete' and !Application::$delete) redirect('error/default/no_permision');
         
    }
  
  // Methods
  /***************************************** ACTIONS ***/
  function actionSave()
  {
    if(Form::$isvalid){
		$obj = new TexylaModel;
		$result = $obj->save($_POST);
		if($result) redirect('texyla/administrate', SAVE_OK);
		if(!$result) redirect('texyla/administrate', SAVE_FAILED);
	}
	else{
		Application::$id = $_POST['id'];
		$this->setRender('edit');
	}
  }
  
  function actionSaveNew()
  {
    if(Form::$isvalid){
		$obj = new TexylaModel;
		$result = $obj->saveNew($_POST);
		if($result) redirect('texyla/administrate', SAVE_OK);
		if(!$result) redirect('texyla/administrate', SAVE_FAILED);
	}
	else{
		$this->setRender('add');
	}
  }
  
  function actionDelete($id)
  {
    if(Application::$logged['role'] != 'admin') redirect('error/show/403');
	$obj = new TexylaModel;
	
	$result = $obj->delete($id);
	if($result) redirect('texyla/administrate', DELETED);
	if(!$result) redirect('texyla/administrate', DELETE_FAILED);
  }
  
  /***************************************** RENDERERS ***/ 
  function renderDefault()
  {
	$obj = new TexylaModel;
	
	$textareas = $obj->findAll();
	
  	$this->template->title = TEXYLA_ADMIN_TITLE;
	$this->template->textareas = $textareas;
  
  }
  
  function renderAdd()
  {
        $this->template->setView('edit');
        $this->template->title = TEXYLA_ADD_TITLE;
	$this->template->link = Html::elem('a', array('href'=>Application::link('texyla/administrate')), '[ '.TEXYLA_ADMIN_TITLE.' ]');
	$this->template->content = $this->createAddForm();
  }
  
  function renderEdit($id)
  {
        $this->template->title = TEXYLA_EDIT_TITLE;
	$this->template->link = Html::elem('a', array('href'=>Application::link('texyla/administrate')), '[ '.TEXYLA_ADMIN_TITLE.' ]');
	$this->template->content = $this->createEditForm($id);
  }
  
  /***************************************** FACTORIES ***/
  private function createAddForm()
  {
    $form = new Form('texyla', 'texyla', Application::link('texyla/administrate/save-new'));
	
	if($_POST){
		foreach($_POST as $key => $val){
			$$key = $val;
		}
		if(!isset($admin_allow)) $admin_allow = 0;
		if(!isset($admin_bottomLeftToolbarEdit)) $admin_bottomLeftToolbarEdit = 0;
		if(!isset($admin_bottomLeftToolbarPreview)) $admin_bottomLeftToolbarPreview = 0;
		if(!isset($admin_bottomLeftToolbarHtmlPreview)) $admin_bottomLeftToolbarHtmlPreview = 0;
		if(!isset($admin_tabs)) $admin_tabs = 0;
		if(!isset($admin_headers)) $admin_headers = 0;
		if(!isset($admin_font_style)) $admin_font_style = 0;
		if(!isset($admin_text_align)) $admin_text_align = 0;
		if(!isset($admin_lists)) $admin_lists = 0;
		if(!isset($admin_link)) $admin_link = 0;
		if(!isset($admin_img)) $admin_img = 0;
		if(!isset($admin_table)) $admin_table = 0;
		if(!isset($admin_emoticon)) $admin_emoticon = 0;
		if(!isset($admin_symbol)) $admin_symbol = 0;
		if(!isset($admin_color)) $admin_color = 0;
		if(!isset($admin_textTransform)) $admin_textTransform = 0;
		if(!isset($admin_blocks)) $admin_blocks = 0;
		if(!isset($admin_codes)) $admin_codes = 0;
		if(!isset($admin_others)) $admin_others = 0;
		
		if(!isset($editor_allow)) $editor_allow = 0;
		if(!isset($editor_bottomLeftToolbarEdit)) $editor_bottomLeftToolbarEdit = 0;
		if(!isset($editor_bottomLeftToolbarPreview)) $editor_bottomLeftToolbarPreview = 0;
		if(!isset($editor_bottomLeftToolbarHtmlPreview)) $editor_bottomLeftToolbarHtmlPreview = 0;
		if(!isset($editor_tabs)) $editor_tabs = 0;
		if(!isset($editor_headers)) $editor_headers = 0;
		if(!isset($editor_font_style)) $editor_font_style = 0;
		if(!isset($editor_text_align)) $editor_text_align = 0;
		if(!isset($editor_lists)) $editor_lists = 0;
		if(!isset($editor_link)) $editor_link = 0;
		if(!isset($editor_img)) $editor_img = 0;
		if(!isset($editor_table)) $editor_table = 0;
		if(!isset($editor_emoticon)) $editor_emoticon = 0;
		if(!isset($editor_symbol)) $editor_symbol = 0;
		if(!isset($editor_color)) $editor_color = 0;
		if(!isset($editor_textTransform)) $editor_textTransform = 0;
		if(!isset($editor_blocks)) $editor_blocks = 0;
		if(!isset($editor_codes)) $editor_codes = 0;
		if(!isset($editor_others)) $editor_others = 0;
		
		if(!isset($visitor_allow)) $visitor_allow = 0;
		if(!isset($visitor_bottomLeftToolbarEdit)) $visitor_bottomLeftToolbarEdit = 0;
		if(!isset($visitor_bottomLeftToolbarPreview)) $visitor_bottomLeftToolbarPreview = 0;
		if(!isset($visitor_bottomLeftToolbarHtmlPreview)) $visitor_bottomLeftToolbarHtmlPreview = 0;
		if(!isset($visitor_tabs)) $visitor_tabs = 0;
		if(!isset($visitor_headers)) $visitor_headers = 0;
		if(!isset($visitor_font_style)) $visitor_font_style = 0;
		if(!isset($visitor_text_align)) $visitor_text_align = 0;
		if(!isset($visitor_lists)) $visitor_lists = 0;
		if(!isset($visitor_link)) $visitor_link = 0;
		if(!isset($visitor_img)) $visitor_img = 0;
		if(!isset($visitor_table)) $visitor_table = 0;
		if(!isset($visitor_emoticon)) $visitor_emoticon = 0;
		if(!isset($visitor_symbol)) $visitor_symbol = 0;
		if(!isset($visitor_color)) $visitor_color = 0;
		if(!isset($visitor_textTransform)) $visitor_textTransform = 0;
		if(!isset($visitor_blocks)) $visitor_blocks = 0;
		if(!isset($visitor_codes)) $visitor_codes = 0;
		if(!isset($visitor_others)) $visitor_others = 0;
		
		if(!isset($user_allow)) $user_allow = 0;
		if(!isset($user_bottomLeftToolbarEdit)) $user_bottomLeftToolbarEdit = 0;
		if(!isset($user_bottomLeftToolbarPreview)) $user_bottomLeftToolbarPreview = 0;
		if(!isset($user_bottomLeftToolbarHtmlPreview)) $user_bottomLeftToolbarHtmlPreview = 0;
		if(!isset($user_tabs)) $user_tabs = 0;
		if(!isset($user_headers)) $user_headers = 0;
		if(!isset($user_font_style)) $user_font_style = 0;
		if(!isset($user_text_align)) $user_text_align = 0;
		if(!isset($user_lists)) $user_lists = 0;
		if(!isset($user_link)) $user_link = 0;
		if(!isset($user_img)) $user_img = 0;
		if(!isset($user_table)) $user_table = 0;
		if(!isset($user_emoticon)) $user_emoticon = 0;
		if(!isset($user_symbol)) $user_symbol = 0;
		if(!isset($user_color)) $user_color = 0;
		if(!isset($user_textTransform)) $user_textTransform = 0;
		if(!isset($user_blocks)) $user_blocks = 0;
		if(!isset($user_codes)) $user_codes = 0;
		if(!isset($user_others)) $user_others = 0;
		
	}
	else{
		$values = $form->getZeroValues('texyla');
		foreach($values as $key => $val){
			$$key = $val;
		}
		$admin_allow = 1;
		$admin_texyCfg = 'admin';
		$admin_bottomLeftToolbarEdit = 1;
		$admin_bottomLeftToolbarPreview = 1;
		$admin_bottomLeftToolbarHtmlPreview = 1;
		$admin_buttonType = 'span';
		$admin_tabs = 1;
		$admin_headers = 1;
		$admin_font_style = 1;
		$admin_text_align = 1;
		$admin_lists = 1;
		$admin_link = 1;
		$admin_img = 1;
		$admin_table = 1;
		$admin_emoticon = 1;
		$admin_symbol = 1;
		$admin_color = 1;
		$admin_textTransform = 1;
		$admin_blocks = 1;
		$admin_codes = 1;
		$admin_others = 1;
		
		$editor_allow = 1;
		$editor_texyCfg = 'admin';
		$editor_bottomLeftToolbarEdit = 1;
		$editor_bottomLeftToolbarPreview = 1;
		$editor_bottomLeftToolbarHtmlPreview = 0;
		$editor_buttonType = 'button';
		$editor_tabs = 0;
		$editor_headers = 1;
		$editor_font_style = 1;
		$editor_text_align = 1;
		$editor_lists = 1;
		$editor_link = 1;
		$editor_img = 0;
		$editor_table = 0;
		$editor_emoticon = 0;
		$editor_symbol = 1;
		$editor_color = 0;
		$editor_textTransform = 0;
		$editor_blocks = 0;
		$editor_codes = 0;
		$editor_others = 0;
		
		$user_allow = 1;
		$user_texyCfg = 'admin';
		$user_bottomLeftToolbarEdit = 1;
		$user_bottomLeftToolbarPreview = 1;
		$user_bottomLeftToolbarHtmlPreview = 0;
		$user_buttonType = 'button';
		$user_tabs = 0;
		$user_headers = 0;
		$user_font_style = 1;
		$user_text_align = 0;
		$user_lists = 0;
		$user_link = 0;
		$user_img = 0;
		$user_table = 0;
		$user_emoticon = 0;
		$user_symbol = 1;
		$user_color = 0;
		$user_textTransform = 0;
		$user_blocks = 0;
		$user_codes = 0;
		$user_others = 0;
		
		$visitor_allow = 0;
		$visitor_texyCfg = 'admin';
		$visitor_bottomLeftToolbarEdit = 1;
		$visitor_bottomLeftToolbarPreview = 1;
		$visitor_bottomLeftToolbarHtmlPreview = 0;
		$visitor_buttonType = 'button';
		$visitor_tabs = 0;
		$visitor_headers = 0;
		$visitor_font_style = 1;
		$visitor_text_align = 0;
		$visitor_lists = 0;
		$visitor_link = 0;
		$visitor_img = 0;
		$visitor_table = 0;
		$visitor_emoticon = 0;
		$visitor_symbol = 1;
		$visitor_color = 0;
		$visitor_textTransform = 0;
		$visitor_blocks = 0;
		$visitor_codes = 0;
		$visitor_others = 0;
	}
	
	$form->addText('textarea', TEXYLA_FRM_ADD_TEXYLA_LABEL, $textarea);
		$form->addDescription('textarea', TEXYLA_FRM_ADD_TEXYLA_DESC);
		$form->addRule('textarea', Form::FILLED, TEXYLA_FRM_ADD_TEXYLA_RULE);
	$form->addTextarea('description', TEXYLA_FRM_ADD_DESCRIPTION_LABEL, $description);
	$form->emptyLine();
	
	// Admin Part
	$form->insertContent(TEXYLA_FRM_ADD_ADMIN_SETTINGS_LABEL);
	$form->addCheckbox('admin_allow', 1, TEXYLA_FRM_ADD_ALLOW_LABEL, '', $admin_allow);
	$form->addSelect('admin_texyCfg', array('admin'=>'Admin', 'forum'=>'Forum'), TEXYLA_FRM_ADD_TEXYCFG_LABEL, $admin_texyCfg);
	$form->addSelect('admin_buttonType', array('span'=>'Span', 'button'=>'Tlačidlo'), TEXYLA_FRM_ADD_BUTTONTYPE_LABEL, $admin_buttonType);
	
	$adm_checks[] = array('admin_bottomLeftToolbarEdit', 1, TEXYLA_FRM_ADD_BTLTOOLBAREDIT_LABEL, $admin_bottomLeftToolbarEdit);
	$adm_checks[] = array('admin_bottomLeftToolbarPreview', 1, TEXYLA_FRM_ADD_BTLTOOLBARPREVIEW_LABEL, $admin_bottomLeftToolbarPreview);
	$adm_checks[] = array('admin_bottomLeftToolbarHtmlPreview', 1, TEXYLA_FRM_ADD_BTLTOOLBARHTMLPREVIEW_LABEL, $admin_bottomLeftToolbarHtmlPreview);
	$adm_checks[] = array('admin_tabs', 1, TEXYLA_FRM_ADD_TABS_LABEL, $admin_tabs);
	$adm_checks[] = array('admin_headers', 1, TEXYLA_FRM_ADD_HEADERS_LABEL, $admin_headers);
	$adm_checks[] = array('admin_font_style', 1, TEXYLA_FRM_ADD_FONT_STYLE_LABEL, $admin_font_style);
	$adm_checks[] = array('admin_text_align', 1, TEXYLA_FRM_ADD_TEXT_ALIGN_LABEL, $admin_text_align);
	$adm_checks[] = array('admin_lists', 1, TEXYLA_FRM_ADD_LISTS_LABEL, $admin_lists);
	$adm_checks[] = array('admin_link', 1, TEXYLA_FRM_ADD_LINK_LABEL, $admin_link);
	$adm_checks[] = array('admin_img', 1, TEXYLA_FRM_ADD_IMG_LABEL, $admin_img);
	$adm_checks[] = array('admin_table', 1, TEXYLA_FRM_ADD_TABLE_LABEL, $admin_table);
	$adm_checks[] = array('admin_emoticon', 1, TEXYLA_FRM_ADD_EMOTICON_LABEL, $admin_emoticon);
	$adm_checks[] = array('admin_symbol', 1, TEXYLA_FRM_ADD_SYMBOL_LABEL, $admin_symbol);
	$adm_checks[] = array('admin_color', 1, TEXYLA_FRM_ADD_COLOR_LABEL, $admin_color);
	$adm_checks[] = array('admin_textTransform', 1, TEXYLA_FRM_ADD_TEXTTRANSFORM_LABEL, $admin_textTransform);
	$adm_checks[] = array('admin_blocks', 1, TEXYLA_FRM_ADD_BLOCKS_LABEL, $admin_blocks);
	$adm_checks[] = array('admin_codes', 1, TEXYLA_FRM_ADD_CODES_LABEL, $admin_codes);
	$adm_checks[] = array('admin_others', 1, TEXYLA_FRM_ADD_OTHERS_LABEL, $admin_others);
	
	$form->addCheckboxgroup('adm_checks', $adm_checks, TEXYLA_FRM_ADD_ADMIN_CHECKS_LABEL);
	$form->emptyLine();
	
	// Editor Part
	$form->insertContent(TEXYLA_FRM_ADD_EDITOR_SETTINGS_LABEL);
	$form->addCheckbox('editor_allow', 1, TEXYLA_FRM_ADD_ALLOW_LABEL, '', $editor_allow);
	$form->addSelect('editor_texyCfg', array('admin'=>'Admin', 'forum'=>'Forum'), TEXYLA_FRM_ADD_TEXYCFG_LABEL, $editor_texyCfg);
	$form->addSelect('editor_buttonType', array('span'=>'Span', 'button'=>'Tlačidlo'), TEXYLA_FRM_ADD_BUTTONTYPE_LABEL, $editor_buttonType);
	
	$editor_checks[] = array('editor_bottomLeftToolbarEdit', 1, TEXYLA_FRM_ADD_BTLTOOLBAREDIT_LABEL, $editor_bottomLeftToolbarEdit);
	$editor_checks[] = array('editor_bottomLeftToolbarPreview', 1, TEXYLA_FRM_ADD_BTLTOOLBARPREVIEW_LABEL, $editor_bottomLeftToolbarPreview);
	$editor_checks[] = array('editor_bottomLeftToolbarHtmlPreview', 1, TEXYLA_FRM_ADD_BTLTOOLBARHTMLPREVIEW_LABEL, $editor_bottomLeftToolbarHtmlPreview);
	$editor_checks[] = array('editor_tabs', 1, TEXYLA_FRM_ADD_TABS_LABEL, $editor_tabs);
	$editor_checks[] = array('editor_headers', 1, TEXYLA_FRM_ADD_HEADERS_LABEL, $editor_headers);
	$editor_checks[] = array('editor_font_style', 1, TEXYLA_FRM_ADD_FONT_STYLE_LABEL, $editor_font_style);
	$editor_checks[] = array('editor_text_align', 1, TEXYLA_FRM_ADD_TEXT_ALIGN_LABEL, $editor_text_align);
	$editor_checks[] = array('editor_lists', 1, TEXYLA_FRM_ADD_LISTS_LABEL, $editor_lists);
	$editor_checks[] = array('editor_link', 1, TEXYLA_FRM_ADD_LINK_LABEL, $editor_link);
	$editor_checks[] = array('editor_img', 1, TEXYLA_FRM_ADD_IMG_LABEL, $editor_img);
	$editor_checks[] = array('editor_table', 1, TEXYLA_FRM_ADD_TABLE_LABEL, $editor_table);
	$editor_checks[] = array('editor_emoticon', 1, TEXYLA_FRM_ADD_EMOTICON_LABEL, $editor_emoticon);
	$editor_checks[] = array('editor_symbol', 1, TEXYLA_FRM_ADD_SYMBOL_LABEL, $editor_symbol);
	$editor_checks[] = array('editor_color', 1, TEXYLA_FRM_ADD_COLOR_LABEL, $editor_color);
	$editor_checks[] = array('editor_textTransform', 1, TEXYLA_FRM_ADD_TEXTTRANSFORM_LABEL, $editor_textTransform);
	$editor_checks[] = array('editor_blocks', 1, TEXYLA_FRM_ADD_BLOCKS_LABEL, $editor_blocks);
	$editor_checks[] = array('editor_codes', 1, TEXYLA_FRM_ADD_CODES_LABEL, $editor_codes);
	$editor_checks[] = array('editor_others', 1, TEXYLA_FRM_ADD_OTHERS_LABEL, $editor_others);
	
	$form->addCheckboxgroup('editor_checks', $editor_checks, TEXYLA_FRM_ADD_EDITOR_CHECKS_LABEL);
	$form->emptyLine();
	
	// User Part
	$form->insertContent(TEXYLA_FRM_ADD_USER_SETTINGS_LABEL);
	$form->addCheckbox('user_allow', 1, TEXYLA_FRM_ADD_ALLOW_LABEL, '', $user_allow);
	$form->addSelect('user_texyCfg', array('admin'=>'Admin', 'forum'=>'Forum'), TEXYLA_FRM_ADD_TEXYCFG_LABEL, $user_texyCfg);
	$form->addSelect('user_buttonType', array('span'=>'Span', 'button'=>'Tlačidlo'), TEXYLA_FRM_ADD_BUTTONTYPE_LABEL, $user_buttonType);
	
	$user_checks[] = array('user_bottomLeftToolbarEdit', 1, TEXYLA_FRM_ADD_BTLTOOLBAREDIT_LABEL, $user_bottomLeftToolbarEdit);
	$user_checks[] = array('user_bottomLeftToolbarPreview', 1, TEXYLA_FRM_ADD_BTLTOOLBARPREVIEW_LABEL, $user_bottomLeftToolbarPreview);
	$user_checks[] = array('user_bottomLeftToolbarHtmlPreview', 1, TEXYLA_FRM_ADD_BTLTOOLBARHTMLPREVIEW_LABEL, $user_bottomLeftToolbarHtmlPreview);
	$user_checks[] = array('user_tabs', 1, TEXYLA_FRM_ADD_TABS_LABEL, $user_tabs);
	$user_checks[] = array('user_headers', 1, TEXYLA_FRM_ADD_HEADERS_LABEL, $user_headers);
	$user_checks[] = array('user_font_style', 1, TEXYLA_FRM_ADD_FONT_STYLE_LABEL, $user_font_style);
	$user_checks[] = array('user_text_align', 1, TEXYLA_FRM_ADD_TEXT_ALIGN_LABEL, $user_text_align);
	$user_checks[] = array('user_lists', 1, TEXYLA_FRM_ADD_LISTS_LABEL, $user_lists);
	$user_checks[] = array('user_link', 1, TEXYLA_FRM_ADD_LINK_LABEL, $user_link);
	$user_checks[] = array('user_img', 1, TEXYLA_FRM_ADD_IMG_LABEL, $user_img);
	$user_checks[] = array('user_table', 1, TEXYLA_FRM_ADD_TABLE_LABEL, $user_table);
	$user_checks[] = array('user_emoticon', 1, TEXYLA_FRM_ADD_EMOTICON_LABEL, $user_emoticon);
	$user_checks[] = array('user_symbol', 1, TEXYLA_FRM_ADD_SYMBOL_LABEL, $user_symbol);
	$user_checks[] = array('user_color', 1, TEXYLA_FRM_ADD_COLOR_LABEL, $user_color);
	$user_checks[] = array('user_textTransform', 1, TEXYLA_FRM_ADD_TEXTTRANSFORM_LABEL, $user_textTransform);
	$user_checks[] = array('user_blocks', 1, TEXYLA_FRM_ADD_BLOCKS_LABEL, $user_blocks);
	$user_checks[] = array('user_codes', 1, TEXYLA_FRM_ADD_CODES_LABEL, $user_codes);
	$user_checks[] = array('user_others', 1, TEXYLA_FRM_ADD_OTHERS_LABEL, $user_others);
	
	$form->addCheckboxgroup('user_checks', $user_checks, TEXYLA_FRM_ADD_USER_CHECKS_LABEL);
	$form->emptyLine();
	
	// Visitor Part
	$form->insertContent(TEXYLA_FRM_ADD_VISITOR_SETTINGS_LABEL);
	$form->addCheckbox('visitor_allow', 1, TEXYLA_FRM_ADD_ALLOW_LABEL, '', $visitor_allow);
	$form->addSelect('visitor_texyCfg', array('admin'=>'Admin', 'forum'=>'Forum'), TEXYLA_FRM_ADD_TEXYCFG_LABEL, $visitor_texyCfg);
	$form->addSelect('visitor_buttonType', array('span'=>'Span', 'button'=>'Tlačidlo'), TEXYLA_FRM_ADD_BUTTONTYPE_LABEL, $visitor_buttonType);
	
	$visitor_checks[] = array('visitor_bottomLeftToolbarEdit', 1, TEXYLA_FRM_ADD_BTLTOOLBAREDIT_LABEL, $visitor_bottomLeftToolbarEdit);
	$visitor_checks[] = array('visitor_bottomLeftToolbarPreview', 1, TEXYLA_FRM_ADD_BTLTOOLBARPREVIEW_LABEL, $visitor_bottomLeftToolbarPreview);
	$visitor_checks[] = array('visitor_bottomLeftToolbarHtmlPreview', 1, TEXYLA_FRM_ADD_BTLTOOLBARHTMLPREVIEW_LABEL, $visitor_bottomLeftToolbarHtmlPreview);
	$visitor_checks[] = array('visitor_tabs', 1, TEXYLA_FRM_ADD_TABS_LABEL, $visitor_tabs);
	$visitor_checks[] = array('visitor_headers', 1, TEXYLA_FRM_ADD_HEADERS_LABEL, $visitor_headers);
	$visitor_checks[] = array('visitor_font_style', 1, TEXYLA_FRM_ADD_FONT_STYLE_LABEL, $visitor_font_style);
	$visitor_checks[] = array('visitor_text_align', 1, TEXYLA_FRM_ADD_TEXT_ALIGN_LABEL, $visitor_text_align);
	$visitor_checks[] = array('visitor_lists', 1, TEXYLA_FRM_ADD_LISTS_LABEL, $visitor_lists);
	$visitor_checks[] = array('visitor_link', 1, TEXYLA_FRM_ADD_LINK_LABEL, $visitor_link);
	$visitor_checks[] = array('visitor_img', 1, TEXYLA_FRM_ADD_IMG_LABEL, $visitor_img);
	$visitor_checks[] = array('visitor_table', 1, TEXYLA_FRM_ADD_TABLE_LABEL, $visitor_table);
	$visitor_checks[] = array('visitor_emoticon', 1, TEXYLA_FRM_ADD_EMOTICON_LABEL, $visitor_emoticon);
	$visitor_checks[] = array('visitor_symbol', 1, TEXYLA_FRM_ADD_SYMBOL_LABEL, $visitor_symbol);
	$visitor_checks[] = array('visitor_color', 1, TEXYLA_FRM_ADD_COLOR_LABEL, $visitor_color);
	$visitor_checks[] = array('visitor_textTransform', 1, TEXYLA_FRM_ADD_TEXTTRANSFORM_LABEL, $visitor_textTransform);
	$visitor_checks[] = array('visitor_blocks', 1, TEXYLA_FRM_ADD_BLOCKS_LABEL, $visitor_blocks);
	$visitor_checks[] = array('visitor_codes', 1, TEXYLA_FRM_ADD_CODES_LABEL, $visitor_codes);
	$visitor_checks[] = array('visitor_others', 1, TEXYLA_FRM_ADD_OTHERS_LABEL, $visitor_others);
	
	$form->addCheckboxgroup('visitor_checks', $visitor_checks, TEXYLA_FRM_ADD_VISITOR_CHECKS_LABEL);
	$form->emptyLine();
	
	$form->addSubmit('save', SAVE);
	
	return $form->render();
  }
  
  
  
  private function createEditForm($id)
  {
    $form = new Form('texyla', 'texyla', Application::link('texyla/administrate/save'));
	
	if($_POST){
		foreach($_POST as $key => $val){
			$$key = $val;
		}
		if(!isset($admin_allow)) $admin_allow = 0;
		if(!isset($admin_bottomLeftToolbarEdit)) $admin_bottomLeftToolbarEdit = 0;
		if(!isset($admin_bottomLeftToolbarPreview)) $admin_bottomLeftToolbarPreview = 0;
		if(!isset($admin_bottomLeftToolbarHtmlPreview)) $admin_bottomLeftToolbarHtmlPreview = 0;
		if(!isset($admin_tabs)) $admin_tabs = 0;
		if(!isset($admin_headers)) $admin_headers = 0;
		if(!isset($admin_font_style)) $admin_font_style = 0;
		if(!isset($admin_text_align)) $admin_text_align = 0;
		if(!isset($admin_lists)) $admin_lists = 0;
		if(!isset($admin_link)) $admin_link = 0;
		if(!isset($admin_img)) $admin_img = 0;
		if(!isset($admin_table)) $admin_table = 0;
		if(!isset($admin_emoticon)) $admin_emoticon = 0;
		if(!isset($admin_symbol)) $admin_symbol = 0;
		if(!isset($admin_color)) $admin_color = 0;
		if(!isset($admin_textTransform)) $admin_textTransform = 0;
		if(!isset($admin_blocks)) $admin_blocks = 0;
		if(!isset($admin_codes)) $admin_codes = 0;
		if(!isset($admin_others)) $admin_others = 0;
		
		if(!isset($editor_allow)) $editor_allow = 0;
		if(!isset($editor_bottomLeftToolbarEdit)) $editor_bottomLeftToolbarEdit = 0;
		if(!isset($editor_bottomLeftToolbarPreview)) $editor_bottomLeftToolbarPreview = 0;
		if(!isset($editor_bottomLeftToolbarHtmlPreview)) $editor_bottomLeftToolbarHtmlPreview = 0;
		if(!isset($editor_tabs)) $editor_tabs = 0;
		if(!isset($editor_headers)) $editor_headers = 0;
		if(!isset($editor_font_style)) $editor_font_style = 0;
		if(!isset($editor_text_align)) $editor_text_align = 0;
		if(!isset($editor_lists)) $editor_lists = 0;
		if(!isset($editor_link)) $editor_link = 0;
		if(!isset($editor_img)) $editor_img = 0;
		if(!isset($editor_table)) $editor_table = 0;
		if(!isset($editor_emoticon)) $editor_emoticon = 0;
		if(!isset($editor_symbol)) $editor_symbol = 0;
		if(!isset($editor_color)) $editor_color = 0;
		if(!isset($editor_textTransform)) $editor_textTransform = 0;
		if(!isset($editor_blocks)) $editor_blocks = 0;
		if(!isset($editor_codes)) $editor_codes = 0;
		if(!isset($editor_others)) $editor_others = 0;
		
		if(!isset($visitor_allow)) $visitor_allow = 0;
		if(!isset($visitor_bottomLeftToolbarEdit)) $visitor_bottomLeftToolbarEdit = 0;
		if(!isset($visitor_bottomLeftToolbarPreview)) $visitor_bottomLeftToolbarPreview = 0;
		if(!isset($visitor_bottomLeftToolbarHtmlPreview)) $visitor_bottomLeftToolbarHtmlPreview = 0;
		if(!isset($visitor_tabs)) $visitor_tabs = 0;
		if(!isset($visitor_headers)) $visitor_headers = 0;
		if(!isset($visitor_font_style)) $visitor_font_style = 0;
		if(!isset($visitor_text_align)) $visitor_text_align = 0;
		if(!isset($visitor_lists)) $visitor_lists = 0;
		if(!isset($visitor_link)) $visitor_link = 0;
		if(!isset($visitor_img)) $visitor_img = 0;
		if(!isset($visitor_table)) $visitor_table = 0;
		if(!isset($visitor_emoticon)) $visitor_emoticon = 0;
		if(!isset($visitor_symbol)) $visitor_symbol = 0;
		if(!isset($visitor_color)) $visitor_color = 0;
		if(!isset($visitor_textTransform)) $visitor_textTransform = 0;
		if(!isset($visitor_blocks)) $visitor_blocks = 0;
		if(!isset($visitor_codes)) $visitor_codes = 0;
		if(!isset($visitor_others)) $visitor_others = 0;
		
		if(!isset($user_allow)) $user_allow = 0;
		if(!isset($user_bottomLeftToolbarEdit)) $user_bottomLeftToolbarEdit = 0;
		if(!isset($user_bottomLeftToolbarPreview)) $user_bottomLeftToolbarPreview = 0;
		if(!isset($user_bottomLeftToolbarHtmlPreview)) $user_bottomLeftToolbarHtmlPreview = 0;
		if(!isset($user_tabs)) $user_tabs = 0;
		if(!isset($user_headers)) $user_headers = 0;
		if(!isset($user_font_style)) $user_font_style = 0;
		if(!isset($user_text_align)) $user_text_align = 0;
		if(!isset($user_lists)) $user_lists = 0;
		if(!isset($user_link)) $user_link = 0;
		if(!isset($user_img)) $user_img = 0;
		if(!isset($user_table)) $user_table = 0;
		if(!isset($user_emoticon)) $user_emoticon = 0;
		if(!isset($user_symbol)) $user_symbol = 0;
		if(!isset($user_color)) $user_color = 0;
		if(!isset($user_textTransform)) $user_textTransform = 0;
		if(!isset($user_blocks)) $user_blocks = 0;
		if(!isset($user_codes)) $user_codes = 0;
		if(!isset($user_others)) $user_others = 0;
		
	}
	else{
	    $obj = new TexylaModel;
		$values = $obj->getValues($id);
		foreach($values as $key => $val){
			$$key = $val;
		}
	}
	
	$form->addHidden('id', $id);
	$form->addText('textarea', TEXYLA_FRM_ADD_TEXYLA_LABEL, $textarea);
		$form->addDescription('textarea', TEXYLA_FRM_ADD_TEXYLA_DESC);
		$form->addRule('textarea', Form::FILLED, TEXYLA_FRM_ADD_TEXYLA_RULE);
	$form->addTextarea('description', TEXYLA_FRM_ADD_DESCRIPTION_LABEL, $description);
	$form->emptyLine();
	
	// Admin Part
	$form->insertContent(TEXYLA_FRM_ADD_ADMIN_SETTINGS_LABEL);
	$form->addCheckbox('admin_allow', 1, TEXYLA_FRM_ADD_ALLOW_LABEL, '', $admin_allow);
	$form->addSelect('admin_texyCfg', array('admin'=>'Admin', 'forum'=>'Forum'), TEXYLA_FRM_ADD_TEXYCFG_LABEL, $admin_texyCfg);
	$form->addSelect('admin_buttonType', array('span'=>'Span', 'button'=>'Tlačidlo'), TEXYLA_FRM_ADD_BUTTONTYPE_LABEL, $admin_buttonType);
	
	$adm_checks[] = array('admin_bottomLeftToolbarEdit', 1, TEXYLA_FRM_ADD_BTLTOOLBAREDIT_LABEL, $admin_bottomLeftToolbarEdit);
	$adm_checks[] = array('admin_bottomLeftToolbarPreview', 1, TEXYLA_FRM_ADD_BTLTOOLBARPREVIEW_LABEL, $admin_bottomLeftToolbarPreview);
	$adm_checks[] = array('admin_bottomLeftToolbarHtmlPreview', 1, TEXYLA_FRM_ADD_BTLTOOLBARHTMLPREVIEW_LABEL, $admin_bottomLeftToolbarHtmlPreview);
	$adm_checks[] = array('admin_tabs', 1, TEXYLA_FRM_ADD_TABS_LABEL, $admin_tabs);
	$adm_checks[] = array('admin_headers', 1, TEXYLA_FRM_ADD_HEADERS_LABEL, $admin_headers);
	$adm_checks[] = array('admin_font_style', 1, TEXYLA_FRM_ADD_FONT_STYLE_LABEL, $admin_font_style);
	$adm_checks[] = array('admin_text_align', 1, TEXYLA_FRM_ADD_TEXT_ALIGN_LABEL, $admin_text_align);
	$adm_checks[] = array('admin_lists', 1, TEXYLA_FRM_ADD_LISTS_LABEL, $admin_lists);
	$adm_checks[] = array('admin_link', 1, TEXYLA_FRM_ADD_LINK_LABEL, $admin_link);
	$adm_checks[] = array('admin_img', 1, TEXYLA_FRM_ADD_IMG_LABEL, $admin_img);
	$adm_checks[] = array('admin_table', 1, TEXYLA_FRM_ADD_TABLE_LABEL, $admin_table);
	$adm_checks[] = array('admin_emoticon', 1, TEXYLA_FRM_ADD_EMOTICON_LABEL, $admin_emoticon);
	$adm_checks[] = array('admin_symbol', 1, TEXYLA_FRM_ADD_SYMBOL_LABEL, $admin_symbol);
	$adm_checks[] = array('admin_color', 1, TEXYLA_FRM_ADD_COLOR_LABEL, $admin_color);
	$adm_checks[] = array('admin_textTransform', 1, TEXYLA_FRM_ADD_TEXTTRANSFORM_LABEL, $admin_textTransform);
	$adm_checks[] = array('admin_blocks', 1, TEXYLA_FRM_ADD_BLOCKS_LABEL, $admin_blocks);
	$adm_checks[] = array('admin_codes', 1, TEXYLA_FRM_ADD_CODES_LABEL, $admin_codes);
	$adm_checks[] = array('admin_others', 1, TEXYLA_FRM_ADD_OTHERS_LABEL, $admin_others);
	
	$form->addCheckboxgroup('adm_checks', $adm_checks, TEXYLA_FRM_ADD_ADMIN_CHECKS_LABEL);
	$form->emptyLine();
	
	// Editor Part
	$form->insertContent(TEXYLA_FRM_ADD_EDITOR_SETTINGS_LABEL);
	$form->addCheckbox('editor_allow', 1, TEXYLA_FRM_ADD_ALLOW_LABEL, '', $editor_allow);
	$form->addSelect('editor_texyCfg', array('admin'=>'Admin', 'forum'=>'Forum'), TEXYLA_FRM_ADD_TEXYCFG_LABEL, $editor_texyCfg);
	$form->addSelect('editor_buttonType', array('span'=>'Span', 'button'=>'Tlačidlo'), TEXYLA_FRM_ADD_BUTTONTYPE_LABEL, $editor_buttonType);
	
	$editor_checks[] = array('editor_bottomLeftToolbarEdit', 1, TEXYLA_FRM_ADD_BTLTOOLBAREDIT_LABEL, $editor_bottomLeftToolbarEdit);
	$editor_checks[] = array('editor_bottomLeftToolbarPreview', 1, TEXYLA_FRM_ADD_BTLTOOLBARPREVIEW_LABEL, $editor_bottomLeftToolbarPreview);
	$editor_checks[] = array('editor_bottomLeftToolbarHtmlPreview', 1, TEXYLA_FRM_ADD_BTLTOOLBARHTMLPREVIEW_LABEL, $editor_bottomLeftToolbarHtmlPreview);
	$editor_checks[] = array('editor_tabs', 1, TEXYLA_FRM_ADD_TABS_LABEL, $editor_tabs);
	$editor_checks[] = array('editor_headers', 1, TEXYLA_FRM_ADD_HEADERS_LABEL, $editor_headers);
	$editor_checks[] = array('editor_font_style', 1, TEXYLA_FRM_ADD_FONT_STYLE_LABEL, $editor_font_style);
	$editor_checks[] = array('editor_text_align', 1, TEXYLA_FRM_ADD_TEXT_ALIGN_LABEL, $editor_text_align);
	$editor_checks[] = array('editor_lists', 1, TEXYLA_FRM_ADD_LISTS_LABEL, $editor_lists);
	$editor_checks[] = array('editor_link', 1, TEXYLA_FRM_ADD_LINK_LABEL, $editor_link);
	$editor_checks[] = array('editor_img', 1, TEXYLA_FRM_ADD_IMG_LABEL, $editor_img);
	$editor_checks[] = array('editor_table', 1, TEXYLA_FRM_ADD_TABLE_LABEL, $editor_table);
	$editor_checks[] = array('editor_emoticon', 1, TEXYLA_FRM_ADD_EMOTICON_LABEL, $editor_emoticon);
	$editor_checks[] = array('editor_symbol', 1, TEXYLA_FRM_ADD_SYMBOL_LABEL, $editor_symbol);
	$editor_checks[] = array('editor_color', 1, TEXYLA_FRM_ADD_COLOR_LABEL, $editor_color);
	$editor_checks[] = array('editor_textTransform', 1, TEXYLA_FRM_ADD_TEXTTRANSFORM_LABEL, $editor_textTransform);
	$editor_checks[] = array('editor_blocks', 1, TEXYLA_FRM_ADD_BLOCKS_LABEL, $editor_blocks);
	$editor_checks[] = array('editor_codes', 1, TEXYLA_FRM_ADD_CODES_LABEL, $editor_codes);
	$editor_checks[] = array('editor_others', 1, TEXYLA_FRM_ADD_OTHERS_LABEL, $editor_others);
	
	$form->addCheckboxgroup('editor_checks', $editor_checks, TEXYLA_FRM_ADD_EDITOR_CHECKS_LABEL);
	$form->emptyLine();
	
	// User Part
	$form->insertContent(TEXYLA_FRM_ADD_USER_SETTINGS_LABEL);
	$form->addCheckbox('user_allow', 1, TEXYLA_FRM_ADD_ALLOW_LABEL, '', $user_allow);
	$form->addSelect('user_texyCfg', array('admin'=>'Admin', 'forum'=>'Forum'), TEXYLA_FRM_ADD_TEXYCFG_LABEL, $user_texyCfg);
	$form->addSelect('user_buttonType', array('span'=>'Span', 'button'=>'Tlačidlo'), TEXYLA_FRM_ADD_BUTTONTYPE_LABEL, $user_buttonType);
	
	$user_checks[] = array('user_bottomLeftToolbarEdit', 1, TEXYLA_FRM_ADD_BTLTOOLBAREDIT_LABEL, $user_bottomLeftToolbarEdit);
	$user_checks[] = array('user_bottomLeftToolbarPreview', 1, TEXYLA_FRM_ADD_BTLTOOLBARPREVIEW_LABEL, $user_bottomLeftToolbarPreview);
	$user_checks[] = array('user_bottomLeftToolbarHtmlPreview', 1, TEXYLA_FRM_ADD_BTLTOOLBARHTMLPREVIEW_LABEL, $user_bottomLeftToolbarHtmlPreview);
	$user_checks[] = array('user_tabs', 1, TEXYLA_FRM_ADD_TABS_LABEL, $user_tabs);
	$user_checks[] = array('user_headers', 1, TEXYLA_FRM_ADD_HEADERS_LABEL, $user_headers);
	$user_checks[] = array('user_font_style', 1, TEXYLA_FRM_ADD_FONT_STYLE_LABEL, $user_font_style);
	$user_checks[] = array('user_text_align', 1, TEXYLA_FRM_ADD_TEXT_ALIGN_LABEL, $user_text_align);
	$user_checks[] = array('user_lists', 1, TEXYLA_FRM_ADD_LISTS_LABEL, $user_lists);
	$user_checks[] = array('user_link', 1, TEXYLA_FRM_ADD_LINK_LABEL, $user_link);
	$user_checks[] = array('user_img', 1, TEXYLA_FRM_ADD_IMG_LABEL, $user_img);
	$user_checks[] = array('user_table', 1, TEXYLA_FRM_ADD_TABLE_LABEL, $user_table);
	$user_checks[] = array('user_emoticon', 1, TEXYLA_FRM_ADD_EMOTICON_LABEL, $user_emoticon);
	$user_checks[] = array('user_symbol', 1, TEXYLA_FRM_ADD_SYMBOL_LABEL, $user_symbol);
	$user_checks[] = array('user_color', 1, TEXYLA_FRM_ADD_COLOR_LABEL, $user_color);
	$user_checks[] = array('user_textTransform', 1, TEXYLA_FRM_ADD_TEXTTRANSFORM_LABEL, $user_textTransform);
	$user_checks[] = array('user_blocks', 1, TEXYLA_FRM_ADD_BLOCKS_LABEL, $user_blocks);
	$user_checks[] = array('user_codes', 1, TEXYLA_FRM_ADD_CODES_LABEL, $user_codes);
	$user_checks[] = array('user_others', 1, TEXYLA_FRM_ADD_OTHERS_LABEL, $user_others);
	
	$form->addCheckboxgroup('user_checks', $user_checks, TEXYLA_FRM_ADD_USER_CHECKS_LABEL);
	$form->emptyLine();
	
	// Visitor Part
	$form->insertContent(TEXYLA_FRM_ADD_VISITOR_SETTINGS_LABEL);
	$form->addCheckbox('visitor_allow', 1, TEXYLA_FRM_ADD_ALLOW_LABEL, '', $visitor_allow);
	$form->addSelect('visitor_texyCfg', array('admin'=>'Admin', 'forum'=>'Forum'), TEXYLA_FRM_ADD_TEXYCFG_LABEL, $visitor_texyCfg);
	$form->addSelect('visitor_buttonType', array('span'=>'Span', 'button'=>'Tlačidlo'), TEXYLA_FRM_ADD_BUTTONTYPE_LABEL, $visitor_buttonType);
	
	$visitor_checks[] = array('visitor_bottomLeftToolbarEdit', 1, TEXYLA_FRM_ADD_BTLTOOLBAREDIT_LABEL, $visitor_bottomLeftToolbarEdit);
	$visitor_checks[] = array('visitor_bottomLeftToolbarPreview', 1, TEXYLA_FRM_ADD_BTLTOOLBARPREVIEW_LABEL, $visitor_bottomLeftToolbarPreview);
	$visitor_checks[] = array('visitor_bottomLeftToolbarHtmlPreview', 1, TEXYLA_FRM_ADD_BTLTOOLBARHTMLPREVIEW_LABEL, $visitor_bottomLeftToolbarHtmlPreview);
	$visitor_checks[] = array('visitor_tabs', 1, TEXYLA_FRM_ADD_TABS_LABEL, $visitor_tabs);
	$visitor_checks[] = array('visitor_headers', 1, TEXYLA_FRM_ADD_HEADERS_LABEL, $visitor_headers);
	$visitor_checks[] = array('visitor_font_style', 1, TEXYLA_FRM_ADD_FONT_STYLE_LABEL, $visitor_font_style);
	$visitor_checks[] = array('visitor_text_align', 1, TEXYLA_FRM_ADD_TEXT_ALIGN_LABEL, $visitor_text_align);
	$visitor_checks[] = array('visitor_lists', 1, TEXYLA_FRM_ADD_LISTS_LABEL, $visitor_lists);
	$visitor_checks[] = array('visitor_link', 1, TEXYLA_FRM_ADD_LINK_LABEL, $visitor_link);
	$visitor_checks[] = array('visitor_img', 1, TEXYLA_FRM_ADD_IMG_LABEL, $visitor_img);
	$visitor_checks[] = array('visitor_table', 1, TEXYLA_FRM_ADD_TABLE_LABEL, $visitor_table);
	$visitor_checks[] = array('visitor_emoticon', 1, TEXYLA_FRM_ADD_EMOTICON_LABEL, $visitor_emoticon);
	$visitor_checks[] = array('visitor_symbol', 1, TEXYLA_FRM_ADD_SYMBOL_LABEL, $visitor_symbol);
	$visitor_checks[] = array('visitor_color', 1, TEXYLA_FRM_ADD_COLOR_LABEL, $visitor_color);
	$visitor_checks[] = array('visitor_textTransform', 1, TEXYLA_FRM_ADD_TEXTTRANSFORM_LABEL, $visitor_textTransform);
	$visitor_checks[] = array('visitor_blocks', 1, TEXYLA_FRM_ADD_BLOCKS_LABEL, $visitor_blocks);
	$visitor_checks[] = array('visitor_codes', 1, TEXYLA_FRM_ADD_CODES_LABEL, $visitor_codes);
	$visitor_checks[] = array('visitor_others', 1, TEXYLA_FRM_ADD_OTHERS_LABEL, $visitor_others);
	
	$form->addCheckboxgroup('visitor_checks', $visitor_checks, TEXYLA_FRM_ADD_VISITOR_CHECKS_LABEL);
	$form->emptyLine();
	
	$form->addSubmit('save', SAVE);
	
	return $form->render();
  }

}