<?php

class ProductsModel extends BaseModel
{
  // Properties
    /**
	 * @var string
	 * gallery dir
	 */
	private $dir;
	
	/**
	 * @var int
	 * default image size
	 */
	private $defImgSize;
	
	/**
	 * @var int
	 * default icon size
	 */
	private $defIconSize;
  
  // Constructor
  public function __construct()
  {
	
	parent::__construct();
  
  }
  
  // Methods
  public function runSettings()
  {
  
    $rows = db::fetchAll("SELECT * FROM products_settings");
	
	foreach($rows as $row){
	  define($row['constant'], $row['value']);
	}
  
  }
  
  public function findSettings()
  {
  
    $rows = db::fetchAll("SELECT * FROM products_settings ORDER BY id");
	
	return $rows;
  
  }
  
  public function findInCategory($path)
  {
	// GET CATEGORY ID
	$query = $this->db->prepare("SELECT * FROM menu_items WHERE path='$path'");
	$query->execute();
	
	$record = $query->fetch(PDO::FETCH_ASSOC);
	
	$cat_id = $record['id'];
	$result['cat_name'][$cat_id] = $record['name'];
	
	// GET PRODUCTS IN CATEGORY
	$query = $this->db->prepare("SELECT * FROM products WHERE category_id='$cat_id' ORDER BY priority");
	$query->execute();
	
	$result['products'][$cat_id] = $query->fetchAll(PDO::FETCH_ASSOC);
	
	
	return $result;
  
  }
  
  public function findAll()
  {
	$result = db::fetch("SELECT id FROM menus WHERE machine_name='produkty'");
	$menu_id = $result['id'];
	
	// GET CATEGORY ID
	$records = db::fetchAll("SELECT * FROM menu_items WHERE menu_id=$menu_id ORDER BY id");
	
	if($records){		
		foreach($records as $record){
		
		  $cat_id = $record['id'];
		  $result['cat_name'][$cat_id] = $record['name'];
		
		  // GET PRODUCTS IN CATEGORY
		  $result['products'][$cat_id] = db::fetchAll("SELECT * FROM products WHERE category_id='$cat_id' ORDER BY priority");
		  
		}
		
		return $result;
    }
	else{
	    $result['cat_name'] = null;
		$result['products'] = null;
		return $result;
	}
  }
  
  public function saveNew($post, $files)
  {
    $this->dir =  WWW_DIR.'/images/products/';
	$this->defImgSize = IMAGE_SIZE;
	$this->defIconSize = THUMB_SIZE;
	
	if(isset($files['image'])){
		$image = $files['image'];
		
		// IE work-arround for JPEG image
		if($image['type'] == 'image/pjpeg'){
			$image['type'] = 'image/jpeg';
		}
		
		if($image['name'] != ''){
		  if($image['type'] == 'image/gif' or $image['type'] == 'image/jpeg'){
			$image = $this->upload($image);
			if(!$image){
				Application::setError('Obrazok sa nepodarilo nahrať.');
				return false;
			}
		  }
		  else {
			Application::setError('Súbor obrázku musí byť JPG alebo GIF.');
			return false;
		  }
		}
		else{
		  $image = '';
		}
    }
	
	$row = db::fetch("SELECT MAX(id) AS id FROM products");
	
	$newid = $row['id']+1;
	
	db::exec("INSERT INTO products (id, image, priority) VALUES ($newid, '$image', $newid)");
	
	foreach($post as $key => $value){
	  if($key == 'save' or $key == 'validate'){
	    // DO NOTHING
	  }
	  else{
	    db::exec("UPDATE products SET `$key`='$value' WHERE id=$newid");
	  }
	}
	
	return true;
	
  }
  
  
  public function save($post, $files, $id)
  {
    $this->dir =  WWW_DIR.'/images/products/';
	$this->defImgSize = IMAGE_SIZE;
	$this->defIconSize = THUMB_SIZE;
	//set old image
	$oldimg = $post['oldimg'];
	
	if(isset($files['image'])){
		$image = $files['image'];
		
		// IE work-arround for JPEG image
		if($image['type'] == 'image/pjpeg'){
			$image['type'] = 'image/jpeg';
		}
		
		if($image['name'] != ''){
		  if($image['type'] == 'image/gif' or $image['type'] == 'image/jpeg'){
			$image = $this->upload($image);
			if(!$image){
				Application::setError('Obrazok sa nepodarilo nahrať.');
				return false;
			}
		  }
		  else {
			Application::setError('Súbor obrázku musí byť JPG alebo GIF.');
			return false;
		  }
		}
		else{
		  $image = false;
		}
    }
	
	if($image){
	  $post['image'] = $image;
	  $post['deleteimg'] = 1;
	}
	if(isset($post['deleteimg'])){
	  if(!$image){
	    $post['image'] = '';
	  }
	  unlink($this->dir.$oldimg);
	  unlink($this->dir.'thumb_'.$oldimg);
	}
	
	if(!isset($post['new'])) $post['new'] = 0;
	if(!isset($post['inaction'])) $post['inaction'] = 0;
	
	$oldpriority = $post['oldpriority'];
	if(!isset($post['priority'])) $newpriority = $oldpriority;
		else $newpriority = $post['priority'];
	//unset unusable indexes
	unset($post['save']); unset($post['validate']); unset($post['deleteimg']); unset($post['oldimg']);
	unset($post['oldpriority']); unset($post['priority']); unset($post['old_cat_id']);

	foreach($post as $key => $value){
	    db::exec("UPDATE products SET `$key`='$value' WHERE id=$id");
	}
	
	// change order if new priority is set
	if($oldpriority < $newpriority){
		$newpriority = $newpriority-1;
		db::exec("UPDATE products SET priority=$newpriority WHERE id=$id");
		// order lower priorities
		$rows = db::fetchAll("SELECT id, priority FROM products WHERE priority <= $newpriority AND id !=$id ORDER BY priority");
		$last = count($rows);
		$idx = 1;
		foreach($rows as $row){
			$id = $row['id'];
			db::exec("UPDATE products SET priority=$idx WHERE id=$id");
			$idx++;
		}
	}
	// change order if new priority is set
	if($oldpriority > $newpriority){
		db::exec("UPDATE products SET priority=$newpriority WHERE id=$id");
		// order lower priorities
		$rows = db::fetchAll("SELECT id, priority FROM products WHERE priority >= $newpriority AND id !=$id ORDER BY priority");
		$last = count($rows);
		$idx = $newpriority+1;
		foreach($rows as $row){
			$id = $row['id'];
			db::exec("UPDATE products SET priority=$idx WHERE id=$id");
			$idx++;
		}
	}
	
	return true;
	
  }
  
  public function saveSets($post)
  {
    // unset validate index
    unset($post['validate']);
    // set default values for chcekboxes and disabled items
	// if they will not be set in POST these will be saved to table
	$values[] = array('constant' => 'DISPLAY_PRICE', 'value' => 0);
	$values[] = array('constant' => 'PRICES_WITH_VAT', 'value' => 0);
	$values[] = array('constant' => 'VAT_PAYER', 'value' => 0);
	$values[] = array('constant' => 'PRICES_FOR_LOGGED_ONLY', 'value' => 0);
	
	// check if is set low and high vat 
	if(isset($post['lowvat']) && isset($post['highvat'])){
	  $values[] = array('constant' => 'VATS', 'value' => $post['lowvat'].':'.$post['highvat']);
	}
	
	// and set the rest from POST
	foreach($post as $key => $val){
	  $values[] = array('constant' => $key, 'value' => $val);
	}
	
	//Update table values
	foreach($values as $val){
	  $constant = $val['constant']; $value = $val['value'];
	  $result = db::exec("UPDATE products_settings SET value='$value' WHERE constant='$constant'");
	}
	
	return $result;
  
  }
  
  public function delete($id)
  {
  
    db::exec("CREATE VIEW IF NOT EXISTS products_view AS SELECT id, image FROM products");
	
	$row = db::fetch("SELECT image FROM products_view WHERE id=$id");
	$imagename = $row['image'];
	db::exec("DROP VIEW products_view");

	$result=db::exec("DELETE FROM products WHERE id=$id");

	if(@is_file(WWW_DIR.'/images/products/'.$imagename)){
	  unlink(WWW_DIR.'/images/products/'.$imagename);
	  unlink(WWW_DIR.'/images/products/thumb_'.$imagename);
	}
	
	return $result;
  
  }
  
  public function getImage($id)
  {
    return db::fetch("SELECT image FROM products WHERE id=$id");
  }
  
  public function getValues($id)
  {
    return db::fetch("SELECT * FROM products WHERE id=$id");
  }
  
  public function getCatsStructure($child_of_id=0)
  {
  
    $row = db::fetch("SELECT id FROM menus WHERE machine_name='produkty'");
	$menu_id = $row['id'];
	
	$result = db::fetchAll("SELECT * FROM menu_items WHERE child_of=$child_of_id AND menu_id=$menu_id ORDER BY priority");
	
	if(count($result) > 0){
	  foreach($result as $row){
	    $out[$row['id']]['row'] = $row;
		$nextsub = $this->getCatsStructure($row['id']);
		if($nextsub){
		  $out[$row['id']]['submenu'] = $nextsub;
		}
	  }
	}
	if(isset($out)){	
	  return $out;
	}
	else{
	  return false;
	}
  
  }

    /**
	 * @title Upload
	 * uploads visitor's images
	 */
	public function upload($image)
	{
		
		$return = FALSE;
		
		$uploadfile = $this->dir . $image['name'];
		$tempfile = $image['tmp_name'];
		$filename = $image['name'];
		$type = $image['type'];
		
		$UniqFilename = UniqID(Date("U"));
		$imagename = $UniqFilename . ".jpg";
		$iconame = "thumb_" . $UniqFilename . ".jpg";
		  
		if($type == "image/jpeg"){
			$uploadfile = $this->dir . "tempimage.jpg";
		}
		elseif($type == "image/gif"){
			$uploadfile = $this->dir . "tempimage.gif";
		}
		
		$file = $this->cleanFileName($uploadfile);
		umask(0000);
		if($uplStatus = @move_uploaded_file($tempfile, $file)) {
		    chmod($file, 0666);    
		}
		
		// If upload OK
		if($uplStatus){
		
			// Add path to pictures
			$picture = $this->dir.$imagename;
			$icon = $this->dir.$iconame;
				 
				 
			/* We load a small GIf file to be expanded into a larger file and
			 calculate the width and height of the image */
			list($width, $height, $imgtype) = GetImageSize($file);
			if($width >= $height){
				if(THUMB_CREATE == 'cut'){
				  $inputIconWidth = $height;
				  $inputIconHeight = $height;
				  $input_x = ($width/2) - ($height/2);
				  $input_y = 0;
				  $ico_width = $this->defIconSize;
				  $ico_height = $this->defIconSize;
				}
				if(THUMB_CREATE == 'ratio'){
				  $inputIconWidth = $width;
				  $inputIconHeight = $height;
				  $input_x = 0;
				  $input_y = 0;
				  $ico_width = $this->defIconSize;
				  $ico_height = round($height * ($ico_width / $width));
				}
			 	$img_width = $this->defImgSize;
				$img_height = round($height * ($img_width / $width));
			}
			if($width < $height){
				if(THUMB_CREATE == 'cut'){
				  $inputIconWidth = $width;
				  $inputIconHeight = $width;
				  $input_x = 0;
				  $input_y = ($height/2) - ($width/2);
				  $ico_width = $this->defIconSize;
				  $ico_height = $this->defIconSize;
				}
				if(THUMB_CREATE == 'ratio'){
				  $inputIconWidth = $width;
				  $inputIconHeight = $height;
				  $input_x = 0;
				  $input_y = 0;
				  $ico_height = $this->defIconSize;
				  $ico_width = round($width * ($ico_height / $height));
				}
			 	$img_height = $this->defImgSize;
				$img_width = round($width * ($img_height / $height));
			}
				 
			if($type == "image/jpeg"){
			 	$imgInput = ImageCreateFromJpeg($file);
			}
			if($type == "image/gif"){
			 	$imgInput = ImageCreateFromGif($file);
			}
				 
			/* Set output size of the picture and icon */
			$imgOutputIcon = ImageCreateTrueColor($ico_width, $ico_height);
			$imgOutputImage = ImageCreateTrueColor($img_width, $img_height);
				 
			ImageCopyResampled($imgOutputIcon, $imgInput, 0, 0, $input_x, $input_y, $ico_width, $ico_height, $inputIconWidth, $inputIconHeight);	 	
			ImageCopyResampled($imgOutputImage, $imgInput, 0, 0, 0, 0, $img_width, $img_height, $width, $height);
			ImageJpeg($imgOutputIcon, $icon, 100);
			ImageJpeg($imgOutputImage, $picture, 95);
				      	 
			ImageDestroy($imgInput);
			ImageDestroy($imgOutputIcon);
			ImageDestroy($imgOutputImage);
				 
			unlink($file);
				 
			$return = $imagename;
			
		
		}
		else {
		    Application::setError('Súbor sa nepodarilo odoslat!');
		}
		
		return $return;
		
	}

    /**
	 * @title Clean File Name
	 * removes spaces from file name
	 */
	private function cleanFileName($uploading_file)
	{
		$file = ereg_replace(" ", "_", $uploading_file);
		$file = ereg_replace("%20", "_", $uploading_file);
		
		return $file;
	}
	
	public function getPrioritySelectArray($category_id, $oldpriority)
	{
		$rows = db::fetchAll("SELECT * FROM products WHERE category_id=$category_id ORDER BY priority");
		if(!$rows) return false;
		
		foreach($rows as $row){
			if($row['priority'] != ($oldpriority+1))$return[$row['priority']] = $row['name'];
			$lastpriority = $row['priority'];
		}
		$return[$lastpriority+1] = '[ Zaradiť na koniec ]';
		return $return;
	
	}

}