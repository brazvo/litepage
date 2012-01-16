<?php
class NovinkyModel
{
  // Properties
  
  // Constructor
  public function __construct()
  {
  
  }
  
  // Methods

  public function find($id)
  {
    $row = db::fetch("SELECT * FROM hotnews WHERE id=$id");
	return $row;
  }
  
  public function findForPage()
  {
    $rows = db::fetchAll("SELECT * FROM hotnews ORDER BY datetime ".HN_ORDER." LIMIT ".HN_MESSAGES_ON_PAGE);
	return $rows;
  }
  
  public function findAll()
  {
    $rows = db::fetchAll("SELECT * FROM hotnews ORDER BY datetime ".HN_ORDER);
	return $rows;
  }
  
  public function save($post)
  {
    foreach($post as $key => $val){
	  $$key = $val;
	}
	$title = htmlMyEnts($title);
	$message = htmlMyEnts($message);
	$result = db::exec("UPDATE hotnews SET title='$title', message='$message' WHERE id=$id");
	return $result;
  }
  
  public function saveNew($post)
  {
    foreach($post as $key => $val){
	  $$key = $val;
	}
	$title = htmlMyEnts($title);
	$message = htmlMyEnts($message);
	$result = db::exec("INSERT INTO hotnews (title, message) VALUES ('$title', '$message')");
	return $result;
  }
  
  public function saveSettings($post)
  {
    unset($post['save']);
	unset($post['validate']);
	foreach($post as $key => $val){
	  $result = db::exec("UPDATE hotnews_settings SET value = '$val' WHERE frm_name = '$key'");
	}
	
	return $result;
  }
  
  public function delete($id)
  {
    $result = db::exec("DELETE FROM hotnews WHERE id=$id");
	return $result;
  }
  
  public function getSettings()
  {
    $rows = db::fetchAll("SELECT * FROM hotnews_settings");
	return $rows;
  }
}