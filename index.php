<?php
/**
 * Project LitePage
 * ----------------
 *
 */
session_start();
/*********** SETUP **********/
// absolute filesystem path to the web root
define('WWW_DIR', dirname(__FILE__));
// absolute filesystem path to the application root
define('APP_DIR', WWW_DIR . '/app');
// absolute filesystem path to the classes
define('CLS_DIR', APP_DIR . '/classes');
// absolute filesystem path to the libraries
define('LIBS_DIR', APP_DIR . '/libs');
// absolute filesystem path to the templates
define('TPL_DIR', APP_DIR . '/templates');
// absolute filesystem path to the blocks
define('BLK_DIR', APP_DIR . '/blocks');
// absolute filesystem path to the pages
define('PGS_DIR', APP_DIR . '/pages');
// absolute filesystem path to the includes
define('INC_DIR', APP_DIR . '/includes');
// absolute filesystem path to the modules
define('MOD_DIR', APP_DIR . '/modules');

//********************************

require (APP_DIR.'/loader.php');
?>