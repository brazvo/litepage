<?php

class AdmMenusModel extends BaseModel
{
  // Properties
  
  // Constructor
  public function __construct()
  {
  
    parent::__construct();
  
  }
  
  // Methods
  public function findAll()
  {
    //check if is admin logged
	if(Application::$logged['role'] == 'admin'){
       $result = db::fetchAll("SELECT * FROM menus ORDER BY system DESC");
	}
	else{
	   $result = db::fetchAll("SELECT * FROM menus WHERE only_admin = 0 ORDER BY system DESC");
	}
	
	if($result) return $result;
		else return false;
  
  }
  
  public function find($id)
  {
  
    $result = db::fetch("SELECT * FROM menus WHERE id = $id");
	
	if($result) return $result;
		else return false;
  
  }
  
  public function findItemByPath($path)
  {
  
    $result = db::fetch("SELECT * FROM menu_items WHERE path = '$path'");
	
	if($result) return $result;
		else return false;
  
  }
  
  public function findItemByContentId($module='', $ctid)
  {
  
    $result = db::fetch("SELECT * FROM menu_items WHERE module='$module' AND content_id = '$ctid'");
	
	if($result) return $result;
		else return false;
  
  }
  
  public function findItem($id)
  {
  
    $result = db::fetch("SELECT * FROM menu_items WHERE id = $id");
	
	if($result) return $result;
		else return false;
  
  }
  
  public function findItems($id)
  {
  
    $result = db::fetchAll("SELECT * FROM menu_items WHERE menu_id = $id ORDER BY priority");
	
	if($result) return $result;
		else return false;
  
  }
  
  public function saveMenu($post, $id)
  {
    foreach($post as $key => $val){
	  $$key = $val;
	}
	
	if(trim($machine_name) == ''){
	  $machine_name = strtolower(machineStr($name));
	}
	else{
	  $machine_name = strtolower(machineStr($machine_name));
	}
	
    $result = db::exec("UPDATE menus SET name='$name', machine_name='$machine_name', lang='$lang_code', system='$system' WHERE id = $id");
	
	if($result){
      return $result;
	}
	else{
	  return false;
	}
  
  }
  
  public function saveNewMenu($post)
  {
    foreach($post as $key => $val){
	  $$key = $val;
	}
	
	if(trim($machine_name) == ''){
	  $machine_name = strtolower(machineStr($name));
	}
	else{
	  $machine_name = strtolower(machineStr($machine_name));
	}
	
    $result = db::exec("INSERT INTO menus (name, machine_name, lang, system) VALUES ('$name', '$machine_name', '$lang_code', '$system')");
	
	if($result){
      return $result;
	}
	else{
	  return false;
	}
  
  }
  
  public function saveMenuItem($post, $id)
  {
    foreach($post as $key => $val){
	  $$key = $val;
	}
	if($path == '%front'){
		$path = Application::$language['main_page'];
	}
	elseif($path == '%none'){
		$path = 'error/show/404';
	}
	if(!isset($allowed)) $allowed = 0;
	if(!isset($expanded)) $expanded = 0;
	if(trim($name) == '') $name = $title;
	
	// In the future do image hadler
	$image = '';
	
	$result = db::exec("UPDATE menu_items SET title='$title', name='$name', path='$path', allowed='$allowed', expanded='$expanded', priority='$priority', child_of='$child_of', image='$image' WHERE id=$id");
	
	return $result;
  
  }
  
  public function saveNewMenuItem($post)
  {
    foreach($post as $key => $val){
	  $$key = $val;
	}
	
	if($path == '%front'){
		$path = Application::$language['main_page'];
	}
	elseif($path == '%none'){
		$path = 'error/show/404';
	}
	if(!isset($allowed)) $allowed = 0;
	if(!isset($expanded)) $expanded = 0;
	if(trim($name) == '') $name = $title;
	
	// In the future do image hadler
	$image = '';
	
	$result = db::exec("INSERT INTO menu_items (title, name, path, allowed, expanded, priority, child_of, menu_id) VALUES ('$title', '$name', '$path', '$allowed', '$expanded', '$priority', '$child_of', '$menu_id')");
	
	return $result;
  
  }
  
  public function deleteMenuItem($id, $child_of)
  {
	
	db::exec("UPDATE menu_items SET child_of='$child_of' WHERE child_of='$id'");
	$result = db::exec("DELETE FROM menu_items WHERE id=$id");
	
	return $result;
  
  }
  
  public function delete($id)
  {
  
    $res1 = db::exec("DELETE FROM menu_items WHERE menu_id=$id");
	$res2 = db::exec("DELETE FROM menus WHERE id=$id");
	if($res1 && $res2){
	  return true;
	}
	else{
	  return false;
	}
  
  }
  
  public function getAllMains()
  {
  
    $result = db::fetchAll("SELECT * FROM menu_items WHERE child_of=0 ORDER BY priority");
	
	if($result) return $result;
		else return false;
  
  }
  
  public function getMains($id)
  {
  
    $result = db::fetchAll("SELECT * FROM menu_items WHERE menu_id = $id AND child_of=0 ORDER BY priority");
	
	if($result) return $result;
		else return false;
  
  }
  
  public function getChilds($id)
  {
  
    $result = db::fetchAll("SELECT * FROM menu_items WHERE child_of=$id ORDER BY priority");
	
	if(count($result) > 0){
	  foreach($result as $row){
	    $out[$row['id']]['row'] = $row;
		$nextsub = $this->getChilds($row['id']);
		if($nextsub){
		  $out[$row['id']]['submenu'] = $nextsub;
		}
	  }
	}
	if(isset($out)){	
	  return $out;
	}
  
  }
  
  public function getMenuStructure($menu_id, $child_of_id=0)
  {
  
    $result = db::fetchAll("SELECT * FROM menu_items WHERE child_of=$child_of_id AND menu_id=$menu_id ORDER BY priority");
	
	if(count($result) > 0){
	  foreach($result as $row){
	    $out[$row['id']]['row'] = $row;
		$nextsub = $this->getMenuStructure($menu_id, $row['id']);
		if($nextsub){
		  $out[$row['id']]['submenu'] = $nextsub;
		}
	  }
	}
	if(isset($out)){	
	  return $out;
	}
	else{
	  return false;
	}
  
  }
  
  public function getMenuId($machine_name)
  {
    $result = db::fetch("SELECT id FROM menus WHERE machine_name='$machine_name'");
	
	return $result['id'];
  
  }
  
  public function orderItems($post)
  {
    $ids = preg_split('/;/', $post['ids'], -1, PREG_SPLIT_NO_EMPTY);
	
	foreach($ids as $id){
	  if(!isset($post['allowed__'.$id])){
	    $post['allowed__'.$id] = 0;
	  }
	  if(!isset($post['expanded__'.$id])){
	    $post['expanded__'.$id] = 0;
	  }
	  
	  if($post['child_of__'.$id] == $id){
        Application::setError('Položka nemôže byť podpoložkou sama sebe.');
		return false;
	  }
	  
	  
	  $result = db::exec("UPDATE menu_items SET priority=".$post['priority__'.$id].", allowed=".$post['allowed__'.$id].", expanded=".$post['expanded__'.$id].", child_of=".$post['child_of__'.$id]." WHERE id=$id");
	  if(!$result){
	    Application::setError('Pri ukladaní nastala chyba.');
        return false;
	  }
	  
	}
    return true;
  }
  
  public function getMenuIdByItemId($itemid)
  {
   
	db::exec("CREATE VIEW items_view AS SELECT id, menu_id FROM menu_items");
	//exit;
    $result = db::fetch("SELECT menu_id FROM items_view  WHERE id = $itemid");
	$menu_id = $result['menu_id'];
	db::exec("DROP VIEW items_view");
	
	if($menu_id) return $menu_id;
		else return false;
  
  }
  
  public function getSelectArray($menu_id, $values, $subline='')
  {
    $subline = $subline.'--'; 
    foreach($values as $val){
	  $out[$menu_id.':'.$val['row']['id']] = '< '.$subline.' '.$val['row']['title'].' >';
	  if(isset($val['submenu'])) $out += $this->getSelectArray($menu_id, $val['submenu'], $subline);
	}
	
	return $out;
  
  }
  

}