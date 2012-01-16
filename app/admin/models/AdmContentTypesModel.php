<?php

class AdmContentTypesModel extends BaseModel
{
  // Properties
  
  // Constructor
  public function __construct()
  {
  
    parent::__construct();
  
  }
  
  // Methods

  public function findItems($path)
  {
	
	$row = db::fetch("SELECT * FROM menu_items WHERE path='$path'");
	
	$result = db::fetchAll("SELECT * FROM menu_items WHERE child_of=".$row['id']." ORDER BY priority");
	
	return $result;
  
  }
  
  
  public function findContentTypes()
  {
	
	$result = db::fetchAll("SELECT * FROM content_types ORDER BY name");
	
	// Check and add permisions
	foreach($result as $row){
		$perms = $this->getContentTypePermisions($row['machine_name']);
		$return[$row['id']] = $row;
		$return[$row['id']]['perm'] = $perms;
	}
	
	return $return;
  
  }
  
  public function getContentTypePermisions($mach_name)
  {
  
    $role = Application::$logged['role'];
	
	if($role == 'admin'){
		$perm['view'] = 1;
		$perm['add'] = 1;
		$perm['edit'] = 1;
		$perm['delete'] = 1;
	}
	else{
		$row = db::fetch("SELECT * FROM permisions WHERE cont_mach_name='$mach_name'");
		
		$perm['view'] = $row[$role.'_view'];
		$perm['add'] = $row[$role.'_add'];
		$perm['edit'] = $row[$role.'_edit'];
		$perm['delete'] = $row[$role.'_delete'];
		
		// check for extra user's permisions
		if(Application::$logged['status']){
			$uid = Application::$logged['userid'];
			$row = db::fetch("SELECT * FROM users_permisions WHERE machine_name='$mach_name' AND uid=$uid");
			if($row){
				$perm['view'] = $row['view'];
				$perm['add'] = $row['add'];
				$perm['edit'] = $row['edit'];
				$perm['delete'] = $row['delete'];
			}
		}
	}
	
	return $perm;
  
  }
  
  public function find($machine_name)
  {
  
	$result = db::fetch("SELECT id, name FROM content_types WHERE machine_name='$machine_name'");
	
	return $result;
  
  }
  
  public function findOne($id)
  {
	
	$result = db::fetch("SELECT * FROM content_types WHERE id='$id'");
	
	return $result;
  
  }
  
  public function findAllFields($id)
  {
    // Count rows
	$query = db::fetchAll("SELECT * FROM content_type_fields WHERE content_type_id=$id");
	
	$num_rows = count($query);
	
	if($num_rows > 0){
	  return db::fetchAll("SELECT * FROM content_type_fields WHERE content_type_id=$id ORDER BY priority");
	}
	else{
	  return null;
	}
  
  }
  
  public function findField($id)
  {
  
    return db::fetch("SELECT * FROM content_type_fields WHERE id='$id'");
  
  }
  
  public function saveNewContentType($values)
  {
    // set POST values
	foreach($values as $key => $value){
	  $$key = $value;
	}
	// Remove all non-machine characters
	$machine_name = machineStr($machine_name);
	
	// New ID
    $result = db::fetch("SELECT MAX(id) AS id FROM content_types");
	$newid = $result['id']+1;
	
	$result = db::fetch("SELECT MAX(id) AS id FROM content_type_fields");
	$new_ct_id = $result['id']+1;
	
	//insert new permisions
	db::exec("INSERT INTO permisions (content, cont_mach_name, editor_view, editor_add, editor_edit, editor_delete, user_view, user_add, user_edit, user_delete, visitor_view, visitor_add, visitor_edit, visitor_delete)
	                                 VALUES ('$name', '$machine_name', 1,1,1,1,1,0,0,0,1,0,0,0)");
	
    //insert new content type
	db::exec("INSERT INTO `content_types` (`id`, `name`, `description`, `machine_name`) VALUES ($newid, '$name', '$description', '$machine_name')");
	
	// create new basic fields for this content type
	$attributes = serialize($this->parseAttributes('size:60;maxlength:128'));
	$sql = "INSERT INTO content_type_fields (id, type, label, attributes, content_type_id, priority, `default`, frm_name, required, field_type, machine_field_type, editable)
                               	VALUES
								($new_ct_id, 'text', 'Titulok', '$attributes', '$newid', '0', '', 'title', '1', 'Text', 'text', 0)";
	db::exec($sql);
	
	$new_ct_id = $new_ct_id+1;
	$attributes = serialize($this->parseAttributes('cols:50;rows:30;wrap:off;allowedtags:null'));
	db::exec("INSERT INTO content_type_fields (id, type, label, attributes, content_type_id, priority, `default`, frm_name, required, field_type, machine_field_type, editable)
                               	VALUES
								($new_ct_id, 'textarea', 'Telo', '$attributes', $newid, 2, '', 'body', 0, 'Textbox', 'textarea', 1)");
	
	$new_ct_id = $new_ct_id+1;
	$attributes = serialize($this->parseAttributes('size:60;maxlength:128'));
	db::exec("INSERT INTO content_type_fields (id, type, label, attributes, content_type_id, priority, `default`, frm_name, required, field_type, machine_field_type, editable, description)
                               	VALUES
								($new_ct_id, 'text', 'URL alias', '$attributes', $newid, 1, '', 'path_alias', 0, 'Text', 'text', 1, 'URL alias môže obsahovať iba písmená (bez diakritiky), číslice a pomlčky (namiesto medzier).')");
	
	// Create new Content type table
	if(DB_DRIVER == 'sqlite') $sql = "CREATE TABLE IF NOT EXISTS `$machine_name`
                                      (`id` INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
                                       `title` VARCHAR(128),
                                       `body` TEXT)";
	
	if(DB_DRIVER == 'mysql') $sql = "CREATE TABLE IF NOT EXISTS `$machine_name`
                                      (`id` INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
                                       `title` VARCHAR(128),
                                       `body` TEXT)";
			  
	return db::exec($sql);
	
  }
  
  public function saveContentType($values)
  {
    // set POST values
	foreach($values as $key => $value){
	  $$key = $value;
	}
	
	//update content type
	return db::exec("UPDATE content_types SET name='$name', description='$description' WHERE id=$id");
  }
  
  public function orderFields($post)
  {
	foreach($post['ids'] as $id){
	  $priority = $post["priority_$id"];
	  $result = db::exec("UPDATE content_type_fields SET priority='$priority' WHERE id=$id");
	}
    return $result;
  }
  
  public function getFieldTypes()
  {
  
    $result = db::fetchAll("SELECT * FROM basic_fields ORDER BY id");
	
	foreach($result as $row){
	  $ret[$row['id']] = $row['label'];	
	}

    return $ret;	
  }
  
  public function saveField($contid, $post)
  {
    // get table name
    $result = db::fetch("SELECT machine_name FROM content_types WHERE id=$contid");
	if($result){
	  $table = $result['machine_name'];
	}
	else{
	  return false;
	}
	
	// get values from basic fields table
	$basic_field_id = $post['fieldtype'];
	
	$bas_field = db::fetch("SELECT label, type, attributes, db_type, machine_type FROM basic_fields WHERE id=$basic_field_id");
	if(!$bas_field) return false;

	// get max priority value
	$res1 = db::fetch("SELECT MAX(id) AS id FROM content_type_fields");
	$res2 = db::fetch("SELECT MAX(priority) AS priority FROM content_type_fields WHERE content_type_id=$contid");
	if(!$res2) return false;
	$priority = $res2['priority']+1;
	$id = $res1['id']+1;
	
	// insert into content types table
	$type = $bas_field['type'];
	$label = $post['label'];
	$attributes = serialize($bas_field['attributes'] ? $this->parseAttributes($bas_field['attributes']) : array());
	$default = null;
	$frm_name = machineStr($post['frm_name']); // check if machine correct name
	$required = 0;
	$basic = 0;
        $editable = 1;
	$field_type = $bas_field['label'];
	$machine_type = $bas_field['machine_type'];
	$description = null;
	$content_label='';
	
	$sql = "INSERT INTO content_type_fields (id, type, label, attributes, content_type_id,
	                                         priority, `default`, frm_name, required,
                                                 basic, field_type, description, machine_field_type, content_label, editable)
                                                 VALUES ('$id', '$type', '$label', '$attributes',
                                                         '$contid', '$priority', '$default', '$frm_name',
                                                         '$required', '$basic', '$field_type', '$description', '$machine_type', '$content_label', $editable)";
	
	$query = db::exec($sql);
	if($query){
	
	}
	else{
	  return false;
	}
	
	// Do not alter table if type of field is file
	if($type != 'file'){
	  // Create new column in table
	  $db_type = $bas_field['db_type'];
	  if(DB_DRIVER == 'sqlite') $result = db::exec("ALTER TABLE `$table` ADD COLUMN `$frm_name` $db_type");
	  if(DB_DRIVER == 'mysql') $result = db::exec("ALTER TABLE `$table` ADD `$frm_name` $db_type");
	  
	  if($result){
	    return true;
	  }
	  else{
	    return false;
	  }
	}
	else{
	  return true;
	}
  
  }
  
  public function updateField($post)
  {
    $attributes = '';
    foreach($post as $key => $val){
	  if(substr($key, 0, 5) == 'attr_'){
	    // Remove attr_ from key string
		$key = substr($key, 5);
		//add attribute to attributes
		$attributes[$key] = $val;
	  }
	  $$key = $val;
	}
	//if atrributes, serialize them
	if($attributes) $attributes = serialize($attributes);
	
    if(!isset($required)) $required = 0;
	$default = preg_replace('/\r\n/', ';', $default);
	
	$sql = "UPDATE content_type_fields SET label='$label', description='$description', attributes='$attributes', `default`='$default', required='$required', content_label='$content_label' WHERE id=$id";
	
	$query = db::exec($sql);
	
	if($query){
	  return true;
	}
	else{
	  return false;
	}
  }
  
  public function deleteField($id)
  {
  
    $result = db::fetch("SELECT content_type_id, frm_name, type, machine_field_type FROM content_type_fields WHERE id=$id");
	$contid = $result['content_type_id'];
	$fieldname = $result['frm_name'];
	$machine_field_type = $result['machine_field_type'];
	$field_type = $result['type'];
	
	if($field_type == 'file'){
		if($machine_field_type == 'image'){
			$rows = db::fetchAll("SELECT image_name FROM content_images WHERE content_type_id=$contid");
			if($rows){
				foreach($rows as $row){
					unlink(WWW_DIR.'/images/'.$row['image_name']);
					unlink(WWW_DIR.'/images/thumb_'.$row['image_name']);
				}
			}
			return 'delImgs';
		}
		else{
			$rows = db::fetchAll("SELECT file_name FROM content_files WHERE content_type_id=$contid");
			if($rows){
				foreach($rows as $row){
					unlink(WWW_DIR.'/files/'.$row['file_name']);
				}
			}
			return 'delFiles';
		}
	}
	else{
    	$result = db::fetch("SELECT * FROM content_types WHERE id=$contid");
		$table = $result['machine_name'];
		
		if(DB_DRIVER == 'sqlite'){
		
			$fields = db::fetchAll("PRAGMA table_info($table)");
			
			unset($fields[0]); // unsets id
			unset($fields[1]); // unsets title
			unset($fields[2]); // unsets body
			foreach($fields as $idx => $field){
			  if($field['name'] == $fieldname){
				$tounset = $idx;
			  }
			}
			unset($fields[$tounset]); // unset unwanted
			
			//create temp table
		    $tempsql = "CREATE TABLE temp_$table 
												  (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
												   title VARCHAR(128),
												   body TEXT";
								
												   
			if($fields){
				$last = count($fields);
				$idx = 1;
				foreach($fields as $field){
				  if($idx == $last){
					$tempsql .= ', '.$field['name'].' '.$field['type'];
				  }
				  else{
					$tempsql .= ', '.$field['name'].' '.$field['type'];
				  }
				  $idx++;
				}
			}
			$tempsql .= ')';
			
			$copysql = "INSERT INTO temp_$table SELECT id, title, body";
			if($fields){
				$last = count($fields);
				$idx = 1;
				foreach($fields as $field){
				  if($idx == $last){
					$copysql .= ', '.$field['name'];
				  }
				  else{
					$copysql .= ', '.$field['name'];
				  }
				  $idx++;
				}
			}
			$copysql .= " FROM $table";
			
			$dropsql = "DROP TABLE $table";
			
			$altersql = "ALTER TABLE temp_$table RENAME TO $table";
			
			//$deletesql = "DELETE FROM content_type_fields WHERE id=$id";
			
			
			db::exec($tempsql);
			db::exec($copysql);
			db::exec($dropsql);
			db::exec($altersql);
			//db::exec($deletesql);
		
		}
		
		if(DB_DRIVER == 'mysql'){
			db::exec("ALTER TABLE `$table` DROP `$fieldname`");
		}
		
		return 'delField';
	}
	
  }
  
  public function deleteCTField($id){
  
    $deletesql = "DELETE FROM content_type_fields WHERE id=$id";
	$result = db::exec($deletesql);
	if($result)
	  return true;
	else
	  return false;
  
  }
  
  public function deleteImagesFromTable($id){
  
    $this->deleteCTField($id);
	$deletesql = "DELETE FROM content_images WHERE content_type_id=$id";
	$result = db::exec($deletesql);
	if($result)
	  return true;
	else
	  return false;
  
  }
  
  public function deleteFilesFromTable($id){
  
    $this->deleteCTField($id);
	$deletesql = "DELETE FROM content_files WHERE content_type_id=$id";
	$result = db::exec($deletesql);
	if($result)
	  return true;
	else
	  return false;
  
  }
  
  public function deleteContentType($post){
  
    $id = $post['id'];
	$table = $post['machine_name'];
	
	$res = db::exec("DELETE FROM permisions WHERE cont_mach_name='$table'");
	if($res) $res = db::exec("DELETE FROM content WHERE content_type_machine_name='$table'");
	if($res) $res = db::exec("DELETE FROM content_type_fields WHERE content_type_id=$id");
	if($res) $res = db::exec("DELETE FROM content_types WHERE id=$id");
	if($res) $res = db::exec("DROP TABLE IF EXISTS $table");
	
	return $res;
  
  }
  
  private function parseAttributes($attrs)
  {
    $attributes = explode(';', $attrs);
    
    foreach($attributes as $attribute) {
      list($idx, $value) = explode(':', $attribute);
      $ret[$idx] = $value;
    }
    
    return $ret;
  }
  
  function queryFetch($sql){
    $row = db::fetch($sql);
	
	return $row;
  }
  
  function queryFetchAll($sql){
    $rows = db::fetchAll($sql);
	
	return $rows;
  }
}
