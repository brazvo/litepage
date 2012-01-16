<?php
/**
 * Project: LightPage
 * Description: LightPage is a simple framework for building pages based on SQLite database.
 * created: 13:05 07/02/2011
 * @author: Branislav Zvolensky
 * @copyright: Branislav Zvolensky (c) 2010 - 2011
 * ---------------------------------------
 *
 * @name: CoreLoader
 * @package: Core 
 *
 * ---------------------------------------
 */
final class CoreLoader {
    
    /**
     * Loads core classes and if the class has init() method it will be run
     * @param string $classname The name of the class to be loaded
     */
    public static function load($classname)
    {
        $isClass = false;
        if(!class_exists($classname, false) || !interface_exists($classname, false)) {
        
            if( is_file( dirname(__FILE__) . "/{$classname}.php" ) ) {
                require dirname(__FILE__) . "/{$classname}.php";
                $isClass = true;
            }
            elseif( is_file( dirname(__FILE__) . "/interfaces/{$classname}.php" ) ) {
                require dirname(__FILE__) . "/interfaces/{$classname}.php";
            }
            else {
                throw new Exception("Súbor {$classname}.php nebolo možné načítať");
            }
            
        }
        
        if ( $isClass && method_exists( $classname, 'init' ) ) {
            //$classname::init();
			eval('$var = '.$classname.'::init();');
        }
        
    }
    
}
