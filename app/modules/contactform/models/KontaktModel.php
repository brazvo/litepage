<?php
class KontaktModel
{
  // Properties
  
  // Constructor
  public function __construct()
  {
  
  }
  
  // Methods

  public function find($id)
  {
    $row = db::fetch("SELECT * FROM contactform WHERE id=$id");
	return $row;
  }
  
  public function findForPage($id=null)
  {
    if($id){
	  $rows = db::fetchAll("SELECT * FROM contact_messages WHERE contact_frm_id=$id ORDER BY datetime ".ORDER." LIMIT ".MESSAGES_PER_PAGE);
	}
	else{
	  $rows = db::fetchAll("SELECT * FROM contact_messages ORDER BY datetime ".ORDER." LIMIT ".MESSAGES_PER_PAGE);
	}
	return $rows;
  }
  
  public function findAll()
  {
    $rows = db::fetchAll("SELECT * FROM contactform ORDER BY title");
	return $rows;
  }
  
  public function findAllMessages()
  {
    $rows = db::fetchAll("SELECT * FROM contact_messages ORDER BY datetime ".ORDER);
	return $rows;
  }
  
  public function send($post)
  {
    unset($post['validate']);
	unset($post['send']);
	unset($post['sec_code']);
	
	foreach($post as $key => $val){
	  $$key = htmlMyEnts($val);
	}
	
	$row = db::fetch("SELECT title FROM contactform WHERE id=$contact_frm_id");
	$form = $row['title'];
	
	if(NOTIFICATE){
		$mail = 'Správa z formulára '.$form.NL;
		$mail .= '-----------------------------------------<br>';
		$mail .= $name.NL;
		if($company) $mail .= $company.NL;
		if($address) $mail .= preg_replace("/\r\n/", "<br>", $address.NL);
		if($phone) $mail .= $phone.NL;
		if($email) $mail .= $email.NL;
		if($subject) $mail .= $subject.NL;
		$mail .= '-----------------------------------------<br>';
		$mail .= preg_replace("/\r\n/", "<br>", $message);
		$from = emailStr($name);
		if($email) {$email = trim($email); $from = emailStr($name)." <$email>";}
		if(!$subject) $subject = 'Sprava z formulara '.$form;
		$continue = Application::mailer($from, trim(NOTIFICATE), emailStr($subject), $mail);
		//$continue = true;
	}
	else{
		$continue = true;
	}
	
	if($continue){
	  return db::exec("INSERT INTO contact_messages (name, company, address, phone, email, subject, message, contact_frm_id)
	                   VALUES ('$name', '$company', '$address', '$phone', '$email', '$subject', '$message', $contact_frm_id)");
	}
	else{
	  return false;
	}
	
	
	
  }
  
  public function save($post)
  {
    	
	foreach($post as $key => $val){
	  $$key = $val;
	}
	if(!isset($company_fld)) $company_fld = 0;
	if(!isset($company_fld_req)) $company_fld_req = 0;
	if(!isset($address_fld)) $address_fld =0;
	if(!isset($address_fld_req)) $address_fld_req = 0;
	if(!isset($phone_fld)) $phone_fld = 0;
	if(!isset($phone_fld_req)) $phone_fld_req = 0;
	if(!isset($email_fld)) $email_fld = 0;
	if(!isset($email_fld_req)) $email_fld_req = 0;
	if(!isset($subject_fld)) $subject_fld = 0;
	if(!isset($subject_fld_req)) $subject_fld_req = 0;
	if(!isset($form_after_text)) $form_after_text = 0;
	if(!isset($hide_on_load)) $hide_on_load = 0;
	
	//UPDATE LANGUAGE
	if(isset($lang_code)){
		$lang = $lang_code;
	}
	else{
		$lang = 'none';
	}
	
	$body = stripslashes($body);
	$timestamp = DATE("Y-m-d H:i:s");
	$result = db::exec("UPDATE contactform SET title='$title', form_title='$form_title', body='$body', path_alias='$path_alias', lang='$lang', updated='$timestamp' WHERE id=$id");
	$result = db::exec("UPDATE contact_form_settings SET
	                    company_fld=$company_fld, company_fld_req=$company_fld_req, address_fld=$address_fld, address_fld_req=$address_fld_req,
					    phone_fld=$phone_fld, phone_fld_req=$phone_fld_req, email_fld=$email_fld, email_fld_req=$email_fld_req, subject_fld=$subject_fld, subject_fld_req=$subject_fld_req,
						form_after_text=$form_after_text, hide_on_load=$hide_on_load WHERE contact_frm_id=$id");
	if(trim($path_alias) != ''){
	  $row = db::fetch("SELECT * FROM path_aliases WHERE module='kontakt' AND mod_id=$id");
	  if($row){
	    if(!isset($_SESSION['destination'])){
			if($path_alias != $row['path_alias']){
				Application::$pathRequest = $path_alias;
			}
	    }
	    else{
			unset($_SESSION['destination']);
	    }
	    db::exec("UPDATE path_aliases SET path_alias='$path_alias' WHERE module='kontakt' AND mod_id=$id");
	  }
	  else{
	    db::exec("INSERT INTO path_aliases (path_alias, url, module, mod_id) VALUES ('$path_alias', 'kontakt/show/$id', 'kontakt', $id)");
	  }
	}
	else{
	  db::exec("DELETE FROM path_aliases WHERE module='kontakt' AND mod_id=$id");
	}
	
	//UPDATE MENUS
	
	//Check if selected self menu item
	list($menu_id, $child_of) = explode(':', $menu_items);
	$menuFetch = db::fetch("SELECT * FROM menu_items WHERE module='kontakt' AND content_id='$id' AND menu_id='$menu_id' AND id='$child_of'");
	// if true return with error
	if($menuFetch){
		Application::setError(MENUITEM_THE_SAME_SELECTED);
		return false;
	}
	
	if(trim($post['menu_title']) != ''){
		
		trim($post['path_alias']) !='' ? $path = $post['path_alias'] : $path = "kontakt/show/$id";
		$row = db::fetch("SELECT * FROM menu_items WHERE module='kontakt' AND content_id='$id'");
		if($row){
			db::exec("UPDATE menu_items SET title='".$post['menu_title']."', path='$path', menu_id='$menu_id', child_of='$child_of' WHERE module='kontakt' AND content_id='$id'");
		}
		else{
			db::exec("INSERT INTO menu_items VALUES (null, '".$post['menu_title']."', '$path', 1, 0, 0, '$child_of', '$menu_id', '".$post['menu_title']."', '', '$id', 'kontakt')");
		}
	}
	else{
		db::exec("DELETE FROM menu_items WHERE module='kontakt' AND content_id='$id'");
	}
	
	return $result;
  }
  
  public function saveNew($post)
  {
    foreach($post as $key => $val){
	  $$key = $val;
	}
	if(!isset($company_fld)) $company_fld = 0;
	if(!isset($company_fld_req)) $company_fld_req = 0;
	if(!isset($address_fld)) $address_fld =0;
	if(!isset($address_fld_req)) $address_fld_req = 0;
	if(!isset($phone_fld)) $phone_fld = 0;
	if(!isset($phone_fld_req)) $phone_fld_req = 0;
	if(!isset($email_fld)) $email_fld = 0;
	if(!isset($email_fld_req)) $email_fld_req = 0;
	if(!isset($subject_fld)) $subject_fld = 0;
	if(!isset($subject_fld_req)) $subject_fld_req = 0;
	if(!isset($form_after_text)) $form_after_text = 0;
	if(!isset($hide_on_load)) $hide_on_load = 0;
	
	//INSERT LANGUAGE
	if(isset($lang_code)){
	  	$lang = $lang_code;
	}
	else{
		$lang = 'none';
	}
	
	$row = db::fetch("SELECT MAX(id) AS id FROM contactform");
	$newid = $row['id']+1;
	$body = stripslashes($body);
	
	$timestamp = DATE("Y-m-d H:i:s");
	$result = db::exec("INSERT INTO contactform (id, title, form_title, body, path_alias, lang, updated) VALUES ($newid, '$title', '$form_title', '$body', '$path_alias', '$lang', '$timestamp')");
	$result = db::exec("INSERT INTO contact_form_settings 
	                   (contact_frm_id, company_fld, company_fld_req, address_fld, address_fld_req, phone_fld, phone_fld_req,
					    email_fld, email_fld_req, subject_fld, subject_fld_req, form_after_text, hide_on_load) 
						VALUES ($newid, $company_fld, $company_fld_req, $address_fld, $address_fld_req, $phone_fld, $phone_fld_req,
					    $email_fld, $email_fld_req, $subject_fld, $subject_fld_req, $form_after_text, $hide_on_load)");
	if(trim($path_alias) != ''){
	  db::exec("INSERT INTO path_aliases (path_alias, url, module, mod_id) VALUES ('$path_alias', 'kontakt/show/$newid', 'kontakt', $newid)");
	}
	
	//CREATE MENU ITEM
	if(trim($post['menu_title']) != ''){
		list($menu_id, $child_of) = explode(':', $post['menu_items']);
		trim($post['path_alias']) !='' ? $path = $post['path_alias'] : $path = "kontakt/show/$newid";
		db::exec("INSERT INTO menu_items VALUES (null, '".$post['menu_title']."', '$path', 1, 0, 0, '$child_of', '$menu_id', '".$post['menu_title']."', '', '$newid', 'kontakt')");
	}
	
	return $result;
  }
  
  public function saveSettings($post)
  {
    unset($post['save']);
	unset($post['validate']);
	if(!isset($post['auto_clean'])) $post['auto_clean'] = 0;
	foreach($post as $key => $val){
	  $val = trim($val);
	  $result = db::exec("UPDATE contact_messages_settings SET value = '$val' WHERE frm_name = '$key'");
	}
	
	return $result;
  }
  
  public function delete($id)
  {
    $result = db::exec("DELETE FROM contactform WHERE id=$id");
	$result = db::exec("DELETE FROM contact_form_settings WHERE contact_frm_id=$id");
	$result = db::exec("DELETE FROM contact_messages WHERE contact_frm_id=$id");
	$result = db::exec("DELETE FROM path_aliases WHERE module='kontakt' AND mod_id=$id");
	return $result;
  }
  
  public function deleteMessage($id)
  {
	$result = db::exec("DELETE FROM contact_messages WHERE id=$id");
	return $result;
  }
  
  public function getSettings()
  {
    $rows = db::fetchAll("SELECT * FROM contact_messages_settings");
	return $rows;
  }
  
  public function getFormSettings($id)
  {
    $row = db::fetch("SELECT * FROM contact_form_settings WHERE contact_frm_id=$id");
	return $row;
  }
  
  public function getFormNames()
  {
    $rows = db::fetchAll("SELECT id, title FROM contactform");
	if(!$rows) return false;
	foreach($rows as $row){
	  $return[$row['id']] = $row['title'];
	}
	return $return;
  }
}