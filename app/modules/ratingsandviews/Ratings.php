<?php
/**
 * Module Ratings and views
 * created by Branislav Zvolenský
 * 29.08.2011
 *
 * class: module controller
 */
class Ratings implements IContentModule
{
    
	private $viewed;
	
    function __construct()
    {
        include_once(dirname(__FILE__) . '/models/RatingsModel.php');
		include_once(dirname(__FILE__) . '/controls/StarRater.php');
        if(@is_file(dirname(__FILE__) . '/languages/' . Application::$language['mach_name'] . '.inc'))
            include_once(dirname(__FILE__) . '/languages/' . Application::$language['mach_name'] . '.inc');
        else
            include_once(dirname(__FILE__) . '/languages/english.inc');
		$this->runSettings();
    }
	
	function startUp($oController, $id)
	{
		$oController->template->CSS->add("app/modules/ratingsandviews/css/ratings.css");
	}
    
    function handleShow($id)
    {
        $mod = new RatingsModel;
		
		$contentType = $mod->getContentType($id);
		$row = $mod->find($id);
		if(!$row) {
			$row = array('rating_on'=>0, 'view_on'=>0);
		}
		$this->viewed = isset( $row['views'] ) ? $row['views'] : 0;
		// save viewed anyway but not after redirect
		$GET = Vars::get('GET');
		if( !isset($GET->rd) ) $mod->saveViewed($id);
		
		// allowed for rating
		$aAllowedForRatings = array();
		if( trim(RAV_RATINGS_AFFECTED_CONTENT_TYPES) !== '' ) {
			
			if( preg_match('/,/', RAV_RATINGS_AFFECTED_CONTENT_TYPES) ) {
				$aTemp = preg_split('/,/', RAV_RATINGS_AFFECTED_CONTENT_TYPES, null, PREG_SPLIT_NO_EMPTY);
				foreach($aTemp as $val) {
					$aAllowedForRatings[] = trim($val);
				}
			}
			else {
				$aAllowedForRatings[] = trim(RAV_RATINGS_AFFECTED_CONTENT_TYPES);
			}
			
		}
		
		// allowed for rating
		$aAllowedForViews = array();
		if( trim(RAV_VIEWS_AFFECTED_CONTENT_TYPES) !== '' ) {
			
			if( preg_match('/,/', RAV_VIEWS_AFFECTED_CONTENT_TYPES) ) {
				$aTemp = preg_split('/,/', RAV_VIEWS_AFFECTED_CONTENT_TYPES, null, PREG_SPLIT_NO_EMPTY);
				foreach($aTemp as $val) {
					$aAllowedForViews[] = trim($val);
				}
			}
			else {
				$aAllowedForViews[] = trim(RAV_VIEWS_AFFECTED_CONTENT_TYPES);
			}
			
		}
		
		$rating = '';
		$views = '';
		if( in_array($contentType, $aAllowedForRatings) or $row['rating_on'] ) {
			$rating = $this->createRating($id);
		}
		if( in_array($contentType, $aAllowedForViews) or $row['view_on'] ) {
			$views =   $this->createViewed($id);
		}
		
		return Html::divBlock($rating . $views, 'ratins-and-views', 'ratings-and-views');
    }
    
    function handleEdit($id, $values)
    {
        $allowed = Application::getPermisionForAction(Application::$logged['role'], 'ratingsandviews', 'edit');
        
        if($allowed) {
            
            // backward compatibility
            if(!empty ($values) ) {
                $this->handleSave($id, $values);                                        
            }
            
            $obj = new RatingsModel();
            $row = $obj->find($id);

            if($row) {
                return $this->createFormInputs($row);
            }
            else {
                return $this->createFormInputs(array('rating_on'=>0, 'view_on'=>0));
            }
        }
        else {
            return '';
        }
    }
    
    function handleSave($id, $values)
    {
        $obj = new RatingsModel();
        
        $result = $obj->save($id, $values);
    }
    
    function handleAdd($id, $values)
    {
        if(!Application::getPermisionForAction(Application::$logged['role'], 'ratingsandviews', 'add')) {
            return '';
        }
		// backward compatibility
		if(!empty ($values) ) {
			$this->handleSaveNew($id, $values);                                        
		}
        return $this->createFormInputs( array('rating_on'=>0, 'view_on'=>0) );
    }
    
    function handleSaveNew($newid, $values)
    {
        $obj = new RatingsModel();
        
        $result = $obj->saveNew($newid, $values);
    }
    
    function handleDelete($id)
    {
        
    }
    
    private function createFormInputs($values = null)
    {
		
		$ratings = Html::elem('input')->id('formFieldRatings')->setClass('frm-checkbox');
        $ratings->type = 'checkbox';
        $ratings->name = 'rating_on';
        $ratings->value = 1;
        $checked = $values['rating_on'] ? 'checked' : null;
        if($checked) $ratings->checked = $checked;
		
		$views = Html::elem('input')->id('formFieldViews')->setClass('frm-checkbox');
        $views->type = 'checkbox';
        $views->name = 'view_on';
        $views->value = 1;
        $checked = $values['view_on'] ? 'checked' : null;
        if($checked) $views->checked = $checked;
		
		$Desc = Html::elem('div')->setClass('frm-item-description')->setCont(RAV_FRM_RATINGS_DESC);
        $rLabel = Html::elem('span')->setClass('frm-label')->setCont(Html::elem('label')->setCont(RAV_FRM_RATINGS_LABEL));
        $rItem = Html::elem('div')->setClass('frm-control')->setCont($Desc . $ratings . $rLabel);
        $formInput1 = Html::elem('div')->setClass('form-element')->setCont($rItem);

		$vLabel = Html::elem('span')->setClass('frm-label')->setCont(Html::elem('label')->setCont(RAV_FRM_VIEWS_LABEL));
        $vItem = Html::elem('div')->setClass('frm-control')->setCont($views . $vLabel);
        $formInput2 = Html::elem('div')->setClass('form-element')->setCont($vItem);
        
        $legend = Html::elem('legend')->setCont(RAV_FRM_RATINGS_SETTINGS)->setClass('autoalias_fieldset_legend');
        
        $fieldset = Html::elem('fieldset')->setClass('autoalias_fieldset')->setCont($legend . $formInput1 . $formInput2);
        
        return $fieldset;
    }
	
	private function runSettings()
	{
		$obj = new RatingsModel;

		$rows = $obj->getSettings();

		foreach($rows as $row){
			define($row['constant'], $row['value']);
		}
	}
	
	
	public function createRating($id)
    {
        $obj = new RatingsModel;
		$rat = $obj->getRating($id);
		$starRater = new StarRater( round($rat['rating'],2), $rat['raters'], $id );
		
        if( isset( $_COOKIE[ md5( baseUrl()."ratings_{$id}" ) ] ) && $_COOKIE[ md5( baseUrl()."ratings_{$id}" ) ] === '1' ) {
            $starRater->isActive( FALSE );
			$starRater->setMessage ( 'Už máme tvoj hlas' );
        }
        
        return $starRater;
    }
	
	
	public function createViewed($id)
    {
        
		$span = Html::elem('span')->setClass('viewed')->setCont( $this->viewed."x" );
		$block = Html::divBlock(RAV_VIEWS_VIEWED . $span, 'control-viewed', 'control-viewed');
		
		return $block;
    }
}
