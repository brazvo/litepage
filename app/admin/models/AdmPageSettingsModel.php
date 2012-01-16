<?php

class AdmPageSettingsModel extends BaseModel
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
	
	return db::fetchAll("SELECT * FROM page_settings ORDER BY id");
  
  }
  
  public function save($post)
  {
    // check if exist template
	if(!@is_file(APP_DIR.'/templates/'.$post['template'])){
	  Application::setError('Súbor s názvom '.$post['template'].' sa v adresáry /app/templates/ nenachádza.');
	  return false;
	}
	else{
	  foreach($post as $key => $val){
	    $$key = $val;
	  }
	
      $rows = db::fetchAll("SELECT frm_name FROM page_settings");

      foreach($rows as $row){
	    $frm_name = $row['frm_name'];
	    $value = $$row['frm_name'];
	    db::exec("UPDATE page_settings SET value='$value' WHERE frm_name='$frm_name'");
	  }
	  return true;
	}
  }
  
  public function findTemplates()
  {
  
    $tplDir = opendir(TPL_DIR.'/');
    while($files = readdir($tplDir)) {
	  $tpls[] = $files;
    }
    closedir($tplDir);

    foreach($tpls as $tpl){
	
	  if(substr($tpl, 0, 1) != '.'){
	    if(preg_match('/.tpl/', $tpl)){
		  $return[$tpl] = $tpl;
		}
	  }
    }
    return $return;
  }
}