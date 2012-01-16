<?php
/**
 * Module Autoalias
 * created by Branislav Zvolenský
 * 27.11.2010
 *
 * class: module model
 */
class AutoaliasModel
{
    
    function __construct()
    {
        
    }
    
    function find($id)
    {
        return db::fetch("SELECT * FROM `autoalias_setting` WHERE `cid` = $id");
    }
    
    function saveNew($nid, $values)
    {
        
        $autoalias_on = isset($values['autoalias_on']) ? 1 : 0;
        
        if($autoalias_on) Vars::get('POST')->setRaw('path_alias', urlStr(Vars::get('POST')->title) );
        
        return db::exec("INSERT INTO `autoalias_setting` VALUES (null, %i, %i)",
                            $nid, $autoalias_on);
    }
    
    function save($id, $values)
    {
        $row = $this->find($id);

        $autoalias_on = isset($values['autoalias_on']) ? 1 : 0;
        
        if($autoalias_on) Vars::get('POST')->setRaw('path_alias', urlStr(Vars::get('POST')->title) );
        
        if($row) {
            return db::exec("UPDATE `autoalias_setting` SET `autoalias_on` = %i WHERE `cid` = %i",
                            $autoalias_on, $id);
        }
        else {
            return db::exec("INSERT INTO `autoalias_setting` VALUES (null, %i, %i)",
                            $id, $autoalias_on);
        }
    }
    
    function delete($id)
    {
        return db::exec("DELETE FROM `autoalias_setting` WHERE `cid` = %i", $id);
    }
}