<?php

class Administrate extends CategoriesBaseController
{

  // Properties
  
  // Constructor
  public function __construct()
  {
    $this->perm_mach_name = 'categories';
    parent::__construct();

  }
  
  // Methods
  /***************************************** ACTIONS ***/
  function actionSave()
  {
    if(!Application::$edit) redirect('error/show/1');
	
	if(Form::$isvalid){
		$obj = new AdmCategoriesModel;
		$result = $obj->save($_POST);
		if($result){
			redirect('categories', SAVE_OK);
		}
		else{
			Application::setError(SAVE_FAILED);
			Application::$id = $_POST['id'];
			$this->render('edit');
		}
	}
	else{
		Application::$id = $_POST['id'];
		$this->render('edit');
	}
  }
  
  function actionSaveNew()
  {
    if(!Application::$add) redirect('error/show/1');
	
	if(Form::$isvalid){
		$obj = new AdmCategoriesModel;
		$result = $obj->saveNew($_POST);
		if($result){
			redirect('categories', SAVE_OK);
		}
		else{
			Application::setError(SAVE_FAILED);
			$this->render('add');
		}
	}
	else{
		$this->render('add');
	}
  }
  
  function actionSaveitem()
  {
    if(!Application::$edit) redirect('error/show/1');
	
	if(Form::$isvalid){
		$obj = new AdmCategoriesModel;
		$result = $obj->saveItem($_POST);
		if($result){
			redirect('categories/administrate/listitems/'.$_POST['cat_id'], SAVE_OK);
		}
		else{
			Application::setError(SAVE_FAILED);
			Application::$id = $_POST['id'];
			$this->render('edititem');
		}
	}
	else{
		Application::$id = $_POST['id'];
		$this->render('edititem');
	}
  }
  
  function actionSaveNewItem()
  {
    if(!Application::$add) redirect('error/show/1');
	
	if(Form::$isvalid){
		$obj = new AdmCategoriesModel;
		$result = $obj->saveNewItem($_POST);
		if($result){
			redirect('categories/administrate/listitems/'.$_POST['cat_id'], SAVE_OK);
		}
		else{
			Application::$id = $_POST['cat_id'];
			Application::setError(SAVE_FAILED);
			$this->render('additem');
		}
	}
	else{
		Application::$id = $_POST['cat_id'];
		$this->render('additem');
	}
  }
  
  function actionDeleteConfirm()
  {
    if(!Application::$delete) redirect('error/show/1');
	$obj = new AdmCategoriesModel;
	$result = $obj->delete($_POST);
	if($result){
		redirect('categories', DELETED);
	}
	else{
		redirect('categories', DELETE_FAILED);
	}
  }
  
  function actionDeleteItemConfirm()
  {
    if(!Application::$delete) redirect('error/show/1');
	$obj = new AdmCategoriesModel;
	$result = $obj->deleteItem($_POST);
	if($result){
		redirect('categories/administrate/listitems/'.$_POST['cat_id'], DELETED);
	}
	else{
		redirect('categories/administrate/listitems/'.$_POST['cat_id'], DELETE_FAILED);
	}
  }
  
  /***************************************** RENDERERS ***/
    
  function renderDefault()
  {
    if(!Application::$view) redirect('error/show/1');
	
	$obj = new AdmCategoriesModel;
	$rows = $obj->findAll();
	
	$this->template->title = CAT_DEFAULT_TITLE;
	$this->template->content = Html::elem('p', null, CAT_DEFAULT_DESC) . $this->createCategoriesList($rows);

  }
  
  function renderAdd()
  {
    if(!Application::$add) redirect('error/show/1');
	
	$this->template->setView('default');
	$this->template->title = CAT_ADD_TITLE;
	$this->template->content = $this->createAddForm();
  }
  
  function renderEdit($id)
  {
    if(!Application::$edit) redirect('error/show/1');
	
	$obj = new AdmCategoriesModel;
	if($values = $obj->find($id)){
		$this->template->setView('default');
		$this->template->title = CAT_EDIT_TITLE;
		$this->template->content = $this->createEditForm($id, $values);
	}
	else{
		if(DEVELOPMENT){
			redirect('error/show/404', CAT_ERR_BAD_ID);
		}
		else{
			redirect('error/show/404');
		}
	}
  }
  
  function renderDelete($id)
  {
    if(!Application::$delete) redirect('error/show/1');
	$obj = new AdmCategoriesModel;
	$values = $obj->find($id);
	$this->template->setView('default');
	$this->template->title = CAT_DELETE_TITLE;
	$this->template->content = Html::elem('p', null, CAT_DELETE_TEXT) . $this->createDeleteForm($id, $values);
  }
  
  // Categories items
  function renderListitems($id)
  {
    if(!Application::$view) redirect('error/show/1'); //check permision
	
	$obj = new AdmCategoriesModel;
	$values = $obj->findItems($id);
	$this->template->setView('default');
	$this->template->title = CAT_ITEMS_LIST_TITLE;
	$this->template->content = $this->createItemsList($id, $values);

  }
  
  function renderAdditem($id)
  {
    if(!Application::$add) redirect('error/show/1');
	
	$this->template->setView('default');
	$this->template->title = CAT_ITEMS_ADD_TITLE;
	$this->template->content = $this->createAddItemForm($id);
  }
  
  function renderEdititem($id)
  {
    if(!Application::$add) redirect('error/show/1');
	
	$this->template->setView('default');
	$this->template->title = CAT_ITEMS_EDIT_TITLE;
	$this->template->content = $this->createEditItemForm($id);
  }
  
  function renderDeleteitem($id)
  {
    if(!Application::$delete) redirect('error/show/1');
	$obj = new AdmCategoriesModel;
	$values = $obj->findItem($id);
	$this->template->setView('default');
	$this->template->title = CAT_ITEMS_DELETE_TITLE;
	$this->template->content = Html::elem('p', null, CAT_ITEMS_DELETE_TEXT) . $this->createDeleteItemForm($id, $values);
  }
  
  /***************************************** HANDLERS ***/
  
  
  /***************************************** FACTORIES ***/
  private function createCategoriesList($rows)
  {
	// create add link
	$add = Html::elem('a', array('href'=>Application::link('categories/administrate/add'), 'class'=>'add-link'), '[ '.ADD_NEW_FEMALE.' ]');
	$addP = Html::elem('p', null, $add);
	if(!Application::$add) $addP = ''; // If have no permision to add reset the link
	if(!$rows) return Html::elem('p', null, $addP . CAT_DEFAULT_NO_RECORDS);
	
	$table = new Table('categories', $this);
	$table->setClass('admin-table');
	$table->setDataSource($rows);
	$table->setAjax();
	$table->setPaginator(10, 10);
	
	$table->addColumn('title', TITLE)
	      ->addOrderShift();
	$table->addActions('id', ACTIONS)->setClass('actions')->setStyle('width:72px;');
	  if(Application::$edit) {
	    $table->addAction(EDIT, 'categories/administrate:edit', Table::WITH_KEY)
	          ->setImage( Html::elem('img')->src( Application::imgSrc('icon-edit.jpg') )->style('width:20px;height:20px;')->alt(EDIT) );
	    $table->addAction(SUBCATEGORIES, 'categories/administrate:listitems', Table::WITH_KEY)
	          ->setImage( Html::elem('img')->src(Application::imgSrc('icon-items.jpg'))->style('width:20px;height:20px;')->alt(SUBCATEGORIES) );
	  }
	  else {
	    $table->addAction(EDIT, 'categories/administrate:edit', Table::WITH_KEY)
	          ->setDisabled()
	          ->setImage( Html::elem('img')->src(Application::imgSrc('icon-edit-gr.jpg'))->style('width:20px;height:20px;')->alt(EDIT) );
	    $table->addAction(SUBCATEGORIES, 'categories/administrate:listitems', Table::WITH_KEY)
	          ->setDisabled()
	          ->setImage( Html::elem('img')->src(Application::imgSrc('icon-items-gr.jpg'))->style('width:20px;height:20px;')->alt(SUBCATEGORIES) );
	  }
	  
	  if(Application::$delete) {
	    $table->addAction(DELETE, 'categories/administrate:delete', Table::WITH_KEY)
	          ->setImage( Html::elem('img')->src(Application::imgSrc('icon-delete.jpg'))->style('width:20px;height:20px;')->alt(DELETE) );
	  }
	  else {
	    $table->addAction(DELETE, 'categories/administrate:delete', Table::WITH_KEY)
	          ->setDisabled()
	          ->setImage( Html::elem('img')->src(Application::imgSrc('icon-delete-gr.jpg'))->style('width:20px;height:20px;')->alt(DELETE) );
	  }
	
	return $addP . $table;
	
  }
  
  private function createItemsList($cat_id, $rows)
  {
	// create add link
	$add = Html::elem('a', array('href'=>BASEPATH.'/categories/administrate/additem/'.$cat_id, 'class'=>'add-link'), '[ '.ADD_NEW_FEMALE.' ]');
	$addP = Html::elem('p', null, $add);
	if(!Application::$add) $addP = ''; // If have no permision to add reset the link
	if(!$rows) return Html::elem('p', null, $addP . CAT_ITEMS_NO_RECORDS);
	
	$table = new Table('subcategories', $this);
	$table->setClass('admin-table');
	$table->setDataSource($rows);
	$table->setAjax();
	$table->setPaginator(10, 10);
	
	$table->addColumn('title', NAME)
	      ->addOrderShift();
	$table->addActions('id', ACTIONS)->setClass('actions')->setStyle('width:48px;');
	  if(Application::$edit) {
	    $table->addAction(EDIT, 'categories/administrate:edititem', Table::WITH_KEY)
	          ->setImage( Html::elem('img')->src(Application::imgSrc('icon-edit.jpg'))->style('width:20px;height:20px;')->alt(EDIT) );
	  }
	  else {
	    $table->addAction(EDIT, 'categories/administrate:edititem', Table::WITH_KEY)
	          ->setDisabled()
	          ->setImage( Html::elem('img')->src(Application::imgSrc('icon-edit-gr.jpg'))->style('width:20px;height:20px;')->alt(EDIT) );
	  }
	  
	  if(Application::$delete) {
	    $table->addAction(DELETE, 'categories/administrate:deleteitem', Table::WITH_KEY)
	          ->setImage( Html::elem('img')->src(Application::imgSrc('icon-delete.jpg'))->style('width:20px;height:20px;')->alt(DELETE) );
	  }
	  else {
	    $table->addAction(DELETE, 'categories/administrate:deleteitem', Table::WITH_KEY)
	          ->setDisabled()
	          ->setImage( Html::elem('img')->src(Application::imgSrc('icon-delete-gr.jpg'))->style('width:20px;height:20px;')->alt(DELETE) );
	  }
	
	return $addP . $table;
	
  }
  
  private function createAddForm()
  {
    $form = new Form('catAddForm', 'cat-add-form', Application::link('categories/administrate/save-new'));
	
	if($_POST){
		$values = $_POST;
	}
	else{
		$values = $form->getZeroValues('categories');
	}
	
	foreach($values as $key => $val){
		$$key = $val;
	}
	// get menus into array
	$menus = $this->getMenus();
	// get conntent types into array for checkbox group
	$content_types = $this->getContentTypesArray(null);
	
	// Fill checkbox values
	isset($main_menu_item) ? $main_menu_item = $main_menu_item : $main_menu_item = 0;
	isset($show_partial) ? $show_partial = $show_partial : $show_partial = 0;
	isset($show_updated) ? $show_updated = $show_updated : $show_updated = 0;
	isset($show_user) ? $show_user = $show_user : $show_user = 0;
	isset($image_gallery) ? $image_gallery = $image_gallery : $image_gallery = 0;
	isset($required) ? $required = $required : $required = 0;
	$show_created = isset($show_created) ? $show_created : 0;
	$show_pages = isset($show_pages) ? $show_pages : 0;
	
	$form->addText('title', TITLE, $title);
		$form->addRule('title', Form::FILLED, CAT_FRM_TITLE_RULE);
	$form->addText('menu_title', MENU_TITLE, $menu_title);
		$form->addDescription('menu_title', CAT_FRM_MENU_TITLE_DESC);
	$form->addTextarea('description', DESCRIPTION, $description);
	$form->emptyLine();
	$form->addHidden('content_types_names', $content_types['content_types']);
	unset($content_types['content_types']);
	$form->addCheckboxGroup('content_types', $content_types, CAT_FRM_CONTENT_TYPES_LABEL, false);
	$form->emptyLine();
	$form->addCheckbox('main_menu_item', 1, CAT_FRM_MAIN_MENU_ITEM_LABEL,'',$main_menu_item);
	$form->emptyLine();
	$form->addCheckbox('show_partial', 1, '', CAT_FRM_SHOW_PARTIAL_LABEL, $show_partial);
		$form->addDescription('show_partial', CAT_FRM_SHOW_PARTIAL_DESC);
	$form->addText('chars_num', CAT_FRM_CHARS_NUM_LABEL, $chars_num, 'frm-text', 3, 3);
		$form->addRule('chars_num', Form::NUMERIC, CAT_FRM_CHARS_NUM_RULE);
	$form->emptyLine();
	$form->addCheckbox('show_created', 1, CAT_FRM_SHOW_CREATED_LABEL,'',$show_created, 'frm-checkbox', true);
	$form->addCheckbox('show_updated', 1, CAT_FRM_SHOW_UPDATED_LABEL,'',$show_updated, 'frm-checkbox', true);
	$form->addCheckbox('show_user', 1, CAT_FRM_SHOW_USER_LABEL,'',$show_user, 'frm-checkbox');
	$form->emptyLine();
	$form->addCheckbox('image_gallery', 1, CAT_FRM_IMAGE_GALLERY_LABEL,'',$image_gallery);
		$form->addDescription('image_gallery', CAT_FRM_IMAGE_GALLERY_DESC);
	$form->emptyLine();
	$form->addCheckbox('required', 1, REQUIRED,'',$required);
		$form->addDescription('required', CAT_FRM_REQUIRED_DESC);
	$form->emptyLine();
	$form->addCheckbox('show_pages', 1, CAT_FRM_SHOW_PAGES_LABEL,'',$show_pages);
		$form->addDescription('show_pages', CAT_FRM_SHOW_PAGES_DESC);
	$form->addSelect('items_per_page', array(5=>5, 10=>10, 15=>15, 20=>20, 25=>25, 30=>30), CAT_FRM_ITEMS_PER_PAGE_LABEL, $items_per_page);
	$form->addSelect('paginator_limit', array(5=>5, 10=>10, 15=>15), CAT_FRM_PAGINATOR_LIMIT_LABEL, $paginator_limit);
	$form->emptyLine();
	$form->addText('path_alias', URL_ALIAS, $path_alias);
	$form->emptyLine();
	$form->addSelect('menu_id', $menus, 'Zaradiť do menu', $menu_id);
	$form->emptyLine();
	$form->addSubmit('save', SAVE);
	
	return $form->render();
	
  }
  
  private function createAddItemForm($catid)
  {
    $form = new Form('catAddItemForm', 'cat-add-item-form', Application::link('categories/administrate/save-newitem'));
	
	if($_POST){
		$values = $_POST;
	}
	else{
		$values = $form->getZeroValues('categories_items');
	}
	
	foreach($values as $key => $val){
		$$key = $val;
	}
	isset($show_images) ? $show_images = $show_images : $show_images = 0;
	isset($show_files) ? $show_files = $show_files : $show_files = 0;
	$cat_id = $catid;
	// get menu id
	$cat_vals = $this->getCategoryMenu($cat_id);
	
	$menu_id = $cat_vals['menu_id'];
	$child_of = $cat_vals['menu_item_id'];
	
	$form->addHidden('cat_id', $cat_id);
	$form->addHidden('menu_id', $menu_id);
	$form->addHidden('child_of', $child_of);
	$form->addText('title', TITLE, $title);
		$form->addRule('title', Form::FILLED, CAT_FRM_TITLE_RULE);
	$form->addText('menu_title', MENU_TITLE, $menu_title);
		$form->addDescription('menu_title', CAT_FRM_MENU_TITLE_DESC);
	$form->addTextarea('description', DESCRIPTION, $description);
	$form->addCheckbox('show_images', 1, CAT_FRM_SHOW_IMAGES_LABEL,'',$show_images);
	$form->addCheckbox('show_files', 1, CAT_FRM_SHOW_FILES_LABEL,'',$show_files);
	$form->emptyLine();
	$form->addText('path_alias', URL_ALIAS, $path_alias);
	$form->emptyLine();
	$form->addSubmit('save', SAVE);
	
	return $form->render();
	
  }
  
  private function createEditForm($id, $values)
  {
    $form = new Form('catEditForm', 'cat-edit-form', Application::link('categories/administrate/save'));
	
	if($_POST){
		$values = $_POST;
	}
	
	foreach($values as $key => $val){
		$$key = $val;
	}
	
	// get menus into array
	$menus = $this->getMenus();
	
	// get conntent types into array for checkbox group
	$content_types = $this->getContentTypesArray($id);
	
	// Fill checkbox values
	isset($main_menu_item) ? $main_menu_item = $main_menu_item : $main_menu_item = 0;
	isset($show_partial) ? $show_partial = $show_partial : $show_partial = 0;
	isset($show_updated) ? $show_updated = $show_updated : $show_updated = 0;
	isset($show_user) ? $show_user = $show_user : $show_user = 0;
	isset($image_gallery) ? $image_gallery = $image_gallery : $image_gallery = 0;
	isset($required) ? $required = $required : $required = 0;
	$show_created = isset($show_created) ? $show_created : 0;
	$show_pages = isset($show_pages) ? $show_pages : 0;
	
	$form->addHidden('id', $id);
	$form->addHidden('menu_item_id', $menu_item_id);
	$form->addText('title', TITLE, $title);
		$form->addRule('title', Form::FILLED, CAT_FRM_TITLE_RULE);
	$form->addText('menu_title', MENU_TITLE, $menu_title);
		$form->addDescription('menu_title', CAT_FRM_MENU_TITLE_DESC);
	$form->addTextarea('description', DESCRIPTION, $description);
	$form->emptyLine();
	$form->addHidden('content_types_names', $content_types['content_types']);
	unset($content_types['content_types']);
	$form->addCheckboxGroup('content_types', $content_types, CAT_FRM_CONTENT_TYPES_LABEL, false);
	$form->emptyLine();
	$form->addCheckbox('main_menu_item', 1, CAT_FRM_MAIN_MENU_ITEM_LABEL,'',$main_menu_item);
	$form->emptyLine();
	$form->addCheckbox('show_partial', 1, '', CAT_FRM_SHOW_PARTIAL_LABEL, $show_partial);
		$form->addDescription('show_partial', CAT_FRM_SHOW_PARTIAL_DESC);
	$form->addText('chars_num', CAT_FRM_CHARS_NUM_LABEL, $chars_num, 'frm-text', 3, 3);
		$form->addRule('chars_num', Form::NUMERIC, CAT_FRM_CHARS_NUM_RULE);
	$form->emptyLine();
	$form->addCheckbox('show_created', 1, CAT_FRM_SHOW_CREATED_LABEL,'',$show_created, 'frm-checkbox', true);
	$form->addCheckbox('show_updated', 1, CAT_FRM_SHOW_UPDATED_LABEL,'',$show_updated, 'frm-checkbox', true);
	$form->addCheckbox('show_user', 1, CAT_FRM_SHOW_USER_LABEL,'',$show_user, 'frm-checkbox');
	$form->emptyLine();
	$form->addCheckbox('image_gallery', 1, CAT_FRM_IMAGE_GALLERY_LABEL,'',$image_gallery);
		$form->addDescription('image_gallery', CAT_FRM_IMAGE_GALLERY_DESC);
	$form->emptyLine();
	$form->addCheckbox('required', 1, REQUIRED,'',$required);
		$form->addDescription('required', CAT_FRM_REQUIRED_DESC);
	$form->emptyLine();
	$form->addCheckbox('show_pages', 1, CAT_FRM_SHOW_PAGES_LABEL,'',$show_pages);
		$form->addDescription('show_pages', CAT_FRM_SHOW_PAGES_DESC);
	$form->addSelect('items_per_page', array(5=>5, 10=>10, 15=>15, 20=>20, 25=>25, 30=>30), CAT_FRM_ITEMS_PER_PAGE_LABEL, $items_per_page);
	$form->addSelect('paginator_limit', array(5=>5, 10=>10, 15=>15), CAT_FRM_PAGINATOR_LIMIT_LABEL, $paginator_limit);
	$form->emptyLine();
	$form->addText('path_alias', URL_ALIAS, $path_alias);
	$form->emptyLine();
	isset($old_menu_id) ? $old_menu_id = $old_menu_id : $old_menu_id = $menu_id;
	$form->addHidden('old_menu_id', $old_menu_id);
	$form->addSelect('menu_id', $menus, 'Zaradiť do menu', $menu_id);
	$form->emptyLine();
	$form->addSubmit('save', SAVE);
	
	return $form->render();
	
  }
  
  private function createEditItemForm($id)
  {
    $obj = new AdmCategoriesModel;
	
	$form = new Form('catEditItemForm', 'cat-edit-item-form', Application::link('categories/administrate/saveitem'));
	
	if($_POST){
		$values = $_POST;
	}
	else{
		$values = $obj->findItem($id);
	}
	
	foreach($values as $key => $val){
		$$key = $val;
	}
	
	isset($show_images) ? $show_images = $show_images : $show_images = 0;
	isset($show_files) ? $show_files = $show_files : $show_files = 0;
	
	// get menu id
	$cat_vals = $this->getCategoryMenu($cat_id);
	
	$menu_id = $cat_vals['menu_id'];
	$child_of = $cat_vals['menu_item_id'];
	
	$form->addHidden('id', $id);
	$form->addHidden('cat_id', $cat_id);
	$form->addHidden('menu_id', $menu_id);
	$form->addHidden('menu_item_id', $menu_item_id);
	$form->addHidden('child_of', $child_of);
	$form->addText('title', TITLE, $title);
		$form->addRule('title', Form::FILLED, CAT_FRM_TITLE_RULE);
	$form->addText('menu_title', MENU_TITLE, $menu_title);
		$form->addDescription('menu_title', CAT_FRM_MENU_TITLE_DESC);
	$form->addTextarea('description', DESCRIPTION, $description);
	$form->addCheckbox('show_images', 1, CAT_FRM_SHOW_IMAGES_LABEL,'',$show_images);
	$form->addCheckbox('show_files', 1, CAT_FRM_SHOW_FILES_LABEL,'',$show_files);
	$form->emptyLine();
	$form->addText('path_alias', URL_ALIAS, $path_alias);
	$form->emptyLine();
	$form->addSubmit('save', SAVE);
	
	return $form->render();
	
  }
  
  private function createDeleteForm($id, $values)
  {
    $form = new Form('catDeleteForm', 'cat-delete-form', Application::link('categories/administrate/delete-confirm'));
	
	foreach($values as $key => $val){
		$$key = $val;
	}
	
	$form->addHidden('id', $id);
	$form->addHidden('menu_id', $menu_id);
	$form->addHidden('menu_item_id', $menu_item_id);
	$form->addHidden('path_alias', $path_alias);
	
	$form->addSubmit('delete', GOON);
	
	return $form->render();
	
  }
  
  private function createDeleteItemForm($id, $values)
  {
    $form = new Form('catDeleteForm', 'cat-delete-form', Application::link('categories/administrate/delete-item-confirm'));
	
	foreach($values as $key => $val){
		$$key = $val;
	}
	
	$form->addHidden('id', $id);
	$form->addHidden('cat_id', $cat_id);
	$form->addHidden('menu_item_id', $menu_item_id);
	$form->addHidden('path_alias', $path_alias);
	
	$form->addSubmit('delete', GOON);
	
	return $form->render();
	
  }
  
  private function getMenus()
  {
    $obj = new AdmCategoriesModel;
	$rows = $obj->findMenus();
	foreach($rows as $row){
		$return[$row['id']] = $row['name'];
	}
	return $return;
  }
  
  private function getCategoryMenu($cat_id)
  {
    $obj = new AdmCategoriesModel;
	$row = $obj->findCategoryMenu($cat_id);
	return $row;
  }
  
  private function getContentTypesArray($id)
  {
    $return = null;
	
	$obj = new AdmCategoriesModel;
	
	$rows = $obj->findContentTypes();
	
	if($id)	$cat_content_types = $obj->findActiveContentTypes($id);
		else $cat_content_types = null;
	
	if($cat_content_types){
		foreach($cat_content_types as $cat_ct_type){
			$$cat_ct_type['ct_name'] = 1;
		}
	}
	
	$types = '';
	if($rows){
		foreach($rows as $row){
			if(isset($$row['machine_name'])) $checked = true;
				else $checked = false;
			$return[] = array('name'=>$row['machine_name'], 'value'=>1, 'option'=>$row['name'], 'checked'=>$checked);
			$types .= $row['machine_name'].':';
		}
		$return['content_types'] = substr($types, 0, -1); //remove last ":" 
	}
	
	return $return;
  }
  
  
  private function createImgGallery($id,$order_by)
  {
  
	$obj = new ContentModel;
	$rows = $obj->findImages($id,$order_by);
	if($rows){
		$lis = '';
		foreach($rows as $row){
		  $src = BASEPATH.'/images/thumb_'.$row['image_name'];
		  $imgSize = getImageSize($src);
		  $img = Html::elem('img', array('src'=>$src, 'width'=>$imgSize[0], 'height'=>$imgSize[1], 'alt'=>$row['description'], 'style'=>'border:none'));
		  $a = Html::elem('a', array('href'=>Application::imgSrc($row['image_name']), 'rel'=>'gallery'.$id, 'class'=>'content-gallery-'.$id, 'title'=>$row['description']), $img);
		  $lis .= Html::elem('li', array('id'=>'image-'.$row['id'], 'class'=>'image-gallery-image'), $a);
		}
		
		$output = Html::elem('ul', array('id'=>'image-gallery-'.$id, 'class'=>'image-gallery'), $lis.'<div class="clearfloat"></div>');
		$output .= "
			<script type='text/javascript'>
			/* <![CDATA[ */
				jQuery(document).ready(function($){
				  $('a.content-gallery-$id').fancybox();
				});
			/* ]]> */
			</script>";
		return $output;
	}
	else{
		return '';
	}
  }
  
  private function createFilesTable($id,$order_by)
  {
  
	$obj = new ContentModel;
	$rows = $obj->findFiles($id,$order_by);
	if($rows){
		$tr = '';
		$idx = 0;
		foreach($rows as $row){
		  if($idx == 1){
		      $trclass = 'even';
			  $idx = 0;
		  }
		  else{
		      $trclass = 'odd';
			  $idx++;
		  }
		  
		  if(trim($row['description']) != ''){
			  $label = $row['description'];
		  }
		  else{
			  $label = $row['file_name'];
		  }
		  
		  $filesize = round(@filesize(WWW_DIR.'/files/'.$row['file_name'])/1024, 2) . 'kB';
		  if(@filesize(WWW_DIR.'/files/'.$row['file_name']) > 1024000) $filesize = round(@filesize(WWW_DIR.'/files/'.$row['file_name'])/1024000, 2) . 'MB';
		  //find fyle-type icon
		  if(is_file(WWW_DIR.'/images/icons/'.$row['file_type'].'.png')){
		      $icon = Html::elem('img',array('src'=>BASEPATH.'/images/icons/'.$row['file_type'].'.png', 'class'=>'content-files-icon', 'alt'=>strtoupper($row['file_type']).' súbor', 'title'=>strtoupper($row['file_type']).' súbor'));
		  }
		  else{
		      $icon = Html::elem('img',array('src'=>BASEPATH.'/images/icons/na.png', 'class'=>'content-files-icon', 'alt'=>strtoupper($row['file_type']).' súbor', 'title'=>strtoupper($row['file_type']).' súbor'));
		  }
		  $a = Html::elem('a', array('href'=>BASEPATH.'/files/'.$row['file_name'], 'class'=>'link content-files-'.$id, 'title'=>$row['description']), $label);
		  $span = Html::elem('span', array('class'=>'filesize content-files-'.$id), $filesize);
		  $td = Html::elem('td', array('class'=>'icon'), $icon);
		  $td .= Html::elem('td', array('class'=>'file'), $a);
		  $td .= Html::elem('td', array('class'=>'size'), $span);
		  $tr .= Html::elem('tr', array('class'=>$trclass), $td);
		}
		$table = Html::elem('table', array('id'=>'content-files-table-'.$id, 'class'=>'content-files-table', 'cellspacing'=>'0'), $tr);
		
		return  $table;
	}
	else{
		return '';
	}
  }

}