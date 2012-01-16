<?php
/**
 * Trieda PathAlias vyhlada URL Alias a v pripade, ze najde zhodu tak vrati retazec pre spracovanie
 * Zaroven ak najde zhodu s retazcom tak presmeruje aplikaciu na URL alias koli prevencii na kanonicke URL adresy
 *
 * @author brazvo
 * @package core
 * 
 * @todo napojenie na databazu v metode init(), dorobit staticke metody pre ulozenie PathAliasu
 */
class PathAlias implements ICore {
    
    /** @property static array */
    private static $aliases = array();
    
    public static function init() {
               
        $rows = db::fetchAll("SELECT `path_alias`, `url` FROM `path_aliases`");
        
        foreach ($rows as $row) {
            self::$aliases[$row['path_alias']] = $row['url'];
        }
     
    }
    
    /**
     * Vracia retazec citatelny pre Router alebo presmeruje stranku na URL Alias
     * @param string $q
     * @return string 
     */
    public static function getRoute( $q )
    {
        
        // odstranime posledne lomitko (aj keby ich bolo viac)
        $q = preg_replace('/[\/]+$/', '', $q);

        // Ak najde URL alias vratime retazec, ktoremu rozumie Router
        if( array_key_exists( $q, self::$aliases ) ) {
            
            return self::$aliases[$q];
            
        }
        
        // Ak najde zhodu s retazcom tak presmerujeme na URL alias
        if( in_array($q, self::$aliases) ) {
            
            // pozor vracia pole, my zobereme hned prvu hodnotu, pretoze by sa nemalo stat, ze mame viac
            // aliasov pre ten isty request
            $aAlias = array_keys( self::$aliases, $q ); 
            redirect( $aAlias[0] );
            
        }
        
        return $q;
        
    }
    
    
    /**
     * Vracia pole s URL aliasmi
     * @return array
     */
    public static function getAlias( $string )
    {
        
        // Ak najde zhodu s retazcom tak presmerujeme na URL alias
        if( in_array($string, self::$aliases) ) {
            
            // pozor vracia pole, my zobereme hned prvu hodnotu, pretoze by sa nemalo stat, ze mame viac
            // aliasov pre ten isty request
            $aAlias = array_keys( self::$aliases, $string );
            return $aAlias[0];
            
        }
        else {
            return $string; // inak vrat sam seba
        }
        
    }
    
    
    /**
     * Vracia pole s URL aliasmi
     * @return array
     */
    public static function getAliases()
    {
        
        return self::$aliases;
        
    }
    
}