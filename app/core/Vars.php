<?php
/**
 * Vars checks if all the input are correct without illegal tags, characters, etc.
 * Global vars $_GET, $_SESSION, $_COOKIES, will be still accessable but cleaned from htmlentities, html tags
 * except var $_POST. $_POST will bw checked in AppForm or Form library
 *
 * @author brazvo
 * @package Core
 */
class Vars implements ICore {
    
    private $vars;
    
    private static $globals = array();
    
    public static function init()
    {
        
        if( isset( $GLOBALS['_POST'] ) ) self::$globals['POST'] = new self( '_POST' );
        if( isset( $GLOBALS['_GET'] ) ) self::$globals['GET'] = new self( '_GET' );
        if( isset( $GLOBALS['_REQUEST'] ) ) self::$globals['REQUEST'] = new self( '_REQUEST' );
        if( isset( $GLOBALS['_COOKIE'] ) ) self::$globals['COOKIE'] = new self( '_COOKIE' );
        
    }
    
    public static function get( $name )
    {
        
        if( isset( self::$globals[$name] ) ) {
            return self::$globals[$name];
        }
        else {
            //throw new Exception("Global variable $name is not set.");
            return array();
        }
        
    }
    
    ///////////////////////////////////////////////////////////////////////////
    
    private $raws;
    
    private $name;
    
    public function __construct( $name ) {
        
        $this->raws = $GLOBALS[$name];
        
        $this->name = $name;
        
        if( is_array( $this->raws ) ) {
            
            foreach( $this->raws as $key => $value ) {
                
                $this->$key = $this->valueCheck( $value, $name );
                
            }
            
        }
    
    }
    
    
    public function __set($name, $value) {
        
        $this->vars[$name] = $value;
        
    }
    
    
    public function __get($name) {
        
        return $this->vars[$name];
    
    }


    public function __isset($name) {
        
        return isset( $this->vars[$name] );
        
    }
    
    
    public function getVals() {
        
        return $this->vars;
        
    }
    
    
    public function getRaws() {
        
        return $this->raws;
        
    }
    
    
    public function setRaw($name, $value)
    {
        $this->raws[$name] = $value;
        $this->$name = $this->valueCheck( $value );
    }


    private function valueCheck ( $value )
    {
        
        if ( is_array( $value ) ) {
            
            $ret = array();        
            
            foreach( $value as $key => $val) {
                
                $ret[$key] = $this->valueCheck( $val );
                
            }
            
            return $ret;
                    
        }
        
        if ( is_string( $value ) ) {
            
            return trim( htmlMyEnts( strip_tags( $value ) ) );
            
        }
        
        if (is_integer( $value ) ) {
            
            return (int) $value;
            
        }
        
        if (is_float( $value ) ) {
            
            return (float) $value;
            
        }        
        
    }
    
}