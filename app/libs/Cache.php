<?php
/**
 * Project: LitePage V2
 * Class which will be called for caching content
 *
 * @author brazvo
 */
class Cache implements ArrayAccess {

    
    private $cacheDir;
    
    private $extension;
    
    private $contents = array();
    
    private $files = array();
    
    public function __construct($dir = 'cache', $extension = 'cach') {
        
        $this->cacheDir = '/' . $dir . '/';
        $this->extension = $extension;
        
    }
    
    
    /**
     * Saves Html pages into cache
     * @param string $key
     * @param string $content Html content
     */
    public function save($key, $content) {
        
        $request = $key;

        $cachedFile = uniqid(date("U")) . '.' . $this->extension;
        
        // temporary
        $this->files[$key] = $this->cacheDir . $cachedFile;
        $this->contents[$key] = $content;

        $fileHandler = fopen(WWW_DIR . $this->cacheDir . $cachedFile, 'w');
        
        if(@fwrite($fileHandler, $content)) {
              db::exec("DELETE FROM `cache` WHERE `request`=%v", $request);
              db::exec("INSERT INTO `cache` (`filename`, `request`) VALUES (%v, %v)", $cachedFile, $request);
        }
        else {
            if(DEVELOPMENT) {echo "Chyba pri zapise html do cache."; exit;}
        }
        fclose($fileHandler);
    }
    
    
    public function get($key, $filename = false) {
        
        // if is in temporary
        if($filename && isset($this->files[$key]))
                return $this->files[$key];
        if(!$filename && isset($this->contents[$key]))
                return $this->contents[$key];
        
        $row = db::fetch("SELECT `filename` FROM `cache` WHERE `request` = %v", $key);
        
        if($row && file_exists(WWW_DIR . $this->cacheDir . $row['filename'])) {
            
            if(!$filename) return file_get_contents(WWW_DIR . $this->cacheDir . $row['filename']);
                else return $this->cacheDir . $row['filename'];
                    
        }
        else {
            
            return null;
            
        }
        
    }
    
    
    public function clean($key) {
        
        return db::exec("DELETE FROM `cache` WHERE `request`=%v", $key);
        
    }
    
    
    public function cleanAll() {
        
        return db::exec("DELETE FROM `cache`");
        
    }
    
    
    /************************** ArrayAccess Implementation **/
    
    public function offsetSet($offset, $value) {
        
        $this->save($offset, $value);
        
    }
    
    public function offsetGet($offset) {
        
        return $this->get($offset);
        
    }
    
    public function offsetExists($key) {
	
        return $this->offsetGet($key) !== NULL;
        
    }
    
    public function offsetUnset($key)
    {
	
        $this->clean($key);
        
    }
    
}
?>
