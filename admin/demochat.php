<?php
//-----------------------------------------------------------------------------------------------
//My Program-O Version 2.0.9
//Program-O  chatbot admin area
//Written by Elizabeth Perreau and Dave Morton
//Aug 2011
//for more information and support please visit www.program-o.com
//-----------------------------------------------------------------------------------------------
// demochat.php
  $upperScripts  = '';
  $topNav        = $template->getSection('TopNav');
  $leftNav       = $template->getSection('LeftNav');
  $main          = $template->getSection('Main');
  $topNavLinks   = makeLinks('top', $topLinks, 12);
  $navHeader     = $template->getSection('NavHeader');
  $leftNavLinks  = makeLinks('left', $leftLinks, 12);
  $FooterInfo    = getFooter();
  $errMsgClass   = (!empty($msg)) ? "ShowError" : "HideError";
  $errMsgStyle   = $template->getSection($errMsgClass);
  $noLeftNav     = '';
  $noTopNav      = '';
  $noRightNav    = $template->getSection('NoRightNav');
  $headerTitle   = 'Actions:';
  $pageTitle     = 'My-Program O - Chat Demo';
  $mainContent   = 'This will eventually be the page for the chat demo.';
  $mainContent   = showChatFrame();
  $mainTitle     = 'Chat Demo';

  function showChatFrame() {
    global $template, $bot_name, $bot_id;
    $dbconn = db_open();
    $sql = "select `format` from `bots` where `bot_id` = $bot_id limit 1;";
    $result = mysql_query($sql, $dbconn)  or die('You have a SQL error on line '. __LINE__ . ' of ' . __FILE__ . '. Error message is: ' . mysql_error() . ".<br />\nSQL = $sql<br />\n");
    $row = mysql_fetch_assoc($result);
    $format = strtolower($row['format']);
    switch ($format) {
      case "html":
        $url = '../gui/plain/';
        break;
      case "json":
        $url = '../gui/jquery/';
        break;
      case "xml":
        $url = '../gui/xml/';
        break;
    }
    $out = $template->getSection('ChatDemo');
    $out = str_replace('[pageSource]', $url, $out);
    $out = str_replace('[format]', strtoupper($format), $out);
    return $out;
  }
?>