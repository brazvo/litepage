<?php
/**
 * Project Lite Page
 * System controller: System
 * file: System.php
 *
 * 
 */
class System extends BaseAdmin
{

  // Properties
  
  // Constructor
  public function __construct()
  {
  
    parent::__construct();

  
  }
  
  // Methods
  
  function renderDefault()
  {
	
	$items = new AdmContentModel;
	
	$result = $items->findItems('admin/'.Application::$pageName);
	
	$output = '';
	foreach($result as $item){
	  $output .= '<div><a href="'.Application::link($item['path']).'">'.$item['title'].'</a></div>';
	}
	
        $this->template->title = 'System';
	$this->template->content = $output;
  
  }

}