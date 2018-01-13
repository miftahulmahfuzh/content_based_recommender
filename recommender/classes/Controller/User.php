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
      $email    = $this->getParam('email');
      $password = $this->getParam('password');

      $data = array(
        'username' => $username,
        'email'    => $email,
        'pass'     => $password,
      );
         
      $user = new Storage_User();

      $results = $user->fetch(null, 'email = ' . $user->escape($email));

      if (!empty($results) && $results[0]['activated'] === '0' && $this->isExpired($results[0]['created_at'])) {
        $user->delete($results[0]['id']);
      }

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

    $username = $this->getParam('username');
    $email    = $this->getParam('email');
    $password = $this->getParam('password');

    $data = array(
      'username' => $username,
      'email'    => $email,
      'pass'     => $password,
    );
         
    $user = new Storage_User();

    $errors = $user->validate($data);

    if (!empty($errors)) {
      $this->err400();
    }

    $submit = $this->getParam('submit');

    if (isset($submit)) {
      $results = $user->fetch(null, 'email = ' . $user->escape($email));

      if (!empty($results)) {
        $this->err400();
      }

      $key = generate_random_string(16);

      $data['activation_key'] = $key;

      $user->insert($data);

      $results = $user->fetch(null, 'email = ' . $user->escape($email));

      if (!empty($results)) {
        $subject = 'Membership Register';
        $body    = "Hi " . $username . ",\n"
                 . "To enable your account please click in the following link (within 24 hours): \n"
                 . "http://". $this->getEnv('http-host') . BASE_URI_PATH . "/activate.php?key=" . $key . "&id=" . $results[0]['id'];

        send_mail($email, $subject, $body);

        $this->render('user/create.php', get_defined_vars()); 
      }
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

      $email    = $this->getParam('email');
      $password = $this->getParam('password');

      $data = array(
        'email' => $email,
        'pass'  => $password
      );

      $errors = $this->validate($data);

      if (empty($errors)) {
        $result = $user->fetch(null, 'email = ' . $user->escape($email));
        
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

    if (array_key_exists('email', $data)) {
      $_len = (isset($data['email'])) ? strlen($data['email']) : 0;

      if ($_len === 0) {
        $errors[] = 'email is empty';
      } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'invalid email format';
      }
    }

    if (array_key_exists('pass', $data)) {
      $_len = (isset($data['pass'])) ? strlen($data['pass']) : 0;

      if ($_len === 0) {
        $errors[] = 'password is empty.';
      } elseif ($_len < self::PASSWORD_MIN_LENGTH || $_len > self::PASSWORD_MAX_LENGTH) {
        $errors[] = 'password must be ' . self::PASSWORD_MIN_LENGTH . ' to ' . self::PASSWORD_MAX_LENGTH . ' characters.';
      } else {
        $condition = 'email = ' . $user->escape($data['email']) . ' && pass = ' . $user->escape(hash_password($data['pass']));

        $results = $user->fetch(null, $condition);

        if (empty($results)) {
          $errors[] = 'your email and password do not match';
        } elseif ($results[0]['activated'] === '0') {
          $errors[] = 'your email has not activated yet';
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
