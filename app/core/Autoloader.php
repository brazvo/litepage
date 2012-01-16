<?php
/**
 * Trieda pre autoloading
 * 
 * @author brazvo
 * @package Core
 */
class Autoloader implements ICore {

    public static $loader;
    
    /** @property array */
    private $folders = array();

    public static function init()
    {
        if (self::$loader == NULL)
            self::$loader = new self();

        return self::$loader;
    }

    public function __construct()
    {
        
        // inicializacia include folderov
        $this->folders[] = APP_DIR.'/libs/';
		$this->folders[] = APP_DIR.'/libs/tools/';

        // ak volame modul pozrieme sa aj do adresara modulu 
        if( Environment::getModule() ) {
            $this->folders[] = APP_DIR.'/modules/'.Environment::getModule().'/controllers/';
            $this->folders[] = APP_DIR.'/modules/'.Environment::getModule().'/models/';
        }
        else if ( Environment::getPathPrefix() ) {
            $this->folders[] = APP_DIR.'/'.Environment::getPathPrefix().'/controllers/';
            $this->folders[] = APP_DIR.'/'.Environment::getPathPrefix().'/models/';
        }
        else { 
            $this->folders[] = APP_DIR.'/controllers/';
        }
        // globalne aplikacne modely su dostupne aj pri volani modulu
        $this->folders[] = APP_DIR.'/models/';
        
        
        spl_autoload_register(array($this,'load'));
        
    }
    
    public function load( $class )
    {
        
        $bIsClass = FALSE;
        $class = ucfirst($class);
        
        foreach( $this->folders as $folder ) {
            
            if ( is_file( $folder . $class . '.php' ) ) {
                include_once $folder . $class . '.php';
                $bIsClass = TRUE;
                break;
            }
            
        }
        
        return $bIsClass;
    }

}

?>
