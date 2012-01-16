<?php 
/**
 * PHP Class to create forms 
 * 
 * <code><?php
 * require('AppForm.php');
 * $form = new AppForm();
 * ? ></code>
 * 
 * ==============================================================================
 * 
 * @version $Id: AppForm.php, v1.71 11:00 06/29/2011 $
 * @copyright Copyright (c) 2010 - 2011 Branislav Zvolensky (http://www.mbmartworks.sk)
 * @author Branislav Zvolensky <zvolensky@mbmartworks.sk>
 * @license http://opensource.org/licenses/gpl-license.php GNU General Public License (GPL)
 * 
 * ==============================================================================
 * new in 1.71
 * added fluent methods to CaptchaElement:
 * setColors($bgcolor, $textcolor, $noisecolor)
 * setBackgroundColor($bgcolor)
 * setTextColor($textcolor)
 * setNoiseColor($noisecolor)
 * every method can receive string (hex value) or array of RGB values
 * 
 * 
 * new in 1.70
 * added rules FLOAT, INTEGER
 * added JavaScript validator
 * FormElement: added method allowTags( $string ). Default validation of data strips Html tags from string inputs
 * and you can allow some Html tags in example:
 * $form = new AppForm();
 * $form->addTextarea('element_name', 'Element label')
 *      ->allowTags('<b><strong><p><i>');
 * 
 * added method AppForm::variableSanity() witch is called to check security of user data and
 * $form->getValues() now returns safe and valid data
 * 
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
 * if(Form::$isvalid){   //...do something if it is valid...  }else{  echo $_SESSION[md5( baseUrl() )]['error']; //...do something if it is not valid... }
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

// load captcha generator
include dirname(__FILE__) . '/tools/CaptchaSecurityImages.php';

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
        const FLOAT = 'float';
        const INTEGER = 'integer';
	const REGEX = 'regex';
        const REGEX_NEGATIVE = 'regex_negative';
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
	
	/** @var bool isvalid status */
	public $isvalid = false;
	
	/** @var array formObjects Array */
	public static $formObjects;
	
	/** @var bool error status */
	public $isError = FALSE;
	
	/** @var array error messages*/
	public $errors = array();
	
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
		
	/** @var string Name of renderer class */
	private $renderer;
	
	/** @var string Submit button name */
	private $submit = array();
	
	/** @var array onSubmit event callback */
	public $onSubmit = array();
	
	/** @var array POST DATA */
	private $POST = array();
	
	/** @var array $forms */
	private static $forms = array();
	
	/** @var array $forms */
	private static $formHtmlStatements = array();
	
	/** @var array FILES DATA */
	private $FILES = array();

	// Constructor
	public function __construct($name=NULL) {
            
	 
	    //$POST = $_POST;    
		$POST = Vars::get('POST')->getRaws();

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
		
		$this->setPostData($POST, $_FILES);
		
		if( isset( $_SESSION[md5( baseUrl() )]['BZ_FORM']['token'] ) ) {
		    $this->formOldToken = $_SESSION[md5( baseUrl() )]['BZ_FORM']['token'];
		}
		
	}
        

        public function variableSanity() {
            $POST = $this->POST;
            
            foreach ( $this->element as $element ) {
                
                $sElName = $element->getName();
                
                if( isset( $POST[$sElName] ) ) {
                    
                    if( is_float( $POST[$sElName] ) ) {
                        $POST[$sElName] = (float) $POST[$sElName];
                    }
                    else if( is_int( $POST[$sElName] ) ) {
                        $POST[$sElName] = (int) $POST[$sElName];
                    }
                    else {
                        $allowed = $element->getStripTags();
                        
                        if($allowed === false or $allowed === '') $POST[$sElName] = strip_tags( $POST[$sElName] );
                        else if($allowed === true) $POST[$sElName] = $POST[$sElName];
                        else $POST[$sElName] = strip_tags( $POST[$sElName], $allowed );
                        
                        $POST[$sElName] = $POST[$sElName];
                        
                        // TODO: Htmlentyties
                        $POST[$sElName] = trim( $POST[$sElName] );
                    }
                    
                }
                
            }
            
            return $POST;
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
	    // $_SESSION[md5( baseUrl() )]['BZ_FORM'][$this->formName] = $this;
	    
	    self::$forms[$this->formName] = $this;
               
	    if( $this->isSubmitted() ){
		
                
		// test checkboxes
		$post = $this->POST;
		foreach($this->element as $element) {
                    
                    $sElType = $element->getType();
                    $sElName = $element->getName();
                    
		    if('checkbox' == $sElType) {
			if(isset($post[$sElName])) {
			    $this->element[$sElName]->setValue(1);
			}
			else {
			    $this->element[$sElName]->setValue(0);
			}
		    }
		}
		
		$this->validate();
		
		$callback = $this->getSubmitEvent();
                
                $object = $this->getSubmitObject();
                
                if( !empty($callback) ) {
                    if( $object && is_object($object) )
                        $object->$callback($this);
                    else
                        $callback($this);
                }
	    }
	}
	
	
	/************************ RENDERER ************************/
	
	/**
	 * @param string $method Rendering method of form. Avialable [ newline | table ]
	 */
	public function render() {
		
		$renderer = $this->getRenderer( $this->attributes['id'] );
		
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
	function getRenderer( $frmID )
	{
	    return new $this->renderer( $frmID );
	}
	
	
	/********************************* FORM ELEMENTS FACTORIES ********************/
	
	/**
	 * Add token to form
	 * @param string $errMessage
	 */
	function addProtection($errMessage = null)
	{
	    if(!$errMessage) $errMessage = 'Illegal submition';
	    
	    $_SESSION[md5( baseUrl() )]['BZ_FORM']['token'] = $this->formToken;
	    
	    $this->element['frm-token'] = new InputElement('frm_token', null, $this->idPref, 'hidden');
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
	 * Adds email input
	 * @param string $name Name of the element
	 * @param string $label Label for the element
	 * @param integer $size Size of the element
	 * @param integer $maxsize Maximum size of the element
	 */
	public function addEmail($name=NULL, $label=NULL, $size='40', $maxsize='256') {
		
		if(!$name){
			echo 'Error: Set name of text input.';
			exit;
		}
		else{
                        
			return $this->element[$name] = new InputElement($name, $label, $this->idPref, 'email', array('size'=>$size, 'maxlength'=>$maxsize));
			
		}
	
	}
	
	/**
	 * Adds url input
	 * @param string $name Name of the element
	 * @param string $label Label for the element
	 * @param integer $size Size of the element
	 * @param integer $maxsize Maximum size of the element
	 */
	public function addUrl($name=NULL, $label=NULL, $size='40', $maxsize='256') {
		
		if(!$name){
			echo 'Error: Set name of text input.';
			exit;
		}
		else{
                        
			return $this->element[$name] = new InputElement($name, $label, $this->idPref, 'url', array('size'=>$size, 'maxlength'=>$maxsize));
			
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
                        return $this->element[$name];
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
		else{
			$this->submit[] = $name;
			return $this->element[$name] = new SubmitElement($name, $value, $this->idPref, 'submit');
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
			return $this->element[$name] = new SubmitElement($name, $value, $this->idPref, 'reset');
		}
	}
        
        /**
	 * Adds Button element
	 * @param string $name Name of the element
	 * @param string $value Label for the element
	 */
	public function addButton($name=NULL, $value=NULL, $type=NULL) {
		
		if(!$name){
			echo 'Error: Set name of submit input.';
			exit;
		}
		else{
			return $this->element[$name] = new ButtonElement($name, $value, $this->idPref, 'button', $type);
		}
	}
	
	/**
	 * Adds Textarea
	 * @param string $name Name of the element
	 * @param string $label Label for the element
	 * @param integer $cols Columns
	 * @param integer $rows Rows
	 */
	public function addTextarea($name=NULL, $label="", $cols='30', $rows='5', $wrap='soft') {
	
	    if(!$name){
		echo 'Error: Set name of Textarea.';
		exit;
	    }
	    else{
		return $this->element[$name] = new TextareaElement($name, $label, $this->idPref, array('cols'=>$cols, 'rows'=>$rows, 'wrap'=>$wrap));
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
			$this->element[$name] = new TimepickerElement($name, $label, $this->idPref);
			
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
			
			$this->element[$name] = new DatepickerElement($name, $label, $format, $years, $this->idPref);			
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
	 * Adds Block
	 * @param string $name
	 * @param string $label
	 * @param bool $inline;
	 */
	public function addBlock($name=NULL, $label=NULL, $inline=false)
	{
		if(!$name){
			echo 'Error: Set name for Checkbox Group.';
			exit;
		}
		else{
			return $this->element[$name] = new BlockElement($name, $label, $this->idPref, null, $inline);
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
	    $this->POST = $post;
	    $this->FILES = $files;
	}
	
        /**
         * returns RAW POST DATA
         * @return type array
         */
	private function getPostData()
	{
	    return $this->POST;
	}
	
	public function getFilesData()
	{
	    return $this->FILES;
	}
	
	public function getToken()
	{
	    return $this->formToken;   
	}
	
	public function getSubmitEvent()
	{
	    return $this->onSubmit['callback'];
	}
        
    public function getSubmitObject()
	{
	    return $this->onSubmit['object'];
	}
	
	/**
	 * Check if form was submitted
	 * @return bool
	 */
	public function isSubmitted()
	{
		foreach($this->submit as $submit) {
			if( isset($this->POST[$submit])) {
				return TRUE;
			}
		}
		return FALSE;
	}
	
	/**
	 * Controls the element which were pressed
	 * @param type $string
	 * @return bool
	 */
	public function isSubmittedBy($string)
	{
		foreach($this->submit as $submit) {
			if( isset($this->POST[$submit]) && $string === $submit) {
				return TRUE;
			}
		}
		return FALSE;
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
		
		if($filesData && isset($filesData[$elName])) $file = $filesData[$elName];
		    else $file = null;
		
		$emailRegExp = "/^[^0-9][A-z0-9_-]+([.][A-z0-9_-]+)*[@][A-z0-9_-]+([.][A-z0-9_-]+)*[.][A-z]{2,4}$/";
		
		if($rule === self::FILLED && trim($value) == '') $this->setError($message);
		
		if($rule === self::EMAIL and trim($value) != ''){
		    if($rule === self::EMAIL and !preg_match($emailRegExp, $value)){
			$this->setError($message);
		    }
		}
		
		if($rule === self::EQUAL){
		    $equalValue = $postData[$args[0]];
		    if($value != $equalValue){
			$this->setError($message);
		    }
		}
		
		if($rule === self::NUMERIC and !is_numeric($value)) $this->setError($message);
		
		if($rule === self::MIN and ( (int)$value < (int)$args[0] or !is_numeric($value) ) ) $this->setError($message);
		
		if($rule === self::MAX and ( (int)$value > (int)$args[0] or !is_numeric($value) ) ) $this->setError($message);
		
		if($rule === self::RANGE and ( (int)$value < (int)$args[0] or (int)$value > (int)$args[1] or !is_numeric($value) ) ) $this->setError($message);
                
        if($rule === self::INTEGER and !preg_match('/^[-]?[0-9]+$/', $value) ) $this->setError($message);

        if($rule === self::FLOAT and !preg_match('/^[-]?[0-9]+[\.][0-9]+$/', $value) ) $this->setError($message);
		
		if($rule === self::REGEX && $value && !preg_match($args[0], $value)) $this->setError($message);
                
        if($rule === self::REGEX_NEGATIVE && $value && preg_match($args[0], $value)) $this->setError($message);

		if($rule === self::CAPTCHA and ($_SESSION[md5( baseUrl() )]['BZ_FORM']['security_code'] !== $value) ) {
                    $this->setError($message, self::CAPTCHA);
                }
		
		if($rule === self::SKIPFIRST and $value == $args[0]) $this->setError($message);
		
		if($rule === 'token' and $value != $args[0]) $this->setError($message);
		
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
			$this->errors[] =  $error;
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
	
	public function onSubmit($callback, $object = null)
	{   
	    $this->onSubmit['callback'] = $callback;
            $this->onSubmit['object'] = $object;
	}
	
	public function isValid()
	{
	    return $this->isvalid;
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
                if('password' == self::$forms[$this->formName]->element[$key]->getType()){
					self::$forms[$this->formName]->element[$key]->setValue('');
			    }
			    elseif('captcha' == self::$forms[$this->formName]->element[$key]->getType()){
					self::$forms[$this->formName]->element[$key]->setValue('');
			    }
			    elseif('frm-token' == $key) {
					self::$forms[$this->formName]->element[$key]->setValue($this->formToken);
			    }
				elseif('select' == self::$forms[$this->formName]->element[$key]->getType()) {
					self::$forms[$this->formName]->element[$key]->setValue($val);
				}
			    else {
					self::$forms[$this->formName]->element[$key]->setValue($val);
			    }
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
				elseif('select' == $this->element[$key]->getType()) {
					$this->element[$key]->setValue($val);
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
	    
            // read checked post data
            $vars = $this->variableSanity() + array('FILES' => $this->FILES);
            //if(!empty($this->FILES)) $vars += array('FILES' => $this->FILES);
	    
	    // create unset array
	    unset($vars['bz_form_token']);
	    foreach($this->element as $element) {
                $sElName = $element->getName();
                $sElType = $element->getType();
				unset($vars['frm_token']); // unset token
				if( 'submit' == $sElType ) unset($vars[$sElName]); // unset submit
                if( 'captcha' == $sElType ) unset($vars[$sElName]); // unset captcha
				if( 'reset' == $sElType ) unset($vars[$sElName]);  // unset reset
				if( 'checkbox' == $sElType && !isset($vars[$sElName])) {
					$vars[$sElName] = 0; // set real checkbox values
                }
				if( 'datepicker' == $sElType || 'timepicker' == $sElType ) {
					$vars[$sElName] = $element->getPostedValue( $vars );
				}
	    }
	    
	    return $vars;
	}
	
	/**
	 * Calls render method when echo or string conversion is called
	 */
	public function __toString()
	{
		return (string)$this->render();
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
    
    /** @var mixed */
    protected $striptags = '';
    
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
    function setCaption($string = null)
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
        $id = isset($this->attributes['id']) ? $this->attributes['id'] : null;
		$name = $this->getName();
		$args = func_get_args();
		
		// if must be filled add attribute required (HTML 5)
		if( $args[0] === AppForm::FILLED) {
			$this->attributes['required'] = 'required';
		}
		
		$this->rules[] = new Rule($id, $name, $args);
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
	
	function setId($string)
    {
		$this->attributes['id'] = $string;
		return $this;
    }
	
	function getId()
    {
		return $this->attributes['id'];
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
    
    /**
     * Strip tags
     * @param mixed $allowed bool|string of allowed tags
     */
    public function allowTags( $allowed = '' ) {
        
        $this->striptags = $allowed;
        return $this;
        
    }
    
    
    public function getStripTags()
    {
        
        return $this->striptags;
        
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
	
	/** @var bool Should be appered inline */
    protected $inline;
    
    
    function __construct($name, $label, $idpref, $type, $attributes = null, $caption = null, $newline = false, $checked = null)
    {
	
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
		parent::__construct( ($label ? "<label for=\"{$this->attributes['id']}\">".$label.'</label>' : null) );

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
	
	function isInline()
	{
		return $this->inline;
	}
    
}

class CaptchaElement extends InputElement implements IFormElements
{
    
    /** @var string Html prototype */
    private $imagePrototype = '<div id="captcha-img"><img src="%image%" width="%width%" height="%height%" alt="code" /></div>';
    
    private $imgwidth;
    
    private $imgheight;
    
    private $inpsize;
    
    private $image;
    /** @property array background of captcha. The array of RGB values. */
    private $bgcolor = array();
    /** @property array text color of captcha. The array of RGB values. */
    private $textcolor = array();
    /** @property array noise color of captcha. The array of RGB values. */
    private $noisecolor = array();
    private $counter;
    
    function __construct($name, $label, $idpref, $type, $imgwidth = 120, $imgheight = 40, $inpsize = 6)
    {
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
		$this->label = ($label ? "<label for=\"{$this->attributes['id']}\">".$label.'</label>' : null);
        
        $this->bgcolor = array(255, 255, 255);
        $this->textcolor = array(20, 40, 100);
        $this->noisecolor = array(100, 120, 180);
          
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
        
        $captcha = new CaptchaSecurityImages( $this->imgwidth, $this->imgheight, $this->inpsize, $this->bgcolor, $this->textcolor, $this->noisecolor);
        $this->image = basePath() . 'tmp/' . $captcha->getFilename();
        
        return $this->control = preg_replace(array('/%attributes%/', '/%value%/', '/%caption%/', '/%nl%/', '/%image%/', '/%width%/', '/%height%/'), array($this->collectAttributes(), $this->value, $this->caption, $this->newline, $this->image, $this->imgwidth, $this->imgheight), $this->prototype);
        
    }
    
    /**
     * You can send parameters as HEX value in string #FFBB00 or FFBB00 and
     * RGB values in array such as array(128, 255, 20)
     * @param mixed string|array $mBackground HEX values as string, RGB Values as array
     * @param mixed string|array $mText HEX values as string, RGB Values as array
     * @param mixed string|array $mNoise HEX values as string, RGB Values as array
     * @return CaptchaElement 
     */
    public function setColors( $mBackground = null, $mText = null, $mNoise = null) {
        
        if( !empty( $mBackground ) ) $this->setBackgroundColor ( $mBackground );
        if( !empty( $mText ) ) $this->setTextColor ( $mText );
        if( !empty( $mNoise ) ) $this->setNoiseColor ( $mNoise );
        
        return $this;
        
    }
    
    public function setBackgroundColor( $mBackground ) {
        
        if ( is_string( $mBackground ) ) {
            
            $this->bgcolor = $this->convertColors( $mBackground );
            
        }
        else if ( is_array( $mBackground ) ) {
            
            if ( count ($mBackground) !== 3 ) die ("AppForm::setBackgroundColor(): sent argument has bad format.");
            
            foreach ( $mBackground as $val ) {
                
                if ( (int)$val > 255 ) die ("AppForm::setBackgroundColor(): the value $val must be in range 0 - 255.");
                
            }
            
            $this->bgcolor = $mBackground;
            
        }
        else {
            
            die( "AppForm::setBackgroundColor(): argument $mBackground must be set");
            
        }
        
        return $this;
    }
    
    function setTextColor( $mText ) {
        
        if ( is_string( $mText ) ) {
            
            $this->textcolor = $this->convertColors( $mText );
            
        }
        else if ( is_array( $mText ) ) {
            
            if ( count ($mText) !== 3 ) die ("AppForm::setTextColor(): sent argument has bad format.");
            
            foreach ( $mText as $val ) {
                
                if ( (int)$val > 255 ) die ("AppForm::setTextColor(): the value $val must be in range 0 - 255.");
                
            }
            
            $this->textcolor = $mText;
            
        }
        else {
            
            die( "AppForm::setTextColor(): argument $mText must be set");
            
        }
        
        return $this;
        
    }
    
    function setNoiseColor( $mNoise ) {
        
        if ( is_string( $mNoise ) ) {
            
            $this->noisecolor = $this->convertColors( $mNoise );
            
        }
        else if ( is_array( $mNoise ) ) {
            
            if ( count ($mNoise) !== 3 ) die ("AppForm::setNoiseColor(): sent argument has bad format.");
            
            foreach ( $mNoise as $val ) {
                
                if ( (int)$val > 255 ) die ("AppForm::setNoiseColor(): the value $val must be in range 0 - 255.");
                
            }
            
            $this->noisecolor = $mNoise;
            
        }
        else {
            
            die( "AppForm::setNoiseColor(): argument $mNoise must be set");
            
        }
        
        return $this;
        
    }
    
    /**
     * Converts hex color to array with rgb values
     * @param string $string Hex color value
     * @return array 
     */
    private function convertColors( $string ) {
        
        $string = preg_replace('/#/', '', $string);
        $string = preg_replace("/[^0-9A-Fa-f]/", '', $string); // Gets a proper hex string
        
        if( strlen($string) !== 6 ) die ( "AppForm: CaptchaElement::convertColors $string is not correct hexa color value." );
        
        $rgb[] = hexdec( substr( $string, 0, 2 ) ); // R color
        $rgb[] = hexdec( substr( $string, 2, 2 ) ); // G color
        $rgb[] = hexdec( substr( $string, 4, 2 ) ); // B color
        
        return $rgb;
        
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
	$this->inline = $newline;
	
	
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
	
		$this->name = $name;
		$this->attributes['id'] = $idpref . $name;
		$this->attributes['class'] = 'frm-textarea';
		$this->attributes['name'] = $name;
		$this->type = 'textarea';
		$this->value = '';
		parent::__construct( ($label ? "<label for=\"{$this->attributes['id']}\">".$label.'</label>' : null) );

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

class SubmitElement extends FormElement implements IFormElements
{
    
    /** @var string */
    private $prototype = "<input %attributes%value=\"%value%\" />";
    
    
    function __construct($name, $value, $idpref, $type)
    {
	
		$this->name = $name;
		$this->attributes['id'] = $idpref . $name;
		$this->attributes['class'] = 'frm-'.$type;
		$this->attributes['name'] = $name;
		$this->attributes['type'] = $type;
		$this->type = $type;
		$this->value = $value;
        parent::__construct( null );
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

class ButtonElement extends FormElement implements IFormElements
{
    
    /** @var string */
    private $prototype = "<button %attributes%>%value%</button>";
    
    
    function __construct($name, $value, $idpref, $type, $elType)
    {
	
		$this->name = $name;
		$this->attributes['id'] = $idpref . $name;
		$this->attributes['class'] = 'frm-'.$type;
		$this->attributes['name'] = $name;
		if($elType) $this->attributes['type'] = $elType;
		$this->type = $type;
		$this->value = $value;
        parent::__construct( null );
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
        $this->label = $label ? "<label for=\"{$this->attributes['id']}\">".$label.'</label>' : null;
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
		
		$this->name;
		$this->attributes['id'] = $idpref . $name;
		$this->attributes['class'] = 'frm-select';
		$this->attributes['name'] = $name;
		$this->type = 'select';
		$this->value = '';
		$this->items = $valAndOps;
		$this->label = $label ? "<label for=\"{$this->attributes['id']}\">".$label.'</label>' : null;

		// if is size it must be multiple
		if($attributes['size']) {
			$this->attributes['size'] = $attributes['size'];
			if($attributes['multiple']) {
				$this->attributes['multiple'] = 'multiple';
				$this->attributes['name'] .= '[]';
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

		if( is_array($selected) && in_array($value, $selected) ) {
			$this->selected = ' selected="selected"';
		}
		else if($selected == $value) {
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
        parent::__construct( ($label ? $label : null) );
    
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
    protected $prototype = "<div %attributes%>%items% %caption%</div>";
    
    /** @var array Items */
    protected $items;
    
    /** @var string generated Html Items */
    protected $HtmlItems;
    
    /** @var string Id Prefix */
    protected $idPref;
    
    /** @var bool Newline */
    protected $newline;
     
    function __construct($name, $label, $idpref, $items = null, $inline = null)
    {
	
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
        parent::__construct( ($label ? "<label for=\"{$this->attributes['id']}\">".$label.'</label>' : null) );
        
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

class DatepickerElement extends ContainerElement
{
	private $format, $month, $day, $year;
	
	public function __construct($name, $label, $format, $years, $idpref) {
		parent::__construct($name, $label, $idpref, null, true);
		$this->type = 'datepicker';
		$this->format = $format;
		$this->init($format, $years);
	}
	
	private function init($format, $years)
	{
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
			
			$this->month = $month = new SelectElement($this->name.'_month', null, $this->idPref, $monthsArr);
			$this->day = $day = new SelectElement($this->name.'_day', null, $this->idPref, $daysArr);
			$this->year = $year = new SelectElement($this->name.'_year', null, $this->idPref, $yearsArr);
			
			if($format == 'YYYY-MM-DD'){
			    $this->addItem($year);
			    $this->addItem($month);
			    $this->addItem($day);
			}
			
			if($format == 'DD.MM.YYYY') {
			    $this->addItem($day);
			    $this->addItem($month);
			    $this->addItem($year);
			}
	}
	
	public function setValue($value)
	{
		$this->value = $value;
		
		if(preg_match('/^([0-9]{2})\.([0-9]{2})\.([0-9]{4})$/', $value, $matches)) {
			$this->day->setValue($matches[1]);
			$this->month->setValue($matches[2]);
			$this->year->setValue($matches[3]);
		}
		if(preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $value, $matches)) {
			$this->day->setValue($matches[3]);
			$this->month->setValue($matches[2]);
			$this->year->setValue($matches[1]);
		}
		
		return $this;
	}
	
	public function getValue() {
		return $this->value;
	}
	
	public function getPostedValue( $post )
	{
		if(preg_match('/^([D]{2})\.([M]{2})\.([Y]{4})$/', $this->format)) {
			return $post[$this->name."_day"] . "." . $post[$this->name."_month"] . "." . $post[$this->name."_year"];
		}
		if(preg_match('/^([Y]{4})-([M]{2})-([D]{2})$/', $this->format)) {
			return $post[$this->name."_year"] . "-" . $post[$this->name."_month"] . "-" . $post[$this->name."_day"];
		}
	}
}


class TimepickerElement extends ContainerElement
{
	private $hour, $min;
	
	public function __construct($name, $label, $idpref) {
		parent::__construct($name, $label, $idpref, null, true);
		$this->type = 'timepicker';
		$this->init();
	}
	
	private function init()
	{
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
			$this->hour = new SelectElement($this->name.'_hour', null, $this->idPref, $hoursArr);
			$this->min = new SelectElement($this->name.'_min', null, $this->idPref, $minsArr);
			
			$this->addItem($this->hour);
			$this->addItem($this->min);
	}
	
	public function setValue($value)
	{
		$this->value = $value;
		
		if(preg_match('/^([0-9]{2}):([0-9]{2})$/', $value, $matches)) {
			$this->hour->setValue($matches[1]);
			$this->min->setValue($matches[2]);
		}
		
		return $this;
	}
	
	public function getValue() {
		return $this->value;
	}
	
	public function getPostedValue( $post )
	{
		return $post[$this->name."_hour"] . ":" . $post[$this->name."_min"];
	}
}


class BlockElement extends FormElement implements IFormElements
{
    
    /** @var string Html prototype */
    private $prototype = "<fieldset %attributes%>%caption%%items%</fieldset>";
	
	/** @var string Html prototype of element label */
    private $labelPrototype = "<div class=\"frm-label frm-label-%elname% gr-label\">%label%</div>";
	
	/** @var string Html prototype of element label */
    private $controlPrototype = "<div class=\"frm-element frm-element-%elname% gr-element\">%element%</div>";
    
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
	
		$this->name;
		$this->attributes['id'] = $idpref . $name;
		$this->attributes['class'] = 'frm-group';
		$this->type = 'block';
		$this->value = '';
		$this->items = $items;
		$this->name = $name;
		$this->idPref = $idpref;
		$this->newline = $inline;
		$this->caption = null;
        parent::__construct( ($label ? $label : null) );
    
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
			if($item->getLabel())	$this->HtmlItems .= preg_replace(array('/%elname%/', '/%label%/'), array($item->getName(), $item->getLabel()), $this->labelPrototype);
			$this->HtmlItems .= preg_replace(array('/%elname%/', '/%element%/'), array($item->getName(), $item->getControl()), $this->controlPrototype);
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
	
	$this->name = $name;
	$this->type = 'breakline';
	$this->value = '';
	$this->name = $name;
	$this->attributes['id'] = "id_$name";
        parent::__construct( null );
    
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
	
	$this->name = $name;
	$this->attributes['id'] = $idpref . $name;
	$this->attributes['class'] = 'frm-inner-content';
	$this->type = 'frm-inner-content';
	$this->value = $value;
        parent::__construct( null );
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
    /** @property string $id Elemen id */
    public $id;
    
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
    function __construct($id, $name, $args)
    {
		$this->id = $id;
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
    private $prototype = "<div id=\"%contId%\">%errors%\n%formstart%\n\t<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n\t\t<tbody>\n\t\t\t%elements%\n\t\t</tbody>\n\t</table>\n\t\n\t%hiddens%\n%formend%</div>";
    
    /** @property string $elemPrototype Html prototype of element*/
    private $elemPrototype = "<tr><td class=\"frm-label %labelclass%\">%label%</td><td class=\"frm-control %controlclass%\">%control%</td></tr>\n\t\t\t";
    
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
    
    /** @property string $scripts code Of Form js Validator */
    private $scripts = '';
    
    /** @property string $formScript code Of Form js Validator */
    private $formScript = '';
    
    private $frmId;
    
    private $contId;
    
    public function __construct( $frmId ) {
        $this->frmId = $frmId;
        $this->contId = "wrapper-{$frmId}";
    }
    
    
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
	
	foreach($elements as $idx => $element) {
	    if(!$element->inGroup()) {
			
			$required = '';
			 // check if js validator
			$rules = $element->getRules();
			foreach($rules as $rule) {
				if($rule->rule == AppForm::FILLED or $rule->rule == AppForm::CAPTCHA) $required = ' required';
				$this->scripts .= new JsValidator($rule->id, $rule->name, $rule->rule, $rule->message, $rule->args);
			}
                
			if($element->getType() == 'hidden') {
				$this->hiddens .= preg_replace('/%hidden%/', $element->getControl(), $this->hiddenPrototype);
			}
			else {
				// if has description
				if($element->getDescription()) $this->elements .= preg_replace('/%description%/', $element->getDescription(), $this->descPrototype);
				$this->elements .= preg_replace(array('/%label%/', '/%labelclass%/', '/%control%/', '/%controlclass%/'), array( ($element->getLabel() ? $element->getLabel() : '&nbsp;'), "frm-label-{$idx}{$required}", $element->getControl(), "frm-control-{$idx}{$required}" ), $this->elemPrototype);
			}
                
			// check if js validator
			$rules = $element->getRules();
			foreach($rules as $rule) {
				$this->scripts .= new JsValidator($rule->id, $rule->name, $rule->rule, $rule->message, $rule->args);
			}
	    }
	}
        
        // if there are some scripts create main script
        if ($this->scripts !== '')
            $this->formScript = '<script type="text/javascript">
				/* <![CDATA[ */
				jQuery(document).ready(function($){
				    $("#'.$this->frmId.'").submit(function(){
					var proceed = true;
					' . $this->scripts . '
				    
					if(proceed) {
					    $("#loader").show();
                                            $(".frm-submit").css({"visibility":"hidden"});
					}
				    });
				});
				/* ]]> */
			    </script>';
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
	return preg_replace(array('/%contId%/', '/%errors%/', '/%formstart%/', '/%elements%/', '/%hiddens%/', '/%formend%/'), array($this->contId, $this->errors, $this->formstart, $this->elements, $this->hiddens, $this->formend), $this->prototype) . $this->formScript;
    }
}

class NewlineRenderer implements IFormRenderer
{
    /** @property string $prototype Html prototype */
    private $prototype = "<div id=\"%contId%\">%formstart%\n%errors%\n%elements%\n%hiddens%\n%formend%</div>";
    
    /** @property string $labelPrototype Html prototype of element label*/
    private $labelPrototype = "\t<div class=\"frm-label %labelclass%\">%label%</div>";
    
    /** @property string $elemPrototype Html prototype of element*/
    private $elemPrototype = "%label%%description%<div id=\"frmCtr%ElementId%\" class=\"frm-control %controlclass%\"%style%>%control%</div>";
    
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
    
    /** @property string $scripts code Of Form js Validator */
    private $scripts = '';
    
    /** @property string $formScript code Of Form js Validator */
    private $formScript = '';
    
    private $frmId;
    
    private $contId;
    
    public function __construct( $frmId ) {
        $this->frmId = $frmId;
        $this->contId = "wrapper-{$frmId}";
    }




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
	
	foreach($elements as $idx => $element) {
	    if(!$element->inGroup()) {
                
			$required = '';
			 // check if js validator
			$rules = $element->getRules();
			foreach($rules as $rule) {
				if($rule->rule == AppForm::FILLED or $rule->rule == AppForm::CAPTCHA) $required = ' required';
				$this->scripts .= new JsValidator($rule->id, $rule->name, $rule->rule, $rule->message, $rule->args);
			}
               
			if($element->getType() == 'hidden') {
				$this->hiddens .= preg_replace('/%hidden%/', $element->getControl(), $this->hiddenPrototype);
			}
			else if ($element->getType() == 'captcha') {

						$label = ($element->getLabel() ? preg_replace(array('/%label%/', '/%labelclass%/'), array( $element->getLabel(), "frm-label-{$idx}{$required}"), $this->labelPrototype) : '' );             
						$this->elements .= preg_replace(array('/%ElementId%/', '/%label%/', '/%description%/', '/%control%/', '/%controlclass%/'), array( $element->getId(), $label, ($element->getDescription() ? $element->getDescription() : ''), $element->getControl(), "frm-control-{$idx}{$required}" ), $this->elemPrototype);
			}
			else {
				if( $element instanceof InputElement && $element->isInline() ) $ctrlStyle = ' style="float:left; margin-right:6px; _margin-right:3px;"';
					else $ctrlStyle = ' style="float:none;"';
				$label = ($element->getLabel() ? preg_replace(array('/%label%/', '/%labelclass%/'), array( $element->getLabel(), "frm-label-{$idx}{$required}"), $this->labelPrototype) : '' );
						$this->elements .= preg_replace(array('/%ElementId%/', '/%label%/', '/%description%/', '/%control%/', '/%controlclass%/', '/%style%/'), array( $element->getId(), $label, ($element->getDescription() ? $element->getDescription() : ''), $element->getControl(), "frm-control-{$idx}{$required}", $ctrlStyle ), $this->elemPrototype);
			}
                
                
	    }
	}
        
        // if there are some scripts create main script
        if ($this->scripts !== '')
            $this->formScript = '<script type="text/javascript">
				/* <![CDATA[ */
				jQuery(document).ready(function($){
				    $("#'.$this->frmId.'").submit(function(){
					var proceed = true;
					' . $this->scripts . '
				    
					if(proceed) {
					    $("#loader").show();
					    $(".frm-submit").css({"visibility":"hidden"});
					}
				    });
				});
				/* ]]> */
			    </script>';
        
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
	return preg_replace(array('/%contId%/', '/%errors%/', '/%formstart%/', '/%elements%/', '/%hiddens%/', '/%formend%/'), array($this->contId, $this->errors, $this->formstart, $this->elements, $this->hiddens, $this->formend), $this->prototype) . $this->formScript;
    }
}

class JsValidator
{
    
    private $script = '';
    
    public function __construct($id, $name, $rule, $message, $args) {
        
        $element = $id ? "#$id" : "[name='$name']"; 
        
        if($rule == AppForm::FILLED) {
            $this->script .= '
                    var value = $("'.$element.'").val();
                    if(value == "") {
                        alert("'.$message.'");
                        $("'.$element.'").focus();
                        proceed = false;
                        return false;
                    }';
        }

        if($rule == AppForm::EMAIL) {
            $this->script .= '
                    var value = $("'.$element.'").val();
                    var regex = /^[^0-9][A-z0-9_-]+([.][A-z0-9_-]+)*[@][A-z0-9_-]+([.][A-z0-9_-]+)*[.][A-z]{2,4}$/;
                    if(value && !value.match(regex)) {
                        alert("'.$message.'");
                        $("'.$element.'").focus();
                        proceed = false;
                        return false;
                    }';
        }

        if($rule == AppForm::NUMERIC) {
            $this->script .= '
                    var value = $("'.$element.'").val();
                    var regex = /^[-]?[0-9]+([\.][0-9]+)?$/;
                    if(!value.match(regex)) {
                        alert("'.$message.'");
                        $("'.$element.'").focus();
                        proceed = false;
                        return false;
                    }';
        }
        
        if($rule == AppForm::INTEGER) {
            $this->script .= '
                    var value = $("'.$element.'").val();
                    var regex = /^[-]?[0-9]+$/;
                    if(!value.match(regex)) {
                        alert("'.$message.'");
                        $("'.$element.'").focus();
                        proceed = false;
                        return false;
                    }';
        }
        
        if($rule == AppForm::FLOAT) {
            $this->script .= '
                    var value = $("'.$element.'").val();
                    var regex = /^[-]?[0-9]+[\.][0-9]+$/;
                    if(!value.match(regex)) {
                        alert("'.$message.'");
                        $("'.$element.'").focus();
                        proceed = false;
                        return false;
                    }';
        }
        
        if($rule == AppForm::MIN) {
            $this->script .= '
                    var value = $("'.$element.'").val();
                    var regex = /^[0-9]+$/;
                    var compare = '.(int)$args[0].';
                    if( !value.match(regex) || value < compare ) {
                        alert("'.$message.'");
                        $("'.$element.'").focus();
                        proceed = false;
                        return false;
                    }';
        }
        
        if($rule == AppForm::MAX) {
            $this->script .= '
                    var value = $("'.$element.'").val();
                    var compare = '.(int)$args[0].';
                    var regex = /^[0-9]+$/;
                    if( !value.match(regex) || value > compare ) {
                        alert("'.$message.'");
                        $("'.$element.'").focus();
                        proceed = false;
                        return false;
                    }';
        }
        
        if($rule == AppForm::RANGE) {
            $this->script .= '
                    var value = $("'.$element.'").val();
                    var regex = /^[0-9]+$/;
                    var compareLow = '.(int)$args[0].';
                    var compareHigh = '.(int)$args[1].';
                    if( !value.match(regex) || ( value < compareLow || value > compareHigh ) ) {
                        alert("'.$message.'");
                        $("'.$element.'").focus();
                        proceed = false;
                        return false;
                    }';
        }
        
        if($rule == AppForm::SKIPFIRST) {
            $this->script .= '
                    var value = $("'.$element.' option:selected").val();
                    var compare = '.$args[0].'
                    if( value == compare) {
                        alert("'.$message.'");
                        $("'.$element.'").focus();
                        proceed = false;
                        return false;
                    }';
        }
		

        if($rule == AppForm::CAPTCHA) {
            $this->script .= '
                    var value = $("'.$element.'").val();
                    var regex = /'.$_SESSION[md5( baseUrl() )]['BZ_FORM']['security_code'].'/;
                    if(!value.match(regex)) {
                        alert("'.$message.'");
                        $("'.$element.'").focus();
                        proceed = false;
                        return false;
                    }';
        }

        if($rule == AppForm::EQUAL) {
			
			$frmId = current(explode('-', $element));
			$equalElement = "{$frmId}-{$args[0]}";
			
            $this->script .= '
                    var value = $("'.$element.'").val();
                    var equal = $("'.$equalElement.']").val();
                    if(value !== equal) {
                        alert("'.$message.'");
                        $("'.$element.'").focus();
                        proceed = false;
                        return false;
                    }';
        }

        if($rule == AppForm::REGEX) {
            $this->script .= '
                    var value = $("'.$element.'").val();
                    var regex = '.$args[0].';
                    if(value && !value.match(regex)) {
                        alert("'.$message.'");
                        $("'.$element.'").focus();
                        proceed = false;
                        return false;
                    }';
        }
        
        if($rule == AppForm::REGEX_NEGATIVE) {
            $this->script .= '
                    var value = $("'.$element.'").val();
                    var regex = '.$args[0].';
                    if(value && value.match(regex)) {
                        alert("'.$message.'");
                        $("'.$element.'").focus();
                        proceed = false;
                        return false;
                    }';
        }

        if($rule == 'isfilled') {
            $this->script .= '
                    if($("'.$element.'").val() !== "") {
                        var value = $("'.$element.'").val();
                        var regex = '.$message.';
                        if(!value.match(regex)) {
                            alert("'.$args[2].'");
                            $("'.$element.'").focus();
                            proceed = false;
                            return false;
                        }
                    }';
        }
    }
    
    
    public function getScript()
    {
        return $this->script;
    }
    
    public function __toString() {
        return $this->script;
    }
    
}
/*
//Inicialize
AppForm::sessionCheck();
AppForm::$pathToCaptcha = '';
*
 * 
 */