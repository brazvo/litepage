<?php

class Content extends BaseAdmin
{

  // Properties
  protected $modulesHtmlContent ='';
  
  private $pages;
  
  private $content_type;
  
  private $content_type_id;
  
  private $content_id;
  
  private $content;
  
  private $destination;
  
  // Constructor
  public function __construct()
  { 

	  $GET = Vars::get('GET');
	  $this->destination = isset( $GET->destination ) ? $GET->destination : null;
        
	if(Application::$pageName == 'content' && Application::$id && Application::$action != 'add' && Application::$action != 'list'){
		$obj = new AdmContentModel;
		$cont_mach_name = $obj->getContentMachineName(Application::$id);
        $cont_id = $obj->getContentId(Application::$id);
		$this->perm_mach_name = $cont_mach_name;
                
                // if is module try to redirect to module
                $saModules = Environment::get('standaloneModules');
				$notarray = array('events', 'tours');
                if(in_array($cont_mach_name, $saModules) && !in_array($cont_mach_name, $notarray) ) {
                    $GET = Vars::get('GET');
                    $action = Environment::getAction();
                    redirect("{$cont_mach_name}/administrate/{$action}/{$cont_id}" . (isset($GET->destination) ? "?destination=".$GET->destination : "") );
                }
	}
	else{
		$this->perm_mach_name = 'content';
		if(Application::$action === 'add') {
			$saModules = Environment::get('standaloneModules');
			if( in_array( Application::$id, $saModules) ) $this->perm_mach_name = Application::$id;
		}
	}
        parent::__construct();

  
  }
  
    protected function startUp() {
        
        parent::startUp();
          
        // contModules insert into $this->modulesHtmlContent
        // scripts alowable when content controller is called
        // standard handlers are show, edit, add, save, save, savenew, delete
        // So it means that you can generate
        // different html code for each handler
        
        $POST = Vars::get('POST');
        $cid = $this->id;
        
        // if action is saveNew check for new id
        if( strtolower(Application::$action) == 'add') {
            $conMod = new ContentModel;
            $cid = $conMod->getNewContentId();
        }
        
        $this->modulesHtmlContent = Application::runContentHandlers($this, $cid, $POST->getRaws());
                

    }
  
    function actionEdit($id)
    {
        if(Application::$edit){
            if(!$this->id){
                redirect('admin/content/list', NOTHING_TO_EDIT);
            }
            else{
                $obj = new AdmContentModel;
                $this->content = $result = $obj->find($id);
                if($result) {
                    $this->content_type = $result['content_type_machine_name'];
                    $this->content_type_id = $result['content_type_id'];
                    $this->content_id = $result['content_id'];
                }
                else {
                    redirect('admin/content/list', NO_CONTENT);
                }
            }
        }
        else{
            redirect('error/show/403');
        }
    }
    
    function actionAjaxImageUpload($id)
    {
        $obj = new AdmContentModel();
        $element = Vars::get('GET')->element;
        $error = $obj->ajaxImageUpload($id, $element);
        
        if(!$error) {
            echo "{\"error\": \"\"}";
        }
        else {
            echo "{\"error\": \"{$error}\"}";
        }
        exit;
    }
    
    function actionAjaxFileUpload($id)
    {
        $obj = new AdmContentModel();
        $element = Vars::get('GET')->element;
        $error = $obj->ajaxFileUpload($id, $element);
        
        if(!$error) {
            echo "{\"error\": \"\"}";
        }
        else {
            echo "{\"error\": \"{$error}\"}";
        }
        exit;
    }
    
  
  function actionDelete()
  {
    if(Application::$delete){
		if(!$this->id){
		  Application::setError(NOTHING_TO_DELETE);
		  $this->template->setView('list');
		}
		else{
		  
		  $obj = new AdmContentModel;
		  
		  $result = $obj->delete($this->id);
		  if($result['status']){
		    redirect('admin/content/deleteCont/'.$this->id."?destination=$this->destination");
		  }
		  else{
			
			$url = $this->destination ? $this->destination : 'admin/content/list';
			
		    redirect($url, $result['message']);
		  }
		  
		}
	}
	else{
	    redirect('error/show/1');
	}
  }
  
  function actionDeleteCont()
  {
    if(Application::$delete){
		if(!$this->id){
		  Application::setError(NOTHING_TO_DELETE);
		  $this->template->setView('list');
		}
		else{
		  
		  $obj = new AdmContentModel;
		  
		  $result = $obj->deleteCont($this->id);
		  $url = $this->destination ? $this->destination : 'admin/content/list';
		  redirect($url, $result['message']);
		  
		}
	}
	else{
	    redirect('error/show/1');
	}
  }
  
  function actionAjaxImageDelete($id)
  {
      $image = Vars::get('GET')->image;
      
      $obj = new AdmContentModel();
      $result = $obj->imageDelete($id, $image);
      
      header('Cache-Control: no-cache, must-revalidate');
      header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
      header('Content-type: application/json');

      if($result) { 
          echo "{\"status\": \"ok\"}";
      }
      else {
          echo "{\"status\": \"notok\"}";
      }
      exit;
  }
  
  function actionAjaxFileDelete($id)
  {
      $file = Vars::get('GET')->file;
      
      $obj = new AdmContentModel();
      $result = $obj->fileDelete($id, $file);
      
      header('Cache-Control: no-cache, must-revalidate');
      header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
      header('Content-type: application/json');

      if($result) { 
          echo "{\"status\": \"ok\"}";
      }
      else {
          echo "{\"status\": \"notok\"}";
      }
      exit;
  }
  
  function actionFilter()
  {
	
	$obj = new AdmContentModel;
	
	$obj->setFilter($_POST);
	
	redirect('admin/content/list');
	
  }
  
  function actionFilterReset()
  {
	
	$obj = new AdmContentModel;
	
	$obj->resetFilter($_POST);
	
	redirect('admin/content/list');
	
  }
  
  // Methods
  function renderDefault()
  {
	
	$items = new AdmContentModel;
	
	$result = $items->findItems('admin/'.Application::$pageName);
	
	$output = '';
	foreach($result as $item){
	  $output .= '<div><a href="'.Application::link($item['path']).'">'.$item['title'].'</a></div>';
	}
	
        $this->template->title = CONTENT;
	$this->template->content = $output;
  
  }
  
  function renderList($id=null)
  {
	if(!$id) $id = 1;
	$id = (int)$id;
	if(!$id) redirect('admin/content/list');
	// Set last path request
	
	$items = new AdmContentModel;
	
	$result = $items->findContents();
	
	$this->getPages($result);

	if($result){
		$this->template->title = CONTENT_LIST;
		$this->template->nocontent = null;
		$this->template->content = $this->pages[$id];
		$this->template->filter = $this->createFilterForm();
		$this->template->paginator = $this->createPaginator($id);
	}
	else{
		$this->template->title = CONTENT_LIST;
		$this->template->nocontent = NO_CONTENT;
		$this->template->content = $result;
		$this->template->filter = $this->createFilterForm();
	}
  
  }
  
  function renderMyContent($id)
  {

	$id = (int)$id;
	if(!$id) $id = 1;
	
	$items = new AdmContentModel;
	
	$result = $items->findUsersContents();
	
	$this->getPages($result);
	$this->template->setView('list');
	if($result){
		$this->template->title = CONTENT_LIST;
		$this->template->nocontent = null;
		$this->template->content = $this->pages[$id];
		$this->template->filter = $this->createFilterForm();
		$this->template->paginator = $this->createPaginator($id);
	}
	else{
		$this->template->title = CONTENT_LIST;
		$this->template->nocontent = NO_CONTENT;
		$this->template->content = $result;
		$this->template->filter = $this->createFilterForm();
	}
  
  }
  
  function renderEdit($id){
		

	  $this->template->title = $this->content['content_title'];
	  $this->template->editform = $this->createControlEditForm(); //$this['editForm'];
	  
  
  }
  
  function renderAdd()
  {
        if(Application::$add){
		if(!$this->id){
		  $this->template->setView('addtype');
                  $this->renderAddtype();
		}
		else{
		  $this->template->setView('addform');
                  $this->renderAddform();
		}
	}
	else{
	    redirect('error/show/403');
	}
	
  }
  
  function renderAddtype()
  {

        $obj = new AdmContentTypesModel;
	
	$result = $obj->findContentTypes();
	
	$this->template->title = ADD_NEW_CONTENT;
	$this->template->types = $result;
  
  }
  
    function renderAddform()
    {
        $obj = new AdmContentTypesModel;

        $result = $obj->find($this->id);
        
        $this->content_type = $this->id;
        $this->content_type_id = $result['id'];

        if($_POST){
              $values = $_POST;
              if($_FILES){
                $values = $values + $_FILES;
              }
            }
            else{
              $values = null;
            }

            $this->template->title = NEW_CONTENT.' - '.$result['name'];
            $this->template->editform = $this['addForm'];

    }
  
  
  public function createControlEditForm()
  {
	
	$obj = new AdmContentModel;
	
	$formitems = $obj->getFormFields($this->content_type_id);

	//$values ? $values += $obj->getContentValues($this->id) : $values = $obj->getContentValues($this->id);
        
        $values = $obj->getContentValues($this->id);
        unset($values['id']);
	
	$form = new AppForm('contentEdit');

	$form->addHidden('id')->setValue($this->id);
	$form->addHidden('content_type_id')->setValue($this->content_type_id);
	
	isset($values['lang_code']) ? $lang_id = $values['lang_code'] : $lang_id = $values['lang'];
	
	// add language selector
	//$form->insertContent(Application::createLangInput($lang_id));
        Application::createLangInput2($form);
        
	
	// if module categories is active
    $aSaModules = Environment::get( 'standaloneModules' );
	if(in_array('categories', $aSaModules)){
		$cats = Application::createCategoriesInput($this->content_type, $this->id);
                if($cats){
			$form->addContent($cats);
		}
	}
	
	foreach($formitems as $formitem){
	
	  // if values are set incomming value or set default value
	  if($values){
	    if(isset($values[$formitem['frm_name']])) $value = $values[$formitem['frm_name']];
			else $value = '';
	  }
	  else{
	    $value = contentApi( $formitem['default'] );
	  }
	  if($formitem['attributes']) {
	    $attrs = @unserialize($formitem['attributes']);
	    // BACKWARD COMPATIBILITY
	    if(!is_array($attrs)) $attrs = $this->parseAttributes($formitem['attributes']);
	    foreach($attrs as $key => $val){
	      $$key = contentApi( $val );
	    }
	  }

	  $value = stripslashes($value);

	  if($formitem['type'] == 'text'){
	    $el = $form->addText($formitem['frm_name'], $formitem['label'], $size, $maxlength);
		if( $formitem['frm_name'] === 'path_alias' ) {
			$el->addRule(AppForm::REGEX, "URL alias môže obsahovať iba písmená (bez diakritiky), číslice, podtržník a pomlčku.", '/^[a-zA-Z0-9_-]+$/');
		}
	  }
	  if($formitem['type'] == 'textarea'){
	    //$el = $form->addTextarea($formitem['frm_name'], $formitem['label'], $value, 'frm-textarea', $cols, $rows, $wrap);
            $el = $form->addTextarea($formitem['frm_name'], $formitem['label'], $cols, $rows, $wrap);
            if($allowedtags === 'true') {
                $el->allowTags(true);
            }
            else if($allowedtags === 'null') {
                $el->allowTags(false);
            }
            else {
                $el->allowTags($allowedtags);
            }
	  }
	  
	  elseif($formitem['type'] == 'select' or $formitem['type'] == 'radio'){
	    $vals = explode(';', contentApi($formitem['default']) );
		$selected = '';
		foreach($vals as $val){
		  $val = explode('=', $val);
		  if( count($val) > 1 ) {
			  $valandops[$val[0]] = $val[1];
		  }
		  else {
			  $valandops[ preg_replace('/:default/', '', $val[0]) ] = $val[0];
		  }
		}
		foreach($valandops as $key => $val){
		  if(preg_match('/:default/', $val)){
			$val = preg_replace('/:default/', '', $val);
		  }
		  $valops[$key] = $val;
		}
		$action = "add".ucfirst($formitem['type']);
	    $form->$action($formitem['frm_name'], $formitem['label'], $valops);
	  }
	  elseif($formitem['type'] == 'multiselect'){
	    $vals = explode(';', contentApi($formitem['default']) );
		if( isset( $values[ $formitem['frm_name'] ] ) ) {
			//unserialize
			$values[ $formitem['frm_name'] ] = unserialize( stripslashes($values[ $formitem['frm_name'] ]) );
		}
		foreach($vals as $val){
		  $val = explode('=', $val);
		  if( count($val) > 1 ) {
			  $valandops[$val[0]] = $val[1];
		  }
		  else {
			  $valandops[ preg_replace('/:default/', '', $val[0]) ] = $val[0];
		  }
		  
		}
		foreach($valandops as $key => $val){
		  if(preg_match('/:default/', $val)){
			$val = preg_replace('/:default/', '', $val);
		  }
		  $valops[$key] = $val;
		}
		$form->addSelect($formitem['frm_name'], $formitem['label'], $valops, $size, $multiple);
	  }
	  elseif($formitem['type'] == 'checkbox'){
	    $el = $form->addCheckbox($formitem['frm_name'], $formitem['label']);
	  }
	  else if($formitem['type'] == 'datepicker') {
		  $form->addDatepicker($formitem['frm_name'], $formitem['label'], $format, explode(',',$years) );
	  }
	  else if($formitem['type'] == 'timepicker') {
		  $form->addTimepicker($formitem['frm_name'], $formitem['label']);
	  }
	  else if($formitem['type'] == 'hidden') {
		  $form->addHidden($formitem['frm_name']);
	  }
	  
	  if($formitem['required']){
	    $el->addRule(AppForm::FILLED, 'Položka '.$formitem['label'].' je povinná.');
	  }
	  
	  if($formitem['machine_field_type'] == 'image'){
 
	    $images = $obj->getImages($this->id, $order_by);
	    $imgcount = count($images);
	    $output = '';
		
		//WE MUST DECLARE NEW FORM BUT WE JUST RENDER SINGE ITEMS FROM IT WITHOUT FROM INICIALIZATION
		$form2 = new Form('contentEdit', 'content-edit', BASEPATH.'/admin/content/validateform');
		$form2->addHidden('image_content_type_field_id', $formitem['id']);
		if($imgcount){
			$form2->addHidden('saveImages',1);
			$image_ids = '';
			foreach($images as $image){
			  $image_ids .= $image['id'].':';
			  $form2->addHidden($image['id'].'__image_name', $image['image_name']);
			  $form2->addText($image['id'].'__description', '', $image['description']);
			  $form2->addText($image['id'].'__priority', '', $image['priority'], 'frm-text', 2,3);
			  //$form->addHidden($image['id'].'__delete', 0);
			  $form2->addCheckbox($image['id'].'__delete', 1, '', '', false, 'image-delete');
			}
			$form2->addHidden('image_ids', substr($image_ids, 0, -1)); // Add IDs to hidden type and remove last ":"
			
			$output .= "<table cellspacing='0' class='admin-table'>\n
						 <thead>\n
						 <tr>\n
						   <td>".IMAGE."</td>\n
						   <td>".DESCRIPTION."</td>\n
						   <td>".PRIORITY."</td>\n
						   <td>".DELETE."</td>\n
						 </tr>\n
						 </thead>\n
						 <tbody>\n";
			foreach($images as $image){
				$imgSize = getImageSize(BASEPATH.'/images/thumb_'.$image['image_name']);

				$imgWidth = $imgSize[0];
				$imgHeight = $imgSize[1];
				if($imgWidth >= $imgHeight){
					$coef = $imgWidth / 75;
					$icoWidth = 75;
					$icoHeight = round($imgHeight/$coef);
				}
				if($imgWidth < $imgHeight){
					$coef = $imgHeight / 75;
					$icoWidth = round($imgWidth/$coef);
					$icoHeight = 75;
				}
				$output .= '<tr>
							 <td><img id="img'.$image['id'].'" src="'.BASEPATH.'/images/thumb_'.$image['image_name'].'" alt="'.$image['image_name'].'" width="'.$icoWidth.'" height="'.$icoHeight.'" /></td>
							 <td>'.$form2->renderSingle($image['id'].'__image_name').$form2->renderSingle($image['id'].'__description').'</td>
							 <td>'.$form2->renderSingle($image['id'].'__priority').'</td>
							 <td>'.$form2->renderSingle($image['id'].'__delete').'</td>
							</tr>';
			}
			$output .= '</tbody>
						</table>';
			$output .= $form2->renderSingle('image_ids');
		}
		else{
			$form2->addHidden('saveImages', 0);
		}
		$output .= $form2->renderSingle('saveImages');
		$output .= $form2->renderSingle('image_content_type_field_id');
		
		//AND NOW WE PUT THE ELEMENTS FROM GOST FORM INTO EDIT FORM AS INSERTED CONTENT
		//$form->insertContent($output.'<img id="loading" src="images/loading.gif" style="display:none;">');
                $form->addContent($output.'<img id="loading" src="images/loading.gif" style="display:none;">')
				->setId("AjaxImagesWrapper");
		
		if($max_files == 0 or ($imgcount < $max_files)){
		  $form->addHidden('newImage')->setValue(1);
		  $form->addHidden('img_max_file_size')->setValue($max_file_size*1024000);
		  $form->addHidden('preview_size')->setValue($preview_size);
		  $form->addHidden('icon_size')->setValue($icon_size);
		  $form->addHidden('thumb_create')->setValue($thumb_create);
		  $form->addHidden('image_frm_name')->setValue($formitem['frm_name']);
		  $el = $form->addFile($formitem['frm_name'], ADD_IMAGE);
                  $form->addButton('uploadImage', UPLOAD)->setAttribute('onclick', "return ajaxImageUpload({$this->id}, 'formContentEdit', '{$formitem['frm_name']}');");
		}
		else{
		  $form->addHidden('newImage')->setValue(0);
		}
		
	  }
	  
	  if($formitem['machine_field_type'] == 'file'){
	    $files = $obj->getFiles($this->id, $order_by);
	    $filecount = count($files);
	    $output = '';
		
		//WE MUST DECLARE NEW FORM BUT WE JUST RENDER SINGE ITEMS FROM IT WITHOUT FROM INICIALIZATION
		$form2 = new Form('contentEdit', 'content-edit', BASEPATH.'/admin/content/validateform');
		$form2->addHidden('file_content_type_field_id', $formitem['id']);
		if($filecount){
			$form2->addHidden('saveFiles',1);
			$file_ids = '';
			foreach($files as $file){
			  $file_ids .= $file['id'].':';
			  $form2->addHidden($file['id'].'__file_name', $file['file_name']);
			  $form2->addText($file['id'].'__file_description', '', $file['description']);
			  $form2->addText($file['id'].'__file_priority', '', $file['priority'], 'frm-text', 2,3);
			  $form2->addCheckbox($file['id'].'__file_delete', 1, '', '', false, 'file-delete');
			}
			$form2->addHidden('file_ids', substr($file_ids, 0, -1)); // Add IDs to hidden type and remove last ":"
			
			$output .= "<table cellspacing='0' class='admin-table'>\n
						 <thead>\n
						 <tr>\n
						   <td>".FILE."</td>\n
						   <td>".DESCRIPTION."</td>\n
						   <td>".PRIORITY."</td>\n
						   <td>".DELETE."</td>\n
						 </tr>\n
						 </thead>\n
						 <tbody>\n";
			foreach($files as $file){
				$output .= '<tr>
							 <td><a id="file'.$file['id'].'" href="'.BASEPATH.'/files/'.$file['file_name'].'">'.$file['file_name'].'</a></td>
							 <td>'.$form2->renderSingle($file['id'].'__file_name').$form2->renderSingle($file['id'].'__file_description').'</td>
							 <td>'.$form2->renderSingle($file['id'].'__file_priority').'</td>
							 <td>'.$form2->renderSingle($file['id'].'__file_delete').'</td>
							</tr>';
			}
			$output .= '</tbody>
						</table>';
			$output .= $form2->renderSingle('file_ids');
		}
		else{
			$form2->addHidden('saveFiles',0);
		}
		$output .= $form2->renderSingle('saveFiles');
		$output .= $form2->renderSingle('file_content_type_field_id');
		
		//AND NOW WE PUT THE ELEMENTS FROM GOST FORM INTO EDIT FORM AS INSERTED CONTENT
		$form->addContent($output.'<img id="loadingFile" src="images/loading.gif" style="display:none;">')
				->setId("AjaxFilesWrapper");
		
		if($max_files == 0 or ($imgcount < $max_files)){
		  $form->addHidden('newFile', 1);
		  $form->addHidden('file_max_file_size', $max_file_size*1024000);
		  $form->addHidden('file_frm_name', $formitem['frm_name']);
		  $el = $form->addFile($formitem['frm_name'], ADD_FILE);
		  
		  $form->addButton('uploadFile', UPLOAD)->setAttribute('onclick', "return ajaxFileUpload({$this->id}, 'formContentEdit', '{$formitem['frm_name']}');");
		}
		else{
		  $form->addHidden('newFile')->setValue(0);
		}
		
	  }
          
          // add desription
          if($formitem['description'] && isset($el)){
	    $el->addDescription($formitem['description']);
	  }
          unset($el);
	}
	
	// if user has rights, create fanthom form for menu insert
	if(Application::$logged['role'] == 'admin' or Application::$logged['role'] == 'editor'){
		$menuobj = new AdmMenusModel;
		$menus = $menuobj->findAll();
		// get default value
		$row = $menuobj->findItemByContentId('content', $this->id);
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
		$form4 = new Form('contentEdit', 'content-edit', BASEPATH.'/admin/content/validateform');
		$form4->addText('menu_title', MENU_TITLE_LABEL, $menu_title);
			$form4->addDescription('menu_title', MENU_TITLE_DESC);
		$form4->addSelect('menu_items', $selArray, MENU_ITEMS_LABEL, $defaultSelect);
			$form4->addDescription('menu_items', MENU_ITEMS_DESC);
		$form->addContent('<fieldset><legend>'.MENU_SETTINGS.'</legend>'.$form4->renderSingle('menu_title').$form4->renderSingle('menu_items').'</fieldset>');
		
	}
	// insert modules content
	if($this->modulesHtmlContent) $form->addContent($this->modulesHtmlContent);
	
	$form->addHidden('old_path_alias')->setValue($values['path_alias']);
	$form->addSubmit('submit', SAVE_CHANGES);
        
        $form->onSubmit('editFormSubmited', $this);
        
        $form->setDefaultVals($values);
        
        $form->collect();
	
	return $form;
  
  }
  
  
    public function createControlAddForm()
    {
        
        $table = $this->content_type;
        $id = $this->content_type_id;
        
        $obj = new AdmContentModel;
	
	$formitems = $obj->getFormFields($id);
	
	$form = new AppForm('contentEdit');
	
	$form->addHidden('id')->setValue($this->id);
	
	// add language selector
	$form->addContent(Application::createLangInput('none'));
	
	// if module categories is active
	$aSaModules = Environment::get( 'standaloneModules' );
	if(in_array('categories', $aSaModules)){
		if($cats = Application::createCategoriesInput($table)){
			$form->addContent($cats);
		}
	}
	
	foreach($formitems as $formitem){
	  
	  $type = $formitem['type'];
	  $machine_type = $formitem['machine_field_type'];

	  $value = contentApi( $formitem['default'] );
	
	  if($formitem['attributes']) {
	    $attrs = @unserialize($formitem['attributes']);
	    
	    //backward compatibylity
	    if(!is_array($attrs)) $attrs = $this->parseAttributes($formitem['attributes']);
	    
	    foreach($attrs as $key => $val){
		  $$key = contentApi( $val );
	    }
	  }
	  
	  $action = 'add'.ucfirst($formitem['type']);
	  
	  $label = $formitem['label'];
	  
	  if($type == 'text'){
	    $el = $form->$action($formitem['frm_name'], $label, $size, $maxlength)->setValue($value);
		if( $formitem['frm_name'] === 'path_alias' ) {
			$el->addRule(AppForm::REGEX, "URL alias môže obsahovať iba písmená (bez diakritiky), číslice, podtržník a pomlčku.", '/^[a-zA-Z0-9_-]+$/');
		}
	  }
	  elseif($type == 'textarea'){
			$form->$action($formitem['frm_name'], $label, $cols, $rows, $wrap)->setValue($value);
			if($allowedtags === 'true') {
                $form->element[$formitem['frm_name']]->allowTags(true);
            }
            else if($allowedtags === 'null') {
                $form->element[$formitem['frm_name']]->allowTags(false);
            }
            else {
                $form->element[$formitem['frm_name']]->allowTags($allowedtags);
            }
	  }
	  elseif($type == 'select' or $type == 'radio'){
	    $vals = explode(';', $value );
		$selected = '';
		foreach($vals as $val){
		  $val = explode('=', $val);
		  if( count($val) > 1 ) {
			  $valandops[$val[0]] = $val[1];
		  }
		  else {
			  $valandops[ preg_replace('/:default/', '', $val[0]) ] = $val[0];
		  }
		  
		}
		foreach($valandops as $key => $val){
		  if(preg_match('/:default/', $val)){
			$val = preg_replace('/:default/', '', $val);
		    $selected = $key;
		  }
		  $valops[$key] = $val;
		}
		$form->$action($formitem['frm_name'], $label, $valops)->setValue($selected);
	  }
	  elseif($type == 'multiselect'){
	    $vals = explode(';', $value );
		$selected = '';
		foreach($vals as $val){
		  $val = explode('=', $val);
		  if( count($val) > 1 ) {
			  $valandops[$val[0]] = $val[1];
		  }
		  else {
			  $valandops[ preg_replace('/:default/', '', $val[0]) ] = $val[0];
		  }
		  
		}
		foreach($valandops as $key => $val){
		  if(preg_match('/:default/', $val)){
			$val = preg_replace('/:default/', '', $val);
		    $selected[] = $key;
		  }
		  $valops[$key] = $val;
		}
		$form->addSelect($formitem['frm_name'], $label, $valops, $size, $multiple)->setValue($selected);
	  }
	  elseif($type == 'checkbox'){
	    $form->$action($formitem['frm_name'], $label)->setValue($checked);
	  }
	  elseif($machine_type == 'image'){
		/*
	    $form->addHidden('image_machine_type')->setValue($machine_type);
	    $form->addHidden('image_frm_name')->setValue($formitem['frm_name']);
		$form->addHidden('img_max_file_size')->setValue($max_file_size*1024000);
		$form->addHidden('preview_size')->setValue($preview_size);
		$form->addHidden('icon_size')->setValue($icon_size);
		$form->addHidden('thumb_create')->setValue($thumb_create);
        $form->$action($formitem['frm_name'],$label); 
		 */
		 $form->addContent( Html::elem('p')->setCont('Súbory obrázkov môžu byť pridávané až pri editácii tohto obsahu. Po uložení obsahu zvoľte možnosť editovať.'), $formitem['frm_name'] );
	  }
	  elseif($machine_type == 'file'){
		/*
	    $form->addHidden('file_machine_type')->setValue($machine_type);
	    $form->addHidden('file_frm_name')->setValue($formitem['frm_name']);
	    $form->addHidden('file_max_file_size')->setValue($max_file_size*1024000);
		$form->$action($formitem['frm_name'], $label); 
		 */
		$form->addContent( Html::elem('p')->setCont('Súbory môžu byť pridávané až pri editácii tohto obsahu. Po uložení obsahu zvoľte možnosť editovať.'), $formitem['frm_name'] );
	  }
	  else if($type == 'datepicker') {
		  $form->addDatepicker($formitem['frm_name'], $label, $format, explode(',',$years) );
	  }
	  else if($type == 'timepicker') {
		  $form->addTimepicker($formitem['frm_name'], $label);
	  }
	  else if($type == 'hidden') {
		  $form->addHidden($formitem['frm_name'])->setValue($value);
	  }
	  
	  if($formitem['description']){
	    $form->element[$formitem['frm_name']]->addDescription($formitem['description']);
	  }
	  if($formitem['required']){
            $form->element[$formitem['frm_name']]->addRule(AppForm::FILLED, ITEM.$formitem['label'].IS_REQUIRED);
	  }
	
	}
	
	// if user has rights, create fanthom form for menu insert
	if(Application::$logged['role'] == 'admin' or Application::$logged['role'] == 'editor'){
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
		$form4 = new Form('contentEdit', 'content-edit', BASEPATH.'/admin/content/validateform');
		$form4->addText('menu_title', MENU_TITLE_LABEL, $menu_title);
			$form4->addDescription('menu_title', MENU_TITLE_DESC);
		$form4->addSelect('menu_items', $selArray, MENU_ITEMS_LABEL, $defaultSelect);
			$form4->addDescription('menu_items', MENU_ITEMS_DESC);
		$form->addContent('<fieldset><legend>'.MENU_SETTINGS.'</legend>'.$form4->renderSingle('menu_title').$form4->renderSingle('menu_items').'</fieldset>');
		
	}
        
        // insert modules content
	if($this->modulesHtmlContent) $form->addContent($this->modulesHtmlContent);
	
	$form->addSubmit('submit', SAVE);
        
        $form->onSubmit('appFormSubmitted', $this);
        
        $form->collect();
	
	return $form->render();
  
  }
  
  private function createFilterForm()
  {
	$obj = new AdmContentModel;
	
	$userid = Application::$logged['userid'];
	$content_types = $obj->getContentTypes();
	$categories = $obj->getCategories();
	$languages = $obj->getLanguages();
	
	if($content_types) $content_types = array(0=>'-- '.FILTER_CHOOSE_CONT_TYPE.' --')+$content_types;
	if($categories) $categories = array(0=>'-- '.FILTER_CHOOSE_CATEGORY.' --')+$categories;
	if($languages) $languages = array(0=>'-- '.FILTER_CHOOSE_LANGUAGE.' --', 'none'=>'< '.FILTER_WITHOUT_LANGUAGE.' >')+$languages;
	
	$form = new Form('filter', 'filter', BASEPATH.'/admin/content/filter');
	
	$filter = $obj->getFilterValues($userid);
	$values = $filter;
	if(!$values) $values = $form->getZeroValues('filter');
	foreach($values as $key => $val){
		$$key = $val;
	}
	
	if($content_types) $form->addSelect('content_type', $content_types, '', $content_type);
	if($categories) $form->addSelect('category', $categories, '', $category);
	if($languages) $form->addSelect('language', $languages, '', $language);
	$form->addHidden('uid', $userid);
	$form->addSubmit('applyfilter', 'Filter');
	
	$filterForm = $form->render();
	
	if($filter){
		$form2 = new Form('reset', 'reset', BASEPATH.'/admin/content/filterReset');
		$form2->addHidden('uid', $userid);
		$form2->addSubmit('resetfilter', 'Reset filter');
		$resetForm = $form2->render();
	}
	else{
		$resetForm = '';
	}
	$title = Html::elem('h3', null, 'Filter');
	return Html::elem('fieldset', array('id'=>'filter-form'), $title . $filterForm . $resetForm);
	
  }
  
    ////////////////////////////////////////////// FORMS CALLBACKS
    function editFormSubmited(AppForm $form)
    {

        if( $form->isValid() ){
            // DO IF VALID
            $obj = new AdmContentModel;

                $cntid = $this->id;
                
                $values = $form->getValues();

                $result = $obj->saveContent($values);

                if($result){

                        if($_FILES && isset($_POST['image_frm_name'])){
                                if(isset($_FILES[$_POST['image_frm_name']]['name']) && $_FILES[$_POST['image_frm_name']]['name'] != ''){
                                  $up_res = $obj->uploadImage($_FILES, $_POST);
                                  if($up_res['status']){
                                        redirect("admin/content/edit/$cntid", FILE_WAS_UPLOADED_AND_CHANGES_WERE_SAVED);
                                        exit;
                                  }
                                  else{
                                        redirect("admin/content/edit/$cntid", $up_res['error']);
                                        exit;
                                  }
                                }
                        }

                        if($_FILES && isset($_POST['file_frm_name'])){
                                if(isset($_FILES[$_POST['file_frm_name']]['name']) && $_FILES[$_POST['file_frm_name']]['name'] != ''){
                                  $up_res = $obj->uplFile($_FILES, $_POST);
                                  if($up_res['status']){
                                        redirect("admin/content/edit/$cntid", FILE_WAS_UPLOADED_AND_CHANGES_WERE_SAVED);
                                        exit;
                                  }
                                  else{
                                        redirect("admin/content/edit/$cntid", $up_res['error']);
                                        exit;
                                  }
                                }
                        }

                        unset($_SESSION['destination']);

                        // clean cache if exists
                        if($this->cacheObj instanceof Cache) {
                            $key = sha1("content/show#{$cntid}");
                            $this->cacheObj->clean($key);
                        }

                        redirect(Application::$pathRequest, CHANGES_WERE_SAVED);
                        exit;
            }
            else{
                $form->setDefaultVals();
            }

        }
        else{

            $form->setDefaultVals();

        }

    }
    
    
    public function appFormSubmitted(AppForm $form)
    {

        if( $form->isValid() ){

            $values = $form->getValues();
            
            $obj = new AdmContentModel;
            $result = $obj->saveNewContent($values);

            if($result){
				$GET = Vars::get('GET');
				$url = isset($GET->destination) ? $GET->destination : 'admin/content/list';
                redirect($url, CONTENT_WAS_SAVED);
            }
            else{
                $form->setDefaultVals();
            }

        }
        else{

            $form->setDefaultVals();

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
  
  private function checkRequired($label, $required)
  {
  
    if($required){
	  return $label.' <sup><span class="required">(*)</span></sup>';
	}
	else{
	  return $label;
	}
  
  }
  
  private function createContentTypesForm($values)
  {
  
    $form = new Form('contentTypesForm');
  
  }
  
  private function getPages($records)
  {
	
	if($records){
	  $pageidx = 1;
	  $idx = 1;
	  $lastidx = 20;
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
		  
		  $pagesElems .= Html::elem('a', array('href'=>BASEPATH.'/admin/content/list/1', 'class'=>'paginator-page-link first'), "&#9668;&nbsp;".PAGINATOR_FIRST."&nbsp;").'|';
		  
		  for($i = $startFrom; $i <= $countTo; $i++)
		  {

			if($pgNumber == $i){
			  $pagesElems .= Html::elem('span', array('class'=>'paginator-page-active'), "&nbsp;$i&nbsp;").'|';
			}
			else{
			  $pagesElems .= Html::elem('a', array('href'=>BASEPATH.'/admin/content/list/'.$i, 'class'=>'paginator-page-link'), "&nbsp;$i&nbsp;").'|';
			}
		  
		  }
		  
		  $pagesElems .= Html::elem('a', array('href'=>BASEPATH.'/admin/content/list/'.$lastPage, 'class'=>'paginator-page-link last'), "&nbsp;".PAGINATOR_LAST."&nbsp;&#9658;");
		  
		  return Html::elem('div', array('class'=>'paginator'), PAGINATOR_PAGE.': '.$pagesElems);
		}
		else{
		  return '';
		}
	
  }
  

}