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
 * @version $Id: class.Form.php, v1.10.19 19:42 10/21/2010 $
 * @copyright Copyright (c) 2010 Branislav Zvolensky (http://www.mbmartworks.sk)
 * @author Branislav Zvolensky <zvolensky@mbmartworks.sk>
 * @license http://opensource.org/licenses/gpl-license.php GNU General Public License (GPL)
 * 
 * ==============================================================================
 * new in v 1.10.19
 * Added new rules for file upload verification LANDSCAPE, PORTRAIT, MIN_DIMENSIONS, MAX_DIMENSIONS
 * MIN_WIDTH, MAX_WIDTH, MIN_HEIGHT, MAX_HEIGHT.
 * Validating was inputed into class from output validation
 * 
 * new in v 1.10:
 * Added new rules for file upload verification UPLOADED, MAX_FILE_SIZE, IMAGE_ONLY, EXTENSIONS, EXTENSION
 * Added rendering system erros for file uploads
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

class Form {

    // Constants
	/** Validators */
	const FILLED = 'filled';
	const ISFILLED = 'isfilled';
	const EMAIL = 'email';
	const CAPTCHA = 'captcha';
	const EQUAL = 'equal';
	const NUMERIC = 'numeric';
	const RANGE = 'range';
	const MIN = 'min';
	const MAX = 'max';
	const REGEX = 'regex';
	const SKIPFIRST = 'skipfirst';
	
	// RULES FOR FILES
	const UPLOADED = 'uploaded';
	const MAX_FILE_SIZE = 'max_file_size';
	const IMAGE_ONLY = 'image_only';
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
	
	/**
	 * @var string
	 * id prefix
	 */
	private $idPref;
	
	/**
	 * @var array
	 * validation array
	 */
	private $valStr = array();
	
	/**
	 * @var bool
	 * isvalid status
	 */
	public static $isvalid;
	
	/**
	 * @var bool
	 * error status
	 */
	public $isError = FALSE;
	
	/**
	 * @var array
	 * error messages
	 */
	public $errors = array();
	
	/**
	 * @var bool
	 * return
	 */
	private $return = FALSE;
	
	/**
	 * @var int
	 * empty index
	 */
	private $emptyIndex = 1;
	
	/**
	 * @var int
	 * content index
	 */
	private $contentIndex = 1;
	
	/**
	 * @array
	 * form tag attributes
	 */
	public $frmTagAttr = array('id' => ' id=',
							   'action' => ' action=',
							   'method' => ' method=',
							   'enctype' => ' enctype=',
							   'class' => ' class=',
							   'style' => ' style=');
	
	/**
	 * @array
	 * form style values
	 */
	public $frmStyleValues = array('color' => 'black',
							       'background-color' => 'silver',
							       'font-size' => '10pt',
							       'font-family' => 'Tahoma, Arial',
							       'text-align' => 'left',
							       'vertical-align' => 'top',
								   'padding' => '10px');
	
	/**
	 * @array
	 * form start and end
	 */
	public $form = array('start' => array('control' => "\n<form%attributes%>\n"),
	                     'end' => array('control' => "</form>\n")
						 );
	
	
	/**
	 * @var
	 * input tag string
	 */
	public $input = "<input%id%%attributes%/>\n";
	
	/**
	 * @var
	 * radio input tag string
	 */
	public $radioinput = "\t<input%attributes% %check%/> %option% %nl%\n";
	
	/**
	 * @var
	 * checkbox input tag string
	 */
	public $checkinput = "\t<input%attributes% %check%/> %option% %nl%\n";
	
	/**
	 * @var
	 * file input tag string
	 */
	public $fileinput = "<input%id%%attributes% style=\"background-color: white\" />\n";
	
	/**
	 * @var
	 * textarea tag string
	 */
	public $textarea = "<textarea%attributes%>%text%</textarea>\n";
	
	/**
	 * @var
	 * select tag string
	 */
	public $select = "<select%attributes%%multiple%>\n%options%</select>";
	
	/**
	 * @var
	 * option tag string
	 */
	public $option = "\t<option value=\"%value%\"%select%>%option%</option>\n";
	
	/**
	 * @array
	 * text input tag attributes
	 */
	public $textInputAttr = array('id' => '',
	                              'class' => '',
	                              'type' => 'text',
							      'name' => '',
								  'value' => '',
							      'size' => '40',
							      'maxlength' => '256'
								 );
	
	/**
	 * @array
	 * pwd input tag attributes
	 */
	public $pwdInputAttr = array('id' => '',
	                             'class' => '',
	                             'type' => 'password',
								 'name' => '',
							     'value' => '',
							     'size' => '40',
							     'maxlength' => '256'
								 );
	
	/**
	 * @array
	 * hidden input tag attributes
	 */
	public $hiddenInputAttr = array('id' => '',
	                                'type' => 'hidden',
									'name' => '',
							        'value' => ''
								   );
	
	/**
	 * @array
	 * submit input tag attributes
	 */
	public $submitInputAttr = array('id' => '',
	                                'class' => '',
	                                'type' => 'submit',
									'name' => '',
							        'value' => ''
								   );
	
	/**
	 * @array
	 * reset input tag attributes
	 */
	public $resetInputAttr = array('id' => '',
	                               'class' => '',
	                               'type' => 'reset',
								   'name' => '',
							       'value' => ''
								  );
	
	/**
	 * @array
	 * radio input tag attributes
	 */
	public $radioInputAttr = array('id' => '',
	                               'class' => '',
	                               'type' => 'radio',
							       'name' => '',
								   'value' => '',
								  );
	
	/**
	 * @array
	 * checkbox input tag attributes
	 */
	public $checkboxAttr = array('id' => '',
	                             'class' => '',
	                               'type' => 'checkbox',
							       'name' => '',
								   'value' => '',
								  );
	
	/**
	 * @array
	 * file input tag attributes
	 */
	public $fileInputAttr = array('id' => '',
	                              'class' => '',
	                              'type' => 'file',
								  'name' => '',
								  'size' => '40'
							      );
	
	/**
	 * @array
	 * Textarea tag attributes
	 */
	public $textareaAttr = array('id' => '',
	                             'class' => '',
								 'name' => '',
							     'cols' => '30',
							     'rows' => '5'
								 );
	
	/**
	 * @array
	 * Select tag attributes
	 */
	public $selectAttr = array('id' => '',
	                           'class' => '',
							   'name' => ''
							  );
	
	/**
	 * @var mixed
	 * output for render
	 */
	private $output;
	
	private $scripts = '';
	
	private $oldToken;
	
	private $postData;
	
	private $id;

	// Constructor
	public function __construct($name=NULL,$cssclass=NULL,$action=NULL) {
	
		if($name){
			$this->id = 'form' . ucfirst($name);
			$this->frmTagAttr['id'] .= $this->collAttr('form' . ucfirst($name));
			$this->idPref = 'form' . ucfirst($name) . '-'; // set id prefix
		}
		else {
			$this->id = 'formBZ';
			$this->frmTagAttr['id'] .= $this->collAttr('form');
			$this->idPref = 'form-'; // set id prefix
		}
		
		if($cssclass){
			$this->frmTagAttr['class'] .= $this->collAttr($cssclass);
			$this->frmTagAttr['style'] .=  '""';
		}
		else {
			$this->frmTagAttr['class'] .= '""';
			$this->frmTagAttr['style'] .=  $this->collStyle();
		}
		if($action){
			$this->frmTagAttr['action'] .= $this->collAttr($action);
		}
		else {
			$this->frmTagAttr['action'] .= '""';
		}
		$this->frmTagAttr['method'] .= $this->collAttr('post');
		$this->frmTagAttr['enctype'] .= $this->collAttr('multipart/form-data');
		
		$this->output='';
		
		if(isset($_SESSION['__BZ_FORM']['token'])) {
		    $this->oldToken = $_SESSION['__BZ_FORM']['token'];
		    unset($_SESSION['__BZ_FORM']['token']);
		}
		
		$this->postData = $_POST;
		/**
		if(isset($_POST['validate'])) {
                    
		    // Validation
                    $_SESSION['__BZ_FORM']['errors'] = '';
                    $result = $this->frmValidate($_POST, $_FILES);
		    
		    if(!$result){
			self::$isvalid = false;
			$_SESSION['__BZ_FORM']['errors'] .= $this->renderErrors();
		    }
		    else{
			self::$isvalid = true;
		    }
		}
                 *
                 */

	}
	
	// Methods
	
	/** Collects attribute for Form Tag */
	private function collAttr($value) {

        return '"' . $value . '"';

	}
	
	/** collects Style Attribute for Form Tag*/
	private function collStyle() {

        $styleStr = '';

		foreach($this->frmStyleValues as $style => $value){
			$styleStr .= $style . ':' . $value . ';';
		}
		
		$style = $this->collAttr($styleStr);
		
		return $style;

	}
	
	/** adds or set style property in Form Tag */
	public function setStyle($style, $value) {

        if(!$style or !$value){
			echo 'Error: empty parameter in setStyle() method.';
		}
		else{
			$this->frmStyleValues[strtolower($style)] = $value;
		}
		
		$this->frmTagAttr['style'] =  ' style='.$this->collStyle(); //recollect style

	}
	
	/** removes style property from Form Tag */
	public function removeStyle($style) {

        if(!$style){
			echo 'Error: empty parameter in removeStyle() method.';
		}
		elseif(!array_key_exists(strtolower($style), $this->frmStyleValues)) {
    		echo 'Error: No style to remove.';
		}
		else{
			unset($this->frmStyleValues[strtolower($style)]);
		}
		
		$this->frmTagAttr['style'] =  ' style='.$this->collStyle(); // recollect style

	}
	
	/** adds indexed <br/> tag into form */
	public function emptyLine()
	{
		$name = 'empty'.$this->emptyIndex;
		$this->emptyIndex++;
		$this->addElement($name, 'empty', "<br/>\n", '', FALSE, false, '');
	}
	
	/** adds indexed <div></div> tag into form */
	public function insertContent($str=null)
	{
		if(!$str){
			echo '<span style="background:#fff;color:red"><b>Function insertContent():</b> Missing argument $str!</span>';
			exit;
		}
		else{
			$name = 'content'.$this->contentIndex;
			$div = "<div class='frm-inner-content' id='frm-content-".$this->contentIndex."'>$str</div>\n";
			$this->contentIndex++;
			$this->addElement($name, 'content', $div, '', FALSE, false, '');
		}
	}
	
	/** return completed form start tag */
	public function start()
	{
		// collect Attributes
		$tags = '';
		foreach($this->frmTagAttr as $tag){
			$tags .= $tag;
		}
		
		return preg_replace('/%attributes%/', $tags, $this->form['start']['control']);	// insert Attributes into <form>
		
	}
	
	/** return form ending tag */
	public function end()
	{
		
		return $this->form['end']['control'];
		
	}
	
	/** render single item */
	public function renderSingle($name=null)
	{
	  if(!$name){
	    echo 'Error: Missing parameter name.';
		exit;
	  }
	  else{
	    $item = $this->form[$name];  
	    if($item['label']) $item['title']= preg_replace('/%label%/', $item['label'], $item['title']);
		// if has description add it
		$item['control']= preg_replace('/%description%/', ($item['description'] ? $item['description'] : ''), $item['control']);
							
		// if has group description add it
		$item['control']= preg_replace('/%groupdescription%/', ($item['groupdescription'] ? $item['groupdescription'] : ''), $item['control']);
		
		return '<div class="frm-label">'.$item['title'].'</div>' . '<div class="frm-item">'.$item['control'].'</div>';
	  
	  }
	  
	
	}
	
	/** render single item label */
	public function renderSingleLabel($name=null)
	{
	  if(!$name){
	    echo 'Error: Missing parameter name.';
		exit;
	  }
	  else{
	    $item = $this->form[$name];  
	    if($item['label']) $item['title']= preg_replace('/%label%/', $item['label'], $item['title']);
		
		return $item['title'];
	  
	  }
	}
	
	/** render single item control */
	public function renderSingleControl($name=null)
	{
	  if(!$name){
	    echo 'Error: Missing parameter name.';
		exit;
	  }
	  else{
	    $item = $this->form[$name];  
	    // if has description add it
	    $item['control']= preg_replace('/%description%/', ($item['description'] ? $item['description'] : ''), $item['control']);
							
	    // if has group description add it
	    $item['control']= preg_replace('/%groupdescription%/', ($item['groupdescription'] ? $item['groupdescription'] : ''), $item['control']);
		
		return $item['control'];
	  
	  }
	}
	
	/** render complete form */
	public function render($method='newline') {
	
		
		$this->output = $this->start();
		
		$this->output .= $this->renderErrors();
		
		// if any element has rules create hidden element
		if($this->valStr){
			//$this->addHidden('validate', htmlMyEnts(serialize($this->valStr)));
                        $this->addHidden('validate', base64_encode(serialize($this->valStr)));
		}
		
		if($method == 'table'){
			$title = '<tr><td class="frm-label">%title%</td>';
			$control = '<td class="frm-control">%control%</td></tr>';
		}
		elseif($method == 'newline'){
			$title = '<div class="frm-label">%title%</div>';
			$control = '<div class="frm-control">%control%</div>';
		}
		
		if($method == 'table') $this->output .= '<table border="0" cellspacing="0" cellpadding="3">';
		$hiddens = ''; // define hiddens
		foreach($this->form as $key => $item){
	
			if($key == 'end' or $key == 'start'){
			   // do nothing
			}
			else{
				// If not In Group render single element
				if(!$item['ingroup']){
					// after these items add <br />

                                        if($item['type'] == 'hidden') {
                                            $hiddens .= $item['control'];
                                        }
					elseif($item['type'] == 'text' or $item['type'] == 'select'
							or $item['type'] == 'password' or $item['type'] == 'reset' or $item['type'] == 'file' or $item['type'] == 'textarea'){
						
						if($item['label']) $item['title']= preg_replace('/%label%/', $item['label'], $item['title']);
						// if has description add it
						$item['control']= preg_replace('/%description%/', ($item['description'] ? $item['description'] : ''), $item['control']);
							
						// if has group description add it
						$item['control']= preg_replace('/%groupdescription%/', ($item['groupdescription'] ? $item['groupdescription'] : ''), $item['control']);
						
						$this->output .= preg_replace('/%title%/', $item['title'], $title) . preg_replace('/%control%/',$item['control'], $control)."\n";
					}
					elseif($item['type'] == 'checkboxgroup' or $item['type'] == 'radio'){
						if($item['label']) $item['control']= preg_replace('/%label%/', $item['label'], $item['control']);
						// if has description add it
						$item['control']= preg_replace('/%description%/', ($item['description'] ? $item['description'] : ''), $item['control']);
							
						// if has group description add it
						$item['control']= preg_replace('/%groupdescription%/', ($item['groupdescription'] ? $item['groupdescription'] : ''), $item['control']);
						
						$this->output .= preg_replace('/%control%/',$item['control'], $control)."\n";
					}
					else{
						// if has description add it
						$item['control']= preg_replace('/%description%/', ($item['description'] ? $item['description'] : ''), $item['control']);
							
						// if has group description add it
						$item['control']= preg_replace('/%groupdescription%/', ($item['groupdescription'] ? $item['groupdescription'] : ''), $item['control']);
					        if($item['label']) $item['title']= preg_replace('/%label%/', $item['label'], $item['title']);
						$this->output .= preg_replace('/%title%/', $item['title'], $title) . preg_replace('/%control%/',$item['control'], $control);
					}
					if(isset($item['script'])) $this->scripts .= $item['script'];
				}
				/*else{
					$this->output .= $item['control'];
				}*/
			}
			
		}

                //add hiddens

		if($method == 'table') $this->output .= '</table>';

                $this->output .= "<div>$hiddens</div>";
		
		$this->output .= $this->end();
		
		$this->output .= '<script type="text/javascript">
				/* <![CDATA[ */
				jQuery(document).ready(function($){
				    $("#'.$this->id.'").submit(function(){
					var proceed = true;
					' . $this->scripts . '
				    
					if(proceed) {
					    $("#loader").show();
					    $(".frm-submit").attr("disabled", "disabled");
					}
				    });
				});
				/* ]]> */
			    </script>';
		
		return $this->output;
	
	}
	
	/** Adds token */
	public function addProtection($token = null)
	{
	    if(!$token) $token = sha1(date('U'));
	    
	    $_SESSION['__BZ_FORM']['token'] = $token;
	    
	    $this->addHidden('__bz_form_token', $token);
		$this->addRule('__bz_form_token', 'protection', 'Neplatné odslanie formulára. Pravdepodobne ste znovunačítali stránku po prvom odoslaní formulára.');
	}
	
	/** Adds text input */
	public function addText($name=NULL, $label=NULL, $value='', $className='frm-text',$size='40', $maxsize='256') {
		
		if(!$name){
			echo 'Error: Set name of text input.';
		}
		else{
			
			if($label) {
				$labTag = "<label>%label%</label>\n";
			}
			else{
				$labTag = '';
			}
			$tag = '%description%';
			// Collect Tag Attributes
			$this->textInputAttr['id'] = $this->idPref . $name;
			$this->textInputAttr['class'] = $className;
			$this->textInputAttr['value'] = $value;
			$this->textInputAttr['name'] = $name;
			$this->textInputAttr['size'] = $size;
			$this->textInputAttr['maxlength'] = $maxsize;
			
			$attributes = $this->textInputAttr;
			
			$id = ' id="'.array_shift($attributes).'"';
			
			$attr = '';
			foreach($attributes as $key => $val){
				$attr .= ' '.$key.'="'.$val.'"';
			}
			
			$replace = array('/%id%/', '/%attributes%/');
			$replacement = array($id, $attr);
			
			$tag .= preg_replace($replace, $replacement, $this->input);	// insert Attributes into <input>
			
			$this->addElement($name, 'text', $tag, $value, true, false, $label, $labTag);
		}
	
	}
	
	/** Adds password input */
	public function addPassword($name=NULL, $label=NULL, $value='', $className='frm-password', $size='40', $maxsize='256') {
		
		if(!$name){
			echo 'Error: Set name of password input.';
		}
		else{
			
			if($label) {
				$labTag = "<label>%label%</label>\n";
			}
			else{
				$labTag = '';
			}
			$tag = '';
			// Collect Tag Attributes
			$this->pwdInputAttr['id'] = $this->idPref . $name;
			$this->pwdInputAttr['class'] = $className;
			$this->pwdInputAttr['value'] = $value;
			$this->pwdInputAttr['name'] = $name;
			$this->pwdInputAttr['size'] = $size;
			$this->pwdInputAttr['maxlength'] = $maxsize;
			
			$attributes = $this->pwdInputAttr;
			
			$id = ' id="'.array_shift($attributes).'"';
			
			$attr = '';
			foreach($attributes as $key => $val){
				$attr .= ' '.$key.'="'.$val.'"';
			}
			
			$replace = array('/%id%/', '/%attributes%/');
			$replacement = array($id, $attr);
			
			$tag .= preg_replace($replace, $replacement, $this->input);	// insert Attributes into <input>
						
			$this->addElement($name, 'password', $tag, $value, true, false, $label, $labTag);

		}
	
	}
	
	/** Adds hidden input */
	public function addHidden($name=NULL, $value=NULL) {
		
		if(!$name){
			echo 'Error: Set name of hidden input.';
		}
		else{
			// Collect Tag Attributes
			$this->hiddenInputAttr['id'] = $this->idPref . $name;
			$this->hiddenInputAttr['value'] = $value;
			$this->hiddenInputAttr['name'] = $name;
			
			$attributes = $this->hiddenInputAttr;
			
			$id = ' id="'.array_shift($attributes).'"';
			
			$attr = '';
			foreach($attributes as $key => $val){
				$attr .= ' '.$key.'="'.$val.'"';
			}
			
			$replace = array('/%id%/', '/%attributes%/');
			$replacement = array($id, $attr);
			
			$tag = preg_replace($replace, $replacement, $this->input);	// insert Attributes into <input>
			
			$this->addElement($name, 'hidden', $tag, $value);

		}
	
	}
	
	/** Adds captcha input */
	public function addCaptcha($name=NULL, $label=NULL, $imgwidth='120', $imgheight='40', $inpsize='6') {
		
		if(!$name){
			echo 'Error: Set name of input.';
		}
		else{
			
			if($label) {
				$labTag = "<label>%label%</label>\n";
			}
			else{
				$labTag = '';
			}
			$tag = '%description%';
			// Collect Tag Attributes
			$this->textInputAttr['id'] = $this->idPref . $name;
			$this->textInputAttr['value'] = '';
			$this->textInputAttr['name'] = $name;
			$this->textInputAttr['size'] = $inpsize;
			$this->textInputAttr['maxlength'] = $inpsize;
						
			$attributes = $this->textInputAttr;
			
			$id = ' id="'.array_shift($attributes).'"';
			
			$attr = '';
			foreach($attributes as $key => $val){
				$attr .= ' '.$key.'="'.$val.'"';
			}
			
			$replace = array('/%id%/', '/%attributes%/');
			$replacement = array($id, $attr);
			
			$tag .= '<div id="captcha-img"><img src="'.BASEPATH.'/images/'.$this->CaptchaSecurityImages($imgwidth, $imgheight, $inpsize, $name).'" alt="code" /></div>';
			
			$tag .= preg_replace($replace, $replacement, $this->input);	// insert Attributes into <input>
			
			$this->addElement($name, 'text', $tag, '', false, false, $label, $labTag);
		}
	
	}
	
	/** Adds submit input */
	public function addSubmit($name=NULL, $value=NULL, $className='frm-submit') {
		
		if(!$name){
			echo 'Error: Set name of submit input.';
		}
		//elseif(!$value){
		//	echo 'Error: Set value of submit input.';
		//}
		else{
			// Collect Tag Attributes
			$this->submitInputAttr['id'] = $this->idPref . $name;
			$this->submitInputAttr['class'] = $className;
			$this->submitInputAttr['value'] = $value;
			$this->submitInputAttr['name'] = $name;
			
			$attributes = $this->submitInputAttr;
			
			$id = ' id="'.array_shift($attributes).'"';
			
			$attr = '';
			foreach($attributes as $key => $val){
				$attr .= ' '.$key.'="'.$val.'"';
			}
			
			$replace = array('/%id%/', '/%attributes%/');
			$replacement = array($id, $attr);
			
			$tag = preg_replace($replace, $replacement, $this->input);	// insert Attributes into <input>
			
			$this->addElement($name, 'submit', $tag, $value, FALSE);
		}
	
	}
	
	/** Adds reset button */
	public function addReset($name=NULL, $value=NULL, $className='frm-reset') {
		
		if(!$name){
			echo 'Error: Set name of reset button.';
		}
		elseif(!$value){
			echo 'Error: Set value of reset button.';
		}
		else{
			// Collect Tag Attributes
			$this->resetInputAttr['id'] = $this->idPref . $name;
            $this->resetInputAttr['class'] = $className;
			$this->resetInputAttr['value'] = $value;
			$this->resetInputAttr['name'] = $name;
			
			$attributes = $this->resetInputAttr;
			
			$id = ' id="'.array_shift($attributes).'"';
			
			$attr = '';
			foreach($attributes as $key => $val){
				$attr .= ' '.$key.'="'.$val.'"';
			}
			
			$replace = array('/%id%/', '/%attributes%/');
			$replacement = array($id, $attr);
			
			$tag = preg_replace($replace, $replacement, $this->input);	// insert Attributes into <input>
			
			$this->addElement($name, 'reset', $tag, $value, false);
			
		}
	
	}
	
	/** Adds Textarea Tag*/
	public function addTextarea($name=NULL, $label="", $text="", $className='frm-textarea', $cols='30', $rows='5', $wrap='soft') {
	
    	if(!$name){
			echo 'Error: Set name of Textarea.';
		}
		else{
			
			if($label) {
				$labTag = "<label>%label%</label>\n";
			}
			else{
				$labTag = '';
			}
			$tag = '%description%';
			// Collect Tag Attributes
			$this->textareaAttr['id'] = $this->idPref . $name;
			$this->textareaAttr['class'] = $className;
			$this->textareaAttr['name'] = $name;
			$this->textareaAttr['cols'] = $cols;
			$this->textareaAttr['rows'] = $rows;
                        $this->textareaAttr['wrap'] = $wrap;
			$attr = '';
			foreach($this->textareaAttr as $key => $value){
				$attr .= ' '.$key.'="'.$value.'"';
			}
			
			$tag .= preg_replace('/%attributes%/', $attr, $this->textarea);	// insert Attributes into <textarea>
			$tag = preg_replace('/%text%/', $text, $tag);	// insert value into <textarea>
			
			$this->addElement($name, 'textarea', $tag, $text, true, false, $label, $labTag);
		}

	}
	
	/** Adds Select Tag*/
	public function addSelect($name=NULL, $valAndOps=array(""), $label=NULL, $selected="", $className='frm-select',$size=NULL, $multiple=NULL) {
	
    	if(!$name){
			echo 'Error: Set name of Select.';
		}
		else{
			
			if($label) {
				$labTag = "<label>%label%</label>\n";
			}
			else{
				$labTag = '';
			}
		    $tag = '%description%';
			// Collect Options
			$options = '';
			foreach($valAndOps as $value => $option){
			    
				// Check for selected item
				if($selected == $value) {
					$select = ' selected="selected"';
				}
				else {
					$select = "";
				}
				
				$op = preg_replace('/%value%/', $value, $this->option);	// insert Attributes into <option>
				$op = preg_replace('/%select%/', $select, $op);
				$op = preg_replace('/%option%/', $option, $op);
				
				$options .= $op;
			
			}
			
			// Collect Tag Attributes
			if($size) {
				$this->selectAttr['size'] = $size;
				// If set size may be multiple
				if($multiple){
					$select = preg_replace('/%multiple%/', ' multiple', $this->select);	// if list and multiple
				}
				else{
					$select = preg_replace('/%multiple%/', '', $this->select); // if list only
				}
			}
			else{
				$select = preg_replace('/%multiple%/', '', $this->select); // if not list not multiple allowed
			}
			
			$this->selectAttr['id'] = $this->idPref . $name;
			$this->selectAttr['name'] = $name;
			$this->selectAttr['class'] = $className;
			$attr = '';
			foreach($this->selectAttr as $key => $val){
				$attr .= ' '.$key.'="'.$val.'"';
			}
			
			$tag .= preg_replace('/%attributes%/', $attr, $select);	// insert Attributes into <select>
			$tag = preg_replace('/%options%/', $options, $tag);	// insert Options into <select>
			
			$this->addElement($name, 'select', $tag, $selected, true, false, $label, $labTag);
			
		}

	}
	
	/** Adds Time picker*/
	public function addTimepicker($name=NULL, $label=NULL, $selHours='', $selMins='')
	{
		if(!$name){
			echo 'Error: Set name for Time Picker.';
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
		
			if($label) $labTag = "<span>%label%</span>\n";
			//Make Field set
			$tag = "<div class='frm-time-picker'>\n";
			$tag .= '%groupdescription%';
			$selects = '';
			
			// Call addSelect()
			$this->addSelect($name.'_hour',$hoursArr,'',$selHours);
			$this->elementInGroup($name.'_hour'); //put element into group
			$selects .= $this->form[$name.'_hour']['control'];
			
			$this->addSelect($name.'_min',$minsArr,':',$selMins);
			$this->elementInGroup($name.'_min'); //put element into group
			$selects .= $this->form[$name.'_min']['label'].$this->form[$name.'_min']['control'];
			
			$tag .= $selects;
			
			$tag .= "</div>\n";
			
			$this->addElement($name, 'timepicker', $tag, '', false, false, $label, $labTag);
			
		}
		
	}
	
	/** Adds Date picker*/
	public function addDatepicker($name=NULL, $label=NULL, $format='YYYY-MM-DD', $selDay='', $selMonth='', $selYear='', $years=null)
	{
		
		if(!$name){
			echo 'Error: Set name for Date Picker.';
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
		
			if($label) $labTag = "<span>%label%</span>\n";
			//Make Field set
			$tag = "<div class='frm-date-picker>\n";
			$tag .= '%groupdescription%';
			$selects = '';
			
			// Call addSelect()
			$this->addSelect($name.'_month',$monthsArr,'',$selMonth);
			$this->elementInGroup($name.'_month'); //put element into group
				
			$monthTag = $this->form[$name.'_month']['control'];
			
			$this->addSelect($name.'_day',$daysArr,'',$selDay);
			$this->elementInGroup($name.'_day'); //put element into group
				
			$dayTag = $this->form[$name.'_day']['control'];
			
			$this->addSelect($name.'_year',$yearsArr,'',$selYear);
			$this->elementInGroup($name.'_year'); //put element into group
				
			$yearTag = $this->form[$name.'_year']['control'];
			
			if($format == 'YYYY-MM-DD')	$tag .= $yearTag.'-'.$monthTag.'-'.$dayTag;
			if($format == 'DD.MM.YYYY')	$tag .= $dayTag.'.'.$monthTag.'.'.$yearTag;
			
			$tag .= "</div>\n";
			
			$this->addElement($name, 'datepicker', $tag, '', false, false, $label, $labTag);
		}
		
	}
	
	/** Adds Radio buttons*/
	public function addRadio($name=NULL, $valAndOps, $label=NULL, $checked="", $className='frm-radio', $inline=TRUE) {
		
	    if(!$name){
			echo'Error: Set name for radio';
		}
		elseif(!is_array($valAndOps)){
			echo 'Error: Values and Options must be in array.';
		}
		else{
		
			// Make as Field set
			$tag = "<fieldset>\n";
	    	if($label) $tag .= "\t<legend>%label%</legend>\n";
			$tag .= '%groupdescription%';
			$idIndex = 1;
			$radios = '';
			foreach($valAndOps as $value => $option){

				// Check for checked item
				if($checked == $value) {
					$check = ' checked="checked" ';
				}
				else {
					$check = "";
				}
				
				// Collect Tag Attributes
				$this->radioInputAttr['id'] = $this->idPref . $name . "-$idIndex";
				$this->radioInputAttr['class'] = $className;
				$this->radioInputAttr['value'] = $value;
				$this->radioInputAttr['name'] = $name;
				$attr = '';
				foreach($this->radioInputAttr as $key => $val){
					$attr .= ' '.$key.'="'.$val.'"';
				}
				
				$radio = preg_replace('/%attributes%/', $attr, $this->radioinput);	// insert Attributes into <input>
				$radio = preg_replace('/%check%/', $check, $radio);
				$radio = preg_replace('/%option%/', $option, $radio);
				
				if(!$inline){
					$radio = preg_replace('/%nl%/', '<br/>', $radio);
				}
				else{
					$radio = preg_replace('/%nl%/', '', $radio);
				}
				
				$radios .= $radio;
				
				$idIndex++;
			}
			
			$tag .= $radios;
			
			$tag .= "</fieldset>\n";
			
			$this->addElement($name, 'radio', $tag, $checked, true, false, $label);
			
		}
		
	}
	
	/** Adds Single Check box */
	public function addCheckbox($name=NULL, $value=NULL, $option=NULL, $label=NULL, $checked=FALSE, $className='frm-checkbox', $inline=FALSE) {
	
    	if(!$name){
			echo'Error: Set name for checkbox';
		}
		elseif(!$value){
			echo 'Error: Value must be set.';
		}
		//elseif(!$option){
		//	echo 'Error: Option must be set.';
		//}
		else{
		
			if($label) {
				$labTag = "<label>%label%</label>\n";
			}
			else{
				$labTag = '';
			}
			$tag = '%description%';
			// Check for checked item
			if($checked) {
				$check = ' checked="checked" ';
			}
			else {
				$check = "";
			}
			
			// Collect Tag Attributes
			$this->checkboxAttr['id'] = $this->idPref . $name;
			$this->checkboxAttr['class'] = $className;
			$this->checkboxAttr['value'] = $value;
			$this->checkboxAttr['name'] = $name;
			$attr = '';
			foreach($this->checkboxAttr as $key => $val){
				$attr .= ' '.$key.'="'.$val.'"';
			}
				
			$checkbox = preg_replace('/%attributes%/', $attr, $this->checkinput);	// insert Attributes into <input>
			$checkbox = preg_replace('/%check%/', $check, $checkbox);
			$checkbox = preg_replace('/%option%/', $option, $checkbox);
			
			if(!$inline){
				$checkbox = preg_replace('/%nl%/', '<br/>', $checkbox);
			}
			else{
				$checkbox = preg_replace('/%nl%/', '', $checkbox);
			}
				
			$tag .= $checkbox;
			
			$this->addElement($name, 'checkbox', $tag, $value, true, false, $label, $labTag);
		}
		

	}
	
	/** Adds Group of Checkboxes*/
	public function addCheckboxGroup($name=NULL, $attributes, $label=NULL, $inline=TRUE)
	{
		if(!$name){
			echo 'Error: Set name for Checkbox Group.';
		}
		elseif(!is_array($attributes)){
			echo 'Error: Attributes must be multidimensional array.';
		}
		else{
			//Make Field set
			$tag = "<fieldset>\n";
			if($label) $tag .= "\t<legend>%label%</legend>\n";
			$tag .= '%groupdescription%';
			$checks = '';
			foreach($attributes as $att){
				// Call addCheckbox()
				if(isset($att[0])) $att['name'] = $att[0];
				if(isset($att[1])) $att['value'] = $att[1];
				if(isset($att[2])) $att['option'] = $att[2];
				if(isset($att[3])) $att['checked'] = $att[3];
				
				$this->addCheckbox($att['name'],$att['value'],$att['option'], NULL, $att['checked'],'frm-checkbox-group',$inline);
				
				$this->addElement($att['name'], 'checkbox', $this->form[$att['name']]['control'], $att['value'], TRUE, TRUE);
				
				$checks .= $this->form[$att['name']]['control'];
				
				//unset($this->form[$att['name']]);
				
			}
			
			$tag .= $checks;
			
			$tag .= "</fieldset>\n";
			
			$this->addElement($name, 'checkboxgroup', $tag, '', false, false, $label);
			
			//$this->form[$name]['control'] = $tag;
			//$this->form[$name]['type'] = 'checkboxgroup';
		}
		
	}
	
	/** Adds File input */
	public function addFile($name=NULL, $label=NULL, $size='50', $className='frm-file') {
	
    	if(!$name){
			echo 'Error: Set name for file input.';
		}
		else{
			if($label) {
				$labTag = "<label>%label%</label>\n";
			}
			else{
				$labTag = '';
			}
			
			$tag = '%description%';
			// Collect Tag Attributes
			$this->fileInputAttr['id'] = $this->idPref . $name;
			$this->fileInputAttr['class'] = $className;
			$this->fileInputAttr['size'] = $size;
			$this->fileInputAttr['name'] = $name;
			
			$attributes = $this->fileInputAttr;
			
			$id = ' id="'.array_shift($attributes).'"';
			
			$attr = '';
			foreach($attributes as $key => $val){
				$attr .= ' '.$key.'="'.$val.'"';
			}
			
			$replace = array('/%id%/', '/%attributes%/');
			$replacement = array($id, $attr);
			
			$tag .= preg_replace($replace, $replacement, $this->input);	// insert Attributes into <input>
			
			$this->addElement($name, 'file', $tag, FALSE, true, false, $label, $labTag);
		}
		
	}
	
	/** Adds whole element into form array */
	private function addElement($name, $type, $control,  $value='', $dbfield=TRUE, $ingroup=FALSE, $label='', $labTag=''){
		$this->form[$name]['type'] = $type;		
		$this->form[$name]['control'] = $control;
		$this->form[$name]['value'] = $value;
		$this->form[$name]['dbfield'] = $dbfield;
		$this->form[$name]['ingroup'] = $ingroup;
		$this->form[$name]['label'] = $label;
		$this->form[$name]['title'] = $labTag;
		$this->form[$name]['description'] = null;
		$this->form[$name]['groupdescription'] = null;
		$this->form[$name]['script'] = '';
	}
	
	/** Adds whole element into form array */
	private function elementInGroup($name){
		$this->form[$name]['ingroup'] = true;
	}
	
	/**
	 * Adds a rule to form element
	 * @param string
	 * @param string
	 * @param string
	 * @return void
	 */
	public function addRule()
	{
		$args = func_get_args();
		$name = array_shift($args);
		if(!isset($this->form[$name])){
			echo "Error: The name <b>$name</b> of element is missing. Cannot add rule.";
		}
		else{
		    // if argument is FILLED the add required to label
			if($args[0]=='filled') $this->form[$name]['label'] = $this->form[$name]['label'].'<span class="required"><sup> *</sup></span>';
		
			/*
                        if(!$this->valStr){
				$argsString = $name;
			}
			else{
				$argsString = '%elem%'.$name;
			}
			$count = count($args);
			for($i = 0; $i < $count; $i++){
				$argsString .= '%argum%'.$args[$i]; // add next args	
			}
			$this->valStr .= $argsString;
                        */
                        $this->valStr[$name]  = $args;
			
			if($args[0] == 'filled') {
			    $this->form[$name]['script'] .= '
				    var value = $("[name='.$name.']").val();
				    if($.trim(value) === "") {
					alert("'.$args[1].'");
					$("[name='.$name.']").focus();
					proceed = false;
					return false;
				    }';
			}
			
			if($args[0] == 'email') {
			    $this->form[$name]['script'] .= '
				    var value = $("[name='.$name.']").val();
				    var regex = /^[^0-9][A-z0-9_-]+([.][A-z0-9_-]+)*[@][A-z0-9_-]+([.][A-z0-9_-]+)*[.][A-z]{2,4}$/;
				    if(value && !value.match(regex)) {
					alert("'.$args[1].'");
					$("[name='.$name.']").focus();
					proceed = false;
					return false;
				    }';
			}
			
			if($args[0] == 'numeric') {
			    $this->form[$name]['script'] .= '
				    var value = $("[name='.$name.']").val();
				    var regex = /^[0-9]+$/;
				    if(!value.match(regex)) {
					alert("'.$args[1].'");
					$("[name='.$name.']").focus();
					proceed = false;
					return false;
				    }';
			}
			
			if($args[0] == Form::MIN) {
			    $this->form[$name]['script'] .= '
				    var value = $("[name='.$name.']").val();
				    if(value < '.$args[1].')) {
					alert("'.$args[2].'");
					$("[name='.$name.']").focus();
					proceed = false;
					return false;
				    }';
			}
			
			if($args[0] == 'captcha') {
			    $this->form[$name]['script'] .= '
				    var value = $("[name='.$name.']").val();
				    var regex = /'.$_SESSION['__BZ_FORM']['class_form_'.$name].'/;
				    if(!value.match(regex)) {
					alert("'.$args[1].'");
					$("[name='.$name.']").focus();
					proceed = false;
					return false;
				    }';
			}
			
			if($args[0] == 'equal') {
			    $this->form[$name]['script'] .= '
				    var value = $("[name='.$name.']").val();
				    var equal = $("[name='.$args[1].']").val();
				    if(value !== equal) {
					alert("'.$args[2].'");
					$("[name='.$name.']").focus();
					proceed = false;
					return false;
				    }';
			}
			
			if($args[0] == 'regex') {
			    $this->form[$name]['script'] .= '
				    var value = $("[name='.$name.']").val();
				    var regex = '.$args[1].';
				    if(!value.match(regex)) {
					alert("'.$args[2].'");
					$("[name='.$name.']").focus();
					proceed = false;
					return false;
				    }';
			}
			
			if($args[0] == 'isfilled') {
			    $this->form[$name]['script'] .= '
				    if($("[name='.$name.']").val() !== "") {
					var value = $("[name='.$name.']").val();
					var regex = '.$args[1].';
					if(!value.match(regex)) {
					    alert("'.$args[2].'");
					    $("[name='.$name.']").focus();
					    proceed = false;
					    return false;
					}
				    }';
			}
		}
	}
	
	/**
	 * Adds description to form element
	 * @param string
	 * @param string
	 * @return void
	 * awaiting params: name, description
	 */
	public function addDescription()
	{
		$args = func_get_args();
		$name = array_shift($args);
		$desc = array_shift($args);
		if(!$desc or !isset($desc)){
		   echo "Error: The argument description is missing. Cannot add description.";
		   return;
		}
		
		if(!isset($this->form[$name])){
			echo "Error: The name <b>$name</b> of element is missing. Cannot add description.";
		}
		else{
		    $this->form[$name]['description'] = "<div class=\"frm-item-description\">$desc</div>\n";
		}
	}
	
	/**
	 * Adds description to Group of form elements
	 * @param string
	 * @param string
	 * @return void
	 * awaiting params: name, description
	 */
	public function addGroupDescription()
	{
		$args = func_get_args();
		$name = array_shift($args);
		$desc = array_shift($args);
		if(!$desc or !isset($desc)){
		   echo "Error: The argument description is missing. Cannot add description.";
		   return;
		}
		
		if(!isset($this->form[$name])){
			echo "Error: The name <b>$name</b> of element is missing. Cannot add description.";
		}
		else{
		    $this->form[$name]['groupdescription'] = "<div class=\"frm-item-groupdescription frm-item-description\">$desc</div>\n";
		}
	}
	
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
			$result = db::getTableFields($table);
			
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
	
	/**
	 * Validate form values
	 * @param array
	 * @return errors
	 */
	public function frmValidate($post, $files = null)
	{
		
		if(isset($post['validate'])){
                        if(get_magic_quotes_gpc()) $post['validate'] = stripslashes($post['validate']);

                        $post['validate'] = base64_decode($post['validate']);
                        $post['validate'] = unserialize($post['validate']);
			$itemsToVal = $post['validate'];
			foreach($itemsToVal as $key => $item){
				$argums[0] = $key;
                                $argums = array_merge($argums, $item);
				if(count($argums) == 3){
				  $name = $argums[0];
				  $rule = $argums[1];
				  $args = $argums[2];
				  $eqval = null;
				}
				if(count($argums) == 4){
				  $name = $argums[0];
				  $rule = $argums[1];
				  $eqto = $argums[2];
				  $args = $argums[3];
				  if(isset($post[$eqto])){
				    $eqval = $post[$eqto];
				  }
				  else{
				    $eqval = $eqto;
				  }
				}
				if(count($argums) == 5){
				  $name = $argums[0];
				  $rule = $argums[1];
				  $min = $argums[2];
				  $max = $argums[3];
				  $args = $argums[4];
				}
				
				$value = $post[$name];
				if ($files && isset($files[$name])) $file = $files[$name];
				// reg expression for email validation
				$regexp = "/^[^0-9][A-z0-9_-]+([.][A-z0-9_-]+)*[@][A-z0-9_-]+([.][A-z0-9_-]+)*[.][A-z]{2,4}$/";
				//$regexp = "/[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/";
				if($rule == 'filled' and trim($value) == ''){
					$this->setError($args);
				}
				if($rule == 'email' and trim($value) !== ''){
				  if($rule == 'email' and !preg_match($regexp, $value)){
					$this->setError($args);
				  }
				}
				if($rule == 'captcha' and $_SESSION['__BZ_FORM']['class_form_'.$name] != $post[$name]){
				  $this->setError($args);
				}
				if($rule == 'equal' and $value != $eqval){
				  $this->setError($args);
				}
				if($rule == 'numeric' and !is_numeric($value)){
				  $this->setError($args);
				}
				if($rule == Form::MIN and (int)$value < (int)$eqval){
				  $this->setError($args);
				}
				if($rule == 'max' and (int)$value > (int)$eqval){
				  $this->setError($args);
				}
				if($rule == 'range' and ((int)$value < (int)$min or (int)$value > (int)$max)){
				  $this->setError($args);
				}
				
				if($rule == 'regex' and !preg_match($eqval, $value)){
				  $this->setError($args);
				}
				if($rule == 'skipfirst' and $value == $eqval){
				  $this->setError($args);
				}
				
				if($rule == 'protection' and $value != $this->oldToken) {
				    $this->setError($args);
				}
				
				// FILES RULES
				if($rule == 'uploaded' and $file['error'] == '4'){
					$this->setError($args);
				}
				if($rule == 'max_file_size' and $file['size'] > $eqval){
					$this->setError($args);
				}
				if($rule == 'image_only'){
					$fileExplode = explode('.', $file['name']);
					$fileExtension = end($fileExplode);
					if(!in_array(strtolower($fileExtension), array('gif', 'jpg', 'jpeg', 'png'))) {
					    $this->setError($args);
					}
				}
				
				if($rule == 'extensions'){
					$fileExplode = explode('.', $file['name']);
					$fileExtension = end($fileExplode);
					if(!in_array(strtolower($fileExtension), $eqval)) {
					    $this->setError($args);
					}
				}
				
				if($rule == 'extension'){
					$fileExplode = explode('.', $file['name']);
					$fileExtension = end($fileExplode);
					if(strtolower($fileExtension) != $eqval) {
					    $this->setError($args);
					}
				}
				
				if($rule == 'landscape') {
				    if($file['tmp_name']) {
					list($width, $height) = getimagesize($file['tmp_name']);
					if($width < $height) $this->setError($args);
				    }
				}
				
				if($rule == 'portrait') {
				    if($file['tmp_name']) {
					list($width, $height) = getimagesize($file['tmp_name']);
					if($width > $height) $this->setError($args);
				    }
				}
				
				if($rule == 'min_dimensions') {
				    if($file['tmp_name']) {
					list($width, $height) = getimagesize($file['tmp_name']);
					if($width < $min or $height < $max) $this->setError($args);
				    }
				}
				
				if($rule == 'max_dimensions') {
				    if($file['tmp_name']) {
					list($width, $height) = getimagesize($file['tmp_name']);
					if($width > $min or $height > $max) $this->setError($args);
				    }
				}
				
				if($rule == 'max_width') {
				    if($file['tmp_name']) {
					list($width, $height) = getimagesize($file['tmp_name']);
					if($width > $eqval) $this->setError($args);
				    }
				}
				
				if($rule == 'max_height') {
				    if($file['tmp_name']) {
					list($width, $height) = getimagesize($file['tmp_name']);
					if($height > $eqval) $this->setError($args);
				    }
				}
				
				if($rule == 'min_width') {
				    if($file['tmp_name']) {
					list($width, $height) = getimagesize($file['tmp_name']);
					if($width < $eqval) $this->setError($args);
				    }
				}
				
				if($rule == 'min_height') {
				    if($file['tmp_name']) {
					list($width, $height) = getimagesize($file['tmp_name']);
					if($height < $eqval) $this->setError($args);
				    }
				}
				
				// System Files errors
				if($files) {
				    foreach($files as $file) {
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
				
				if(!$this->isError){
					$this->return = TRUE;
				}
				
			}
			return $this->return;
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
			$this->return = FALSE;
		}
	}
	
	public function renderErrors()
	{
	
		if($this->isError){
			$output = '<h3 class="error_title">Chyby pri odosielaní formulára:</h3>';
			$output .= '<ul class="error">';
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
	
	//******************************************************************* CAPTCHA METHODS */
	
  /*
  * Author: Simon Jarvis
  * Copyright: 2006 Simon Jarvis
  * Date: 03/08/06
  * Updated: 07/02/07
  * Requirements: PHP 4/5 with GD and FreeType libraries
  * Link: http://www.white-hat-web-design.co.uk/articles/php-captcha.php
  * 
  * This program is free software; you can redistribute it and/or 
  * modify it under the terms of the GNU General Public License 
  * as published by the Free Software Foundation; either version 2 
  * of the License, or (at your option) any later version.
  * 
  * This program is distributed in the hope that it will be useful, 
  * but WITHOUT ANY WARRANTY; without even the implied warranty of 
  * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
  * GNU General Public License for more details: 
  * http://www.gnu.org/licenses/gpl.html
  *
  */
  
  private $font = 'monofont.ttf';	
	
	function generateCode($characters) {
		/* list all possible characters, similar looking characters and vowels have been removed */
		$possible = '23456789bcdfghjkmnpqrstvwxyz';
		$code = '';
		$i = 0;
		while ($i < $characters) { 
			$code .= substr($possible, mt_rand(0, strlen($possible)-1), 1);
			$i++;
		}
		return $code;
	}

	function CaptchaSecurityImages($width='120',$height='40',$characters='6', $index='security-code') {
		$code = $this->generateCode($characters);
		/* font size will be 75% of the image height */
		$font_size = $height * 0.75;
		$image = @imagecreate($width, $height) or die('Cannot initialize new GD image stream');
		/* set the colours */
		$background_color = imagecolorallocate($image, 255, 255, 255);
		$text_color = imagecolorallocate($image, 20, 40, 100);
		$noise_color = imagecolorallocate($image, 100, 120, 180);
		/* generate random dots in background */
		for( $i=0; $i<($width*$height)/3; $i++ ) {
			imagefilledellipse($image, mt_rand(0,$width), mt_rand(0,$height), 1, 1, $noise_color);
		}
		/* generate random lines in background */
		for( $i=0; $i<($width*$height)/150; $i++ ) {
			imageline($image, mt_rand(0,$width), mt_rand(0,$height), mt_rand(0,$width), mt_rand(0,$height), $noise_color);
		}
		/* create textbox and add text */
		$textbox = imagettfbbox($font_size, 0, $this->font, $code) or die('Error in imagettfbbox function');
		$x = ($width - $textbox[4])/2;
		$y = ($height - $textbox[5])/2;
		imagettftext($image, $font_size, 0, $x, $y, $text_color, $this->font , $code) or die('Error in imagettftext function');
		/* output captcha image to browser */
		//header('Content-Type: image/jpeg');
		
		/* self cleaning routine - erases old captcha images*/
		$imagesDir = opendir(WWW_DIR.'/images/');
		while($files = readdir($imagesDir)) {
			$images[] = $files;
		}
		closedir($imagesDir);

		$imagesDir = WWW_DIR.'/images/';

		foreach($images as $img){
		
			if(substr($img, 0, 1) != '.'){
			  if(substr($img, 0 , 7) == 'captcha') unlink($imagesDir . $img);
			}
		}
		
		$filename = 'captcha-'.date("U").'.jpg';
		// U can change the path of the file manually. The images directore must have write rights.
		$captchafile = WWW_DIR.'/images/'.$filename;
		
		imagejpeg($image, $captchafile);
		imagedestroy($image);
		$_SESSION['__BZ_FORM']['class_form_'.$index] = $code;
		
		return $filename;
	}
}

$_SESSION['__BZ_FORM']['errors'] = null;
if(isset($_POST['validate'])){
  // Validating code
  $_SESSION['__BZ_FORM']['errors'] = '';
  $frm = new Form;
  $frm->setStyle('background-color','transparent');
  $result = $frm->frmValidate($_POST, $_FILES);
  
  if(!$result){
    Form::$isvalid = false;
    $_SESSION['__BZ_FORM']['errors'] .= $frm->renderErrors();
  }
  else{
    $_SESSION['__BZ_FORM']['errors'] .= '';
    Form::$isvalid = true;
  }
}

// check for general files errors
/*
if($_FILES) {
    $frm = new Form;
    foreach($_FILES as $file) {
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
	
	if($frm->isError) {
	    Form::$isvalid = false;
	    $_SESSION['__BZ_FORM']['errors'] .= $frm->renderErrors();
	}
	else {
	    if(Form::$isvalid) Form::$isvalid = true;
		else Form::$isvalid = false;
	}
    }
}
 */
?>