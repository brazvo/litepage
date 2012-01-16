<?php

class Frontend extends CategoriesBaseController
{

  // Properties
  
  // Constructor
  public function __construct()
  {
    $this->perm_mach_name = 'categories';
    parent::__construct();

  }
  
  // Methods
  /***************************************** ACTIONS ***/
  
  
  /***************************************** RENDERERS ***/
  
  // Categories items
  function renderListitems($id)
  {
    if(!Application::$view) redirect('error/show/1'); //check permision
	
	$obj = new AdmCategoriesModel;
	$values = $obj->findItems($id);
	$this->template->setView('default');
	$this->template->title = CAT_ITEMS_LIST_TITLE;
	$this->template->content = $this->createItemsList($id, $values);

  }
  
  function renderShow($id)
  {
    if(!Application::$view) redirect('error/show/1'); //check permision
	
	$obj = new AdmCategoriesModel;
	$row = $obj->find($id);
	
	$values = $obj->findAllContent($id);
	
	$this->template->setView('showitem');
	$this->template->title = $row['title'];
	$this->template->bodyclass .= ' '.strtolower(machineStr($row['title']));
	$this->template->description = $this->texy->process($row['description']);
	$this->template->content = $this->createContentList($row['id'], $values);

  }
  
  function renderShowitem($id)
  {
    if(!Application::$view) redirect('error/show/1'); //check permision
	
	$obj = new AdmCategoriesModel;
	$row = $obj->findItem($id);
	
	$values = $obj->findContent($row['cat_id'], $id);
	
	$this->template->title = $row['title'];
	$this->template->bodyclass .= ' '.strtolower(machineStr($row['title']));
	$this->template->description = $this->texy->process($row['description']);
	$this->template->content = $this->createContentListForItem($row['cat_id'], $values);

  }
  
  /***************************************** HANDLERS ***/
  
  
  /***************************************** FACTORIES ***/
  
  private function createContentList($cat_id, $rows)
  {
	$obj = new AdmCategoriesModel;
	
	$settings = $obj->getCatSettings($cat_id);
	foreach($settings as $key=>$val){
		$$key = $val;
	}
	
	$contDivs = '';
	if($rows){
	  $lastrow = count($rows);
	  $idx = 0;
	  foreach($rows as $cat_item_id => $catitems){
	    $idx++;
	    $show_files = $obj->getShowFiles($cat_item_id);
	    $show_images = $obj->getShowImages($cat_item_id);
		$catLnk = Html::elem('a', array('href'=>BASEPATH.'/categories/frontend/showitem/'.$cat_item_id), $obj->getCatItemTitle($cat_item_id));
		$contDivs .= Html::elem('h2', array('class'=>'title', 'style'=>'float:none;'), $catLnk);
		$itemidx = 0;
		$lastitem = count($catitems);
		foreach($catitems as $row){
			$itemidx++;
		    if($itemidx == $lastitem) $clsLast = ' last-image';
				else $clsLast = '';
			//if image gallery
			if($image_gallery){
				$image = $obj->getFirstImage($row['id']);
				$src = BASEPATH.'/images/thumb_'.$image;
				$imgSize = getImageSize($src);
				$img = Html::elem('img', array('style'=>'border:none', 'src'=>$src, 'width'=>$imgSize[0], 'height'=>$imgSize[1], 'title'=>$row['content_title'], 'alt'=>$row['content_title']));
				$a = Html::elem('a', array('href'=>BASEPATH. '/' . ($row['path_alias'] ? $row['path_alias'] : 'content/show/'.$row['id'])), $img);
				$contDivs .= Html::elem('div', array('class'=>'categories-content-gallery-container'.$clsLast),$a);
			}
			else{
			
				$content_body = $obj->getContentBody($row['content_type_machine_name'], $row['content_id']);
				
				if($show_images){
					if($order_by = $obj->getImagesOrdering($row['content_type_id'])){
						$imgs = $this->createImgGallery($row['id'], $order_by);
					}
					else{
						$imgs = '';
					}
				}
				else{
					$imgs = '';
				}
				
				if($show_files){
					if($order_by = $obj->getFilesOrdering($row['content_type_id'])){
						$files = $this->createFilesTable($row['id'], $order_by);
					}
					else{
						$files = '';
					}
				}
				else{
					$files = '';
				}
			
				if(trim($row['path_alias']) != ''){
					$titleLnk = Html::elem('a', array('href'=>BASEPATH.'/'.$row['path_alias']), $row['content_title']);
				}
				else{
					$titleLnk = Html::elem('a', array('href'=>BASEPATH.'/content/show/'.$row['id']), $row['content_title']);
				}
				
				$titleDiv = Html::elem('h3', array('class'=>'categories-content-title'), $titleLnk);
				if($show_partial){
                                        $cleanString = strip_tags($this->texy->process($content_body));
                                        if(mb_strlen($cleanString, 'utf-8') > $chars_num)
                                            $bodyDiv = Html::elem('div', array('class'=>'categories-content-body'), mb_substr(strip_tags($this->texy->process($content_body)), 0, $chars_num, 'utf-8').'...');
                                        else
                                            $bodyDiv = Html::elem('div', array('class'=>'categories-content-body'), strip_tags($this->texy->process($content_body)));
				}
				else{
					$bodyDiv = Html::elem('div', array('class'=>'categories-content-body'), $this->texy->process($content_body));
				}
				if($show_user){
					$userSpan = Html::elem('span', array('class'=>'categories-content-user'), AUTHOR.': '.$obj->getUserName($row['uid']).'<br/>');
				}
				else{
					$userSpan = '';
				}
				if($show_created){
					$crSpan = Html::elem('span', array('class'=>'categories-content-updated'), slovdate($row['created']).'&nbsp;');
				}
				else{
					$crSpan = '';
				}
				if($show_updated){
					$updSpan = Html::elem('span', array('class'=>'categories-content-updated'), /*LAST_UPDATE.': '.*/slovdate($row['last_update']));
				}
				else{
					$updSpan = '';
				}
				if($userSpan or $crSpan or $updSpan){
					$userDiv = Html::elem('div', array('class'=>'categories-content-user-div'),$userSpan.$crSpan.$updSpan);
				}
				else{
					$userDiv = '';
				}
				
				$contDivs .= Html::elem('div', array('class'=>'categories-content-row'),$titleDiv . $userDiv . $bodyDiv . $imgs . $files);
				if($idx < $lastrow) $contDivs .= Html::elem('div', array('class'=>'categories-spacer'),'&nbsp;');
		    }
		}
		
	  }
	}
	else{
		$contDivs = '';
	}
	
	if($image_gallery) return $contDivs . '<br style="clear:both" />';
		else return $contDivs;
	
  }
  
  private function createContentListForItem($cat_id, $rows)
  {
	  
	  
	  $obj = new AdmCategoriesModel;
	
	$settings = $obj->getCatSettings($cat_id);
	foreach($settings as $key=>$val){
		$$key = $val;
	}
	
	  if($show_pages) {
		$pages = getPages($rows, $items_per_page);
		$pageNr = isset(Vars::get('GET')->pn) ? Vars::get('GET')->pn : 1;
	  
		$rows = $pages[$pageNr];

		$paginator = new Paginator($pageNr, array('limit'=>$paginator_limit, 'pages'=>$pages));
	  }
	  else {
		$paginator = '';
	  }
	
	$contDivs = '';
	if($rows){
		foreach($rows as $row){
			$show_files = $obj->getShowFiles($this->id);
	                $show_images = $obj->getShowImages($this->id);
			//if image gallery
			if($image_gallery){
				$image = $obj->getFirstImage($row['id']);
				if($image){
					$src = BASEPATH.'/images/thumb_'.$image;
					$imgSize = getImageSize($src);
					$img = Html::elem('img', array('style'=>'border:none', 'src'=>$src, 'width'=>$imgSize[0], 'height'=>$imgSize[1],'title'=>$row['content_title'], 'alt'=>$row['content_title']));
					$a = Html::elem('a', array('href'=>BASEPATH.'/'.($row['path_alias'] ? $row['path_alias'] : 'content/show/'.$row['id'])), $img);
					$contDivs .= Html::elem('div', array('class'=>'categories-content-gallery-container'),$a);
				}
				else{
					$contDivs .='';
				}
			}
			else{
			
				$content_body = $obj->getContentBody($row['content_type_machine_name'], $row['content_id']);
				
				if($show_images){
					if($order_by = $obj->getImagesOrdering($row['content_type_id'])){
						$imgs = $this->createImgGallery($row['id'], $order_by);
						$firstImg = '';
					}
					else{
						$imgs = '';
						$firstImg = '';
					}
				}
				else{
					$imgs = '';
					$image = $obj->getFirstImage($row['id']);
					if($image){
						$src = BASEPATH.'/images/thumb_'.$image;
						$imgSize = getImageSize($src);
						$img = Html::elem('img', array('style'=>'border:none', 'src'=>$src, 'width'=>$imgSize[0], 'height'=>$imgSize[1],'title'=>$row['content_title'], 'alt'=>$row['content_title']));
						$a = Html::elem('a', array('href'=>BASEPATH.'/'.($row['path_alias'] ? $row['path_alias'] : 'content/show/'.$row['id'])), $img);
						$firstImg = Html::elem('div', array('class'=>'categories-first-image'),$a);
					}
					else{
						$firstImg ='';
					}
				}
				
				if($show_files){
					if($order_by = $obj->getFilesOrdering($row['content_type_id'])){
						$files = $this->createFilesTable($row['id'], $order_by);
					}
					else{
						$files = '';
					}
				}
				else{
					$files = '';
				}
			
				if(trim($row['path_alias']) != ''){
					$titleLnk = Html::elem('a', array('href'=>BASEPATH.'/'.$row['path_alias']), $row['content_title']);
					$moreLnk = Html::elem('a', array('href'=>BASEPATH.'/'.$row['path_alias']), CAT_READ_MORE.'&nbsp;>>');
				}
				else{
					$titleLnk = Html::elem('a', array('href'=>BASEPATH.'/content/show/'.$row['id']), $row['content_title']);
					$moreLnk = Html::elem('a', array('href'=>BASEPATH.'/content/show/'.$row['id']), CAT_READ_MORE.'&nbsp;>>');
				}
				
				$titleDiv = Html::elem('h3', array('class'=>'categories-content-title'), $titleLnk);
				if($show_partial){
					$bodyDiv = Html::elem('div', array('class'=>'categories-content-body'), substr(strip_tags($this->texy->process($content_body)), 0, $chars_num) .'...&nbsp;&nbsp; '.$moreLnk);
				}
				else{
					$bodyDiv = Html::elem('div', array('class'=>'categories-content-body'), $this->texy->process($content_body));
				}
				if($show_user){
					$userSpan = Html::elem('span', array('class'=>'categories-content-user'), AUTHOR.': '.$obj->getUserName($row['uid']).'<br/>');
				}
				else{
					$userSpan = '';
				}
				if($show_created){
					$crSpan = Html::elem('span', array('class'=>'categories-content-updated'), slovdate($row['created']).'&nbsp;');
				}
				else{
					$crSpan = '';
				}
				if($show_updated){
					$updSpan = Html::elem('span', array('class'=>'categories-content-updated'), slovdate($row['last_update']));
				}
				else{
					$updSpan = '';
				}
				if($userSpan or $crSpan or $updSpan){
					$userDiv = Html::elem('div', array('class'=>'categories-content-user-div'),$userSpan.$crSpan .$updSpan);
				}
				else{
					$userDiv = '';
				}
				
				$contDivs .= Html::elem('div', array('class'=>'categories-content-row'),$titleDiv . $userDiv . $firstImg . $bodyDiv . $imgs . $files);
		    }
	    }
		$contDivs .= $paginator;
	}
	else{
		$contDivs = '';
	}
	
	if($image_gallery) return $contDivs . '<div style="clear:both"></div>';
		else return $contDivs;
	
  }
  
  private function createItemsList($cat_id, $rows)
  {
	// create add link
	$add = Html::elem('a', array('href'=>BASEPATH.'/categories/additem/'.$cat_id, 'class'=>'add-link'), '[ '.ADD_NEW_FEMALE.' ]');
	$addP = Html::elem('p', null, $add);
	if(!Application::$add) $addP = ''; // If have no permision to add reset the link
	if(!$rows) return Html::elem('p', null, $addP . CAT_ITEMS_NO_RECORDS);
	
	$table = new Table('subcategories', $this);
	$table->setClass('admin-table');
	$table->setDataSource($rows);
	$table->setAjax();
	$table->setPaginator(10, 10);
	
	$table->addColumn('title', NAME)
	      ->addOrderShift();
	$table->addActions('id', ACTIONS)->setClass('actions')->setStyle('width:48px;');
	  if(Application::$edit) {
	    $table->addAction(EDIT, 'categories:edititem', Table::WITH_KEY)
	          ->setImage( Html::elem('img')->src(BASEPATH . '/images/icon-edit.jpg')->style('width:20px;height:20px;')->alt(EDIT) );
	  }
	  else {
	    $table->addAction(EDIT, 'categories:edititem', Table::WITH_KEY)
	          ->setDisabled()
	          ->setImage( Html::elem('img')->src(BASEPATH . '/images/icon-edit-gr.jpg')->style('width:20px;height:20px;')->alt(EDIT) );
	  }
	  
	  if(Application::$delete) {
	    $table->addAction(DELETE, 'categories:deleteitem', Table::WITH_KEY)
	          ->setImage( Html::elem('img')->src(BASEPATH . '/images/icon-delete.jpg')->style('width:20px;height:20px;')->alt(DELETE) );
	  }
	  else {
	    $table->addAction(DELETE, 'categories:deleteitem', Table::WITH_KEY)
	          ->setDisabled()
	          ->setImage( Html::elem('img')->src(BASEPATH . '/images/icon-delete-gr.jpg')->style('width:20px;height:20px;')->alt(DELETE) );
	  }
	
	return $addP . $table;
	
  }
  
  
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
		
		$output = Html::elem('ul', array('id'=>'image-gallery-'.$id, 'class'=>'image-gallery'), $lis.'<br class="clearfloat" />');
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