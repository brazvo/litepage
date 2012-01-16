<?php

class Admin extends BaseAdmin
{

  // Properties
  
  // Constructor
  public function __construct()
  {
        $this->perm_mach_name = 'content';
        parent::__construct();

  }
  
  // Methods
  function actionLogout()
  {
        $log = new LoginModel;
	
	$result = $log->logout();
	
	if($result){
            redirect();
	}
	else{
            Application::setError('Pri odhlasovaní došlo k chybe.');
            $this->render('default');
	}
  
  }
  
  function renderDefault()
  {
        switch(Application::$logged['role']){
		case 'admin':
		    $menu = 'administration';
			break;
		case 'editor':
		    $menu = 'editors_menu';
			break;
		case 'user':
		    $menu = 'users_menu';
			break;
	}
  	$this->template->title = 'Administrácia';
	$this->template->content = block::getMenu($menu);
  
  }

}