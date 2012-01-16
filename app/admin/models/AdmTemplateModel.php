<?php
class AdmTemplateModel extends BaseModel
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
    $row = db::fetch("SELECT * FROM table_name WHERE id=$id");
	return $row;
  }
  
  public function findAll()
  {
    $rows = db::fetchAll("SELECT * FROM table_name WHERE id=$id");
	return $rows;
  }
  
  
  public function save($post)
  {
  
  }
  
  public function saveNew($post)
  {
  
  }
  
  public function delete($id)
  {
    $row = db::exec("DELETE FROM table_name WHERE id=$id");
	return $row;
  }
}