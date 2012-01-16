<?php
/**
 * Module Simple SEO
 * created by Branislav Zvolenský
 * 27.11.2010
 *
 * class: module model
 */
class SimpleseoModel
{
    
    function __construct()
    {
        
    }
    
    function find($id)
    {
        return db::fetch("SELECT * FROM `content_seo` WHERE `cid` = $id");
    }
    
    function saveNew($nid, $values)
    {
        
        $simpleseo_keywords = isset($values['simpleseo_keywords']) ? preg_replace("/'/", "", $values['simpleseo_keywords']) : '';
        $simpleseo_description = isset($values['simpleseo_description']) ? $values['simpleseo_description'] : '';
        $simpleseo_robots = isset($values['simpleseo_robots']) ? $values['simpleseo_robots'] : '';
        
        return db::exec("INSERT INTO `content_seo` VALUES (null, %i, %v, %v, %v)",
                            $nid, $simpleseo_keywords, $simpleseo_description, $simpleseo_robots);
    }
    
    function save($id, $values)
    {
        $row = $this->find($id);
        
        $simpleseo_keywords = isset($values['simpleseo_keywords']) ? $values['simpleseo_keywords'] : '';
        $simpleseo_description = isset($values['simpleseo_description']) ? $values['simpleseo_description'] : '';
        $simpleseo_robots = isset($values['simpleseo_robots']) ? $values['simpleseo_robots'] : '';
        
        if($row) {
            return db::exec("UPDATE `content_seo` SET `keywords` = %v, `description` = %v, `robots` = %v WHERE `cid` = %i",
                            $simpleseo_keywords, $simpleseo_description, $simpleseo_robots, $id);
        }
        else {
            return db::exec("INSERT INTO `content_seo` VALUES (null, %i, %v, %v, %v)",
                            $id, $simpleseo_keywords, $simpleseo_description, $simpleseo_robots);
        }
    }
    
    function delete($id)
    {
        return db::exec("DELETE FROM `content_seo` WHERE `cid` = %i", $id);
    }
}