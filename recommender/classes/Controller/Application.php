<?php

class Controller_Application extends Controller_Base
{
  protected $session = NULL;

  public function setUp()
  {
    parent::setUp();

    $this->session = new SessionManager();
  }

  public function getLoggedInId()
  {
    $userId = $this->session->get('userId');

    if (empty($userId)) {
      return false;
    } 

    return $userId;
  }
}
