<?php
/**
 * PHP Class to create tables
 *
 * The class needs Db class witch conects to Mysql or Sqlite database
 *
 * The class needs Html class for rendering
 * 
 * Usage:
 * <code><?php
 * require('class.Html.php');
 * require('class.Db.php');
 * require('class.Table.php');
 * $table = new Table($table_name, [$object]);
 * ? ></code>
 * 
 * ==============================================================================
 * 
 * @version $Id: class.Table.php, v0.88 2010/10/24 $
 * @copyright Copyright (c) 2010 Branislav Zvolensky (http://www.mbmartworks.sk)
 * @author Branislav Zvolensky <zvolensky@mbmartworks.sk>
 * @license http://opensource.org/licenses/gpl-license.php GNU General Public License (GPL)
 * 
 * ==============================================================================
 *
 * Usage of this script is on own risk. The autor does not give you any warranty.
 * If you will use this script in your files you must place these header informations with the script.
 *
 * v0.88 added the possibilty make action disabled. it means it does not create link but simple span only
 * with class - actions-disabled
 *
 * v0.87
 * added Paginator tool with Ajax request
 * added the option set the table data from outside source (array) 
 * 
 * v0.85
 * added requestUri
 * v0.84
 * added setImage() to action
 * v0.80
 * rebuilded to more objectal structure for fluent usage.
 * 
 */
interface ITableControl
{
	function __set($name, $value);
	
	function __get($name);
	
	function setName($string);
	
	function getName();
	
	function setAttributes($attrs);
	
	function getAttributes();
}


abstract class TableControl implements ITableControl
{
	/** @var array */
	protected $attributes = array();
	/** @var string */
	protected $name;
	
	public function __construct()
	{
		
	}
	
	/**
	 * Sets Html attribute for the table
	 * @param string $name Html attribute
	 */
	function __set($name, $value)
	{
		$this->attributes[$name] = $value;
	}
	/**
	 * Gets Html attribute for the table
	 * @param string $name Html attribute
	 * @return string
	 */
	function __get($name)
	{
		return $this->attributes[$name];
	}
	/**
	 * Sets Name of the control
	 * @param string $name
	 */
	function setName($name)
	{
		$this->name = $name;
		return $this;
	}
	/**
	 * Gets Name of the control
	 * @return string
	 */
	function getName()
	{
		return $this->name;
	}
	/**
	 * Sets Html attributes for the table
	 * @param array $attrs Table html attributes
	 */
	function setAttributes($attrs)
	{
		if(!is_array($attrs)) die('Error: setAttributes() - parameter must be an array.');
		
		if(!$this->attributes) $this->attributes += $attrs;
		
		else $this->attributes = $attrs;
	}
	
	/**
	 * Gets Attributes of control
	 * @return array
	 */
	function getAttributes()
	{
		return $this->attributes;
	}
}
 
class Table extends TableControl
{

	// *************************************************************
	// *                  Properties & Constants                   *
	// *************************************************************
	
	/** @constant */
	const WITH_KEY = TRUE;
	
	/** @constant */
	const WITHOUT_KEY = FALSE;
	
	/** @var string */
	private $className;
									 
	/** @var string */
	private $fileName;
	
	/** 
	  * @var mixed 
	  * rows container
	  */
	private $rows = array();
	
	/** 
	  * @array 
	  * headers container
	  */
	private $headers = array();
	
	/** 
	  * @array 
	  * actions container
	  */
	private $actions = array();
	
	/** 
	  * @var string
	  * actions key
	  */
	private $actKey;
	
	/** 
	  * @var string
	  * mysql table name
	  */
	private $mysqlTableName;
	
	/** 
	  * @var array
	  * table fields
	  */
	private $fields = array();
	
	/** 
	  * @var array
	  * order attributes
	  */
	private $order = array();
	
	/** 
	  * @var array
	  * arguments container from last request
	  */
	private $arguments = array();
	
	/** 
	  * @var bool
	  * is ajax?
	  */
	private $ajax;
	/** @var string Container CSS Id */
	private $contCssId;
	/** @var string Controller name */
	private $controller;
	/** @var string Requested URI */
	private $requestUri;
	/** @var array */
	private $paginator = array();
	/** @var array */
	private $data;
	/** @var integer Current page */
	private $currentPage = 1;
	/** @var array pages */
	private $pages = array();
	
	
	// ************************************************************** CONSTRUCTOR
	public function __construct($name = null, $object = null)
	{
		$get = $_GET;
		$this->fileName = end(explode('/', $_SERVER['SCRIPT_NAME']));
		
		$this->attributes['class'] = 'dataTable';
		$this->attributes['id'] = ($name ? 'dataTable' . ucfirst($name) : 'dataTable_BZ');
		$this->name = ($name ? $name : 'dataTable_BZ');
		$this->ajax = false;
		//setting attributes
		$this->cellspacing = 0;
		$this->cellpadding = 0;
		$this->border = 0;
		
		if(isset($get['pn'])) $this->currentPage = $get['pn'];
		
		// parameters from uri will be removed
		$this->requestUri = preg_replace('/[\?].*/', '', $_SERVER['REQUEST_URI']);
		
		/*** loading needed classes ***/
		//if(!class_exists('Html', false)) require(dirname(__FILE__) . '/Html.php');
		//if(!class_exists('Paginator', false)) require(dirname(__FILE__) . '/tools/Paginator.php');
		
		if($object && method_exists($object, 'getName')) $this->controller = $object->getName();
		
		if(!empty($get)){
			// save gets, you can use these arguments later
			$this->arguments = $get;
			// if not empty $get and set do 
			if(isset($get['do']) && $get['do'] != ''){
				//$action = ucfirst(array_shift($get));
				$action = $get['do'];
				$args = $get;
				$methodName = 'handle'.ucfirst($action);
				
				if($object && !is_object($object)) {
					die('Error: Table::__constructor, parameter $object must be an object');
				}
				
				if(is_object($object) && method_exists($object, $methodName)) {
					$object->$methodName($args);
				}
				elseif(method_exists($this, $methodName)){
					$this->$methodName($args);
				}
				elseif(function_exists($methodName)) {
					$methodName($args);
				}
				else{
					die("Error: Table::__contruct(), method or function <b>$methodName()</b> does not exist");
				}
				
			}
		}
	}
	
	
	// *************************************************************
	// *                           METHODS                         *
	// *************************************************************
	
	
	// ************************************************************* RENDER METHODS
	/**
	 * @return html table
	 */
	public function render()
	{
		// Collect the table structure
		$this->buildDataTable();
		
		//Collect Headers
		$headers ='';
		foreach($this->headers as $header){
			// Collect header content (label and ordershifts)
			$headerCont = $header->getLabel();
			if($header->getOrderShift()){
				$headerCont .= $this->createOrderShifts($header->getName());
			}
			
			$attrs = $header->getAttributes();
			
			// create th tag from prototype
			$headers .= Html::elem('th', $attrs, $headerCont);
		}
		
		// create tr tag from prototype
		$trAttr = array('class' => 'tblHeader'); // attributes for tr tag
		$headerRow = Html::elem('thead')->setCont(Html::elem('tr', $trAttr, $headers));

		$tableCont = $headerRow;
		
		// Finalize table tag
		// Add Table Data as Rows
		$tableCont .= Html::elem('tbody')->setCont($this->rows);
		// table attributes
		$tableAttr = $this->getAttributes();
		// Create <table></table> tag from prototype
		$table = Html::elem('table')->setCont($tableCont)->setAttributes($tableAttr);
		
		$this->contCssId = ('#'.$this->getName() . 'Container');
		$container_inner = Html::elem('div')->setCont($table);
		$container_inner->id = ($reloadId = $this->getName() . 'ContainerInner');
		
		if($this->paginator) $container_inner->add($this->paginator['control']);
		
		$container = Html::elem('div')->setCont($container_inner);
		$container->id = ($this->getName() . 'Container');
		
		$script = ($this->ajax ? $this->getAjaxScript($this->contCssId, $this->requestUri, $reloadId) : '');
		
		return (string)$container . (string)$script;
	}
	
	//************************************************************* MODEL METHODS
	/**
	 * @title Find All
	 * finds all record from table
	 */
	private function findAll()
	{
		
		if($this->data) $result = $this->data;
		else $result = db::fetchAll("SELECT * FROM %t", $this->mysqlTableName);
		
		$numrows =  count($result);
		
		if(!$result or $numrows == 0){
			return array();
		}
		
		
		if(isset($this->order['orderby'])) {
		
			foreach($result as $key => $row) {
				$sort[$key] = $row[$this->order['orderby']];
			}
			
			if($this->order['ordering'] == 'ASC' ) array_multisort($sort, SORT_ASC, SORT_REGULAR, $result);
			
			if($this->order['ordering'] == 'DESC' ) array_multisort($sort, SORT_DESC, SORT_REGULAR, $result);
			
		}
		
		if($this->paginator) {
			$this->paginator['pages'] = $this->getPages($result);
			$result = $this->paginator['pages'][$this->currentPage];
			$this->paginator['control'] = new Paginator($this->currentPage, $this->paginator);
		}
		
		return $result;
		
	}
	
	// ************************************************************ FACTORIES
	/**
	 * @return void
	 * factory for adding a column to table
	 */
	public function addColumn($name=NULL, $label=NULL)
	{
		
		if(!$name){
			echo 'Error: function <b>addColumn()</b> - missing argument <b>name</b>.';
			exit();
		}
		else{
			if(!$label) $label = $name;
				
				return $this->headers[$name] = new ColumnControl($name, $label);
				
			}
			
	}
	
	/**
	 * @title Create Order Shifts
	 * Factory for ordering shifts in table
	 */
	private function createOrderShifts($orderby)
	{
		
		// a href alternative
		$aAttrASC = array('class'=>'hreforder', 'href'=>$this->requestUri.'?do=order&amp;orderby='.$orderby.'&amp;ordering=ASC');
		$aAttrDESC = array('class'=>'hreforder', 'href'=>$this->requestUri.'?do=order&amp;orderby='.$orderby.'&amp;ordering=DESC');
		// create links from prototype and place them into span	tag
		$shifts = '&nbsp;&nbsp;' . Html::elem('span', NULL, Html::elem('a', $aAttrASC, '▼').Html::elem('a', $aAttrDESC, '▲'));
		
		
		/*
		// Forms alernative
		// Tags attributes
		$divAttr = array('class'=>'frmorder');
		$formAttr = array('method'=>'post', 'action'=>$this->fileName);
		$doInAttr = array('type'=>'hidden', 'name'=>'do', 'value'=>'order');
		$obInAttr = array('type'=>'hidden', 'name'=>'orderby', 'value'=>$orderby);
		$orAscInAttr = array('type'=>'hidden', 'name'=>'ordering', 'value'=>'ASC');
		$orDescInAttr = array('type'=>'hidden', 'name'=>'ordering', 'value'=>'DESC');
		$imgAscAttr = array('type'=>'image', 'src'=>'images/shift_asc.png', 'border' = '0', 'alt'=>'ASC');
		$imgAscAttr = array('type'=>'image', 'src'=>'images/shift_desc.png', 'border' = '0', 'alt'=>'DESC');
		
		$forBoth = Html::elem('input', $doInAttr).Html::elem('input', $obInAttr);
		// ASC Shift
		$frmCont = $forBoth . Html::elem('input', $orAscInAttr) . Html::elem('input', $imgAscAttr);
		$divCont = Html::elem('form', $formAttr, $frmCont);
		$shifts = Html::elem('div', $divAttr, $divCont);
		
		// Desc Shift
		$frmCont = $forBoth . Html::elem('input', $orDescInAttr) . Html::elem('input', $imgDescAttr);
		$divCont = Html::elem('form', $formAttr, $frmCont);
		$shifts .= Html::elem('div', $divAttr, $divCont);
		*/
		
		return $shifts;
		
	}
	
	/**
	 * @return void
	 * factory for adding the actions column to table
	 */
	public function addActions($key=NULL, $label='Akcie')
	{
		if($key){
			$this->actKey = $key;
		}
		return $this->addColumn('actions', $label);
		
	}
	
	/**
	 * @return void
	 * factory for adding an action into actions column
	 */
	public function addAction($label=NULL, $action=NULL, $withKey=FALSE)
	{
		if(!$label){
			echo 'Error: function <b>addAction()</b>: Label of the action must be set.';
			exit();
		}
		elseif(!$action){
			echo 'Error: function <b>addAction()</b>: Action must be set.';
			exit();
		}
		else{
			return $this->actions[] = new ActionControl($label, $action, $withKey);
		}
		
	}
	
	/**
	 * @return void
	 * factory for completing rows with data
	 */
	public function buildDataTable()
	{

		$data = $this->findAll();
		
		$this->rows = '';
		$index = 0;
		foreach($data as $cols){
			$tdCont ='';
			
			foreach($this->headers as $header){
				// Get key
				//if($header->getName() == $this->actKey){
				if($this->actKey) $id = $cols[$this->actKey];
				//}
				
				// collect actions
				if($header->getName() == 'actions'){
					$links = '';
					foreach($this->actions as $action){
						
						// Default <a href..> sends do=action and id=key parameters
						$links .= $this->createLink($action, $id);
						
					}
					// create td with actions from prototype
					$tdCont .= Html::elem('td')->setClass('actions')->setCont($links);
				}
				// COLLECT OTHER FIELDS
				else{
					// get attributes
					$attrs = $header->getAttributes();
					
					// If callback is set then the content of the field will be createted by custom callback function
					if($callback = $header->getCallback()){
						$function = $callback[0];
						$params = $callback[1];
						
						// collect params
						// if param is name of the field it sets field value into argument
						if(is_array($params)) {
							foreach($params as $param) {
								if(isset($cols[$param])) {
									$args[] = $cols[$param];
								}
								else {
									$args[] = $param;
								}
							}
						}
						else {
							if(isset($cols[$params])) {
								$args = $cols[$params];
							}
							else {
								$args = $params;
							}
						}
						
						$object = $callback[2];
						if($object) $result = $object->$function($args);
						else $result = $function($args);
												
						$tdCont .= Html::elem('td', $attrs, $result);
					}
					// Or the field will have the same content as in the table
					else{
						$tdCont .= Html::elem('td', $attrs, ($cols[$header->getName()] ? $cols[$header->getName()] : '&nbsp;'));
					}
				}
			}
			
			//Add Even class
			if($index == 0){
				$this->rows .= Html::elem('tr', NULL, $tdCont);
				$index++;
			}
			else{
				$trAttr = array('class'=>'even');
				$this->rows .= Html::elem('tr', $trAttr, $tdCont);
				$index--;
			}
		}
	}
	
	/**
	 * @return html
	 * returns collected <a href...>
	 */
	private function createLink($action, $id)
	{		
		// create a tag from prototype
		if($action->isDisabled()) {
			$aTag = Html::elem('span')->setCont($action->getImage() ? $action->getImage() : $action->getLabel())
		                                  ->title($action->getLabel())
						  ->setClass('actions-disabled');
		}
		else {
			$aTag = Html::elem('a')->setCont($action->getImage() ? $action->getImage() : $action->getLabel())
		                               ->title($action->getLabel());	
		}
		
		
		if(preg_match('/:/', $action->getAction())) {
			list($controller, $act) = explode(':', $action->getAction());
			$aTag->addClass( $action->getImage() ? $act . ' img-actions' : $act . ' text-actions' );
			$href = $controller . '/' . $act;
			if($action->getWithKey()) $href .= "/$id";
		}
		elseif(preg_match('/!/', $action->getAction())) {
			$href = $this->requestUri . '?do=' . preg_replace('/!/', '', $action->getAction());
			$aTag->addClass( $action->getImage() ? preg_replace('/!/', '', $action->getAction()) . ' img-actions' : preg_replace('/!/', '', $action->getAction()) . ' text-actions' );
			if($action->getWithKey()) $href .= '&amp;id='.$id;
		}
		else {
			$href = $this->requestUri . '?do='.$action->getAction();
			$aTag->addClass($action->getImage() ? $action->getAction() . ' img-actions' : $action->getAction() . ' text-actions');
			if($action->getWithKey()) $href .= '&amp;id='.$id;
		}
		
		/*
		if(isset($this->order['orderby'])){
			$href .= '&amp;orderby='.$this->order['orderby'].'&amp;ordering='.$this->order['ordering'];
		}
		*/
		
		if(!$action->isDisabled()) $aTag->href = $href;
		
		return $aTag;
		
	}
	
	//*********************************************************************** SETS
	/**
	 * @return object
	 * sets class Name
	 */
	public function setClass($clsName=NULL)
	{
		if(!$clsName){
			echo 'Error: function <b>setClass():</b> Class name missed';
			exit;
		}
		else{
			$this->attributes['class'] = $clsName;
		}
		return $this;
	
	}
	
	/**
	 * @param string | array $source Source may be name of the table or fetched array
	 * @return object
	 * sets Data Table Name
	 */
	public function setDataSource($source = NULL)
	{
		if(!$source){
			echo 'Error: <b>Table::setDataSource()</b>, Table name or Fetched array missing.';
			exit();
		}
		elseif(is_string($source)){
			
			$this->mysqlTableName = $source;
			$this->fields = $this->getFields();
			
		}
		else {
			$this->data = $source;
			$this->setFields($source);
		}
		return $this;
	
	}
	
	/**
	 * @param array
	 * @return void
         */
	private function setFields($source)
	{
		
		$source = array_shift($source);
		
		foreach($source as $key => $value) {
			$this->fields[$key] = null;
		}
	}
	
	/**
	 * @return object
	 * sets Default Order of the Table
	 */
	public function setDefaultOrder($fieldName=NULL, $ordering='ASC')
	{
		if(!$fieldName){
			echo 'Error: function <b>setDefaultOrder()</b>, Field name missing.';
			exit;
		}
		else{
			$this->setOrder($fieldName, $ordering);
		}
		return $this;
	
	}
	
	/**
	 * @return void
	 * sets Order of the Table
	 */
	private function setOrder($fieldName=NULL, $ordering='ASC')
	{

			$this->order = array('orderby' => $fieldName, 'ordering' => $ordering);
	
	}
	
	/**
	 * @return object
	 * sets if ajax request
         */
	public function setAjax()
	{
		$this->ajax  = true;
		return $this;
	}
	
	/**
	 * @return object
	 * sets paginator and rows per page
	 */
	public function setPaginator($records = 20, $limit = 10)
	{
		$this->paginator = array('records'=>$records, 'limit'=>$limit);
		return $this;
	}
	
	/**
	 * @return object
	 * gets paginator and rows per page
	 */
	public function getPaginator()
	{
		if(!$this->mysqlTableName) die('Error: Table::getPaginator(), Table::$mysqlTableName must be set for Paginator');
		
		if(!$this->paginator) die('Error: Table::getPaginator(), Table::$paginator must be set.');
		
		
		
		return $this;
	}
	
	// ********************************************************************* GETS
	
	/**
	 * @return bool
	 * finds if field exists
	 */
	private function getField($fieldName)
	{
			
			if(in_array($fieldName, $this->fields)){
				return TRUE;
			}
			// if not found return FALSE
			return FALSE;
	
	}
	
	/**
	 * @return array
	 * returns all fields
	 */
	private function getFields()
	{
			
		return db::getTableFields($this->mysqlTableName);
	
	}
	
	private function getAjaxScript($contId, $filename, $reloadId)
	{
		$script = '
		/* <![CDATA[ */
		jQuery(document).ready(function($) {
			//load as default
			//$("'. $contId. '").append("<div id=\"ajax-spinner\"></div>");
			//$("'. $contId. '").load("'. $filename .'");
			$("'. $contId. '").css({"position":"relative"});
			
			$("a.hreforder").live("click", function(){
				var href = $(this).attr("href");
				var arr = href.split("?");
				var url = "'. $filename .' #' .$reloadId. '";
				var data = arr[1];
		
				$("'. $contId. '").append("<div id=\"ajax-spinner\"></div>");
				$("'. $contId. '").load(url, data);
			
				return false;
			
			});
			
			$("a.paginator-page-link").live("click", function(){
				var href = $(this).attr("href");
				var arr = href.split("?");
				var url = "'. $filename .' #' .$reloadId. '";
				var data = arr[1];
		
				$("'. $contId. '").append("<div id=\"ajax-spinner\"></div>");
				$("'. $contId. '").load(url, data);
			
				return false;
			
			});
		
		});
		/* ]]> */';
		
		$ret =  Html::elem('script')->setCont($script);
		$ret->type = 'text/javascript';
		
		return $ret;
	}
	
	private function getPages($records)
	{
		if($records){
			$pageidx = 1;
			$idx = 1;
			$lastidx = $this->paginator['records'];
			foreach($records as $record){
				$rows[] = $record;
				if($idx == $lastidx){
				  $idx = 0;
				  $pages[$pageidx] = $rows;
				  unset($rows);
				  $pageidx++;
				}
				$idx++;
			}
			// Place the rest of messages to last page
			if(isset($rows) && count($rows)>0){
				$pages[$pageidx] = $rows;
			}
			
			return $pages;
		}
		else{
		  return array();
		}
	}
	
	/**
	 * When calling object as string call render
         */
	public function __toString()
	{
		return $this->render();
	}
	
	// ******************************************************************* CALLBACKS
	// This is the place for custom callback functions
	// Every callback function must begin with with prefix callback like: callbackFastenNames()
	
	private function fastenNames($id)
	{
		$result = db::fetch("SELECT * FROM %t WHERE id=%i", $this->mysqlTableName, $id);
		
		if(count($result) > 0){
			return $result['name'] . ' ' . $result['surname'];
		}
		else{
			return 'N/A';
		}
	}
	
	// ******************************************************************** HANDLERS
	// This is the place for handlers
	
	private function handleOrder($args)
	{
		
		$this->order = $args;
		
	}
	
	private function handleAdd()
	{
		
		echo "Message: You can put your script to Add Handler";
		exit;  	
		
	}
	
	private function handleEdit($args)
	{
		
		 echo "Message: You can put your script to Edit Handler";
		 exit;
		
	}
	
	private function handleDelete($args)
	{
		
		 echo "Message: You can put your script to Delete Handler";
		 exit;
		
	}

}

class ColumnControl extends TableControl
{
	/** @var bool */
	private $ordershift;
	/** @var string */
	private $label;
	/** @var array */
	private $callback = array();
	
	function __construct($name, $label)
	{
		$this->name = $name;
		$this->label = $label;
	}
	
	/**
	 * Add Ordering Shifts to Column
	 * @return object
	 */
	function addOrderShift()
	{
		$this->ordershift = true;
		return $this;
	}
	
	/**
	 * Gets ordershift status
	 * @return bool
	 */
	function getOrderShift()
	{
		return $this->ordershift;
	}
	
	/**
	 * Sets Label
	 * @param string
         */
	function setLabel($label)
	{
		$this->label = $label;
		return $this;
	}
	
	/**
	 * Gets Label
	 * @return string
         */
	function getLabel()
	{
		return $this->label;
	}
	
	/**
	 * sets callback function
	 * @param string $function Function name
	 * @param string | array $param Parameter(s) to send to function
	 * @param object $object If the function is part of object, you must set this param.
	 */
	function callback($function, $param = null, $object = null)
	{
		if((string)$function == '') die('Error: callback() param $function must be set.');
		if($object && !is_object($object)) die('Error: callback() param $object must be object.');
		
		$this->callback = array($function, $param, $object);
		return $this;
	}
	
	/**
	 * Gets callback
	 * @return array
	 */
	function getCallback()
	{
		return $this->callback;
	}
	
	/**
	 * sets style sttribute
	 * @param string
	 * @return object
         */
	function setStyle($string)
	{
		$this->attributes['style'] = $string;
		return $this;
	}
	
	/**
	 * sets class sttribute
	 * @param string
	 * @return object
         */
	function setClass($string)
	{
		$this->attributes['class'] = $string;
		return $this;
	}
	
	
}

class ActionControl
{
	/** @var string */
	private $label;
	/** @var string */
	private $action;
	/** @var object html */
	private $image;
	/** @var bool */
	private $withkey;
	/** @var bool */
	private $disabled;
	/** @var array */
	private $callback = array();
	
	function __construct($label = null, $action = null, $withkey = false, $disabled = false)
	{
		$this->action = $action;
		$this->label = $label;
		$this->withkey = $withkey;
		$this->disabled = $disabled;
	}
	
	/**
	 * Set Action
	 * @return object
	 */
	function setAction($action)
	{
		$this->action = $action;
		return $this;
	}
	
	/**
	 * Gets Action
	 * @return string
	 */
	function getAction()
	{
		return $this->action;
	}
	
	/**
	 * Sets Label
	 * @param string
         */
	function setLabel($label)
	{
		$this->label = $label;
		return $this;
	}
	
	/**
	 * Gets Label
	 * @return string
         */
	function getLabel()
	{
		return $this->label;
	}
	
	/**
	 * Sets With Key
	 * @param bool
         */
	function setWithKey($withkey)
	{
		$this->withkey = $withkey;
		return $this;
	}
	
	/**
	 * Gets With Key
	 * @return bool
         */
	function getWithKey()
	{
		return $this->withkey;
	}
	
	/**
	 * sets callback function
	 * @param string $function Function name
	 * @param string | array $param Parameter(s) to send to function
	 * @param object $object If the function is part of object, you must set this param.
	 */
	function callback($function, $param, $object)
	{
		$this->callback = array($function, $param, $object);
		return $this;
	}
	
	/**
	 * Gets callback
	 * @return array
	 */
	function getCallback()
	{
		return $this->callback;
	}
	
	/**
	 * sets image
	 * @param object | string
	 * @return object
	 */
	function setImage($object)
	{
		$this->image = $object;
		return $this;
	}
	
	/**
	 * gets image
	 * @return object
	 */
	function getImage()
	{
		return $this->image;
	}
	
	/**
	 * sets action disabled
	 * @param bool
	 * @return object
	 */
	function setDisabled($status = true)
	{
		$this->disabled = $status;
		return $this;
	}
	
	/**
	 * is disabled status
	 * @return object
	 */
	function isDisabled()
	{
		return $this->disabled;
	}
	
	
}
