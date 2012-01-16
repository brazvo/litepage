<?php
class BaseController extends Controller {
  
    protected $class;
    
    protected $perm_mach_name;
    
    protected $modulesHtmlContent;
    
    protected function startUp($id)
    {
        $this->user = Application::$logged;
        
        if(isset($this->perm_mach_name)) Application::setPermissions(Application::$logged['role'], $this->perm_mach_name);
        
        //if front page set class
        if(Application::$isFrontPage) $this->addClass ('frontpage');
        
        if(Application::$action == 'add' and !Application::$add) redirect('error/default/no_permision');
		if(Application::$action == 'edit' and !Application::$edit) redirect('error/default/no_permision');
		if(Application::$action == 'delete' and !Application::$delete) redirect('error/default/no_permision');
        
        $result = Application::runAppHandlers($this, $id, Vars::get('POST')->getRaws());
        $this->template->headerModulesHtml = $result['header'];
        $this->template->bodyModulesHtml = $result['body'];
    }
    
    
    protected function shutDown()
    {
        // set body class
        $this->template->bodyclass = $this->getClass();
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
