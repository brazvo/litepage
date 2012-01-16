<?php
/**
 * Control vykresluje komponenty / ovladacie prvky nadefinovane v controlleroch cez
 * tovarnicky createControlNazovKomponenty
 * @author Branislav Zvolensky
 * @package Core
 */
class Control extends Object {
    
    private  $htmlStatement;

    public function __construct( $object, $method ) {
               
        $this->object = $object;
        $this->method = $method;
        //$this->htmlStatement = (string)$object->$method();
        
    }
    
    
    public function __toString() {
        $object = $this->object;
        $method = $this->method;
        return (string)$object->$method();
    }
}

