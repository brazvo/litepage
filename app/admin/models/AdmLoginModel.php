<?php

class AdmLoginModel extends BaseModel
{
  // Properties
  
  // Constructor
  public function __construct()
  {
  
    parent::__construct();
  
  }
  
  // Methods

  public function validateUser($post)
  {
	
    $user = $post['user'];
	$password = md5($post['password']);
	
	$result = db::fetchAll("SELECT * FROM users");
	
	$found = false;
	foreach($result as $record){
	  
	  if($user == $record['user'] && $password == $record['password']){
	    //echo 'SME TU';  exit;
	    $now = date("Y-m-d H:i:s");
		$sql = "UPDATE users SET session_id='".SESSIONID."', last_login='$now' WHERE id=".$record['id'];
	    db::exec($sql);
		$found = true;
	  }
	}
	
	if($found){
	  //DO IF FOUND
	  return true;
	}
	else{
	  //DO IF NOT FOUND
	  return false;
	}
	
	return $result;
  
  }
  
  public function isLogged()
  {
   
    $user = db::fetch("SELECT * FROM users WHERE session_id='".SESSIONID."'");
	
	if(isset($user['session_id'])){
	  return $user;
	}
	else{	  
	  return false;
	}
	
  }
  
  public function logout()
  {
    
	$sql = "UPDATE users SET session_id='XXX' WHERE session_id='".SESSIONID."'";
	
    return db::exec($sql);
	
  }

}