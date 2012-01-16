<?php /* Created on: 3/19/2010 */ 

class Application
{

	// Properties
	
	/**
	 * @array
	 * global variables
	 */
	private static $variables;
	
	/**
	 * @array
	 * active modules
	 */
	public static $activeModules;
	
	/**
	 * @array
	 * application modules
	 */
	public static $appModules;
	
	/**
	 * @array
	 * standalone modules
	 */
	public static $saModules;
	
	/**
	 * @array
	 * content extend modules
	 */
	public static $ceModules;
	
	/**
	 * @array
	 * content modules objects
	 */
	public static $contentModulesObjects;
	
	/**
	 * @array
	 * module extend modules
	 */
	public static $meModules;
	
	/**
	 * @var
	 * bool - module request
	 */
	public static $moduleRequest;
	
	/**
	 * @var
	 * html - module css
	 */
	public static $moduleCSS;
	
	/**
	 * @var
	 * html - module js
	 */
	public static $moduleJS;
	
	/**
	 * @var string
	 * complete menu output
	 */
	public $menu;
	
	/**
	 * @var string
	 * loads page
	 */
	public $page;
	
	/**
	 * @var bool
	 * error status
	 */
	public static $isError;
	
	/**
	 * @array
	 * error messages container
	 */
	public static $errors;
	
	/**
	 * @var bool
	 * message status
	 */
	public static $isMessage;
	
	/**
	 * @array
	 * messages container
	 */
	public static $messages;
	
	/**
	 * @var bool
	 * query status
	 */
	public static $isQuery;
	
	/**
	 * @array
	 * queries container
	 */
	public static $queries;
        
        /**
         * is set tu true if is front page
         * @var bool
         */
        public static $isFrontPage = false;

        public static $logged;
	
	public static $title;
	
	public static $content;
	
	public static $headerAppModulesContent;
	
	public static $bodyAppModulesContent;
	
	public static $template;
	
	public static $language;
	
	public static $defLanguage;
	
	public static $lang_file;
	
	public static $sys_lang_file;
	
	public static $subDir;
	
	public static $pageName;
	
	public static $menuActive;
	
	public static $action;
	
	public static $id;
	
	public static $tpl_vars;
	
	public static $db;
	
	public static $pathRequest;
	
	public static $view;
	
	public static $add;
	
	public static $edit;
	
	public static $delete;
	
	public static $stylesheets = array();
        
        /**
         * paths to css files that will be cached
         * @var array
         */
        public static $cssCache = array();
	
	public static $javascripts = array();
        
        /**
         * paths to js files that will be cached
         * @var array
         */
        public static $jsCache = array();
	
	public static $metas = array();
	
	public static $googleAnalytics;
        
        public static $pageHtml;
	
	// Constructor
	public function __construct()
	{
		                
                $this->setCacheAndDev();    // set caching and dev tools
		
		self::$isError = FALSE;
		self::$errors = array();
		self::$isMessage = FALSE;
		self::$messages = array();
		self::$logged = false;
		self::$subDir = '';
		self::$language = $this->getDefaultLang();
		self::$defLanguage = $this->getDefaultLang();

		if(isset($_REQUEST['q'])){
		  //CHECK IF LANGUAGE IS IN REQUEST
		  $lang = current(urlDetect($_REQUEST['q']));
		  //REMOVE LANG ID FROM REQUEST
		  $_REQUEST['q'] = $this->removeLangId($_REQUEST['q'], $lang);
		  
		  self::$pathRequest = $_REQUEST['q'];
		  self::$menuActive = $_REQUEST['q'];
		  
		  $params = $this->checkPathAlias($_REQUEST['q']);
		  // Check if language request was added to url
		  $result = $this->checkLanguageReq($lang);
		  if($result){
			self::$language = $result;
			if(empty($params)){
			  $_SESSION['pathRequest'] = '';
		          self::$menuActive = self::$language['main_page'];
			  $params = $this->checkPathAlias(self::$language['main_page']);
                          self::$isFrontPage = true;
			}
		  }
		  elseif(!$params && !$result){
		    //self::$language = $this->getDefaultLang();
			//$params = $this->checkPathAlias(self::$language['main_page']);
			redirect();
		  }

                  if(self::$language['main_page'] == $_REQUEST['q']) self::$isFrontPage = true;
		}
		else{
		  $_SESSION['pathRequest'] = '';
		  self::$menuActive = self::$language['main_page'];
		  $params = $this->checkPathAlias(self::$language['main_page']);
                  self::$isFrontPage = true;
		}
		
		if(isset($_REQUEST['destination'])){
			$_SESSION['destination'] = $_REQUEST['destination'];
		}
		
		if(isset($_SESSION['pathRequest']) && $_SESSION['pathRequest']){
		  self::$pathRequest = $_SESSION['pathRequest'];
		}
		
		// Message after redirect;
		if(isset($_SESSION['message'])){
			self::setMessage($_SESSION['message']);
			unset($_SESSION['message']);
		}
		
		// Errors after redirect;
		if(isset($_SESSION['error'])){
			self::setError($_SESSION['error']);
			unset($_SESSION['error']);
		}
		
		// Errors from form after redirect;
		if(isset($_SESSION['__BZ_FORM']['errors'])){
			self::setError($_SESSION['__BZ_FORM']['errors']);
			unset($_SESSION['__BZ_FORM']['errors']);
		}
		
		//set language into session
		if(self::$defLanguage['code'] == self::$language['code']){
			$_SESSION['lang'] = '';
		}
		else{
			$_SESSION['lang'] = self::$language['code'];
		}
                
                $this->loadSystemData();    // load system settings
                
                $this->loadTemplateConfig();
		
		$this->setAppData($params);
		
		self::$moduleCSS = '';
		self::$moduleJS = '';
		
		
		if(self::$appModules){
		  foreach(self::$appModules as $module => $val){
			$this->setModuleCSSandJS($module);
		  }
		}
		
		if(isset(self::$saModules[$params[0]])){
		  $this->setModuleCSSandJS();
		}
		
		$this->getAppModules(); // load application modules
		
		$this->loadContentModules(); // load content extenders
		
		// Set language file
		self::$lang_file = self::$subDir.'/languages/'.self::$language['mach_name'].'.inc';
		
		// Set system language file
		self::$sys_lang_file = APP_DIR.'/languages/system/'.self::$language['mach_name'].'.inc';
                
                $this->checkLogged(); // check if user is logged;
                
                // look into chache if no one is logged
                if(!Application::$logged['status'] && CACHE_PAGES) {
                    $request = sha1(self::$pageName . '/' . self::$action . '#' . self::$id);
                    
                    $row = db::fetch("SELECT `filename` FROM `cache` WHERE `request` = %v", $request);
                    
                    if($row && file_exists(WWW_DIR . '/cache/' . $row['filename'])) {
                        include WWW_DIR . '/cache/' . $row['filename'];
                        exit;
                    }
                }
                
                $this->getPage();
	}

	// Methods
	
	/**
         * Checks if request is to admin section or some of active module or to basic frontend section
         * and it sets the global properties
         * @param array $params
         * @return void
         */
        private function setAppData($params)
	{

	  if($params[0] == 'admin'){
            self::$subDir = '/'.array_shift($params);

            require(APP_DIR.'/admin/controllers/BaseAdmin.php');

            if(isset($params[0])){
                self::$pageName = $params[0];
            }
            else{
                self::$pageName = 'admin';
            }

            self::$moduleRequest = false;
	  }
	  elseif(isset(self::$activeModules[$params[0]])){
	    
		self::$subDir = '/modules/'.$params[0];
		
		self::$pageName = $params[0];
		
		self::$moduleRequest = true;
	  }
	  else{
	    
	    self::$pageName = $params[0];
		
		self::$moduleRequest = false;
		
	  }
	  
	  
	  if(isset($params[1])){
	    self::$action = $params[1];
	  }
	  else{
	    self::$action = 'default';
	  }
	  
	  if(isset($params[2])){
	    self::$id = $params[2];
	  }
	  else{
	    self::$id = NULL;
	  }
	
	}
	
        
        /**
         * looks for module javascripts and css style sheets documents
         * @param string $module 
         * @return void
         */
	private function setModuleCSSandJS($module = null)
	{
		
		if($module){
			$modpath = "/modules/$module";
		}
		else{
			$modpath = self::$subDir;
		}
		
		//if(self::$moduleRequest){
			$jsDir = @opendir(WWW_DIR.'/app'.$modpath.'/js/');
			$cssDir = @opendir(WWW_DIR.'/app'.$modpath.'/css/');
		    
		    if($jsDir){
				while($jsFiles = readdir($jsDir)) {
					$jss[] = $jsFiles;
				}
				closedir($jsDir);
				
				$dir = '/app'.$modpath.'/js/';

				foreach($jss as $js){
					if(substr($js, 0, 1) != '.'){
                                            self::setJavascript($dir . $js);
					}
				}
			}
			
			if($cssDir){
				while($cssFiles = readdir($cssDir)) {
					$csss[] = $cssFiles;
				}
				closedir($cssDir);
				
				$dir = '/app'.$modpath.'/css/';

				foreach($csss as $css){
					if(substr($css, 0, 1) != '.'){
						self::setStylesheet($dir . $css);
					}
				}
			}
			
		//}
	}
	
	/**
	 * @title Load System Data
	 * Loads sytem data a puts them into constants
	 */
	private function loadSystemData()
	{

          $rows = db::fetchAll("SELECT * FROM page_settings");
	  
	  foreach($rows as $row){
	    define($row['constant'], $row['value']);
	  }
          
	  // set meta tags
          Application::setMeta(array('http-equiv'=>'Content-Type', 'content'=>'text/html; charset=utf-8'));
	  if(META_KEYWORDS) Application::setMeta(array('name' => 'keywords', 'content' => META_KEYWORDS));
	  if(META_DESCRIPTION) Application::setMeta(array('name' => 'description', 'content' => META_DESCRIPTION));
	  if(META_ROBOTS) Application::setMeta(array('name' => 'robots', 'content' => META_ROBOTS));
	  if(META_GOOGLE_SITE_VERIFICATION) Application::setMeta(array('name' => 'google-site-verification', 'content' => META_GOOGLE_SITE_VERIFICATION));
	  if(META_AUTHOR) Application::setMeta(array('name' => 'author', 'content' => META_AUTHOR));
	  if(META_COPYRIGHT) Application::setMeta(array('name' => 'copyright', 'content' => META_COPYRIGHT));
          if(META_RATING) Application::setMeta(array('name' => 'rating', 'content' => META_RATING));
          if(self::$language['code']) Application::setMeta(array('name' => 'content-language', 'content' => self::$language['code']));
	  
	  // google analytics code
	  if(META_GOOGLE_ANALYTICS) {
		self::$googleAnalytics = "<script type=\"text/javascript\">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', '". META_GOOGLE_ANALYTICS ."']);
_gaq.push(['_trackPageview']);
					      
(function() {
	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
</script>\n";
	  }
	  else {
		self::$googleAnalytics = '';
	  }
	
	}
        
        
        /**
         * defines constants for caching and development
         */
        private function setCacheAndDev()
        {
            $rows = db::fetchAll("SELECT * FROM development");
            
            foreach($rows as $row){
                define($row['constant'], $row['value']);
            }
            
            if(DEVELOPMENT){
                error_reporting(E_ALL);
                ini_set('display_errors', TRUE);
                ini_set('display_startup_errors', TRUE);
            }
            else{
                error_reporting(E_ALL);
                ini_set('display_errors', FALSE);
                ini_set('display_startup_errors', FALSE);
            }
            
        }
        
        /**
         * loads template config file;
         */
        private function loadTemplateConfig()
        {
            // load template config file
            if(file_exists(APP_DIR . '/templates/config_template.php')) {
                include APP_DIR . '/templates/config_template.php';
            }
        }
	
	/**
	 * @title Set Error
	 * global set of an error message
	 */
	public static function setError($error)
	{
		self::$isError = TRUE;
		self::$errors[] = $error;
	}
	
	/**
	 * @title Get Errors
	 * returns errors in container
	 */
	public static function getErrors()
	{
		$output = '';
		if(self::$isError){
		  $messages = '';
		  foreach(self::$errors as $message){
		    $messages .= "<p>$message</p>";
		  }

		  $output .= "<div class=\"flash-errors\">".$messages."</div>";
		}
		
		return $output;
	}
	
	/**
	 * @title Set Message
	 * global set of a message
	 */
	public static function setMessage($message)
	{
		self::$isMessage = TRUE;
		self::$messages[] = $message;
	}
	
	/**
	 * @title Get Messages
	 * returns messages in container
	 */
	public static function getMessages()
	{
		if(self::$isMessage){
		  $messages = '';
		  foreach(self::$messages as $message){
		    $messages .= "<p>$message</p>";
		  }
		  return "<div class='flash-messages'>$messages</div>";
		}
		else{
		  return "";
		}
	}
	
	/**
	 * @title Set Query
	 * global set of a message
	 */
	public static function setQuery($query)
	{
		self::$isQuery = TRUE;
		self::$queries[] = $query;
	}
	
	/**
	 * @title Get queries
	 * returns messages in container
	 */
	public static function getQueries()
	{
		if(self::$isQuery){
		  $queries = '';
		  foreach(self::$queries as $query){
		    $queries .= "<div>$query</div>";
		  }
		  return "<div class='queries'>$queries</div>";
		}
		else{
		  return "";
		}
	}
	
	/**
	 * @title Set Favicon
	 * set stylesheets
	 */
	static function setFavicon($path)
	{
		if(!$path) die('Application::setFavicon - $path must be set.');
		
		$link = Html::elem('link')->href(BASEPATH . $path);
		$link->rel = 'shortcut icon';
		$link->type = 'image/x-icon';
		
		self::$stylesheets[] = $link;
	}
	
	/**
	 * @title Set Stylesheet
	 * set stylesheets
	 */
	static function setStylesheet($path = null, $ie = false, $media = false)
	{
		if(!$path) die('Application::setStylesheet - $path must be set.');
                
                $cache = (int)CACHE_CSS;
                
                if($cache && !$ie) {
                    self::$cssCache[] = $path;
                }
                else {
                    
                    $link = Html::elem('link')->href(BASEPATH . $path);
                    $link->rel = 'stylesheet';
                    $link->type = 'text/css';
                    if($media) $link->media = $media;

                    if($ie) $link = '<!--[if IE]>' . $link . '<![endif]-->';

                    self::$stylesheets[] = $link;
                    
                }
		
	}
	
	/**
	 * @title Get CSS
	 * returns css style sheets as Html content
         * if caching of css is enabled it loads cached files
         * @return string $output Html content
	 */
	public function getCss()
	{
		$cache = (int)CACHE_CSS;
                
                if(self::$cssCache && $cache) {
                                
                    $cacheObj = new Cache('cache', 'css');
                    
                    $cssCachedFile = $cacheObj->get('css', true);
                    
                    if(!$cssCachedFile) {
                         
                        $cssCont = '';
                        $docRoot = $_SERVER['DOCUMENT_ROOT'];
                        $docRootPattern = '/'.addcslashes($docRoot, '/').'/';
                        $scriptFilename = $_SERVER['SCRIPT_FILENAME'];
                        $basePath = preg_replace(array($docRootPattern, '/index.php/'), array('', ''), $scriptFilename);
                        foreach (self::$cssCache as $cssFile) {
                            
                            $content = file_get_contents(WWW_DIR . $cssFile);
                            
                            $fileDir = preg_replace($docRootPattern, '', dirname(WWW_DIR . $cssFile)) . '/';
                            
                            preg_match_all('/url\([\'"]?(?![a-z]+:|\/+)([^\'")]+)[\'"]?\)/i', $content, $matchesarray);
                            
                            foreach($matchesarray[0] as $key => $val) {
                                
                                $url = $matchesarray[1][$key];
                                $urlPattern = '/'.addcslashes($matchesarray[0][$key], "/.'()").'/';
                                if(substr($url, 0, 4) !== 'http') {
                                    $replUrl = 'url("'.$fileDir . $url . '")';
                                    $content = preg_replace($urlPattern, $replUrl, $content);
                                }
                                
                            }
                            
                            $cssCont .= $content."\n";
                            
                        }
                                                
                        //compress css
                        $cssCont = cssCompress($cssCont);
                        
                        $cacheObj->save('css', $cssCont);
                        
                        $cssCachedFile = $cacheObj->get('css', true);
                        
                    }
                    
                     
                    $link = Html::elem('link')->href(BASEPATH . $cssCachedFile);
                    $link->rel = 'stylesheet';
                    $link->type = 'text/css';
                    
                }
                
                $output = '';
                if($cache > 0) $output .= $link . "\n";

                if(self::$stylesheets){
		  
		  foreach(self::$stylesheets as $stylesheet){
		    $output .= $stylesheet . "\n";
		  }
		  
		}
		
                return $output;
	}
	
	/**
	 * @title Set Javascript
	 * set javascripts
	 */
	static function setJavascript($path = null, $ie = false)
	{
		if(!$path) die('Application::setJavascript - $path must be set.');
                
                $cache = (int)CACHE_JS;
                
                if($cache && !$ie) {
                    self::$jsCache[] = $path;
                }
                else {
		
                    $link = Html::elem('script')->src(BASEPATH . $path);
                    $link->type = 'text/javascript';

                    if($ie) $link = '<!--[if IE]>' . $link . '<![endif]-->';

                    self::$javascripts[] = $link;
                }
	}
	
	/**
	 * @title Get JS
	 * returns messages in container
	 */
	public function getJs()
	{
            $cache = (int)CACHE_JS;
                
            if(self::$jsCache && $cache) {
                
                $cacheObj = new Cache('cache', 'js');
                
                $jsCachedFile = $cacheObj->get('js', true);


                if(!$jsCachedFile) {
                    
                    $jsCont = '';
                    foreach (self::$jsCache as $jsFile) {
                        $jsCont .= file_get_contents(WWW_DIR . $jsFile)."\r\n\r\n";
                    }

                    //compress js
                    //$jsPacker = new JavaScriptPacker($jsCont);
                    //$jsCont = $jsPacker->pack();

                    $cacheObj->save('js', $jsCont);
                        
                    $jsCachedFile = $cacheObj->get('js', true);

                }


                $link = Html::elem('script')->src(BASEPATH .$jsCachedFile);
                $link->type = 'text/javascript';

            }
            
            $output = '';
            if($cache > 0) $output .= $link . "\n";
            
            if(self::$javascripts){
		  
		  foreach(self::$javascripts as $javascript){
		    $output .= $javascript . "\n";
		  }
		  
            }
            
            return $output;
	}
	
	/**
	 * @title Set Meta
	 * set Meta
	 */
	static function setMeta($attributes)
	{
		if(!$attributes) die('Application::setMeta - $attributes must be set.');
		
		self::$metas[] = $attributes;
	}
	
	/**
	 * @title Get META
	 * returns meta elements
	 */
	public function getMeta()
	{
		if(self::$metas){
		  $output = '';
		  foreach(self::$metas as $meta){
			$metatag = Html::elem('meta');
			foreach($meta as $key => $value) {
				$metatag->$key = $value;
			}
			$output .= $metatag . "\n";
		  }
		  return $output;
		}
		else{
		  return "";
		}
	}
	
	/**
	 * @title Check Logged
	 * Checks if user is logged yet
	 */
	private function checkLogged()
	{
	
	  $log = new LoginModel;
	  
	  $result = $log->isLogged();
	  
	  if($result){
	    self::$logged['status'] = true;
		self::$logged['userid'] = $result['id'];
	    self::$logged['user'] = $result['user'];
	    self::$logged['name'] = $result['name'];
	    self::$logged['surname'] = $result['surname'];
	    self::$logged['role'] = $result['role'];
	  
	    return true;
	  }
	  else{
	    self::$logged['status'] = false;
		self::$logged['userid'] = null;
	    self::$logged['user'] = null;
	    self::$logged['name'] = null;
	    self::$logged['surname'] = null;
	    self::$logged['role'] = 'visitor';
	  
	    return false;
	  }
	  
	}
	
	/**
	 * @title Get Page
	 * return the page from request
	 */
	private function getPage()
	{
		// LOAD database
		$db = self::$db;
		
		$pagename = self::$pageName;
		
		$pageid = self::$action;
		  
		// Include language file if exists
		if(is_file(APP_DIR.self::$lang_file)){
			include(APP_DIR.self::$lang_file);
		}
		
		// Include system language file if exists
		if(is_file(self::$sys_lang_file)){
			include(self::$sys_lang_file);
		}
		
		$result = $this->getController(self::$pageName, self::$action, self::$id);
		  
		$tpl = $this->getTemplate(self::$pageName, self::$action, self::$id);
		
		if(!$result) {
		  if(DEVELOPMENT){
		    $this->setRunError('controller_missing', self::$pageName);
		    $this->getController('error', self::$action, self::$id);
                    $tpl = $this->getTemplate('error', self::$action, self::$id);
		  }
		  else{
		    redirect('error/show/404');
		  }
		  
		}
		
		// check if is set view attribute to change view
		if(isset(Application::$tpl_vars['view'])){
		  $tpl->changeView(Application::$tpl_vars['view']);
		}
                
                // check if the content will be cached
                if(isset(Application::$tpl_vars['cache']) && Application::$tpl_vars['cache'] && CACHE_PAGES) {
                    if(is_string(Application::$tpl_vars['cache'])) {
                        $pageCacheKey = sha1(Application::$tpl_vars['cache']);
                    }
                    else {
                        if(DEVELOPMENT) {
                            die('Application::getPage(): Cache Key must be a string');
                        }
                        $pageCacheKey = null;
                    }
                }
                else {
                    $pageCacheKey = null;
                }
                
                // get template vars
                $cont = new ContentVars; 

                if(Application::$tpl_vars){
                  foreach(Application::$tpl_vars as $key => $value){
                    $cont->$key = $value;
                    $$key = $value;
                  }
                }
                
                $tplPath = $tpl->getTplPath();

		if(@is_file($tplPath)){
                    $template = $tplPath;
		}
		else{
		    $template = null;
		}
		
		if($template){
		    //$pageAsString = file_get_contents($template);
                    ob_start();
                    include $template;
                    $pageAsString = ob_get_clean();
		}
		else{
		  self::setError('Missing template '.$tplPath);
		  $pageAsString = '';
		}
                
                          
                // extract page blocks
                preg_match_all('/{\w+}/i', $pageAsString, $pageBlocks);

                if(count($pageBlocks[0]) > 0) {
                    
                    foreach($pageBlocks[0] as $key => $blockStart) {
                        
                        $blockName = preg_replace('/[{}]/', '', $blockStart);
                        $blockEnd = preg_replace('/{/', '{/', $blockStart);
                        
                        $$blockName = $this->extractUnit($pageAsString, $blockStart, $blockEnd);
                        
                    }
                    
                }
                
                // prepare main page buffer
                $js = $this->getJs();
                $css = $this->getCss();
                $meta = $this->getMeta();
                $gAnalytics = self::$googleAnalytics;
                $lang = self::$language['code'];
                
		$mainTemplate = $tpl->getMainTplPath();
                
                ob_start();
                include $mainTemplate;
                self::$pageHtml = ob_get_clean();
                
                // if cache is ON and isset $pageCacheKey
                if(CACHE_PAGES && $pageCacheKey) {
                    $cacheObj = new Cache('cache', 'html');
                    
                    $cacheObj->save($pageCacheKey, self::$pageHtml);
                }
		
	}
	
	private function getController($contName, $param1='default', $param2=NULL)
	{
	  $contName = ucfirst($contName);
	  
	  self::$action = $param1;
	  self::$id = $param2;
	  
	  if(@is_file(APP_DIR.self::$subDir.'/controllers/'.$contName.'.php')){
		require(APP_DIR.self::$subDir.'/controllers/'.$contName.'.php');
		
		$controller = new $contName();
		
		return true;
	  }
	  else{
	  
	    return false;
	    
	  }
	
	}
	
	/**
	 * Inserts Application Modules into template
	 */
	private function getAppModules()
	{
		self::$headerAppModulesContent = '?> ';
		self::$bodyAppModulesContent = '?> ';
		if(count(self::$appModules) > 0){
			foreach(self::$appModules as $module => $val){
				if($val){
					$appFile = APP_DIR."/modules/$module/application.inc";
					$appFile = file_get_contents($appFile);
					self::$headerAppModulesContent .= $this->extractUnit($appFile, '{header}', '{/header}');
					self::$bodyAppModulesContent .= $this->extractUnit($appFile, '{body}', '{/body}');
					//include();
				}
			} 
		}
	}
	
	/**
	 * Load content extend modules into template
	 */
	private function loadContentModules()
	{
		if(count(self::$ceModules) > 0){
			foreach(self::$ceModules as $module => $val){
				if($val){
					$classname = ucfirst($module);
					$appFile = APP_DIR."/modules/$module/controllers/$classname.php";
					include($appFile);
					Application::$contentModulesObjects[] = new $classname;
				}
			} 
		}
	}
	
	private function removeLangId($request, $lang)
	{
	  $request = $request.'/'; //add slash to end
	  
	  $row = db::fetch("SELECT * FROM languages WHERE langid='$lang'");

	  if($row){
		$request = preg_replace("/$lang\//", '', $request);
	  }
	  return substr($request, 0,-1); //remove slash from end
	}
	
	private function checkPathAlias($request){
	
	  $row = db::fetch("SELECT url FROM path_aliases WHERE path_alias='$request'");
	  if($row){
	    $result = urlDetect($row['url']);
	  }
	  else{
	    $result = urlDetect($request);
	  }
	  return $result;
	}
	
	private function setRunError($error, $error_object)
	{
	   self::$action = 'default';
	   self::$id = $error.':'.$error_object;
		
	}
	
	private function getTemplate($template, $param1, $id)
	{
	
	  return new Templates($template, $param1, $id);
	
	}
	
	private function extractUnit($string, $start, $end)
  {
    $pos = stripos($string, $start);
    
	$str = substr($string, $pos);
 
    $str_two = substr($str, strlen($start));
 
    $second_pos = stripos($str_two, $end);
 
    $str_three = substr($str_two, 0, $second_pos);
 
    $unit = trim($str_three); // remove whitespaces
 
    return $unit;
  }
  
  /**
   * @title Global variable setter
   * Sets a value to global container $variables
   */
  public static function setVar($name=null, $value=null)
  {
    if($name === null && $value === null){
	  echo 'Function: Application::setVar - Parameters $name and $value are set to NULL.';
	  exit;
	}
	else{
	  self::$variables[$name] = $value;
	}
  }
  
  /**
   * @title Global variable getter
   * @return mix
   * Returns value of global viariable setted by function setVar
   */
  public static function getVar($name=null)
  {
    if($name === null){
	  echo 'Function: Application::getVar - Waiting parameter $name.';
	  return false;
	}
	if(!isset(self::$variables[$name])){
	  echo "Function: Application::getVar - Variable $name not found";
	  exit;
	}
	else{
	  return self::$variables[$name];
	}
  }
  
  public static function getModulesCSS()
  {
    $modules = db::fetchAll("SELECT * FROM modules WHERE installed=1");
	
	$output = '';
	if(count($modules)>0){
	  foreach($modules as $mod){
	    $module = $mod['machine_name'];
	    if(@is_file(APP_DIR."/modules/$module/css/$module.css")){
		$output .= '<link href="'.BASEPATH.'/app/modules/'.$module.'/css/'.$module.'.css" rel="stylesheet" type="text/css" />';
	    }
	  }
    }
	return $output;
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
  
  /**
   * @title Get All Languages
   * @return array
   * returns array of langs in form 'langid' => 'lang_title' 
   */
  private function getAllLanguages()
  {
    
	$rows = db::fetchAll("SELECT * FROM languages WHERE active='1'");
	if(!$rows) return false;
	if(count($rows) == 1) return 'one';
	if($rows){
		foreach($rows as $lg){
			$ret[$lg['langid']] = $lg['name'];
		}
		return $ret;
	}
  }
  
  /**
   * @title Create Lang Input
   * @return html
   * factory for creating form select element
   * if it finds that only one lang is active, it creates hidden input with empty value
   */
  public static function createLangInput($currlang='none')
  {
	$langs = self::getAllLanguages();
	
	//creating fanthom form (we only use its elements)
	$form = new Form;
	if($langs && $langs != 'one'){
		$langs = array('none'=>FOR_ALL_LANGS)+$langs;
		$form->addSelect('lang_code', $langs, FRM_LANGUAGE_LABEL, $currlang);
		$output = $form->renderSingle('lang_code');
	}
	else{
		$form->addHidden('lang_code', self::$language['code']);
		$output = Html::elem('div', null, LANGUAGE . ': '. self::$language['name']);
		$output .= $form->renderSingle('lang_code');
	}
	return $output;
  }
  
  /**
   * @title Check Language Request
   * @return array or bool
   * returns array of lang informations or flase
   */
  private function checkLanguageReq($param)
  {
  
	$row = db::fetch("SELECT * FROM languages WHERE langid='$param' AND active=1");
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
   * @title Create Categories Input
   * @return html
   * factory for creating form select element
   * 
   */
  public static function createCategoriesInput($table, $cont_id=null)
  {
    // get Category id
	$row = db::fetch("SELECT catid FROM categories_content_types WHERE ct_name='$table'");
	if(!$row) return false;
	$cat_id = $row['catid'];
	
	//select category
	$row = db::fetch("SELECT * FROM categories WHERE id=$cat_id");
	//if is required set it
	$required = $row['required'];
	
	// select actual category item
	if($cont_id){
		$row = db::fetch("SELECT * FROM categories_relations WHERE cont_id=$cont_id");
		if($row) $sel_cat_item = $row['cat_item_id'];
		if(!$row) $sel_cat_item = 0;
	}
	else{
		$sel_cat_item = 0;
	}
	
	// select sub categories
	$rows = db::fetchAll("SELECT * FROM categories_items WHERE cat_id=$cat_id ORDER BY title");
	if(!$rows) return false;
	
	if(!$required){
		$cats[0] = UNSORTED;		
	}
	
	foreach($rows as $row){
		$cats[$row['id']] = $row['title'];
	}
	
	$form = new Form; //create new phanthom form
	
	$form->addHidden('cat_id', $cat_id);
	$form->addSelect('cat_items', $cats, SELECT_CATEGORY, $sel_cat_item);
	
	return $form->renderSingle('cat_id').$form->renderSingle('cat_items');

  }
  
  public static function mailer($from, $to, $subject, $messageBody)
  {
  
    // Generate a boundary string    
		$semi_rand = md5(time());    
		$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
		
		$headers = "From: $from";
		
		// Add the headers for a file attachment    
		$headers .= "\nMIME-Version: 1.0\n" .    
             "Content-Type: multipart/mixed;\n" .    
             " boundary=\"{$mime_boundary}\"";
			 
		// Add a multipart boundary above the plain message    
		$message = "--{$mime_boundary}\n" .    
            "Content-Type: text/html; charset=\"utf-8\"\n" .    
            "Content-Transfer-Encoding: 7bit\n\n" .    
            $messageBody . "\n\n";
		
		
		$message .= "--{$mime_boundary}--\n";
		
		// Send the message    
		$ok = @mail($to, $subject, $message, $headers);    
		if ($ok) {    
		    $return = TRUE;
		} else {    
		    $return = FALSE;
		}
		
		return $return;
  
  }
  
  public static function uploadFile($file){
	
	$return = FALSE;
		
	$filename = date('U').'-'.machineStr(cleanFileName($file['name']), '.');
	$uploadfile = FILES_DIR . $filename;
	$tempfile = $file['tmp_name'];
	$type = $file['type'];
		
	umask(0000);
	if($uplStatus = @move_uploaded_file($tempfile, $uploadfile)){
	    chmod($uploadfile, 0666);    
	}
		
	// If upload OK
	if($uplStatus){
	    $return = $filename;
	}
	else {
	    Application::setError('Súbor sa nepodarilo odoslat!');
	}
		
	return $return;
  }
  
  
  /**
   * Saves Html pages into cache
   * @param string $buffer Html content
   */
  public function saveCachedPage($buffer) {
        
      $request = ($_SERVER["QUERY_STRING"] ? $_SERVER["QUERY_STRING"] : "front");
      
      $cachedFile = uniqid(date("U")) . '.html';

      $fileHandler = fopen(WWW_DIR . '/cache/' . $cachedFile, 'w');
      if(@fwrite($fileHandler, $buffer)) {
          db::exec("DELETE FROM `cache` WHERE `request`=%v", $request);
          db::exec("INSERT INTO `cache` (`filename`, `request`) VALUES (%v, %v)", $cachedFile, $request);
      }
      else {
          if(DEVELOPMENT) {echo "Chyba pri zapise html do cache."; exit;}
      }
      fclose($fileHandler);
  }
}