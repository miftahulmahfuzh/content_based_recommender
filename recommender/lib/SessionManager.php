<?php

class SessionManager
{
  public function __construct()
  {
    if (!$this->isSessionActive()) {
      $this->start();
    }
  }

  public function isSessionActive()
  {
    return session_status() !== PHP_SESSION_NONE;
  }

  public function start() 
  {
    session_start();
  }

  public function set($key, $value)
  {
    $_SESSION[$key] = $value;
  }
  
  public function remove($key)
  {
    unset($_SESSION[$key]);
  }

  public function get($key)
  {
    if (!isset($_SESSION[$key])) {
      return NULL;
    }

    return $_SESSION[$key];
  }

  public function stop()
  {
    if ($this->isSessionActive()) {
      session_unset();
      session_destroy();
    }
  }
}
