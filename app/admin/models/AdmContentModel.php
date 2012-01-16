<?php

class AdmContentModel extends BaseModel
{
  // Properties
	/**
	 * @var string
	 * gallery dir
	 */
	private $dir;
	
	/**
	 * @var int
	 * default image size
	 */
	private $defImgSize;
	
	/**
	 * @var int
	 * default icon size
	 */
	private $defIconSize;
	
	/**
	 * @var string
	 * thumb creating
	 */
	private $thumbCreate;
  
  // Constructor
  public function __construct()
  {
  
    parent::__construct();
  
  }
  
  // Methods

  public function find($id)
  {
	/*
		If the logged person has role - user,
		then we must select only his content.
		If the user is trying to get a content that he does not own,
		He will get error messgage.
	*/
	$uid = Application::$logged['userid'];
	if($id){
	  if(Application::$logged['role'] == 'user'){
		$row = db::fetch("SELECT * FROM content WHERE id=$id AND uid=$uid");
		if(!$row){
			redirect('error/default/403');exit;
		}
	  }
	  else{
	    $row = db::fetch("SELECT * FROM content WHERE id=$id");
	  }
	  
	  if($row){
	    return $row;
	  }
	  else{
		  Application::setError('Záznam s týmto identifikátorom neexistuje.');
		  return false;
	  }
		
	}
	else{
	
	  Application::setError('Chybne zadaný identifikátor.');
	  
	  return false;
	
	}
  
  }
  
  public function findItems($path)
  {
	
	$row = db::fetch("SELECT * FROM menu_items WHERE path='$path'");
	
	$result = db::fetchAll("SELECT * FROM menu_items WHERE child_of=".$row['id']." ORDER BY priority");
	
	return $result;
  
  }
  
  
  public function findContents()
  {
	/*
	   If role of the logged person is user,
	   then we will return record created only by this user.
	*/
	$uid = Application::$logged['userid'];
	//check for filter
	$filters = db::fetch("SELECT * FROM filter WHERE uid=$uid");
	if($filters){
		foreach($filters as $key => $val){
			$$key = $val;
		}
	}
	
	$and = '';
	if(isset($content_type) && $content_type){
		$and = "content_type_machine_name='$content_type'";
	}
	if(isset($language) && $language){
		$and = "lang='$language'";
	}
	if((isset($content_type) && $content_type) && (isset($language) && $language)){
		$and = "content_type_machine_name='$content_type' AND lang='$language'";
	}
	
	if(isset($category) && $category){
		$cat_content = db::fetchAll("SELECT * FROM categories_relations WHERE cat_item_id='$category'");
	}
	else{
		$cat_content = null;
	}
	
	
	if(Application::$logged['role'] == 'user'){
		if($and) $and = "AND ".$and;
		$rows = db::fetchAll("SELECT * FROM content WHERE uid=$uid $and ORDER BY last_update DESC");
		if($cat_content){
		    $ret=null;
			foreach($rows as $row){
				foreach($cat_content as $cont){
					if($row['id'] == $cont['cont_id']) $ret[] = $row;
				}
			}
			$result = $ret;
		}
		else{
			$result = $rows;
		}
	}
	else{
		if($and) $and = "WHERE ".$and;
		$sql = "SELECT * FROM content $and ORDER BY last_update DESC";
		
		$rows = db::fetchAll($sql);
		if($cat_content){
			$ret = null;
			foreach($rows as $row){
				foreach($cat_content as $cont){
					if($row['id'] == $cont['cont_id']) $ret[] = $row;
				}
			}
			$result = $ret;
		}
		else{
			$result = $rows;
		}
	}

	if($result){
		// Check and add permisions
		foreach($result as $row){
			$perms = $this->getContentTypePermisions($row['content_type_machine_name']);
			$user = $this->getUser($row['uid']);
			$return[$row['id']] = $row;
			$return[$row['id']]['perm'] = $perms;
			$return[$row['id']]['user'] = $user;
		}
		
		return $return;
	}
	else{
		return false;
	}
  
  }
  
  /**
   * Returns Content Only for Logged user
   * @return type array
   */
  public function findUsersContents()
  {

	$uid = Application::$logged['userid'];
	//check for filter
	$filters = db::fetch("SELECT * FROM filter WHERE uid=$uid");
	if($filters){
		foreach($filters as $key => $val){
			$$key = $val;
		}
	}
	
	$and = '';
	if(isset($content_type) && $content_type){
		$and = "content_type_machine_name='$content_type'";
	}
	if(isset($language) && $language){
		$and = "lang='$language'";
	}
	if((isset($content_type) && $content_type) && (isset($language) && $language)){
		$and = "content_type_machine_name='$content_type' AND lang='$language'";
	}
	
	if(isset($category) && $category){
		$cat_content = db::fetchAll("SELECT * FROM categories_relations WHERE cat_item_id='$category'");
	}
	else{
		$cat_content = null;
	}

	if($and) $and = "AND ".$and;
	$rows = db::fetchAll("SELECT * FROM content WHERE uid=$uid $and ORDER BY last_update DESC");
	if($cat_content){
		$ret=null;
		foreach($rows as $row){
			foreach($cat_content as $cont){
				if($row['id'] == $cont['cont_id']) $ret[] = $row;
			}
		}
		$result = $ret;
	}
	else{
		$result = $rows;
	}

	if($result){
		// Check and add permisions
		foreach($result as $row){
			$perms = $this->getContentTypePermisions($row['content_type_machine_name']);
			$user = $this->getUser($row['uid']);
			$return[$row['id']] = $row;
			$return[$row['id']]['perm'] = $perms;
			$return[$row['id']]['user'] = $user;
		}
		
		return $return;
	}
	else{
		return false;
	}
  
  }
  
  public function getUser($id)
  {
	
	$result = db::fetch("SELECT * FROM users WHERE id=$id");
	if($result){
		$name = $result['name'].' '.$result['surname'].' -&nbsp;<i>'.$result['role'].'</i>';
		return $name;
	}
	
	return false;
  
  }
  
  public function getFormFields($cont_type_id)
  {
	
	$result = db::fetchAll("SELECT * FROM content_type_fields WHERE content_type_id=$cont_type_id ORDER BY priority");
	
	return $result;
  
  }
  
  public function getContentMachineName($id)
  {
	
	
	$row = db::fetch("SELECT content_type_machine_name AS ctmn FROM content WHERE id=$id");
	
	return $row['ctmn'];
  
  }
  
  public function getContentId($id)
  {
	
	
	$row = db::fetch("SELECT content_id FROM content WHERE id=$id");
	
	return $row['content_id'];
  
  }
  
  public function getContentTypes()
  {
	$rows = db::fetchAll("SELECT * FROM content_types ORDER BY name");
	if(!$rows) return false;
	foreach($rows as $row){
		$return[$row['machine_name']] = $row['name'];
	}
	
	return $return;
  
  }
  
  public function getCategories()
  {
	
	if(!isset(Application::$activeModules['categories'])) return false;
	
	$rows = db::fetchAll("SELECT * FROM categories_items ORDER BY cat_id, title");
	if(!$rows) return false;
	foreach($rows as $row){
		$return[$row['id']] = $row['title'];
	}
	
	return $return;
  
  }
  
  public function getFilterValues($uid)
  {
	
	$row = db::fetch("SELECT * FROM filter WHERE uid=$uid");
	if(!$row) return false;
	
	return $row;
  
  }
  
  public function getLanguages()
  {
	
	$rows = db::fetchAll("SELECT * FROM languages ORDER BY langid");
	if(!$rows or count($rows) == 1) return false;
	foreach($rows as $row){
		$return[$row['langid']] = $row['name'];
	}
	
	return $return;
  
  }
  
  public function getNewContentId()
  {
	  // get MAx id in content table
	  $row = db::fetch("SELECT MAX(id) AS id FROM content");
	  return $row['id']+1;
  }
  
  public function setFilter($post)
  {
	foreach($post as $key => $val){
		$$key = $val;
	}
	if(!isset($content_type)) $content_type = 0;
	if(!isset($category)) $category = 0;
	if(!isset($language)) $language = 0;
	
	if($content_type or $category or $language){
		$row = $this->getFilterValues($uid);
		if($row){
			db::exec("UPDATE filter SET content_type='$content_type', category='$category', language='$language' WHERE uid=$uid");
		}
		else{
			db::exec("INSERT INTO filter VALUES ($uid, '$content_type', '$category', '$language')");
		}
	}
	else{
		db::exec("DELETE FROM filter WHERE uid=$uid");
	}
  }
  
  public function resetFilter($post)
  {
	$uid = $post['uid'];
	db::exec("DELETE FROM filter WHERE uid=$uid");
  }
  
  public function getContentValues($id)
  {
	
	$row = db::fetch("SELECT * FROM content WHERE id=$id");
	$table = $row['content_type_machine_name'];
	$tblid = $row['content_id'];
	$lang = $row['lang'];
	
	if($table && $id){
	  
	  
	    $res1 = db::fetch("SELECT * FROM $table WHERE id=$tblid");
		$res2 = db::fetch("SELECT path_alias FROM content WHERE content_type_machine_name='$table' AND content_id=$tblid");
	    if($res1 && $res2){
		  $ret = array_merge($res1, $res2);
		  $ret['lang'] = $lang;
		  return $ret;
		}
		else{
		  Application::setError('Záznam s týmto identifikátorom neexistuje.');
		  return false;
		}
	
		
	}
	else{
	
	  Application::setError('Chybne zadaný identifikátor.');
	  
	  return false;
	
	}
  
  }
  
  public function saveContent($values)
  {
	$cont_id = $values['id'];
	
	//check for double path aliases
	if(trim($values['path_alias']) != '' && $values['path_alias'] != $values['old_path_alias']){
	  $result = $this->checkForDoublePaths($values['path_alias']);
	}
	else{
	  $result = false;
	}
	
	if(!isset($_SESSION['destination'])){
		if(trim($values['path_alias']) != '') {
                    Application::$pathRequest = $values['path_alias'];
                }
                else {
                    Application::$pathRequest = 'content/show/'.$cont_id;
                }
        
	}
	else{
		//echo $_SESSION['destination'].'<br>'.$values['old_path_alias']; exit;
                if($_SESSION['destination'] == 'admin/content/list' or $values['path_alias'] == $values['old_path_alias']) {
                    Application::$pathRequest = $_SESSION['destination'];
                }
                else {
                    if(trim($values['path_alias']) != '') {
                        Application::$pathRequest = $values['path_alias'];
                    }
                    else {
                        Application::$pathRequest = 'content/show/'.$cont_id;
                    }
                }

	}
	
	// if true return with error
	if($result){
		Application::setError('Zadaný URL alias <i>'.$values['path_alias'].'</i> už existuje.');
		return false;
	}
	
	//Check if selected self menu item
	list($menu_id, $child_of) = explode(':', $values['menu_items']);
	$result = db::fetch("SELECT * FROM menu_items WHERE content_id='$cont_id' AND menu_id='$menu_id' AND id='$child_of'");
	// if true return with error
	if($result){
		Application::setError('Nie je možné zvoliť ako nadradenú položku vlastnú položku.');
		return false;
	}
	
	// Update or delete images;
	if(isset($values['image_ids'])){
	  // get images IDs
	  $ids = explode(':', $values['image_ids']);
	  foreach($ids as $id){
	    //check if to delete
		if(isset($values[$id.'__delete'])){
			unlink(WWW_DIR.'/images/thumb_'.$values[$id.'__image_name']);
			unlink(WWW_DIR.'/images/'.$values[$id.'__image_name']);
			db::exec("DELETE FROM content_images WHERE id=$id");
		}
		// or save changes
		else{
			$description = $values[$id.'__description'];
			$priority = $values[$id.'__priority'];
			db::exec("UPDATE content_images SET description='$description', priority='$priority' WHERE id=$id");
		}
	  }
	}
	
	// Update or delete files;
	if(isset($values['file_ids'])){
	  // get images IDs
	  $ids = explode(':', $values['file_ids']);
	  foreach($ids as $id){
	    //check if to delete
		if(isset($values[$id.'__file_delete'])){
			unlink(WWW_DIR.'/files/'.$values[$id.'__file_name']);
			db::exec("DELETE FROM content_files WHERE id=$id");
		}
		// or save changes
		else{
			$description = $values[$id.'__file_description'];
			$priority = $values[$id.'__file_priority'];
			db::exec("UPDATE content_files SET description='$description', priority='$priority' WHERE id=$id");
		}
	  }
	}
	
	$row = db::fetch("SELECT * FROM content WHERE id=$cont_id");
	$table= $row['content_type_machine_name'];
	$id = $row['content_id'];
	
	if($table && $id){
	
	  $cols = db::fetch("SELECT * FROM $table");
	  
	  foreach($cols as $key => $val){
	    $columns[] = $key;
	  }
	  
	  for($i=0; $i < count($columns); $i++){
	    if($columns[$i] != 'id'){ // do not accept id column
		  // serialize if is array
	      if( is_array($values[$columns[$i]]) ) $values[$columns[$i]] = serialize ( $values[$columns[$i]] );
		  if(get_magic_quotes_gpc()){
		    $insvalue = str_replace("'", "&#039;", $values[$columns[$i]]);
		  }
		  else{
		    $insvalue = addslashes(str_replace("'", "&#039;", $values[$columns[$i]]));
		  }
		  $result = db::exec("UPDATE $table SET `".$columns[$i]."`='$insvalue' WHERE `id`='$id'");
	      if($result){
		    $error = false;
		  }
          else{
			$error = true;
			break;
		  }		  
		}
	  }
	  
	  if(!$error){
		//UPDATE MENUS
		if(trim($values['menu_title']) != ''){
			
			trim($values['path_alias']) !='' ? $path = $values['path_alias'] : $path = "content/show/$cont_id";
			$row = db::fetch("SELECT * FROM menu_items WHERE module='content' AND content_id='$cont_id'");
			if($row){
				db::exec("UPDATE menu_items SET title='".$values['menu_title']."', path='$path', menu_id='$menu_id', child_of='$child_of' WHERE module='content' AND content_id='$cont_id'");
			}
			else{
				db::exec("INSERT INTO menu_items VALUES (null, '".$values['menu_title']."', '$path', 1, 0, 0, '$child_of', '$menu_id', '".$values['menu_title']."', '', '$cont_id', 'content')");
			}
		}
		else{
			db::exec("DELETE FROM menu_items WHERE module='content' AND content_id='$cont_id'");
		}
		
		//UPDATE CATEGORIES
		if(isset($values['cat_id'])){
			// check if exists
			$row = db::fetch("SELECT * FROM categories_relations WHERE cont_id=$cont_id");
			if($row){
				db::exec("UPDATE categories_relations SET cat_id=".$values['cat_id'].", cat_item_id=".$values['cat_items']." WHERE cont_id=$cont_id");
			}
			else{
				db::exec("INSERT INTO categories_relations VALUES (".$values['cat_id'].", ".$values['cat_items'].", $cont_id, 0)");
			}
		}
		//UPDATE LANGUAGE
		if(isset($values['lang_code'])){
			$lang = $values['lang_code'];
		}
		else{
			$lang = 'none';
		}
		//UPDATE content table
		$timestamp = date("Y-m-d H:i:s");
		if(get_magic_quotes_gpc()){
		    $values['title'] = str_replace("'", "&#039;", $values['title']);
		}
		else{
		    $values['title'] = addslashes(str_replace("'", "&#039;", $values['title']));
		}
		$result = db::exec("UPDATE content SET content_title='".$values['title']."', path_alias='".$values['path_alias']."', last_update='$timestamp', edit_uid=".Application::$logged['userid'].", lang='$lang' WHERE content_type_machine_name='$table' AND content_id=$id");
		if(trim($values['path_alias'] != '')){
		  $row = db::fetch("SELECT * FROM path_aliases WHERE module='content' AND mod_id=$cont_id");
		  if($row){
		    db::exec("UPDATE path_aliases SET path_alias='".$values['path_alias']."' WHERE id=".$row['id']);
		  }
		  else{
		    db::exec("INSERT INTO path_aliases (path_alias, url, module, mod_id) VALUES ('".$values['path_alias']."', 'content/show/$cont_id', 'content', $cont_id)");
		  }
		}
		else{
		  db::exec("DELETE FROM path_aliases WHERE module='content' AND mod_id=$cont_id");
		}
		
		if($result)
		  Application::setMessage('Zmeny boli uložené.');
		  return true;
      }
      else{
	    Application::setError('Pri ukladaní obsahu došlo k chybe.');
		return false;
	  }
	}
	else{
	
	  Application::setError('Chybne zadaný identifikátor.');
	  
	  return false;
	
	}
	
	
  }
  
  
  public function saveNewContent($values)
  {
	
        $files = $values['FILES']; unset($values['FILES']);
	
        $table = $values['id'];
	//check for double path aliases
	trim($values['path_alias']) != '' ? $result = $this->checkForDoublePaths($values['path_alias']) : $result = false;
	// if true return with error
	if($result){
		Application::setError('Zadaný URL alias <i>'.$values['path_alias'].'</i> už existuje.');
		return false;
	}
	
	if($table){
	
	  // get Max id
	  $actual = db::fetch("SELECT MAX(id) as id FROM $table");
	  $newid = $actual['id']+1;
	  
	  // get MAx id in content table
	  $new_cont_id = $this->getNewContentId();
	  
	  //get Content Type Values
	  $cont_type = db::fetch("SELECT * FROM content_types WHERE machine_name='$table'");
	  
	  if(DB_DRIVER == 'sqlite'){
		  //get Field names
		  $cols = db::fetchAll("PRAGMA table_info($table)");
		  
		  foreach($cols as $val){
			$columns[] = $val['name'];
		  }
	  }
	  
	  if(DB_DRIVER == 'mysql'){
		$cols = db::getTableFields($table);

		foreach($cols as $key => $val){
			$columns[] = $key;
		}
	  }
	  
	  $sql = "INSERT INTO $table ";
	  $sqlfields = "(`id`, ";
	  $sqlvalues = "VALUES ($newid, ";
	  $last = count($columns)-1;
	  $idx = 1;
	  foreach($columns as $field){
	    if($field != 'id'){ // do not accept id column
			// serialize if is array
			if( is_array($values[$field]) ) $values[$field] = serialize ( $values[$field] );
			//insert slashes
			if(get_magic_quotes_gpc()){
		      $insvalue = str_replace("'", "&#039;", $values[$field]);
		    }
		    else{
		      $insvalue = addslashes(str_replace("'", "&#039;", $values[$field]));
		    }
		  
			if($idx == $last){
			  $sqlfields .= "`$field`) ";
			  $sqlvalues .= "'$insvalue')";
			}
			else{
			  $sqlfields .= "`$field`, ";
			  $sqlvalues .= "'$insvalue', ";
			}
			$idx++;
		}
		
	  }
	  //fasten SQL
	  $sql = $sql . $sqlfields . $sqlvalues;
	  
	  $result = db::exec($sql);
	  if($result){
		$error = false;
	  }
      else{
		$error = true;
	  }
	  
	  //INSERT LANGUAGE
	  if(isset($values['lang_code'])){
	  	$lang = $values['lang_code'];
	  }
	  else{
	  	$lang = 'none';
	  }
 	 
      	  
	  if(!$error){
        //INSERT INTO content table
		$timestamp = date("Y-m-d H:i:s");
		if(get_magic_quotes_gpc()){
		    $values['title'] = str_replace("'", "&#039;", $values['title']);
		}
		else{
		    $values['title'] = addslashes(str_replace("'", "&#039;", $values['title']));
		}
		$sql = "INSERT INTO content (`id`, `content_type_id`, `content_type_name`, `content_title`,
		                            `path_alias`, `last_update`, `content_type_machine_name`, `content_id`, `uid`, `edit_uid`, `lang`, `created`) 
									VALUES 
									('$new_cont_id', '".$cont_type['id']."', '".$cont_type['name']."', '".$values['title']."',
												          '".$values['path_alias']."', '$timestamp', '$table', $newid, ".Application::$logged['userid'].", ".Application::$logged['userid'].", '$lang', '$timestamp')";
		
 	    $result = db::exec($sql);
		
		//UPDATE CATEGORIES
		if(isset($values['cat_id'])){
			$cat_id = $values['cat_id'];
			$cat_item_id = $values['cat_items'];
			db::exec("INSERT INTO categories_relations VALUES ($cat_id, $cat_item_id, $new_cont_id, 0)");
		}
		
		if($files && $files[$values['image_frm_name']]['name']){
			
			if($files[$values['image_frm_name']]['size'] > $values['img_max_file_size']){
				Application::setError('Veľkosť nahrávaného súboru je väčšia ako maximálne povolená veľkosť súboru.');
				return false;
			}
			// Check for upload errors
			if($error = $this->checkUploadErrors($files[$values['image_frm_name']])){
				Application::setError($error);
				return false;
			}
		
			// everything ok
			if(isset($files[$values['image_frm_name']]) && $values['image_machine_type'] == 'image'){
			    $this->dir =  WWW_DIR.'/images/';
			    $this->defImgSize = $values['preview_size'];
			    $this->defIconSize = $values['icon_size'];
			    $this->thumbCreate = $values['thumb_create'];
				
				$image = $files[$values['image_frm_name']];
				
				// IE work-arround for JPEG image
				if($image['type'] == 'image/pjpeg'){
					$image['type'] = 'image/jpeg';
				}
				
				if($image['name'] != ''){
				  if($image['type'] == 'image/gif' or $image['type'] == 'image/jpeg'){
				 
					$image = $this->upload($image);
					if(!$image){
						Application::setError('Obrazok sa nepodarilo nahrať.');
						return false;
					}
				  }
				  else {
					Application::setError('Súbor obrázku musí byť JPG alebo GIF.');
					return false;
				  }
				}
				else{
				  $image = false;
				}
				
				if($image){
				  $image_name = $image;
				  $content_type_id = $cont_type['id'];
				  $id = $new_cont_id;
				  $priority = 0;
				  $timestamp = date("Y-m-d H:i:s");
				  db::exec("INSERT INTO content_images VALUES (null, $content_type_id, '$image_name', '', '$timestamp', $id, $priority)");
				  
				}
			}

			}
		
		if($files && $files[$values['file_frm_name']]['name']){
		    // set file extension
			$file_type = end(explode(".", strtolower($files[$values['file_frm_name']]['name'])));
			// check for allowed file type
			$allowedExtensions = array("txt","csv","htm","html","xml","css","doc","docx","xls","rtf","ppt","pdf","swf","flv","avi",
				                       "wmv","mov","jpg","jpeg","gif","png","zip","rar","gz","mp3","wma","wav","ods","odt");
			
			$error = false;
			if(!in_array($file_type, $allowedExtensions)){
				Application::setError('Pokúšate sa nahrať súbor nepovoleného typu.');
				return false;
			}
			if($files[$values['file_frm_name']]['size'] > $values['file_max_file_size']){
				Application::setError('Veľkosť nahrávaného súboru je väčšia ako maximálne povolená veľkosť súboru.');
				return false;
			}
			// Check for upload errors
			if($error = $this->checkUploadErrors($files[$values['file_frm_name']])){
				Application::setError($error);
				return false;
			}
		
			// EVERYTHINGs OK
			if(isset($files[$values['file_frm_name']]) && $values['file_machine_type'] == 'file'){
				$this->dir =  WWW_DIR.'/files/';
				
				$file = $files[$values['file_frm_name']];
				
				if($file['name'] != ''){
				  $filename = $this->uploadFile($file);
				  if(!$filename){
					Application::setError('Súbor sa nepodarilo nahrať.');
					return false;
				  }
				}
				else{
				  $filename = false;
				}
				
				if($filename){
				  $file_name = $filename;
				  $content_type_id = $cont_type['id'];
				  $id = $new_cont_id;
				  $priority = 0;
				  $timestamp = date("Y-m-d H:i:s");
				  db::exec("INSERT INTO content_files VALUES (null, $content_type_id, '$file_name', '', '$timestamp', $id, $priority, '$file_type')");
				  
				}
			}
		
		
		}
		//CREATE PATH ALIAS
		if(trim($values['path_alias']) != ''){
		  db::exec("INSERT INTO path_aliases (path_alias, url, module, mod_id) VALUES ('".$values['path_alias']."', 'content/show/$new_cont_id', 'content', $new_cont_id)");
		}
		//CREATE MENU ITEM
		if(trim($values['menu_title']) != ''){
			list($menu_id, $child_of) = explode(':', $values['menu_items']);
			trim($values['path_alias']) !='' ? $path = $values['path_alias'] : $path = "content/show/$new_cont_id";
			db::exec("INSERT INTO menu_items VALUES (null, '".$values['menu_title']."', '$path', 1, 0, 0, '$child_of', '$menu_id', '".$values['menu_title']."', '', '$new_cont_id', 'content')");
		}
		
		if($result)
		  Application::setMessage('Obsah bol uložený.');
		  return true;
      }
      else{
	    Application::setError('Pri ukladaní obsahu došlo k chybe.');
		return false;
	  }
	}
	else{
	
	  Application::setError('Chybne zadaný identifikátor.');
	  
	  return false;
	
	}
  }
  
  
  public function delete($id)
  {
	
	// Check if both are set
	if($id){
	
	  // check if existsts
	  db::exec("DROP TABLE IF EXISTS temp_content");
	  db::exec("CREATE TABLE IF NOT EXISTS temp_content AS SELECT * FROM content");
	  $row = db::fetchAll("SELECT * FROM temp_content WHERE id=$id");
	  $exists = count($row);
	  if($exists){
	    $tblid = $row[0]['content_id'];
	    $table = $row[0]['content_type_machine_name'];
	    db::exec("DELETE FROM path_aliases WHERE module='content' AND mod_id=$id");
	    $res = db::exec("DELETE FROM $table WHERE id=$tblid");
	  }
	  if(!$exists){
		$return['message'] = 'Snažíte sa vymazať neexistujúci záznam.';
	    $return['status'] = false;
	  }
	  elseif(!$res){
		$return['message'] = 'Záznam z tabulky '.$table.' sa nepodarilo vymazať.';
	    $return['status'] = false;
	  }
	  else{
	    $rows = db::fetchAll("SELECT image_name FROM content_images WHERE content_id=$id");
		if($rows){
		  foreach($rows as $row){
		    unlink(WWW_DIR.'/images/'.$row['image_name']);
			unlink(WWW_DIR.'/images/thumb_'.$row['image_name']);
		  }
		}
		$rows = db::fetchAll("SELECT file_name FROM content_files WHERE content_id=$id");
		if($rows){
		  foreach($rows as $row){
		    unlink(WWW_DIR.'/files/'.$row['file_name']);
		  }
		}
	    $return['message'] = '';
		$return['status'] = true;
	  }
	  		
	}
	else{
	
	  $return['message'] = 'Chybne zadaný identifikátor.';
	  $return['status'] = false;
	
	}
	
	return $return;
  
  }
  
  public function deleteCont($id){
	db::exec("DELETE FROM content WHERE id=$id");
	db::exec("DELETE FROM content_images WHERE content_id=$id");
	db::exec("DELETE FROM content_files WHERE content_id=$id");
	
	if(Application::$activeModules['categories']) db::exec("DELETE FROM categories_relations WHERE cont_id=$id");
	$return['message'] = 'Záznam bol vymazaný.';
	$return['status'] = true;
	return $return;
  }
  
  public function getImages($id, $order_by){
  
	return db::fetchAll("SELECT * FROM content_images WHERE content_id=$id ORDER BY $order_by");
  
  }
  
  public function getFiles($id, $order_by){
  
	return db::fetchAll("SELECT * FROM content_files WHERE content_id=$id ORDER BY $order_by");
  
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
  
  public function uploadImage($files, $post){
  
	$row = db::fetch("SELECT frm_name FROM content_type_fields WHERE id=".$post['image_content_type_field_id']);
	$fileElementName = $row['frm_name'];
	
	if($files[$fileElementName]['size'] > $post['img_max_file_size']){
    	$error = 'Veľkosť nahrávaného súboru je väčšia ako maximálne povolená veľkosť súboru.';
		return array('status'=>false, 'error'=>$error);
	}
    // Check for upload errors
	if($error = $this->checkUploadErrors($files[$fileElementName])){
		return array('status'=>false, 'error'=>$error);
	}
	
	// everything ok
	$this->dir =  WWW_DIR.'/images/';
	$this->defImgSize = $post['preview_size'];
	$this->defIconSize = $post['icon_size'];
	$this->thumbCreate = $post['thumb_create'];
	
	if(isset($files[$fileElementName])){
		$image = $files[$fileElementName];
		
		// IE work-arround for JPEG image
		if($image['type'] == 'image/pjpeg'){
			$image['type'] = 'image/jpeg';
		}
		
		if($image['name'] != ''){
		  if($image['type'] == 'image/gif' or $image['type'] == 'image/jpeg'){
			$image = $this->upload($image);
			if(!$image){
				$error = 'Obrazok sa nepodarilo nahrať.';
				return array('status'=>false, 'error'=>$error);
			}
		  }
		  else {
			$error = 'Súbor obrázku musí byť JPG alebo GIF.';
			return array('status'=>false, 'error'=>$error);
		  }
		}
		else{
		  $image = false;
		}
		
		if($image){
		  $post['image_name'] = $image;
		  foreach($post as $key => $val){
		    $$key = $val;
		  }
		  $row = db::fetch("SELECT MAX(priority) AS prior FROM content_images WHERE content_id=$id");
		  $priority = $row['prior']+1;
		  $timestamp = date("Y-m-d H:i:s");
		  db::exec("INSERT INTO content_images VALUES (null, $content_type_id, '$image_name', '', '$timestamp', $id, $priority)");
		  
		  return array('status'=>true);
		  
		}
    }
	
	
  
  }
  
    /**
	 * @title Upload
	 * uploads visitor's images
	 */
	public function upload($image)
	{
		$return = FALSE;
		
		$uploadfile = $this->dir . $image['name'];
		$tempfile = $image['tmp_name'];
		$filename = $image['name'];
		$type = $image['type'];
		
		$UniqFilename = UniqID(Date("U"));
		$imagename = $UniqFilename . ".jpg";
		$iconame = "thumb_" . $UniqFilename . ".jpg";
		  
		if($type == "image/jpeg"){
			$uploadfile = $this->dir . "tempimage.jpg";
		}
		elseif($type == "image/gif"){
			$uploadfile = $this->dir . "tempimage.gif";
		}
		//echo NL.$uploadfile.NL;
		
		
		$file = $uploadfile;
		umask(0000);
		if($uplStatus = @move_uploaded_file($tempfile, $file)) {
		    chmod($file, 0666);    
		}
		
		// If upload OK
		if($uplStatus){
		
			// Add path to pictures
			$picture = $this->dir.$imagename;
			$icon = $this->dir.$iconame;
				 
				 
			/* We load a small GIf file to be expanded into a larger file and
			 calculate the width and height of the image */
			list($width, $height, $imgtype) = GetImageSize($file);
			if($width >= $height){
				if($this->thumbCreate == 'cut'){
				  $inputIconWidth = $height;
				  $inputIconHeight = $height;
				  $input_x = ($width/2) - ($height/2);
				  $input_y = 0;
				  $ico_width = $this->defIconSize;
				  $ico_height = $this->defIconSize;
				}
				if($this->thumbCreate == 'ratio'){
				  $inputIconWidth = $width;
				  $inputIconHeight = $height;
				  $input_x = 0;
				  $input_y = 0;
				  $ico_width = $this->defIconSize;
				  $ico_height = round($height * ($ico_width / $width));
				}
			 	$img_width = $this->defImgSize;
				$img_height = round($height * ($img_width / $width));
			}
			if($width < $height){
				if($this->thumbCreate == 'cut'){
				  $inputIconWidth = $width;
				  $inputIconHeight = $width;
				  $input_x = 0;
				  $input_y = ($height/2) - ($width/2);
				  $ico_width = $this->defIconSize;
				  $ico_height = $this->defIconSize;
				}
				if($this->thumbCreate == 'ratio'){
				  $inputIconWidth = $width;
				  $inputIconHeight = $height;
				  $input_x = 0;
				  $input_y = 0;
				  $ico_height = $this->defIconSize;
				  $ico_width = round($width * ($ico_height / $height));
				  
				}
			 	$img_height = $this->defImgSize;
				$img_width = round($width * ($img_height / $height));
			}
				 
			if($type == "image/jpeg"){
			 	$imgInput = ImageCreateFromJpeg($file);
			}
			if($type == "image/gif"){
			 	$imgInput = ImageCreateFromGif($file);
			}
				 
			/* Set output size of the picture and icon */
			$imgOutputIcon = ImageCreateTrueColor($ico_width, $ico_height);
			$imgOutputImage = ImageCreateTrueColor($img_width, $img_height);
				 
			ImageCopyResampled($imgOutputIcon, $imgInput, 0, 0, $input_x, $input_y, $ico_width, $ico_height, $inputIconWidth, $inputIconHeight);	 	
			ImageCopyResampled($imgOutputImage, $imgInput, 0, 0, 0, 0, $img_width, $img_height, $width, $height);
			ImageJpeg($imgOutputIcon, $icon, 100);
			ImageJpeg($imgOutputImage, $picture, 95);
				      	 
			ImageDestroy($imgInput);
			ImageDestroy($imgOutputIcon);
			ImageDestroy($imgOutputImage);
				 
			unlink($file);
				 
			$return = $imagename;
			
		
		}
		else {
		    Application::setError('Súbor sa nepodarilo odoslat!');
		}
		
		return $return;
		
	}
	
	
	
  public function uplFile($files, $post){
  
    
	$row = db::fetch("SELECT frm_name FROM content_type_fields WHERE id=".$post['file_content_type_field_id']);
	$fileElementName = $row['frm_name'];
    // set file extension
	$file_type = end(explode(".", strtolower($files[$fileElementName]['name'])));
	// check for allowed file type
	$allowedExtensions = array("txt","csv","htm","html","xml","css","doc","docx","xls","rtf","ppt","pdf","swf","flv","avi",
		                       "wmv","mov","jpg","jpeg","gif","png","zip","rar","gz","mp3","wma","wav","ods","odt");
	$error = false;
	if(!in_array($file_type, $allowedExtensions)){
		$error = 'Pokúšate sa nahrať súbor nepovoleného typu.';
		return array('status'=>false, 'error'=>$error);
	}
	if($files[$fileElementName]['size'] > $post['file_max_file_size']){
    	$error = 'Veľkosť nahrávaného súboru je väčšia ako maximálne povolená veľkosť súboru.';
		return array('status'=>false, 'error'=>$error);
	}
    // Check for upload errors
	if($error = $this->checkUploadErrors($files[$fileElementName])){
		return array('status'=>false, 'error'=>$error);
	}
	
	// everything ok
	$this->dir =  WWW_DIR.'/files/';
	
	if(isset($files[$fileElementName])){
		$file = $files[$fileElementName];
				
		if($file['name'] != ''){
		  
		    $filename = $this->uploadFile($file);
		    if(!$filename){
				$error = 'Obrazok sa nepodarilo nahrať.';
				return array('status'=>false, 'error'=>$error);
			}

		}
		else{
		    $filename = false;
		}
		
		if($filename){
		    $post['file_name'] = $filename;
		    foreach($post as $key => $val){
		        $$key = $val;
		    }
		    $row = db::fetch("SELECT MAX(priority) AS prior FROM content_files WHERE content_id=$id");
		    $priority = $row['prior']+1;
			$timestamp = date("Y-m-d H:i:s");
		    db::exec("INSERT INTO content_files VALUES (null, $content_type_id, '$file_name', '', '$timestamp', $id, $priority, '$file_type')");
		  
		    return array('status'=>true);
		  
		}
    }
  }
	
	/**
	 * @title UploadFile
	 * uploads visitor's files
	 */
	public function uploadFile($file)
	{
		
		$return = FALSE;
		
		$filename = date('U').'-'.machineStr($this->cleanFileName($file['name']), array('.','-'));
		$uploadfile = $this->dir . $filename;
		$tempfile = $file['tmp_name'];
		$type = $file['type'];
		
		umask(0000);
		if($uplStatus = @move_uploaded_file($tempfile, $uploadfile)){
		    chmod($uploadfile, 0666);    
		}
		
		// If upload OK
		if($uplStatus){
		    $return = $filename;
		}
		else {
		    Application::setError('Súbor sa nepodarilo odoslat!');
		}
		
		return $return;
		
	}
        
        public function ajaxImageUpload($id, $element) {
            $error = $this->checkUploadErrors($_FILES[$element]);
            
            $cont = $this->find($id);
                
            $ctid = $cont['content_type_id'];

            $fieldSet = db::fetchSingle("SELECT attributes FROM content_type_fields WHERE content_type_id = %i AND frm_name = %v", $ctid, $element);

            $attrs = unserialize($fieldSet);
            if($_FILES[$element]['size'] > ($attrs['max_file_size']*1024000)) {
                $error = 'Súbor prekročil maximálnu veľkosť.';
            }
            
            if(!$error) {
                $error = false;
                
                $this->dir = WWW_DIR.'/images/';
                $this->thumbCreate = $attrs['thumb_create'];
                $this->defIconSize = $attrs['icon_size'];
                $this->defImgSize = $attrs['preview_size'];
                
                $image = $this->upload($_FILES[$element]);
                
                if(!$image) {
                    $error = 'Uploading of an image failed.';
                }
                else {
                    $newPrior = db::fetchSingle("SELECT MAX(priority)+1 FROM content_images WHERE content_id = %i", $id);
                    if(!$newPrior) $newPrior = 1;
                    $time = date("Y-m-d H:i:s");
                    $result = db::exec("INSERT INTO content_images VALUES (null, {$ctid}, '{$image}', '', '{$time}', {$id}, {$newPrior})");
                    if($result) {
                        $msg = 'OK';
                    }
                    else {
                        $error = 'Saving an image failed.';
                    }
                }
            }
            
            return $error;
            
        }
        
        public function imageDelete($id, $image)
        {
            $res = db::exec("DELETE FROM content_images WHERE id = %i", $id);
            
            if($res) {
                unlink(WWW_DIR . "/images/$image");
                return true;
            }
            else {
                return false;
            }
        }
        
        
        public function ajaxFileUpload($id, $element) {
            $error = $this->checkUploadErrors($_FILES[$element]);
            
            $cont = $this->find($id);
                
            $ctid = $cont['content_type_id'];

            $fieldSet = db::fetchSingle("SELECT attributes FROM content_type_fields WHERE content_type_id = %i AND frm_name = %v", $ctid, $element);

            $attrs = unserialize($fieldSet);
            
            if(!$error) {
                $file_type = end(explode(".", strtolower($_FILES[$element]['name'])));
                // check for allowed file type
                $allowedExtensions = array("txt","csv","htm","html","xml","css","doc","docx","xls","rtf","ppt","pdf","swf","flv","avi",
                                               "wmv","mov","jpg","jpeg","gif","png","zip","rar","gz","mp3","wma","wav","ods","odt");

                if(!in_array($file_type, $allowedExtensions)){
                        $error = 'Pokúšate sa nahrať súbor nepovoleného typu.';
                        return $error;
                }

                if($_FILES[$element]['size'] > ($attrs['max_file_size']*1024000)) {
                    $error = 'Súbor prekročil maximálnu veľkosť.';
                    return $error;
                }
            }
            
            if(!$error) {
                $error = false;
                
                $this->dir = WWW_DIR.'/files/';
                
                $file = $this->uploadFile($_FILES[$element]);
                
                if(!$file) {
                    $error = 'Uploading of an file failed.';
                }
                else {
                    $newPrior = db::fetchSingle("SELECT MAX(priority)+1 FROM content_files WHERE content_id = %i", $id);
                    if(!$newPrior) $newPrior = 1;
                    $time = date("Y-m-d H:i:s");
                    $result = db::exec("INSERT INTO content_files VALUES (null, {$ctid}, '{$file}', '', '{$time}', {$id}, {$newPrior}, '{$file_type}')");
                    if($result) {
                        $msg = 'OK';
                    }
                    else {
                        $error = 'Saving a file failed.';
                    }
                }
            }
            
            return $error;
            
        }
        
        
        public function fileDelete($id, $image)
        {
            $res = db::exec("DELETE FROM content_files WHERE id = %i", $id);
            
            if($res) {
                unlink(WWW_DIR . "/files/$image");
                return true;
            }
            else {
                return false;
            }
        }

    /**
	 * @title Clean File Name
	 * removes spaces from file name
	 */
	private function cleanFileName($uploading_file)
	{
		$file = preg_replace("/ /", "_", $uploading_file);
		$file = preg_replace("/%20/", "_", $uploading_file);
		
		return $file;
	}
	
	/**
	 * @title Check for Upload Errors
	 * checks if no errors by uploading
	 */
	private function checkUploadErrors($file)
	{
		if(!empty($file['error']))
		{
			switch($file['error'])
			{

				case '1':
					$error = 'Nahrávaný súbor prekročil maximálne povelenú veľkosť nastavenú v direktíve upload_max_filesize v php.ini';
					break;
				case '2':
					$error = 'Nahrávaný súbor prekročil hodnotu z direktívy MAX_FILE_SIZE, ktorá bola nastavená v HTML formuláre';
					break;
				case '3':
					$error = 'Nahrávaný súbor bol nahraný len čiastočne.';
					break;
				case '4':
					$error = 'Žiadny súbor nebol nahratý.';
					break;

				case '6':
					$error = 'Chýba pracovný priečinok';
					break;
				case '7':
					$error = 'Zápis na disk zlyhal';
					break;
				case '8':
					$error = 'File upload stopped by extension';
					break;
				case '999':
				default:
					$error = 'Neznáma chyba pri nahrávaní súboru';
			}
			
			return $error;
			
		}
                elseif(empty($file['tmp_name']) || $file['tmp_name'] == 'none')
                {
                    return 'Žiadny súbor nebol nahratý..';
                }
		else{
		
			return '';
		
		}
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