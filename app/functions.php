<?php /* Created on: 3/19/2010 */ 
// funkcie.php

function redirect($page='', $messStatus=false)
{
	if($messStatus) $_SESSION['message'] = $messStatus;
	    else $_SESSION['message'] = null;
	if($_SESSION['lang']) $lang = $_SESSION['lang'].'/';
		else $lang = '';
	if(preg_match('/\?/', $page)) $page .= "&rd=" . dechex ( date("His") );
		else $page .= "?rd=" . dechex ( date("His") );
	if($page){
		header('Location: '.getBasePath().'/'.$lang.$page);
	}
	else{
		header('Location: '.getBasePath().'/'.$lang);
	}
        exit;
}

// SQL Injection test
function sqlTest($request)
{

	
	foreach($request as $value){
		$value = strtolower($value);
		
		if($value != addslashes($value)) return true;
		
		if(preg_match('/union /', $value)){
			return true;
		}
		elseif(preg_match('/select /', $value)){
			return true;
		}
		elseif(preg_match('/ or /', $value)){
			return true;
		}
		elseif(preg_match('/\/\*/', $value)){
			return true;
		}
		elseif(preg_match('/#/', $value)){
			return true;
		}
		elseif(preg_match('/--/', $value)){
			return true;
		}
	}
	return false;
}

// Remove special chars
function htmlMyEnts($string)
{
  $replacements = array('&'=>'&amp;',
                         '<'=>'&lt;',
                         '>'=>'&gt;',
                         '"'=>'&quot;',
                         "'"=>'&#039;');
  foreach($replacements as $replace => $replacement){
    $string = str_replace($replace, $replacement, $string);
  }
  
  return $string;
}

// URL Parameters detect
function urlDetect($urlQuery)
{
	$params = preg_split('/\//', $urlQuery, -1, PREG_SPLIT_NO_EMPTY);
	
	return $params;
}

// BasePath
function getBasePath()
{
	$server = $_SERVER['SERVER_NAME'];
	$file = $_SERVER['SCRIPT_NAME'];
	$path = 'http://'.$server.$file;
	$path = preg_replace('/\/index.php/', '', $path);
	return $path;
}

function baseUrl()
{
	$server = $_SERVER['SERVER_NAME'];
	$file = $_SERVER['SCRIPT_NAME'];
	$path = 'http://'.$server.$file;
	$path = preg_replace('/\/index.php/', '', $path);
	return $path;
}

function basePath()
{
	$server = $_SERVER['SERVER_NAME'];
	$file = $_SERVER['SCRIPT_NAME'];
	$path = $file;
	$path = preg_replace('/index.php/', '', $path);
	return $path;
}

/**
 * Returns parsed module.ini
 * @param string $sModule
 * @return array 
 */
function getModuleIni($sModule) {
    if( file_exists( MOD_DIR . "/{$sModule}/module.ini") ) {
        return parse_ini_file(MOD_DIR . "/{$sModule}/module.ini");
    }
    else {
        return array();
    }
}

//------------- FUNKCIA SLOVENSKY TVAR DATUMU ----------
function slovdate($engdate) {
	// rozdel anglicky datum
	$year=substr($engdate, 0, 4);
	$month=substr($engdate, 5, 2);
	$day=substr($engdate, 8, 2);
	$time=substr($engdate, 10,6);
	// spoj datum do slov. tvaru
	$date=$day.".".$month.".".$year.$time;
	return $date;
}
//------------ KONIEC SLOVDATE--------------------------

//------------- FUNKCIA SLOVENSKY TVAR DATUMU - AK TREBA SKRATENY ----------
function slovshortdate($engdate) {
	// rozdel anglicky datum
	$year=substr($engdate, 0, 4);
	$month=substr($engdate, 5, 2);
	$day=substr($engdate, 8, 2);
	// spoj datum do slov. tvaru
	$date=$day.".".$month.".".$year;
	if($day=="00") $date=$month."/".$year; //ak nieje den vrat len MM/RRRR
	if($month=="00") $date=$year; //ak nieje den vrat len RRRR
	return $date;
}
//------------ KONIEC SLOVSHORTDATE--------------------------

//------------- FUNKCIA MYSQL TVAR DATUMU ----------
function mysqldate($slovdate) {
	// rozdel anglicky datum
	$year=substr($slovdate, 6, 4);
	$month=substr($slovdate, 3, 2);
	$day=substr($slovdate, 0, 2);
	$time=substr($slovdate, 10,6);
	// spoj datum do slov. tvaru
	$date=$year."-".$month."-".$day.$time;
	return $date;
}
//------------ KONIEC SLOVDATE--------------------------

/**
 * @title Machine string
 * @param string
 * @param string or array
 * replaces not allowed characters, $except - this character will not be replaced
 */
function machineStr($string, $except=null){
    $table = array(
        'Š'=>'S', 'š'=>'s', 'Đ'=>'D', 'đ'=>'d', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
        'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
        'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
        'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ů'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
        'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
        'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
        'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ů'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
        'ÿ'=>'y', 'Ř'=>'R', 'ř'=>'r', 'Ŕ'=>'R', 'ŕ'=>'r', 'Ť'=>'T', 'ť'=>'t', 'Ĺ'=>'L', 'Ľ'=>'L', 'ĺ'=>'l', 'ľ'=>'l',
	
		'-' => '_',
		'+' => '_',
		'/' => '_',
		'\\' => '_',
		'|' => '_',
		'(' => '_',
		')' => '_',
		'[' => '_',
		']' => '_',
		'{' => '_',
		'}' => '_',
		'.' => '_',
		':' => '_',
		',' => '_',
		';' => '_',
		' ' => '_',
		'%' => '_',
    );
	
	if($except){
	  if(is_array($except)){
	    foreach($except as $val){
		  unset($table[$val]);
		}
	  }
	  else{
	    unset($table[$except]);
	  }
	}
   
    return strtr($string, $table);
}

/**
 * @title Email string
 * @param string
 * replaces not allowed characters
 */
function emailStr($string){
    $table = array(
        'Š'=>'S', 'š'=>'s', 'Đ'=>'D', 'đ'=>'d', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
        'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
        'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
        'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ů'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
        'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
        'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
        'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ů'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
        'ÿ'=>'y', 'Ř'=>'R', 'ř'=>'r', 'Ŕ'=>'R', 'ŕ'=>'r', 'Ť'=>'T', 'ť'=>'t', 'Ĺ'=>'L', 'Ľ'=>'L', 'ĺ'=>'l', 'ľ'=>'l',
    );
   
    return strtr($string, $table);
}

/**
 * @title url string
 * @param string
 * @param string or array
 * replaces not allowed characters, $except - this character will not be replaced
 */
function urlStr($string){
    $table = array(
        'Š'=>'S', 'š'=>'s', 'Đ'=>'D', 'đ'=>'d', 'Ď'=>'D', 'ď'=>'d', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
        'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ě'=>'E',
        'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ň'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
        'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ů'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
        'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ě'=>'e',
        'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ň'=>'n', 'ò'=>'o', 'ó'=>'o',
        'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ů'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
        'ÿ'=>'y', 'Ř'=>'R', 'ř'=>'r', 'Ŕ'=>'R', 'ŕ'=>'r', 'Ť'=>'T', 'ť'=>'t', 'Ĺ'=>'L', 'Ľ'=>'L', 'ĺ'=>'l', 'ľ'=>'l',

		'_' => '-',
		'+' => '-',
		'/' => '-',
		'\\' => '-',
		'|' => '-',
		'(' => '',
		')' => '',
		'[' => '-',
		']' => '-',
		'{' => '',
		'}' => '',
		'.' => '-',
		':' => '-',
		',' => '',
		';' => '-',
                "„" => '',
                "“" => '',
		' ' => '-',
		'%' => '-',
                "'" => '',
                '"' => '',
                "?" => '',
                "!" => '',
                "&" => '-',
                "–" => '-'
    );

    return strtolower(strtr($string, $table));
}

/**
 * @title cssclass string
 * @param string
 * @param string or array
 * replaces not allowed characters, $except - this character will not be replaced
 */
function cssClassStr($string){
    $table = array(
        'Š'=>'S', 'š'=>'s', 'Đ'=>'D', 'đ'=>'d', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C', 'č'=>'c', 'Ć'=>'C', 'ć'=>'c',
        'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
        'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
        'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ů'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
        'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
        'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
        'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ů'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
        'ÿ'=>'y', 'Ř'=>'R', 'ř'=>'r', 'Ŕ'=>'R', 'ŕ'=>'r', 'Ť'=>'T', 'ť'=>'t', 'Ĺ'=>'L', 'Ľ'=>'L', 'ĺ'=>'l', 'ľ'=>'l',

		'_' => '',
                '-' => '',
		'+' => '',
		'/' => '',
		'\\' => '',
		'|' => '',
		'(' => '',
		')' => '',
		'[' => '',
		']' => '',
		'{' => '',
		'}' => '',
		'.' => '',
		':' => '',
		',' => '',
		';' => '',
		' ' => '',
		'%' => '',
    );

    return strtolower(strtr($string, $table));
}

/**
 * @title Clean File Name
 * removes spaces from file name
 */
function cleanFileName($uploading_file)
{
	$file = preg_replace("/ /", "_", $uploading_file);
	$file = preg_replace("/%20/", "_", $uploading_file);
	
	return $file;
}

function sqliteToMysql($sql, $trigger = false){
        if($trigger) {$sql = "DELIMITER $$\n".$sql."$$\nDELIMITER ;";}
	return preg_replace(array('/INTEGER/i', '/AUTOINCREMENT/i', '/COLUMN/i'), array('INT', 'AUTO_INCREMENT', ''), $sql);
}

function errorWrite($error=null)
{
	$filename = 'errors.txt';
	
	$content  = date("Y-m-d H:i:s",time())." $error\r\n";
	$handle = fopen($filename, 'a+');
	fwrite($handle, $content);
	fclose($handle);
        
        if(DEVELOPMENT) {
            throw new Exception("There was an error in application run: $content");
        }
        else {
            redirect('', APPLICATION_RUN_ERROR);
        }

}


/**
 * CSS Compressor
 * @param string $buffer
 * @return string 
 */
function cssCompress($buffer) {
/* remove comments */
    $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
/* remove tabs, spaces, new lines, etc. */        
    $buffer = str_replace(array("\r\n", "\r", "\n", "\t"), '', $buffer);
/* remove unnecessary spaces */        
    $buffer = str_replace('{ ', '{', $buffer);
    $buffer = str_replace(' }', '}', $buffer);
    $buffer = str_replace('; ', ';', $buffer);
    $buffer = str_replace(', ', ',', $buffer);
    $buffer = str_replace(' {', '{', $buffer);
    $buffer = str_replace('} ', '}', $buffer);
    $buffer = str_replace(': ', ':', $buffer);
    $buffer = str_replace(' ,', ',', $buffer);
    $buffer = str_replace(' ;', ';', $buffer);
    
return $buffer;
}

/**
 * Converts Fetched array to paged array where array index is page number
 * @param array $records
 * @param integer $pageLimit
 * @return array 
 */
function getPages($records, $pageLimit = 10)
{
	$pages = array();
	
	if($records){
		$pageidx = 1;
		$idx = 1;
		$lastidx = $pageLimit;
		foreach($records as $record){
			$rows[] = $record;
			if($idx == $lastidx){
				$idx = 0;
				$pages[$pageidx] = $rows;
				unset($rows);
				$pageidx++;
			}
			$idx++;
		}
		// Place the rest of messages to last page
		if(isset($rows) && count($rows)>0){
		$pages[$pageidx] = $rows;
		}
	}
	return $pages;
}

/**
 * Replaces macros in content with true data
 * @param string $Content
 * @return string 
 */
function contentApi($sContent) {
	
	$apiRegex = '/\{(\w+):([a-z0-9:|;_-]+)\}/i';
	//$apiRegex = '/\{(block):([a-z0-9:;_-]+)\}/i';
	$allowedApiActions = array('block', 'content', 'year', 'datetime', 'user', 'pairs', 'tourleg');
	
	$matches = array();
	if(!preg_match_all($apiRegex, $sContent, $matches)) return $sContent;

	// gets
	$continue = FALSE;
	foreach($matches[1] as $key => $val) {
		$gets[$key] = $val;
		if(in_array($val, $allowedApiActions)) {
			$continue = TRUE;
		}
	}
	
	if(!$continue) return $sContent;
	
	// replace patterns
	foreach($matches[0] as $key => $val) {
		$patterns[$key] = '/'.addcslashes($val, '{}<>/').'/i';
	}
	
	// params
	foreach($matches[2] as $key => $val) {
		$params[$key] = $val;
	}
	
	// replace the MAcros
	foreach ($gets as $key => $get) {
		if($get === 'user') {
			$replacements[] = Application::$logged[$params[$key]];
		}
		else {
			$replacements[] = file_get_contents(baseUrl()."/api/{$get}/{$params[$key]}/");
		}
	}
	
	return preg_replace($patterns, $replacements, $sContent);
	
}

// emulate lcfirst php 5.2 compatibility
if(function_exists('lcfirst') === false) {
    function lcfirst($str) {
        $str[0] = strtolower($str[0]);
        return $str;
    }
}