<?php

class ContentTypes extends BaseAdmin
{

  // Properties
  
  // Constructor
  public function __construct()
  {
    $this->perm_mach_name = 'content_types';  
    parent::__construct();
  }
  
  // Methods
  function actionValidate()
  {
  
    $form = new Form;
	$result = $form->frmValidate($_POST);
	
	if($result === TRUE){
	// DO IF VALID
	  $obj = new AdmContentTypesModel;
	  
	  $result = $obj->saveNewContentType($_POST);
	  
	   if($result){
	     redirect('admin/contentTypes', 'Typ obsahu bol uložený.');
	   }
	   else{
	     $this->template->setView('add');
	   }
	
	}
	else{
	  
	  Application::setError($form->render());
	  
	  $this->template->setView('add');
	  
	}
  
  }
  
  function actionValedit()
  {
  
    $form = new Form;
	$result = $form->frmValidate($_POST);
	
	if($result === TRUE){
	// DO IF VALID
	  $obj = new AdmContentTypesModel;
	  
	  $result = $obj->saveContentType($_POST);
	  
	   if($result){
	     redirect('admin/contentTypes', 'Zmeny boli uložené.');
	   }
	   else{
	     $this->template->setView('edit');
	   }
	
	}
	else{
	  
	  Application::setError($form->render());
	  
	  $this->template->setView('edit');
	  
	}
  
  }
  
  function actionOrder()
  {
  
    $obj = new AdmContentTypesModel;
	$result = $obj->orderFields($_POST);
	
	if($result){
	  redirect('admin/contentTypes/fields/'.$_POST['contid'], 'Poradie bolo zmenené.');
	}
	else{
	  Application::setError('Ukladanie poradia zlyhalo');
	  $this->id = $_POST['contid'];
	  $this->template->setView('fields');
	}
  
  }
  
  function actionAddField()
  {
  
    if(Form::$isvalid){
	
	  $obj = new AdmContentTypesModel;
	  
	  $result = $obj->saveField($this->id, $_POST);
	  
	  if($result){
	    redirect('admin/contentTypes/fields/'.$this->id, 'Nové pole bolo pridané.');
	  }
	  else{
	    Application::setError('Pri ukladaní došlo k chybe.');
		$this->template->setView('fields');
	  }
	
	}
	else{
	
	  $this->template->setView('fields');
	
	}
  
  }
  
  function actionSavefield()
  {
  
    if(Form::$isvalid){
	
	  $obj = new AdmContentTypesModel;
	  
	  $result = $obj->updateField($_POST);
	  
	  if($result)
	    redirect($_SESSION['pathRequest'], 'Zmeny boli ulozené');
	  else{
	    Application::setError('Pri ukladaní došlo k chybe.');
		$this->id = $_POST['id'];
		$this->setRender('editfield');
	  }
	
	}
	else{
	  $this->id = $_POST['id'];
	  $this->setRender('editfield');
	}
  
  }
  
  
  function actionDeletefield()
  {
  
    $obj = new AdmContentTypesModel;
	
	$result = $obj->deleteField($this->id);
	
	if($result == 'delImgs'){
	  redirect('admin/contentTypes/deleteImagesFromTable/'.$this->id);
	}
	elseif($result == 'delFiles'){
	  redirect('admin/contentTypes/deleteFilesFromTable/'.$this->id);
	}
	elseif($result == 'delField'){
	  redirect('admin/contentTypes/deleteCTField/'.$this->id);
	}
    else{
	  redirect($_SESSION['pathRequest'], 'Vymazanie zlyhalo');
	}
  }
  
  function actionDeleteCTField()
  {
  
    $obj = new AdmContentTypesModel;
	
	$result = $obj->deleteCTField($this->id);
	
	if($result){
	  redirect($_SESSION['pathRequest'], 'Pole bolo vymazané');
	}
    else{
	  redirect($_SESSION['pathRequest'], 'Vymazanie zlyhalo');
	}
  }
  
  function actionDeleteImagesFromTable()
  {
  
    $obj = new AdmContentTypesModel;
	
	$result = $obj->deleteImagesFromTable($this->id);
	
	if($result){
	  redirect($_SESSION['pathRequest'], 'Pole bolo vymazané');
	}
    else{
	  redirect($_SESSION['pathRequest'], 'Vymazanie zlyhalo');
	}
  }
  
  function actionDeleteFilesFromTable()
  {
  
    $obj = new AdmContentTypesModel;
	
	$result = $obj->deleteFilesFromTable($this->id);
	
	if($result){
	  redirect($_SESSION['pathRequest'], 'Pole bolo vymazané');
	}
    else{
	  redirect($_SESSION['pathRequest'], 'Vymazanie zlyhalo');
	}
  }
  
  function actionDeleteConfirm()
  {
  
    $obj = new AdmContentTypesModel;
	
	$result = $obj->deleteContentType($_POST);
	
	if($result){
	  redirect('admin/contentTypes', DELETED);
	}
    else{
	  redirect('admin/contentTypes', DELETE_FAILED);
	}
  }
  
  function renderDelete($id)
  {
	$this->template->setView('add');
	$this->template->title = 'Vymazať typ obsahu';
	$this->template->addform = 'Chystáte sa vymazať typ obsahu. Touto operáciou dôjde aj k výmazu vytvoreného obsahu. Súbory a obrázky pripojené k obsahom nebudú vymazané, preto je vhodné, aby ste pred výmazom typu obsahu najskôr vymazali samotný obsah. Ak máte nainštalovaný modul Kategórie, je tiež nutné zrušiť všetky vzťahy v Kategóriach.'.$this->createDeleteForm($id);
  }
  
  
  function renderDefault()
  {
    // Set last path request
	$_SESSION['pathRequest'] = $_REQUEST['q'];
	
	$items = new AdmContentTypesModel;
	
	$result = $items->findContentTypes();
	
        $this->template->title = 'Typy obsahu';
	$this->template->content = $this->createListTable($result);
  
  }
  
  function renderAdd()
  {
    
	$this->template->title = 'Nový typ obsahu';
	$this->template->addform = $this->createAddForm($_POST);
  
  }
  
  function renderEdit()
  {
    if($_POST){
	  $values = $_POST;
	}
	else{
	  $obj = new AdmContentTypesModel;
	  $values = $obj->findOne($this->id);
	}
	
	$this->template->title = 'Úprava typu obsahu';
	$this->template->editform = $this->createEditForm($values);
  
  }
  
  function renderFields()
  {
    // Set last path request
	$_SESSION['pathRequest'] = $_REQUEST['q'];
	
	$obj = new AdmContentTypesModel;
	$type = $obj->findOne($this->id);
	$table = $obj->findAllFields($this->id);
		
	$this->template->title = 'Polia typu obsahu '.$type['name'];
	$this->template->id = $this->id;
	$this->template->table = $table;
	$this->template->form = $this->createAddFieldForm();
  
  }
  
  function renderEditfield()
  {
  
    $obj = new AdmContentTypesModel;
	$result = $obj->findField($this->id);
	
	$this->template->title = 'Editácia poľa '.$result['label'];
	$this->template->form = $this->createEditFieldForm($result);
  
  }
  
  private function createAddForm($values)
  {
  
    if($values){
	  foreach($values as $key => $value){
	    $$key = $value;
	  }
	}
	else{
	  $name = ''; $machine_name = ''; $description = '';
	}
	
	$form = new Form('ctAddForm', 'ct-add-form', BASEPATH.'/admin/contentTypes/validate');
	
	$form->addText('name', 'Názov', $name);
	  $form->addRule('name', Form::FILLED, 'Zadajte Názov.');
	$form->addText('machine_name', 'Strojový názov', $machine_name);
	  $form->addRule('machine_name', Form::FILLED, 'Zadajte Strojový názov.');
	  $form->addDescription('machine_name', '(Nesmie obsahovat diakritiku a nedovolene znaky: medzera + - \ / a pod. Správny príklad: <b><i>strojovy_nazov</i></b>.)');
	$form->addTextarea('description', 'Popis', $description, 'frm-textarea', 50, 6);
	
	$form->addSubmit('submit', 'Uložiť');
	
	return $form->render();
  
  }
  
  
  private function createEditForm($values)
  {
  
    foreach($values as $key => $value){
	 $$key = $value;
	}
		
	$form = new Form('ctEditForm', 'ct-edit-form', BASEPATH.'/admin/contentTypes/valedit');
	
	$form->addHidden('id', $id);
	$form->addText('name', 'Názov', $name);
	  $form->addRule('name', Form::FILLED, 'Zadajte Názov.');
	$form->addTextarea('description', 'Popis', $description, 'frm-textarea', 50, 6);
	
	$form->addSubmit('submit', 'Uložiť');
	
	return $form->render();
  
  }
  
  private function createAddFieldForm()
  {
    //set default values
	$label = ''; $frm_name = ''; $fieldtype = '';
	if($_POST){
	  foreach($_POST as $key => $value){
	    $$key = $value;
	  }
	}
	
	$form = new Form('newField', 'new-field-frm', BASEPATH.'/admin/contentTypes/addField/'.$this->id);
	
	$obj = new AdmContentTypesModel;
	$fieldtypes = $obj->getFieldTypes();
	
	$form->addHidden('content_type_id', $this->id);
	$form->addText('label', 'Titulok', $label);
	  $form->addRule('label', Form::FILLED, 'Zadajte Titulok');
	$form->addSelect('fieldtype', $fieldtypes, 'Vyberte typ poľa', $fieldtype);
	$form->addText('frm_name', 'Meno atribútu name vo formuláry', $frm_name);
	  $form->addRule('frm_name', Form::FILLED, 'Zadajte Meno atribútu name');
	  $form->addRule('frm_name', Form::REGEX, '/^[a-z_]+$/', 'Meno atribútu Name môže obsahovať iba malé písmená bez diakritiky a podtržník.');
	  $form->addDescription('frm_name', 'Strojové meno. Môže obsahovať len malé písmená bez diakritiky a podtržník');
	$form->addSubmit('save', 'Pridať nové pole');
	
    return $form->render();
  
  }
  
  private function createEditFieldForm($values)
  {
    $id = $values['id'];
	$fld_type = $values['type'];
	$mach_fld_type = $values['machine_field_type'];

	if(!empty ($values['attributes']) ){
	  $exp_attrs = @unserialize($values['attributes']);
	  if(is_array($exp_attrs)) {
	    foreach($exp_attrs as $idx => $val){
	      ${"attr_$idx"} = $val;
	    }
	  }
	  // Backward compatibility
	  else {
	    $exp_attrs = explode(';', $values['attributes']);
	    foreach($exp_attrs as $attr){
	      list($idx, $val) = explode(':', $attr);
		  ${"attr_$idx"} = $val;
	    }
	  }
	  
	  if(!empty($exp_attrs)) $render_attrs = true;
	  else $render_attrs = FALSE;
	  
	  if($mach_fld_type == 'text'){
	    $attrs_for = 'text';
	  }
	  elseif($mach_fld_type == 'textarea'){
	    $attrs_for = 'textarea';
	  }
	  elseif($mach_fld_type == 'file'){
	    $attrs_for = 'file';
	  }
	  elseif($mach_fld_type == 'image'){
	    $attrs_for = 'image';
	  }
	  elseif($mach_fld_type == 'datetime'){
	    $attrs_for = 'text';
	  }
	  elseif($mach_fld_type == 'date'){
	    $attrs_for = 'text';
	  }
	  elseif($mach_fld_type == 'number'){
	    $attrs_for = 'text';
	  }
	  elseif($mach_fld_type == 'checkbox'){
	    $attrs_for = 'checkbox';
	  }
	  elseif($mach_fld_type == 'datepicker'){
	    $attrs_for = 'datepicker';
	  }
	  elseif($fld_type == 'multiselect'){
	    $attrs_for = 'multiselect';
	  }
	}
	else{
	  $render_attrs = false;
	}
	
	if($fld_type == 'text') $text_as_def = true;
	  else $text_as_def = false;
	
	if($fld_type == 'file' || $fld_type == 'datepicker' || $fld_type == 'timepicker') $no_default = true;
	  else $no_default = false;
	  
	if($fld_type == 'hidden') $ishidden = TRUE; else $ishidden = FALSE;
	
	if($fld_type == 'checkbox'){
      $check_as_def = true;
	  $check = $attr_checked;
	  $value = 1;
	}
	else{
      $check_as_def = false;
	}
	
    if($_POST){
	  $values = $_POST;
	}
	
	foreach($values as $key => $val){
	  $$key = $val;
	}
	
	if(isset($required) && $required == 1) $checked = true;
	  else $checked = false;
	
	$form = new Form('edit-field', 'frm-edit-field', BASEPATH.'/admin/contentTypes/savefield');
	
	$form->addHidden('id', $id);
	$form->addText('label', 'Názov poľa (titulok)', $label);
	  $form->addRule('label', Form::FILLED, 'Zadajte názov poľa.');
	
	if(!$ishidden) {
		$form->addText('content_label', 'Popisok na stránke', $content_label);
			$form->addDescription('content_label', 'Ak zadáte popisok, bude sa tento zobrazovať pred hodnotou poľa. Ak ponecháte prázdne, popisok sa nezobrazí.');
		$form->addTextarea('description', 'Popis', $description, 'frm-textarea' ,30, 3);
	}
	else {
		$form->addHidden('content_label', '');
		$form->addHidden('description', '');
	}
	
	if($text_as_def){
	  $form->addText('default', 'Výchozia hodnota', $default);
	}
	elseif($check_as_def){
	  $form->addHidden('default', 1);
	  $form->addRadio('attr_checked', array('nie', 'ano'), 'Výchozia hodnota - zaškrtnuté', $check, true);
	}
	elseif($no_default){
	  // do nothing
	  $form->addHidden('default', null);
	}
	else{
	  if($fld_type == 'select' or $fld_type == 'multiselect' or $fld_type == 'radio' or $fld_type == 'checkboxgroup'){
	    $default = preg_replace('/;/', "\r\n", $default);
		$form->addTextarea('default', 'Výchozia hodnota', $default);
	    $form->addDescription('default', 'Vložte každú položku zoznamu na nový riadok. Za výchoziu položku dajte slovíčko default oddelené dvojbodkou:<br/><i>Položka 1:default<br/>Položka 2<br/>Položka 3</i><br/>Takisto je možne vytvoriť páry hodnot. Prvá hodnota sa uloží a druhá hodnota sa zobrazí v rozbaľovacom zozname:<br/>hodnota1=Položka 1:default<br/>hodnota2=Položka 2<br/>hodnota3=Položka 3');
		$form->addRule('default', Form::FILLED, 'Výchozia hodnota - Zoznam musí buť zadaný.');
	  }
	  else{
	    $form->addTextarea('default', 'Výchozia hodnota', $default);
	  }
	}
	if( $fld_type === 'file' or $ishidden === TRUE ) {
	  $form->addHidden('required', 0);
	}
	else{
	  $form->addCheckbox('required', 1, 'povinná položka', null, $checked);
	}
	
	if($render_attrs){
	  if($attrs_for == 'text'){
	    $form->addText('attr_size', 'Počet zobrazených znakov', $attr_size, 'frm-text',5, 5);
		$form->addText('attr_maxlength', 'Maximálny počet znakov', $attr_maxlength, 'frm-text',5, 5);
	  }
	  elseif($attrs_for == 'textarea'){
                $form->addText('attr_cols', 'Počet stĺpcov', $attr_cols, 'frm-text',5, 5);
		$form->addText('attr_rows', 'Počet riadkov', $attr_rows, 'frm-text',5, 5);
		$form->addSelect('attr_wrap', array('off'=>'Off', 'soft'=>'Soft', 'hard'=>'Hard'), 'Zalomenie textu', $attr_wrap);
                $form->addRadio('attr_allowedtags', array('null'=>'Žiadne', '<b><i><strong><em><p><div><span><a><ul><li><img>'=>'&lt;b>&lt;i>&lt;strong>&lt;em>&lt;p>&lt;div>&lt;span>&lt;a>&lt;ul>&lt;li>&lt;img>', 'true'=>'Všetky'), 'Povolené HTML značky', $attr_allowedtags);
	  }
	  elseif($attrs_for == 'file'){
	    $form->insertContent('Maximálna veľkosť súboru môže byť '.(int)ini_get("upload_max_filesize").'MB. Ak chcete zvýšiť túto hodnotu, musíte zmeniť nastavenie php.ini, alebo požiadajte svojho administrátora');
		$form->addText('attr_max_file_size', 'Maximálna veľkosť súboru v megabajtoch', $attr_max_file_size, 'frm-text',2, 2);
			$form->addRule('attr_max_file_size', Form::MIN, 1, (int)ini_get("upload_max_filesize"),'Max. veľkosť súboru: Číslo musí mať min. hodnotu 1 a max. hodnotu '.(int)ini_get("upload_max_filesize"));
		$form->addText('attr_max_files', 'Maximálny počet súborov', $attr_max_files, 'frm-text',2, 2);
			$form->addDescription('attr_max_files', 'Ak ponecháte nulu, tak počet súborov na stránku bude neobmedzený.');
		$form->addSelect('attr_order_by', array('description'=>'Popisu', 'priority'=>'Priority','datetime'=>'Dátum a času pridania'), 'Zoradiť súbory podľa', $attr_order_by);
	  }
	  elseif($attrs_for == 'image'){
	    $form->insertContent('Maximálna veľkosť súboru môže byť '.(int)ini_get("upload_max_filesize").'MB. Ak chcete zvýšiť túto hodnotu, musíte zmeniť nastavenie php.ini, alebo požiadajte svojho administrátora');
	    $form->addText('attr_max_file_size', 'Maximálna veľkosť súboru v megabajtoch', $attr_max_file_size, 'frm-text',2, 2);
			$form->addRule('attr_max_file_size', Form::RANGE, 1, (int)ini_get("upload_max_filesize"),'Max. veľkosť súboru: Číslo musí mať min. hodnotu 1 a max. hodnotu '.(int)ini_get("upload_max_filesize"));
		$form->addText('attr_max_files', 'Maximálny počet súborov', $attr_max_files, 'frm-text',2, 2);
			$form->addDescription('attr_max_files', 'Ak ponecháte nulu, tak počet súborov na stránku bude neobmedzený.');
		$form->addSelect('attr_order_by', array('description'=>'Popisu', 'priority'=>'Priority','datetime'=>'Dátum a času pridania'), 'Zoradiť obrázky podľa', $attr_order_by);
		$form->addText('attr_preview_size', 'Veľkosť obrázku v pixeloch', $attr_preview_size, 'frm-text', 3, 3);
			$form->addDescription('attr_preview_size', 'Zadajte hodnotu v intervale 300 - 800');
			$form->addRule('attr_preview_size', Form::RANGE, 300, 800, 'Veľkosť obrázku v pixeloch: Zadajte číslo v rozpätí 300 až 800');
		$form->addText('attr_icon_size', 'Veľkosť ikony v pixeloch', $attr_icon_size, 'frm-text', 3, 3);
			$form->addDescription('attr_icon_size', 'Zadajte hodnotu v intervale 50 - 450');
			$form->addRule('attr_icon_size', Form::RANGE, 50, 450, 'Veľkosť ikony v pixeloch: Zadajte číslo v rozpätí 50 až 450');
		$form->addSelect('attr_thumb_create', array('ratio'=>'Zmenšiť a zachovať pomer strán', 'cut'=>'Zmenšiť a orezať na štvorec'), 'Spôsob nahrania ikony', $attr_thumb_create);
	  }
	  elseif($attrs_for == 'datepicker') {
		  $form->addSelect('attr_format', array('DD.MM.YYYY'=>'DD.MM.YYYY', 'YYYY-MM-DD'=>'YYYY-MM-DD'), 'Formát', $attr_format);
		  $form->addTextarea('attr_years', 'Preddefinované roky', $attr_years, 'frm-textarea', 67, 5);
			$form->addDescription('attr_years', 'Vložte roky oddelené čiarkou bez medzier: <b>2010,2011,2012</b>');
	  }
	  elseif($attrs_for == 'multiselect') {
		  $form->addText('attr_size', 'Počet riadkov (min. 2)', $attr_size, 'frm-textarea', 2, 2);
			$form->addDescription('attr_size', 'Zadajte počet riadkov zoznamu (minmálne dva)');
			$form->addRule('attr_size', Form::MIN, 2, 'Počet riadkov musí byť minimálne 2');
		  $form->addHidden('attr_multiple', $attr_multiple);
	  }
	}
		
	$form->addSubmit('save', 'Uložiť');
	
	return $form->render();
  
  }
  
  private function createDeleteForm($id)
  {
	$obj = new AdmContentTypesModel;
	
	$type = $obj->findOne($id);
	
	$form = new Form('deleteCT', 'deleteCT', BASEPATH.'/admin/contentTypes/deleteConfirm');
	
	$form->addHidden('id', $id);
	$form->addHidden('machine_name', $type['machine_name']);
	$form->addSubmit('delete', GOON);
	
	return $form->render();
  }
  
  private function createListTable($rows)
  {
	  $table = new Table('contentTypes', $this);
	  $table->setDataSource($rows);
	  $table->setAjax();
	  $table->setPaginator(10, 10);
	  $table->setClass('admin-table');
	  
	  $table->addColumn('name', NAME)
	        ->addOrderShift();
		
	  $table->addColumn('machine_name', MACHINE_NAME);
	  
	  $table->addColumn('description', DESCRIPTION);
	  
	  $table->addActions('id', ACTIONS)->setClass('actions')->setStyle('width:72px;');
	  
	  if(Application::$edit) {
	    $table->addAction(FIELDS, 'admin/content-types:fields', Table::WITH_KEY)
	          ->setImage( Html::elem('img')->src(Application::imgSrc('icon-items.jpg'))->style('width:20px;height:20px;')->alt(FIELDS) );
	    $table->addAction(EDIT, 'admin/content-types:edit', Table::WITH_KEY)
	          ->setImage( Html::elem('img')->src(Application::imgSrc('icon-edit.jpg'))->style('width:20px;height:20px;')->alt(EDIT) );
	  }
	  else {
	    $table->addAction(FIELDS, 'admin/content-types:fields', Table::WITH_KEY)
	          ->setDisabled()
	          ->setImage( Html::elem('img')->src(Application::imgSrc('icon-items-gr.jpg'))->style('width:20px;height:20px;')->alt(FIELDS) );
	    $table->addAction(EDIT, 'admin/content-types:edit', Table::WITH_KEY)
	          ->setDisabled()
	          ->setImage( Html::elem('img')->src(Application::imgSrc('icon-edit-gr.jpg'))->style('width:20px;height:20px;')->alt(EDIT) );
	  }
	  
	  if(Application::$delete) {
	    $table->addAction(DELETE, 'admin/content-types:delete', Table::WITH_KEY)
	          ->setImage( Html::elem('img')->src(Application::imgSrc('icon-delete.jpg'))->style('width:20px;height:20px;')->alt(DELETE) );
	  }
	  else {
	    $table->addAction(DELETE, 'admin/content-types:delete', Table::WITH_KEY)
	          ->setDisabled()
	          ->setImage( Html::elem('img')->src(Application::imgSrc('icon-delete-gr.jpg'))->style('width:20px;height:20px;')->alt(DELETE) );
	  }
	  
	  return $table;
  }

}