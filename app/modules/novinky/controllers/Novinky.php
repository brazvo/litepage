<?php
class Administrate extends Controller
{

  // Properties
  private $pages;
  
  // Constructor
  public function __construct()
  {
    $this->perm_mach_name = 'novinky'; //
	$this->runSettings();
	$this->getPages();
    parent::__construct();

  }
  
  // Methods
  /***************************************** ACTIONS ***/
  function actionAdmin()
  {
    if(!Application::$logged['status']){
	  redirect('login');
	}
	else{
	  $this->render('admin');
	}
  }
  
  function actionSave()
  {
    if(Form::$isvalid){
	  $obj = new NovinkyModel;
	  $result = $obj->save($_POST);
	  if($result){
	    redirect('novinky', 'Úprava správy bola uložená.');
	  }
	  else{
	    redirect('novinky', 'Uloženie úpravy zlyhalo.');
	  }
	}
	else{
	  $this->render('default');
	}
  }
  
  function actionSavenew()
  {
    if(Form::$isvalid){
	  $obj = new NovinkyModel;
	  $result = $obj->saveNew($_POST);
	  if($result){
	    redirect('novinky', 'Novinka bola uložená.');
	  }
	  else{
	    redirect('novinky', 'Uloženie novinky zlyhalo');
	  }
	}
	else{
	  $this->render('default');
	}
  }
  
  function actionSaveSettings()
  {
    if(Form::$isvalid){
	  $obj = new NovinkyModel;
	  $result = $obj->saveSettings($_POST);
	  if($result){
	    redirect('novinky/settings', 'Nastavenia boli uložené.');
	  }
	  else{
	    Application::setError('Uloženie nastavení zlyhalo.');
		$this->render('settings');
	  }
	}
	else{
	  $this->render('settings');
	}
  }
  
  function actionDelete($id)
  {
    if(Application::$delete){
	  $obj = new NovinkyModel;
	  $result = $obj->delete($id);
	  if($result){
	    redirect('novinky', 'Správa bola vymazaná.');
	  }
	  else{
	    redirect('novinky', 'Vymazanie správy sa nepodarilo.');
	  }
	}
	else{
	  redirect('novinky', 'Nemáte povolené vykonať vymazanie záznamu.');
	}
  }
   
  /***************************************** RENDERERS ***/
  function renderDefault()
  {
    if(Application::$view){	
	  $obj = new NovinkyModel;
  	  $this->template['title'] = 'Novinky';
	  $this->template['messages'] = $obj->findForPage();
	  $this->template['paginator'] = $this->createPaginator(1);
    }
	else{
		$this->render('nopermision');
	}  
  }
  
  function renderSprava($id)
  {
    if(Application::$view){	
	  $obj = new NovinkyModel;
	
	  $row = $obj->find($id);
	  
	  if($row){
  	    $this->template['title'] = $row['title'];
	    $this->template['date'] = slovdate($row['datetime']);
	    $this->template['message'] = $row['message'];
		$this->template['id'] = $row['id'];
	  }
	  else{
	    $this->template['title'] = 'Chyba!';
	    $this->template['date'] = '';
	    $this->template['message'] = 'Správa neexistuje.';
		$this->template['id'] = '';
	  }
	}
	else{
		$this->render('nopermision');
	}
  }
  
  function renderPage($id)
  {
    $obj = new NovinkyModel;
	$this->template['view'] = 'default';
	$this->template['title'] = 'Novinky';
	$this->template['messages'] = $this->pages[$id];
	$this->template['paginator'] = $this->createPaginator($id);
  
  }
  
  function renderAdd()
  {
    if(Application::$add){
	  $this->template['view'] = 'settings';
	  $this->template['title'] = 'Novinky - nová správa';
	  $this->template['content'] = $this->createAddForm();
	}
	else{
		$this->render('nopermision');
	}
  }
  
  function renderEdit($id)
  {
    if(Application::$edit){
	  $obj = new NovinkyModel;
  	  $this->template['view'] = 'settings';
	  $this->template['title'] = 'Novinky - úprava správy';
	  $this->template['content'] = $this->createEditForm($id);
	}
	else{
	  redirect('novinky', 'Nemáte povolené vykonávať úpravy záznamu.');
	}
  }
  
  function renderAdmin()
  {
     
  	$this->template['title'] = 'Novinky - administrácia';
  
  }
  
  function renderSettings()
  {
    
  	$this->template['title'] = 'Novinky - nastavenia';
	$this->template['content'] = $this->createSettingsForm();
  
  }
  
  function renderNopermision()
  {
    $this->template['view'] = 'settings';
  	$this->template['title'] = 'Novinky';
	$this->template['content'] = 'Nemáte oprávnene na zobraznie tohto obsahu.';
  
  }
  
  /***************************************** FACTORIES ***/
  private function createSettingsForm()
  {
    $obj = new NovinkyModel;
	$rows = $obj->getSettings();
	
	foreach($rows as $row){
	  $$row['frm_name'] = $row['value'];
	  $label[$row['frm_name']] = $row['title'];
	  $desc[$row['frm_name']] = $row['description'];
	}
	if($_POST){
	  foreach($_POST as $key => $val){
	    $$key = $val;
	  }
	}
	
	$form = new Form('news-settings', 'frm-news-settings', BASEPATH.'/novinky/saveSettings');
	$form->addText('messages_in_block', $label['messages_in_block'], $messages_in_block, 2, 2);
		$form->addRule('messages_in_block', Form::MIN, 1, 'Počet noviniek v bloku: zadajte celé číslo s minimálnou hodnotou 1.');
		$form->addDescription('messages_in_block', $desc['messages_in_block']);
	$form->addText('characters_in_block', $label['characters_in_block'], $characters_in_block, 3, 3);
	    $form->addRule('characters_in_block', Form::MIN, 50, 'Počet znakov: zadajte celé číslo s minimálnou hodnotou 50.');
		$form->addDescription('characters_in_block', $desc['characters_in_block']);
	$form->addText('messages_on_page', $label['messages_on_page'], $messages_on_page, 2, 2);
	    $form->addRule('messages_on_page', Form::MIN, 5, 'Počet noviniek na stránke: zadajte celé číslo s minimálnou hodnotou 5.');
		$form->addDescription('messages_on_page', $desc['messages_on_page']);
	$form->addSelect('order', array('ASC' => 'Staršie >> Novšie', 'DESC'=>'Novšie >> Staršie'), $label['order'], $order);
		$form->addDescription('order', $desc['order']);
	$form->addRadio('show_date', array(0 => 'Nie', 1 => 'Ano'), $label['show_date'], $show_date, true);
	$form->emptyLine();
	$form->addSubmit('save', 'Uložiť');
	
	return $form->render();
	
  }
  
  private function createAddForm()
  {
    
    $form = new Form('add-message', 'news-add-message', BASEPATH.'/novinky/savenew');
	
	if($_POST){
	  foreach($_POST as $key => $val){
	    $$key = $val;
	  }
	}
	else{
	  $values = $form->getZeroValues('hotnews');
	  foreach($values as $key => $val){
	    $$key = $val;
	  }
	}
	
	$form->addText('title', 'Titulok', $title);
	  $form->addRule('title', Form::FILLED, 'Vyplnte položku Titulok.');
	$form->addTextarea('message', 'Správa', $message);
	  $form->addRule('message', Form::FILLED, 'Vyplnte políčko Správa');
	$form->emptyLine();
	$form->addSubmit('save', 'Uložiť');
	
    return $form->render();
  }
  
  private function createEditForm($id)
  {
    
    $form = new Form('edit-message', 'news-edit-message', BASEPATH.'/novinky/save');
	
	if($_POST){
	  foreach($_POST as $key => $val){
	    $$key = $val;
	  }
	}
	else{
	  $obj = new NovinkyModel;
	  $values = $obj->find($id);
	  foreach($values as $key => $val){
	    $$key = $val;
	  }
	}
	
	$form->addHidden('id', $id);
	$form->addText('title', 'Titulok', $title);
	  $form->addRule('title', Form::FILLED, 'Vyplnte položku Titulok.');
	$form->addTextarea('message', 'Správa', $message);
	  $form->addRule('message', Form::FILLED, 'Vyplnte políčko Správa');
	$form->emptyLine();
	$form->addSubmit('save', 'Uložiť');
	
    return $form->render();
  }
   
  private function createPaginator($pgNumber)
	{
		if(count($this->pages)>1){
          $limit = 10; // limit of paginator page links
		  
		  $lastPage = count($this->pages); // last page number
		  
		  $startFrom = $pgNumber - ($limit/2); // start paginator from page
		  
		  $startFrom < 1 ? $startFrom = 1 : $startFrom = $startFrom; // if less than 1 set to 1
		  
		  $countTo = $startFrom + ($limit-1); // end paginator with page...
		  
		  if($countTo > $lastPage){ // if more than last then set to last
			 $countTo = $lastPage;
			 $startFrom = $countTo - $limit;
			 $startFrom < 1 ? $startFrom = 1 : $startFrom = $startFrom; // if less than 1 set to 1
		  }
		  
		  $pagesElems = '';
		  
		  $pagesElems .= Html::elem('a', array('href'=>BASEPATH.'/novinky/page/1', 'class'=>'news-page-link first'), "&#9668;&nbsp;prvá&nbsp;").'|';
		  
		  for($i = $startFrom; $i <= $countTo; $i++)
		  {

			if($pgNumber == $i){
			  $pagesElems .= Html::elem('span', array('class'=>'news-page-active'), "&nbsp;$i&nbsp;").'|';
			}
			else{
			  $pagesElems .= Html::elem('a', array('href'=>BASEPATH.'/novinky/page/'.$i, 'class'=>'news-page-link'), "&nbsp;$i&nbsp;").'|';
			}
		  
		  }
		  
		  $pagesElems .= Html::elem('a', array('href'=>BASEPATH.'/novinky/page/'.$lastPage, 'class'=>'news-page-link last'), "&nbsp;posledná&nbsp;&#9658;");
		  
		  return Html::elem('div', array('class'=>'news-paginator'), 'Stránka: '.$pagesElems);
		}
		else{
		  return '';
		}
	
	}
  /*************************************** OTHER METHODS ***/
  
  private function runSettings()
  {
    $obj = new NovinkyModel;
  
    $rows = $obj->getSettings();
  
    foreach($rows as $row){
      define($row['constant'], $row['value']);
    }
  }
  
  private function getPages()
  {
    $obj = new NovinkyModel;
	$records = $obj->findAll();
	$rec_num = count($records);
	if($rec_num){
	  $pageidx = 1;
	  $idx = 1;
	  $lastidx = (int)HN_MESSAGES_ON_PAGE;
	  foreach($records as $record){
		$messages[$record['id']] = $record;
		if($idx == $lastidx){
		  $idx = 0;
		  $this->pages[$pageidx] = $messages;
		  unset($messages);
		  $pageidx++;
		}
		$idx++;
	  }
	  // Place the rest of messages to last page
	  if(isset($messages) && count($messages)>0){
	    $this->pages[$pageidx] = $messages;
	  }
	}
	else{
	  $this->pages = false;
	}
  }
}
