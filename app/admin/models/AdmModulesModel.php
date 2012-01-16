<?php
class AdmModulesModel extends BaseModel
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
        
      $rows = db::fetchAll("SELECT * FROM modules ORDER BY name");
      
      $aInstalled = array();
      foreach ($rows as $row) {
          $aInstalled[] = $row['machine_name'];
      }
      
      $modules = array();
      $modulesDir = opendir(WWW_DIR.'/app/modules/');
      while($files = readdir($modulesDir)) {
          if(substr($files, 0, 1) != '.') $modules[] = $files;
      }
      closedir($modulesDir);
      
      $count = 0;
      
      foreach ($modules as $module) {
          if( file_exists(APP_DIR.'/modules/'.$module.'/module.ini') ) {
              $modIni = parse_ini_file(APP_DIR.'/modules/'.$module.'/module.ini');
              $mod_name = $modIni['name'];
              $mod_version = $modIni['version'];
              $mod_description = $modIni['description'];
          }
          else {
              require(APP_DIR.'/modules/'.$module.'/config.inc');
          }
          $rows[$count]['machine_name'] = $module;
          $rows[$count]['name'] = $mod_name;
          $rows[$count]['version'] = $mod_version;
          $rows[$count]['description'] = $mod_description;
          $rows[$count]['installed'] = in_array($module, $aInstalled) ? true : false;
          
          $count++;
      }
        
	return $rows;
  }
  
  
  public function installModule($post)
  {
	
        $module = $post['machine_name'];
        $modDir = MOD_DIR;
        if ( file_exists( "{$modDir}/{$module}/module.ini" ) ) {
            $modIni = parse_ini_file( "{$modDir}/{$module}/module.ini" );
            $mod_name = $modIni['name'];
            $mod_machine_name = $modIni['machine_name'];
            $mod_version = $modIni['version'];
            $mod_description = $modIni['description'];
            $mod_standalone = $modIni['standalone'];
            $mod_content_extension = $modIni['content_extension'];
            $mod_module_extension = $modIni['module_extension'];
            $mod_application = $modIni['application'];
        }
        // backward compatibility
        else if( file_exists("{$modDir}/{$module}/config.inc") ) {
            require("{$modDir}/{$module}/config.inc");
        }

        db::exec("INSERT INTO modules (name, machine_name, installed, version, description, standalone, content_extension, module_extension, application) VALUES ('$mod_name', '$mod_machine_name', 0, '$mod_version', '$mod_description', '$mod_standalone', '$mod_content_extension', '$mod_module_extension', '$mod_application')");
        require( "{$modDir}/{$module}/installation.inc" );
	moduleInstall();
  }
  
  public function uninstallModule($post)
  {
        $module = $post['machine_name'];
        $modDir = MOD_DIR;
        require( "{$modDir}/{$module}/installation.inc" );
	moduleUninstall();
        db::exec("DELETE FROM modules WHERE machine_name = %v", $module);
  }
  
}