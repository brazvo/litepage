<?php

class Produkty extends Controller
{

  // Properties
  
  // Constructor
  public function __construct()
  {
    
	$obj = new ProductsModel;
	$obj->runSettings();
	$this->perm_mach_name = 'products';
    parent::__construct();
  
  }
  
  // Methods
  //*************************************** ACTIONS
  function actionSavesets()
  {
  
    if(Form::$isvalid){
	  $obj = new ProductsModel;
	  
	  $result = $obj->saveSets($_POST);
	  if($result)
	    redirect('produkty/settings', 'Nastavenia boli uložené.');
	  else{
	    Application::setError('Ukladanie nastavení zlyhalo.');
		$this->render('settings');
	  }
	}
	else{
	  $this->render('settings');
	}
  
  }
  
  function actionSaveNew()
  {
    if(Form::$isvalid){  
	  
      $obj = new ProductsModel;
	  $result = $obj->saveNew($_POST, $_FILES);
	  if($result){
	    $catid = $_POST['category_id'];
		$row = db::fetch("SELECT path FROM menu_items WHERE id=$catid");
	    redirect('produkty/zoznam/'.$row['path'], 'Nový produkt bol uložený.');
	  }
	  else{
	    $this->render('add');
	  }
	}
	else{
	  $this->render('add');
	}
  
  }
  
  function actionSave()
  {
	if(Form::$isvalid){  
	  
      $obj = new ProductsModel;
	  $result = $obj->save($_POST, $_FILES, $_POST['id']);
	  if($result){
	    if(isset($_POST['category_id'])) {$catid = $_POST['category_id'];} else {$catid = $_POST['old_cat_id'];}
		$row = db::fetch("SELECT path FROM menu_items WHERE id=$catid");
	    redirect('produkty/zoznam/'.$row['path'].'#produkt'.$_POST['id'], 'Úprava produktu bola uložená.');
	  }
	  else{
	    Application::$id = $_POST['id'];
		$this->render('edit');
	  }
	}
	else{
	  Application::$id = $_POST['id'];
	  $this->render('edit');
	}
  
  }
  
  function actionDelete($id)
  {
  
    $obj = new ProductsModel;
	
	$result = $obj->delete($id);
	
	if($result){
	   redirect('produkty/zoznam', 'Produkt bol vymazaný');
	}
	else{
	  Application::setError('Pri odstraňovaní produktu došlo k chybe.');
	  $this->render('zoznam');
	}
  
  }
  
  function actionUpdate()
  {
    $rows = db::fetchAll("SELECT * FROM temp_produkty");
	if($rows){
		foreach($rows as $row){
			$image = $row['image'];
			$id = $row['id'];
			db::exec("UPDATE products SET image='$image' WHERE id=$id");
		}
		Application::setMessage('Update OK.');
		$this->render('zoznam');
	}
	else{
		Application::setError('No update.');
		$this->render('zoznam');
	}
  
  }
  
  
  
  //*************************************** RENDERERS
  function renderDefault()
  {
  
    $this->template['title'] = 'Produkty';
	$this->template['content'] = 'Toto je obsah produktov';
  
  }
  
  function renderSettings()
  {
  
    $obj = new ProductsModel;
	
	$values = $obj->findSettings();
	
	$this->template['view'] = 'default';
	$this->template['title'] = 'Administrácia modulu produkty';
	if($values){
	  $this->template['content'] = $this->createAdmProductsForm($values);
	}
	else{
	  Application::setError('Požiadavka sa nedala vykonať');
	  $this->template['content'] = '';
	}
  
  }
  
  function renderZoznam($id=null)
  {
    
	$prd = new ProductsModel;
	
	if($id){
	  $records = $prd->findInCategory($id);
	}
	else{
	  $records = $prd->findAll();
	}
	
    $this->template['titles'] = $records['cat_name'];
	$this->template['products'] = $records['products'];
  
  }
  
  function renderAdd($catid=null)
  {
    $this->template['view'] = 'default';
	$this->template['title'] = 'Nový produkt';
	$this->template['content'] = $this->createAddForm($catid);
  
  }
  
  function renderEdit($id)
  {
	$obj = new ProductsModel;
	
	$result = $obj->getImage($id);
	if($result){
	  $this->template['image'] = $result['image'];
	}
	else{
	  $this->template['image'] = false;
	}
	
	$this->template['title'] = 'Úprava produktu';
	$this->template['content'] = $this->createEditForm($id);
  }
  
  function renderAdmin()
  {
     
  	$this->template['title'] = 'Produkty - administrácia';
  
  }
  
  //*************************************** FACTORIES
  
  private function createAdmProductsForm($values)
  {
    
	$form = new Form('products-settings', 'frm-products-settings', BASEPATH.'/produkty/savesets');
    foreach($values as $row){
	  
	  $record[$row['constant']] = $row;
	
	}
	
	$form->addCheckbox('DISPLAY_PRICE', 1 , $record['DISPLAY_PRICE']['title'], null, $record['DISPLAY_PRICE']['value']);
	$form->addCheckbox('PRICES_FOR_LOGGED_ONLY', 1 , $record['PRICES_FOR_LOGGED_ONLY']['title'], null, $record['PRICES_FOR_LOGGED_ONLY']['value']);
	$form->addCheckbox('VAT_PAYER', 1 , $record['VAT_PAYER']['title'], null, $record['VAT_PAYER']['value']);
	$form->addCheckbox('PRICES_WITH_VAT', 1 , $record['PRICES_WITH_VAT']['title'], null, $record['PRICES_WITH_VAT']['value']);
	list($lowvat, $highvat) = explode(':', $record['VATS']['value']);
	$form->addText('lowvat', 'Znížená sadzba DPH', $lowvat, 2,2);
	$form->addText('highvat', 'Normálna sadzba DPH', $highvat, 2,2);
	$form->emptyLine();
	
	$form->addText('IMAGE_SIZE', $record['IMAGE_SIZE']['title'], $record['IMAGE_SIZE']['value'], 10,10);
		$form->addRule('IMAGE_SIZE', Form::FILLED, 'Veľkosť obrázku musí byť zadaná.');
	$form->addText('THUMB_SIZE', $record['THUMB_SIZE']['title'], $record['THUMB_SIZE']['value'], 10,10);
		$form->addRule('THUMB_SIZE', Form::FILLED, 'Veľkosť ikony musí byť zadaná.');
	$form->addSelect('THUMB_CREATE', array('cut' => 'Zmenšiť a orezať', 'ratio'=>'Zmenšiť a dodržať pomer strán'), $record['THUMB_CREATE']['title'], $record['THUMB_CREATE']['value']);
	$form->emptyLine();
	$form->addSelect('ORDER_BY', array('name'=>'Podľa názvu produktov od A po Z', 'priority'=>'Podľa priority (manuálne nastaviteľné)'), $record['ORDER_BY']['title'],$record['ORDER_BY']['value']);
	$form->emptyLine();
	$form->addSubmit('save', 'Uložiť');
	
    $output =  $form->render();
	$output .= '<script type="text/javascript">
                    /* <![CDATA[ */
                    if($("#formProducts-settings-DISPLAY_PRICE").is(":checked")){
					   $("#formProducts-settings-PRICES_FOR_LOGGED_ONLY").removeAttr("disabled");
					   $("#formProducts-settings-VAT_PAYER").removeAttr("disabled");
					   $("#formProducts-settings-PRICES_WITH_VAT").removeAttr("disabled");
					   if($("#formProducts-settings-VAT_PAYER").is(":checked")){
						 $("#formProducts-settings-PRICES_WITH_VAT").removeAttr("disabled");
						 $("#formProducts-settings-lowvat input").removeAttr("disabled");
						 $("#formProducts-settings-highvat input").removeAttr("disabled");
					   }
					   else{
						 $("#formProducts-settings-PRICES_WITH_VAT").attr("disabled", "disabled");
						 $("#formProducts-settings-lowvat input").attr("disabled", "disabled");
						 $("#formProducts-settings-highvat input").attr("disabled", "disabled");
					   }
					 }
					 else{
					   $("#formProducts-settings-PRICES_FOR_LOGGED_ONLY").attr("disabled", "disabled");
					   $("#formProducts-settings-VAT_PAYER").attr("disabled", "disabled");
					   $("#formProducts-settings-PRICES_WITH_VAT").attr("disabled", "disabled");
					   $("#formProducts-settings-lowvat input").attr("disabled", "disabled");
					   $("#formProducts-settings-highvat input").attr("disabled", "disabled");
					 }
				   
				   $("#formProducts-settings-DISPLAY_PRICE").click(function(){
					 if($(this).is(":checked")){
						 $("#formProducts-settings-PRICES_FOR_LOGGED_ONLY").removeAttr("disabled");
						 $("#formProducts-settings-VAT_PAYER").removeAttr("disabled");
						 $("#formProducts-settings-PRICES_WITH_VAT").removeAttr("disabled");
					   if($("#formProducts-settings-VAT_PAYER").is(":checked")){
						 $("#formProducts-settings-PRICES_WITH_VAT").removeAttr("disabled");
						 $("#formProducts-settings-lowvat input").removeAttr("disabled");
						 $("#formProducts-settings-highvat input").removeAttr("disabled");
					   }
					   else{
						 $("#formProducts-settings-PRICES_WITH_VAT").attr("disabled", "disabled");
						 $("#formProducts-settings-lowvat input").attr("disabled", "disabled");
						 $("#formProducts-settings-highvat input").attr("disabled", "disabled");
					   }
					 }
					 else{
					   $("#formProducts-settings-PRICES_FOR_LOGGED_ONLY").attr("disabled", "disabled");
					   $("#formProducts-settings-VAT_PAYER").attr("disabled", "disabled");
					   $("#formProducts-settings-PRICES_WITH_VAT").attr("disabled", "disabled");
					   $("#formProducts-settings-lowvat input").attr("disabled", "disabled");
					   $("#formProducts-settings-highvat input").attr("disabled", "disabled");
					 }
				   });
				   
				   $("#formProducts-settings-VAT_PAYER").click(function(){
					 if($(this).is(":checked")){
					   $("#formProducts-settings-PRICES_WITH_VAT").removeAttr("disabled");
					   $("#formProducts-settings-lowvat input").removeAttr("disabled");
					   $("#formProducts-settings-highvat input").removeAttr("disabled");
					 }
					 else{
					   $("#formProducts-settings-PRICES_WITH_VAT").attr("disabled", "disabled");
					   $("#formProducts-settings-lowvat input").attr("disabled", "disabled");
					   $("#formProducts-settings-highvat input").attr("disabled", "disabled");
					 }
				   });
                    /* ]]> */
                    </script>';
	return $output;
  }
  
  private function createAddForm($catid)
  {
  
    // get taxes
    $res = explode(':', VATS);
	foreach($res as $tax){
	  $taxes[$tax] = $tax;
	}
	
	$form = new Form('add-product', 'frm-add-product', BASEPATH.'/produkty/saveNew');
	
    if($_POST){
	  $values = $_POST;
	  if(!isset($values['new'])) $values['new'] = 0;
	  if(!isset($values['inaction'])) $values['inaction'] = 0;
	}
	else{
	  $values = $form->getZeroValues('products');
	}
	
	foreach($values as $key => $val){
	  $$key = $val;
	}
	if($catid){
	  $form->addHidden('category_id', $catid);
	}
	else{
	  $obj = new ProductsModel;
	  $cats = $obj->getCatsStructure();
	  $cats_arr = $this->createSelectArray($cats);
	  $form->addSelect('category_id', $cats_arr, 'Vyberte kategóriu', $category_id);
	}
	$form->addText('name', 'Názov', $name);
		$form->addRule('name', Form::FILLED,'Zadajte názov produktu.');
	$form->addTextarea('description', 'Popis', $description);
	$form->addTextarea('attributes', 'Atribúty (rozmer, váha, farba)', $attributes);
	$form->addCheckbox('new', 1, 'Novinka', null, $new);
	
	$form->emptyLine();
	if(DISPLAY_PRICE){
	  IF(PRICES_WITH_VAT){
	    $with = 's DPH';
      }
      ELSE{
	    $with = 'bez DPH';
	  }
	  if(!VAT_PAYER) $with = '';
	  $form->addText('price', "Cena $with", $price, 10, 10);
	  if(VAT_PAYER){
	    $form->addSelect('vat', $taxes, 'Sadzba DPH (%)', $vat);
	  }
	  $form->addCheckbox('inaction', 1, 'V akcii?', null, $inaction);
	  $form->addText('discount', 'Zľava (%)', $discount, 2,4);
	  $form->emptyLine();
	}
	$form->addFile('image', 'Obrázok GIF alebo JPG');
	$form->emptyLine();
	$form->addSubmit('save', 'Uložiť');
	
	return $form->render();
  
  }
  
  private function createEditForm($id)
  {
  
    // get taxes
    $res = explode(':', VATS);
	foreach($res as $tax){
	  $taxes[$tax] = $tax;
	}
	
	$form = new Form('add-product', 'frm-add-product', BASEPATH.'/produkty/save');
	
    if($_POST){
	  $values = $_POST;
	  if(!isset($values['new'])) $values['new'] = 0;
	  if(!isset($values['inaction'])) $values['inaction'] = 0;
	}
	else{
	  $obj = new ProductsModel;
	  $values = $obj->getValues($id);
	  $oldpriority = $values['priority'];
	}
	
	foreach($values as $key => $val){
	  $$key = $val;
	}
	
	$prioritySelectVals = $obj->getPrioritySelectArray($category_id, $oldpriority);
	
	$cats = $obj->getCatsStructure();
	$cats_arr = $this->createSelectArray($cats);
	isset($old_cat_id) ? $old_cat_id = $old_cat_id : $old_cat_id = $category_id;
	$form->addHidden('old_cat_id', $old_cat_id);
	$form->addSelect('category_id', $cats_arr, 'Vyberte kategóriu', $category_id);
		$form->addDescription('category_id', '<span style="color:red"><b>Pri zmene Kategórie sa nesmie meniť Poradie!!</b></span>');
	$form->addHidden('oldpriority', $oldpriority);
	if($prioritySelectVals){
		$form->addSelect('priority', $prioritySelectVals, 'Poradie', $priority);
		$form->addDescription('priority', 'Zvoľte položku, pred ktorú chcete umiestniť tento produkt. Ak ponecháte nezmenené, zoradenie sa nezmení.
		                                   <span style="color:red"><b>Pri zmene poradia sa nesmie meniť Kategória!!</b></span>');
	}
	else{
		$form->addHidden('priority', $priority);
	}
	$form->emptyLine();
	$form->addHidden('id', $id);
	$form->addText('name', 'Názov', $name);
		$form->addRule('name', Form::FILLED,'Zadajte názov produktu.');
	$form->addTextarea('description', 'Popis', $description);
	$form->addTextarea('attributes', 'Atribúty (rozmer, váha, farba)', $attributes);
	$form->addCheckbox('new', 1, 'Novinka', null, $new);
	
	$form->emptyLine();
	if(DISPLAY_PRICE){
	  IF(PRICES_WITH_VAT){
	    $with = 's DPH';
      }
      ELSE{
	    $with = 'bez DPH';
	  }
	  if(!VAT_PAYER) $with = '';
	  $form->addText('price', "Cena $with", $price, 10, 10);
	  if(VAT_PAYER){
	    $form->addSelect('vat', $taxes, 'Sadzba DPH (%)', $vat);
	  }
	  $form->addCheckbox('inaction', 1, 'V akcii?', null, $inaction);
	  $form->addText('discount', 'Zľava (%)', $discount, 2,4);
	  $form->emptyLine();
	}
	if($image){
	  $form->addCheckbox('deleteimg', 1, 'Vymazať obrázok');
	  $change = 'Zmeniť';
	}
	else{
	  $change = 'Nahrať';
	}
	
	isset($oldimg) ? $oldimg = $oldimg : $oldimg = $image;
	
	$form->addHidden('oldimg', $oldimg);
	$form->addFile('image', $change.' obrázok GIF alebo JPG');
	$form->emptyLine();
	$form->addSubmit('save', 'Uložiť');
	
	$output =  $form->render();
	$output .= '<script type="text/javascript">
                    /* <![CDATA[ */
				   $("#formAdd-product-deleteimg").click(function(){
					 if($(this).is(":checked")){
					   $("#formAdd-product-image").attr("disabled", "disabled");
					 }
					 else{
					   $("#formAdd-product-image").removeAttr("disabled");
					 }
				   });
				   $("#formAdd-product-priority").change(function() {
					 $("#formAdd-product-category_id").attr("disabled", "disabled");
					 alert("Pri zmene poradia sa nesmie meniť kategória");
				   });
				   $("#formAdd-product-category_id").change(function() {
					 $("#formAdd-product-priority").attr("disabled", "disabled");
					 alert("Pri zmene kategórie sa nesmie meniť poradie");
				   });
                    /* ]]> */
                    </script>';
	return $output;
  
  }
  
  private function createSelectArray($values, $subline='')
  {
    $subline = $subline.'-'; 
    foreach($values as $val){
	  $out[$val['row']['id']] = '<'.$subline.' '.$val['row']['title'].' ->';
	  if(isset($val['submenu'])) $out += $this->createSelectArray($val['submenu'], $subline);
	}
	
	return $out;
  
  }

}