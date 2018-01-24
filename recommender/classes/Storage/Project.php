<?php 

class Storage_Project extends Storage_Base
{
  public function update($id, $data)
  { 
    $condition = 'id='.$id;
    $this->database->update($this->tableName, $data, $condition);
  }

  public function delete($id)
  {
    $condition = 'id = ' . $this->escape($id);

    return $this->database->delete($this->tableName, $condition);
  }
}
