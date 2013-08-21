<?php
$thisFile = __FILE__;
session_start();
$bot_id = 1;
$format = "json";
$convo_id = session_id();

  $docRoot = $_SERVER['DOCUMENT_ROOT'];
  $docRoot = str_replace('/', DIRECTORY_SEPARATOR, $docRoot);
  $thisFolder = dirname(realpath(__FILE__)) . DIRECTORY_SEPARATOR;
  $baseFolder = str_ireplace('gui'.DIRECTORY_SEPARATOR.'jquery'.DIRECTORY_SEPARATOR, '', $thisFolder);
  $relPath = str_ireplace(array($docRoot, DIRECTORY_SEPARATOR), array('', '/'), $baseFolder);
  $configFile = $baseFolder . 'config' . DIRECTORY_SEPARATOR . 'global_config.php';
  $headerURL = 'http://' . $_SERVER["HTTP_HOST"] . $relPath . 'install/install_programo.php';


  require_once($configFile);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
	<head>
	<link rel="stylesheet" type="text/css" href="main.css" media="all" />
	<link rel="icon" href="./favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="./favicon.ico" type="image/x-icon" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Program O AIML PHP Chatbot</title>
	<meta name="Description" content="A Free Open Source AIML PHP MySQL Chatbot called Program-O. Version2" />
	<meta name="keywords" content="Open Source, AIML, PHP, MySQL, Chatbot, Program-O, Version2" />
		
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.0/jquery.min.js"></script>
	<script type="text/javascript" >

	 $(document).ready(function() {
		// put all your jQuery goodness in here.
			$('#talkform').submit(function() {
			user = $('#say').val();
			$('.usersay').text(user);
			formdata = $("#talkform").serialize();
		  	$('#say').val('')
			$('#say').focus();

			$.post('../../chatbot/conversation_start.php', formdata, function(data){ 
				var b = data.botsay; 
				$('.botsay').html(b);
			
			 }, 'json');	
		  return false;
		});
	});
	</script>
</head>
<body>
	<div class="centerthis">
		<div class="manspeech"><div  class="triangle-border bottom blue"><div class="botsay">Hey!</div></div></div>
		<div class="dogspeech"><div  class="triangle-border bottom orange"><div class="usersay">&nbsp;</div></div></div>	
		<div class="man"></div>
		<div class="dog"></div>
	</div>
	<div class="clearthis"></div>
	
	<div class="centerthis">
		<form method="post" name="talkform" id="talkform" action="index.php">
			<p>
				<label>Say:</label>	
				<input type="text" name="say" id="say"/>
				<input type="submit" name="submit" id="submit" class="submit"  value="say" />
				<input type="hidden" name="convo_id" id="convo_id" value="<?php echo $convo_id;?>" />
				<input type="hidden" name="bot_id" id="bot_id" value="<?php echo $bot_id;?>" />
				<input type="hidden" name="format" id="format" value="<?php echo $format;?>" />
			</p>
		</form>
	</div>
	
	
	
</div>
	</body>
</html>

?>
