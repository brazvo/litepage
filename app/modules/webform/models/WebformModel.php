<?php
class WebformModel
{
  // Properties
  
  // Constructor
  public function __construct()
  {
  
  }
  
  // Methods

  public function find($id)
  {
    $row = db::fetch("SELECT * FROM webform WHERE id=$id");
	return $row;
  }
  
  public function findForPage($id=null)
  {
    if($id){
	  $rows = db::fetchAll("SELECT * FROM webform_messages WHERE webform_id=$id ORDER BY datetime ".WF_ORDER." LIMIT ".WF_MESSAGES_PER_PAGE);
	}
	else{
	  $rows = db::fetchAll("SELECT * FROM webform_messages ORDER BY datetime ".WF_ORDER." LIMIT ".WF_MESSAGES_PER_PAGE);
	}
	return $rows;
  }
  
  public function findAll()
  {
    $rows = db::fetchAll("SELECT * FROM webform ORDER BY title");
	return $rows;
  }
  
  public function findAllMessages()
  {
    $rows = db::fetchAll("SELECT * FROM webform_messages ORDER BY datetime ".WF_ORDER);
	return $rows;
  }
  
  public function findOne($id)
  {
    $result = db::fetch("SELECT * FROM webform WHERE id='$id'");
	return $result;
  }
  
  public function findAllFields($id)
  {
    // Count rows
	$rows = db::fetchAll("SELECT * FROM webform_fields WHERE webform_id=$id");
	$num_rows = count($rows);
	
	if($num_rows > 0){
	  return db::fetchAll("SELECT * FROM webform_fields WHERE webform_id=$id ORDER BY priority");
	}
	else{
	  return null;
	}
  }
  
  public function findField($id)
  {
    return db::fetch("SELECT * FROM webform_fields WHERE id='$id'");
  }
  
  
  public function getFieldTypes()
  {
	
	$result = db::fetchAll("SELECT * FROM basic_fields ORDER BY id");
	
	foreach($result as $row){
	  $ret[$row['id']] = $row['label'];	
	}

    return $ret;	
  }
  
  
  /**
   * @title Function Send
   * sends form
   */ 
  public function send($post)
  {
    
	$webform_id = $post['webform_id'];
	
	unset($post['validate']);
	unset($post['send']);
	unset($post['sec_code']);
	unset($post['id']);
	unset($post['webform_id']);
	
	//check for bool values of checkboxes
	$fields = db::fetchAll("SELECT * FROM webform_fields WHERE webform_id=".$webform_id);
	foreach($fields as $field){
		if(!isset($post[$field['frm_name']])) $post[$field['frm_name']] = Application::getVal('no');
	}
	
	$messageDivs = '';
	foreach($post as $key => $val){
	  $post[$key] = htmlMyEnts($val);
	  
	  $row = db::fetch("SELECT * FROM webform_fields WHERE frm_name='$key'");
	  
	  if($row['type'] == 'textarea'){
		$messageDivs .= Html::elem('div', null, '<b>'.$row['label'].':</b><br/>'.preg_replace("/\r\n/", "<br>", $post[$key]));
	  }
	  else{
	    $messageDivs .= Html::elem('div', null, '<b>'.$row['label'].':</b> '.$post[$key]);
	  }
	  
	  
	}
	
	$row = db::fetch("SELECT title, email FROM webform WHERE id=".$webform_id);
	$form = $row['title'];
	$from = WF_NOTIFICATE;
	
	if(WF_NOTIFICATE or $row['email']){
		
		if(WF_NOTIFICATE) $to = trim(WF_NOTIFICATE);
	    if($row['email']) $to = trim($row['email']);
	    if(WF_NOTIFICATE && $row['email']) $to = trim(WF_NOTIFICATE).', '.trim($row['email']);
	
		$mail = WF_ACTION_SEND_MESSAGE_FROM.$form.NL;
		$mail .= '-----------------------------------------<br>';
		$mail .= $messageDivs;
		$mail .= '-----------------------------------------<br>';
		$from = emailStr($from);
		if($post['email']) {$from = "<".$post['email'].">";}
		$subject = WF_ACTION_SEND_MESSAGE_FROM.$form;
		$continue = Application::mailer($from, $to, emailStr($subject), $mail);
		//$continue = true;
	}
	else{
		$continue = true;
	}
	
	if($continue){
	  return db::exec("INSERT INTO webform_messages (webform_id, html_content)
	                   VALUES ($webform_id, '$messageDivs')");
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
	
	//check for double path aliases
	if(trim($path_alias) != '' && $path_alias != $old_path_alias){
	  $result = $this->checkForDoublePaths($path_alias);
	}
	else{
	  $result = false;
	}
	// if true return with error
	if($result){
		Application::setError(WF_ERR_PATH_ALIAS_PART_ONE.' <i>'.$path_alias.'</i> '.WF_ERR_PATH_ALIAS_PART_TWO);
		return false;
	}
	
	//Check if selected self menu item
	list($menu_id, $child_of) = explode(':', $menu_items);
	$menuFetch = db::fetch("SELECT * FROM menu_items WHERE module='webform' AND content_id='$id' AND menu_id='$menu_id' AND id='$child_of'");
	// if true return with error
	if($menuFetch){
		Application::setError('Nie je možné zvoliť ako nadradenú položku vlastnú položku.');
		return false;
	}
	
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
	$result = db::exec("UPDATE webform SET title='$title', form_title='$form_title',
	                    body='$body', path_alias='$path_alias', form_after_text='$form_after_text',
						hide_on_load='$hide_on_load', email='$email', lang='$lang', updated='$timestamp' WHERE id=$id");
	// UPDATE PATH ALIAS
	if(trim($path_alias) != ''){
	  $row = db::fetch("SELECT * FROM path_aliases WHERE module='webform' AND mod_id=$id");
	  if($row){
	    if(!isset($_SESSION['destination'])){
			if($path_alias != $row['path_alias']){
				Application::$pathRequest = $path_alias;
			}
		}
		else{
			unset($_SESSION['destination']);
		}
	    db::exec("UPDATE path_aliases SET path_alias='$path_alias' WHERE module='webform' AND mod_id=$id");
	  }
	  else{
	    db::exec("INSERT INTO path_aliases (path_alias, url, module, mod_id) VALUES ('$path_alias', 'webform/frontend/show/$id', 'webform', $id)");
	  }
	}
	else{
	  db::exec("DELETE FROM path_aliases WHERE module='webform' AND mod_id=$id");
	}
	
	//UPDATE MENUS
	if(trim($post['menu_title']) != ''){
		
		trim($post['path_alias']) !='' ? $path = $post['path_alias'] : $path = "webform/frontend/show/$id";
		$row = db::fetch("SELECT * FROM menu_items WHERE module='webform' AND content_id='$id'");
		if($row){
			db::exec("UPDATE menu_items SET title='".$post['menu_title']."', path='$path', menu_id='$menu_id', child_of='$child_of' WHERE module='webform' AND content_id='$id'");
		}
		else{
			db::exec("INSERT INTO menu_items VALUES (null, '".$post['menu_title']."', '$path', 1, 0, 0, '$child_of', '$menu_id', '".$post['menu_title']."', '', '$id', 'webform')");
		}
	}
	else{
		db::exec("DELETE FROM menu_items WHERE module='webform' AND content_id='$id'");
	}
        
        //UPADATE CONTENT ROW
        $uid = Application::$logged['userid'];
        $sql = "UPDATE content SET `content_title`='{$title}',
                                   `path_alias`='{$path_alias}',
                                   `last_update`='{$timestamp}',
                                   `edit_uid`={$uid},
                                   `lang`='{$lang}'
                               WHERE `content_type_machine_name`='webform'
                                   AND `content_id`={$id}";
        db::exec($sql);
	
	return $result;
  }
  
  public function saveNew($post)
  {
    foreach($post as $key => $val){
	  $$key = $val;
	}
	
	//check for double path aliases
	trim($path_alias) != '' ? $result = $this->checkForDoublePaths($path_alias) : $result = false;
	// if true return with error
	if($result){
		Application::setError(WF_ERR_PATH_ALIAS_PART_ONE.' <i>'.$path_alias.'</i> '.WF_ERR_PATH_ALIAS_PART_TWO);
		return false;
	}
	
	if(!isset($form_after_text)) $form_after_text = 0;
	if(!isset($hide_on_load)) $hide_on_load = 0;
	
	//INSERT LANGUAGE
	if(isset($lang_code)){
	  	$lang = $lang_code;
	}
	else{
		$lang = 'none';
	}
	
	$row = db::fetch("SELECT MAX(id) AS id FROM webform");
	$newid = $row['id']+1;
	$body = stripslashes($body);
	$timestamp = DATE("Y-m-d H:i:s");
	$result = db::exec("INSERT INTO webform VALUES ($newid, '$title', '$form_title', '$body', '$path_alias', '$form_after_text', '$hide_on_load', '$email', '$lang', '$timestamp')");
	
	//CREATE PATH ALIAS
	if(trim($path_alias) != ''){
	  db::exec("INSERT INTO path_aliases (path_alias, url, module, mod_id) VALUES ('$path_alias', 'webform/frontend/show/$newid', 'webform', $newid)");
	}
	
	//CREATE MENU ITEM
	if(trim($post['menu_title']) != ''){
		list($menu_id, $child_of) = explode(':', $post['menu_items']);
		trim($post['path_alias']) !='' ? $path = $post['path_alias'] : $path = "webform/frontend/show/$newid";
		db::exec("INSERT INTO menu_items VALUES (null, '".$post['menu_title']."', '$path', 1, 0, 0, '$child_of', '$menu_id', '".$post['menu_title']."', '', '$newid', 'webform')");
	}
        
        //CREATE CONTENT ROW
        $contMod = new ContentModel;
        $new_cont_id = $contMod->getNewContentId();
        $uid = Application::$logged['userid'];
        $sql = "INSERT INTO content (`id`, `content_type_id`, `content_type_name`, `content_title`,
		                     `path_alias`, `last_update`, `content_type_machine_name`, `content_id`,
                                     `uid`, `edit_uid`, `lang`, `module`, `created`) 
				     VALUES ('$new_cont_id', 0, 'WebForm', '{$title}',
				     '{$path_alias}', '{$timestamp}', 'webform',
                                     {$newid}, {$uid}, {$uid}, '{$lang}', 1, '{$timestamp}')";
		
 	$result = db::exec($sql);
	
	return $result;
  }
  
  
  public function saveField($post)
  {
	
	foreach($post as $key => $val){
		$$key = $val;
	}

	// get values from basic fields table
	$basic_field_id = $fieldtype;
	$bas_field = db::fetch("SELECT label, type, attributes, db_type, machine_type FROM basic_fields WHERE id=$basic_field_id");
	if(!$bas_field) return false;
	
	// get max priority value
	$result = db::fetch("SELECT MAX(id) AS id FROM webform_fields");
	$result += db::fetch("SELECT MAX(priority) AS priority FROM webform_fields WHERE webform_id=$webform_id");
	if($result){
	  $newid = $result['id']+1;
	  $priority = $result['priority']+1;
	}
	else{
	  return false;
	}
	
	// insert into content types table
	$type = $bas_field['type'];
	$attributes = $bas_field['attributes'];
	$default = null;
	$frm_name = machineStr($frm_name); // check if machine correct name
	$required = 0;
	$email = 1;
	$field_type = $bas_field['label'];
	$machine_type = $bas_field['machine_type'];
	$description = null;
	$webform_label='';
	
	$sql = "INSERT INTO webform_fields VALUES
	        ('$newid', '$webform_id', '$type', '$label', '$webform_label', '$attributes', '$priority', '$default', '$frm_name', '$required',
			 '$email', '$field_type', '$description', '$machine_type')";
	
	echo $sql;
	return db::exec($sql);
  
  }
  
  public function updateField($post)
  {
    $attributes = '';
    foreach($post as $key => $val){
	  if(substr($key, 0, 5) == 'attr_'){
	    // Remove attr_ from key string
		$key = substr($key, 5);
		//add attribute to attributes
		$attributes .= $key.':'.$val.';';
	  }
	  $$key = $val;
	}
	//if atrributes remove last semi-colon ; from attributes string
	if($attributes) $attributes = substr($attributes, 0, -1);
	
    if(!isset($required)) $required = 0;
	 if(!isset($email)) $required = 0;
	$default = preg_replace('/\r\n/', ';', $default);
	
	$sql = "UPDATE webform_fields SET label='$label', description='$description', attributes='$attributes', `default`='$default',
	        required='$required', email='$email', webform_label='$webform_label' WHERE id=$id";
	
	$result = db::exec($sql);
	
	if($result){
	  return true;
	}
	else{
	  return false;
	}
  }
  
  
  public function deleteField($id)
  {
    return db::exec("DELETE FROM webform_fields WHERE id=$id");
  }
  
  public function orderFields($post)
  {
	foreach($post['ids'] as $id){
	  $priority = $post["priority_$id"];
	  $result = db::exec("UPDATE webform_fields SET priority='$priority' WHERE id=$id");
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
	  $result = db::exec("UPDATE webform_messages_settings SET value = '$val' WHERE frm_name = '$key'");
	}
	
	return $result;
  }
  
  public function delete($id)
  {
        $result = db::exec("DELETE FROM webform WHERE id=$id");
        $result = db::exec("DELETE FROM webform_fields WHERE webform_id=$id");
	$result = db::exec("DELETE FROM webform_messages WHERE webform_id=$id");
	$result = db::exec("DELETE FROM menu_items WHERE module='webform' AND content_id=$id");
	$result = db::exec("DELETE FROM path_aliases WHERE module='webform' AND mod_id=$id");
        $result = db::exec("DELETE FROM content WHERE content_type_machine_name='webform' AND content_id=$id");
	return $result;
  }
  
  public function deleteMessage($id)
  {
	$result = db::exec("DELETE FROM webform_messages WHERE id=$id");
	return $result;
  }
  
  public function getSettings()
  {
    $rows = db::fetchAll("SELECT * FROM webform_messages_settings");
	return $rows;
  }
  
  public function getFormNames()
  {
    $rows = db::fetchAll("SELECT id, title FROM webform");
	if(!$rows) return false;
	foreach($rows as $row){
	  $return[$row['id']] = $row['title'];
	}
	return $return;
  }
  
  private function checkForDoublePaths($path)
  {
	$row = db::fetch("SELECT * FROM path_aliases WHERE path_alias='$path'");
	if($row){
		return true;
	}
	else{
		return false;
	}
  }
}