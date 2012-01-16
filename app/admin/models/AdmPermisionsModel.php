<?php

class AdmPermisionsModel extends BaseModel
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
    
	$result = db::fetchAll("SELECT * FROM permisions ORDER BY content");
	
	return $result;
	
  }
  
  public function save($post)
  {
  
        $rows = $this->findAll();
        $save = array();
        unset($post['save']);
	foreach($rows as $row){
	  if(!isset($post[$row['id'].'__editor_view']))$post[$row['id'].'__editor_view'] = 0;
	  if(!isset($post[$row['id'].'__editor_add']))$post[$row['id'].'__editor_add'] = 0;
	  if(!isset($post[$row['id'].'__editor_edit']))$post[$row['id'].'__editor_edit'] = 0;
	  if(!isset($post[$row['id'].'__editor_delete']))$post[$row['id'].'__editor_delete'] = 0;
	  if(!isset($post[$row['id'].'__user_view']))$post[$row['id'].'__user_view'] = 0;
	  if(!isset($post[$row['id'].'__user_add']))$post[$row['id'].'__user_add'] = 0;
	  if(!isset($post[$row['id'].'__user_edit']))$post[$row['id'].'__user_edit'] = 0;
	  if(!isset($post[$row['id'].'__user_delete']))$post[$row['id'].'__user_delete'] = 0;
	  if(!isset($post[$row['id'].'__visitor_view']))$post[$row['id'].'__visitor_view'] = 0;
	  if(!isset($post[$row['id'].'__visitor_add']))$post[$row['id'].'__visitor_add'] = 0;
	  if(!isset($post[$row['id'].'__visitor_edit']))$post[$row['id'].'__visitor_edit'] = 0;
	  if(!isset($post[$row['id'].'__visitor_delete']))$post[$row['id'].'__visitor_delete'] = 0;
          
          $save[$row['id']] = array('editor_view' => $post[$row['id'].'__editor_view'],
                                    'editor_add' => $post[$row['id'].'__editor_add'],
                                    'editor_edit' => $post[$row['id'].'__editor_edit'],
                                    'editor_delete' => $post[$row['id'].'__editor_delete'],
                                    'user_view' => $post[$row['id'].'__user_view'],
                                    'user_add' => $post[$row['id'].'__user_add'],
                                    'user_edit' => $post[$row['id'].'__user_edit'],
                                    'user_delete' => $post[$row['id'].'__user_delete'],
                                    'visitor_view' => $post[$row['id'].'__visitor_view'],
                                    'visitor_add' => $post[$row['id'].'__visitor_add'],
                                    'visitor_edit' => $post[$row['id'].'__visitor_edit'],
                                    'visitor_delete' => $post[$row['id'].'__visitor_delete']
                                   );
	}
	
	foreach($save as $id => $row){
	  $result = db::exec("UPDATE permisions SET %a WHERE id=$id", $row);
	}
	
	return $result;
  
  }

}