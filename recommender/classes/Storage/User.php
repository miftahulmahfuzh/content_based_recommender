<?php 

class Storage_User extends Storage_Project
{
  const USERNAME_MIN_LENGTH = 3;
  const USERNAME_MAX_LENGTH = 16;
  const PASSWORD_MIN_LENGTH = 8;
  const PASSWORD_MAX_LENGTH = 16;
  const ACTIVATION_PERIOD   = 24;

  protected $tableName = 'user';
  
  public function insert($data)
  {
    $this->database->insert($this->tableName, $data);
  }
  
  public function validate($data) 
  {
    $errors = array();
    
    if (array_key_exists('username', $data)) {
      $_len = (isset($data['username'])) ? strlen($data['username']) : 0;

      if ($_len === 0) {
        $errors[] = 'username is empty.';
      } elseif ($_len < self::USERNAME_MIN_LENGTH || $_len > self::USERNAME_MAX_LENGTH) {
        $errors[] = 'username must be ' . self::USERNAME_MIN_LENGTH . ' to ' . self::USERNAME_MAX_LENGTH . ' characters.';
      } else {
        $results = $this->fetch(null, 'username = ' . $this->escape($data['username']));

        if (!empty($results)) {
          $errors[] = 'someone else is using this username';
        }
      }
    }

    if (array_key_exists('password', $data)) {
      $_len = (isset($data['password'])) ? strlen($data['password']) : 0;

      if ($_len === 0) {
        $errors[] = 'password is empty.';
      } elseif ($_len < self::PASSWORD_MIN_LENGTH || $_len > self::PASSWORD_MAX_LENGTH) {
        $errors[] = 'password must be ' . self::PASSWORD_MIN_LENGTH . ' to ' . self::PASSWORD_MAX_LENGTH . ' characters.';
      }
    }

    return $errors;
  }

  public function activate($id) 
  {
    return $this->update($id, array('activated' => 1));
  }
}
