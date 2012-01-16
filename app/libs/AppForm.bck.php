<?php 
/**
 * PHP Class to create forms 
 * 
 * <code><?php
 * require('class.Form.php');
 * $form = new Form();
 * ? ></code>
 * 
 * ==============================================================================
 * 
 * @version $Id: class.Form.php, v1.60 21:02 10/21/2010 $
 * @copyright Copyright (c) 2010 Branislav Zvolensky (http://www.mbmartworks.sk)
 * @author Branislav Zvolensky <zvolensky@mbmartworks.sk>
 * @license http://opensource.org/licenses/gpl-license.php GNU General Public License (GPL)
 * 
 * ==============================================================================
 * new in v 1.62 add fluent method __toString for direct printing of form. setRenderer() was moved to
 * __construct() and to static method getHtml(). Now there are three ways to render form:
 * 1. $form->render()
 * 2. echo $form // alias for $form->render(), optionally before render you can set renderer: $form->setRenderer('table');
 * 3. statically Form::getHtml($formName, [$renderMethod]) $renderMethod may be 'newline' or 'table';
 *
 * new in v 1.60
 * Added new rules for file upload verification LANDSCAPE, PORTRAIT, MIN_DIMENSIONS, MAX_DIMENSIONS
 * MIN_WIDTH, MAX_WIDTH, MIN_HEIGHT, MAX_HEIGHT and system erros messages for file upload.
 *
 * new in v 1.51
 * added parameter $errMessage into method addProtection(). Default error message Illegal submition can be
 * replaced by the value if this parameter.
 *
 * new in v 1.50
 * All this library was reworked from array system to objects. Each element is instance of IFormElements interface.
 * It now allows fluent adding parameters and properties settings (description is in formexample.php). There were new
 * rules added too. Most of them are for Files validing: UPLOADED, MAX_FILE_SIZE, IMAGES_ONLY, EXTENSIONS, EXTENSION.
 * For Select validation there is new rule SKIPFIRST. In some cases you add as the first option in select box something
 * like: -- Select from list --. The rule SKIPFIRST controls if the user left this first option selected.
 * Captcha was reworked too. Now it does not save the captcha image into file system, but it draws
 * the image into screen directly. Drawing class for captcha is now in the separate file and you can set the path
 * to the file at the end of this file into static property Form::$pathToCaptcha.
 * Rendering of the form was changed too. Form->render() method was removed and new method was added Form->collect(),
 * witch collects the form internally. This method must called at the end of the form declaration. Generating Html
 * code of the form is now called via static function Form::getHtml(string $formname, [string $rendermethod]).
 * Parameter $rendermethod may be 'table' or 'newline', default is 'newline'
 * 
 * 
 * new in v 1.01:
 * Added rule: Form::REGEX, now you can put own regex as a rule for form inputs. In example regex '/^[^<'\"]+$/i' do
 * not allow put html entities into form input, so you can simply save you forms in front of SQL or Script injections.
 *
 * new in v 1.00:
 * Added new methods: renderSingleLabel and renderSingleControl. It is useful when you want have more control by custom rendering of the form.
 * 
 * Method render() now receives parameter $method wich can be set to 'newline' or 'table' (default is 'newline'), when you set it to newline
 * labels and controls will be rendered on new line. When you set it to table, labels and controls will be put to table rows so they will be
 * appeared in one line. Also the label and control containers wil have classes now: frm-label and frm-control, so they can be formatted by CSS.
 *
 * Added new Factories (methods) addTimepicker and addDatepicker
 * addTimepicker receives these parameters: $name, $label, $selectedHour, $selectedMinute
 * addDatepicker receives these parameters: $name, $label, $format, $selectedDay, $selectedMonth, $selectedYear, $years
 * - $format can be YYYY-MM-DD or DD.MM.YYYY, $years is array such as: array(2010, 2011, 2012)
 *
 * added new method addGroupDescription(). This method puts a description to grouped elements such as:
 * Radio, CheckboxGroup, Timepicker, Datepicker
 * addDescription method now use only for single elements
 *
 * new in v 0.92:
 * Added method insertContent(). You add some content into form. The content will appear between form items where you put it.
 *
 * new in v 0.90:
 * Added new rules for numeric inputs:
 * NUMERIC - checks if input is numeric. Example: $form->addRule('field_name', Form::NUMERIC, 'The input value must be numeric.');
 * MIN - checks if input is minimally this number. Example: $form->addRule('field_name', Form::MIN, 5, 'The input value must at least 5.');
 * MAX - checks if input is maximally this number. Example: $form->addRule('field_name', Form::MAX, 10, 'The maximum input value must be 10.');
 * RANGE - checks if input is in the range of two values. Example: $form->addRule('field_name', Form::RANGE, 5, 10, 'The input value may be between 5 and 10 only.');
 *
 * Added new rendering method renderSingle(). Useful if you in example need render a single form element in a table cell
 * or in separate blocks. Usage: $form->renderSingle('field_name');
 *
 * Now you can add class names to form elements. See bellow onto add[Elements] method and check where the methods await $className
 * parameter. Example: $form->addText('field_name', 'Label', 'Value', 'className');
 * If you do not put this parameter, the default value is set to: frm-text for text input, frm-select for select input, etc.
 * 
 * Added new validation routine. After submiting form you can check it if it is valid by putting this code to you PHP page:
 * if(Form::$isvalid){   //...do something if it is valid...  }else{  echo $_SESSION['error']; //...do something if it is not valid... }
 *
 * new in v 0.82:
 * Added new rule EQUAL. It is useful if some field must be equal to another such as password confirmation
 *
 * new in v 0.80:
 * Added 'Required star' to items with rule FILLED. You can change color and size 
 * in your CSS adding rules for element span.required {};
 * Added new method: addDescription(). Calling: $object->addDescription('item_name', 'description')
 * It useful if you want add description to a form item but you do not want to put it into Label.
 * It also can be styled separately in your CSS file - div.frm-item-description{};
 *
 * new in v 0.70:
 * added Captcha input type with rule CAPTCHA
 *
 * ==============================================================================
 * Usage of this script is on own risk. The autor does not give you any warranty.
 * If you will use this script in your files you must place these header informations with the script.
 */
class AppForm {

	// Constants
	/** Validators */
	const FILLED = 'filled';
	const EMAIL = 'email';
	const CAPTCHA = 'captcha';
	const EQUAL = 'equal';
	const NUMERIC = 'numeric';
	const RANGE = 'range';
	const MIN = 'min';
	const MAX = 'max';
	const REGEX = 'regex';
	const SKIPFIRST = 'skipfirst';
	
	// Rules for Files
	const UPLOADED = 'uploaded';
	const MAX_FILE_SIZE = 'max_file_size';
	const IMAGES_ONLY = 'images_only';
	const EXTENSIONS = 'extensions';
	const EXTENSION = 'extension';
	const LANDSCAPE = 'landscape';
	const PORTRAIT = 'portrait';
	const MIN_DIMENSIONS = 'min_dimensions';
	const MAX_DIMENSIONS = 'max_dimensions';
	const MIN_WIDTH = 'min_width';
	const MAX_WIDTH = 'max_width';
	const MIN_HEIGHT = 'min_height';
	const MAX_HEIGHT = 'max_height';
	
	// Properties
	
	/** @var string Form name */
	public static $pathToCaptcha;
	
	/** @var string Form name */
	private $formName;
	
	/** @var string Form token */
	private $formToken;
	
	/** @var string Form old token */
	private $formOldToken;
	
	/** @var string id prefix */
	private $idPref;
	
	/** @var string validation string */
	private $valStr = '';
	
	/** @var bool isvalid status */
	public $isvalid;
	
	/** @var array formObjects Array */
	public static $formObjects;
	
	/** @var bool error status */
	public $isError = FALSE;
	
	/** @var array error messages*/
	public $errors = array();
	
	/** @var bool return */
	private $return = FALSE;
	
	/** @var int break line index*/
	private $breakLineIndex = 1;
	
	/** @var int content index*/
	private $contentIndex = 1;
	
	/** @property array $form Form element prototype */
	public $form = array('start' => "<form%attributes%>", 'end' => "</form>");
	
	/** @var array form elements conainer */
	public $element = array();
	
	/** @property array $attributes Form attributes */
	private $attributes = array();
	
	/** @var mixed output for render */
	private $output;
	
	/** @var string Name of renderer class */
	private $renderer;
	
	/** @var string Submit button name */
	private $submit;
	
	/** @var array onSubmit event callback */
	public $onSubmit;
	
	/** @var array POST DATA */
	private $postData;
	
	/** @var array POST */
	public static $POST;
	
	/** @var array $forms */
	private static $forms;
	
	/** @var array $forms */
	private static $formHtmlStatements;
	
	/** @var array FILES DATA */
	private $filesData;

	// Constructor
	public function __construct($name=NULL) {
	
		if($name){
		    $this->formName = $name;
		    $this->formToken = sha1(date('U'));
		    $this->attributes['id'] = 'form' . ucfirst($name);
		    $this->idPref = 'form' . ucfirst($name) . '-'; // set id prefix
		}
		else {
		    $this->formName = 'form_BZ_FORM';
		    $this->formToken = sha1(date('U'));
		    $this->idPref = 'form-'; // set id prefix
		}
		$this->attributes['method'] = 'post'; // set post as default
		
		$this->setRenderer('newline');
		
		$this->setPostData($_POST, $_FILES);
		
		if( isset( $_SESSION['__BZ_FORM']['token'] ) ) {
		    $this->formOldToken = $_SESSION['__BZ_FORM']['token'];
		}
		
	}
	
	// Methods
	
	/**
	 * Collects attributes for Form Tag
	 * @return string The string of HTML attributes
	 */
	private function collectAttributes() {
	    $ret = '';
	    foreach($this->attributes as $key => $val) {
		$ret .= ' ' . $key . '="' . $val . '"';
	    }
	    return $ret;
	}
	
	
	/******************** FORM ATTRIBUTES SETTERS / GETTERS ********************/
	
	/**
	 * Set style attribute for Form Tag
	 * @param string $styleString
	 * @return obejct Form
	 */
	public function setStyle($styleString = null) {
	    if(!$styleString){
		echo 'Error: empty parameter in setStyle() method.';
	    }
	    else{
		$this->attributes['style'] = $styleString;
	    }
	    return $this;
	}
	
	/**
	 * Set class attribute for Form Tag
	 * @param string $class
	 * @return obejct Form
	 */
	public function setClass($class = null) {
	    if(!$class){
		echo 'Error: empty parameter in setClass() method.';
		exit;
	    }
	    else{
		$this->attributes['class'] = $class;
	    }
	    return $this;
	}
	
	/**
	 * Set method attribute for Form Tag
	 * @param string $method
	 * @return obejct Form
	 */
	public function setMethod($method = null) {
	    if(!$method){
		echo 'Error: empty parameter in setMethod() method.';
		exit;
	    }
	    else{
		$this->attributes['method'] = $method;
	    }
	    return $this;
	}
	
	/**
	 * Set action attribute for Form Tag
	 * @param string $action
	 * @return object Form
	 */
	public function setAction($action = null) {
	    if(!$action){
		echo 'Error: empty parameter in setAction() method.';
		exit;
	    }
	    else{
		$this->attributes['action'] = $action;
	    }
	    return $this;
	}
	
	/********************** START AND END FORM TAGS ******************/
	
	/**
	 * @return string HTML open tag
	 */
	public function start()
	{		
	    return preg_replace('/%attributes%/', $this->collectAttributes(), $this->form['start']);
	}
	
	/**
	 * @return string HTML ending tag
	 */
	public function end()
	{   
	    return $this->form['end'];
	}
	
	public function collect()
	{
	    // save form into session
	    // $_SESSION['__BZ_FORM'][$this->formName] = $this;
	    
	    self::$forms[$this->formName] = $this;
	    
	    if($this->isSubmitted()){
		
		// test checkboxes
		$post = $this->getPostData();
		foreach($this->element as $element) {
		    if('checkbox' == $element->getType()) {
			if(isset($post[$element->getName()])) {
			    $this->element[$element->getName()]->setValue(1);
			}
			else {
			    $this->element[$element->getName()]->setValue(0);
			}
		    }
		}
		
		$this->validate();
		
		$callback = $this->getSubmitEvent();
		
		$callback($this);
	    }
	}
	
	
	/************************ RENDERER ************************/
	
	/**
	 * @param string $method Rendering method of form. Avialable [ newline | table ]
	 */
	public function render() {
		
		$renderer = $this->getRenderer();
		
		$renderer->setFormStart($this->start());
		
		$renderer->setFormEnd($this->end());
		
		$renderer->setElements($this->element);
		
		$renderer->setErrors($this->errors);
		
		self::$formHtmlStatements[$this->formName] = $renderer->getCompleteForm();
		
		return self::getHtmlStatement($this->formName);
	
	}
	
	/**
	 * @param string $renderMethod
	 * @return object
	 */
	function setRenderer($renderMethod) {
	    $renderClass = ucfirst($renderMethod) . 'Renderer';
	    
	    if(!class_exists($renderClass)) {
		die("Error: Renderer - $renderClass does not exists");
	    }
	    else {
		$this->renderer = $renderClass;
	    }
	    return $this;
	}
	
	/**
	 * @return object IFormRenderer
	 */
	function getRenderer()
	{
	    return new $this->renderer;
	}
	
	
	/********************************* FORM ELEMENTS FACTORIES ********************/
	
	/**
	 * Add token to form
	 * @param string $errMessage
	 */
	function addProtection($errMessage = null)
	{
	    if(!$errMessage) $errMessage = 'Illegal submition';
	    
	    $_SESSION['__BZ_FORM']['token'] = $this->formToken;
	    
	    $this->element['frm-token'] = new InputElement('frm-token', null, $this->idPref, 'hidden');
	    $this->element['frm-token']->addRule('token', $errMessage, $this->formOldToken);
	    $this->element['frm-token']->setValue($this->formToken);
	}
	
	/**
	 * Adds text input
	 * @param string $name Name of the element
	 * @param string $label Label for the element
	 * @param integer $size Size of the element
	 * @param integer $maxsize Maximum size of the element
	 */
	public function addText($name=NULL, $label=NULL, $size='40', $maxsize='256') {
		
		if(!$name){
			echo 'Error: Set name of text input.';
			exit;
		}
		else{
			
			return $this->element[$name] = new InputElement($name, $label, $this->idPref, 'text', array('size'=>$size, 'maxlength'=>$maxsize));
			
		}
	
	}
	
	/**
	 * Adds password input
	 * @param string $name Name of the element
	 * @param string $label Label for the element
	 * @param integer $size Size of the element
	 * @param integer $maxsize Maximum size of the element
	 */
	public function addPassword($name=NULL, $label=NULL, $size='40', $maxsize='256') {
		
		if(!$name){
			echo 'Error: Set name of password input.';
			exit;
		}
		else{
			return $this->element[$name] = new InputElement($name, $label, $this->idPref, 'password', array('size'=>$size, 'maxlength'=>$maxsize));
		}
	
	}
	
	/**
	 * Adds password input
	 * @param string $name Name of the element
	 */
	public function addHidden($name=NULL) {
		
		if(!$name){
			echo 'Error: Set name of hidden input.';
			exit;
		}
		else{
			return $this->element[$name] = new InputElement($name, null, $this->idPref, 'hidden');
		}
	
	}
	
	/**
	 * Adds captcha input
	 * @param string $name
	 * @param string $label
	 * @param string $errMessage
	 * @param integer $inpsize Input Size, Number of characters
	 * @param integer $imgwidth
	 * @param integer $imgheight
	 */
	public function addCaptcha($name=NULL, $label=NULL, $errMessage = null, $inpsize='6', $imgwidth='120', $imgheight='40') {
		
		if(!$name){
			die('Error: Set name of input.');
		}
		else{
			if(!$errMessage) $errMessage = 'Refill the security code correctly';
			$this->element[$name] = new CaptchaElement($name, $label, $this->idPref, 'captcha', $imgwidth, $imgheight, $inpsize);
			$this->element[$name]->addRule(self::CAPTCHA, $errMessage);
		}
	
	}
	
	/**
	 * Adds submit button
	 * @param string $name Name of the element
	 * @param string $value Label for the element
	 */
	public function addSubmit($name=NULL, $value=NULL) {
		
		if(!$name){
			echo 'Error: Set name of submit input.';
			exit;
		}
		elseif(!$value){
			echo 'Error: Set value of submit input.';
			exit;
		}
		else{
			$this->submit = $name;
			return $this->element[$name] = new ButtonElement($name, $value, $this->idPref, 'submit');
		}
	}
	
	/**
	 * Adds reset button
	 * @param string $name Name of the element
	 * @param string $value Label for the element
	 */
	public function addReset($name=NULL, $value=NULL) {
		
		if(!$name){
			echo 'Error: Set name of reset button.';
			exit;
		}
		elseif(!$value){
			echo 'Error: Set value of reset button.';
			exit;
		}
		else{
			return $this->element[$name] = new ButtonElement($name, $value, $this->idPref, 'reset');
		}
	}
	
	/**
	 * Adds Textarea
	 * @param string $name Name of the element
	 * @param string $label Label for the element
	 * @param integer $cols Columns
	 * @param integer $rows Rows
	 */
	public function addTextarea($name=NULL, $label="", $cols='30', $rows='5') {
	
	    if(!$name){
		echo 'Error: Set name of Textarea.';
		exit;
	    }
	    else{
		return $this->element[$name] = new TextareaElement($name, $label, $this->idPref, array('cols'=>$cols, 'rows'=>$rows));
	    }

	}
	
	/**
	 * Adds Select
	 * @param string $name Name of the element
	 * @param string $label Label for the element
	 * @param array $valAndOps Values and Options in array($value => $option)
	 * @param mixed $selected Value of selected option
	 * @param integer $size
	 * @param bool $multiple Multiple select
	 */
	public function addSelect($name=null, $label=null, $valAndOps=array(), $size=null, $multiple=false) {
	
	    if(!$name){
		echo 'Error: Set name of Select.';
		exit;
	    }
	    else {
		return $this->element[$name] = new SelectElement($name, $label, $this->idPref, $valAndOps, array('size'=>$size, 'multiple'=>$multiple));
	    }

	}
	
	/**
	 * Adds Time picker
	 * @param string $name
	 * @param string $label
	 */
	public function addTimepicker($name=NULL, $label=NULL)
	{
		if(!$name){
			die('Error: Set name for Time Picker.');
		}
		else{
			for($i = 0; $i < 24; $i++){
				if($i < 10) $hours = '0'.$i;
					else $hours = $i;
				$hoursArr[$hours] = $hours; 
			}
			for($i = 0; $i < 60; $i++){
				if($i < 10) $mins = '0'.$i;
					else $mins = $i;
				$minsArr[$mins] = $mins; 
			}
			
			// Call addSelect()
			$this->element[$name.'_hour'] = new SelectElement($name.'_hour', null, $this->idPref, $hoursArr);
			$this->element[$name.'_min'] = new SelectElement($name.'_min', null, $this->idPref, $minsArr);
			
			$this->element[$name] = new ContainerElement($name, $label, $this->idPref, null, true);
			$this->element[$name]->addItem($this->element[$name.'_hour']);
			$this->element[$name]->addItem($this->element[$name.'_min']);
			
			$this->element[$name]->setCaption('HH:MM');
			
			return $this->element[$name];
			
		}
		
	}
	
	/**
	 * Adds Date picker
	 * @param string $name
	 * @param string $label
	 * @param string $format [YYYY-MM-DD | DD.MM.YYYY]
	 * @param array $years
	 */
	public function addDatepicker($name=NULL, $label=NULL, $format='YYYY-MM-DD', $years=null)
	{
		
		if(!$name){
		    die('Error: Datepicker - Set name for Date Picker.');
		}
		elseif(!in_array($format, array('YYYY-MM-DD', 'DD.MM.YYYY'))){
		    die('Error: Datepicker - the Format may be YYYY-MM-DD or DD.MM.YYYY');
		}
		else{
			if(!$years){
				$currYear = date("Y");
				$lastYear = $currYear+5;
				for($i = $currYear; $i <= $lastYear; $i++)
				{
					$yearsArr[$i] = $i;
				}
			}
			else{
				if(!is_array($years)) die('Error: DatePicker - $years must be array');
				foreach($years as $year){
					$yearsArr[$year] = $year;
				}
			}
		
			for($i = 1; $i < 13; $i++){
				if($i < 10) $months = '0'.$i;
					else $months = $i;
				$monthsArr[$months] = $months; 
			}
			for($i = 1; $i < 32; $i++){
				if($i < 10) $days = '0'.$i;
					else $days = $i;
				$daysArr[$days] = $days; 
			}
			
			$this->element[$name.'_month'] = new SelectElement($name.'_month', null, $this->idPref, $monthsArr);
			$this->element[$name.'_day'] = new SelectElement($name.'_day', null, $this->idPref, $daysArr);
			$this->element[$name.'_year'] = new SelectElement($name.'_year', null, $this->idPref, $yearsArr);
			
			$this->element[$name] = new ContainerElement($name, $label, $this->idPref, null, true);
			if($format == 'YYYY-MM-DD'){
			    $this->element[$name]->addItem($this->element[$name.'_year']);
			    $this->element[$name]->addItem($this->element[$name.'_month']);
			    $this->element[$name]->addItem($this->element[$name.'_day']);
			}
			
			if($format == 'DD.MM.YYYY') {
			    $this->element[$name]->addItem($this->element[$name.'_day']);
			    $this->element[$name]->addItem($this->element[$name.'_month']);
			    $this->element[$name]->addItem($this->element[$name.'_year']);
			}
			
			$this->element[$name]->setCaption($format);
			
			return $this->element[$name];
		}
		
	}
	
	/**
	 * Adds Radio buttons
	 * @param string $name
	 * @param string $label
	 * @param array $valuesAndCaptions $values and captions
	 */
	public function addRadio($name=NULL, $label=NULL, $valuesAndCaptions = array(), $inline=TRUE) {
		
	    if(!$name){
		echo'Error: Set name for radio';
		exit;
	    }
	    elseif(!is_array($valuesAndCaptions)){
		echo 'Error: Values and Options must be in array.';
		exit;
	    }
	    else{
		$items = array();
		$idx = 1;
		foreach($valuesAndCaptions as $val => $caption) {
		    $opt = new InputElement($name, null, $this->idPref.$idx, 'radio', null, $caption, $inline);
		    $opt->setValue($val);
		    $items[] = $opt;
		    $idx++;
		}
		return $this->element[$name] = new RadioElement($name, $label, $this->idPref, $items, $inline);
	    }
	}
	
	/**
	 * Adds Check box
	 * @param string $name
	 * @param string $caption
	 * @param bool $checked
	 * @param bool $inline
	 */
	public function addCheckbox($name=NULL, $caption=NULL, $inline=FALSE) {
	
	    if(!$name){
		echo'Error: Set name for checkbox';
		exit;
	    }
	    else{    
		return $this->element[$name] = new CheckboxElement($name, null, $this->idPref, 'checkbox', null, $caption, $inline);
	    }
	
	}
	
	/**
	 * Adds Group of Checkboxes
	 * @param string $name
	 * @param string $label
	 * @param string $caption
	 * @param array $checkboxes
	 * @param bool $inline;
	 */
	public function addCheckboxGroup($name=NULL, $label=NULL, $checkboxes, $inline=TRUE)
	{
		if(!$name){
			echo 'Error: Set name for Checkbox Group.';
			exit;
		}
		elseif(!is_array($checkboxes)){
			echo 'Error: Checkboxes must be array.';
			exit;
		}
		else{
			$items = array();
			foreach($checkboxes as $checkbox) {
			    $this->element[$checkbox['name']] = new InputElement($checkbox['name'], null, $this->idPref, 'checkbox', null, $checkbox['caption'], $inline);
			    $this->element[$checkbox['name']]->setValue($checkbox['value']);
			    $this->element[$checkbox['name']]->setGroup($name);
			    $items[] = $this->element[$checkbox['name']];
			}
			
			return $this->element[$name] = new GroupElement($name, $label, $this->idPref, $items, $inline);
		}
		
	}
	
	/**
	 * Adds Group
	 * @param string $name
	 * @param string $label
	 * @param string $caption
	 * @param bool $inline;
	 */
	public function addGroup($name=NULL, $label=NULL, $inline=false)
	{
		if(!$name){
			echo 'Error: Set name for Checkbox Group.';
			exit;
		}
		else{
			return $this->element[$name] = new GroupElement($name, $label, $this->idPref, null, $inline);
		}
		
	}
	
	/**
	 * Adds Container
	 * @param string $name
	 * @param string $label
	 * @param string $caption
	 * @param bool $inline;
	 */
	public function addContainer($name=NULL, $label=NULL, $inline=false)
	{
		if(!$name){
			echo 'Error: Set name for Container.';
			exit;
		}
		else{
			return $this->element[$name] = new ContainerElement($name, $label, $this->idPref, null, $inline);
		}
		
	}
	
	/**
	 * Adds Break Line
	 * @return object
	 */
	public function addBreak()
	{
	    $name = 'break-' . $this->breakLineIndex++;
	    return $this->element[$name] = new BreakElement($name);
	}
	
	/**
	 * Adds Content
	 * @return object
	 */
	public function addContent($value = null, $name = null)
	{
	    if(!$name) $name = 'content-' . $this->contentIndex++;
	    return $this->element[$name] = new ContentElement($name, $value, $this->idPref);
	}
	
	/**
	 * Adds File input
	 * @param string $name
	 * @param string $label
	 * @param integer $size
	 */
	public function addFile($name=NULL, $label=NULL, $size='50') {
	
	    if(!$name){
		echo 'Error: Set name for file input.';
		exit;
	    }
	    else{
		// if file input set attribute enctype for form
		if(!isset($this->attributes['enctype'])) $this->attributes['enctype'] = 'multipart/form-data';
		
		return $this->element[$name] = new FileElement($name, $label, $this->idPref, array('size'=>$size));
	    }
	
	}
	
	
	/*************************** OTHER METHODS ********************/
	
	/**
	 * Get zero values custom function for LitePage project
	 * @param string
	 * @return array
	 * Returns array with default values. Usable with SqLite and MySql
	 */
	public function getZeroValues($table=null)
	{
	  if(!$table){
	    echo 'Error: missing argument table.';
		exit;
	  }
	  else{
		if(DB_DRIVER == 'mysql'){
			$rows = db::getTableFields($table);
			foreach($rows as $row){
				$result[$row['Field']]=$row['Default'];
			}
		}
		if(DB_DRIVER == 'sqlite'){
			$rows = db::fetchAll("PRAGMA table_info($table)");
			
			
			foreach($rows as $row){
			  $result[$row['name']]=$row['dflt_value'];
			}
		}
		
		return $result;
	  }
	
	}
	
	public static function sessionCheck()
	{   
	    if(!session_id()) {
		session_start();
	    }
	    
	}
	
	public function setPostData($post, $files)
	{
	    $this->postData = $post;
	    $this->filesData = $files;
	}
	
	public function getPostData()
	{
	    return $this->postData;
	}
	
	public function getFilesData()
	{
	    return $this->filesData;
	}
	
	public function getToken()
	{
	    return $this->formToken;   
	}
	
	public function getSubmitEvent()
	{
	    return $this->onSubmit;
	}
	
	public function isSubmitted()
	{
	    if(isset($this->postData[$this->submit]))
		return true;
	    
	    return false;
	    
	}
	
	/**
	 * Validate form values
	 * @param array
	 * @return errors
	 */
	public function validate()
	{
	    
	    foreach($this->element as $name => $element) {
		$rules = $element->getRules();
		
		if($rules) {
		    foreach($rules as $rule) {
			$this->validator($rule->name, $rule->rule, $rule->message, $rule->args);
		    }
		}
	    }
	    
	    if(!$this->isError) $this->isvalid = TRUE;

	}
	
	/**
	 * @param string $elName
	 * @param string $rule
	 * @param string $message
	 * @param array $args
	 */
	function validator($elName, $rule, $message, $args)
	{
		$postData = $this->getPostData();
		
		$filesData = $this->getFilesData();
		
		$value = $postData[$elName];
		
		if($filesData) $file = $filesData[$elName];
		    else $file = null;
		
		$emailRegExp = "/^[^0-9][A-z0-9_-]+([.][A-z0-9_-]+)*[@][A-z0-9_-]+([.][A-z0-9_-]+)*[.][A-z]{2,4}$/";
		
		if($rule == 'filled' && trim($value) == '') $this->setError($message);
		
		if($rule == 'email' and trim($value) != ''){
		    if($rule == 'email' and !preg_match($emailRegExp, $value)){
			$this->setError($message);
		    }
		}
		
		if($rule == 'equal'){
		    $equalValue = $postData[$args[0]];
		    if($value != $equalValue){
			$this->setError($message);
		    }
		}
		
		if($rule == 'numeric' and !is_numeric($value)) $this->setError($message);
		
		if($rule == 'min' and (int)$value < (int)$args[0]) $this->setError($message);
		
		if($rule == 'max' and (int)$value > (int)$args[0]) $this->setError($message);
		
		if($rule == 'range' and ((int)$value < (int)$args[0] or (int)$value > (int)$args[1])) $this->setError($message);
		
		if($rule == 'regex' and !preg_match($args[0], $value)) $this->setError($message);
		
		if($rule == 'captcha' and $_SESSION['__BZ_FORM']['security_code'] != $value) $this->setError($message);
		
		if($rule == 'skipfirst' and $value == $args[0]) $this->setError($message);
		
		if($rule == 'token' and $value != $args[0]) $this->setError($message);
		
		// FILES
		if($rule == 'uploaded' and $file['error'] == '4') $this->setError($message);
		
		if($rule == 'max_file_size' and $file['size'] > $args[0]) $this->setError($message);
		
		if($rule == 'image_only'){
		    $fileExplode = explode('.', $file['name']);
		    $fileExtension = end($fileExplode);
		    if(!in_array(strtolower($fileExtension), array('gif', 'jpg', 'jpeg', 'png'))) {
			$this->setError($message);
		    }
		}
		
		if($rule == 'extensions') {
		    $fileExplode = explode('.', $file['name']);
		    $fileExtension = end($fileExplode);
		    if(!in_array(strtolower($fileExtension), $args[0])) {
			$this->setError($message);
		    }
		}
		
		if($rule == 'extension') {
		    $fileExplode = explode('.', $file['name']);
		    $fileExtension = end($fileExplode);
		    if(strtolower($fileExtension) != $args[0]) {
			$this->setError($message);
		    }
		}
		
		if($rule == 'landscape') {
		    if($file['tmp_name']) {
			    list($width, $height) = getimagesize($file['tmp_name']);
			    if($width < $height) $this->setError($message);
		    }
		}
				    
		if($rule == 'portrait') {
		    if($file['tmp_name']) {
			    list($width, $height) = getimagesize($file['tmp_name']);
			    if($width > $height) $this->setError($message);
		    }
		}
				    
		if($rule == 'min_dimensions') {
		    if($file['tmp_name']) {
			    list($width, $height) = getimagesize($file['tmp_name']);
			    if($width < $min or $height < $max) $this->setError($message);
		    }
		}
				    
		if($rule == 'max_dimensions') {
		    if($file['tmp_name']) {
			    list($width, $height) = getimagesize($file['tmp_name']);
			    if($width > $min or $height > $max) $this->setError($message);
		    }
		}
				    
		if($rule == 'max_width') {
		    if($file['tmp_name']) {
			    list($width, $height) = getimagesize($file['tmp_name']);
			    if($width > $eqval) $this->setError($message);
		    }
		}
				    
		if($rule == 'max_height') {
		    if($file['tmp_name']) {
			    list($width, $height) = getimagesize($file['tmp_name']);
			    if($height > $eqval) $this->setError($message);
		    }
		}
				    
		if($rule == 'min_width') {
		    if($file['tmp_name']) {
			    list($width, $height) = getimagesize($file['tmp_name']);
			    if($width < $eqval) $this->setError($message);
		    }
		}
				    
		if($rule == 'min_height') {
		    if($file['tmp_name']) {
			    list($width, $height) = getimagesize($file['tmp_name']);
			    if($height < $eqval) $this->setError($message);
		    }
		}
				
		// System Files errors
		if($file) {
			if($file['error'] == 1) {
				$max_upload = (int)(ini_get('upload_max_filesize'));
				$frm->setError("Nahrávaný súbor je väčší ako administrátorom stanovená veľkosť $max_upload MB.");
			}
			if($file['error'] == 3) {
				$frm->setError("Nahrávaný súbor bol nahraný len čiastočne");
			}
			if($file['error'] == 6) {
				$frm->setError("Chýba pracovný adresár pre nahranie súboru");
			}
			if($file['error'] == 7) {
				$frm->setError("Súbor sa nepodarilo zapísať na disk");
			}
			if($file['error'] == 8) {
				$frm->setError("Nastala nešpecifikovaná chyba pri nahrávaní súboru");
			}
		}
	    
	}
	
	/**
	 * Sets error
	 * @param string
	 * @return void
	 */
	public function setError($error)
	{
		if(isset($error)){
			$this->isError = TRUE;
			$this->errors[] = $error;
			$this->isvalid = FALSE;
		}
	}
	
	public function renderErrors()
	{
	
		if($this->isError){
			$output = '<ul class="error">';
			foreach($this->errors as $error){
				$output .= '<li>' . $error . '</li>';
			}
			$output .= '</ul>';
		}
		else{
			$output = '';
		}
		
		return $output;
	
	}
	
	public function onSubmit($callback)
	{   
	    $this->onSubmit = $callback;
	}
	
	public function isValid()
	{
	    return $this->isvalid;
	}
	
	public static function get($name)
	{
	    return self::$forms[$name];
	}
	
	public static function getHtml($name, $method = 'newline')
	{
	    if(!isset(self::$forms[$name])) {
		die("Error: getHtml, you are calling form that does not exists");
	    }
	    
	    self::$forms[$name]->setRenderer($method);
	    
	    return self::$forms[$name]->render();
	}
	
	public static function getHtmlStatement($name)
	{
	    if(!isset(self::$formHtmlStatements[$name])) {
		die("Error: getHtmlStatement, you are calling form that does not exists");
	    }
	    
	    return self::$formHtmlStatements[$name];
	}
	
	public function setDefaultVals($values = array())
	{
	    $post = $this->getPostData();
	    
	    if($post) {
		$values +=$post;
	    }
	    
	    if($values) {
		foreach($values as $key => $val) {
		    if(isset(self::$forms[$this->formName])) {
			if(isset(self::$forms[$this->formName]->element[$key])) {
			    self::$forms[$this->formName]->element[$key]->setValue($val);
			}
		    }
		    else {
			if(isset($this->element[$key])){
			    if('password' == $this->element[$key]->getType()){
				$this->element[$key]->setValue('');
			    }
			    elseif('captcha' == $this->element[$key]->getType()){
				$this->element[$key]->setValue('');
			    }
			    elseif('frm-token' == $key) {
				$this->element[$key]->setValue($this->formToken);
			    }
			    else {
				$this->element[$key]->setValue($val);
			    }
			}
		    }
		}
	    }
	    
	}
	
	public function getValues ()
	{
	    $post = $this->getPostData();
	    
	    // create unset array
	    unset($post['__bz_form_token']);
	    foreach($this->element as $element) {
		unset($post['frm-token']); // unset token
		if('submit' == $element->getType()) unset($post[$element->getName()]); // unset submit
		if('reset' == $element->getType()) unset($post[$element->getName()]);  // unset reset
		if('checkbox' == $element->getType() && !isset($post[$element->getName()]))
		    $post[$element->getName()] = 0; // set real checkbox values
	    }
	    
	    return $post;
	}
	
	/**
	 * Calls render method when echo or string conversion is called
	 */
	public function __toString()
	{
		return $this->render();
	}
}


class FormElement
{
    
    /** @var string Input Type or Type of element */
    protected $type;
    
    /** @var string Name of the element */
    protected $name;
    
    /** @var mixed */
    protected $value;
    
    /** @var string (html) */
    protected $label;
    
    /** @var string (html) */
    protected $caption;
    
    /** @var string (html) */
    protected $control;
    
    /** @var string */
    protected $description;
    
    /** @var array Set of rules */
    protected $rules = array();
    
    /** @var array */
    protected $attributes = array();
    
    /** @var bool */
    protected $ingroup = false;
    
    /** @var string */
    protected $groupName;
    
    function __construct($label = null)
    {
	$this->label = $label;
    }
    
    function setValue($val)
    {
	$this->value = $val;
	return $this;
    }
    
    function getValue()
    {
	return $this->value;
    }
    
    function getName()
    {
	return $this->name;
    }
    
    /**
     * Adds Description to form element
     * @param string $string
     */
    function addDescription($string)
    {
	$this->description = '<div class="frm-element-description">' . $string . '</div>';
	return $this;
    }
    
    function getDescription()
    {
	return $this->description;
    }
    
    /**
     * Set Caption
     * @param string $string
     */
    function setCaption($string)
    {
	$this->caption = '<span class="frm-caption" style="margin-left:10px">' . $string . '</span>';
	return $this;
    }
    
    /**
     * Add Rule
     * @param string $rule Rule
     * @param string $message Message
     * @param mixed $arg1
     * @param mixed $arg2
     * @param mixed $arg3
     */
    function addRule(){
	$name = $this->getName();
	$args = func_get_args();
	$this->rules[] = new Rule($name, $args);
	return $this;
    }
    
    /**
     * Get Rules
     * @return array
     */
    function getRules()
    {
	return $this->rules;
    }
    
    /**
     * Get Type
     * @return string
     */
    function getType()
    {
	return $this->type;
    }
    
    function setClass($string)
    {
	$this->attributes['class'] = $string;
	return $this;
    }
    
    function setGroup($name)
    {
	$this->groupName = $name;
	$this->ingroup = true;
	return $this;
    }
    
    function getLabel()
    {
	return $this->label;
    }
    
    function getControl()
    {
	return $this->control;
    }
    
    function inGroup()
    {
	return $this->ingroup;
    }
    
    function setAttribute($attr, $val)
    {
	$this->attributes[$attr] = $val;
    }
    
    function getAttributes()
    {
	return $this->attributes;
    }
    
    function getAttribute($attr)
    {
	if(isset($this->attributes[$attr])) {
	    return $this->attributes[$attr];
	}
	else {
	    echo 'Class InputElement / getAttribute: Attribute does not exists.';
	    exit;
	}
    }
    
    protected function collectAttributes()
    {
	$return = '';
	foreach($this->attributes as $key => $val) {
	    $return .= $key.'="'.$val.'" ';
	}
	
	return $return;
    }
}


interface IFormElements
{
    function getPrototype();
    
    function getLabel();
    
    function getControl();
}

class InputElement extends FormElement implements IFormElements
{
    
    /** @var string Html prototype */
    protected $prototype = "<input %attributes%value=\"%value%\" />%caption%%nl%";
    
    /** @var string Html breakline */
    protected $newline;
    
    /** @vat mixed checked value */
    public $checked;
    
    
    function __construct($name, $label, $idpref, $type, $attributes = null, $caption = null, $newline = false, $checked = null)
    {
	parent::__construct( ($label ? '<label>'.$label.'</label>' : null) );
	$this->name = $name;
	$this->attributes['id'] = $idpref . $name;
	$this->attributes['class'] = 'frm-'.$type;
	$this->attributes['name'] = $name;
	$this->attributes['type'] = $type;
	$caption_span = ($newline ? '<span style="margin-right:10px">' : '<span>');
	$this->caption = ($caption ? $caption_span . '&nbsp;' . $caption . '</span>' : '');
	$this->type = $type;
	$this->value = '';
	$this->checked = $checked;
	
	if($this->type == 'checkbox' or $this->type == 'radio') {
	    $this->newline = ($newline ? '' : '<br />');
	}
	else {
	    $this->newline = '';
	}
	
	if($attributes and is_array($attributes)) {
	    foreach($attributes as $key => $attribute) {
		$this->attributes[$key] = $attribute;
	    } 
	}
    }
    
    function getPrototype()
    {
	return $this->prototype;
    }
    
    function getLabel()
    {
	return parent::getLabel();
    }
    
    function getControl()
    {
	return $this->control = preg_replace(array('/%attributes%/', '/%value%/', '/%caption%/', '/%nl%/'), array($this->collectAttributes(), $this->value, $this->caption, $this->newline), $this->prototype);
    }
    
}

class CaptchaElement extends InputElement implements IFormElements
{
    
    /** @var string Html prototype */
    private $imagePrototype = '<div id="captcha-img"><img src="%path%CaptchaSecurityImages.php?width=%width%&amp;height=%height%&amp;characters=%characters%" alt="code" /></div>';
    
    private $imgwidth;
    
    private $imgheight;
    
    private $inpsize;
    
    function __construct($name, $label, $idpref, $type, $imgwidth, $imgheight, $inpsize)
    {
	$this->label = ($label ? '<label>'.$label.'</label>' : null);
	$this->name = $name;
	$this->attributes['id'] = $idpref . $name;
	$this->attributes['class'] = 'frm-'.$type;
	$this->attributes['name'] = $name;
	$this->attributes['type'] = 'text';
	$this->caption = '';
	$this->newline = '';
	$this->type = $type;
	$this->value = '';
	$this->prototype .= $this->imagePrototype;
	$this->imgwidth = $imgwidth;
	$this->imgheight = $imgheight;
	$this->inpsize = $inpsize;
    }
    
    function getPrototype()
    {
	return $this->prototype;
    }
    
    function getLabel()
    {
	return parent::getLabel();
    }
    
    function getControl()
    {
	return $this->control = preg_replace(array('/%attributes%/', '/%value%/', '/%caption%/', '/%nl%/', '/%path%/', '/%width%/', '/%height%/', '/%characters%/'), array($this->collectAttributes(), $this->value, $this->caption, $this->newline, Form::$pathToCaptcha, $this->imgwidth, $this->imgheight, $this->inpsize), $this->prototype);
    }
    
}

class CheckboxElement extends InputElement implements IFormElements
{
    
    function __construct($name, $label, $idpref, $type, $attributes = null, $caption = null, $newline = false)
    {
	$this->name = $name;
	$this->attributes['id'] = $idpref . $name;
	$this->attributes['class'] = 'frm-'.$type;
	$this->attributes['name'] = $name;
	$this->attributes['type'] = $type;
	$caption_span = ($newline ? '<span style="margin-right:10px">' : '<span>');
	$this->caption = ($caption ? $caption_span . '&nbsp;' . $caption . '</span>' : '');
	$this->type = $type;
	$this->value = 1;
	$this->checked = null;
	
	if($this->type == 'checkbox' or $this->type == 'radio') {
	    $this->newline = ($newline ? '' : '<br />');
	}
	else {
	    $this->newline = '';
	}
	
	if($attributes and is_array($attributes)) {
	    foreach($attributes as $key => $attribute) {
		$this->attributes[$key] = $attribute;
	    } 
	}
    }
    
    function setValue($val)
    {
	if($val) {
	    $this->checked = true;
	}
	else {
	    $this->checked = false;
	}
	return $this;
    }
    
    function getPrototype()
    {
	return $this->prototype;
    }
    
    function getLabel()
    {
	return null;
    }
    
    function getControl()
    {
	// if checkbox and is set value make checked
	if($this->checked) $this->attributes['checked'] = 'checked';
	
	return $this->control = preg_replace(array('/%attributes%/', '/%value%/', '/%caption%/', '/%nl%/'), array($this->collectAttributes(), $this->value, $this->caption, $this->newline), $this->prototype);
    }
}

class TextareaElement extends FormElement implements IFormElements
{
    
    /** @var string */
    private $prototype = "<textarea %attributes%>%value%</textarea>";
    
    
    function __construct($name, $label, $idpref, $attributes = null)
    {
	parent::__construct( ($label ? '<label>'.$label.'</label>' : null) );
	$this->name = $name;
	$this->attributes['id'] = $idpref . $name;
	$this->attributes['class'] = 'frm-textarea';
	$this->attributes['name'] = $name;
	$this->type = 'textarea';
	$this->value = '';
	
	if($attributes and is_array($attributes)) {
	    foreach($attributes as $key => $attribute) {
		$this->attributes[$key] = $attribute;
	    } 
	}
	
    }
    
    function getPrototype()
    {
	return $this->prototype;
    }
    
    function getLabel()
    {
	return parent::getLabel();
    }
    
    function getControl()
    {
	return $this->control = preg_replace(array('/%attributes%/', '/%value%/'), array($this->collectAttributes(), $this->value), $this->prototype);
    }
}

class ButtonElement extends FormElement implements IFormElements
{
    
    /** @var string */
    private $prototype = "<input %attributes%value=\"%value%\" />";
    
    
    function __construct($name, $value, $idpref, $type)
    {
	parent::__construct( null );
	$this->name = $name;
	$this->attributes['id'] = $idpref . $name;
	$this->attributes['class'] = 'frm-'.$type;
	$this->attributes['name'] = $name;
	$this->attributes['type'] = $type;
	$this->type = $type;
	$this->value = $value;
    }
    
    function getPrototype()
    {
	return $this->prototype;
    }
    
    function getLabel()
    {
	return NULL;
    }
    
    function getControl()
    {
	return $this->control = preg_replace(array('/%attributes%/', '/%value%/'), array($this->collectAttributes(), $this->value), $this->prototype);
    }
}

class FileElement extends FormElement implements IFormElements
{
    
    /** @var string Html prototype */
    private $prototype = "<input %attributes% style=\"background-color: white\" />";
    
    
    function __construct($name, $label, $idpref, $attributes = null)
    {
	parent::__construct( ($label ? '<label>'.$label.'</label>' : null) );
	$this->name = $name;
	$this->attributes['id'] = $idpref . $name;
	$this->attributes['class'] = 'frm-file'  ;
	$this->attributes['name'] = $name;
	$this->attributes['type'] = 'file';
	$this->type = 'file';
	$this->value = null;
	
	if($attributes and is_array($attributes)) {
	    foreach($attributes as $key => $attribute) {
		$this->attributes[$key] = $attribute;
	    } 
	}
    }
    
    function getPrototype()
    {
	return $this->prototype;
    }
    
    function getLabel()
    {
	return parent::getLabel();
    }
    
    function getControl()
    {
	return $this->control = preg_replace(array('/%attributes%/', '/%value%/'), array($this->collectAttributes(), $this->value), $this->prototype);
    }
    
}

class SelectElement extends FormElement implements IFormElements
{
    
    /** @var string Html prototype */
    private $prototype = "<select %attributes%>%options%</select>";
    
    /** @var array Options and Values */
    private $options;
    
    /** @var array Items */
    private $items;
    
    /** @var string html Options */
    private $HtmlOptions;
     
    function __construct($name, $label, $idpref, $valAndOps, $attributes = null)
    {
	parent::__construct( ($label ? '<label>'.$label.'</label>' : null) );
	$this->name;
	$this->attributes['id'] = $idpref . $name;
	$this->attributes['class'] = 'frm-select';
	$this->attributes['name'] = $name;
	$this->type = 'select';
	$this->value = '';
	$this->items = $valAndOps;
	
	// if is size it must be multiple
	if($attributes['size']) {
	    $this->attributes['size'] = $attributes['size'];
	    if($attributes['multiple']) {
		$this->attributes['multiple'] = 'multiple';
	    }
	}
    
    }
    
    function getPrototype()
    {
	return $this->prototype;
    }
    
    function getLabel()
    {
	return parent::getLabel();
    }
    
    function getControl()
    {
	$this->setOptions($this->items);
	
	return $this->control = preg_replace(array('/%attributes%/', '/%options%/'), array($this->collectAttributes(), $this->getOptions()), $this->prototype);
    }
    
    function setOptions($valAndOps)
    {
	if(!is_array($valAndOps)) {
	    echo 'ERROR: $valAndOps must be an array!';
	    exit;
	}
	foreach($valAndOps as $val => $opt) {
	    $this->options[] = new OptionElement($val, $opt, $this->value);
	}
	
	$this->HtmlOptions = '';
	foreach( $this->options as $option) {
	    $this->HtmlOptions .= $option->getControl();
	}
    }
    
    function getOptions()
    {
	return $this->HtmlOptions;
    }
}

class OptionElement extends FormElement implements IFormElements
{
    
    /** @var string Html prototype */
    private $prototype = "<option value=\"%value%\"%select%>%option%</option>";
    
    /** @var string Html attribute selected */
    private $selected;
     
    function __construct($value, $option, $selected)
    {
	parent::__construct( null );
	$this->attributes = array();
	$this->type = 'option';
	$this->value = $value;
	$this->caption = $option;
	
	if($selected == $value) {
	    $this->selected = ' selected="selected"';
	}
	else {
	    $this->selected = '';
	}
    
    }
    
    function getPrototype()
    {
	return $this->prototype;
    }
    
    function getLabel()
    {
	return NULL;
    }
    
    function getControl()
    {
	return $this->control = preg_replace(array('/%value%/', '/%select%/', '/%option%/'), array($this->value, $this->selected, $this->caption), $this->prototype);
    }
}

class RadioElement extends FormElement implements IFormElements
{
    
    /** @var string Html prototype */
    private $prototype = null;
    
    /** @var array Items */
    private $items;
    
    /** @var string generated Html Items */
    private $HtmlItems;
    
    /** @var string Id Prefix */
    private $idPref;
    
    /** @var bool Newline */
    private $newline;
     
    function __construct($name, $label, $idpref, $items, $inline)
    {
	parent::__construct( ($label ? '<label>'.$label.'</label>' : null) );
	$this->name = $name;
	$this->attributes = array();
	$this->type = 'radio';
	$this->value = '';
	$this->items = $items;
	$this->name = $name;
	$this->idPref = $idpref;
	$this->newline = $inline;
    
    }
    
    function getPrototype()
    {
	return $this->prototype;
    }
    
    function getLabel()
    {
	return parent::getLabel();
    }
    
    function getControl()
    {
	$this->setItems();
	
	return $this->control = $this->getItems();
    }
    
    function setItems()
    {
	
	$this->HtmlItems = '';
	foreach( $this->items as $item ) {
	    // make checked
	    if($this->value == $item->getValue()) $item->setAttribute('checked', 'checked');
	    $this->HtmlItems .= $item->getControl();
	}
	
    }
    
    function getItems()
    {
	return $this->HtmlItems;
    }
}


class GroupElement extends FormElement implements IFormElements
{
    
    /** @var string Html prototype */
    private $prototype = "<fieldset %attributes%>%caption%%items%</fieldset>";
    
    /** @var array Items */
    private $items;
    
    /** @var string generated Html Items */
    private $HtmlItems;
    
    /** @var string Id Prefix */
    private $idPref;
    
    /** @var bool Newline */
    private $newline;
     
    function __construct($name, $label, $idpref, $items, $inline)
    {
	parent::__construct( ($label ? $label : null) );
	$this->name;
	$this->attributes['id'] = $idpref . $name;
	$this->attributes['class'] = 'frm-group';
	$this->type = 'group';
	$this->value = '';
	$this->items = $items;
	$this->name = $name;
	$this->idPref = $idpref;
	$this->newline = $inline;
	$this->caption = null;
    
    }
    
    
    function addItem($element)
    {
	$element instanceof IFormElements
	    or die( 'Error: addGroup->addItem(): Parameter must be instance of Form Element Object.' );
	    
	$element->setGroup($this->name);
	$this->items[] = $element;
	return $this;
    }
    
    function getPrototype()
    {
	return $this->prototype;
    }
    
    function getLabel()
    {
	return parent::getLabel();
    }
    
    function setCaption($string = null)
    {
	if(!$string) die("Error: setCaption() - missing parameter string");
	
	else $this->caption = $string;
	return $this;
    }
    
    function getCaption()
    {
	return $this->caption;
    }
    
    function getControl()
    {
	$this->setItems();
	
	return $this->control = preg_replace(array('/%attributes%/', '/%caption%/', '/%items%/'), array($this->collectAttributes(), ($this->getCaption() ? '<legend>'.$this->getCaption().'</legend>' : ''), $this->getItems()), $this->getPrototype());
    }
    
    function setItems()
    {
	
	$this->HtmlItems = '';
	foreach( $this->items as $item) {
	    $this->HtmlItems .= $item->getControl();
	}
	
    }
    
    function getItems()
    {
	return $this->HtmlItems;
    }
}

class ContainerElement extends FormElement implements IFormElements
{
    
    /** @var string Html prototype */
    private $prototype = "<div %attributes%>%items% %caption%</div>";
    
    /** @var array Items */
    private $items;
    
    /** @var string generated Html Items */
    private $HtmlItems;
    
    /** @var string Id Prefix */
    private $idPref;
    
    /** @var bool Newline */
    private $newline;
     
    function __construct($name, $label, $idpref, $items = null, $inline = null)
    {
	parent::__construct( ($label ? $label : null) );
	$this->name;
	$this->attributes['id'] = $idpref . $name;
	$this->attributes['class'] = 'frm-container';
	$this->type = 'container';
	$this->value = '';
	$this->items = $items;
	$this->name = $name;
	$this->idPref = $idpref;
	$this->newline = $inline;
	$this->caption = null;
    
    }
    
    
    function addItem($element)
    {
	$element instanceof IFormElements
	    or die( 'Error: addContainer->addItem(): Parameter must be instance of Form Element Object.' );
	    
	$element->setGroup($this->name);
	$this->items[] = $element;
	return $this;
    }
    
    function getPrototype()
    {
	return $this->prototype;
    }
    
    function getLabel()
    {
	return parent::getLabel();
    }
    
    function setCaption($string = null)
    {
	if(!$string) die("Error: setCaption() - missing parameter string");
	
	else $this->caption = $string;
	return $this;
    }
    
    function getCaption()
    {
	return $this->caption;
    }
    
    function getControl()
    {
	$this->setItems();
	
	return $this->control = preg_replace(array('/%attributes%/', '/%caption%/', '/%items%/'), array($this->collectAttributes(), ($this->getCaption() ? $this->getCaption() : ''), $this->getItems()), $this->getPrototype());
    }
    
    function setItems()
    {
	
	$this->HtmlItems = '';
	foreach( $this->items as $item) {
	    $this->HtmlItems .= $item->getControl();
	}
	
    }
    
    function getItems()
    {
	return $this->HtmlItems;
    }
}

class BreakElement extends FormElement implements IFormElements
{
    /** @var string Html prototype */
    private $prototype = "<br />";
     
    function __construct($name)
    {
	parent::__construct( null );
	$this->name = $name;
	$this->type = 'breakline';
	$this->value = '';
	$this->name = $name;
    
    }
    
    function getPrototype()
    {
	return $this->prototype;
    }
    
    function getLabel()
    {
	return parent::getLabel();
    }
    
    function getControl()
    {
	return $this->control = $this->prototype;
    }
}

class ContentElement extends FormElement implements IFormElements
{
    
    /** @var string */
    private $prototype = "<div %attributes%>%value%</div>";
    
    
    function __construct($name, $value, $idpref)
    {
	parent::__construct( null );
	$this->name = $name;
	$this->attributes['id'] = $idpref . $name;
	$this->attributes['class'] = 'frm-inner-content';
	$this->type = 'frm-inner-content';
	$this->value = $value;
    }
    
    function getPrototype()
    {
	return $this->prototype;
    }
    
    function getLabel()
    {
	return NULL;
    }
    
    function getControl()
    {
	return $this->control = preg_replace(array('/%attributes%/', '/%value%/'), array($this->collectAttributes(), $this->value), $this->prototype);
    }
}

class Rule
{
    /** @property string $name Element name */
    public $name;
    
    /** @property string $rule Rule */
    public $rule;
    
    /** @var string Message */
    public $message;
    
    /** @var array Additional Arguments */
    public $args;
    
    /**
     * Constructor
     * @param string $name Name of element
     * @param array Arguments
     * @return void
     */
    function __construct($name, $args)
    {
	$this->name = $name;
	$this->rule = array_shift($args);
	$this->message = array_shift($args);
	$this->args = $args;
    }
}


interface IFormRenderer
{
    /**
     * @param string $html HTML Form Starting Tag
     */
    function setFormStart($html);
    
    /**
     * @param string $html HTML Form Ending Tag
     */
    function setFormEnd($html);
    
    /**
     * @param array $elements Array of Form Elements Objects
     */
    function setElements($elements);
    
    /**
     * @return string HTML Form
     */
    function getCompleteForm();
}

class TableRenderer implements IFormRenderer
{
    /** @property string $prototype Html prototype */
    private $prototype = "%errors%\n%formstart%\n\t<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n\t\t<tbody>\n\t\t\t%elements%\n\t\t</tbody>\n\t</table>\n\t\n\t%hiddens%\n%formend%";
    
    /** @property string $elemPrototype Html prototype of element*/
    private $elemPrototype = "<tr><td class=\"frm-label\">%label%</td><td class=\"frm-control\">%control%</td></tr>\n\t\t\t";
    
    /** @property string $descPrototype Html prototype of element description*/
    private $descPrototype = "<tr><td>&nbsp;</td><td class=\"frm-description\">%description%</td></tr>\n\t\t\t";
    
    /** @property string $hiddenPrototype Html prototype of hidden elements container*/
    private $hiddenPrototype = "<div>%hidden%</div>\n\t";
    
    /** @property string $formstart Html Start of form */
    private $formstart;
    
    /** @property string $formend Html End of form */
    private $formend;
    
    /** @property string $elements Html Of Form elements */
    private $elements = '';
    
    /** @property string $errors Html Of Form errors */
    private $errors = '';
    
    /** @property string $hiddens Html Of Form hidden elements */
    private $hiddens = '';
    
    
    function setFormStart($html)
    {
	$this->formstart = $html;
    }
    
    function setFormEnd($html)
    {
	$this->formend = $html;
    }
    
    function setElements($elements)
    {
	
	foreach($elements as $element) {
	    if(!$element->inGroup()) {
		if($element->getType() == 'hidden') {
		    $this->hiddens .= preg_replace('/%hidden%/', $element->getControl(), $this->hiddenPrototype);
		}
		else {
		    // if has description
		    if($element->getDescription()) $this->elements .= preg_replace('/%description%/', $element->getDescription(), $this->descPrototype);
		    $this->elements .= preg_replace(array('/%label%/', '/%control%/'), array( ($element->getLabel() ? $element->getLabel() : '&nbsp;'), $element->getControl() ), $this->elemPrototype);
		}
	    }
	}
    }
    
    function setErrors($errors) {
	if($errors){
	    $output = '<ul class="error">';
	    foreach($errors as $error){
		$output .= '<li>' . $error . '</li>';
	    }
	    $output .= '</ul>';
	}
	else{
	    $output = '';
	}
	
	$this->errors = $output;
    }
    
    function getCompleteForm()
    {
	return preg_replace(array('/%errors%/', '/%formstart%/', '/%elements%/', '/%hiddens%/', '/%formend%/'), array($this->errors, $this->formstart, $this->elements, $this->hiddens, $this->formend), $this->prototype);
    }
}

class NewlineRenderer implements IFormRenderer
{
    /** @property string $prototype Html prototype */
    private $prototype = "%errors%\n%formstart%\n%elements%\n%hiddens%\n%formend%";
    
    /** @property string $elemPrototype Html prototype of element*/
    private $elemPrototype = "\t<div class=\"frm-label\">%label%</div>%description%<div class=\"frm-control\">%control%</div>";
    
    /** @property string $hiddenPrototype Html prototype of hidden elements container*/
    private $hiddenPrototype = "\t<div>%hidden%</div>";
    
    /** @property string $formstart Html Start of form */
    private $formstart;
    
    /** @property string $formend Html End of form */
    private $formend;
    
    /** @property string $elements Html Of Form elements */
    private $elements = '';
    
    /** @property string $errors Html Of Form errors */
    private $errors = '';
    
    /** @property string $hiddens Html Of Form hidden elements */
    private $hiddens = '';
    
    
    function setFormStart($html)
    {
	$this->formstart = $html;
    }
    
    function setFormEnd($html)
    {
	$this->formend = $html;
    }
    
    function setElements($elements)
    {
	
	foreach($elements as $element) {
	    if(!$element->inGroup()) {
		if($element->getType() == 'hidden') {
		    $this->hiddens .= preg_replace('/%hidden%/', $element->getControl(), $this->hiddenPrototype);
		}
		else {
		    $this->elements .= preg_replace(array('/%label%/', '/%description%/', '/%control%/'), array( ($element->getLabel() ? $element->getLabel() : '&nbsp;'), ($element->getDescription() ? $element->getDescription() : ''), $element->getControl() ), $this->elemPrototype);
		}
	    }
	}
    }
    
    function setErrors($errors) {
	if($errors){
	    $output = '<ul class="error">';
	    foreach($errors as $error){
		$output .= '<li>' . $error . '</li>';
	    }
	    $output .= '</ul>';
	}
	else{
	    $output = '';
	}
	
	$this->errors = $output;
    }
    
    function getCompleteForm()
    {
	return preg_replace(array('/%errors%/', '/%formstart%/', '/%elements%/', '/%hiddens%/', '/%formend%/'), array($this->errors, $this->formstart, $this->elements, $this->hiddens, $this->formend), $this->prototype);
    }
}

//Inicialize
AppForm::sessionCheck();
AppForm::$pathToCaptcha = '';

?>