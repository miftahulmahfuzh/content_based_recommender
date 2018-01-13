<?php

error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('Asia/Makassar');

define('BASE_URI_PATH',   '/recommender');
define('PROJECT_ROOT',    '/opt/lampp/htdocs' . BASE_URI_PATH);

define('CLASS_FILES_DIR', PROJECT_ROOT  . '/classes');
define('LIB_FILES_DIR',   PROJECT_ROOT  . '/lib');
define('HTML_FILES_DIR',  PROJECT_ROOT  . '/html');
define('LOG_FILES_DIR',   PROJECT_ROOT  . '/logs');
define('LIB_PHP_MAILER',  LIB_FILES_DIR . '/PhpMailer'); 

require_once(PROJECT_ROOT   . '/config/database.php');
require_once(PROJECT_ROOT   . '/lib/functions.php');
require_once(LIB_FILES_DIR  . '/ClassLoader.php');
require_once(LIB_PHP_MAILER . '/class.phpmailer.php');

add_include_path(CLASS_FILES_DIR);
add_include_path(LIB_FILES_DIR, TRUE);

spl_autoload_register(array('ClassLoader', 'autoload'));
