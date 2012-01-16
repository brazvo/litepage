<?php /* Created on: 3/19/2010 */
require(APP_DIR.'/functions.php');
// hlavna absolutna cesta
define('BASEPATH', getBasePath());
//xdebug_disable();

//require(APP_DIR.'/includes/config.inc');
error_reporting(E_ALL);
ini_set( 'html_errors', 1 );
ini_set( 'error_reporting', 2147483647 );
ini_set( 'display_errors', 1 );   

require(APP_DIR.'/core/CoreLoader.php');

//load interfaces
CoreLoader::load('ICore');
CoreLoader::load('ICoreController');
CoreLoader::load('IContentModule');
CoreLoader::load('IModuleController');

//load core classes
CoreLoader::load('Object');
CoreLoader::load('Vars');
CoreLoader::load('Environment');
CoreLoader::load('Config');
CoreLoader::load('Db');
CoreLoader::load('PathAlias');
CoreLoader::load('Router');
CoreLoader::load('ModuleChecker');

CoreLoader::load('Autoloader');
CoreLoader::load('BaseModel');
CoreLoader::load('Application');
CoreLoader::load('Template');
CoreLoader::load('Control');
CoreLoader::load('Controller');

//load main libraries with class prefix
/*
include(WWW_DIR.'/app/libs/class.Html.php');
include(WWW_DIR.'/app/libs/class.Cache.php');
include(WWW_DIR.'/app/libs/class.Blocks.php');
include(WWW_DIR.'/app/libs/class.ContentVars.php');
include(WWW_DIR.'/app/libs/class.Texy.php');
include(WWW_DIR.'/app/libs/class.Form.php');
include(WWW_DIR.'/app/libs/class.AppForm.php');
include(WWW_DIR.'/app/libs/class.Table.php');
include(WWW_DIR.'/app/libs/JavaScriptPacker.php');
 * 
 */

$app = new Application();

echo Application::$pageHtml;

Application::$db = null;