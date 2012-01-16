<?php
/**
 * Tool Paginator
 *
 * Usage:
 * <code><?php
 * require('Paginator.php');
 * $paginator = new Paginator($actualPageNumber, array('limit'=>10, 'pages'=>[array]));
 * ? ></code>
 * 
 * ==============================================================================
 * 
 * @version $Id: Paginator.php, v0.10 2010/10/24 $
 * @copyright Copyright (c) 2010 Branislav Zvolensky (http://www.mbmartworks.sk)
 * @author Branislav Zvolensky <zvolensky@mbmartworks.sk>
 * @license http://opensource.org/licenses/gpl-license.php GNU General Public License (GPL)
 * 
 * ==============================================================================
 *
 * Usage of this script is on own risk. The autor does not give you any warranty.
 * If you will use this script in your files you must place these header informations with the script.
 */
class Paginator
{
    /** @var integet Actual Page */
    private $actualPage;
    
    /** @var integer Limit */
    private $limit;
    
    /** @var integer Pages */
    private $pagesCount;
    
    /** @var string */
    private $requestUri;
    
    public function __construct($actualPageNumber, $params)
    {
        $this->actualPage = $actualPageNumber;
        $this->limit = $params['limit'];
        $this->pagesCount = count($params['pages']);
        
        $this->requestUri = ( preg_replace('/[&\?]pn=\d+/', '', $_SERVER['REQUEST_URI']) );
        
        $this->requestUri = ( preg_match('/[\?]/', $this->requestUri) ? $this->requestUri.'&amp;' : $this->requestUri.'?' );
    }
    
    
    /**
     * creates paginator control
     * @return string html
     */
    private function get()
    {
	if($this->pagesCount > 1){
            
            $limit = $this->limit;
            
            $lastPage = $this->pagesCount; // last page number
            
            $pgNumber = $this->actualPage;
		  
	    $startFrom = $pgNumber - ($limit/2); // start paginator from page
		  
	    $startFrom < 1 ? $startFrom = 1 : $startFrom = $startFrom; // if less than 1 set to 1
		  
	    $countTo = $startFrom + ($limit-1); // end paginator with page...
		  
	    if($countTo > $lastPage){ // if more than last then set to last
		$countTo = $lastPage;
		$startFrom = $countTo - $limit;
		$startFrom < 1 ? $startFrom = 1 : $startFrom = $startFrom; // if less than 1 set to 1
	    }
		  
	    $pagesElems = '';
		  
	    $pagesElems .= Html::elem('a', array('href'=>$this->requestUri.'pn=1', 'class'=>'paginator-page-link first'), "&#9668;&#9668;&nbsp;&nbsp;").'|';
		  
	    for($i = $startFrom; $i <= $countTo; $i++)
	    {

		if($pgNumber == $i){
		    $pagesElems .= Html::elem('span', array('class'=>'paginator-page-active'), "&nbsp;$i&nbsp;").'|';
		}
		else{
		    $pagesElems .= Html::elem('a', array('href'=>$this->requestUri.'pn='.$i, 'class'=>'paginator-page-link'), "&nbsp;$i&nbsp;").'|';
		}
		  
	    }
		  
	    $pagesElems .= Html::elem('a', array('href'=>$this->requestUri.'pn='.$lastPage, 'class'=>'paginator-page-link last'), "&nbsp;&nbsp;&#9658;&#9658;");
		  
	    return (string)Html::elem('div', array('class'=>'paginator'), $pagesElems);
	}
	else{
	    return '';
	}
	
    }
    
    /**
     * if object called as a string
     * @return string Html
     */
    public function __toString()
    {
        return $this->get();
    }
}