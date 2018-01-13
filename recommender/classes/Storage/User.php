<?php 

class Storage_User extends Storage_Project
{
  const USERNAME_MIN_LENGTH = 3;
  const USERNAME_MAX_LENGTH = 16;
  const PASSWORD_MIN_LENGTH = 8;
  const PASSWORD_MAX_LENGTH = 16;
  const ACTIVATION_PERIOD   = 24;

  protected $tableName = 'sample_user';

  public function insert($data)
  {
    if (isset($data['pass'])) {
      $data['pass'] = hash_password($data['pass']);
    }

    if (!isset($data['created_at'])) {
      $data['created_at'] = date('Y-m-d H:i:s');
    }
    
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
      }
    }

    if (array_key_exists('email', $data)) {
      $_len = (isset($data['email'])) ? strlen($data['email']) : 0;

      if ($_len === 0) {
        $errors[] = 'email is empty.';
      } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'invalid email format';
      } else {
        $results = $this->fetch(null, 'email = ' . $this->escape($data['email']));

        if (!empty($results)) {
          $errors[] = 'someone is using this email';
        }
      }
    }

    if (array_key_exists('pass', $data)) {
      $_len = (isset($data['pass'])) ? strlen($data['pass']) : 0;

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
