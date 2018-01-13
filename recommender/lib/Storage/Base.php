<?php 

abstract class Storage_Base
{
  protected $database  = null;
  protected $tableName = '';

  public function __construct()
  {
    $this->database = new Storage_Database_MySQL();
  }  

  public function fetch($column = null, $condition = null, $order = null, $offset = null, $limit = null)
  {
    return $this->database->fetch($this->tableName, $column, $condition, $order, $offset, $limit);
  }
  
  public function getCount($column = null, $condition = null) 
  {
    return $this->database->getCount($this->tableName, $column, $condition);
  }

  public function escape($value, $withQuotes = true)
  {
    return $this->database->escape($value, $withQuotes);
  }
}
