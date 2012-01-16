<?php
/**
 * Template Model
 * if module extends content it should implement IContentModule interface
 * created by Branislav Zvolenský
 *
 * class: module controller
 */
class Template implements IContentModule
{
    
    function __construct()
    {
        include_once(dirname(__FILE__) . '/../models/AdmTemplateModel.php');
        if(@is_file(dirname(__FILE__) . '/../languages/' . Application::$language['mach_name'] . '.inc'))
            include(dirname(__FILE__) . '/../languages/' . Application::$language['mach_name'] . '.inc');
        else
            include(dirname(__FILE__) . '/../languages/english.inc');
    }
    
    function handleShow($id)
    {
        
    }
    
    function handleEdit($id)
    {
        
    }
    
    function handleSave($id, $values)
    {
        
    }
    
    function handleAdd()
    {
        
    }
    
    function handleSaveNew($id, $values)
    {
        
    }
    
    function handleDelete($id)
    {
        
    }
    

}
