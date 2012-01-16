<?php
/**
 * Class ModuleChecker checks for active installed modules
 *
 * @author brazvo
 * @package core
 */
class ModuleChecker implements ICore {
    
    public static function init()
    {
        
        $obj = new self;
        
        // init arrays
        $saMods = array();
        $appMods = array();
        $contExMods = array();
        $modExMods = array();
        
        $rows = db::fetchAll("SELECT * FROM `modules` WHERE `installed` = 1");
        
        if( $rows ) {
            
            foreach( $rows as $row ) {
                
                if( $row['standalone'] ) $saMods[] = $row['machine_name'];
                else if( $row['content_extension'] ) $contExMods[] = $row['machine_name'];
                else if( $row['module_extension'] ) $modExMods[] = $row['machine_name'];
                else if( $row['application'] ) $appMods[] = $row['machine_name'];
                
            }
            
        }
        
        // save results to Environment
        Environment::set( 'standaloneModules', $saMods );
        Environment::set( 'applicationModules', $appMods );
        Environment::set( 'contExModules', $contExMods );
        Environment::set( 'modExModules', $modExMods );
        
        
    }
    
    
}