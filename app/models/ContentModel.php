<?php

class ContentModel extends BaseModel
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
    /*
		If the logged person has role - user, he must be owner to edit the content
	*/
	
        $row = db::fetch("SELECT * FROM content WHERE id=$id");
        
	if(Application::$logged['role'] == 'user'){
		if($row['uid'] == Application::$logged['userid']){
     		$owner = true;
		}
		else{
    		$owner = false;
		}
	}
	else{
		// if not role - user, it does not matter, so...
		$owner = true;
	}
	
	if($row){
		$tablename = $row['content_type_machine_name'];
		$record_id = $row['content_id'];
		
		$result = db::fetch("SELECT * FROM $tablename WHERE id=$record_id");
		
		$result['content_owner'] = $owner;
		$result['lang'] = $row['lang'];
		$result['module'] = $row['module'];
		$result['table'] = $row['content_type_machine_name'];
                $result['content_id'] = $row['content_id'];
		
		return $result;
	}
	else{
		return false;
	}
  
  }
  
  public function findAllOfKind($table)
  {
    
	$result = db::fetchAll("SELECT * FROM `$table`");

	return $result;
  
  }
  
  public function getFields($id)
  {
    $row = db::fetch("SELECT * FROM content WHERE id=$id");
		
	$table = $row['content_type_machine_name'];
	$record_id = $row['content_id'];
	
	$flds = db::fetchAll("SELECT type, frm_name, machine_field_type, content_label, attributes FROM content_type_fields WHERE content_type_id=".$row['content_type_id']." ORDER BY priority");
	
	foreach($flds as $idx => $field){
	  if($field['frm_name'] == 'path_alias'){
	    $tounset = $idx;
	  }
	}
	unset($flds[$tounset]);
	
	foreach($flds as $field){
	  
	  $fieldname = $field['frm_name'];
	  
	  //set attributes
	  if($field['attributes']) {
		$attrs = @unserialize($field['attributes']);
		// BACKWARD COMPATIBILITY
		if(!is_array($attrs)) $attrs = $this->parseAttributes($field['attributes']);
		$return[$fieldname]['attrs'] = $attrs;
	  }
	  

	  if($field['type'] == 'file'){
	    $return[$fieldname]['value'] = $field['machine_field_type'];
	  }
	  else{
	    $record = db::fetch("SELECT $fieldname FROM $table WHERE id=$record_id");
	    $return[$fieldname]['value'] = $record[$fieldname];
		$return[$fieldname]['label'] = $field['content_label'];
		$return[$fieldname]['class'] = $fieldname;
	  }
	
	}
	
	return $return;
	
  }
  
  public function findImages($content_id, $order_by)
  {
    return db::fetchAll("SELECT * FROM content_images WHERE content_id=$content_id ORDER BY $order_by");
  }
  
  public function findFiles($content_id, $order_by)
  {
    return db::fetchAll("SELECT * FROM content_files WHERE content_id=$content_id ORDER BY $order_by");
  }
  
  public function getContentTypeTitle($mach_name)
  {
    $row = db::fetch("SELECT `name` FROM content_types WHERE `machine_name`='$mach_name'");
	return $row['name'];
  }
  
  public function getContentMachineName($id)
  {
	$row = db::fetch("SELECT content_type_machine_name AS ctmn FROM content WHERE id=$id");
	
	return $row['ctmn'];
  
  }
  
  public function checkTable($name)
  {
    if(DB_DRIVER == 'sqlite'){
		$row = db::fetch("SELECT name FROM sqlite_master WHERE type='table' AND name='$name'");
	}
	if(DB_DRIVER == 'mysql'){
		$row = db::fetch("SHOW TABLES WHERE Tables_in_".MYSQL_DB."='$name'");
	}
	if($row){
		return $name;
	}
	else{
		return false;
	}
  }
  
  
  public function getNewContentId()
  {
	  // get MAx id in content table
	  $row = db::fetch("SELECT MAX(id) AS id FROM content");
	  return $row['id']+1;
  }
  
  
  public function getContentId($cont_type, $content_id)
  {
	  // get MAx id in content table
	  $row = db::fetch("SELECT id FROM content WHERE content_type_machine_name = %v AND content_id = %i", $cont_type, $content_id);
	  return $row['id'];
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