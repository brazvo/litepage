<?php
/**
 * Trieda nahra prisluchajucu sablonu
 * Trieda pouziva Kniznicu Smarty
 * Sablony sa nacitavaju v tvare: MenocontroleruMenosablony.tpl,
 * cize pre sablonu s nazvom Priklad volanu z kontroleru Basic
 * bude nazov sablony: BasicPriklad.tpl
 *
 * @author BraZvo
 */
require_once APP_DIR . "/libs/smarty/Smarty.class.php";

class Template extends Object {
    
    /** @property string Template file name */
    private $presenter;
    
    /** @property string Template file name */
    private $template;
    
    /** @property string Template file name */
    private $templateDir;
    
    /** @property object */
    public $Smarty;
    
    /** @property object */
    public $CSS;
    
    /** @property object */
    public $JS;
    
    /** @property array */
    private $tplIni;

    public function  __construct( $sAction, $sPresenter ) {
        
        // spustime a nakonfigurujeme Smarty
        $this->Smarty = new Smarty();
        
        // nastavime cestu k hlavnej sablone
        $this->Smarty->setTemplateDir( TPL_DIR . '/' );
        
        //presenter tempalte dir
        $sPresDir = strtolower($sPresenter);
        
        // ocekujeme ci je dopyt na modul
        $module = Environment::getModule();
        $moduleDir = ( $module ? "/modules/" . $module : NULL );
        
        // ak sa vola modul chod do modulu
        if( $moduleDir !== NULL ) {
            $this->templateDir = APP_DIR . $moduleDir . "/templates/{$sPresDir}/";
        }
        // ak mame selector
        else if ( Application::$subDir && $moduleDir === NULL ){
            $this->templateDir = APP_DIR . Application::$subDir . "/templates/{$sPresDir}/";
        }
        // ak nie tak chod do adresara presenteru
        else {
            $this->templateDir = TPL_DIR . "/{$sPresDir}/";
        }
        $this->Smarty->addTemplateDir( $this->templateDir );
        $this->Smarty->compile_dir  = APP_DIR . '/templates/@compiled/';
        $this->Smarty->config_dir   = APP_DIR . '/templates/@configs/';
        $this->Smarty->cache_dir    = APP_DIR . '/templates/@cache/';
        
        // nacitaj INI
        $tplName = current(explode('.', MAIN_TEMPLATE));
        $this->tplIni = parse_ini_file( APP_DIR . "/templates/@configs/$tplName.ini", true );

        // inicializuj CSS
        $this->CSS = new CssLoader();
        // naloaduj styly s INI suboru
        $this->loadStyles();
        
        // inicializuj JS
        $this->JS = new JsLoader();
        // naloaduj scripty s INI suboru
        $this->loadScripts();
        
        $this->name = $sAction;
        $this->presenter = $sPresenter;
        $this->setView( $sAction );
        
    }

    public function setView( $string )
    {
        $this->template = strtolower( $string ) . '.tpl';
        
    }

    public function getView()
    {
        return $this->template;
    }

    public function getVars()
    {
        return $this->vars;
    }
    
    public function getPage()
    {
        
               
        if( file_exists( $this->templateDir . $this->template ) ) {
            // nic neurob
        }
        else {
            throw new Exception("Sablona {$this->templateDir}{$this->template} neexistuje.");
        }
  
        // Priradime globalne premenne do sablony
        $this->Smarty->assignGlobal( 'baseHref', baseUrl() );
        $this->Smarty->assignGlobal( 'sHtmlCss', (string)$this->CSS );
        $this->Smarty->assignGlobal( 'sHtmlJs', (string)$this->JS );
        $this->Smarty->assignGlobal( 'sHtmlMeta', Application::getMeta() );
        $this->Smarty->assignGlobal( 'sHtmlGA', Application::$googleAnalytics );
        $this->Smarty->assignGlobal( 'sLang', Application::$language['code'] );
        $this->Smarty->assignGlobal( 'flashMessages', Environment::getMessages() );
        $this->Smarty->assignGlobal( 'flashErrors', Environment::getErrors() );
        $this->Smarty->assignGlobal( 'user', Application::$logged );
        if( isset( $this->tplIni['FAVICON']['favicon'] ) && $this->tplIni['FAVICON']['favicon'] )
                $this->Smarty->assignGlobal('favicon', $this->tplIni['FAVICON']['favicon']);
        else $this->Smarty->assignGlobal('favicon', '');
        
        $this->Smarty->assignGlobal( 'pagetitle', isset( $this->vars['title'] ) ? $this->vars['title'] . ' | ' . PAGE_TITLE : PAGE_TITLE );
        
        // priradime lokalne premenne
        foreach ( $this->vars as $sKey => $mValue ) {
            
            $this->Smarty->assign( $sKey, $mValue );
            
        }
        
        
        $this->Smarty->display( "extends:".MAIN_TEMPLATE."|{$this->template}" );
    }
    
    /**
     * Nahra CSS styly do $this->CSS
     * @return void
     */
    private function loadStyles()
    {
        if( !isset( $this->tplIni['CSS']['css'] ) or !$this->tplIni['CSS'] ) {
            return null;
        }

        // rozparsujeme jednotlive CSS a priradime ich
        foreach( $this->tplIni['CSS']['css'] as $sStyle ) {

            // ak obsahujeme delic atributov
            if( preg_match( '/[ \t]+/', $sStyle ) ) {
                
                $aStyle = preg_split('/[ \t]+/', $sStyle);

                $sStyle = array_shift($aStyle); // oddelime adresu stylu
                
                $css = $this->CSS->add( $sStyle ); // zinicializujeme si styl
                
                // rozparsujeme atributy
                foreach( $aStyle as $sAttr) {
                    
                    $sAttr = trim($sAttr);
                    
                    // ak nebude atribut v tvare atribut:hodnota
                    // atribut sa nepriradi a vyhodi vynimku
                    if ( !preg_match('/:/', $sAttr ) ) {
                        throw new Exception("Atribut $sAttr neobsahuje povinny delic - dvojbodku.");
                    }
                    else {
                        list( $attribute, $value ) = explode( ':', $sAttr );
                        
                        switch ( $attribute ) {
                            case 'media':
                                $css->setMedia( $value );
                                break;
                            case 'charset':
                                $css->setCharset( $value );
                                break;
                        }
                    }
                    
                }
            }
            // Ak delic nemame rovno inicializujeme styl
            else {
                $css = $this->CSS->add( $sStyle );
            }
            
            
            // Teraz si checkneme ci je to styl pre IE a ak mame sufix _ie, tak ideme na to
            if( preg_match( '/_ie/', $sStyle) ) {
                
                // kuknime ci tam mame aj verziu
                if ( preg_match( '/_ie[0-9]/', $sStyle, $match ) ) {
                    $version = substr( $match[0], -1, 1 );
                }
                else {
                    $version = null;
                }
                
                // kuknime ci ma platit aj pre nizsie verzie
                if ( preg_match( '/_ie[0-9]lte/', $sStyle ) ) {
                    $lower = true;
                }
                else {
                    $lower = false;
                }
                
                // a teraz mu to povieme
                $css->isIE( $version, $lower );
                
            }
            
        }
    }
    
    
    /**
     * Nahra JavaScripty do $this->JS
     * @return void
     */
    private function loadScripts()
    {
        if( !isset( $this->tplIni['JS']['js'] ) or !$this->tplIni['JS'] ) {
            return null;
        }

        // rozparsujeme jednotlive CSS a priradime ich
        foreach( $this->tplIni['JS']['js'] as $sScript ) {

            // ak obsahujeme delic atributov
            if( preg_match( '/[ \t]+/', $sScript ) ) {
                
                $aScript = preg_split( '/[ \t]+/', $sScript );

                $sScript = array_shift($aScript); // oddelime adresu scriptu
                
                $css = $this->JS->add( $sScript ); // zinicializujeme si script
                
                // rozparsujeme atributy
                foreach( $aScript as $sAttr) {
                    
                    $sAttr = trim($sAttr);
                    
                    // ak nebude atribut v tvare atribut:hodnota
                    // atribut sa nepriradi a vyhodi vynimku
                    if ( !preg_match('/:/', $sAttr ) ) {
                        throw new Exception("Atribut $sAttr neobsahuje povinny delic - dvojbodku.");
                    }
                    else {
                        list( $attribute, $value ) = explode( ':', $sAttr );
                        
                        switch ($attribute) {
                            case 'media':
                                $css->setMedia( $value );
                                break;
                            case 'charset':
                                $css->setCharset( $value );
                                break;
                        }
                    }
                    
                }
            }
            // Ak delic nemame rovno inicializujeme script
            else {
                $css = $this->JS->add( $sScript );
            }
            
            
            // Teraz si checkneme ci je to script pre IE a ak mame sufix _ie, tak ideme na to
            if( preg_match( '/_ie/', $sScript) ) {
                
                // kuknime ci tam mame aj verziu
                if ( preg_match( '/_ie[0-9]/', $sScript, $match ) ) {
                    $version = substr($match[0], -1, 1);
                }
                else {
                    $version = null;
                }
                
                // kuknime ci ma platit aj pre nizsie verzie
                if ( preg_match( '/_ie[0-9]lte/', $sScript ) ) {
                    $lower = true;
                }
                else {
                    $lower = false;
                }
                
                // a teraz mu to povieme
                $css->isIE( $version, $lower );
                
            }
            
        }
    }
}
?>
