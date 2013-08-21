<?php

  /***************************************
  * http://www.program-o.com
  * PROGRAM O
  * Version: 2.0.9
  * FILE: gui/plain/index.php
  * AUTHOR: ELIZABETH PERREAU
  * DATE: 19 JUNE 2012
  * DETAILS: simple example gui
  ***************************************/
  $display = "";
  $thisFile = __FILE__;
  require_once ('../../config/global_config.php');
  require_once ('../chatbot/conversation_start.php');
  if (isset ($_REQUEST['bot_id']))
  {
    $bot_id = $_REQUEST['bot_id'];
  }
  else
  {
    $bot_id = 1;
  }
  if (isset ($_REQUEST['convo_id']))
  {
    $convo_id = $_REQUEST['convo_id'];
  }
  else
  {
  //session started in the conversation_start.php
    $convo_id = session_id();
  }
  if (isset ($_REQUEST['format']))
  {
    $format = $_REQUEST['format'];
  }
  else
  {
    $format = "html";
  }

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
	<head>
		<link rel="icon" href="./favicon.ico" type="image/x-icon" />
		<link rel="shortcut icon" href="./favicon.ico" type="image/x-icon" />

		  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Program O AIML PHP Chatbot</title>
		<meta name="Description" content="A Free Open Source AIML PHP MySQL Chatbot called Program-O. Version2" />
		<meta name="keywords" content="Open Source, AIML, PHP, MySQL, Chatbot, Program-O, Version2" />
	</head>
	<body onload="document.getElementById('input').focus()">
	<?php echo $display;?>
		<form method="get" action="index.php">
			<p>
				<label>Say:</label>
				<input type="text" id="input" name="say" id="say" />
				<input type="submit" name="submit" id="say" value="say" />
				<input type="hidden" name="convo_id" id="convo_id" value="<?php echo $convo_id;?>" />
				<input type="hidden" name="bot_id" id="bot_id" value="<?php echo $bot_id;?>" />
				<input type="hidden" name="format" id="format" value="<?php echo $format;?>" />
			</p>
		</form>
	</body>
</html>