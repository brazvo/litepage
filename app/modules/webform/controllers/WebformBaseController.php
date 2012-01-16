<?php

class WebformBaseController extends Controller {
    
    protected $modulesHtmlContent = '';
    
    protected $cid; //content ID
    
    protected function startUp()
    {
        Application::setPermissions(Application::$logged['role'], 'webform');
        
        $this->template->bodyclass = 'module_webform';
        
        $contMod = new ContentModel;
        if( $this->id ) {
            $this->cid = $contMod->getContentId('webform', $this->id);
        }
        else {
            $this->cid = -1;
        }
        
        if(Application::$action == 'add' and !Application::$add) redirect('error/default/no_permision');
	if(Application::$action == 'edit' and !Application::$edit) redirect('error/default/no_permision');
	if(Application::$action == 'delete' and !Application::$delete) redirect('error/default/no_permision');
        
        // contModules insert into $this->modulesHtmlContent
        // scripts alowable when content controller is called
        // standard handlers are show, edit, add, save, save, savenew, delete
        // So it means that you can generate
        // different html code for each handler
        
        $POST = Vars::get('POST');
        $cid = isset($POST->cid) ? $POST->cid : $this->cid;
        
        // if action is saveNew check for new id
        if( strtolower(Application::$action) == 'savenew') {
            $conMod = new ContentModel;
            $cid = $conMod->getNewContentId();
        }
        
        $this->modulesHtmlContent = Application::runContentHandlers($this, $cid, $POST->getRaws());
        
        // appModules insert into template->headerModulesHtml and template->bodyModulesHtml
        // scripts alowable in whole application when the handlers return the code
        // standard handlers are show, edit, add, So it means that you can generate
        // different html code for each handler
        // the result has to be accessable in $result['header'] and $result['body']
        
        $result = Application::runAppHandlers($this, $this->id, Vars::get('POST')->getRaws());
        $this->template->headerModulesHtml = $result['header'];
        $this->template->bodyModulesHtml = $result['body'];
         
    }
    
    
    protected function beforeRender()
    {
        $this->template->CSS->add("app/modules/webform/css/webform.css");
    }
    
}
