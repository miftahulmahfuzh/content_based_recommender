<?php 

class Storage_Preference extends Storage_Project
{
  protected $tableName = 'preferences';

  public function insert($data)
  {
    $this->database->insert($this->tableName, $data);
  }
}
