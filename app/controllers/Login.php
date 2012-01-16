<?php

class Login extends Controller
{

  // Properties
  private $exchange;
  
  // Constructor
  public function __construct()
  {
  
    parent::__construct();
  
  }
  
  protected function beforeRender()
  {
      $this->template->bodyclass = 'login';
  }
  
  // Methods
  function actionCheck()
  {
        $result = $this->checkForm($_POST);
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Content-type: application/json');
		echo "{result:\"$result\"}";exit;
	if($result == 'valid'){
	  // DO IF IS Login Form VALID
	  $login = new LoginModel;
	  
	  $result = $login->validateUser($_POST);
	  
	  if($result){
	    redirect('admin');
	  }
	  else{
	    Application::setError('Zadané meno alebo heslo nebolo správne, skúste znova.');
		
		$this->setRender('show');
	  }
	
	}
	else{
	  $this->exchange['error'] = $result;
	  $this->setRender('show');
	}
  
  }
  
  // Methods
  function actionCheckAjax()
  {

	  // DO IF IS Login Form VALID
	  $login = new LoginModel;
	  
	  $result = $login->validateUser($_POST);
	  
	  header('Cache-Control: no-cache, must-revalidate');
	  header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	  header('Content-type: application/json');
	  
	  if($result){
	    echo "{\"result\":\"$result\"}";exit;
	  }
	  else{
		echo "{\"result\":\"0\"}";exit;
	  }
  
  }
  
  function renderShow()
  {
    if(!Application::$logged['status']){
  	  $this->template->title = 'Login';
	  $this->template->content = 'Zadajte prihlasovacie údaje';
	  if(isset($this->exchange['error'])){
	    $this->template->formError =  $this->exchange['error'];
	  }
	  else{
	    $this->template->formError =  '';
	  }
	
	  $this->template->loginForm = $this->createLoginForm($_POST);
	}
	else{
	  redirect('admin');
	}
  
  }
  
  function renderCheck()
  {
      
  }
  
  private function createLoginForm($post=NULL)
  {
    $GET = Vars::get('GET');
	
	
	if($post){
	  foreach($post as $key => $value){
	    $$key = $value;
	  }
	}
	else{
	  //default values
	  $user = '';
	  $password = '';
	}
	$form = new Form('login', 'login-form', BASEPATH.'/login/check');

	$form->addText('user', 'VID', $user, 'frm-text', 37);
	  $form->addRule('user', Form::FILLED, 'Zadajte VID');
	$form->addPassword('password', 'Heslo', $password, 'frm-text', 37);
	  $form->addRule('password', Form::FILLED, 'Zadajte heslo');
	$form->addCheckbox('stay_logged', 1, 'Zapamätať prihlásenie');
	$form->addSubmit('login', 'Prihlásiť sa');
	if( isset($GET->isajax) ) $form->insertContent ('&nbsp;');
	
	return $form->render();
  
  }
  
  /**
	 * @return bool
	 * checks if form id valid
	 */
	private function checkForm($post)
	{
		
		$validate = new Form();
		$validate->setStyle('background-color', 'white');
		$isvalid = $validate->frmValidate($post);
		if($isvalid){
			return 'valid';
		}
		else{
			return $validate->render();
		}
	
	}

}