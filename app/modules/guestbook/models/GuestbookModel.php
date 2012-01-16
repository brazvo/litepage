<?php
class GuestbookModel
{
  // Properties
  
  // Constructor
  public function __construct()
  {
  
  }
  
  // Methods

  public function find($id)
  {
    $row = db::fetch("SELECT * FROM guestbook WHERE id=$id");
	return $row;
  }
  
  public function findForPage()
  {
    $rows = db::fetchAll("SELECT * FROM guestbook ORDER BY datetime ".ORDER." LIMIT ".MESSAGES_PER_PAGE);
	return $rows;
  }
  
  public function findAll()
  {
    $rows = db::fetchAll("SELECT * FROM guestbook ORDER BY datetime ".ORDER);
	return $rows;
  }
  
  public function save($post)
  {
    foreach($post as $key => $val){
	  $$key = $val;
	}
	$name = htmlMyEnts($name);
	$subject = htmlMyEnts($subject);
	$message = htmlMyEnts($message);
	$result = db::exec("UPDATE guestbook SET name='$name', email='$email', subject='$subject', message='$message' WHERE id=$id");
	return $result;
  }
  
  public function saveNew($post)
  {
    foreach($post as $key => $val){
	  $$key = $val;
	}
	$name = htmlMyEnts($name);
	$subject = htmlMyEnts($subject);
	$message = htmlMyEnts($message);
	$result = db::exec("INSERT INTO guestbook (name, email, subject, message) VALUES ('$name', '$email', '$subject', '$message')");
	if(trim(NOTIFICATE) != ''){
	  $email ? $from = $email : $from = $name;
	  $mess = "<p>Do knihy návštev pribudol nový odkaz</p>";
	  $mess .= "<p>------------------------------------</p>";
	  if($email) $mess .= "<p>$email</p>";
	  if($subject) $mess .= "<p>$subject</p>";
	  $mess .= "<p>$message</p>";
	  Application::mailer($from, NOTIFICATE, $subject, $mess);
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
	  $result = db::exec("UPDATE guestbook_settings SET value = '$val' WHERE frm_name = '$key'");
	}
	
	return $result;
  }
  
  public function delete($id)
  {
    $result = db::exec("DELETE FROM guestbook WHERE id=$id");
	return $result;
  }
  
  public function getSettings()
  {
    $rows = db::fetchAll("SELECT * FROM guestbook_settings");
	return $rows;
  }
}