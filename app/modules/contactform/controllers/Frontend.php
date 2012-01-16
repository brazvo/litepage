<?php
class Frontend extends ContactFormBaseController
{

  // Properties
  
  // Constructor
  public function __construct()
  {
    $this->perm_mach_name = 'contactform'; //
	$this->runSettings();
	// only action show may be used by unlogged user
	if(!Application::$logged['status']){
	  if(Application::$action != 'show'){
	    redirect('error/show/403');
	  }
	}
    parent::__construct();

  }
  
  /////////////////////////////////////////// ACTIONS
  
    function actionSend()
    {

        if(Form::$isvalid){
            $obj = new AdmKontaktModel;
            $result = $obj->send($_POST);
            if($result){
                $redirect = $_SESSION['destination'];
                $_SESSION['destination'] = null;
                redirect($redirect, KONTAKT_ACT_SEND_MESSAGE_SENT);
            }
            else{
                Application::$id = $_POST['contact_frm_id'];
                Application::setError(KONTAKT_ACT_SEND_MESSAGE_SENT_ERROR);
                $this->setRender('show');
            }
        }
        else{
            Application::$id = $_POST['contact_frm_id'];
            $this->setRender('show');
        }
    }

  /***************************************** RENDERERS ***/
  function renderShow($id)
  {
	
	if(Application::$view){
		if(isset($_REQUEST['q'])) $destination = $_REQUEST['q'];
			else $destination = '';
		
		$obj = new AdmKontaktModel;
		$row = $obj->find($id);
		
		//check if language only content
		if(!$row['lang']){
			//do nothing
		}
		elseif($row['lang'] && $row['lang'] == 'none'){
			//do nothing
		}
		elseif($row['lang'] && $row['lang'] != Application::$language['code']){
			redirect('error/show/2');exit;
		}
		
		
		$settings = $obj->getFormSettings($id);
		$body = $this->texy->process($row['body']);
		$form = $this->createContactForm($id, $destination);
		if($settings['hide_on_load']){
		  $formlink = Html::elem('div', array('class'=>'cf-show-lnk'), '[ ' . KONTAKT_RND_SHOW_OPEN_FORM . ' &#9660; ]');
		}
		else{
		  $formlink = '';
		}
		if($settings['form_after_text']){
		  $content = $body . $formlink . $form;
		}
		else{
		  $content = $formlink . $form . $body;
		}
		$this->template->title = $row['title'];
		$this->template->content = $content;
		$this->template->hide = $settings['hide_on_load'];
		$this->template->destination = $destination;
	}
	else{
	    redirect('error/show/404');
	}
  }
  
  /***************************************** FACTORIES ***/
  private function createContactForm($id, $destination)
  {
    $form = new Form('contact', 'contact-form', BASEPATH.'/contactform/frontend/send');
	
	$cont_frm_id = $id;
	
    $obj = new AdmKontaktModel;
	$settings = $obj->getFormSettings($id);
	$row = $obj->find($id);
	$form_title = $row['form_title'];
	foreach($settings as $key => $val){
	  $$key = $val;
	}
	
	if($_POST){
	  foreach($_POST as $key => $val){
	    $$key = $val;
	  }
	}
	else{
	  $values = $form->getZeroValues('contact_messages');
	  foreach($values as $key => $val){
	    $$key = $val;
	  }
	  $contact_frm_id = $cont_frm_id;
	}

	$form->addText('name', KONTAKT_FRM_NAME_LABEL, $name);
	    $form->addRule('name', Form::FILLED, KONTAKT_FRM_NAME_RULE);
	if($company_fld){
	    $form->addText('company', KONTAKT_FRM_COMPANY_LABEL, $company);
		if($company_fld_req){
			$form->addRule('company', Form::FILLED, KONTAKT_FRM_COMPANY_RULE);
		}
	}
	else{
		$form->addHidden('company', $company);
	}
	if($address_fld){
	    $form->addTextarea('address', KONTAKT_FRM_ADDRESS_LABEL, $address, 'frm-textarea', 30, 3);
		if($address_fld_req){
			$form->addRule('address', Form::FILLED, KONTAKT_FRM_ADDRESS_RULE);
		}
	}
	else{
		$form->addHidden('address', $address);
	}
	if($phone_fld){
	    $form->addText('phone', KONTAKT_FRM_PHONE_LABEL, $phone);
		if($phone_fld_req){
			$form->addRule('phone', Form::FILLED, KONTAKT_FRM_PHONE_RULE);
		}
	}
	else{
		$form->addHidden('phone', $phone);
	}
	if($email_fld){
	    $form->addText('email', 'Email', $email);
			$form->addRule('email', Form::EMAIL, KONTAKT_FRM_EMAIL_RULE_EMAIL);
		if($email_fld_req){
			$form->addRule('email', Form::FILLED, KONTAKT_FRM_EMAIL_RULE_FILLED);
		}
	}
	else{
		$form->addHidden('email', $email);
	}
	if($subject_fld){
	    $form->addText('subject', KONTAKT_FRM_SUBJECT_LABEL, $subject);
		if($subject_fld_req){
			$form->addRule('subject', Form::FILLED, KONTAKT_FRM_SUBJECT_RULE);
		}
	}
	else{
		$form->addHidden('subject', $subject);
	}
	$form->addTextarea('message', KONTAKT_FRM_MESSAGE_LABEL, $message, 'cf-form-messagebox');
		$form->addRule('message', Form::FILLED, KONTAKT_FRM_MESSAGE_RULE);
	$form->addCaptcha('sec_code', KONTAKT_FRM_CAPTCHA_LABEL);
		$form->addRule('sec_code', Form::CAPTCHA, KONTAKT_FRM_CAPTCHA_RULE);
	$form->addHidden('contact_frm_id', $contact_frm_id);
	$form->addHidden('destination', $destination);
	
	$form->emptyLine();
	$form->addSubmit('send', KONTAKT_FRM_SEND_LABEL);
	$frm = $form->render('table');
	
	if($form_title){
	  $header = Html::elem('h3', array('class'=>'cf-form-title title'),$form_title);
	}
	else{
	  $header = '';
	}
	
	$output = Html::elem('div', array('id'=>'cf-form-'.$contact_frm_id, 'class'=>'cf-form-container'), $header.$frm);
	return $output;
	
  }
  
  /*************************************** OTHER METHODS ***/
  
  private function runSettings()
  {
    $obj = new AdmKontaktModel;
  
    $rows = $obj->getSettings();
  
    foreach($rows as $row){
      define($row['constant'], $row['value']);
    }
	
	if(AUTO_CLEAN){
	  $clean_date = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m")  , date("d")-DELETE_OLDER_THEN, date("Y")));
	  db::exec("DELETE FROM contact_messages WHERE datetime < '$clean_date'");
	}
  }
  
}
