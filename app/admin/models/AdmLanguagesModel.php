<?php
class AdmLanguagesModel extends BaseModel
{
  // Properties
  
  // Constructor
  public function __construct()
  {
  
    parent::__construct();
  
  }
  
  // Methods

  public function find($id)
  {
    $row = db::fetch("SELECT * FROM languages WHERE langid='$id'");
	if(!$row) return false;
	return $row;
  }
  
  public function findAll()
  {
    $rows = db::fetchAll("SELECT * FROM languages ORDER BY name");
	if(!$rows) return false;
	return $rows;
  }
  
  
  public function save($post)
  {
    foreach($post as $key => $val){
		$$key = $val;
	}
	
	return db::exec("UPDATE languages SET name='$name', eng_machine_name='$eng_machine_name', main_page_path='$main_page_path' WHERE langid='$langid'");
  }
  
  public function saveNew($post)
  {
	foreach($post as $key => $val){
		$$key = $val;
	}
	
	return db::exec("INSERT INTO languages VALUES ('$langid', '$name', '$eng_machine_name', '$main_page_path',0 , 1, 0)");
  }
  
  public function saveActive($post)
  {
	$ids = explode(':', $post['ids']);
	unset($post['ids']);
	unset($post['save']);

	foreach($ids as $id){
		if(!isset($post[$id.'_active'])){
			if($post[$id.'_main_lang']){
				$active = 1;
				$_SESSION['error'] = LANG_ADMIN_ERR_THREE;
			}
			else{
				$active = 0;
			}
		}
		else{
			$active = $post[$id.'_active'];
		}
		$return = db::exec("UPDATE languages SET active='$active' WHERE langid='$id'");
	}
	
	return $return;
  }
  
  public function delete($id)
  {
    $row = db::exec("DELETE FROM languages WHERE langid='$id'");
	return $row;
  }
  
  public function setDefault($id)
  {
	$row = db::fetch("SELECT * FROM languages WHERE langid='$id'");
	if(!$row) return false;
	
	db::exec("UPDATE languages SET main_lang='0'");
	db::exec("UPDATE languages SET main_lang='1' WHERE langid='$id'");
	
	// set code for Application
	Application::$language['code'] = $id;
	
	return true;
	
  }
}