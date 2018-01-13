<?php

class Storage_Database_MySQL extends Storage_Database
{
  public function __construct($config = array())
  {
    if (!isset($config['charset'])) {
      $config['charset'] = 'utf8';
    }

    parent::__construct($config);
  }
  
  public function fetch($tableName, $column = null, $condition = null, $order = null, $offset = null, $limit = null)
  {
    if (empty($column)) {
      $column = '*';
    }

    $sql = "SELECT {$column} FROM {$tableName}";

    if (!empty($condition)) {
      $sql = $sql . ' WHERE ' . $condition;
    }
    
    if (!empty($order)) {
      $sql = $sql . ' ORDER BY ' . $order;
    }
    
    if (!empty($limit)) {
      $sql = $sql . ' LIMIT ' . $limit;
    }
    
    if (!empty($offset)) {
      $sql = $sql . ' OFFSET ' . $offset;
    }

    $result = mysqli_query($this->conn, $sql);
   
    if ($result === false) {
      throw new Exception(__METHOD__ . '() ' . mysql_error($this->conn));
    }

    $rows = array();
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }

    return $rows;
  }

  public function getCount($tableName, $column = null, $condition = null)
  {
    if (empty($column)) {
      $column = '*';
    }

    $result = $this->fetch($tableName, "COUNT({$column}) AS c", $condition);

    if (isset($result[0]['c'])) {
      return $result[0]['c'];
    } else {
      throw new Exception(__METHOD__ . '() failed.');
    }
  }

  public function insert($tableName, $data)
  {
    if (empty($data)) {
      throw new Exception(__METHOD__ . '() data is empty.');
    }

    $sql     = "INSERT INTO {$tableName}";
    $keys    = array_keys($data);
    $columns = implode(', ', $keys);
    $values  = array();
    
    foreach ($data as $value) {
      $values[] = $this->escape($value);
    }

    $values = implode(', ', $values);  
    $sql    = $sql . "({$columns}) VALUES({$values})"; 
    
    $result = mysqli_query($this->conn, $sql);    

    if ($result === false) {
      throw new Exception(__METHOD__ . '() ' . mysql_error($this->conn));
    }

    return $result;
  }

  public function update($tableName, $data, $condition = null)  
  { 
    if (empty($data)) {
      throw new Exception(__METHOD__ . '() data is empty.');
    }

    $sql = "UPDATE {$tableName}";
    
    $values = array();
    foreach ($data as $key => $value) {
      $values[] = $key . ' = ' . $this->escape($value);
    }

    $values = implode(', ', $values);  
    $sql = $sql . " SET {$values}"; 

    if (!empty($condition)) {
      $sql = $sql . " WHERE {$condition}"; 
    }

    $result = mysqli_query($this->conn, $sql);

    if ($result === false) {
      throw new Exception(__METHOD__ . '() ' . mysql_error($this->conn));
    }

    return $result;
  }

  public function delete($tableName, $condition = null)
  {
    $sql = "DELETE FROM {$tableName}";
    if (!empty($condition)) {  
      $sql = $sql . ' WHERE ' . $condition;
    }
    
    $result = mysqli_query($this->conn, $sql);

    if ($result === false) {
      throw new Exception(__METHOD__ . '() ' . mysql_error($this->conn));
    }

    return $result;
  }

  public function escape($value, $withQuotes = true)
  {
    if (empty($value)) {
      return 'NULL';
    } elseif (is_string($value)) {
      $value = mysqli_real_escape_string($this->conn, $value);
      return ($withQuotes) ? "'{$value}'" : $value;
    } else {
      return $value;
    }
  }

  protected function connect()
  {
    $config = $this->config;

    $host = $config['host'];
    if (isset($config['port']) && !empty($config['port'])) {
      $host .= ':' . $config['port'];
    }
    
    $conn = mysqli_connect($host, $config['user'], $config['password'], $config['name']);

    if (!$conn) {
      throw new Exception(__METHOD__ . "() Can't connect to the database server. " . mysql_error());
    }

    if (isset($config['charset'])) {
      mysqli_set_charset($conn, $config['charset']);
    }

    return $conn; 
  }
}
