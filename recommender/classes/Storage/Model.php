<?php

class Storage_Model extends Storage_Project
{
  protected $tableName = '';

  public function __construct($alg)
  {
    parent::__construct();

    if ($alg==1) {
      $table = 'model_jac';
    } else if ($alg==2) {
      $table = 'model_euc';
    } else {
      $table = 'model_cos';
    }

    $this->tableName = $table;
  }
}
