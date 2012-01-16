<?php

class Environment implements ICore {
	      
        /** @property array */
        private static $globals;
        
        /** @property array */
        private static $user;
        
        /** @property array */
        private static $errors = array();
        
        /** @property array */
        private static $messages = array();
    
	public static function Init () {
            // check for session messages after redirect
            if( isset( $_SESSION[md5( baseUrl() )]['message'] ) && $_SESSION[md5( baseUrl() )]['message'] ) {
                self::setMessage( $_SESSION[md5( baseUrl() )]['message'] );
                $_SESSION[md5( baseUrl() )]['message'] = null;
            }
	}
        
        /**
         * Nastavuje globalne dostupnu pemennu
         * @param string $sName
         * @param mixed $mValue 
         */
        public static function set($sName = null, $mValue = null)
        {
            if( !$sName && !is_string( $sName ) ) throw new Exception('Environment::set, parameter $sName musí byť reťazec a nesmie byť NULL');
            
            else self::$globals[$sName] = $mValue;
        }
        
        /**
         * Vracia globalne dostupnu premennu
         * @param string $sName 
         */
        public static function get($sName = null)
        {
            
            if( !isset( self::$globals[$sName] ) ) throw new Exception("Environment::get, hodnota s parametrom '$sName' nie je nastavena");
            
            else return self::$globals[$sName];
            
        }
        
             
        /**
         * Returns user info
         * @return array
         */
        public static function getUser()
        {
            return self::$user;
        }
        
        
        ///////////////////////////////////
        // Setery a Getery pre route     //
        ///////////////////////////////////
        
        /**
         * Nastavuje Path prefix
         * @param string $string 
         */
        public static function setPathPrefix( $string )
        {
            self::$globals['sPathPrefix'] = $string ;
        }
        
        /**
         * Vracia path prefix
         * @return string
         */
        public static function getPathPrefix()
        {
            return isset( self::$globals['sPathPrefix'] ) ? self::$globals['sPathPrefix'] : '';
        }
        
        
        /**
         * Nastavuje Controller
         * @param string $string 
         */
        public static function setController( $string )
        {
            self::$globals['sController'] = $string ;
        }
        
        /**
         * Vracia meno Controllera
         * @return string
         */
        public static function getController()
        {
            return isset( self::$globals['sController'] ) ? self::$globals['sController'] : 'basic';
        }
        
        
        /**
         * Nastavuje akciu
         * @param string $string 
         */
        public static function setAction( $string )
        {
            self::$globals['sAction'] = $string ;
        }
        
        /**
         * Vracia meno akcie
         * @return string
         */
        public static function getAction()
        {
            return isset( self::$globals['sAction'] ) ? self::$globals['sAction'] : 'show';
        }
        
        
        /**
         * Nastavuje ID
         * @param string $string 
         */
        public static function setId( $string )
        {
            self::$globals['sID'] = $string ;
        }
        
        /**
         * Vracia ID
         * @return string
         */
        public static function getId()
        {
            return isset( self::$globals['sID'] ) ? self::$globals['sID'] : NULL;
        }
        
        
        /**
         * Nastavuje meno Modulu
         * @param string $string 
         */
        public static function setModule( $string )
        {
            self::$globals['sModule'] = $string ;
        }
        
        /**
         * Vracia ID
         * @return string
         */
        public static function getModule()
        {
            return isset( self::$globals['sModule'] ) ? self::$globals['sModule'] : NULL;
        }
        
        
        /**
         * Nastavuje selektory s routu
         * @param string $string 
         */
        public static function setSelector( $sKey )
        {
            self::$globals['aSelectors'][$sKey] = 1 ;
        }
        
        /**
         * Vracia konkretny selektor
         * @param integer $idx index selektoru
         * @return string | void
         */
        public static function getSelector($sKey = 'xXxXxX')
        {
            return isset( self::$globals['aSelectors'][$sKey] ) ? self::$globals['aSelectors'][$sKey] : NULL;
        }
        
        /**
         * Vracia selektory
         * @return array
         */
        public static function getSelectors()
        {
            return isset( self::$globals['aSelectors'] ) ? self::$globals['aSelectors'] : Array();
        }
        
        /**
         * Nastavuje info o jazyku
         * @param type $aLang 
         */
        public static function setLanguage( $aLang )
        {
            self::$globals['aLanguage'] = $aLang;
        }
        
        /**
         * Vracia info o jazyku
         * @return type mixed array|void
         */
        public static function getLanguage()
        {
            return isset( self::$globals['aLanguage'] ) ? self::$globals['aLanguage'] : NULL;
        }
        
        /**
	 * @title Set Error
	 * global set of an error message
	 */
	public static function setError($error)
	{
		self::$errors[] = $error;
	}
	
	/**
	 * @title Get Errors
	 * returns errors in container
	 */
	public static function getErrors()
	{
		$output = '';
		if(!empty(self::$errors)){
		  $messages = '';
		  foreach(self::$errors as $message){
		    $messages .= "<p>$message</p>";
		  }

		  $output .= "<div class=\"flash-errors\">".$messages."</div>";
		}
		
		return $output;
	}
	
	/**
	 * @title Set Message
	 * global set of a message
	 */
	public static function setMessage($message)
	{
		self::$messages[] = $message;
	}
	
	/**
	 * @title Get Messages
	 * returns messages in container
	 */
	public static function getMessages()
	{
		if(!empty(self::$messages)){
		  $messages = '';
		  foreach(self::$messages as $message){
		    $messages .= "<p>$message</p>";
		  }
		  return "<div class='flash-messages'>$messages</div>";
		}
		else{
		  return "";
		}
	}


        //////////////////////////////

}