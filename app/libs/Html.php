<?php
/**
 * PHP Class to create html tags and attrinutes
 *
 * The class offers static functions to create html tags or return default tags attributes.
 * Usage: <code><?php
 *        $formTag = Html::elem('form', (array)$attributes, (string|array)$content);
 *        ?></code>
 *        param #1 - tag name,
 *        param #2 - array of attributes in ex.: array('method'=>'post', 'action'=>'index.php'); etc,
 *        param #3 - string|array that contains html (other tags) or plain text
 *
 * <code><?php
 * include('class.Html.php');
 * ? ></code>
 * 
 * ==============================================================================
 * 
 * @version $Id: class.Html.php, v0.60 2010/10/24 $
 * @copyright Copyright (c) 2010 Branislav Zvolensky (http://www.mbmartworks.sk)
 * @author Branislav Zvolensky <zvolensky@mbmartworks.sk>
 * @license http://opensource.org/licenses/gpl-license.php GNU General Public License (GPL)
 * 
 * ==============================================================================
 *
 * Usage of this script is on own risk. The autor does not give you any warranty.
 * If you will use this script in your files you must place these header informations with the script.
 *
 * Version History
 * v0.60
 * added method addClass. if some class is set it adds next class.
 *
 * v0.59
 * added method setAttributes()
 *
 * v0.58
 * added flunet methods for setting attributes of the Html tags:
 * setClass(), id(), style(), href(), src(), alt(), title(), target()
 * 
 * usage: $div = Html::elem('div')->setCont('Some text')->setClass('container')->style('color:red');
 *
 * v0.50
 * Calling method Html::elem now creates object, that can be set fluently or additionaly:
 * example: $div = Html::elem('div')->setContent('Some text or Html place here');
 *          // setting attributes is very simple
 *          $div->class = 'some_class';
 *          $div->style = 'color:red;';
 *
 *          // adding another content
 *          $div->add('Another text or Html to add');
 *
 *          // adding another element into $div
 *          $span = Html::elem('span')->setCont('This text will be blue and bold');
 *          $span->style = 'color:blue; font-weight:bold;';
 *          	// and now add
 *              $div->add($span);
 *
 *          // rendering is simple too
 *          echo $div;
 */

class Html
{
	
	/** @property string $doubleTagPrototype */
	private $doubleTagPrototype = "<%tag%%attr%>%cont%</%tag%>";
	
	/** @property string $singleTagPrototype */
	private $singleTagPrototype = "<%tag%%attr% />";
	
	/** @property array $singleTags */
	private $singleTags = array('img', 'input', 'area', 'base', 'br', 'col', 'frame', 'hr',
				    'link', 'meta', 'param');
	/** @property string $tag */
	private $tag;
	
	/** @property string $prototype */
	private $prototype;
	
	/** @property array $content */
	private $content = array();
	
	/** @property array $attributes */
	private $attributes = array();
	
	/**
	 * builds HTML Tag with attributes and content
	 * @param string $tag
	 * @param array $attrs (backward compatibility)
	 * @cont string $cont (backward compatibility) 
	 * @return object
	 */
	public static function elem($tag=NULL, $attrs=NULL, $cont=NULL)
	{
		$elem = new self;
		
		if(!$tag){
			echo "Error: function <b>elem()</b>: Missing name argument.";
			exit;
		}
		elseif($attrs !== NULL && !is_array($attrs)){
			echo "Error: function <b>elem()</b>: Argument attrs must be array.";
			exit;
		}
		else{
			$elem->setTag($tag);
			
			if(in_array((string)$tag, $elem->singleTags)) {
				$elem->prototype = $elem->singleTagPrototype;
			}
			else {
				$elem->prototype = $elem->doubleTagPrototype;
			}
			
			if($attrs) {
				$elem->addAttributes($attrs);
			}
			
			if($cont) {
				$elem->setCont($cont);
			}
		}
		
		return $elem;
	}
	
	/**
	 * Attributes setter
	 */
	public function __set($name, $value)
	{
		$this->attributes[$name] = $value;
	}
	
	/**
	 * Attributes getter
	 */
	public function __get($name)
	{
		return $this->attributes[$name];
	}
	
	/**
	 * sets tag
	 * @param string $tag
	 */
	public function setTag($tag)
	{
		$this->tag = $tag;
		return $this;
	}
	
	/**
	 * gets tag
	 * @param string $tag
	 */
	public function getTag($tag)
	{
		return $this->tag;
	}
	
	/**
	 * sets content into tag
	 * @param string|array content
	 */
	public function setCont($cont)
	{       
                $this->content = array();
		if($cont instanceof Html) {
			$this->content[] = $cont;
 		}
		elseif(is_array($cont)) {
			$this->content = $cont;
		}
		else {
			$this->content[] = $cont;
		}
		return $this;
	}
	
	/**
	 * adds content into tag
	 * @param string|array content
	 */
	public function add($cont)
	{
		if(is_array($cont)) {
			$this->content = array_merge($this->content, $cont);
		}
		else {
			$this->content[] = $cont;
		}
	}
	
	/************** Fluent attributes adding ******************/
	
	/**
	 * sets class attribute
	 * @param string $value
	 * @return object
	 */
	public function setClass($value = null)
	{
		if(!$value) die('Error: Html::setClass() - value must be set!');
		
		$this->attributes['class'] = $value;
		return $this;
	}
	
	/**
	 * adds class attribute
	 * @param string $value
	 * @return object
	 */
	public function addClass($value = null)
	{
		if(!$value) die('Error: Html::addClass() - value must be set!');
		
		if(!isset($this->attributes['class'])) $this->attributes['class'] = $value;
		else $this->attributes['class'] = $this->attributes['class'] . ' ' . $value;
		
		return $this;
	}
		
	
	/**
	 * adds id attribute
	 * @param string $value
	 * @return object
	 */
	public function id($value = null)
	{
		if(!$value) die('Error: Html::id() - value must be set!');
		
		$this->attributes['id'] = $value;
		return $this;
	}
	
	/**
	 * adds style attribute
	 * @param string $value
	 * @return object
	 */
	public function style($value = null)
	{
		if(!$value) die('Error: Html::style() - value must be set!');
		
		$this->attributes['style'] = $value;
		return $this;
	}
	
	/**
	 * adds href attribute
	 * @param string $value
	 * @return object
	 */
	public function href($value = null)
	{
		if(!$value) die('Error: Html::href() - value must be set!');
		
		$this->attributes['href'] = $value;
		return $this;
	}
	
	/**
	 * adds src attribute
	 * @param string $value
	 * @return object
	 */
	public function src($value = null)
	{
		if(!$value) die('Error: Html::src() - value must be set!');
		
		$this->attributes['src'] = $value;
		return $this;
	}
	
	/**
	 * adds alt attribute
	 * @param string $value
	 * @return object
	 */
	public function alt($value = null)
	{
		if(!$value) $value = '';
		
		$this->attributes['alt'] = $value;
		return $this;
	}
	
	/**
	 * adds title attribute
	 * @param string $value
	 * @return object
	 */
	public function title($value = null)
	{
		if(!$value) die('Error: Html::title() - value must be set!');
		
		$this->attributes['title'] = $value;
		return $this;
	}
	
	/**
	 * adds target attribute
	 * @param string $value
	 * @return object
	 */
	public function target($value = null)
	{
		if(!$value) die('Error: Html::target() - value must be set!');
		
		$this->attributes['target'] = $value;
		return $this;
	}
	
	/**
	 * adds attributes
	 * @param array
	 */
	public function addAttributes($attrs)
	{
		if(!is_array($attrs)) {
			die('Error: addAttributes - $attrs must be array');
		}
		$this->attributes += $attrs;
		return $this;
	}
	
	/**
	 * sets attributes
	 * @param array
	 */
	public function setAttributes($attrs)
	{
		if(!is_array($attrs)) {
			die('Error: addAttributes - $attrs must be array');
		}
		$this->attributes = $attrs;
		return $this;
	}
	
	
	/******************* rendering ****************/
	
	/**
	 * Generate Html code
	 */
	public function render()
	{
		return preg_replace(array('/%tag%/', '/%attr%/', '/%cont%/'), array($this->tag, $this->collectAttributes(), $this->collectContent()), $this->prototype);
	}
	
	/**
	 * Collecting attributes string
	 */
	private function collectAttributes()
	{
		if($this->attributes) {
			$attrs = '';
			
			foreach($this->attributes as $key => $val) {
				$attrs .= ' ' . $key . '="' . $val . '"';
			}
		}
		else {
			$attrs = '';
		}
		return $attrs;
	}
	
	/**
	 * Collecting content
	 */
	private function collectContent()
	{
		if($this->content) {
			$content = '';
			$count = 1;
			foreach($this->content as $val) {
				$content .= ($count == 1 ? '' : ' ') . $val;
			}
		}
		else {
			$content = '';
		}
		return $content;
	}
	
	/**
	 * will be called when will be called echo function
	 */
	public function __toString()
	{
		return $this->render();
	}
	
	/************************** Prepared containers and elements ********************/
	
	/**
	 * Creates Div (default) empty tag that has class fltClear or you can set custom class name
	 * Then you can in your css file make style definition for this class
	 * @param string $tag
	 * @param string $class
	 * @return object
	 */
	public static function fltClear($tag = null, $class = null)
	{
		if(!$tag) $tag = 'div';
		if(!$class) $class = 'fltClear';
		
		return self::elem($tag, array('class'=>$class, 'style'=>'clear:both;'));
	}
        
        /**
         * returns Image html object
         * @param string $src html src attribute
         * @param string $alt html alt attribute
         * @param integer $width html width attribute
         * @param integer $height html height attribute
         * @return object Html object 
         */
        public static function img( $src, $alt, $width = null, $height = null )
        {
                $img = self::elem('img')->src($src)->alt( $alt );
                if( $width ) $img->width = (int)$width;
                if( $height ) $img->height = (int)$height;
                
                return $img;
        }
        
        /**
         * returns div block with outer and inner container
         * @param string $content
         * @param string $class optional
         * @param string $id optional
         * @return string html 
         */
        public static function divBlock( $content, $class = null, $id = null )
        {
                $cont = Html::elem('div');
                $contInner = Html::elem('div');
                
                if( $class ) {
                    $cont->setClass( $class );
                    $contInner->setClass( "{$class}-inner");
                }
                if( $id ) {
                    $cont->id( $id );
                    $contInner->id( "{$id}-inner");
                }
                
                $contInner->setCont( $content );
                $cont->setCont( $contInner );
                
                return (string)$cont;
        }
        
        
        public static function script( $file = null)
        {
            $r = Html::elem('script');
            $r->type = "text/javascript";
            if($file) $r->src = $file;
            return $r;
        }
	
}
