<?php
class Reports extends Controller {
    
    private $leg;
	
	private $report;
	
	private $destination;
	
	private $isAjax = false;
	
    public function __construct() {
        parent::__construct();
    }
	
	protected function startUp()
	{
		if( !$this->user['logged'] ) {
			redirect('login');
		}
		
		$GET = Vars::get('GET');
		if( isset($GET->destination) ) $this->destination = $GET->destination;
		if( isset($GET->isajax) && $GET->isajax ) $this->isAjax = TRUE;
	}
    
    ////////////////////////////// Actions
    public function actionTourLeg($id)
    {
		$mod = new ReportModel;
		$this->leg = $mod->findTourLeg($id);
    }
	
	public function actionShow($id)
	{
		$mod = new ReportModel;
		$this->report = $mod->find($id);
	}
    
    ////////////////////////////////// before render
    protected function beforeRender()
    {
        $this->template->bodyclass = "reports";
        $this->template->CSS->add('css/reports.css');
    }

        ////////////////////////////// Renderes
    public function renderTourLeg($id)
    {
        $this->template->title = "Report leg #{$this->leg['leg_number']} of {$this->leg['tour_title']} ({$this->leg['tour_year']})";
		$this->template->leg = $this->leg['title'];
		$this->template->vid = $this->user['user'];
        $this->template->reportForm = $this['tourLegForm']; 
        
    }
	
	public function renderShow()
	{
		$this->template->title = "Leg #{$this->report['leg_number']} of {$this->report['title']}";
		$this->template->from = $this->report['from'];
		$this->template->to = $this->report['to'];
		$this->template->callsign = $this->report['callsign'];
		$this->template->dep_date = $this->report['dep_date'];
		$this->template->dep_time = $this->report['dep_time'];
		$this->template->arr_time = $this->report['arr_time'];
		$this->template->comment = $this->report['comment'];
		$this->template->validator_comment = $this->report['validator_comment'];
		
		$this->template->close = $this->isAjax ? Html::elem('div')->setClass('close') : "";
	}
    
	
    ////////////////////////////// Factories
    public function createControlTourLegForm()
    {
        $years = array( date("Y")-1, date("Y") );
		
		$form = new AppForm('TourLegForm');
		$form->setRenderer('table');
		$form->addHidden('tour_id')->setValue($this->leg['tour_id']);
		$form->addHidden('tour_leg_id')->setValue($this->leg['id']);
		$form->addHidden('from')->setValue($this->leg['from_icao']);
		$form->addHidden('to')->setValue($this->leg['to_icao']);
		$form->addHidden('vid')->setValue($this->user['user']);
		$form->addHidden('leg_number')->setValue($this->leg['leg_number']);
		$form->addHidden('type')->setValue("p");
		$form->addText('callsign', 'Callsign', 7, 10)
				->addRule(AppForm::FILLED, 'Callsign must be filled.')
				->addRule(AppForm::REGEX, 'Callsign may contain alpha-numeric characters only', '/[a-z0-9]+/i');
		$form->addDatepicker('dep_date', 'Date of Departure', 'DD.MM.YYYY', $years)->setValue( date("d.m.Y") );
		$form->addTimepicker('dep_time', 'Time of TO (UTC)');
		$form->addTimepicker('arr_time', 'Time of TD (UTC)');
		$form->addTextarea('comment', 'Comments / ATCs online', 40, 5);
		$form->addSubmit('saveReport', 'Save report');
		$form->onSubmit('tourLegFormSubmitted', $this);
		$form->collect();
		
		return $form;
    }
	
	public function tourLegFormSubmitted(AppForm $form)
	{
		if( $form->isValid() ) {
			$values = $form->getValues();
			
			$mod = new ReportModel();
			$res = $mod->saveNewTourLeg($values);
			
			if($res) {
				redirect($this->destination, SAVE_OK);
			}
			else {
				$this->flashError(SAVING_FAILED);
				$form->setDefaultVals();
			}
		}
		else {
			$form->setDefaultVals();
		}
	}
}
?>
