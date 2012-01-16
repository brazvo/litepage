<?php
/**
 * Project: LitePage
 *
 * @author Branislav Zvolenský
 *
 * Interface IModuleController
 * defines methods that are required for any module controller
 * which should implement this interface
 * =====================================
 * Each module controller must extend Controller and implement IModuleController
 * 
 */
interface IModuleController
{
    function actionShow($id);
    
    function actionEdit($id);
    
    function actionSave($id, $values);
    
    function actionAdd();
    
    function actionSaveNew($id, $values);
    
    function actionDelete($id);

    function renderShow($id);

    function renderAdd();

    function renderEdit($id);
    
}