<?php

/**
 * subor: JsLoader.php
 * @version 0.11
 * @author B ZVolensky (BZ)
 * =============================================================
 * 
 * @tutorial
 * 
 * Ak chces nahrat JS do stranky alebo len lokalne (pre urcitu kategoriu alebo URI)
 * na to sluzi objekt $this->template->JS, do ktoreho pridavas jednotlive scripty pomocou
 * metody add(). Globalny definicny subor pre scripty najdes v subore Template.ini
 * 
 * Metoda add() ocakava 5 parametrov $path (povinny), $id_cat (nepovinny), $request (nepovinny) $charset (nepovinny)
 * 
 * $path - relativna cesta k suboru odvodena od document rootu napr. scripte.js alebo js/scripte.js
 * $id_cat - cislo kategorie, pre ktoru sa bude script natahovat. Ak zadas napr. 2100,
 *           tak script bude platit pre www.osporte.sk/index.php?id_cat=2100,
 *           ale aj pre www.osporte.sk/index.php?id_cat=2100&subpage=podtsranka5
 * 
 * $request - sluzi na presne urcenie stranky pre ktoru script plati.
 *            Zadava sa v tvare napr. index.php?id_cat=2100&subpage=5 co bude platit pre
 *            platit pre www.osporte.sk/index.php?id_cat=2100&subpage=5,
 *            alebo aj pre www.osporte.sk/index.php?id_cat=2100&subpage=5&showform=1
 *            ale uz nie pre www.osporte.sk/index.php?id_cat=2100&subpage=4.
 *            Z toho vyplyva, ze cim presnejsie definujes adresu, tym presnejsie
 *            vies ovplyvnit natiahnutie daneho scriptu
 * $charset - urcuje znakovu sadu scriptu
 * 
 * Volat metodu add() mozes dvoma sposobmi:
 * 
 * 1. priamim zadanim parametrov napr. $this->template->JS->add('js/mojscript.js', 'screen', null, 'index.php?id_cat=2100&sb=podstranka');
 * 
 * 2. fluentnym (hladkym nastavenim) iba potrebnych parametrov cez set metody setMedia(), setCat(), setRequest()
 *    
 *    priklad: $this->template->JS->add('js/mojscript.js')->setMedia('screen')->setRequest('index.php?id_cat=2100&sb=podstranka');
 *    
 *    vyhoda tohoto zapisu je v tom, ze ta to nenuti zadavat (definovat) nepotrebne parametre
 *    a zaroven v metodach setCat() alebo setRequest() mozes odovdzat aj viac parametrov napriklad:
 *    $this->template->JS->add('script.js')->setCat(2100, 2101, 2105);
 *    $this->template->JS->add('script.js')->setRequest('index.php?id_cat=2100&sb=5', 'index.php?id_cat=2100&sb=8');
 * 
 * Lokalne definovat JS script, je idealne v metode presenteru beforeRender(), kedy bude JS script nacitany vzdy,
 * ked sa nacita presenter.
 * Taktiez mozeme JS script nacitat v niektorej metode (napr. renderShow() ) a vtedy bude JS script nacitany, pri volani
 * tejto vykreslovacej metody.
 * 
 * 
 * Priklad nahratia globalneho JS scriptu v subore Template.ini (vid. navod v Template.ini):
 * 
 * js[] = js/script.js
 * js[] = js/script.js charset:utf-8
 * 
 * Priklad nahratia lokalneho JS stylu:
 * $this->template->JS->add('js/script.js')
 * $this->template->JS->add('js/script.js')->setCharset('utf-8');
 * 
 * priklady pouzitia len pre IE
 * 
 * na urcenie toho, ze script je len pre IE je mozne pouzit metodu isIE(), ktora ocakava dva nepovinne parametre 
 * $version, $andLower defaultne nastavene na NULL a FALSE
 * 
 * $this->template->JS->add('script')->isIE(); vykresli podmieneny komentar <!--[if IE]>
 * $this->template->JS->add('script')->isIE(7); vykresli podmieneny komentar <!--[if IE 7]>
 * $this->template->JS->add('script')->isIE(7, true); vykresli podmieneny komentar <!--[if lte IE 7]>
 *
 * Historia:
 * ========
 * v0.11
 * Pridane overenie existencie js suboru v metode render() a
 * k JS suborom je automaticky pridana timestamp, takze po zmene v subore sa automaticky
 * nacita aktualna verzia
 */

class JsScript {
    
    private $prototype = '%iestart%<script type="text/javascript" %src%%charset%></script>%ieend%';
    
    private $path;
    
    private $charset;
    
    private $isIE = array();
    
    public $id_cat = array();
    
    public $request = array();
    
    public function __construct($path, $id_cat = null, $request = null, $charset = null) {
        
        $this->path = urldecode($path);
        if($id_cat) $this->setCat($id_cat);
        if($request) $this->setRequest($request);
        if($charset) $this->setCharset($media);
        
    }  
    
    /**
     * nastavuje hodnotu pre atribut charset v tagu link
     * @param string $string
     * @return object JsScript (pre ľahké volanie ďalších metód)
     */
    public function setCharset($string)
    {
        if(!$string or !is_string($string)) die('JsScript::setCharset(): parameter $string musi byt reťazec');
        $this->charset = $string;
        return $this;
    }
    
    /**
     * nastavuje hodnotu id kategorie pre ktoru ma byt styl js nacitany
     * @param integer $cat [, $secondCat [, $thirdCat]]
     * @return object JsScript (pre ľahké volanie ďalších metód) 
     */
    public function setCat($cat = null)
    {
        if(!$cat) die('JsScript::setCat(): Musi byt zadany aspon jeden parameter');
        
        foreach(func_get_args() as $catNr) {
            
            if(0 === (int)$catNr) die('JsScript::setCat(): parametre musia byt cele cislo vecsie od  0');
        
            $this->id_cat[] = (int)$catNr;
        
        }
        
        return $this;
    }
    
    /**
     * nastavuje retazec z uri, podla ktoreho bude identifikovana stranka, pre ktoru sa ma natiahnut styl JS
     * @param string $string
     * @return object JsScript (pre ľahké volanie ďalších met 
     */
    public function setRequest($string = null)
    {
        
        if(!$string) die('JsScript::setRequest(): Musi byt zadany aspon jeden parameter');
        
        
        foreach(func_get_args() as $reqStr) { 
        
            if(!$reqStr or !is_string($reqStr)) die('JsScript::setRequest(): parametre musia byt reťazec');
        
            $this->request[] = $reqStr;
        
        }
        
        return $this;
        
    }
    
    /**
     * Nastavuje ci ma byt styl pouzity len pre IE
     * @param integer $version
     * @param bool $andLower urcuje ak je nastavena verzia, ci bude styl pouzity aj pre nizsie verzie
     * @return JsScript 
     */
    public function isIE($version = null, $andLower = false)
    {
        $this->isIE['version'] = $version;
        if($this->isIE['version']) $this->isIE['lower'] = $andLower;
            else $this->isIE['lower'] = false;
        
        return $this;
    }



    /**
     * vracia hotovy html tag link
     * @return string (HTML content) 
     */
    public function render()
    {
        
        // check if external file is loaded
        if( !preg_match( '/http:/', $this->path ) && !file_exists( $this->path ) ) {
            return '';
        }
        else if ( !preg_match( '/http:/', $this->path ) && file_exists( $this->path ) ) {
            $timestamp = filemtime($this->path);
            $basePath = basePath();
        }
        else {
            $timestamp = date('U');
            $basePath = '';
        }
        
        // add time stamp to file
        $this->path = $basePath . $this->path . ( $timestamp ? "?{$timestamp}" : '');

        // check if for ie
        if($this->isIE) {
            
            $lte = ( $this->isIE['lower'] ? 'lte ' : '');
            $version = ( $this->isIE['version'] ? ' '.$this->isIE['version'] : '');
                
            $iestart = "<!--[if {$lte}IE{$version}]>";
            $ieend = '<![endif]-->';
        }
        else {$iestart = ''; $ieend = '';}
        
        $charset = $this->charset ? "charset=\"{$this->charset}\" " : "";
        $src = "src=\"{$this->path}\" ";
        
        $patterns = array('/%iestart%/', '/%src%/', '/%charset%/', '/%ieend%/');
        $replacements = array($iestart, $src, $charset, $ieend);
        
        return preg_replace($patterns, $replacements, $this->prototype)."\n";
    }
    
    /**
     * zavovola metodu render() pri echovani objektu
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}

class JsLoader {
    
    private $jsContainer = array();
    
    private $uri;
    
    private $id_cat;
    
    public function __construct() {
        
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->id_cat = (isset($_GET['id_cat']) ? (int)$_GET['id_cat'] : null);
        
    }
    
    
    public function add($path = null, $id_cat = null, $request = null, $charset = null)
    {
        
        if(!$path or !is_string($path)) die('JsLoader::add(): Parameter $path musi byt zadany a musi byt retazec');
        
        return $this->jsContainer[] = new JsScript($path, $id_cat, $request, $charset);
        
    }
    
    
    public function getScripts()
    {
        
        $output = '';
        
        foreach($this->jsContainer as $script) {
            
            
            
            // ak su nastavene id_cat alebo request,
            // tak natiahni styl len pre toto id_cat alebo request
            if($script->id_cat or $script->request) {
                
                               
                if($this->id_cat > 0 && in_array($this->id_cat, $script->id_cat) ) {
                    $output .= $script;
                }
                elseif( $script->request ) {
                    foreach($script->request as $request) {
                        
                        // pridaj backslashe  a vytvor regex
                        $uriPattern = '/'.addcslashes($request, "/.?").'/';
                        
                        if(preg_match($uriPattern, $this->uri)){
                            $output .= $script;
                        }
                        
                    }
                }
                
            }
            else {
                
                $output .= $script;
                
            }
            
        }
        
        return $output;
        
    }
    
    
    public function __toString() {
        return $this->getScripts();
    }
}