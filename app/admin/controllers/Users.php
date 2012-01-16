<?php

class Users extends BaseAdmin
{

	// Properties
	private $profile;
  
  // Constructor
  public function __construct()
  {
    $this->perm_mach_name = 'users';
    parent::__construct();
  
  }
  
  // Methods
  function actionLogout()
  {
    $log = new LoginModel;
	
	$result = $log->logout();
	
	if($result){
	  redirect();
	}
	else{
	  Application::setError('Pri odhlasovaní došlo k chybe.');
	  $this->render('default');
	}
  
  }
  
  function actionSavenew()
  {
  
    if(Form::$isvalid){
	  $obj = new AdmUsersModel;
	  $result = $obj->saveNew($_POST);
	  if($result){
	    redirect('admin/users/list', 'Nový užívateľ bol vytvorený.');
	  }
	  else{
	    Application::setError('Uloženie sa nepodarilo.');
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
	  $obj = new AdmUsersModel;
	  $result = $obj->save($_POST);
	  if($result){
	    redirect('admin/users/list', SAVE_OK);
	  }
	  else{
	    Application::setError(SAVE_FAILED);
		Application::$id = $_POST['id'];
		$this->render('edit');
	  }
    }
	else{
	  Application::$id = $_POST['id'];
	  $this->render('edit');
	}
  }
  
  function actionDelete(){
  
    if(Application::$delete){
		$obj = new AdmUsersModel;
		
		$result = $obj->delete($this->id);
		
		if($result){
		  redirect('admin/users/list', 'Užívateľ bol vymazaný.');
		}
		else{
		  redirect('admin/users/list', 'Výmaz sa nepodaril.');
		}
	}
	else{
	    redirect('error/default/no_permision');
	}
  
  }
  
  function actionAddUsersPermision()
  {
	if(!Application::$logged['role'] == 'admin') redirect('error/show/1');
	
	$obj = new AdmUsersModel;
	
	$result = $obj->addUsersPermision($_POST);
	
	if($result){
		redirect('admin/users/usersPermisions/'.$_POST['uid'], USERS_ADMIN_USERS_PERMISIONS_SAVE_OK);
	}
	else{
		redirect('admin/users/usersPermisions/'.$_POST['uid'], USERS_ADMIN_USERS_PERMISIONS_SAVE_FAILED);
	}
  }
  
  function actionSaveUsersPermisions()
  {
	if(!Application::$logged['role'] == 'admin') redirect('error/show/1');
	
	$obj = new AdmUsersModel;
	
	$result = $obj->saveUsersPermision($_POST);
	
	if($result){
		redirect('admin/users/usersPermisions/'.$_POST['uid'], USERS_ADMIN_USERS_PERMISIONS_SAVE_OK);
	}
	else{
		redirect('admin/users/usersPermisions/'.$_POST['uid'], USERS_ADMIN_USERS_PERMISIONS_SAVE_FAILED);
	}
  }
  
  function actionDeleteUsersPermision($id)
  {
	if(!Application::$logged['role'] == 'admin') redirect('error/show/1');
	
	$obj = new AdmUsersModel;
	
	$result = $obj->deleteUsersPermision($id);
	
	if($result){
		$path = $_SESSION['destination'];
		unset($_SESSION['destination']);
		redirect($path, USERS_ADMIN_USERS_PERMISIONS_SAVE_OK);
	}
	else{
		redirect($path, USERS_ADMIN_USERS_PERMISIONS_SAVE_FAILED);
	}
  }
  
  function actionEdit($id)
  {
	  $obj = new AdmUsersModel;
	  $this->profile = $obj->find($id);
  }
  
  function actionLockUserConfirm($uid)
  {
	  $obj = new AdmUsersModel();
	  if( $obj->lockUser($uid) ) {
		  redirect('admin/users/edit/'.$uid, 'Užívateľ bol zablokovaný.');
	  }
	  else {
		  redirect('admin/users/edit/'.$uid, 'Užívateľa sa nepodarilo zablokovať.');
	  }
  }
  
  function actionUnlockUserConfirm($uid)
  {
	  $obj = new AdmUsersModel();
	  if( $obj->unlockUser($uid) ) {
		  redirect('admin/users/edit/'.$uid, 'Užívateľ bol odblokovaný.');
	  }
	  else {
		  redirect('admin/users/edit/'.$uid, 'Užívateľa sa nepodarilo odblokovať.');
	  }
  }
  
  function actionGeneratePasswordConfirm($uid)
  {
	  $this->newPwd = $this->generatePwd();
	  $this->noemail = FALSE;
	  $obj = new AdmUsersModel();
	  if( $obj->saveNewPwd($uid, $this->newPwd) ) {
		  redirect('admin/users/edit/'.$uid, 'Heslo bolo vygenerovane a odoslané na email užívateľa.');
	  }
	  else {
		  $this->noemail = TRUE;
	  }
  }
  
  ///////////////////////////////////////// renderes
  
  function renderDefault()
  {
	
	$items = new AdmContentModel;
	
	$result = $items->findItems('admin/'.Application::$pageName);
	
	$output = Html::elem('ul');
	foreach($result as $item){
	  $output->add('<li><a href="' . Application::link($item['path']) . '">'.$item['title'].'</a></li>');
	}
	
        $this->template->title = 'Užívatelia';
	$this->template->content = $output;
  
  }
  
  function renderList()
  {
        if(Application::$view){
		$obj = new AdmUsersModel;
		
		$users = $obj->findAll();
		
		$this->template->title = 'Zoznam užívateľov';
		$this->template->users = $users;
	}
        else{
	    redirect('error/default/no_permision');
	}
  }
  
  function renderDetail()
  {
  
  	$object = new AdmUsersModel;
	
	$user = $object->findUser();
	
	$this->template->title = 'Užívateľ';
	$this->template->userinfo = $user;
	$this->template->pwdform = $this->createPwdForm();
  
  }
  
  function renderAdd()
  {
    if(Application::$add){
		$this->template->setView('default');
		$this->template->title = 'Pridať nového užívateľa';
		$this->template->content = $this->createAddForm();
	}
	else{
	    redirect('error/default/no_permision');
	}
  
  }
  
  function renderEdit($id)
  {
    if(Application::$edit){
		$this->template->title = 'Editácia užívateľa';
		$this->template->content = $this->createControlEditForm();
		$this->template->locked = $this->profile['lock'];
		$this->template->id = $id;
	}
	else{
	    redirect('error/default/no_permision');
	}
  
  }
  
  function renderUsersPermisions($id)
  {
    if(Application::$logged['role'] == 'admin'){
		$this->template->setView('default');
		$this->template->title = 'Extra oprávnenia užívateľa';
		$this->template->content = $this->createAddUserPermisionsForm($id) . $this->createUserPermisionsTable($id);
	}
	else{
	    redirect('error/default/no_permision');
	}
  
  }
  
    function renderLockUser($uid)
	{
		$this->template->title = 'Potvrdenie zablokovania';
		$this->template->message = 'Naozaj zablokovať užívateľské konto?';
		$this->template->userId = $uid;
	}
	
	function renderUnlockUser($uid)
	{
		$this->template->title = 'Potvrdenie odblokovania';
		$this->template->message = 'Naozaj odblokovať užívateľské konto?';
		$this->template->userId = $uid;
	}
	
	function renderGeneratePassword($uid)
	{
		$this->template->title = 'Potvrdenie generovania hesla';
		$this->template->message = 'Naozaj vygenerovat nové heslo?';
		$this->template->userId = $uid;
	}
	
	function renderGeneratePasswordConfirm($uid)
	{
		$this->template->title = 'Heslo vygenerované';
		$this->template->message = 'Nové heslo bolo vygenerované, ale užívateľ nezadal svoj email pri registrácii. Opíš si toto heslo a oboznám užívateľa s novým heslom alternatívnym spôsobom.';
		$this->template->newPwd = $this->newPwd;
	}
  
  private function createPwdForm()
  {
  
    $form = new Form('pwdForm', 'pwd-form');
	
	//set default values
	$oldpwd = ''; $newpwd = ''; $newpwdconf = ''; $output = '';
	
	if(isset($_POST['action']) && $_POST['action'] == 'check'){
	
	  //$result = $this->checkForm($_POST);
	  
	  if(!Form::$isvalid){
	    // if posted set values
	    foreach($_POST as $key => $value){
	      $$key = $value;
	    }
		//$output .= $result;
	  }
	  else{
	    // Try to save new password
		$obj = new AdmUsersModel;
		
		$result = $obj->savePwd($_POST);
		
		if(!$result){
		  // not saved set posted values
	      foreach($_POST as $key => $value){
	        $$key = $value;
	      }
		}
		
	  }
	}
	
	$form->addHidden('action','check');
	$form->addPassword('oldpwd','Staré heslo', $oldpwd);
	  $form->addRule('oldpwd', Form::FILLED, 'Zadajte staré heslo.');
	$form->addPassword('newpwd','Nové heslo', $newpwd);
	  $form->addRule('newpwd', Form::FILLED, 'Zadajte nové heslo.');
	$form->addPassword('newpwdconf', 'Potvrdte nové heslo', $newpwdconf);
	  $form->addRule('newpwdconf', Form::FILLED, 'Zadajte potvrdenie nového hesla.');
	  $form->addRule('newpwdconf', Form::EQUAL, 'newpwd', 'Potvrdenie nového hesla sa musí zhodovať.');
	
	$form->addSubmit('submit', 'Uložiť');
	
	return $output.$form->render();
  
  }
  
  private function checkForm($post)
  {
  
    $form = new Form;
	
	$result = $form->frmValidate($post);
	
	if($result){
	  return false;
	}
	else{
	  return $form->render();
	}
  
  }
  
  private function createAddForm()
  {
    if($_POST){
	  foreach($_POST as $key => $val){
	    $$key = $val;
	  }
	}
	else{
	  $name = '';
	  $surname = '';
	  $user = '';
	}
	
	
    $form = new Form('add-new-user','frm-new-user',BASEPATH.'/admin/users/savenew');
	
	$form->addText('name', 'Meno', $name);
	$form->addText('surname', 'Priezvisko', $surname);
	$form->addText('user', 'Užívateľské meno', $user);
	  $form->addRule('user', Form::FILLED, 'Zadajte Užívateľské meno');
	$form->addPassword('password', 'Užívateľské heslo', null);
	  $form->addRule('password', Form::FILLED, 'Zadajte Užívateľské heslo');
	$form->addPassword('pwdconfirm', 'Overenie hesla', null);
	  $form->addRule('pwdconfirm', Form::FILLED, 'Zadajte Overenie hesla');
	  $form->addRule('pwdconfirm', Form::EQUAL, 'password', 'Overenie hesla je nesprávne.');
	$form->addSelect('role', array('user' => 'Užívateľ', 'editor' => 'Editor', 'admin' => 'Administrátor'), 'Práva ako:');
	
	$form->addSubmit('save', 'Uložiť');
	
	return $form->render();
  
  }
  
  public function createControlEditForm()
  {
    $obj = new AdmUsersModel;

	$values = $obj->find($this->id);

	$staff_positions = $obj->getStaffPositions();
	
    $form = new AppForm('edit-user','frm-new-user', BASEPATH.'/admin/users/save');
	
	$form->addHidden('id', $this->id)->setValue($this->id);
	$form->addText('name', 'Meno:');
	$form->addText('surname', 'Priezvisko:');
	$form->addContent('Užívateľské meno: '.$this->profile['user'].'<br/><br/>'.'Ak chcete zmeniť heslo, zadajte nové heslo a overenie. Ak nie, ponechajte prázdne.');
	$form->addPassword('password', 'Užívateľské heslo:')
			->addDescription('Ak chcete zmeniť heslo, zadajte nové heslo a overenie. Ak nie, ponechajte prázdne');
	$form->addPassword('pwdconfirm', 'Overenie hesla:')
			->addRule(AppForm::EQUAL, 'Overenie hesla je nesprávne.', 'password');
	$form->addSelect('role',  'Práva ako:', array('user' => 'Užívateľ', 'editor' => 'Editor', 'admin' => 'Administrátor'));
	
	$form->addBreak();
	$form->addTextarea('staff_comment', 'Komentár staffu k užívateľovi:', 67, 5);
	$form->addBreak();
	
	$staff = $form->addBlock('staff')->setCaption('Členstvo v Staffe');
	$staff->addItem( $form->addCheckbox('isstaff', 'Člen stafu') );
	$staff->addItem( $form->addSelect('staff_position', 'Pozície v staffe (hromadný výber CTRL+klik):', $staff_positions, 8, true) );
	$staff->addItem( $form->addText('staff_email', 'Staff email:')->addRule(AppForm::EMAIL, 'Zadaj korektný email') );
	
	$form->addSubmit('save', 'Uložiť');	
	$form->onSubmit('editFormSubmitted', $this);
	$form->collect();
	
	$form->setDefaultVals($this->profile);
	
	return $form->render();
  
  }
  
  public function editFormSubmitted(AppForm $form)
  {
	  if( $form->isValid() ) {
		  $values = $form->getValues();
		  $obj = new AdmUsersModel;
		  $result = $obj->save($values);
		  if($result){
				redirect('admin/users/list', SAVE_OK);
		  }
		  else{
			  $this->flashError(SAVE_FAILED);
			  $form->setDefaultVals();
		  }
	  }
	  else {
		  $form->setDefaultVals();
	  }
	  
  }
  
  
  private function createAddUserPermisionsForm($id)
  {
    $obj = new AdmUsersModel;
	
	$modules = $obj->getModules($id);
	
	$form = new Form('user-permisions', 'user-permisions', BASEPATH.'/admin/users/addUsersPermision');
	$form->addHidden('uid', $id);
	$form->addSelect('machine_name', $modules, USERS_ADMIN_FRM_ADD_USERS_PERMISIONS_MACHINE_NAME);
	$form->addCheckbox('view', 1, USERS_ADMIN_FRM_ADD_USERS_PERMISIONS_VIEW, '', 0,'frm-checkbox', true);
	$form->addCheckbox('add', 1, USERS_ADMIN_FRM_ADD_USERS_PERMISIONS_ADD, '', 0,'frm-checkbox', true);
	$form->addCheckbox('edit', 1, USERS_ADMIN_FRM_ADD_USERS_PERMISIONS_EDIT, '', 0,'frm-checkbox', true);
	$form->addCheckbox('delete', 1, USERS_ADMIN_FRM_ADD_USERS_PERMISIONS_DELETE, '', 0,'frm-checkbox', false);
	$form->addSubmit('addnew', ADD);
	
	return $form->render();
  }

  private function createUserPermisionsTable($id)
  {
	$obj = new AdmUsersModel;
	
	$perms = $obj->getUsersPermisions($id);
	
	if(!$perms) return '';
	
	$form = new Form('user-perms-table', 'user-perms-table', BASEPATH.'/admin/users/saveUsersPermisions');
	
	$form->addHidden('uid', $id);
	
	$frmStart = $form->start();
	
	$thead = '
			  <thead>
				<tr>
				  <td>'.NAME.'</td>
				  <td><img src="images/icon-view.jpg" alt="'.USERS_ADMIN_FRM_ADD_USERS_PERMISIONS_VIEW.'" title="'.USERS_ADMIN_FRM_ADD_USERS_PERMISIONS_VIEW.'" /></td>
				  <td><img src="images/icon-add.jpg" alt="'.USERS_ADMIN_FRM_ADD_USERS_PERMISIONS_ADD.'" title="'.USERS_ADMIN_FRM_ADD_USERS_PERMISIONS_ADD.'" /></td>
				  <td><img src="images/icon-edit.jpg" alt="'.USERS_ADMIN_FRM_ADD_USERS_PERMISIONS_EDIT.'" title="'.USERS_ADMIN_FRM_ADD_USERS_PERMISIONS_EDIT.'" /></td>
				  <td><img src="images/icon-delete.jpg" alt="'.USERS_ADMIN_FRM_ADD_USERS_PERMISIONS_DELETE.'" title="'.USERS_ADMIN_FRM_ADD_USERS_PERMISIONS_DELETE.'" /></td>
				  <td>&nbsp;</td>
				</tr>
			  </thead>
	';
	$trs = '';
	foreach($perms as $perm){
		$form->addCheckbox($perm['id'].'__view', 1, '', '', $perm['view']);
		$form->addCheckbox($perm['id'].'__add', 1, '', '', $perm['add']);
		$form->addCheckbox($perm['id'].'__edit', 1, '', '', $perm['edit']);
		$form->addCheckbox($perm['id'].'__delete', 1, '', '', $perm['delete']);
		
		$tds = Html::elem('td', null, $perm['name']);
		$tds .= Html::elem('td', array('style'=>'width:30px;text-align:center'), $form->renderSingleControl($perm['id'].'__view'));
		$tds .= Html::elem('td', array('style'=>'width:30px;text-align:center'), $form->renderSingleControl($perm['id'].'__add'));
		$tds .= Html::elem('td', array('style'=>'width:30px;text-align:center'), $form->renderSingleControl($perm['id'].'__edit'));
		$tds .= Html::elem('td', array('style'=>'width:30px;text-align:center'), $form->renderSingleControl($perm['id'].'__delete'));
		$delIcon = Html::elem('img', array('src'=>BASEPATH.'/images/icon-delete.jpg', 'style'=>'border:none;', 'alt'=>DELETE, 'title'=>DELETE));
		$delA = Html::elem('a', array('href'=>BASEPATH.'/admin/users/deleteUsersPermision/'.$perm['id'].'?destination=admin/users/usersPermisions/'.$id, 'class'=>'delete'), $delIcon);
		$tds .= Html::elem('td', array('style'=>'width:30px;text-align:center'), $delA);
		$trs .= Html::elem('tr', null, $tds);
	}
	
	$table = Html::elem('table', array('class'=>'admin-table', 'cellspacing'=>'0'), $thead . $trs);
	
	$form->addSubmit('save', SAVE);
	
	$frmEnd = $form->end();
	
	return '<hr />' . $frmStart . $form->renderSingle('uid') . $table . $form->renderSingle('save') . $frmEnd;
	
  }
  
    /**
	 *
	 * @return string new pwd
	 */
	private function generatePwd()
	{
		$chars = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9");

		$range = count($chars) - 1;

		$pwd = '';
		for($i = 1; $i < 9; $i++){
			$pwd .= $chars[rand(0,$range)];
		}

		return $pwd;

	}

}