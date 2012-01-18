<?php

/**
 * subor: CssLoader.php
 * @version 0.12
 * @author B ZVolensky (BZ)
 * =============================================================
 * 
 * @tutorial
 * 
 * Ak chces nahrat CSS do stranky alebo len lokalne (pre urcitu kategoriu alebo URI)
 * na to sluzi objekt $this->template->CSS, do ktoreho pridavas jednotlive styly pomocou
 * metody add(). Globalny definicny subor pre styly najdes v subore Template.ini
 * 
 * Metoda add() ocakava 5 parametrov $path (povinny), $media (nepovinny), $id_cat (nepovinny), $request (nepovinny) $charset (nepovinny)
 * 
 * $path - relativna cesta k suboru odvodena od document rootu napr. style.css alebo css/style.css
 * $media - typ media, pre ktore je styl (screen, print, all...)
 * $id_cat - cislo kategorie, pre ktoru sa bude styl natahovat. Ak zadas napr. 2100,
 *           tak styl bude platit pre www.osporte.sk/index.php?id_cat=2100,
 *           ale aj pre www.osporte.sk/index.php?id_cat=2100&subpage=podtsranka5
 * 
 * $request - sluzi na presne urcenie stranky pre ktoru styl plati.
 *            Zadava sa v tvare napr. index.php?id_cat=2100&subpage=5 co bude platit pre
 *            platit pre www.osporte.sk/index.php?id_cat=2100&subpage=5,
 *            alebo aj pre www.osporte.sk/index.php?id_cat=2100&subpage=5&showform=1
 *            ale uz nie pre www.osporte.sk/index.php?id_cat=2100&subpage=4.
 *            Z toho vyplyva, ze cim presnejsie definujes adresu, tym presnejsie
 *            vies ovplyvnit natiahnutie daneho stylu
 * $charset - urcuje znakovu sadu stylu
 * 
 * Volat metodu add() mozes dvoma sposobmi:
 * 
 * 1. priamim zadanim parametrov napr. $this->template->CSS->add('css/mojstyl.css', 'screen', null, 'index.php?id_cat=2100&sb=podstranka');
 * 
 * 2. fluentnym (hladkym nastavenim) iba potrebnych parametrov cez set metody setMedia(), setCat(), setRequest()
 *    
 *    priklad: $this->template->CSS->add('css/mojstyl.css')->setMedia('screen')->setRequest('index.php?id_cat=2100&sb=podstranka');
 *    
 *    vyhoda tohoto zapisu je v tom, ze ta to nenuti zadavat (definovat) nepotrebne parametre
 *    a zaroven v metodach setCat() alebo setRequest() mozes odovdzat aj viac parametrov napriklad:
 *    $this->template->CSS->add('styl.css')->setCat(2100, 2101, 2105);
 *    $this->template->CSS->add('styl.css')->setRequest('index.php?id_cat=2100&sb=5', 'index.php?id_cat=2100&sb=8');
 * 
 * Lokalne definovat CSS styl, je idealne v metode presenteru beforeRender(), kedy bude CSS styl nacitany vzdy,
 * ked sa nacita presenter.
 * Taktiez mozeme CSS styl nacitat v niektorej metode (napr. renderShow() ) a vtedy bude CSS styl nacitany, pri volani
 * tejto vykreslovacej metody. 
 * 
 * Priklad nahratia globalneho CSS stylu:
 * 
 * css[] = css/style.css
 * css[] = css/print.css media:print charset:utf-8
 * css[] = css/style_ie7lte.css
 * css[] = css/style_ie.css
 * 
 * Priklad nahratia lokalneho CSS stylu:
 * 
 * $this->template->CSS->add('css/style.css');
 * $this->template->CSS->add('css/style.css')->setMedia('screen');
 * $this->template->CSS->add('css/style.css')->setMedia('screen')->setCharset('utf-8');
 * 
 * priklady pouzitia len pre IE
 * 
 * na urcenie toho, ze styl je len pre ie je mozne pouzit metodu isIE(), ktora ocakav dva nepovinne parametre 
 * $version, $andLower defaultne nastavene na NULL a FALSE
 * 
 * $this->template->CSS->add('css/style')->isIE(); vykresli podmieneny komentar <!--[if IE]>
 * $this->template->CSS->add('css/style_ie7.css')->isIE(7); vykresli podmieneny komentar <!--[if IE 7]>
 * $this->template->CSS->add('css/style_ie7lte.css')->isIE(7, true); vykresli podmieneny komentar <!--[if lte IE 7]>
 *
 * Historia:
 * ========
 * v0.11
 * Pridane overenie existencie css suboru v metode render() a
 * k CSS suborom je automaticky pridana timestamp, takze po zmene v subore sa automaticky
 * nacita aktualna verzia
 * 
 * v0.12
 * Pri suboroch pre IE detekcia priznaku lte
 */

class CssStyle {
    
    private $prototype = '%iestart%<link rel="stylesheet" type="text/css" %href%%media%%charset%/>%ieend%';
    
    private $path;
    
    private $media;
    
    private $charset;
    
    private $isIE = array();
    
    public $id_cat = array();
    
    public $request = array();
    
    public function __construct($path, $media = null, $id_cat = null, $request = null, $charset = null) {
        
        $this->path = $path;
        if($media) $this->setMedia($media);
        if($id_cat) $this->setCat($id_cat);
        if($request) $this->setRequest($request);
        if($charset) $this->setCharset($media);
        
    }
    
    /**
     * nastavuje hodnotu pre atribut media v tagu link
     * @param string $string
     * @return object CssStyle (pre ľahké volanie ďalších metód)
     */
    public function setMedia($string)
    {
        if(!$string or !is_string($string)) die('CssStyle::setMedia(): parameter $string musi byt reťazec');
        $this->media = $string;
        return $this;
    }
    
    
    /**
     * nastavuje hodnotu pre atribut charset v tagu link
     * @param string $string
     * @return object CssStyle (pre ľahké volanie ďalších metód)
     */
    public function setCharset($string)
    {
        if(!$string or !is_string($string)) die('CssStyle::setCharset(): parameter $string musi byt reťazec');
        $this->charset = $string;
        return $this;
    }
    
    /**
     * nastavuje hodnotu id kategorie pre ktoru ma byt styl css nacitany
     * @param integer $cat [, $secondCat [, $thirdCat]]
     * @return object CssStyle (pre ľahké volanie ďalších metód) 
     */
    public function setCat($cat = null)
    {
        if(!$cat) die('CssStyle::setCat(): Musi byt zadany aspon jeden parameter');
        
        foreach(func_get_args() as $catNr) {
            
            if(0 === (int)$catNr) die('CssStyle::setCat(): parametre musia byt cele cislo vecsie od  0');
        
            $this->id_cat[] = (int)$catNr;
        
        }
        
        return $this;
    }
    
    /**
     * nastavuje retazec z uri, podla ktoreho bude identifikovana stranka, pre ktoru sa ma natiahnut styl CSS
     * @param string $string
     * @return object CssStyle (pre ľahké volanie ďalších met 
     */
    public function setRequest($string = null)
    {
        
        if(!$string) die('CssStyle::setRequest(): Musi byt zadany aspon jeden parameter');
        
        
        foreach(func_get_args() as $reqStr) { 
        
            if(!$reqStr or !is_string($reqStr)) die('CssStyle::setRequest(): parametre musia byt reťazec');
        
            $this->request[] = $reqStr;
        
        }
        
        return $this;
        
    }
    
    /**
     * Nastavuje ci ma byt styl pouzity len pre IE
     * @param integer $version
     * @param bool $andLower urcuje ak je nastavena verzia, ci bude styl pouzity aj pre nizsie verzie
     * @return CssStyle 
     */
    public function isIE($version = null, $andLower = NULL)
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
            
            $lte = ($this->isIE['lower'] ? $this->isIE['lower'] . ' ' : '');
            $version = ( $this->isIE['version'] ? ' '.$this->isIE['version'] : '');
                
            $iestart = "<!--[if {$lte}IE{$version}]>";
            $ieend = '<![endif]-->';
        }
        else {$iestart = ''; $ieend = '';}
        
        $media = $this->media ? "media=\"{$this->media}\" " : "";
        $charset = $this->charset ? "charset=\"{$this->charset}\" " : "";
        $href = "href=\"{$this->path}\" ";
        
        $patterns = array('/%iestart%/', '/%href%/', '/%media%/', '/%charset%/', '/%ieend%/');
        $replacements = array($iestart, $href, $media, $charset, $ieend);
        
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

class CssLoader {
    
    private $cssContainer = array();
    
    private $uri;
    
    private $id_cat;
    
    public function __construct() {
        
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->id_cat = (isset($_GET['id_cat']) ? (int)$_GET['id_cat'] : null);
        
    }
    
    
    public function add($path = null, $media = null, $id_cat = null, $request = null, $charset = null)
    {
        
        if(!$path or !is_string($path)) die('CssLoader::add(): Parameter $path musi byt zadany a musi byt retazec');
        
        return $this->cssContainer[] = new CssStyle($path, $media, $id_cat, $request, $charset);
        
    }
    
    
    public function getStyles()
    {
        
        $output = '';
        
        foreach($this->cssContainer as $style) {
            
            
            
            // ak su nastavene id_cat alebo request,
            // tak natiahni styl len pre toto id_cat alebo request
            if($style->id_cat or $style->request) {
                
                               
                if($this->id_cat > 0 && in_array($this->id_cat, $style->id_cat) ) {
                    $output .= $style;
                }
                elseif( $style->request ) {
                    foreach($style->request as $request) {
                        
                        // pridaj backslashe  a vytvor regex
                        $uriPattern = '/'.addcslashes($request, "/.?").'/';
                        
                        if(preg_match($uriPattern, $this->uri)){
                            $output .= $style;
                        }
                        
                    }
                }
                
            }
            else {
                
                $output .= $style;
                
            }
            
        }
        
        return $output;
        
    }
    
    
    public function __toString() {
        return $this->getSTyles();
    }
}

$CSS = new CssLoader;