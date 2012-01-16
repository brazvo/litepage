<?php
/**
 * Top abstract class for of all objects
 *
 * @author brazvo
 * @package Core
 */
abstract class Object {
    
    protected $vars = array();
    
    protected $name;
    
    public function __set($name, $value) {
        $this->vars[$name] = $value;
    }

    public function __get($name) {

        if (array_key_exists($name, $this->vars)) {
            return $this->vars[$name];
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }
    
    /**
     * Name setter
     * @param string $sName 
     */
    public function setName( $sName )
    {
        
        $this->name = $sName;
        
    }
    
    
    /**
     * Object name getter
     * @return string
     */
    public function getName()
    {
        
        return $this->name;
        
    }
    
}

?>
