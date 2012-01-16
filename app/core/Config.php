<?php
/**
 * Description of Config
 *
 * @author brazvo
 */
class Config implements ICore{
    
    private static $sysConfig = array();
    
    public static function init()
    {
        
        self::$sysConfig = parse_ini_file( APP_DIR . "/configuration/system.ini", true );
        
        if ( isset( self::$sysConfig['router']['default.controller'] ) )
            Environment::setController( self::$sysConfig['router']['default.controller'] );
        if ( isset( self::$sysConfig['router']['default.action'] ) )
            Environment::setAction( self::$sysConfig['router']['default.action'] );
        if ( isset( self::$sysConfig['router']['default.id'] ) )
            Environment::setId( self::$sysConfig['router']['default.id'] );
        
        define("DB_DRIVER", self::$sysConfig['database']['driver']);
        define("DB_USER", self::$sysConfig['database']['dbuser']);
        define("DB_PASSWORD", self::$sysConfig['database']['dbpassword']);
        define("DB_SERVER", self::$sysConfig['database']['dbhost']);
        define('SQLITE_DB', self::$sysConfig['database']['dbname']);
        define('MYSQL_DB', self::$sysConfig['database']['dbname']);

        define('NL', "<br/>\n");
        define('FILES_DIR', WWW_DIR.'/files/');
        define('IMAGES_DIR', WWW_DIR.'/images/');
        define('SESSIONID', session_id());
        
        
    }
    
    
    public static function core( $sParam = null )
    {
        if(!$sParam) return self::$sysConfig['core'];
        
        if(isset(self::$sysConfig['core'][$sParam])) {
            return self::$sysConfig['core'][$sParam];
        }
        else {
            return null;
        }
    }
    
    
    public static function database()
    {
        return self::$sysConfig['database'];
    }
    
    
    public static function router()
    {
        return self::$sysConfig['router']['route'];
    }
    
    
    public static function system()
    {
        return self::$sysConfig;
    }
    
}
