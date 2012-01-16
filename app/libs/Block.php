<?php 
/**
 * PHP Class to create blocks
 * 
 * <code><?php
 * require('class.Blocks.php');
 * ? ></code>
 * 
 * ==============================================================================
 * 
 * @version $Id: class.Blocks.php,v 0.10 2010/01/28 $
 * @copyright Copyright (c) 2010 Branislav Zvolensky
 * @author Branislav Zvolensky <zvolensky@mbmartworks.sk>
 * @license http://opensource.org/licenses/gpl-license.php GNU General Public License (GPL)
 * 
 * ==============================================================================
 *
 * Usage of this script is on own risk. The autor does not give you any warranty.
 * If you will use this script in your files you must place these header informations with the script.
 */
class Block {

  // Properties
	private $host, $user, $password, $database;
	private $account;
	
	
	// Constructor
	protected function __construct() {
	
		    $this->host="localhost";		// Set host name
		    $this->user="nce";			// Set user
		    $this->password="brunobfoot";	// Set password
		    $this->database="nce";	// Set database name
			$this->account="";
		
	}
	
	// Methods
	
	/**
	 * @title Navigation Bar
	 * rendering navigation bar
	 */
	public static function navBar($notext = false)
	{
		
		return self::getMenu('navigation', '',$notext);

	}
	
	/**
	 * @title User Info
	 * displays informations about logged user
	 */
	public static function userInfo()
	{
	  $output = '';
	  
	  if(Application::$logged['status']){
	    if(Application::$logged['role'] == 'admin') $title = 'Administrátor: ';
		if(Application::$logged['role'] == 'editor') $title = 'Editor: ';
		if(Application::$logged['role'] == 'user') $title = 'Užívateľ: ';
		
		$showStr = "$title".Application::$logged['name']." ".Application::$logged['surname']." (".Application::$logged['user'].")";
		$showStr .= ' | <a href="'.BASEPATH.'/admin/users/logout">Odhlasiť sa</a>';
		$output .= '<div class="block-user-info">'.$showStr.'</div>';
	  }
	  
	  return $output;
	}
	
	/**
	 * @title Admin Bar
	 * displays Administration Bar
	 */
	public static function adminBar()
	{
	  $output = '';
	  
	  if(Application::$logged['status']){
	    
		switch(Application::$logged['role']){
		  case 'admin':
		    $menu = 'administration';
			break;
		  case 'editor':
		    $menu = 'editors_menu';
			break;
		  case 'user':
		    $menu = 'users_menu';
			break;
		}
	    
		$output = '<div class="block-adm-bar">'.self::getMenu($menu).self::userInfo().'</div>';
		$output .= '<script type="text/javascript">
                    /* <![CDATA[ */
                    // content of your Javascript goes here
					$("div.block-adm-bar .menu ul.submenu").css({"display":"none"});
					$("div.block-adm-bar .menu li.menu-item").mouseover(function(){
					  $("ul.submenu", this).show();
					});
					$("div.block-adm-bar .menu li.menu-item").mouseout(function(){
					  $("ul.submenu", this).hide();
					});
                    /* ]]> */
                    </script>';
	  }
	  
	  return $output;
	}
	
	/**
	 * @title Logo Block
	 * displays Logo
	 */
	public static function logoBlock()
	{
	  $output = '';
	  
	  if(Application::$defLanguage['code'] == Application::$language['code']){
	    $lang = '';
	  }
	  else{
	    $lang = '/' . Application::$language['code'];
	  }
	  
	    if(trim(LOGO) != ''){
		$showStr = '<div id="block-logo">
		              <a href="'.BASEPATH.$lang.'"><img src="'.BASEPATH.'/images/'.LOGO.'" alt="" style="border:none" /></a>
				   </div>';
					
		$output .= $showStr;
		}
	  
	  
	  return $output;
	}
	
	/**
	 * @title Header Title Block
	 * displays Header Title
	 */
	public static function headerTitleBlock()
	{
	  $output = '';
	  
	    if(trim(HEADER_TITLE) != ''){
		$showStr = '<div id="block-header-title">
		              <h2><a href="'.BASEPATH.'">'.HEADER_TITLE.'</a></h2>
				   </div>';
					
		$output .= $showStr;
		}
	  
	  
	  return $output;
	}
	
	/**
	 * @title Slogan Block
	 * displays Slogan
	 */
	public static function sloganBlock()
	{
	  $output = '';
	  
	    if(trim(PAGE_SLOGAN) !=''){
		  $output = '<div id="block-slogan">'.PAGE_SLOGAN.'</div>';
		}
	  
	  return $output;
	}
	
	/************************************************************** GETs & SETs
	
	/**
	 * @title Get
	 * reads prepared block include pages
	 */
	public static function get($block=NULL)
	{
		if(!$block){
  		  Application::setError('Class Blocks, function get. Error: No block attribute received');
		  return false;
		}
		
		if(@is_file(BLK_DIR.'/'.$block.'.php')){
			$blockPath = BLK_DIR.'/'.$block.'.php';
                        ob_start();
			require($blockPath);
                        $blockHtml = ob_get_clean();

                        echo Html::divBlock($blockHtml, "block block_{$block}", $block);
                        
		}
		else{
		  Application::setError('Class Blocks, function get. Error: A block with this name ' . $block . ' does not exists.');
		  return false;
		}
		
	}
	
	/**
	 * @title Get Menu Items
	 * returns array with menu items
	 */
	private function getMenuItems($menu)
	{
	  
	  $db = Application::$db;
	  // get Menu
	  $sql = "SELECT * FROM menus WHERE machine_name='$menu'";
	  
	  $query = $db->prepare($sql);
	  
	  $query->execute();
	  
	  $row = $query->fetch();
	  
	  // get Menu items
	  $sql = "SELECT * FROM menu_items WHERE menu_id=$row[0] AND allowed=1 ORDER BY priority";
	  
	  $query = $db->prepare($sql);
	  
	  $query->execute();
	  
	  $rows = $query->fetchAll(PDO::FETCH_ASSOC);
	  
	  $return = null;
	  foreach($rows as $row){
	    $return[$row['path']] = $row['title'];
	  }
	  
	  return $return;
	
	}
	
	/**
	 * @title Get Menu
	 * returns array with menu items
	 */
	public static function getMenu($menu, $path_pref='', $title = false, $notext = false)
	{
	  if($path_pref) $path_pref = $path_pref.'/';

          $row = db::fetch("SELECT * FROM menus WHERE machine_name='$menu'");
	  
	  //insert language code into paths
	  if(!$row['lang'] or $row['lang'] == 'none'){
	    // if not set menu language set language from request
	    if(Application::$defLanguage['code'] == Application::$language['code']){
	      $lang = '';
	    }
	    else{
		  $lang = Application::$language['code'].'/';
	    } 
	  }
	  // else if current language is equal to menu language, do not set $lang
	  elseif($row['lang'] == Application::$defLanguage['code']){
		$lang = '';
	  }
	  // else set $lang to menu language
	  else{
	    $lang = $row['lang'].'/';
	  }
	  
	  $menu_id = $row['id'];
	  $menu_title = $row['name'];
	  
	  // get Menu items
          $rows = db::fetchAll("SELECT * FROM menu_items WHERE menu_id='$menu_id' AND allowed=1 AND child_of=0 ORDER BY priority");
	  $output = ($title ? Html::elem('div')->setClass('menu-title')->setCont(Html::elem('div')->setClass('menu-title-inner')->setCont($menu_title)) : '');
	  $output .= '<ul class="menu menu-'.$menu.'">';
	  $index = 1;
	  $last = count($rows);
	  foreach($rows as $row){
	    
		if($notext) $row['title'] = '';
		
		$li = Html::elem('li')->setClass('menu-item');
		$li->addClass('menu-item-'.$row['id']);
		
		$classFirst = ($index == 1 ? 'first' : false);
		$classLast = ($index == $last ? 'last' : false);
		
		if($classFirst) $li->addClass($classFirst);
                if($classLast) $li->addClass($classLast);
		
		$classExp = ($row['expanded'] == 1 ? $classExp = 'expanded' : false);
		
		if($classExp) $li->addClass($classExp);
		
		$path = $row['path'];
		
		if($row['path'] == '<front>') $path = Application::$language['main_page'];
		if($row['path'] == '<none>') $path = 'error/show/404';
		$path = preg_replace('/<basepath>/', BASEPATH, $path);

                if(Application::$menuActive == $path) $active = 'active';
                    //elseif(BASEPATH == $path) $active = 'active';
                    //elseif(Application::$menuActive == Application::$language['main_page']) $active = 'active';
                    else $active = false;
		
		if($active) $li->addClass($active);
		
		//$output .= '<li class="menu-item menu-item-'.$index.$classExp.$classFirst.$active.'">';
		$link = Html::elem('a');
		if(substr($path,0,4) == 'http' or substr($path,0,4) == 'www.'){
		  if(substr($path,0,4) == 'www.') $path = 'http://' . $path;
		  $link->href($path)->title($row['title'])->setCont($row['title']);
		}
		else{
		  //$output .= '<a href="'.BASEPATH.'/'.$lang.$path_pref.$row['path'].'"'.$HrefActive.'>'.$row['title'].'</a>';
		  $link->href(BASEPATH.'/'.$lang.$path_pref.$path)->title($row['title'])->setCont($row['title']);
		  if($active) $link->setClass($active);
		}
		$submenu = ($row['expanded'] ? self::getSubMenu($row['id'], $path_pref, $lang, $notext) : '');
		$li->setCont($link . $submenu);
		$output .= $li;
		  
		$index++;  
	  }
	  
	  $output .= '</ul>';
	  
	  return $output;
	
	}
	
	
	private function getSubMenu($childof, $path_pref, $lang, $notext)
	{
	  
	  $db = Application::$db;
	  
	  $sql = "SELECT * FROM menu_items WHERE child_of=$childof AND allowed=1 ORDER BY priority";
          $rows = db::fetchAll($sql);
	  
	  if($rows){
	    $output = '<ul class="submenu submenu-'.$childof.'">';
		$last = count($rows);
		$index = 1;
	    foreach($rows as $row){
		  
		  if($notext) $row['title'] = '';
		  $li = Html::elem('li')->setClass('menu-item');
		  $li->addClass('menu-item-'.$row['id']);
		  
		  $classExp = ($row['expanded'] == 1 ? $classExp = 'expanded' : false);
		  if($classExp) $li->addClass($classExp);
		  
		  $path = $row['path'];
		
		  if($row['path'] == '<front>') $path = Application::$language['main_page'];
		  if($row['path'] == '<none>') $path = 'error/show/404';
		  $path = preg_replace('/<basepath>/', BASEPATH, $path);
		
		  if(Application::$menuActive == $path) $active = 'active';
                    elseif(BASEPATH == $path) $active = 'active';
                    else $active = false;
		  
		  //$output .= '<li class="menu-item menu-item-'.$index.$classExp.'">';
		  $link = Html::elem('a');
		  if(substr($path,0,4) == 'http' or substr($path,0,4) == 'www.'){
		    if(substr($path,0,4) == 'www.')
		      $path = 'http://' . $path;
		    $link->href($path)->title($row['title'])->setCont($row['title']);
		  }
		  else{
		    //$output .= '<a href="'.BASEPATH.'/'.$lang.$path_pref.$row['path'].'"'.$HrefActive.'>'.$row['title'].'</a>';
		    $link->href(BASEPATH.'/'.$lang.$path_pref.$path)->title($row['title'])->setCont($row['title']);
		    if($active) $link->setClass($active);
		  }
		  
		  $submenu = ($row['expanded'] ? self::getSubMenu($row['id'], $path_pref, $lang, $notext) : '');
		  $li->setCont($link . $submenu);
		  $output .= $li;
		  
		  $index++;
		}
		$output .= '</ul>';
	  }
	  else{
	    $output = '';
	  }
	  return $output;
	
	}
	
	
}
?>