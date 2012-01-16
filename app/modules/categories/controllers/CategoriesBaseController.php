<?php

class CategoriesBaseController extends Controller {
    
    protected $modulesHtmlContent = '';
    
    protected $cid; //content ID
    
    protected function startUp()
    {
        Application::setPermissions(Application::$logged['role'], 'categories');
        
        $this->template->bodyclass = 'module_categories';
        
        $contMod = new ContentModel;
        if( $this->id ) {
            $this->cid = $contMod->getContentId('categories', $this->id);
        }
        else {
            $this->cid = -1;
        }
        
        if(Application::$action == 'add' and !Application::$add) redirect('error/default/no_permision');
	if(Application::$action == 'edit' and !Application::$edit) redirect('error/default/no_permision');
	if(Application::$action == 'delete' and !Application::$delete) redirect('error/default/no_permision');
        
        /*
        //call content module handlers
        $POST = Vars::get('POST');           
        if(Application::$action == 'add') $this->modulesHtmlContent = Application::modulesHandleAdd();
        if(Application::$action == 'edit') $this->modulesHtmlContent = Application::modulesHandleEdit($this->cid);
        if(Application::$action == 'delete') Application::modulesHandleDelete($this->cid);
        if(Application::$action == 'save') Application::modulesHandleSave($POST->cid, $POST->getRaws());
        if(strtolower(Application::$action) == 'savenew') Application::modulesHandleSaveNew( $POST->getRaws() );
        if(Application::$action == 'show') Application::modulesHandleShow($this->cid);
        */ 
    }
    
    
    protected function beforeRender()
    {
        $this->template->CSS->add("app/modules/categories/css/categories.css");
    }
    
}
