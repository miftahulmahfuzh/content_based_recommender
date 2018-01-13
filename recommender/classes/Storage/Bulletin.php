<?php 

class Storage_Bulletin extends Storage_Project
{
  const USERNAME_MIN_LENGTH = 3;
  const USERNAME_MAX_LENGTH = 16;
  const TITLE_MIN_LENGTH    = 8;
  const TITLE_MAX_LENGTH    = 32;
  const BODY_MIN_LENGTH     = 8;
  const BODY_MAX_LENGTH     = 200;
  const PASSWORD_LENGTH     = 4;

  protected $tableName = 'articles';
  
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
  
  public function validate($data, $forUpdate = false)
  {
    $errors = array();

    if (array_key_exists('username', $data) || !$forUpdate) {
      $_len = (isset($data['username'])) ? strlen($data['username']) : 0;
      if ($_len > 0 && ($_len < self::USERNAME_MIN_LENGTH || $_len > self::USERNAME_MAX_LENGTH)) {
        $errors[] = 'username must be ' . self::USERNAME_MIN_LENGTH . ' to ' . self::USERNAME_MAX_LENGTH . ' characters.';
      }
    }

    if (array_key_exists('title', $data) || !$forUpdate) {
      $_len = (isset($data['title'])) ? strlen($data['title']) : 0;
      if ($_len === 0) {
        $errors[] = 'title is empty.';
      } elseif ($_len < self::TITLE_MIN_LENGTH || $_len > self::TITLE_MAX_LENGTH) {
        $errors[] = 'title must be ' . self::TITLE_MIN_LENGTH . ' to ' . self::TITLE_MAX_LENGTH . ' characters.';
      } 
    }
    
    if (array_key_exists('body', $data) || !$forUpdate) {
      $_len = (isset($data['body'])) ? strlen($data['body']) : 0;
      if ($_len === 0) {
        $errors[] = 'body is empty.';
      } elseif ($_len < self::BODY_MIN_LENGTH || $_len > self::BODY_MAX_LENGTH) {
        $errors[] = 'body must be ' . self::BODY_MIN_LENGTH . ' to ' . self::BODY_MAX_LENGTH . ' characters.';
      }
    }
    
    if (isset($data['pass']) && preg_match('/^[0-9]{' . self::PASSWORD_LENGTH . '}$/', $data['pass']) === 0) {
        $errors[] = 'password must be ' . self::PASSWORD_LENGTH . ' digit number.';
    }

    return $errors;
  }

  public function softDelete($postId)
  {
    $condition = 'id = ' . $this->escape($postId);

    return $this->database->update($this->tableName, array(
      'is_deleted' => 1,
    ), $condition);
  }
}
