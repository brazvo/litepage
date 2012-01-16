<?php

class Controller_Template extends BaseAdmin
{

  // Properties
  
  // Constructor
  public function __construct()
  {
    /* $this->perm_mach_name = 'content'; */ //
    parent::__construct();

  }
  
  // Methods
  /***************************************** ACTIONS ***/
  function actionSave()
  {
    
  }
  
  function actionSavenew()
  {
    
  }
  
  function actionDelete()
  {
    
  }
  
  /***************************************** RENDERERS ***/
  function renderDefault()
  {
     
  	$this->template->title = 'Controller Template';
	$this->template->content = 'Place for page content';
  
  }
  
  function renderAdd()
  {
  
  }
  
  function renderEdit()
  {
  
  }
  
  /***************************************** FACTORIES ***/
  private function createControlForm()
  {
  
  }
  
  /***************************************** INSTALLATION ROUTINE ***/
  private function install()
  {
    
  }
  
  /***************************************** UNINSTALLATION ROUTINE ***/
  private function uninstall()
  {
  
  }

}