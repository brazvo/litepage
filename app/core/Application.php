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
        
        public static $pageCache = null;
        
        public static $pageHtml;
	
	// Constructor
	public function __construct()
	{
		                
                $GET = Vars::get('GET');
            
                $this->setCacheAndDev();    // set caching and dev tools

		self::$isError = FALSE;
		self::$errors = array();
		self::$isMessage = FALSE;
		self::$messages = array();
		self::$logged = false;
		self::$subDir = '';
		self::$language = Environment::getLanguage() ? Environment::getLanguage() : $this->getDefaultLang();
		self::$defLanguage = $this->getDefaultLang();
                
                // set controller, action, id
                $params[] = self::$pageName = Environment::getController();
                $params[] = self::$action = Environment::getAction();
                $params[] = self::$id = Environment::getId();
                
                self::$isFrontPage = Environment::get('frontPage');
                
                self::$pathRequest = isset ( $GET->q ) ? $GET->q : NULL;
		self::$menuActive = isset ( $GET->q ) ? $GET->q : self::$language['main_page'];
                              
		
		if( isset($_REQUEST['destination']) && $_REQUEST['destination'] ){
			$_SESSION['destination'] = $_REQUEST['destination'];
		}
		
		if(isset($_SESSION['pathRequest']) && $_SESSION['pathRequest']){
		  self::$pathRequest = $_SESSION['pathRequest'];
		}
		
		// Message after redirect;
		if( isset($_SESSION['message']) && $_SESSION['message'] ){
			self::setMessage($_SESSION['message']);
			unset($_SESSION['message']);
		}
		
		// Errors after redirect;
		if( isset($_SESSION['error']) && $_SESSION['error'] ){
			self::setError($_SESSION['error']);
			unset($_SESSION['error']);
		}
		
		// Errors from form after redirect;
		if( isset( $_SESSION['__BZ_FORM']['errors'] ) && $_SESSION['__BZ_FORM']['errors'] ){
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
		
		$this->setAppData($params);
						
		//$this->getAppModules(); // load application modules
		
		//$this->loadContentModules(); // load content extenders
                
                Environment::set( 'contModulesObjects', $this->loadContentModules() );
                Environment::set( 'appModulesObjects', $this->loadAppModules() );
		
		// Set language file
		self::$lang_file = self::$subDir.'/languages/'.self::$language['mach_name'].'.inc';
		
		// Set system language file
		self::$sys_lang_file = APP_DIR.'/languages/system/'.self::$language['mach_name'].'.inc';
                
                // Include language file if exists
		if(is_file(APP_DIR.self::$lang_file)){
			include_once(APP_DIR.self::$lang_file);
		}
		
		// Include system language file if exists
		if(is_file(self::$sys_lang_file)){
			include_once(self::$sys_lang_file);
		}
                
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

	  self::$subDir = substr('/' . Environment::getPathPrefix(), 0 ,-1); //remove last slash

          // get standalone mudules and if the request is into the module set subdir
          self::$activeModules = Environment::get('standaloneModules');
          self::$activeModules = array_merge(self::$activeModules, Environment::get('applicationModules') );

          if( in_array( Environment::getModule(), self::$activeModules ) ){
	    
		self::$subDir = '/modules/'.Environment::getModule();
		
		self::$moduleRequest = true;
	  }
	  else{
		
		self::$moduleRequest = false;
		
	  }
	
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
            
            if($result) {
                $locked = $log->isLocked();
                
                if($locked) {
                    $log->logout();
                    redirect(null, LOCKED_ACCOUNT);
                }
            }

            if($result){
                self::$logged['status'] = true;
                self::$logged['logged'] = true;
                self::$logged['userid'] = $result['id'];
                self::$logged['user'] = $result['user'];
                self::$logged['name'] = $result['name'];
                self::$logged['surname'] = $result['surname'];
                self::$logged['role'] = $result['role'];

                return true;
            }
            else{
                self::$logged['status'] = false;
                self::$logged['logged'] = false;
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
		  
		// check if the content will be cached
                if(Application::$pageCache && CACHE_PAGES) {
                    if(is_string(Application::$pageCache)) {
                        $pageCacheKey = sha1(Application::$pageCache);
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
                
                
		$controller = $this->getController(self::$pageName);
		
		if(!$controller) {
                    if(DEVELOPMENT){
                        throw new Exception("Controller: ".ucfirst(self::$pageName)." is missing");
                    }
                    else{
                        redirect('error/show/404');
                    }
		  
		}
                else {
                    
                    ob_start();
                    $controller->run();
                    self::$pageHtml = ob_get_clean();

                    // if cache is ON and isset $pageCacheKey
                    if(CACHE_PAGES && $pageCacheKey) {
                        $cacheObj = new Cache('cache', 'html');

                        $cacheObj->save($pageCacheKey, self::$pageHtml);
                    }
                }
		
	}
	
	private function getController($contName)
	{
            $contName = ucfirst($contName);

            if(@is_file(APP_DIR.self::$subDir.'/controllers/'.$contName.'.php')){
                require(APP_DIR.self::$subDir.'/controllers/'.$contName.'.php');
                return new $contName();
            }
            else{
                return false;
            }
	
	}
    
    /**
     * Inserts Application Modules into template
     */
    private function loadAppModules()
    {
        $modules = Environment::get( 'applicationModules' );
        $return = array();
        if(!empty($modules)){
            foreach($modules as $module){
                
                // check for ini file
                if( file_exists( MOD_DIR."/$module/module.ini" ) ) {
                    $ini = parse_ini_file(MOD_DIR."/$module/module.ini");
                    if( isset($ini['handlers']) && $ini['handlers'] ) {
                        $classname = ucfirst($ini['handlers']);
                    }
                    else {
                        $classname = ucfirst($module);
                    }
                }
                else {
                    $classname = ucfirst($module);
                }
                
                $appFile = MOD_DIR."/{$module}/{$classname}.php";
                require($appFile);
                $return[] = new $classname;
            } 
        }
        
        return $return;

    }

    /**
     * Load content extend modules into template
     */
    private function loadContentModules()
    {
        $modules = Environment::get( 'contExModules' );
        $return = array();
        if(!empty($modules)){
            foreach($modules as $module){
                
                // check for ini file
                if( file_exists( MOD_DIR."/$module/module.ini" ) ) {
                    $ini = parse_ini_file(MOD_DIR."/$module/module.ini");
                    if( isset($ini['handlers']) && $ini['handlers'] ) {
                        $classname = ucfirst($ini['handlers']);
                    }
                    else {
                        $classname = ucfirst($module);
                    }
                }
                else {
                    $classname = ucfirst($module);
                }
                
                $appFile = MOD_DIR."/{$module}/{$classname}.php";
                require($appFile);
                $return[] = new $classname;
            } 
        }
        
        return $return;
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
   * @title Create Lang Input
   * @return html
   * factory for creating form select element
   * if it finds that only one lang is active, it creates hidden input with empty value
   */
  public static function createLangInput2(AppForm $form)
  {
	$langs = self::getAllLanguages();
	
	//creating fanthom form (we only use its elements)
	if($langs && $langs != 'one'){
		$langs = array('none'=>FOR_ALL_LANGS)+$langs;
		//$form->addSelect('lang_code', $langs, FRM_LANGUAGE_LABEL, $currlang);
                $form->addSelect('lang_code', FRM_LANGUAGE_LABEL, $langs);
	}
	else{
		$form->addHidden('lang_code', self::$language['code']);
	}
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
  
  
  
  public static function link( $sLink )
  {
      $lang = self::$language['code'] !== self::$defLanguage['code'] ? self::$language['code'] . '/' : '';
      
      return baseUrl() . "/{$lang}{$sLink}";
  }
  
  public static function path( $sLink )
  {
      $lang = self::$language['code'] !== self::$defLanguage['code'] ? self::$language['code'] . '/' : '';
      
      return "{$lang}{$sLink}";
  }  
  
  public static function imgSrc( $sLink )
  {     
      return baseUrl() . "/images/{$sLink}";
  }
  
    public static function phpRun($code)
    {
        eval($code);
    }


    /**
     * Sets permissions for content type
     * @param type $role
     * @param type $mach_id 
     */
    public static function setPermissions($role, $mach_id)
    {
        if($role == 'admin'){
            Application::$view = 1;
            Application::$add = 1;
            Application::$edit = 1;
            Application::$delete = 1;
	}
	else{
            $row = db::fetch("SELECT * FROM permisions WHERE cont_mach_name='$mach_id'");

            Application::$view = $row[$role.'_view'];
            Application::$add = $row[$role.'_add'];
            Application::$edit = $row[$role.'_edit'];
            Application::$delete = $row[$role.'_delete'];

            // check for extra user's permisions
            if(Application::$logged['status']){
                $uid = Application::$logged['userid'];
                //echo $mach_id;
                $row = db::fetch("SELECT * FROM users_permisions WHERE machine_name='$mach_id' AND uid=$uid");
                //var_dump($row);
                if($row){
                        Application::$view = $row['view'];
                        Application::$add = $row['add'];
                        Application::$edit = $row['edit'];
                        Application::$delete = $row['delete'];
                }
            }
	}
	
    }
    
    /**
     * Get permission for an action
     * @param type $role
     * @param type $mach_id
     * @param type $action
     * @return bool 
     */
    public static function getPermisionForAction($role, $machine_name, $action)
    {
        if($role == 'admin'){
            return 1;
        }
        else{
            $row = db::fetch("SELECT * FROM permisions WHERE cont_mach_name='$machine_name'");
            return $row[$role.'_'.$action];
        }
    }
    
    
    /////////////////////////////////////////// Content Modules Handlers
    public static function runContentHandlers($oController, $cid, $post)
    {
        // contModules insert into $this->modulesHtmlContent
        // scripts alowable when content controller is called
        // standard handlers are show, edit, add, save, save, savenew, delete
        // So it means that you can generate
        // different html code for each handler
        
        $ret = '';

        $contModules = Environment::get( 'contModulesObjects' );
        foreach ($contModules as $oModObj) {
            $rc = new ReflectionClass( $oModObj );
            
            // make startup in module
            if( $rc->hasMethod('startUp') ) $oModObj->startUp( $oController, $cid );
            
            // found and run handler
            $handler = 'handle' . ucfirst( Application::$action );

            if( $rc->hasMethod($handler) ) {
                $result = $oModObj->$handler( $cid, $post );
                if( $result ) $ret .= $result;
            }
        }
        
        return $ret;
    }
    
    
    public static function runAppHandlers($oController, $id, $post)
    {
        // appModules insert into template->headerModulesHtml and template->bodyModulesHtml
        // scripts alowable in whole application when the handlers return the code
        // standard handlers are show, edit, add, So it means that you can generate
        // different html code for each handler
        // the result has to be accessable in $result['header'] and $result['body']
        
        //init template vars
        $ret = array('header'=>'', 'body'=>'');
        $appModules = Environment::get( 'appModulesObjects' );
        foreach ($appModules as $oModObj) {
            $rc = new ReflectionClass( $oModObj );
            
            // make startup in module
            if( $rc->hasMethod('startUp') ) $oModObj->startUp( $oController, $id );
            
            // found and run handler
            $handler = 'handle' . ucfirst( Application::$action );
            if( $rc->hasMethod($handler) ) {
                $result = $oModObj->$handler( $id );
                if( isset($result['header']) ) $ret['header'] .= $result['header'];
                if( isset($result['body']) ) $ret['body'] .= $result['body'];
            }
        }
        
        return $ret;
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