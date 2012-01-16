<?php

class AdmUsersModel extends BaseModel
{
  // Properties
  
  // Constructor
  public function __construct()
  {
  
    parent::__construct();
  
  }
  
  // Methods

  public function findUser()
  {
	return db::fetch("SELECT * FROM users WHERE session_id='".SESSIONID."'");
  }
  
  public function find($id)
  {
	$row = db::fetch("SELECT us.id, us.user, us.name, us.surname, us.role, us.lock,
			          ur.email, ur.isstaff, ur.staff_position, ur.staff_email, ur.staff_comment
			          FROM users us 
			          LEFT JOIN users_registration ur ON ur.uid = us.id
			          WHERE id=$id");
	if(!$row) return FALSE;
	
	if( preg_match('/, /', $row['staff_position']) ) {
		$row['staff_position'] = preg_split('/, /', $row['staff_position'], null, PREG_SPLIT_NO_EMPTY);
	}
	
	return $row;
  }
  
  public function findAll()
  {
	return db::fetchAll("SELECT * FROM users ORDER BY surname");
  }
  
  public function savePwd($post)
  {
   
	$result = db::fetch("SELECT * FROM users WHERE session_id='".SESSIONID."'");
	
	if($post['newpwd'] == $post['newpwdconf']){
	
	  if($result['password'] == md5($post['oldpwd'])){
	
	    db::exec("UPDATE users SET password='".md5($post['newpwd'])."' WHERE session_id='".SESSIONID."'");
	    
		Application::setMessage('Nové heslo bolo uložené.');
		
		return true;
	  
	  }
	  else{
	  
	    Application::setError('Staré heslo nebolo správne zadané.');
		
		return false;
	  
	  }
	
	}
	else{
	
	  Application::setError('Nové heslo sa nezhoduje s potvrdením hesla.');
	  
	  return false;
	
	}
	
	
  }
  
  public function saveNew($post)
  {
    
    foreach($post as $key => $value){
	  $$key = $value;
	}
	
	$password = md5($password);
	
	$result = db::exec("INSERT INTO users (user, password, role, name, surname, last_login, session_id) VALUES ('$user','$password','$role','$name','$surname','0000-00-00 00:00:00','XXX')");
	
	return $result;
  
  }
  
  public function save($post)
  {
    
    foreach($post as $key => $value){
	  $$key = $value;
	}
	
	if( isset($staff_position) && $isstaff ) {
		if( is_array($staff_position) ) {
			foreach($staff_position as $pos) {
				db::exec("UPDATE users_staff_positions SET uid=$id, isfree=0 WHERE staff_position = '$pos'");
			}
			
			$staff_position = implode (', ', $staff_position);
		}
		else {
			db::exec("UPDATE users_staff_positions SET uid=$id, isfree=0 WHERE staff_position = %v", trim($staff_position));
		}
	}
	else {
		db::exec("UPDATE users_staff_positions SET uid=0, isfree=1 WHERE uid = %v", $id);
		$staff_position = '';
		$isstaff = 0;
	}
	
	if($password != ''){
		$password = md5($password);
		$result = db::exec("UPDATE users SET name='$name', surname='$surname',
				            password='$password', role='$role'
				            WHERE id=$id");
	}
	else{
		$result = db::exec("UPDATE users SET name='$name', surname='$surname', role='$role' WHERE id=$id");
	}
	$result = db::exec("UPDATE users_registration SET staff_comment='$staff_comment',
							staff_position='$staff_position', isstaff=$isstaff, staff_email='$staff_email'
				            WHERE uid=$id");
	
	
	return $result;
  
  }
  
  public function addUsersPermision($post){
	foreach($post as $key => $val){
		$$key = $val;
	}
	
	$row = db::fetch("SELECT * FROM permisions WHERE cont_mach_name='$machine_name'");
	$name = $row['content'];
	
	if(!isset($view)) $view = 0;
	if(!isset($add)) $add = 0;
	if(!isset($edit)) $edit = 0;
	if(!isset($delete)) $delete = 0;
	
	return db::exec("INSERT INTO users_permisions VALUES (null, $uid, '$name', '$machine_name', '$view', '$add', '$edit', '$delete')");
  }
  
  public function saveUsersPermision($post){
	
	$uid = $post['uid'];
	
	$rows = db::fetchAll("SELECT * FROM users_permisions WHERE uid=$uid");
	
	foreach($rows as $row){
		if(!isset($post[$row['id'].'__view'])) $post[$row['id'].'__view'] = 0;
		if(!isset($post[$row['id'].'__add'])) $post[$row['id'].'__add'] = 0;
		if(!isset($post[$row['id'].'__edit'])) $post[$row['id'].'__edit'] = 0;
		if(!isset($post[$row['id'].'__delete'])) $post[$row['id'].'__delete'] = 0;
		echo $sql = "UPDATE `users_permisions` SET `view`=".$post[$row['id'].'__view'].", `add`=".$post[$row['id'].'__add'].", 
		                    `edit`=".$post[$row['id'].'__edit'].", `delete`=".$post[$row['id'].'__delete']." WHERE `id`=".$row['id'];
		$result = db::exec($sql);
	}
	return $result;
  }
  
  public function delete($id)
  {
  
    return db::exec("DELETE FROM users WHERE id=$id");
  
  }
  
  public function logout()
  {
    
	$sql = "UPDATE users SET session_id='XXX' WHERE session_id='".SESSIONID."'";
	
    return db::exec($sql);
	
  }
  
  public function getModules($uid)
  {
	$perms = db::fetchAll("SELECT * FROM permisions ORDER BY content");
	if(!$perms) return false;
	
	foreach($perms as $perm){
		$return[$perm['cont_mach_name']] = $perm['content'];
	}
	
	$usPerms = db::fetchAll("SELECT * FROM users_permisions ORDER BY name");
	if($usPerms){
		foreach($usPerms as $usPerm){
			unset($return[$usPerm['machine_name']]);
		}
	}
	
	return $return;
	
  }
  
  public function getUsersPermisions($id)
  {
	return db::fetchAll("SELECT * FROM users_permisions WHERE uid=$id ORDER BY name");
  }
  
  public function deleteUsersPermision($id)
  {
	return db::exec("DELETE FROM users_permisions WHERE id=$id");
  }
  
  
  public function getStaffPositions()
  {
	  $rows = db::fetchAll("SELECT `staff_position` FROM `users_staff_positions` ORDER BY `id`");
	  
	  $ret = array();
	  
	  if($rows) {
		  foreach ($rows as $row) {
			  $ret[ $row['staff_position'] ] = $row['staff_position'];
		  }
	  }
	  
	  return $ret;
  }
  
  public function lockUser($uid)
  {
	  return db::exec("UPDATE users SET lock=1 WHERE id =%i", $uid);
  }
  
  public function unlockUser($uid)
  {
	  return db::exec("UPDATE users SET lock=0 WHERE id =%i", $uid);
  }
  
  public function saveNewPwd($uid, $newPwd)
  {
	  $email = db::fetchSingle("SELECT email FROM users_registration WHERE uid = %i", $uid);
	  
	  $res = db::exec("UPDATE users SET password = %v WHERE id=%i", md5($newPwd), $uid);
	  
	  if($res && $email) {
		  $messageBody = "<p>Pre prihlasenie na stranke <a href='http://www.ivao.sk/'>www.ivao.sk</a> Ti bolo vygenerovane nove heslo: $newPwd</p>";
		  $messageBody .= "<br>";
		  $messageBody .= "<p>staff SK-DIV IVAO</p>";
		  $subject = "Vygenerovane nove heslo";
		  $from = "sk-mc@ivao.aero";
		  $to = $email;
		  
		  Application::mailer($from, $to, $subject, $messageBody);
		  
		  return true;
	  }
	  else {
		  return false;
	  }
			  
  }
	
}