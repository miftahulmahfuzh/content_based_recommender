<?php

class Controller_User extends Controller_Application
{
  const PASSWORD_MIN_LENGTH = 8;
  const PASSWORD_MAX_LENGTH = 16;
  const ACTIVATION_PERIOD   = 24;

  public function register() 
  {
    if ($this->getLoggedInId()) {
      $this->err400();
    }

    $submit = $this->getParam('submit');
    $page   = $this->getParam('page');

    $page = (empty($page)) ? 1 : (int)$page;

    if (isset($submit)) {
      $username = $this->getParam('username');
      $password = $this->getParam('password');

      $data = array(
        'username' => $username,
        'pass'     => $password,
      );
         
      $user = new Storage_User();

      $errors = $user->validate($data);
    } 

    $this->render('user/register.php', get_defined_vars());
  }

  public function create()
  {
    if ($this->getLoggedInId()) {
      $this->err400();
    }

    $page = $this->getParam('page');

    $page = (empty($page)) ? 1 : (int)$page;

    $username  = $this->getParam('username');
    $password  = $this->getParam('password');
    $bisnis    = $this->getParam('bisnis');
    $olahraga  = $this->getParam('olahraga');
    $selebriti = $this->getParam('selebriti');
    $otomotif  = $this->getParam('otomotif');
    $teknologi = $this->getParam('teknologi');

    $data = array(
      'username' => $username,
      'password' => $password,
    );

    $dataPreference = array(
      'bisnis'    => $bisnis,
      'olahraga'  => $olahraga,
      'selebriti' => $selebriti,
      'otomotif'  => $otomotif,
      'teknologi' => $teknologi,
    );
         
    $user = new Storage_User();
    $preference = new Storage_Preference();

    $errors = $user->validate($data);

    if (!empty($errors)) {
      $this->err400();
    }

    $submit = $this->getParam('submit');

    if (isset($submit)) {
      $results = $user->fetch(null, 'username = ' . $user->escape($username));

      if (!empty($results)) {
        $this->err400();
      }

      $user->insert($data);
      $preference->insert($dataPreference);

      $results = $user->fetch(null, 'username = ' . $user->escape($username));

      if (empty($results)) {
        $this->err400();
      }

      $this->render('user/create.php', get_defined_vars()); 
    } else {
      $this->render('user/register.php', get_defined_vars()); 
    }
  }

  public function activate()
  {
    if ($this->getLoggedInId()) {
      $this->err400();
    }

    $user = new Storage_User();

    $key = $this->getParam('key');
    $id  = $this->getParam('id');

    if (empty($key) || empty($id)) {
      $this->err400();
    }

    $result = $user->fetch(
      null,
      'id = ' . $user->escape($id) . ' && ' .
      'activation_key = ' . $user->escape($key)
    );

    if (empty($result)) {
      $this->err404();
    }

    $timeCreated = $result[0]['created_at'];
    $activated   = $result[0]['activated'];

    $expired = false;

    if ($activated === '1') {
      $this->err400();
    } elseif ($this->isExpired($timeCreated)) {
      $expired = true;

      $user->delete($id);
    } else {
      $user->activate($id);
    }

    $this->render('user/activate.php', get_defined_vars());
  }

  public function login()
  {
    if ($this->getLoggedInId()) {
      $this->err400();
    }

    $isLoginForm = true;

    $submit = $this->getParam('submit');
    $page   = $this->getParam('page');

    $page = (empty($page)) ? 1 : (int)$page;

    if (isset($submit)) {
      $user = new Storage_User();

      $username = $this->getParam('username');
      $password = $this->getParam('password');

      $data = array(
        'username' => $username,
        'password' => $password
      );

      $errors = $this->validate($data);

      if (empty($errors)) {
        $result = $user->fetch(null, 'username = ' . $user->escape($username));
        
        $this->session->set('userId', $result[0]['id']);
        $this->session->set('username', $result[0]['username']);

        $this->redirect('index.php', array('page' => $page));
      }
    } 

    if (!empty($errors) || !isset($submit)) {
      $this->render('user/login.php', get_defined_vars());
    }
  }

  public function logout()
  {
    $this->session->stop();

    $this->redirect('index.php');
  }

  protected function validate($data)
  {
    $user = new Storage_User();

    $errors = array();

    if (array_key_exists('username', $data)) {
      $_len = (isset($data['username'])) ? strlen($data['username']) : 0;

      if ($_len === 0) {
        $errors[] = 'username is empty';
      }
    }

    if (array_key_exists('password', $data)) {
      $_len = (isset($data['password'])) ? strlen($data['password']) : 0;

      if ($_len === 0) {
        $errors[] = 'password is empty.';
      } elseif ($_len < self::PASSWORD_MIN_LENGTH || $_len > self::PASSWORD_MAX_LENGTH) {
        $errors[] = 'password must be ' . self::PASSWORD_MIN_LENGTH . ' to ' . self::PASSWORD_MAX_LENGTH . ' characters.';
      } else {
        $condition = 'username = ' . $user->escape($data['username']) . ' && password = ' . $user->escape($data['password']);

        $results = $user->fetch(null, $condition);

        if (empty($results)) {
          $errors[] = 'your username and password do not match';
        } 
      }
    }

    return $errors;
  }

  private function isExpired($timeCreated)
  {
    return (strtotime(date('Y-m-d H:i:s')) - strtotime($timeCreated) > (self::ACTIVATION_PERIOD * 3600));
  }
}
