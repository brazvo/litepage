<?php
class User extends BaseController {
    
    
    public function __construct() {
        parent::__construct();
    }
    
    private $profile;
    
    ////////////////////////////// Actions
    public function actionShow($vid)
    {
        $mod = new UserModel();
        $this->profile = $mod->find($vid);
        
        if(!$this->profile) {
            redirect('error/show/404', USER_ERROR_NO_PROFILE);
        }
    }
    
    public function actionEditProfile()
    {
        if(!$this->user['status']) {
            redirect('error/show/403', NO_PERMISSION_TO_SEE_CONTENT);
        }
        
        $mod = new UserModel();
        $this->profile = $mod->findBySession();
        
        if(!$this->profile) {
            redirect('error/show/404', USER_ERROR_NO_PROFILE);
        }
    }
    
    ////////////////////////////////// before render
    protected function beforeRender()
    {
        $this->addClass('user');
    }

        ////////////////////////////// Renderes
    public function renderShow($vid)
    {
        $this->template->title = USER_SHOW_TITLE;
        $this->template->profile = $this->profile;
		$this->template->vid = $vid;
        $this->template->blog = $this['myBlog'];
        $this->template->photoalbums = $this['myPhotoAlbums'];
        
    }
    
    
    public function renderEditProfile($id)
    {
        $GET = Vars::get('GET');
		
		if( isset($GET->migration) ) {
			$this->template->migration = USER_EDIT_PROFILE_MIGRATION;
			$this->template->migrationTips = USER_EDIT_PROFILE_MIGRATION_TIPS;
		}
		else {
			$this->template->migration = NULL;
			$this->template->migrationTips = NULL;
		}
		
		$this->template->title = USER_EDIT_PROFILE_TITLE;
        $this->template->profileForm = $this['profileEditForm'];
        
    }
	
    ////////////////////////////// Factories
    public function createControlMyBlog()
    {
        $blog = db::fetchAll("SELECT bl.title, bl.body, ct.id, ct.last_update FROM content ct
                              LEFT JOIN blog bl ON bl.id = ct.content_id
                              WHERE ct.uid = %i AND ct.content_type_machine_name = 'blog'
                              ORDER BY ct.last_update DESC
                              LIMIT 5", $this->profile['id']);
        if($blog) {
            $articles = Html::elem('div')->setClass('articles');
            foreach($blog as $article) {
                $body  = strip_tags( $this->texy->process( $article['body'] ) );
                if(mb_strlen($body, Config::core('encoding')) > 128) {
                    $body = mb_substr($body, 0, 128, Config::core('encoding')) . '...';
                }
                $date = slovdate($article['last_update']);

                $dateSpan = Html::elem('span')->setClass('date')->setCont("&nbsp;({$date})");
                $bodyDiv = Html::elem('div')->setClass('body')->setCont($body);
                $titleSpan = Html::elem('span')->setClass('title')->setCont($article['title']);
                $titleA = Html::elem('a')->href(Application::link("content/show/{$article['id']}"))->setCont($titleSpan);
                $titleDiv = Html::elem('div')->setCont($titleA . $dateSpan);

                $row = Html::divBlock($titleDiv . $bodyDiv, 'article-row');

                $articles->add($row);
            }

            return $articles;
        }
        else {
            return null;
        }
    }
    
    
    public function createControlMyPhotoAlbums()
    {
        $pas = db::fetchAll("SELECT pa.title, ct.id, ct.last_update, ci.image_name FROM content ct
                              LEFT JOIN photoalbum pa ON pa.id = ct.content_id
                              LEFT OUTER JOIN content_images ci ON ci.content_id = ct.id
                              WHERE ct.uid = %i AND ct.content_type_machine_name = 'photoalbum'
                              GROUP BY ct.id
                              ORDER BY ct.last_update DESC
                              LIMIT 5", $this->profile['id']);

        if($pas) {
            $albums = Html::elem('div')->setClass('albums');
            foreach($pas as $album) {
                
                $date = slovdate($album['last_update']);
                $img = Html::img(Application::imgSrc("thumb_".$album['image_name']), $album['image_name'], 75);
                $imgA = Html::elem('a')->href(Application::link("content/show/{$album['id']}"))->setCont($img)->title($album['title'].' - '.$date);

                $albums->add($imgA);
            }

            return $albums;
        }
        else {
            return null;
        }
    }
    
    
    public function createControlProfileEditForm()
    {
        $form = new AppForm('prefileEdit');
        
        $form->addText('name', HUM_NAME)->addRule(AppForm::FILLED, REGISTRATION_FORM_NAME_RULE_FILLED);
        $form->addText('surname', SURNAME)->addRule(AppForm::FILLED, REGISTRATION_FORM_SURNAME_RULE_FILLED);
        $form->addText('email', EMAIL)->addRule(AppForm::EMAIL, REGISTRATION_FORM_EMAIL_RULE_EMAIL);
        $form->addHidden('oldemail')->setValue($this->profile['email']);
        $form->addTextarea('profile', REGISTRATION_FORM_PROFILE, 67, 7)
            ->addDescription(REGISTRATION_FORM_PROFILE_DESC);
        $form->addCheckbox('newsletter', REGISTRATION_FORM_NEWSLETTER);
        $form->addCheckbox('events_reminder', REGISTRATION_FORM_EVENTS_REMINDER);
		
		$contacts = $form->addBlock('contacts')->setCaption(REGISTRATION_FORM_CONTACTS_CAPTION);
		
		$contacts->addItem( $form->addCheckbox('publicate_email', REGISTRATION_FORM_PUBLICATE_EMAIL) );
		$contacts->addItem( $form->addText('contact_skype', REGISTRATION_FORM_CONTACT_SKYPE) );
		$contacts->addItem( $form->addText('contact_icq', REGISTRATION_FORM_CONTACT_ICQ) );
		$contacts->addItem( $form->addText('contact_gtalk', REGISTRATION_FORM_CONTACT_GTALK)->addRule(AppForm::EMAIL, REGISTRATION_FORM_CONTACT_GTALK_RULE_EMAL) );
		
        $form->addSubmit('update', UPDATE);
        
        $form->onSubmit('profileEditFormSubmitted', $this);
        
        $form->setDefaultVals($this->profile);
        
        $form->collect();
        
        return $form->render();
        
    }
    
    
    public function profileEditFormSubmitted(AppForm $form)
    {
        if( $form->isValid() ) {
            $values = $form->getValues();
            unset($values['FILES']);
            
            $mod = new UserModel();
            $result = $mod->save($values);
            if($result) {
                redirect('user/edit-profile', USER_CHANGES_WERE_SAVED);
            }
            else {
                $this->flashError(USER_EMAIL_ERROR);
            }
        }
        else{
            $form->setDefaultVals();
        }
    }
}
?>
