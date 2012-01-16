<?php

class BaseAdmin extends Controller
{

    // Properties
    protected $class;
    
    protected $perm_mach_name;
    
  
    protected function startUp()
    {
        
        // Check if is user logged
	if(!Application::$logged['status']){
            redirect('login');
	}
        
        if(isset($this->perm_mach_name)) Application::setPermissions(Application::$logged['role'], $this->perm_mach_name);
        
        
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
    
    
    protected function shutDown()
    {
        // set body class
        $this->template->bodyclass = $this->getClass();
    }
  
  // Methods
  
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
    
}