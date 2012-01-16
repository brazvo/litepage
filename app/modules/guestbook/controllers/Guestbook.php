<?php
class Guestbook extends Controller
{

  // Properties
  private $pages;
  
  // Constructor
  public function __construct()
  {
    $this->perm_mach_name = 'guestbook'; //
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
	  $obj = new GuestbookModel;
	  $result = $obj->save($_POST);
	  if($result){
	    redirect('guestbook', 'Úprava odkazu bola uložená.');
	  }
	  else{
	    redirect('guestbook', 'Uloženie úpravy zlyhalo.');
	  }
	}
	else{
	  $this->render('default');
	}
  }
  
  function actionSavenew()
  {
    if(Form::$isvalid){
	  $obj = new GuestbookModel;
	  $result = $obj->saveNew($_POST);
	  if($result){
	    redirect('guestbook', 'Odkaz bol odoslaný.');
	  }
	  else{
	    redirect('guestbook', 'Odoslanie obsahu zlyhalo');
	  }
	}
	else{
	  $this->render('default');
	}
  }
  
  function actionSaveSettings()
  {
    if(Form::$isvalid){
	  $obj = new GuestbookModel;
	  $result = $obj->saveSettings($_POST);
	  if($result){
	    redirect('guestbook/settings', 'Nastavenia boli uložené.');
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
	  $obj = new GuestbookModel;
	  $result = $obj->delete($id);
	  if($result){
	    redirect('guestbook', 'Odkaz bol vymazaný.');
	  }
	  else{
	    redirect('guestbook', 'Vymazanie odkazu sa nepodarilo.');
	  }
	}
	else{
	  redirect('guestbook', 'Nemáte povolené vykonať vymazanie záznamu.');
	}
  }
   
  /***************************************** RENDERERS ***/
  function renderDefault()
  {
    $obj = new GuestbookModel;
  	$this->template['title'] = 'Kniha návštev';
	$this->template['form'] = $this->createAddForm();
	$this->template['messages'] = $obj->findForPage();
	$this->template['paginator'] = $this->createPaginator(1);
  
  }
  
  function renderPage($id)
  {
    $obj = new GuestbookModel;
	$this->template['view'] = 'default';
  	$this->template['title'] = 'Kniha návštev';
	$this->template['form'] = $this->createAddForm();
	$this->template['messages'] = $this->pages[$id];
	$this->template['paginator'] = $this->createPaginator($id);
  
  }
  
  function renderAdd()
  {
  
  }
  
  function renderEdit($id)
  {
    if(Application::$edit){
	  $obj = new GuestbookModel;
  	  $this->template['view'] = 'settings';
	  $this->template['title'] = 'Kniha návštev - úprava odkazu';
	  $this->template['content'] = '<p style="color:red; font-size:8pt">Uprávy odkazu vykonajte len v najnutnejších prípadoch ako odstránenie reklám, vulgarizmov a pod. Prílišnou cenzúrou môžete odradiť návševníkov k posielaniu odkazov.</p>';
	  $this->template['content'] .= $this->createEditForm($id);
	}
	else{
	  redirect('guestbook', 'Nemáte povolené vykonávať úpravy záznamu.');
	}
  }
  
  function renderAdmin()
  {
     
  	$this->template['title'] = 'Kniha návštev - administrácia';
  
  }
  
  function renderSettings()
  {
    
  	$this->template['title'] = 'Kniha návštev - nastavenia';
	$this->template['content'] = $this->createSettingsForm();
  
  }
  
  /***************************************** FACTORIES ***/
  private function createSettingsForm()
  {
    $obj = new GuestbookModel;
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
	  if(!isset($auto_clean)) $auto_clean = 0;
	}
	
	$form = new Form('gb-settings', 'frm-gb-settings', BASEPATH.'/guestbook/saveSettings');
	$form->addText('messages_per_page', $label['messages_per_page'], $messages_per_page, 2, 2);
	$form->addSelect('order', array('ASC' => 'Staršie >> Novšie', 'DESC'=>'Novšie >> Staršie'), $label['order'], $order);
		$form->addDescription('order', $desc['order']);
	$form->addRadio('email_wanted', array(0 => 'Nie', 1 => 'Ano'), $label['email_wanted'], $email_wanted, true);
	$form->addRadio('subject_wanted', array(0 => 'Nie', 1 => 'Ano'), $label['subject_wanted'], $subject_wanted, true);
	$form->addText('notificate', $label['notificate'], $notificate);
		$form->addDescription('notificate', $desc['notificate']);
		$form->addRule('notificate', Form::EMAIL, 'Zadajte korektný email.');
	$form->addCheckbox('auto_clean', 1, '', $label['auto_clean'], $auto_clean);
		$form->addDescription('auto_clean', $desc['auto_clean']);
	$form->addText('delete_older_then', $label['delete_older_then'], $delete_older_then, 'frm-text', 5, 5);
		$form->addDescription('delete_older_then', $desc['delete_older_then']);
		$form->addRule('delete_older_then', Form::MIN, 1,'Zadajte číslo s minimálnou hodnotou 1.');
	$form->emptyLine();
	$form->addSubmit('save', 'Uložiť');
	
	return $form->render();
	
  }
  
  private function createAddForm()
  {
    
    $form = new Form('add-message', 'gb-add-message', BASEPATH.'/guestbook/savenew');
	
	if($_POST){
	  foreach($_POST as $key => $val){
	    $$key = $val;
	  }
	}
	else{
	  $values = $form->getZeroValues('guestbook');
	  foreach($values as $key => $val){
	    $$key = $val;
	  }
	}
	
	$form->addText('name', 'Meno', $name);
	  $form->addRule('name', Form::FILLED, 'Vyplnte položku meno.');
	if(EMAIL_WANTED){
	  $form->addText('email', 'Email', $email);
		$form->addRule('email', Form::EMAIL, 'Zadajte korektny email.');
	}
	else{
	  $form->addHidden('email', $email);
	}
	if(SUBJECT_WANTED){
	  $form->addText('subject', 'Predmet', $subject);
	}
	else{
	  $form->addHidden('subject', $subject);
	}
	$form->addTextarea('message', 'Odkaz', $message);
	  $form->addRule('message', Form::FILLED, 'Vyplnte políčko odkaz');
	$form->addCaptcha('sec_code', 'Opíšte kód z obrázku');
	  $form->addRule('sec_code', Form::CAPTCHA, 'Opíšte správne kód z obrázku');
	$form->emptyLine();
	$form->addSubmit('save', 'Uložiť');
	
    return $form->render();
  }
  
  private function createEditForm($id)
  {
    
    $form = new Form('edit-message', 'gb-edit-message', BASEPATH.'/guestbook/save');
	
	if($_POST){
	  foreach($_POST as $key => $val){
	    $$key = $val;
	  }
	}
	else{
	  $obj = new GuestbookModel;
	  $values = $obj->find($id);
	  foreach($values as $key => $val){
	    $$key = $val;
	  }
	}
	
	$form->addHidden('id', $id);
	$form->addText('name', 'Meno', $name);
	  $form->addRule('name', Form::FILLED, 'Vyplnte položku meno.');
	if(EMAIL_WANTED){
	  $form->addText('email', 'Email', $email);
		$form->addRule('email', Form::EMAIL, 'Zadajte korektny email.');
	}
	else{
	  $form->addHidden('email', $email);
	}
	if(SUBJECT_WANTED){
	  $form->addText('subject', 'Predmet', $subject);
	}
	else{
	  $form->addHidden('subject', $subject);
	}
	$form->addTextarea('message', 'Odkaz', $message);
	  $form->addRule('message', Form::FILLED, 'Vyplnte políčko odkaz');
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
		  
		  $pagesElems .= Html::elem('a', array('href'=>BASEPATH.'/guestbook/page/1', 'class'=>'gb-page-link first'), "&#9668;&nbsp;prvá&nbsp;").'|';
		  
		  for($i = $startFrom; $i <= $countTo; $i++)
		  {

			if($pgNumber == $i){
			  $pagesElems .= Html::elem('span', array('class'=>'gb-page-active'), "&nbsp;$i&nbsp;").'|';
			}
			else{
			  $pagesElems .= Html::elem('a', array('href'=>BASEPATH.'/guestbook/page/'.$i, 'class'=>'gb-page-link'), "&nbsp;$i&nbsp;").'|';
			}
		  
		  }
		  
		  $pagesElems .= Html::elem('a', array('href'=>BASEPATH.'/guestbook/page/'.$lastPage, 'class'=>'gb-page-link last'), "&nbsp;posledná&nbsp;&#9658;");
		  
		  return Html::elem('div', array('class'=>'gb-paginator'), 'Stránka: '.$pagesElems);
		}
		else{
		  return '';
		}
	
	}
  /*************************************** OTHER METHODS ***/
  
  private function runSettings()
  {
    $obj = new GuestbookModel;
  
    $rows = $obj->getSettings();
  
    foreach($rows as $row){
      define($row['constant'], $row['value']);
    }
	if(AUTO_CLEAN){
	  $clean_date = date("Y-m-d H:i:s", mktime(0, 0, 0, date("m")  , date("d")-DELETE_OLDER_THEN, date("Y")));
	  db::exec("DELETE FROM guestbook WHERE datetime < '$clean_date'");
	}
  }
  
  private function getPages()
  {
    $obj = new GuestbookModel;
	$records = $obj->findAll();
	$rec_num = count($records);
	if($rec_num){
	  $pageidx = 1;
	  $idx = 1;
	  $lastidx = (int)MESSAGES_PER_PAGE;
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
