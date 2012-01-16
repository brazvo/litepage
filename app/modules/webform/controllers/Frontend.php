<?php
class Frontend extends WebformBaseController
{

  // Properties
  private $pages;
  
  // Constructor
  public function __construct()
  {
    
	$this->runSettings();
	$this->getPages();
	
        parent::__construct();

  }
    
  // Methods
  /***************************************** ACTIONS ***/
  
  function actionSend()
  {
	if(!$_POST) redirect('error/show/404');
	
	if(Form::$isvalid){
	  $obj = new WebformModel;
	  $result = $obj->send($_POST);
	  if($result){
                redirect('webform/frontend/show/'.$_POST['webform_id'], WF_ACTION_SEND_SEND_MESSAGE);
	  }
	  else{
		Application::$id = $_POST['webform_id'];
                Application::setError(WF_ACTION_SEND_SEND_FAILED_MESSAGE);
                $this->setRender('show');
	  }
	}
	else{
	  Application::$id = $_POST['webform_id'];
	  $this->setRender('show');
	}
  }
   
  /***************************************** RENDERERS ***/
  
  function renderShow($id)
  {
    if(Application::$view){
		if(isset($_REQUEST['q'])) $_SESSION['pathRequest'] = $_REQUEST['q'];
		
		$obj = new WebformModel;
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
		
		$body = $this->texy->process($row['body']);
		$form = $this->createWebForm($id);
		if($row['hide_on_load']){
		  $formlink = Html::elem('div', array('class'=>'wf-show-lnk'), '[ '.WF_RENDER_SHOW_OPEN_FORM.' &#9660; ]');
		}
		else{
		  $formlink = '';
		}
		if($row['form_after_text']){
		  $content = $body . $formlink . $form;
		}
		else{
		  $content = $formlink . $form . $body;
		}
		$this->template->title = $row['title'];
		$this->template->content = $content;
		$this->template->hide = $row['hide_on_load'];
	}
	else{
	    redirect('error/show/404');
	}
  }
  
  /***************************************** FACTORIES ***/
  
  private function createWebForm($id)
  {
    $form = new Form('webform-'.$id, 'web-form', Application::link('webform/administrate/send') );
	
	$cont_frm_id = $id;
	
    $obj = new WebformModel;
	$row = $obj->find($id);
	$form_title = $row['form_title'];
	
	$fields = $obj->findAllFields($id);
	
	if($fields){
		
		if($_POST){
		  $values = $_POST;
		}
		else{
		  // get default vals
		  foreach($fields as $field){
			$values[$field['frm_name']] = $field['default'];
		  }
		}
		
		$form->addHidden('id', $id);
		
		//Create fields
		foreach($fields as $field){
			
			// Set value
			if(isset($values[$field['frm_name']])){
				$value = $values[$field['frm_name']];
			}
			else{
				$value = '';
			}
			
			//set attributes
			if($field['attributes']) {
				$attrs = $this->parseAttributes($field['attributes']);
				foreach($attrs as $key => $val){
				  $$key = $val;
				}
			}
			
			$type = $field['type'];
			
			$machine_type = $field['machine_field_type'];
			
			$action = 'add'.ucfirst($field['type']);
		  
			$label = $field['label'];
			
			$webform_id = $id;
		  
			if($type == 'text'){
				$form->$action($field['frm_name'], $label, $value, 'frm-'.$field['type'],$size, $maxlength);
				//for numbers
				if(isset($min) && $min > 0){
					$form->addRule($field['frm_name'], Form::MIN, $min, ITEM.' '.$field['label'].' '.WF_CREATE_WEB_FORM_MIN_RULE.$min);
					unset($min);
				}
				if(isset($max) && $max > 0){
					$form->addRule($field['frm_name'], Form::MAX, $max, ITEM.' '.$field['label'].' '.WF_CREATE_WEB_FORM_MAN_RULE.$max);
					unset($max);
				}
				if(isset($min) && $min > 0 && isset($max) && $max > 0){
					$form->addRule($field['frm_name'], Form::RANGE, $min ,$max, ITEM.' '.$field['label'].' '.WF_CREATE_WEB_FORM_RANGE_RULE.$min.' - '.$max);
					unset($min);
					unset($max);
				}
				unset($size);
				unset($maxlength);
			}
			elseif($type == 'textarea'){
				$form->$action($field['frm_name'], $label, $value, 'frm-'.$field['type'],$cols, $rows);
				unset($cols);
				unset($rows);
			}
			elseif($type == 'select' or $type == 'radio'){
				$vals = explode(';', $field['default']);
				$selected = '';
				foreach($vals as $val){
					$valandops[$val] = $val;
				}
				foreach($valandops as $key => $val){
					if(preg_match('/:default/', $key)){
						$key = preg_replace('/:default/', '', $key);
						$val = preg_replace('/:default/', '', $val);
						$selected = $val;
					}
					$valops[$key] = $val;
				}
				
				$form->$action($field['frm_name'], $valops, $label, $selected);
				
				unset($valops);
				unset($selected);
			}
			elseif($type == 'checkbox'){
				if($field['default']) $check = true;
				  else $check = false;
				$form->$action($field['frm_name'], 1, $label, null, $check);
				unset($check);
			}
			elseif($machine_type == 'image'){
				$form->addHidden('image_machine_type', $machine_type);
				$form->addHidden('image_frm_name', $field['frm_name']);
				$form->addHidden('img_max_file_size', $max_file_size*1024000);
				$form->addHidden('preview_size', $preview_size);
				$form->addHidden('icon_size', $icon_size);
				$form->addHidden('thumb_create', $thumb_create);
				$form->$action($field['frm_name'], $label);
			}
			elseif($machine_type == 'file'){
				$form->addHidden('file_machine_type', $machine_type);
				$form->addHidden('file_frm_name', $field['frm_name']);
				$form->addHidden('file_max_file_size', $max_file_size*1024000);
				$form->$action($field['frm_name'], $label);
			}
			if($field['description']){
				$form->addDescription($field['frm_name'], $field['description']);
			}
			if($field['required']){
				$form->addRule($field['frm_name'], Form::FILLED, ITEM.' '.$field['label'].' '.IS_REQUIRED);
			}
			if($machine_type == 'number') $form->addRule($field['frm_name'], Form::NUMERIC, ITEM.' '.$field['label'].' '.WF_CREATE_WEB_FORM_NUMERIC_RULE);
			// if element name contains email add email rule
			if(preg_match('/^[a-z_]*email[a-z_]*$/', $field['frm_name'])) $form->addRule($field['frm_name'], Form::EMAIL, ITEM.' '.$field['label'].' '.WF_CREATE_WEB_FORM_EMAIL_RULE);

		}
		
		$form->addCaptcha('sec_code', WF_CREATE_WEB_FORM_CAPTCHA_LABEL);
			$form->addRule('sec_code', Form::CAPTCHA, WF_CREATE_WEB_FORM_CAPTCHA_RULE);
		$form->addHidden('webform_id', $webform_id);
		
		$form->emptyLine();
		$form->addSubmit('send', SEND);
		$frm = $form->render();

		if($form_title){
		  $header = Html::elem('h3', array('class'=>'wf-form-title title'), $form_title);
		}
		else{
		  $header = '';
		}
		
		$output = Html::elem('div', array('id'=>'wf-form-'.$webform_id, 'class'=>'wf-form-container'), $header.$frm);
		
	}
	else{
		$output = WF_CREATE_WEB_FORM_NO_FIELDS;
	}
	
	return $output;
	
  }
  
  /*************************************** OTHER METHODS ***/
  
  private function runSettings()
  {
    $obj = new WebformModel;
  
    $rows = $obj->getSettings();
  
    foreach($rows as $row){
      define($row['constant'], $row['value']);
    }
	
	if(WF_AUTO_CLEAN){
	  $clean_date = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m")  , date("d")-WF_DELETE_OLDER_THEN, date("Y")));
	  db::exec("DELETE FROM webform_messages WHERE datetime < '$clean_date'");
	}
  }
  
  private function getPages()
  {
    $obj = new WebformModel;
	
	$records = $obj->findAllMessages();
	$rec_num = count($records);
	if($rec_num){
	  $pageidx = 1;
	  $idx = 1;
	  $lastidx = (int)WF_MESSAGES_PER_PAGE;
	  foreach($records as $record){
		$messages[$record['id']] = $record;
		if($idx == $lastidx){
		  $idx = 0;
		  $this->pages[$pageidx] = $messages;
		  unset($messages);
		  $pageidx++;
		}
		$idx++;
	  }
	  // Place the rest of messages to last page
	  if(isset($messages) && count($messages)>0){
	    $this->pages[$pageidx] = $messages;
	  }
	}
	else{
	  $this->pages = false;
	}
  }
  
  //******** Internal Function parseAttributes() ****************
  private function parseAttributes($attrs){
	
	  $attritems = explode(';', $attrs);
	  
	  foreach($attritems as $attritem){
	  
	    list($idx, $value) = explode(':',$attritem);
		
		$return[$idx] = $value;
	  
	  }
	  
	  return $return;
	
  }
}
