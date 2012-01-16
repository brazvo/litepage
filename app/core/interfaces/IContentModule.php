<?php
interface IContentModule
{
    function handleShow($id);
    
    function handleEdit($id, $values);
    
    function handleSave($id, $values);
    
    function handleAdd($id, $values);
    
    function handleSaveNew($newid, $values);
    
    function handleDelete($id);
    
}