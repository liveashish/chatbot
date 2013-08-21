<?php

  /***************************************
  * www.program-o.com
  * PROGRAM O
  * Version: 2.0.9
  * FILE: gui/xml/index.php
  * AUTHOR: ELIZABETH PERREAU and DAVE MORTON
  * DATE: JUNE. 19th, 2012
  * DETAILS: this file contains the chatbot's
  XML interface
  ***************************************/
  session_start();
  $thisFile = __FILE__;
  require_once ('../../config/global_config.php');
  //handle the convo id here otherwise i cant clear it
  //TODO SORT THAT OUT!
  if (isset ($_REQUEST['say']) && ($_REQUEST['say'] == 'clear properties'))
  {
    if (ini_get("session.use_cookies"))
    {
      $params = session_get_cookie_params();
      setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }
    // Finally, destroy the session.
    session_destroy();
    session_start();
    session_regenerate_id();
    $convo_id = session_id();
    $say = urlencode($_REQUEST['say']);
  }
  elseif (isset ($_REQUEST['say']))
  {
    $convo_id = session_id();
    $say = urlencode($_REQUEST['say']);
  }
  else
  {
    $say = "hi";
    $convo_id = session_id();
  }
  $response = '';
  $responseXML = '';
  $bot_id = 1;
  $format = "xml";
  $thisFileURL = $_SERVER['SCRIPT_NAME'];
  $chatbotURLpath = str_replace('/gui/xml/index.php', '/chatbot', $thisFileURL);
  define("CHATBOT_URL_PATH", $chatbotURLpath);
  $send = "http://" . $_SERVER['HTTP_HOST'] . CHATBOT_URL_PATH . "/conversation_start.php?say=$say&convo_id=$convo_id&bot_id=$bot_id&format=$format";
  #$X = file_put_contents('URL.txt', "$send\r\n",FILE_APPEND);
  #die();
  $sXML = trim(get_response($send));
  //just output as an example
  $responseXML = htmlentities($sXML);
  $responseXML = str_replace("\n\t", "<br/>        ", $responseXML);
  $responseXML = str_replace("\n", "<br/>   ", $responseXML);
  $xml = new SimpleXMLElement($sXML);
  $count = 0;
  foreach ($xml->children() as $child)
  {
    $childName = $child->getName();
    switch ($childName)
    {
      case 'user_name' :
        $user_name = $child;
        break;
      case 'bot_name' :
        $bot_name = $child;
        break;
      case 'usersay' :
        $response .= "$user_name: " . $child . "<br />\n";
        break;
      case 'botsay' :
        $response .= "$bot_name: " . $child . "<br />\n";
      default :
    }
  }

  function get_response($path)
  {
    $strCookie = 'PHPSESSID=' . $_COOKIE['PHPSESSID'] . '; path=/';
    session_write_close();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $path);
    curl_setopt($ch, CURLOPT_FAILONERROR, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_COOKIE, $strCookie);
    $retValue = curl_exec($ch);
    curl_close($ch);
    return $retValue;
  }

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
  <head>
    <link rel="icon" href="./favicon.ico" type="image/x-icon" />
    <link rel="shortcut icon" href="./favicon.ico" type="image/x-icon" />
      <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>Program O AIML Chatbot</title>
    <meta name="Description" content="A Free Open Source AIML PHP MySQL Chatbot called Program-O. Version2" />
    <meta name="keywords" content="Open Source, AIML, PHP, MySQL, Chatbot, Program-O, Version2" />
  </head>
  <body>
    <div id="response"><?php echo $response ?></div>
    <form method="post" action="./">
      <p>
        <label>Say:</label>
        <input type="text" name="say" id="say" />
        <input type="submit" name="submit" id="say" value="say" />
        <input type="hidden" name="convo_id" id="convo_id" value="<?php echo $convo_id;?>" />
        <input type="hidden" name="bot_id" id="bot_id" value="<?php echo $bot_id;?>" />
        <input type="hidden" name="format" id="format" value="<?php echo $format;?>" />
      </p>
    </form>

    <?php echo $responseXML;?>

  </body>
</html>