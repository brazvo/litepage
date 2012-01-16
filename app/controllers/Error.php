<?php
/**
 *
 *
 *
 */
class Error extends Controller
{
  // PROPERTIES
  private $errors = array('s1' => 'Controller with name: %replace% is missing.','controller_missing' => 'Controller with name: %replace% is missing.',
                          's2' => 'Template file %replace% is missing.','missing_template' => 'Template file %replace% is missing.',
			   1 => YOU_HAVE_NOT_PERMISSION, 'no_permision' => YOU_HAVE_NOT_PERMISSION,
			   2 => CONTENT_NOT_EXIST_FOR_LANG, 'no_lang' => CONTENT_NOT_EXIST_FOR_LANG,
			   403=>NO_PERMISSION_TO_SEE_CONTENT, 'no_view'=>NO_PERMISSION_TO_SEE_CONTENT,
			   404=>CONTENT_NOT_EXISTS, 'no_content'=>CONTENT_NOT_EXISTS);
  
  private $error_code;
  
  private $error_replacement;
  
  // CONSTRUCTOR
  public function __construct(){
  
    parent::__construct();
	
  }

  
  // METHODS
  protected function beforeRender()
  {
      $this->template->bodyclass = 'error';
  }
  
  /******************************************************* RENDERERS ***/
    public function renderShow($id)
    {
        $this->template->setView('default');

        $err = explode(':', Application::$id);

        $this->error_code = $err[0];
        if(isset($err[1])){
            $this->error_replacement = $err[1];
        }
        else{
            $this->error_replacement = '';
        }

        $this->template->title = ERROR_TITLE;
        if(isset($this->errors[$this->error_code])){
            $this->template->content = preg_replace('/%replace%/', $this->error_replacement, $this->errors[$this->error_code]);
        }
        else{
            $this->template->content = UNDEFINED_ERROR;
        }
    }
  
  public function renderDefault($id)
  {
    
	$err = explode(':', Application::$id);
	
        $this->error_code = $err[0];
	if(isset($err[1])){
	  $this->error_replacement = $err[1];
	}
	else{
	  $this->error_replacement = '';
	}
	
        $this->template->title = ERROR_TITLE;
	if(isset($this->errors[$this->error_code])){
	  $this->template->content = preg_replace('/%replace%/', $this->error_replacement, $this->errors[$this->error_code]);
	}
	else{
	  $this->template->content = UNDEFINED_ERROR;
	}
  
  }

}