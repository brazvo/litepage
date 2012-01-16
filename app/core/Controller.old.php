<?php
/**
 * Project LitePage
 *
 * file: class.Controller.php
 * @author Branislav Zvolenský <zvolensky@mbmartworks.sk>
 * @copyright Copyright (c) 2010, Branislav Zvolenský
 *
 * ==================================================
 * Main contoller abstract class.
 */

abstract class Controller
{
  protected $pageName;
  
  protected $action;
  
  protected $render;
  
  protected $id;
  
  protected $template;
  
  protected $texy;
  
  protected $texyla;
  
  protected $perm_mach_name;
  
  protected $module;
  
  protected $moduleObject;
  
  protected $modulesHtmlContent;
  
  protected $post;

  protected $class;
  
  protected $cacheObj;
  
  public function __construct()
  {
	if($_POST) {
	  if (get_magic_quotes_gpc()) {
	    foreach($_POST as $key => $value){
	      $this->post[$key] = stripslashes($value);
	    }
	    $_POST = $this->post;
	  }
	  else {
	    foreach($_POST as $key => $value){
	      $this->post[$key] = $value;
	    }
	    $_POST = $this->post;
	  }
	}
        
        if(CACHE_PAGES) $this->cacheObj = new Cache();
	
	if(isset($this->perm_mach_name)) $this->getPermisions(Application::$logged['role'], $this->perm_mach_name);
    
	$this->pageName = Application::$pageName;
	$this->action = 'action'.ucfirst(Application::$action);
	$this->render = 'render'.ucfirst(Application::$action);
	$this->id = Application::$id;
	$this->modulesHtmlContent = '';
	
	//check if action is allowed for this type  of user_error except content
	//if(Application::$pageName == 'content'){ 
	if(Application::$action == 'add' and !Application::$add) redirect('error/default/no_permision');
	if(Application::$action == 'edit' and !Application::$edit) redirect('error/default/no_permision');
	if(Application::$action == 'delete' and !Application::$delete) redirect('error/default/no_permision');
	//}
	
	//Setup texy
	$this->texy = new Texy;
	$this->texy->encoding = 'utf-8';
	$this->texy->imageModule->root = BASEPATH.'/images/';
	$this->texy->headingModule->balancing = TexyHeadingModule::FIXED;
	
	//call content module handlers
        //TODO: make automatical detection of using handlers
        $allowedControlers = array('content', 'webform', 'kontakt');
        if(in_array($this->pageName, $allowedControlers)) {
            if(Application::$action == 'add') $this->modulesHandleAdd();
            if(Application::$action == 'edit') $this->modulesHandleEdit();
            if(Application::$action == 'delete') $this->modulesHandleDelete();
            if(Application::$action == 'save') $this->modulesHandleSave();
            if(strtolower(Application::$action) == 'savenew') $this->modulesHandleSaveNew();
            if(Application::$action == 'show') $this->modulesHandleShow();
        }

        //if front page set class
        if(Application::$isFrontPage)            $this->addClass ('frontpage');
	
	$rc = new ReflectionClass($this);
	
	if($rc->hasMethod($this->action)){
	  $action = $this->action;
	  $this->$action($this->id);
	}
	else{
	  $this->render(Application::$action);
	}
  
  }
  
  protected function render($action)
  {
  
    $this->render = 'render'.ucfirst($action);
    
    $rc = new ReflectionClass($this);
	
    if($rc->hasMethod($this->render)){
	  // set view
	  $this->template['view'] = $action;
	  // run method
	  $render = $this->render;
	  $this->$render(Application::$id);

          // set body class
          $this->template['bodyclass'] = $this->getClass();

	  // save global template vars  
	  Application::$tpl_vars = $this->template;
		  
    }
    else{
	  Application::setError("Missing method $this->render!!");
	  if(!DEVELOPMENT){
	    redirect('error/default/404');exit;
	  }
    }
  
  }
  
  protected function getPermisions($role, $mach_id)
  {
    if($role == 'admin'){
		Application::$view = 1;
		Application::$add = 1;
		Application::$edit = 1;
		Application::$delete = 1;
	}
	else{
		$row = db::fetch("SELECT * FROM permisions WHERE cont_mach_name='$mach_id'");
		
		Application::$view = $row[$role.'_view'];
		Application::$add = $row[$role.'_add'];
		Application::$edit = $row[$role.'_edit'];
		Application::$delete = $row[$role.'_delete'];
		
		// check for extra user's permisions
		if(Application::$logged['status']){
			$uid = Application::$logged['userid'];
			//echo $mach_id;
			$row = db::fetch("SELECT * FROM users_permisions WHERE machine_name='$mach_id' AND uid=$uid");
			//var_dump($row);
			if($row){
				Application::$view = $row['view'];
				Application::$add = $row['add'];
				Application::$edit = $row['edit'];
				Application::$delete = $row['delete'];
			}
		}
	}
	
  }
  
  protected function getPermisionForAction($role, $mach_id, $action)
  {
    if($role == 'admin'){
		return 1;
	}
	else{
		$row = db::fetch("SELECT * FROM permisions WHERE cont_mach_name='$mach_id'");
		return $row[$role.'_'.$action];
	}
  }
  
  public function getName()
  {
    return $this->pageName;
  }
  
  protected function modulesHandleAdd()
  {
    // if any modules read content
    if(Application::$contentModulesObjects) {
      foreach(Application::$contentModulesObjects as $moduleObject) {
	$this->modulesHtmlContent .= ( $moduleObject->handleAdd() ? $moduleObject->handleAdd() : '' );
      }
    }
  }
  
  protected function modulesHandleEdit()
  {
    // if any modules read content
    if(Application::$contentModulesObjects) {
      foreach(Application::$contentModulesObjects as $moduleObject) {
	$this->modulesHtmlContent .= ( $moduleObject->handleEdit($this->id) ? $moduleObject->handleEdit($this->id) : '' );
      }
    }
  }
  
  protected function modulesHandleShow()
  {
    // if any modules read content
    if(Application::$contentModulesObjects) {
      foreach(Application::$contentModulesObjects as $moduleObject) {
	$moduleObject->handleShow($this->id);
      }
    }
  }
  
  protected function modulesHandleSave()
  {
    // if any modules read content
    if(Application::$contentModulesObjects) {
      foreach(Application::$contentModulesObjects as $moduleObject) {;
	$moduleObject->handleSave($this->post['id'], $this->post);
      }
    }
  }
  
  protected function modulesHandleSaveNew()
  {
    // if any modules read content
    if(Application::$contentModulesObjects) {
      $obj = new AdmContentModel;
      $newid = $obj->getNewContentId();
      foreach(Application::$contentModulesObjects as $moduleObject) {
	$moduleObject->handleSaveNew($newid, $this->post);
      }
    }
  }
  
  protected function modulesHandleDelete()
  {
    // if any modules read content
    if(Application::$contentModulesObjects) {
      foreach(Application::$contentModulesObjects as $moduleObject) {
	$moduleObject->handleDelete($this->id);
      }
    }
  }

  protected function addClass($class) {
      if(!$this->class) {
          $this->class = $class;
      }
      else {
          $this->class .= " {$class}";
      }
  }

  private function getClass() {
      return $this->class;
  }

}