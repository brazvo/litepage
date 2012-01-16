<?php
class Frontend extends RatingsBaseController
{ 
  
	private $settings;
	
	private $frmValues;

	// Constructor
	public function __construct()
	{
		parent::__construct();
	}


	// Methods
	/***************************************** ACTIONS ***/ 
	function actionRate($cid)
	{
	    $GET = Vars::get('GET');
		$des = $GET->des;
		$am = $GET->am;
		
		$obj = new RatingsModel;
		$res = $obj->saveRating($cid, $am);

		if($res) {
			setcookie( md5( baseUrl()."ratings_{$cid}" ), 1, time()+30*24*60*60, '/');
			redirect($des, RAV_RATING_SAVED_MESSAGE);
		}
		else {
			redirect($des, RAV_RATING_UNSAVED_MESSAGE);
		}

		
	}
   
	/***************************************** RENDERERS ***/
	function renderRate()
	{
		$this->template->title = RAV_RENDER_RATE_TITLE;
		$this->template->content = null;
	}


	/***************************************** FACTORIES ***/
	

	/*************************************** OTHER METHODS ***/
  
}
