<?php

class RatingsBaseController extends Controller {
    
    protected function startUp()
    {
        Application::setPermissions(Application::$logged['role'], 'ratingsandviews');
        
        $this->template->bodyclass = 'module_ratingsandviews';
        
        if(Application::$action == 'add' and !Application::$add) redirect('error/default/no_permision');
		if(Application::$action == 'edit' and !Application::$edit) redirect('error/default/no_permision');
		if(Application::$action == 'delete' and !Application::$delete) redirect('error/default/no_permision');
        
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
        //$this->template->CSS->add("app/modules/contactform/css/kontakt.css");
    }
    
}
