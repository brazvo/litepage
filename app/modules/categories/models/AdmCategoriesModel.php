<?php
class AdmCategoriesModel extends Object
{
  // Properties
  private $ini, $feCont, $admCont;
  
  // Constructor
  public function __construct()
  {
	  $this->ini = parse_ini_file( dirname(__FILE__) . "/../module.ini" );
	  $this->feCont = strtolower($this->ini['frontend.controller']);
	  $this->admCont = strtolower($this->ini['controller']);
  }
  
  // Methods

  public function find($id)
  {
    $row = db::fetch("SELECT * FROM categories WHERE id=$id");
	return $row;
  }
  
  public function findAll()
  {
    $rows = db::fetchAll("SELECT * FROM categories ORDER BY title");
	return $rows;
  }
  
  public function findItem($id)
  {
    return db::fetch("SELECT * FROM categories_items WHERE id=$id");
  }
  
  public function findItems($cat_id)
  {
    $rows = db::fetchAll("SELECT * FROM categories_items WHERE cat_id=$cat_id ORDER BY title");
	return $rows;
  }
  
  public function findMenus()
  {
    $rows = db::fetchAll("SELECT * FROM menus WHERE only_admin=0 ORDER BY id");
	return $rows;
  }
  
  public function findContentTypes()
  {
    $rows = db::fetchAll("SELECT * FROM content_types ORDER BY id");
	return $rows;
  }
  
  public function findActiveContentTypes($id)
  {
    $rows = db::fetchAll("SELECT * FROM categories_content_types WHERE catid=$id");
	return $rows;
  }
  
  public function findCategoryMenu($cat_id)
  {
	return db::fetch("SELECT menu_id, menu_item_id FROM categories WHERE id=$cat_id");
  } 

  public function findContent($cat_id, $cat_item_id)
  {
	$rows = db::fetchAll("SELECT * FROM categories_content_types WHERE catid=$cat_id");

	if(!$rows) return null;
	$contents = array();
	if(!$rows) return null;
	foreach($rows as $row){
		$ct_name = $row['ct_name'];
		$records = db::fetchAll("SELECT * FROM content WHERE content_type_machine_name='$ct_name' ORDER BY created DESC");
		if($records){
			foreach($records as $record){
				$contents[] = $record;
			}
		}
	}
	
	$return = false;
	foreach($contents as $content){
		$cont_id = $content['id'];
		$row = db::fetch("SELECT * FROM categories_relations WHERE cont_id=$cont_id AND cat_item_id=$cat_item_id");
		if($row){
			$return[] = $content;
		}
	}
	
	return $return;
  }

  public function findAllContent($cat_id)
  {
	$rows = db::fetchAll("SELECT * FROM categories_content_types WHERE catid=$cat_id");
	$contents = array();
	if(!$rows) return null;
	foreach($rows as $row){
		$ct_name = $row['ct_name'];
		$records = db::fetchAll("SELECT * FROM content WHERE content_type_machine_name='$ct_name' ORDER BY id");
		if($records){
			foreach($records as $record){
				$contents[] = $record;
			}
		}
	}
	
	$return = false;
	foreach($contents as $content){
		$cont_id = $content['id'];
		$row = db::fetch("SELECT * FROM categories_relations WHERE cont_id=$cont_id AND cat_id=$cat_id");
		if($row){
			$return[$row['cat_item_id']][] = $content;
		}
	}
	ksort($return);
	return $return;
  }  
  
  public function save($post)
  {
    // set values from post
	foreach($post as $key => $val){
		$$key = $val;
	}
	// insert or update selected content types into base
	db::exec("DELETE FROM categories_content_types WHERE catid=$id");
	$ct_types = explode(':',$content_types_names);
	foreach($ct_types as $ct_type){
		if(isset($$ct_type)) db::exec("INSERT INTO categories_content_types VALUES ($id, '$ct_type')");
	}
	
	// set unset bool values
	isset($main_menu_item) ? $main_menu_item = $main_menu_item : $main_menu_item = 0;
	isset($show_partial) ? $show_partial = $show_partial : $show_partial = 0;
	isset($show_updated) ? $show_updated = $show_updated : $show_updated = 0;
	isset($show_user) ? $show_user = $show_user : $show_user = 0;
	isset($image_gallery) ? $image_gallery = $image_gallery : $image_gallery = 0;
	isset($required) ? $required = $required : $required = 0;
	$show_pages = (int)isset($show_pages);
	$show_created = (int)isset($show_created);
	
	$menu_title ? $menu_title = $menu_title : $menu_title = $title;
	
	// if main_menu_item get new menu item ID and save path_alias
	if($main_menu_item){
		$row = db::fetch("SELECT * FROM menu_items WHERE id='$menu_item_id'");
		if(!$row){
			$row = db::fetch("SELECT MAX(id) AS mi_id FROM menu_items");
			$new_mi_id = $row['mi_id']+1;
			$menu_item_id = $new_mi_id;
			if(trim($path_alias) != ''){
				$row = db::fetch("SELECT * FROM path_aliases WHERE module='categories' AND mod_id='$id'");
				if($row) db::exec("UPDATE path_aliases SET path_alias='$path_alias' WHERE module='categories' AND mod_id='$id'");
					else db::exec("INSERT INTO path_aliases VALUES (null, '$path_alias', 'categories/{$this->feCont}/show/$id', 'categories', $id)");
			}
			db::exec("INSERT INTO menu_items VALUES ($new_mi_id, '$menu_title', '$path_alias', 1, 1, 0, 0, $menu_id, '$title', '', $id, 'categories')");
			
			$rows = db::fetchAll("SELECT * FROM categories_relations WHERE cat_id='$id'");
			if($rows){
				foreach($rows as $row){
					$menu_items_ids[] = $row['menu_item_id'];
				}
				foreach($menu_items_ids as $menuitid){
					db::exec("UPDATE menu_items SET child_of=$new_mi_id, menu_id=$menu_id WHERE id=$menuitid");
				}
			}
		}
		else{
			db::exec("UPDATE menu_items SET menu_id=$menu_id WHERE id=$menu_item_id");
			db::exec("UPDATE menu_items SET menu_id=$menu_id WHERE child_of=$menu_item_id");
		}
	}
	else{
		if($menu_item_id > 0){
			db::exec("UPDATE menu_items SET child_of=0, menu_id=$menu_id WHERE child_of=$menu_item_id");
			db::exec("DELETE FROM menu_items WHERE id=$menu_item_id");
			$menu_item_id = 0;
		}
		// Update menu_id in menu_items table
		$rows = db::fetchAll("SELECT * FROM categories_items WHERE cat_id=$id");
		if($rows){
			foreach($rows as $row){
				$menu_it_id = $row['menu_item_id'];
				db::exec("UPDATE menu_items SET menu_id=$menu_id WHERE id=$menu_it_id");
			}
		}
	}
	db::exec("UPDATE categories SET title='$title', menu_title='$menu_title', description='$description', main_menu_item=$main_menu_item,
	          menu_id=$menu_id, menu_item_id=$menu_item_id, show_partial=$show_partial, chars_num='$chars_num', show_updated=$show_updated,
			  show_user=$show_user, image_gallery=$image_gallery, required=$required, path_alias='$path_alias',
			  show_created=$show_created, show_pages=$show_pages, items_per_page=$items_per_page, paginator_limit=$paginator_limit  WHERE id=$id");
	
	return true;
  }
  
  public function saveNew($post)
  {
    // set values from post
	foreach($post as $key => $val){
		$$key = $val;
	}
	
	// set unset bool values
	isset($main_menu_item) ? $main_menu_item = $main_menu_item : $main_menu_item = 0;
	isset($show_partial) ? $show_partial = $show_partial : $show_partial = 0;
	isset($show_updated) ? $show_updated = $show_updated : $show_updated = 0;
	isset($show_user) ? $show_user = $show_user : $show_user = 0;
	isset($image_gallery) ? $image_gallery = $image_gallery : $image_gallery = 0;
	isset($required) ? $required = $required : $required = 0;
	$show_pages = (int)isset($show_pages);
	$show_created = (int)isset($show_created);
	
	$menu_title ? $menu_title = $menu_title : $menu_title = $title;
	
	// new category id
	$row = db::fetch("SELECT MAX(id) AS id FROM categories");
	$new_id = $row['id']+1;
	
	// if main_menu_item get new menu item ID and save path_alias
	if($main_menu_item){
		$row = db::fetch("SELECT MAX(id) AS mi_id FROM menu_items");
		$new_mi_id = $row['mi_id']+1;
		if(trim($path_alias) != ''){
			db::exec("INSERT INTO path_aliases VALUES (null, '$path_alias', 'categories/{$this->feCont}/show/$new_id', 'categories', $new_id)");
			$path = $path_alias;
		}
		else{
			$path = "categories/{$this->feCont}/show/".$new_id;
		}
		db::exec("INSERT INTO menu_items VALUES ($new_mi_id, '$menu_title', '$path', 1, 1, 0, 0, $menu_id, '$title', '', $new_id, 'categories')");
	}
	else{
		$new_mi_id = 0;
	}
	
	// insert or update selected content types into base
	$ct_types = explode(':',$content_types_names);
	foreach($ct_types as $ct_type){
		if(isset($$ct_type)) db::exec("INSERT INTO categories_content_types VALUES ($new_id, '$ct_type')");
	}
	
	db::exec("INSERT INTO categories VALUES ($new_id, '$title', '$menu_title', '$description', $main_menu_item,
	          $menu_id, $new_mi_id, $show_partial, '$chars_num', $show_updated,
			  $show_user, $image_gallery, $required, '$path_alias', $show_created,
			  $show_pages, $items_per_page, $paginator_limit)");
	return true;
  }
  
  public function saveNewItem($post)
  {
    // set values from post
	foreach($post as $key => $val){
		$$key = $val;
	}
	
	$menu_title ? $menu_title = $menu_title : $menu_title = $title;
	isset($show_images) ? $show_images = $show_images : $show_images = 0;
	isset($show_files) ? $show_files = $show_files : $show_files = 0;
	
	// new category item id
	$row = db::fetch("SELECT MAX(id) AS id FROM categories_items");
	$new_id = $row['id']+1;
	
	$row = db::fetch("SELECT MAX(id) AS id FROM menu_items");
	$new_mi_id = $row['id']+1;
	
	// save path_alias
	if(trim($path_alias) != ''){
		db::exec("INSERT INTO path_aliases VALUES (null, '$path_alias', 'categories/{$this->feCont}/showitem/$new_id', 'categories_items', $new_id)");
		$path = $path_alias;
	}
	else{
		$path = "categories/{$this->feCont}/showitem/".$new_id;
	}
	
	db::exec("INSERT INTO menu_items VALUES ($new_mi_id, '$menu_title', '$path', 1, 0, 0, $child_of, $menu_id, '$title', '', $new_id, 'categories_items')");
	
	db::exec("INSERT INTO categories_items VALUES ($new_id, $cat_id, '$title', '$description', 0,
	          $menu_id, $new_mi_id, '$path_alias', '$menu_title', $show_images, $show_files)");
	return true;
  }
  
  public function saveItem($post)
  {
    // set values from post
	foreach($post as $key => $val){
		$$key = $val;
	}
	
	$menu_title ? $menu_title = $menu_title : $menu_title = $title;
	isset($show_images) ? $show_images = $show_images : $show_images = 0;
	isset($show_files) ? $show_files = $show_files : $show_files = 0;
	
	// save path_alias
	if(trim($path_alias) != ''){
		$row = db::fetch("SELECT * FROM path_aliases WHERE module='categories_items' AND mod_id=$id");
		if($row){
			db::exec("UPDATE path_aliases SET path_alias='$path_alias' WHERE id=".$row['id']);
		}
		else{
			db::exec("INSERT INTO path_aliases VALUES (null, '$path_alias', 'categories/{$this->feCont}/showitem/$id', 'categories_items', $id)");
		}
		$path = $path_alias;
	}
	else{
		db::exec("DELETE FROM path_aliases WHERE module='categories_items' AND mod_id=$id");
		$path = "categories/{$this->feCont}/showitem/".$id;
	}

	db::exec("UPDATE menu_items SET title='$menu_title', name='$title', path='$path' WHERE id=$menu_item_id");
	
	db::exec("UPDATE categories_items SET title='$title', description='$description', menu_title='$menu_title', path_alias='$path_alias', show_images=$show_images, show_files=$show_files WHERE id=$id");
	return true;
  }
  
  public function delete($post)
  {
    $id = $post['id'];
	$menu_id = $post['menu_id'];
	$menu_item_id = $post['menu_item_id'];
	$path_alias = $post['path_alias'];
	
	$row = db::exec("DELETE FROM menu_items WHERE id=$menu_item_id");
	if($row) $row = db::exec("DELETE FROM menu_items WHERE child_of=$menu_item_id AND menu_id=$menu_id");
	if($row) $row = db::exec("DELETE FROM categories_relations WHERE cat_id=$id");
	if($row) $row = db::exec("DELETE FROM categories_content_types WHERE catid=$id");
	if($row) $row = db::exec("DELETE FROM categories_items WHERE cat_id=$id");
	if($row) $row = db::exec("DELETE FROM path_aliases WHERE module='categories' AND mod_id=$id");
	if($row) $row = db::exec("DELETE FROM categories WHERE id=$id");
    
	return $row;
  }
  
  public function deleteItem($post)
  {
    $id = $post['id'];
	$menu_item_id = $post['menu_item_id'];
	
	$row = db::exec("DELETE FROM menu_items WHERE id=$menu_item_id");
	if($row) $row = db::exec("DELETE FROM categories_relations WHERE cat_item_id=$id");
	if($row) $row = db::exec("DELETE FROM path_aliases WHERE module='categories_items' AND mod_id=$id");
	if($row) $row = db::exec("DELETE FROM categories_items WHERE id=$id");
    
	return $row;
  }
  
  public function getCatSettings($id)
  {
    $row = db::fetch("SELECT show_partial, chars_num, show_updated, show_created, show_user, image_gallery, show_pages, items_per_page, paginator_limit FROM categories WHERE id=$id");
	return $row;
  }
  
  public function getContentBody($table, $id)
  {
    $row = db::fetch("SELECT body FROM $table WHERE id=$id");
	return stripslashes($row['body']);
  }
  
  public function getUserName($uid)
  {
    $row = db::fetch("SELECT * FROM users WHERE id=$uid");
	return $row['name'].' '.$row['surname'];
  }
  
  public function getFirstImage($cont_id)
  {
    $row = db::fetch("SELECT * FROM content_images WHERE content_id=$cont_id");
	if(!$row) return false;
	return $row['image_name'];
  }
  
  public function getCatItemTitle($id)
  {
    $row = db::fetch("SELECT * FROM categories_items WHERE id=$id");
	return $row['title'];
  }
  
  public function getShowFiles($id)
  {
    $row = db::fetch("SELECT * FROM categories_items WHERE id=$id");
	return $row['show_files'];
  }
  
  public function getShowImages($id)
  {
    $row = db::fetch("SELECT * FROM categories_items WHERE id=$id");
	return $row['show_images'];
  }
  
  public function getImagesOrdering($cont_type_id)
  {
    $row = db::fetch("SELECT * FROM content_type_fields WHERE content_type_id=$cont_type_id AND machine_field_type='image'");
	if(!$row) return false;
	$attrs = $this->parseAttributes($row['attributes']);
	
	return $attrs['order_by'];
  }
  
  public function getFilesOrdering($cont_type_id)
  {
    $row = db::fetch("SELECT * FROM content_type_fields WHERE content_type_id=$cont_type_id AND machine_field_type='file'");
	if(!$row) return false;
	$attrs = $this->parseAttributes($row['attributes']);
	
	return $attrs['order_by'];
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