<?
/**
 * Project Lite Page
 *
 * PHP class Content Vars
 *
 * ========================================================================================
 * @copyright Copyright (c) 2010 Branislav Zvolensky (http://www.mbmartworks.sk)
 * @author Branislav Zvolensky <zvolensky@mbmartworks.sk>
 * @license http://opensource.org/licenses/gpl-license.php GNU General Public License (GPL)
 * ========================================================================================
 *
 */
class ContentVars 
{
	private $data = array();
	
	function __set($name, $value)
	{
		$this->$name = $value;
	}
	
	function __get($name)
	{
		if (array_key_exists($name, $this->data)) {
            return $this->$name;
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
	}
}