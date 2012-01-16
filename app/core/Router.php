<?php
/**
 * Trieda, ktora ktorá roztriedi prijaty URL retazec za domennovym menom a nastavi
 * language, [selektory, modul,] controller, action a [id]. Controller, action su povinne parametre
 * pre chod aplikacie, preto sa v subore config.ini nastavuje ich vychozia hodnota.
 *
 * @author brazvo
 * @package Core
 */
class Router implements ICore {

    private $routes;

    public static function init () {

        $obj = new self;

        $GET = Vars::get( 'GET' );
        
        /////////////////////////////////// Language
        // get default language settings
        $aDefLang = $obj->getDefaultLang();
        $aLang = array();

        if ( isset( $GET->q ) ) {

            $q = $GET->q;
            
            Environment::set('frontPage', false);
            
            // check language code
            $sLang = $obj->parsePath( $q, 0 );
            if( $sLang ) {
                
                $aLang = $obj->checkLanguage($sLang);
                
                if( !empty( $aLang ) ) {
                    // remove lang code from query
                    $q = preg_replace("/^{$aLang['code']}[\/]?/", '', $q);
                    
                    if( empty( $q ) ) {
                        // if def lang is the same as from request redirect to homepage
                        if( $aDefLang['code'] === $aLang['code'] ) {
                            redirect();
                        }
                        else {
                            $q = $aLang['main_page'];
                            Environment::set('frontPage', true);
                            $_SESSION['lang'] = $aLang['code'];
                        }
                    }
                    
                }
                
            }
            else {
                unset( $_SESSION['lang'] );
            }

        }
        else {
            $q = $aDefLang['main_page'];
            Environment::set('frontPage', true);
            unset( $_SESSION['lang'] );
        }
        // set language to environment
        !empty( $aLang ) ? Environment::setLanguage( $aLang ) : Environment::setLanguage( $aDefLang );
        /////////////////////////////////

        $q = PathAlias::getRoute( $q ); // kukni ci nemame peknu URL
        
        Environment::set('q', $q);

        $obj->routes = Config::router();

        if ( $obj->routes ) {

            $continue = false;

            foreach ( $obj->routes as $sRoute ) {

                preg_match_all( '/[<]?([^<][0-9a-z_-]+)./i', $sRoute, $matches );

                if ( isset( $q ) ) {

                    if ( substr( $q, -1, 1 ) !== '/' )
                            $q .= '/'; // ak nemame koncove lomitko doplnime
                    
                    Environment::set('q', $q);
                    
                    /*
                    // Vyextrahuj language
                    foreach ( $matches[0] as $iKey => $sMatch ) {
                        if( $sMatch === '<lang>' ) {
                            $sLanguage = $obj->parsePath( $q, 0 );
                            if ( $sLanguage ) {
                                $mLanguage = $obj->checkLanguage( $sLanguage );
                                if( $mLanguage ) {
                                    Environment::setLanguage( $mLanguage );
                                    
                                    // update value in environment
                                    Environment::set('q', $q);
                                }                                   
                            }
                        }
                    }
                     * 
                     */


                    // Vyextrahujeme selektory ak nejake mame
                    Environment::setPathPrefix( '' );

                    $iRealKey = 0;

                    foreach ( $matches[0] as $iKey => $sMatch ) {

                        $sMatch = preg_replace('/^\//', '', $sMatch);
                        $sMatchPattern = addcslashes( $sMatch, '/' );
                        if ( preg_match( "/{$sMatchPattern}/", $q ) ) {

                            //$q = preg_replace( "/{$sMatchPattern}/", '', $q );
                            
                            // update value in environment
                            Environment::set('q', $q);

                            // ak mame v route viac prefixov, naskladame ich za seba
                            Environment::setPathPrefix( Environment::getPathPrefix() . $sMatch );

                            // Nastavime nazov selektoru do globalu.
                            // V aplikacii mozeme overovat pritomnost selektoru
                            // cez Environment::getSelector( $nazov_selektoru ),
                            // ktora bud vrati null alebo 1,
                            // alebo cez Environment::getSelectors(),
                            // ktora vrati pole so selektormi alebo prazdne pole.
                            // Selektor sa nastavuje ako $nazov_selktoru => 1
                            $selector = preg_replace('/\//', '', $matches[1][$iKey]);
                            Environment::setSelector( $selector );
                            
                            // nacitame configuraciu
                            $cfg = Config::system();
                            
                            // nacitame default parametre pre selektor
                            if( isset( $cfg['router'][$selector.'.controller'] ) )
                                Environment::setController( $cfg['router'][$selector.'.controller'] );
                            
                            if( isset( $cfg['router'][$selector.'.action'] ) )
                                Environment::setAction( $cfg['router'][$selector.'.action'] );
                            
                            if( isset( $cfg['router'][$selector.'.id'] ) )
                                Environment::setId( !empty( $cfg['router'][$selector.'.id'] ) ? $cfg['router'][$selector.'.id'] : NULL );

                            // zdvihneme RealKey pre dalsie pouzitie
                            $iRealKey++;

                            // dalsie nehladaj
                            $continue = true;
                        }
                    }


                    // Nakrmime modul, controller, akciu a id
                    foreach ( $matches[0] as $iKey => $sMatch ) {

                        $iIndex = $iKey - $iRealKey;

                        switch ( $sMatch ) {
                                                        
                            /** @todo doriesit moduly */
                            case '<module>':
                                $sModule = $obj->parsePath( $q, $iIndex );
                                if ( $sModule ) {
                                    $bIsModule = $obj->checkModule( $sModule );

                                    if ( $bIsModule ) {
                                        Environment::setModule( $sModule );
                                        $obj->setModuleDefaults( $sModule );
                                        $continue = true;
                                    }
                                }
                                break;
                            case '<controller>':
                                $sController = $obj->parsePath( $q, $iIndex );
                                if ( $sController ) {
                                    if( preg_match( '/-/', $sController ) ) {
                                        // ak meno controllera obsahuje pomlcky, spojime meno do jedneho stringu

                                        $aContName = preg_split( '/-/', $sController, null, PREG_SPLIT_NO_EMPTY );
                                        $sController = ''; // sController znova
                                        foreach ($aContName as $sPartName) {
                                            $sController .= ucfirst($sPartName);
                                        }

                                    }
                                    Environment::setController( $sController );
                                }
                                break;
                            case '<action>':
                                $sAction = $obj->parsePath( $q, $iIndex );
                                if ( $sAction ){
                                    if( preg_match( '/-/', $sAction ) ) {
                                        // ak meno akcie obsahuje pomlcky, spojime meno do jedneho stringu

                                        $aContName = preg_split( '/-/', $sAction, null, PREG_SPLIT_NO_EMPTY );
                                        $sAction = ''; // sAction znova
                                        foreach ($aContName as $sPartName) {
                                            $sAction .= ucfirst($sPartName);
                                        }

                                    }
                                    Environment::setAction( $sAction );
                                }
                                break;
                            case '<id>':
                                $mId = $obj->parsePath( $q, $iIndex );
                                if ( $mId )
                                        Environment::setId( $mId );
                                break;
                        }
                    }
                }

                if ( $continue )
                        break;
            }
        }
    }

    /**
     * Parsuje Http Query na parametre
     * @param string $sQuery
     * @param integer $iKey
     * @return string
     */
    private function parsePath ( $sQuery, $iKey ) {

        if ( $sQuery === '' )
                return NULL; // ked nemam string tak vrat prazdnu hodnotu

        $params = preg_split( '/\//', $sQuery, null, PREG_SPLIT_NO_EMPTY );

        return isset( $params[$iKey] ) ? $params[$iKey] : NULL;
    }

    /**
     * Skontroluje, ci existuje adresar modulu
     * @param string $sModule
     */
    private function checkModule ( $sModule ) {

        $aModules = array( );
        $d = dir( APP_DIR . "/modules" );
        while ( false !== ($entry = $d->read()) ) {
                if ( substr( $entry, 0, 1 ) !== '.' ) {
                        $aModules[] = $entry;
                }
        }
        $d->close();

        if ( in_array( $sModule, $aModules ) )
                return true;
        else
                return false;
    }
    
    
    private function setModuleDefaults( $sModule )
    {
        if(file_exists(APP_DIR . "/modules/{$sModule}/module.ini")) {
            $ini = parse_ini_file(APP_DIR . "/modules/{$sModule}/module.ini");

            if( isset( $ini['controller'] ) )
                Environment::setController( $ini['controller'] );
            if( isset( $ini['action'] ) )
                Environment::setAction( $ini['action'] );
            if( isset( $ini['id'] ) )
                Environment::setId( $ini['id'] );
        }
    }
    
    
    /**
     * Skontroluje, ci existuje jazyk
     * @param string $sModule
     */
    private function checkLanguage ( $sLang ) {

        $row = db::fetch("SELECT * FROM languages WHERE langid='$sLang' AND active=1");
	if($row){
            $ret['code'] = $row['langid'];
            $ret['mach_name'] = $row['eng_machine_name'];
            $ret['name'] = $row['name'];
            $ret['main_page'] = $row['main_page_path'];
            return $ret;	
	}
	else{
            return false;	
	}
    }
    
    /**
     * @title Get Default Language
     * @return array
     * returns array of lang informations
     */
    private function getDefaultLang()
    {

        $row = db::fetch("SELECT * FROM languages WHERE main_lang=1");
        if($row){
                $ret['code'] = $row['langid'];
                $ret['mach_name'] = $row['eng_machine_name'];
                $ret['name'] = $row['name'];
                $ret['main_page'] = $row['main_page_path'];
                return $ret;	
        }
        else{
                $ret['code'] = 'sk';
                $ret['mach_name'] = 'slovak';
                $ret['name'] = 'Slovenčina';
                $ret['main_page'] = 'hlavna-stranka';
                return $ret;	
        }

    }

}
