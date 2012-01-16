<?php
/**
 * Auto loader for model classes
 *
 * @author brazvo
 */
class ModelLoader {
    
    public static function load($className)
    {
        
        $dirs[] = WWW_DIR.'/app/models/';
        if(Application::$subDir) $dirs[] = WWW_DIR.'/app'.  Application::$subDir.'/models/';
        
        $isClass = false;
        
        foreach($dirs as $dir) {
            
            @include $dir . $className . '.php';
            
            if(class_exists($className)) $isClass = true;
            
        }
        
        if(!$isClass) throw new Exception("The file with class {$className} does not exists");
    }
    
}

?>
