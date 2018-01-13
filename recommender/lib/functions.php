<?php

function html_escape($text, $flags = null, $encoding = null)
{
  if (empty($flags)) {
    $flags = ENT_QUOTES;
  }

  if (empty($encoding)) {
    $encoding = 'UTF-8';
  }

  return htmlentities($text, $flags, $encoding);
}

/**
 * Utility(Alias) function for html_escape.
 */
function h($text, $flags = null, $encoding = null)
{
  return html_escape($text, $flags, $encoding);
}

function get_uri($uri)
{
  if (defined('BASE_URI_PATH')) {
    $uri = BASE_URI_PATH . '/' . ltrim($uri, '/');
  }

  return $uri;
}

function is_natural_number($num, $includeZero = false)
{
  if (is_int($num)) {
    return ($includeZero) ? ($num >= 0) : ($num > 0);
  } elseif (is_string($num)) {
    if ($num === "0" && $includeZero) {
      return true;
    } elseif (preg_match('/^[1-9][0-9]*$/', $num) === 1) {
      return true;
    } else {
      return false;
    }
  } else {
    return false;
  }
}

function generate_random_string($length = 16)
{
  $charas  = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $charLen = strlen($charas);
  
  $string = '';
  for ($i = 0; $i < $length; $i++) {
    $string .= $charas[mt_rand(0, $charLen - 1)];
  }
  
  return $string;
}

function get_db_config()
{
  $config = array();
  
  $keys = array('HOST', 'NAME', 'USER', 'PASSWORD');
  foreach ($keys as $key) {
    if (defined('DATABASE_' . $key)) {
      $config[strtolower($key)] = constant('DATABASE_' . $key);
    } else {
      throw new Exception(__FUNCTION__ . "() DATABASE_{$key} is not defined.");
    }
  }

  return $config;
}

function add_include_path($path, $prepend = false)
{
  $current = get_include_path();

  if ($prepend) {
    set_include_path($path . PATH_SEPARATOR . $current);
  } else {
    set_include_path($current . PATH_SEPARATOR . $path);
  }
}

/**
 * Utility function for debug.
 */
function dump(/* plural args */)
{
  echo '<pre style="background: #fff; color: #333; ' .
       'border: 1px solid #ccc; margin: 5px; padding: 10px;">';
  
  foreach (func_get_args() as $value) {
    var_dump($value);
  }
  
  echo '</pre>';
}

function send_mail($email, $subject, $body) 
{
  $mail = new PHPMailer;

  $mail->isSMTP();       

  $mail->SMTPAuth      = true;
  $mail->Host          = 'mail.ozevisionwebhosting.com'; 
  $mail->Port          = '2525';                        
  $mail->Username      = 'intern@timedoor.net';
  $mail->Password      = 'pasprot1';
  $mail->SMTPKeepAlive = true;
  $mail->Mailer        = 'smtp';
  $mail->SMTPAuth      = true;                       
  $mail->CharSet       = 'utf-8';
  $mail->SMTPDebug     = 0;
  $mail->From          = 'intern@timedoor.net';
  $mail->FromName      = 'Timedoor Intern Programme';
  
  $mail->addAddress($email);
  $mail->addReplyTo('intern@timedoor.net');
  
  $mail->isHTML(true);                             

  $mail->Subject = $subject;
  $mail->Body    = $body;

  if (!$mail->send()) {
    dump('Send failed: ' . $mail->ErrorInfo . ', Please try again!');

    die;
  }
  
  return true;
}

function hash_password($pass) 
{
  return sha1($pass);
}
