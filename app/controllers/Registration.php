<?php
class Registration extends BaseController {
    
    
    public function __construct() {
        parent::__construct();
    }
    
    
    ////////////////////////////// Actions
    public function actionShow()
    {
		// if user is logged do not allow registration
		if($this->user['logged']) {redirect();}
    }
    
    
    protected function beforeRender()
    {
        $this->addClass('registration');
    }

        ////////////////////////////// Renderes
    public function renderShow()
    {
        $this->template->title = REGISTRATION_SHOW_TITLE;
        $this->template->info = REGISTRATION_SHOW_INFO;
        $this->template->form = $this['registrationForm'];
    }
    
    ////////////////////////////// Factories
    public function createControlRegistrationForm()
    {
        $form = new AppForm('regForm');
        
        $form->addText('user', REGISTRATION_FORM_USER)
                ->addRule(AppForm::FILLED, REGISTRATION_FORM_USER_RULE_FILLED)
                ->addRule(AppForm::INTEGER, REGISTRATION_FORM_USER_RULE_INTEGER);
        $form->addPassword('password', PASSWORD)
                ->addRule(AppForm::FILLED, REGISTRATION_FORM_PASSWORD_RULE_FILLED)
                ->addRule(AppForm::REGEX, REGISTRATION_FORM_PASSWORD_RULE_REGEX, '/^.{6,24}$/i');
        $form->addPassword('pwdconfirm', REGISTRATION_FORM_PWD_CONFIRM)
                ->addRule(AppForm::EQUAL, REGISTRATION_FORM_PASSWORD_RULE_EQUAL, 'password');
        $form->addText('name', HUM_NAME)
                ->addRule(AppForm::FILLED, REGISTRATION_FORM_NAME_RULE_FILLED);
        $form->addText('surname', SURNAME)
                ->addRule(AppForm::FILLED, REGISTRATION_FORM_SURNAME_RULE_FILLED);
        $form->addText('email', EMAIL)
                ->addRule(AppForm::EMAIL, REGISTRATION_FORM_EMAIL_RULE_EMAIL);
        $form->addCheckbox('newsletter', REGISTRATION_FORM_NEWSLETTER, true);
        $form->addCheckbox('events_reminder', REGISTRATION_FORM_EVENTS_REMINDER);
        $form->addCaptcha('secode', REGISTRATION_FORM_CAPTCHA, REGISTRATION_FORM_CAPTCHA_RULE, 5);
        
        $form->addSubmit('register', SEND);
        
        $form->onSubmit('regFormSubmitted', $this);
        
        $form->collect();
        
        return $form;
    }
    
    
    public function regFormSubmitted(AppForm $form)
    {
        if($form->isValid()) {
            $values = $form->getValues();
            
            $mod = new RegistrationModel();
            $res = $mod->saveNew($values);

            if(!$res) {
                errorWrite('Registration:line:65 - Zlyhal zápis do databázy');
            }
            else if($res === 'email'){
                $this->flashError(USER_EMAIL_ERROR);
                $form->setDefaultVals();
            }
            else {
                redirect('user/edit-profile', REGISTRATION_PROFILE_CREATED);
            }
        }
        else {
            $form->setDefaultVals();
        }
    }
}
?>
