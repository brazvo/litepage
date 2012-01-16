<?php

class Categories extends Controller
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
			redirect('categories/listitems/'.$_POST['cat_id'], SAVE_OK);
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
			redirect('categories/listitems/'.$_POST['cat_id'], SAVE_OK);
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
		redirect('categories/listitems/'.$_POST['cat_id'], DELETED);
	}
	else{
		redirect('categories/listitems/'.$_POST['cat_id'], DELETE_FAILED);
	}
  }
  
  /***************************************** RENDERERS ***/
  function renderAdmin()
  {
    if(!Application::$view) redirect('error/show/1');
	
	$obj = new AdmCategoriesModel;
	$rows = $obj->findAll();
	$this->template['view'] = 'default';
	$this->template['title'] = CAT_DEFAULT_TITLE;
	$this->template['content'] = Html::elem('p', null, CAT_DEFAULT_DESC) . $this->createCategoriesList($rows);

  }
  
  function renderDefault()
  {
    if(!Application::$view) redirect('error/show/1');
	
	$obj = new AdmCategoriesModel;
	$rows = $obj->findAll();
	
	$this->template['title'] = CAT_DEFAULT_TITLE;
	$this->template['content'] = Html::elem('p', null, CAT_DEFAULT_DESC) . $this->createCategoriesList($rows);

  }
  
  function renderAdd()
  {
    if(!Application::$add) redirect('error/show/1');
	
	$this->template['view'] = 'default';
	$this->template['title'] = CAT_ADD_TITLE;
	$this->template['content'] = $this->createAddForm();
  }
  
  function renderEdit($id)
  {
    if(!Application::$edit) redirect('error/show/1');
	
	$obj = new AdmCategoriesModel;
	if($values = $obj->find($id)){
		$this->template['view'] = 'default';
		$this->template['title'] = CAT_EDIT_TITLE;
		$this->template['content'] = $this->createEditForm($id, $values);
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
	$this->template['view'] = 'default';
	$this->template['title'] = CAT_DELETE_TITLE;
	$this->template['content'] = Html::elem('p', null, CAT_DELETE_TEXT) . $this->createDeleteForm($id, $values);
  }
  
  // Categories items
  function renderListitems($id)
  {
    if(!Application::$view) redirect('error/show/1'); //check permision
	
	$obj = new AdmCategoriesModel;
	$values = $obj->findItems($id);
	$this->template['view'] = 'default';
	$this->template['title'] = CAT_ITEMS_LIST_TITLE;
	$this->template['content'] = $this->createItemsList($id, $values);

  }
  
  function renderShow($id)
  {
    if(!Application::$view) redirect('error/show/1'); //check permision
	
	$obj = new AdmCategoriesModel;
	$row = $obj->find($id);
	
	$values = $obj->findAllContent($id);
	
	$this->template['view'] = 'showitem';
	$this->template['title'] = $row['title'];
	$this->template['mainclass'] = strtolower(machineStr($row['title']));
	$this->template['description'] = $this->texy->process($row['description']);
	$this->template['content'] = $this->createContentList($row['id'], $values);

  }
  
  function renderShowitem($id)
  {
    if(!Application::$view) redirect('error/show/1'); //check permision
	
	$obj = new AdmCategoriesModel;
	$row = $obj->findItem($id);
	
	$values = $obj->findContent($row['cat_id'], $id);
	
	$this->template['title'] = $row['title'];
	$this->template['mainclass'] = strtolower(machineStr($row['title']));
	$this->template['description'] = $this->texy->process($row['description']);
	$this->template['content'] = $this->createContentListForItem($row['cat_id'], $values);

  }
  
  function renderAdditem($id)
  {
    if(!Application::$add) redirect('error/show/1');
	
	$this->template['view'] = 'default';
	$this->template['title'] = CAT_ITEMS_ADD_TITLE;
	$this->template['content'] = $this->createAddItemForm($id);
  }
  
  function renderEdititem($id)
  {
    if(!Application::$add) redirect('error/show/1');
	
	$this->template['view'] = 'default';
	$this->template['title'] = CAT_ITEMS_EDIT_TITLE;
	$this->template['content'] = $this->createEditItemForm($id);
  }
  
  function renderDeleteitem($id)
  {
    if(!Application::$delete) redirect('error/show/1');
	$obj = new AdmCategoriesModel;
	$values = $obj->findItem($id);
	$this->template['view'] = 'default';
	$this->template['title'] = CAT_ITEMS_DELETE_TITLE;
	$this->template['content'] = Html::elem('p', null, CAT_ITEMS_DELETE_TEXT) . $this->createDeleteItemForm($id, $values);
  }
  
  /***************************************** HANDLERS ***/
  
  
  /***************************************** FACTORIES ***/
  private function createCategoriesList($rows)
  {
	// create add link
	$add = Html::elem('a', array('href'=>BASEPATH.'/categories/add', 'class'=>'add-link'), '[ '.ADD_NEW_FEMALE.' ]');
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
	    $table->addAction(EDIT, 'categories:edit', Table::WITH_KEY)
	          ->setImage( Html::elem('img')->src(BASEPATH . '/images/icon-edit.jpg')->style('width:20px;height:20px;')->alt(EDIT) );
	    $table->addAction(SUBCATEGORIES, 'categories:listitems', Table::WITH_KEY)
	          ->setImage( Html::elem('img')->src(BASEPATH . '/images/icon-items.jpg')->style('width:20px;height:20px;')->alt(SUBCATEGORIES) );
	  }
	  else {
	    $table->addAction(EDIT, 'categories:edit', Table::WITH_KEY)
	          ->setDisabled()
	          ->setImage( Html::elem('img')->src(BASEPATH . '/images/icon-edit-gr.jpg')->style('width:20px;height:20px;')->alt(EDIT) );
	    $table->addAction(SUBCATEGORIES, 'categories:listitems', Table::WITH_KEY)
	          ->setDisabled()
	          ->setImage( Html::elem('img')->src(BASEPATH . '/images/icon-items-gr.jpg')->style('width:20px;height:20px;')->alt(SUBCATEGORIES) );
	  }
	  
	  if(Application::$delete) {
	    $table->addAction(DELETE, 'categories:delete', Table::WITH_KEY)
	          ->setImage( Html::elem('img')->src(BASEPATH . '/images/icon-delete.jpg')->style('width:20px;height:20px;')->alt(DELETE) );
	  }
	  else {
	    $table->addAction(DELETE, 'categories:delete', Table::WITH_KEY)
	          ->setDisabled()
	          ->setImage( Html::elem('img')->src(BASEPATH . '/images/icon-delete-gr.jpg')->style('width:20px;height:20px;')->alt(DELETE) );
	  }
	
	return $addP . $table;
	
  }
  
  private function createContentList($cat_id, $rows)
  {
	$obj = new AdmCategoriesModel;
	
	$settings = $obj->getCatSettings($cat_id);
	foreach($settings as $key=>$val){
		$$key = $val;
	}
	
	$contDivs = '';
	if($rows){
	  $lastrow = count($rows);
	  $idx = 0;
	  foreach($rows as $cat_item_id => $catitems){
	    $idx++;
	    $show_files = $obj->getShowFiles($cat_item_id);
	    $show_images = $obj->getShowImages($cat_item_id);
		$catLnk = Html::elem('a', array('href'=>BASEPATH.'/categories/showitem/'.$cat_item_id), $obj->getCatItemTitle($cat_item_id));
		$contDivs .= Html::elem('h2', array('class'=>'title', 'style'=>'float:none;'), $catLnk);
		$itemidx = 0;
		$lastitem = count($catitems);
		foreach($catitems as $row){
			$itemidx++;
		    if($itemidx == $lastitem) $clsLast = ' last-image';
				else $clsLast = '';
			//if image gallery
			if($image_gallery){
				$image = $obj->getFirstImage($row['id']);
				$src = BASEPATH.'/images/thumb_'.$image;
				$imgSize = getImageSize($src);
				$img = Html::elem('img', array('style'=>'border:none', 'src'=>$src, 'width'=>$imgSize[0], 'height'=>$imgSize[1], 'title'=>$row['content_title'], 'alt'=>$row['content_title']));
				$a = Html::elem('a', array('href'=>BASEPATH. '/' . ($row['path_alias'] ? $row['path_alias'] : 'content/show/'.$row['id'])), $img);
				$contDivs .= Html::elem('div', array('class'=>'categories-content-gallery-container'.$clsLast),$a);
			}
			else{
			
				$content_body = $obj->getContentBody($row['content_type_machine_name'], $row['content_id']);
				
				if($show_images){
					if($order_by = $obj->getImagesOrdering($row['content_type_id'])){
						$imgs = $this->createImgGallery($row['id'], $order_by);
					}
					else{
						$imgs = '';
					}
				}
				else{
					$imgs = '';
				}
				
				if($show_files){
					if($order_by = $obj->getFilesOrdering($row['content_type_id'])){
						$files = $this->createFilesTable($row['id'], $order_by);
					}
					else{
						$files = '';
					}
				}
				else{
					$files = '';
				}
			
				if(trim($row['path_alias']) != ''){
					$titleLnk = Html::elem('a', array('href'=>BASEPATH.'/'.$row['path_alias']), $row['content_title']);
				}
				else{
					$titleLnk = Html::elem('a', array('href'=>BASEPATH.'/content/show/'.$row['id']), $row['content_title']);
				}
				
				$titleDiv = Html::elem('h3', array('class'=>'categories-content-title'), $titleLnk);
				if($show_partial){
                                        $cleanString = strip_tags($this->texy->process($content_body));
                                        if(mb_strlen($cleanString, 'utf-8') > $chars_num)
                                            $bodyDiv = Html::elem('div', array('class'=>'categories-content-body'), mb_substr(strip_tags($this->texy->process($content_body)), 0, $chars_num, 'utf-8').'...');
                                        else
                                            $bodyDiv = Html::elem('div', array('class'=>'categories-content-body'), strip_tags($this->texy->process($content_body)));
				}
				else{
					$bodyDiv = Html::elem('div', array('class'=>'categories-content-body'), $this->texy->process($content_body));
				}
				if($show_user){
					$userSpan = Html::elem('span', array('class'=>'categories-content-user'), AUTHOR.': '.$obj->getUserName($row['uid']).'<br/>');
				}
				else{
					$userSpan = '';
				}
				if($show_updated){
					$updSpan = Html::elem('span', array('class'=>'categories-content-updated'), /*LAST_UPDATE.': '.*/slovdate($row['last_update']));
				}
				else{
					$updSpan = '';
				}
				if($userSpan or $updSpan){
					$userDiv = Html::elem('div', array('class'=>'categories-content-user-div'),$userSpan.$updSpan);
				}
				else{
					$userDiv = '';
				}
				
				$contDivs .= Html::elem('div', array('class'=>'categories-content-row'),$titleDiv . $userDiv . $bodyDiv . $imgs . $files);
				if($idx < $lastrow) $contDivs .= Html::elem('div', array('class'=>'categories-spacer'),'&nbsp;');
		    }
		}
		
	  }
	}
	else{
		$contDivs = '';
	}
	
	if($image_gallery) return $contDivs . '<br style="clear:both" />';
		else return $contDivs;
	
  }
  
  private function createContentListForItem($cat_id, $rows)
  {
	$obj = new AdmCategoriesModel;
	
	$settings = $obj->getCatSettings($cat_id);
	foreach($settings as $key=>$val){
		$$key = $val;
	}
	
	$contDivs = '';
	if($rows){
		foreach($rows as $row){
			$show_files = $obj->getShowFiles($this->id);
	                $show_images = $obj->getShowImages($this->id);
			//if image gallery
			if($image_gallery){
				$image = $obj->getFirstImage($row['id']);
				if($image){
					$src = BASEPATH.'/images/thumb_'.$image;
					$imgSize = getImageSize($src);
					$img = Html::elem('img', array('style'=>'border:none', 'src'=>$src, 'width'=>$imgSize[0], 'height'=>$imgSize[1],'title'=>$row['content_title'], 'alt'=>$row['content_title']));
					$a = Html::elem('a', array('href'=>BASEPATH.'/'.($row['path_alias'] ? $row['path_alias'] : 'content/show/'.$row['id'])), $img);
					$contDivs .= Html::elem('div', array('class'=>'categories-content-gallery-container'),$a);
				}
				else{
					$contDivs .='';
				}
			}
			else{
			
				$content_body = $obj->getContentBody($row['content_type_machine_name'], $row['content_id']);
				
				if($show_images){
					if($order_by = $obj->getImagesOrdering($row['content_type_id'])){
						$imgs = $this->createImgGallery($row['id'], $order_by);
						$firstImg = '';
					}
					else{
						$imgs = '';
						$firstImg = '';
					}
				}
				else{
					$imgs = '';
					$image = $obj->getFirstImage($row['id']);
					if($image){
						$src = BASEPATH.'/images/thumb_'.$image;
						$imgSize = getImageSize($src);
						$img = Html::elem('img', array('style'=>'border:none', 'src'=>$src, 'width'=>$imgSize[0], 'height'=>$imgSize[1],'title'=>$row['content_title'], 'alt'=>$row['content_title']));
						$a = Html::elem('a', array('href'=>BASEPATH.'/'.($row['path_alias'] ? $row['path_alias'] : 'content/show/'.$row['id'])), $img);
						$firstImg = Html::elem('div', array('class'=>'categories-first-image'),$a);
					}
					else{
						$firstImg ='';
					}
				}
				
				if($show_files){
					if($order_by = $obj->getFilesOrdering($row['content_type_id'])){
						$files = $this->createFilesTable($row['id'], $order_by);
					}
					else{
						$files = '';
					}
				}
				else{
					$files = '';
				}
			
				if(trim($row['path_alias']) != ''){
					$titleLnk = Html::elem('a', array('href'=>BASEPATH.'/'.$row['path_alias']), $row['content_title']);
					$moreLnk = Html::elem('a', array('href'=>BASEPATH.'/'.$row['path_alias']), CAT_READ_MORE.'&nbsp;>>');
				}
				else{
					$titleLnk = Html::elem('a', array('href'=>BASEPATH.'/content/show/'.$row['id']), $row['content_title']);
					$moreLnk = Html::elem('a', array('href'=>BASEPATH.'/content/show/'.$row['id']), CAT_READ_MORE.'&nbsp;>>');
				}
				
				$titleDiv = Html::elem('h3', array('class'=>'categories-content-title'), $titleLnk);
				if($show_partial){
					$bodyDiv = Html::elem('div', array('class'=>'categories-content-body'), substr(strip_tags($this->texy->process($content_body)), 0, $chars_num) .'...&nbsp;&nbsp; '.$moreLnk);
				}
				else{
					$bodyDiv = Html::elem('div', array('class'=>'categories-content-body'), $this->texy->process($content_body));
				}
				if($show_user){
					$userSpan = Html::elem('span', array('class'=>'categories-content-user'), AUTHOR.': '.$obj->getUserName($row['uid']).'<br/>');
				}
				else{
					$userSpan = '';
				}
				if($show_updated){
					$updSpan = Html::elem('span', array('class'=>'categories-content-updated'), /*LAST_UPDATE.': '.*/slovdate($row['last_update']));
				}
				else{
					$updSpan = '';
				}
				if($userSpan or $updSpan){
					$userDiv = Html::elem('div', array('class'=>'categories-content-user-div'),$userSpan.$updSpan);
				}
				else{
					$userDiv = '';
				}
				
				$contDivs .= Html::elem('div', array('class'=>'categories-content-row'),$titleDiv . $userDiv . $firstImg . $bodyDiv . $imgs . $files);
		    }
	    }
	}
	else{
		$contDivs = '';
	}
	
	if($image_gallery) return $contDivs . '<br style="clear:both" />';
		else return $contDivs;
	
  }
  
  private function createItemsList($cat_id, $rows)
  {
	// create add link
	$add = Html::elem('a', array('href'=>BASEPATH.'/categories/additem/'.$cat_id, 'class'=>'add-link'), '[ '.ADD_NEW_FEMALE.' ]');
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
	    $table->addAction(EDIT, 'categories:edititem', Table::WITH_KEY)
	          ->setImage( Html::elem('img')->src(BASEPATH . '/images/icon-edit.jpg')->style('width:20px;height:20px;')->alt(EDIT) );
	  }
	  else {
	    $table->addAction(EDIT, 'categories:edititem', Table::WITH_KEY)
	          ->setDisabled()
	          ->setImage( Html::elem('img')->src(BASEPATH . '/images/icon-edit-gr.jpg')->style('width:20px;height:20px;')->alt(EDIT) );
	  }
	  
	  if(Application::$delete) {
	    $table->addAction(DELETE, 'categories:deleteitem', Table::WITH_KEY)
	          ->setImage( Html::elem('img')->src(BASEPATH . '/images/icon-delete.jpg')->style('width:20px;height:20px;')->alt(DELETE) );
	  }
	  else {
	    $table->addAction(DELETE, 'categories:deleteitem', Table::WITH_KEY)
	          ->setDisabled()
	          ->setImage( Html::elem('img')->src(BASEPATH . '/images/icon-delete-gr.jpg')->style('width:20px;height:20px;')->alt(DELETE) );
	  }
	
	return $addP . $table;
	
  }
  
  private function createAddForm()
  {
    $form = new Form('catAddForm', 'cat-add-form', BASEPATH.'/categories/saveNew');
	
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
	$form->addCheckbox('show_updated', 1, CAT_FRM_SHOW_UPDATED_LABEL,'',$show_updated, 'frm-checkbox', true);
	$form->addCheckbox('show_user', 1, CAT_FRM_SHOW_USER_LABEL,'',$show_user, 'frm-checkbox');
	$form->emptyLine();
	$form->addCheckbox('image_gallery', 1, CAT_FRM_IMAGE_GALLERY_LABEL,'',$image_gallery);
		$form->addDescription('image_gallery', CAT_FRM_IMAGE_GALLERY_DESC);
	$form->emptyLine();
	$form->addCheckbox('required', 1, REQUIRED,'',$required);
		$form->addDescription('required', CAT_FRM_REQUIRED_DESC);
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
    $form = new Form('catAddItemForm', 'cat-add-item-form', BASEPATH.'/categories/saveNewitem');
	
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
    $form = new Form('catEditForm', 'cat-edit-form', BASEPATH.'/categories/save');
	
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
	$form->addCheckbox('show_updated', 1, CAT_FRM_SHOW_UPDATED_LABEL,'',$show_updated, 'frm-checkbox', true);
	$form->addCheckbox('show_user', 1, CAT_FRM_SHOW_USER_LABEL,'',$show_user, 'frm-checkbox');
	$form->emptyLine();
	$form->addCheckbox('image_gallery', 1, CAT_FRM_IMAGE_GALLERY_LABEL,'',$image_gallery);
		$form->addDescription('image_gallery', CAT_FRM_IMAGE_GALLERY_DESC);
	$form->emptyLine();
	$form->addCheckbox('required', 1, REQUIRED,'',$required);
		$form->addDescription('required', CAT_FRM_REQUIRED_DESC);
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
	
	$form = new Form('catEditItemForm', 'cat-edit-item-form', BASEPATH.'/categories/saveitem');
	
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
    $form = new Form('catDeleteForm', 'cat-delete-form', BASEPATH.'/categories/deleteConfirm');
	
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
    $form = new Form('catDeleteForm', 'cat-delete-form', BASEPATH.'/categories/deleteItemConfirm');
	
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
    require_once(APP_DIR.'/models/ContentModel.php');
	$obj = new ContentModel;
	$rows = $obj->findImages($id,$order_by);
	if($rows){
		$lis = '';
		foreach($rows as $row){
		  $src = BASEPATH.'/images/thumb_'.$row['image_name'];
		  $imgSize = getImageSize($src);
		  $img = Html::elem('img', array('src'=>$src, 'width'=>$imgSize[0], 'height'=>$imgSize[1], 'alt'=>$row['description'], 'style'=>'border:none'));
		  $a = Html::elem('a', array('href'=>BASEPATH.'/images/'.$row['image_name'], 'rel'=>'gallery'.$id, 'class'=>'content-gallery-'.$id, 'title'=>$row['description']), $img);
		  $lis .= Html::elem('li', array('id'=>'image-'.$row['id'], 'class'=>'image-gallery-image'), $a);
		}
		
		$output = Html::elem('ul', array('id'=>'image-gallery-'.$id, 'class'=>'image-gallery'), $lis.'<br class="clearfloat" />');
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
    require_once(APP_DIR.'/models/ContentModel.php');
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