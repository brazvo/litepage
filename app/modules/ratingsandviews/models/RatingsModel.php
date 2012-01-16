<?php
/**
 * Module Ratings
 * created by Branislav Zvolenský
 * 29.08.2011
 *
 * class: module model
 */
class RatingsModel
{
    
    function __construct()
    {
        
    }
    
    function find($id)
    {
        return db::fetch("SELECT * FROM `ratingsandviews` WHERE `cid` = $id");
    }
    
    function saveNew($nid, $values)
    {
        
        $rating_on = isset($values['rating_on']) ? 1 : 0;
		$view_on = isset($values['view_on']) ? 1 : 0;
        
        return db::exec("INSERT INTO `ratingsandviews` VALUES (null, %i, 0, 0, 0, %i, %i)",
                            $nid, $rating_on, $view_on);
    }
    
    function save($id, $values)
    {
        $row = $this->find($id);

        $rating_on = isset($values['rating_on']) ? 1 : 0;
		$view_on = isset($values['view_on']) ? 1 : 0;
        
        if($row) {
            return db::exec("UPDATE `ratingsandviews` SET `rating_on`=%i, `view_on`=%i WHERE `cid` = %i",
                            $rating_on, $view_on, $id);
        }
        else {
            return db::exec("INSERT INTO `ratingsandviews` VALUES (null, %i, 0, 0, 0, %i, %i)",
                            $id, $rating_on, $view_on);
        }
    }
    
    function delete($id)
    {
        return db::exec("DELETE FROM `autoalias_setting` WHERE `cid` = %i", $id);
    }
	
	function getSettings()
	{
		return db::fetchAll("SELECT * FROM `ratingsandviews_settings`");
	}
	
	function saveSettings($values)
	{
		unset($values['FILES']);
		foreach ($values as $field => $val) {
			if( !db::exec("UPDATE `ratingsandviews_settings` SET `value` = '{$val}' WHERE `frm_name` = %v", $field) )
				return FALSE;
		}
		return TRUE;
	}
	
	function getContentType($id)
	{
		return db::fetchSingle("SELECT content_type_machine_name FROM content WHERE id=%i", $id);
	}
	
	function getRating($cid)
	{
		$row = db::fetch("SELECT rate_amount, raters FROM ratingsandviews WHERE cid=%i",$cid);

		if(!$row) return array('rating'=>0, 'raters'=>0);
		else return array('rating'=> ($row['rate_amount'] > 0 ? $row['rate_amount'] / $row['raters'] : 0), 'raters'=>$row['raters']);
	}
	
	public function saveRating($cid, $am)
	{
		$row = db::fetch("SELECT * FROM ratingsandviews WHERE cid=%i", $cid);
		
		if($row) {
			return db::exec("UPDATE ratingsandviews SET rate_amount=rate_amount+{$am}, raters=raters+1 WHERE cid=%i", $cid);
		}
		else {
			return db::exec("INSERT INTO ratingsandviews VALUES (null, {$cid}, 0, {$am}, 1, 0, 0)");
		}
	}
	
	
	public function saveViewed($cid)
	{
		$row = db::fetch("SELECT * FROM ratingsandviews WHERE cid=%i", $cid);
		
		if($row) {
			return db::exec("UPDATE ratingsandviews SET views=views+1 WHERE cid=%i", $cid);
		}
		else {
			return db::exec("INSERT INTO ratingsandviews VALUES (null, {$cid}, 1, 0, 0, 0, 0)");
		}
	}
}