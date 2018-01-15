<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>News Recommender System</title>
    <link type="text/css" rel="stylesheet" href="<?php echo get_uri('css/reset.css') ?>" />
    <link type="text/css" rel="stylesheet" href="<?php echo get_uri('css/default.css') ?>" />
  </head>
  <body>
    <div id="header">
      <h1><input type="button" value="News Recommender System" onclick="window.location.href='<?php echo get_uri('index.php') ?>';"></h1>
    </div>
    <?php if ($this->session->get('username')): ?>
    <div id="username">
      Tester : <?php echo $this->session->get('username') ?>
      <input type="button" value="Logout" onclick="window.location.href='<?php echo get_uri('logout.php') ?>';">
    </div>
    <?php endif ?>
