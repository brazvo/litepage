<?php
/**
 * Project: LitePage
 *
 * @author Branislav Zvolenský <zvolensky@mbmartworks.sk>
 */

class Content extends BaseController
{

  // Properties
  private $content;
  
  private $contentFields;
  
  private $module = null;
  
  // Constructor
  public function __construct()
  {
     
	if(empty(Application::$id)) {   
		if(DEVELOPMENT){redirect('error/show/404', 'Chýbajúce ID.'); exit;}
		else{redirect('error/show/404'); exit;}
	}
	$obj = new ContentModel;
	if((int)Application::$id > 0){
                $this->content = $obj->find(Application::$id);
                
                if(!$this->content) {
                    if(DEVELOPMENT){redirect('error/show/404', 'Neexistujúce ID.'); exit;}
                    else{redirect('error/show/404'); exit;}
                }
		
                // check if module
                $this->module = $this->content['module'];
		// if content has module call module controller
                if($this->module) {
                    
                    $aModIni = getModuleIni($this->content['table']);
                    //var_dump($aModIni);exit;
                    $sController = strtolower( isset( $aModIni['frontend.controller'] ) ? $aModIni['frontend.controller'] : 'frontend' );
                    $sAction = strtolower( isset( $aModIni['frontend.action'] ) ? $aModIni['frontend.action'] : 'show' );
                    $mId = isset( $aModIni['frontend.id'] ) ? ( !empty($aModIni['frontend.id']) ? $aModIni['frontend.id'] : $this->content['content_id'] ) : $this->content['content_id'];
                    
                    $redirect = $this->content['table'] . "/{$sController}/{$sAction}/{$mId}";
                    redirect( $redirect );
                }
                
                $this->perm_mach_name = $this->content['table'];
                $this->contentFields = $obj->getFields(Application::$id);
	}
	else {
		$table = $obj->checkTable(Application::$id);
		if($table){
			$this->perm_mach_name = $table;
		}
		else{
			$this->perm_mach_name = 'content';
		}
	}

        $this->addClass($this->perm_mach_name);
        
        parent::__construct();
          
  }
  
    protected function startUp($id) {
        parent::startUp($id);
        // contModules insert into $this->modulesHtmlContent
        // scripts alowable when content controller is called
        // standard handlers are show, edit, add, save, save, savenew, delete
        // So it means that you can generate
        // different html code for each handler
        
        $this->modulesHtmlContent = Application::runContentHandlers($this, $id, null);
    }
  
  
  function actionShow(){
        $this->template->setView('default');
  }
  
  // Methods
  function renderShow($id)
  {    
	// If do not have permisions to view this it redirect you to error page
	if(!Application::$view){
		redirect('error/default/403');exit;
	}
	
	// Set last path request
	if(isset($_REQUEST['q']) && $_REQUEST['q']) $destination = $_REQUEST['q'];
		else $destination = Application::$language["main_page"];
	
	$content = new ContentModel;
	
	$result = $this->content;
	if(!$result){
	    if(DEVELOPMENT){
		redirect('error/show/404', 'Neexistujúci záznam ID: '.Application::$id);exit;
            }
            else{
		redirect('error/show/404');exit;
            }
	}
	//check if language only content
	if(!$result['lang']){
		//do nothing
	}
	elseif($result['lang'] && $result['lang'] == 'none'){
		//do nothing
	}
	elseif($result['lang'] && $result['lang'] != Application::$language['code']){
		redirect('error/show/2');exit;
	}
	
	$fields = $content->getFields(Application::$id);
	
	$this->template->cid = Application::$id;
	$this->template->title = $fields['title']['value'];
        $this->addClass(cssClassStr($fields['title']['value']));
	unset($fields['title']);
	$content ='';
	
	foreach($fields as $key => $val){
		
		if( $key == 'body' ) {
			$val['value'] = contentApi($val['value']);
		}
		
            //if image load images
            if($val['value'] == 'image'){
                $content .= $this->createImgGallery(Application::$id, $val['attrs']['order_by']);
		$this->template->gallery = $this->createImgGallery(Application::$id, $val['attrs']['order_by']);
            }
             elseif($val['value'] == 'file'){
                $content .= $this->createFilesTable(Application::$id, $val['attrs']['order_by']);
		$this->template->files = $this->createFilesTable(Application::$id, $val['attrs']['order_by']);
            }
            else{
	    //strip slashes
				$val['value'] = stripslashes($val['value']);
				
				if( $tmpVal = @unserialize($val['value']) ) {
					$val['value'] = implode(', ', $tmpVal);
				}
		
				if($val['label']){
					$content .= Html::elem('div')->setClass($val['class'])->setCont($this->texy->process('<b>'.$val['label'].'</b>&nbsp;'.$val['value']) ); // if is set label put it there
					$this->template->$key = '<div class="'.$val['class'].'">'.$this->texy->process('<b>'.$val['label'].'</b>&nbsp;'.$val['value']).'</div>';
				}
				else{
					$content .= Html::elem('div')->setClass($val['class'])->setCont($this->texy->process($val['value']));
							$this->template->$key = '<div class="'.$val['class'].'">'.$this->texy->process($val['value']).'</div>';
				}
            }
	}
	
	if(Application::$isFrontPage === true ){
	  $this->template->setView('front');
	}
	
	if(Application::$edit && $result['content_owner']){
	  $this->template->edit = '<div class="cont-edit"><a href="'.BASEPATH.'/admin/content/edit/'.Application::$id.'?destination='.$destination.'">[ '.EDIT.' ]</a></div>';
	}
	else{
	  $this->template->edit = false;
	}
	
	//$this->template['title'] = $result['title'];
	$this->template->class = Application::$pageName;
	$this->template->content = $content;
	$this->template->moduleContent = $this->modulesHtmlContent;
        
        
        // the content may be cahced when user is not logged
        if(!Application::$logged['status']) Application::$pageCache = "content/show#{$id}";

  
  }
  
  function renderShowall($id)
  {    
	// If do not have permisions to view this it redirect you to error page
	if(!Application::$view){
		redirect('error/default/403');exit;
	}
	
	// Set last path request
	if(isset($_REQUEST['q']) && $_REQUEST['q']) $destination = $_REQUEST['q'];
		else $destination = Application::$language["main_page"];
	
	$content = new ContentModel;
	
	$results = $content->findAllOfKind(Application::$id);
	
	if(!$results){
	    if(DEVELOPMENT){
			redirect('error/show/404', 'Neexistujúci záznam ID: '.Application::$id);exit;
		}
		else{
			redirect('error/show/404');exit;
		}
	}
	
	$this->template->title = $content->getContentTypeTitle($id);
	$this->template->class = Application::$pageName;
	$contents = array();
	
	foreach($results as $result){
		
		$row = db::fetch("SELECT * FROM content WHERE content_type_machine_name='$id' AND content_id=".$result['id']);
		if(Application::$logged['role'] == 'user'){
			if($row['uid'] == Application::$logged['userid']){
				$owner = true;
			}
			else{
				$owner = false;
			}
		}
		else{
			// if not role - user, it does not matter, so...
			$owner = true;
		}
		
		$result['content_owner'] = $owner;
		$result['lang'] = $row['lang'];
		
		//check if language only content
		if(!$result['lang']){
			//do nothing
			$notput = false;
		}
		elseif($result['lang'] && $result['lang'] == 'none'){
			//do nothing
			$notput = false;
		}
		elseif($result['lang'] && $result['lang'] != Application::$language['code']){
			$notput = true;
		}
		else{
			$notput = false;
		}
		
		if(!$notput):
		
			$fields = $content->getFields($row['id']);
			
			$contVars['cid'] = $row['id'];
			$contVars['title'] = $fields['title']['value'];
			unset($fields['title']);
			
			foreach($fields as $key => $val){
			  //if image load images
			  if($val['value'] == 'image'){
				$contVars['gallery'] = $this->createImgGallery($result['id'], $val['attrs']['order_by']);
				$contVars['contentElements'][$row['id']][] = $this->createImgGallery($result['id'], $val['attrs']['order_by']);
			  }
			  elseif($val['value'] == 'file'){
				$contVars['files']= $this->createFilesTable($result['id'], $val['attrs']['order_by']);
				$contVars['contentElements'][$row['id']][] = $this->createFilesTable($result['id'], $val['attrs']['order_by']);
			  }
			  else{
			    $val['value'] = stripslashes($val['value']);
				if($val['label']){
					$contVars[$key] = '<div class="'.$val['class'].'">'.$this->texy->process('<b>'.$val['label'].'</b>&nbsp;'.$val['value']).'</div>';
					$contVars['contentElements'][$row['id']][] = '<div class="'.$val['class'].'">'.$this->texy->process('<b>'.$val['label'].'</b>&nbsp;'.$val['value']).'</div>';
				}
				else{
					$contVars[$key] = '<div class="'.$val['class'].'">'.$this->texy->process($val['value']).'</div>';
					$contVars['contentElements'][$row['id']][] = '<div class="'.$val['class'].'">'.$this->texy->process($val['value']).'</div>';
				}
			  }
			}
			
			if(Application::$edit && $result['content_owner']){
			  $contVars['edit'] = '<div class="cont-edit"><a href="'.BASEPATH.'/admin/content/edit/'.$row['id'].'?destination='.$destination.'">[ Editovať ]</a></div>';
			}
			else{
			  $contVars['edit']='';
			}
			
			$contents[] = $contVars;
				
		endif;
	}
        
        $this->template->contents = $contents;
  
  }
  
  /********************************************************* FACTORIES *************/
  private function createImgGallery($id,$order_by)
  {
    $obj = new ContentModel;
	$rows = $obj->findImages($id,$order_by);
	if($rows){
		$lis = '';
		foreach($rows as $row){
		  $src = BASEPATH.'/images/thumb_'.$row['image_name'];
		  $imgSize = getImageSize($src);
		  $img = Html::elem('img', array('src'=>$src, 'width'=>$imgSize[0], 'height'=>$imgSize[1], 'alt'=>$row['description'], 'style'=>'border:none'));
		  $a = Html::elem('a', array('href'=>BASEPATH.'/images/'.$row['image_name'], 'rel'=>'gallery'.$id, 'class'=>'content-gallery-'.$id, 'title'=>$row['description']), $img);
		  $lis .= Html::elem('li', array('id'=>'image-'.$row['id'], 'class'=>'image-gallery-image'), $a);
		}
		
		$output = Html::elem('ul', array('id'=>'image-gallery-'.$id, 'class'=>'image-gallery'), $lis);
		$output .= "
			<script type='text/javascript'>
			/* <![CDATA[ */
				jQuery(document).ready(function($){
				  $('a.content-gallery-$id').fancybox();
				});
			/* ]]> */
			</script>";
		return $output;
	}
	else{
		return '';
	}
  }
  
  private function createFilesTable($id,$order_by)
  {
    $obj = new ContentModel;
	$rows = $obj->findFiles($id,$order_by);
	if($rows){
		$tr = '';
		$idx = 0;
		foreach($rows as $row){
		  if($idx == 1){
		      $trclass = 'even';
			  $idx = 0;
		  }
		  else{
		      $trclass = 'odd';
			  $idx++;
		  }
		  
		  if(trim($row['description']) != ''){
			  $label = $row['description'];
		  }
		  else{
			  $label = $row['file_name'];
		  }
		  
		  $filesize = round(@filesize(WWW_DIR.'/files/'.$row['file_name'])/1024, 2) . 'kB';
		  if(@filesize(WWW_DIR.'/files/'.$row['file_name']) > 1024000) $filesize = round(@filesize(WWW_DIR.'/files/'.$row['file_name'])/1024000, 2) . 'MB';
		  //find fyle-type icon
		  if(is_file(WWW_DIR.'/images/icons/'.$row['file_type'].'.png')){
		      $icon = Html::elem('img',array('src'=>BASEPATH.'/images/icons/'.$row['file_type'].'.png', 'class'=>'content-files-icon', 'alt'=>strtoupper($row['file_type']).' súbor', 'title'=>strtoupper($row['file_type']).' súbor'));
		  }
		  else{
		      $icon = Html::elem('img',array('src'=>BASEPATH.'/images/icons/na.png', 'class'=>'content-files-icon', 'alt'=>strtoupper($row['file_type']).' súbor', 'title'=>strtoupper($row['file_type']).' súbor'));
		  }
		  $a = Html::elem('a', array('href'=>BASEPATH.'/files/'.$row['file_name'], 'class'=>'link content-files-'.$id, 'title'=>$row['description']), $label);
		  $span = Html::elem('span', array('class'=>'filesize content-files-'.$id), $filesize);
		  $td = Html::elem('td', array('class'=>'icon'), $icon);
		  $td .= Html::elem('td', array('class'=>'file'), $a);
		  $td .= Html::elem('td', array('class'=>'size'), $span);
		  $tr .= Html::elem('tr', array('class'=>$trclass), $td);
		}
		$table = Html::elem('table', array('id'=>'content-files-table-'.$id, 'class'=>'content-files-table', 'cellspacing'=>'0'), $tr);
		
		return  $table;
	}
	else{
		return '';
	}
  }

}