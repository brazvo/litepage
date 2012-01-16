<?php

class AdmDevelopmentModel extends BaseModel
{
  // Properties
  
  // Constructor
  public function __construct()
  {
  
    parent::__construct();
  
  }
  
  // Methods
  public function getValues()
  {
	
	return db::fetchAll("SELECT * FROM development ORDER BY id");
  
  }
  
  public function save($post)
  {


      unset($post['save']);
      foreach($post as $key => $val){
        db::exec("UPDATE development SET value='$val' WHERE frm_name='$key'");
      }
      return true;
	
  }
  
  public function flushCache()
  {
  
    $tpls = array();
    $tplDir = opendir(WWW_DIR.'/cache/');
    while($files = readdir($tplDir)) {
          $tpls[] = $files;
    }
    closedir($tplDir);

    foreach($tpls as $tpl){

          if(substr($tpl, 0, 1) != '.'){
              @unlink(WWW_DIR . '/cache/' .$tpl);
          }
    }
    
    // flush compiled smarty templates
    $tpls = array();
    $tplDir = opendir(TPL_DIR.'/@compiled/');
    while($files = readdir($tplDir)) {
          $tpls[] = $files;
    }
    closedir($tplDir);

    foreach($tpls as $tpl){

          if(substr($tpl, 0, 1) != '.'){
              @unlink(TPL_DIR . '/@compiled/' .$tpl);
          }
    }
    
    
    return db::exec("DELETE FROM `cache`");
  }
}