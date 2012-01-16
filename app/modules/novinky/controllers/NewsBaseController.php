<?php

class NewsBaseController extends Controller {
    
    
    protected function startUp()
    {
        Application::setPermissions(Application::$logged['role'], 'novinky');
        
        $this->template->bodyclass = 'module_novinky';
        
        if(Application::$action == 'add' and !Application::$add) redirect('error/default/no_permision');
	if(Application::$action == 'edit' and !Application::$edit) redirect('error/default/no_permision');
	if(Application::$action == 'delete' and !Application::$delete) redirect('error/default/no_permision');
        
        /*
        //call content module handlers
        //TODO: make automatical detection of using handlers
        $allowedControlers = array('content', 'webform', 'kontakt');
        if(in_array($this->name, $allowedControlers)) {
            if(Application::$action == 'add') $this->modulesHandleAdd();
            if(Application::$action == 'edit') $this->modulesHandleEdit();
            if(Application::$action == 'delete') $this->modulesHandleDelete();
            if(Application::$action == 'save') $this->modulesHandleSave();
            if(strtolower(Application::$action) == 'savenew') $this->modulesHandleSaveNew();
            if(Application::$action == 'show') $this->modulesHandleShow();
        }
        */
         
    }
    
    
}
