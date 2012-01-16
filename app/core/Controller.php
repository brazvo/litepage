<?php

/**
 * Abstract class Controller
 *
 * @author BraZvo
 */
abstract class Controller extends Object implements iCoreController {
    
    
    protected $template;

    protected $action;
    
    protected $handler;

    protected $id;

    protected $user;

    protected $texy;
    
    protected $renderMethod;
    
    protected $actionMethod;

    /**
     *
     * @var array
     */
    protected $vars = array();

    public function  __construct() {
        
        $REQUEST = Vars::get( 'REQUEST' );
        
        $this->name = Environment::getController();
        $this->action = Environment::getAction();
        $this->handler = isset ( $REQUEST->do ) ? $REQUEST->do : NULL;
        $this->id = Environment::getId();
        $this->user = Environment::getUser();
        
        $this->template = new Template($this->action, $this->name);

        //Setup texy
	$this->texy = new Texy;
	$this->texy->encoding = Config::core('encoding');
	$this->texy->imageModule->root = basePath() . '/images/';
	$this->texy->headingModule->balancing = TexyHeadingModule::FIXED;

        $this->actionMethod = 'action'.ucfirst( $this->action );
        $handler = 'handle'.ucfirst( $this->handler );
        $this->renderMethod = 'render'.ucfirst( $this->action );

        $rc = new ReflectionClass( $this );
            
        // Execution
        if( $rc->hasMethod('startUp') ) $this->startUp( $this->id );        
        if( $rc->hasMethod( $this->actionMethod ) ) {
            $action = $this->actionMethod;
            $this->$action( $this->id );
        }
        
        // Interaction
        if( $this->handler ) {
            if( $rc->hasMethod( 'beforeHandle' ) ) $this->beforeHandle( $this->id );
            if( $rc->hasMethod( $handler ) ) $this->$handler( $this->id );
        }
        
        // After execution and Interaction look for component factories
        $aMethods = $rc->getMethods( ReflectionMethod::IS_PUBLIC );
        foreach ( $aMethods as $oMethod ) {
            if( preg_match('/createControl/', $oMethod->name) ) {
                
                $sCompName = lcfirst( preg_replace('/createControl/i', '', $oMethod->name ) );
                $sTplCompName = 'control' . preg_replace('/createControl/i', '', $oMethod->name );
                $this->template->$sTplCompName = $this->$sCompName = $this[$sCompName] = new Control( $this, $oMethod->name );
                
            }
        }
        
        // before render
        if( $rc->hasMethod( 'beforeRender' ) ) $this->beforeRender( $this->id );

        
        // Rendering        
        if( $rc->hasMethod( $this->renderMethod ) ) {
            $render = $this->renderMethod;
            $this->$render( $this->id );
        }
        else {
            throw new Exception( "Metoda {$this->renderMethod} nie je definovana v contollery {$this->name}" );
        }
        
        // Shut down
        if( $rc->hasMethod( 'shutDown' ) ) $this->shutDown( $this->id );
        
        // collect flash messages
        $this->template->flashErrors = Application::getErrors();
        $this->template->flashMessages = Application::getMessages();
        
    }
    
    public function run()
    {
        $this->template->getPage();
    }

    
    public function getTemplate()
    {
        return $this->template;
    }
    
    protected function flashMessage( $sMessage )
    {
        //Environment::setMessage( $sMessage );
        Application::setMessage( $sMessage );
    }
    
    protected function flashError( $sError )
    {
        //Environment::setError( $sError );
        Application::setError( $sError );
    }
    
    protected function setRender( $sName )
    {
        $this->renderMethod = 'render'.ucfirst($sName);
        $this->template->setView($sName);
    }
    
    
    ///////////////////////////////////////// Array access
    public function offsetGet($offset) {
        return isset($this->container[$offset]) ? (string)$this->container[$offset] : null;
    }

}