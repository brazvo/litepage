<?php

abstract class BaseModel
{
  // Properties
  protected $db;
  
  // Constructor
  function __construct()
  {
    $this->db = Application::$db;
  }
  
  // Methods
  

}