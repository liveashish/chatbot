<?php
//-----------------------------------------------------------------------------------------------
//My Program-O Version 2.0.9
//Program-O  chatbot admin area
//Written by Elizabeth Perreau and Dave Morton
//Aug 2011
//for more information and support please visit www.program-o.com
//-----------------------------------------------------------------------------------------------
// logs.php

$show = (isset($_GET['showing'])) ? $_GET['showing'] : "last 20";//showThis($show)
$show_this = showThis($show);
$convo = (isset($_GET['id'])) ? getuserConvo($_GET['id'],$show) : "Please select a conversation from the side bar.";
$user_list = (isset($_GET['id'])) ? getuserList($_GET['id'],$show) : getuserList($_SESSION['poadmin']['bot_id'],$show);
$bot_name = (isset($_SESSION['poadmin']['bot_name'])) ? $_SESSION['poadmin']['bot_name'] : 'unknown';
$upperScripts = <<<endScript

    <script type="text/javascript">
<!--
      var state = 'hidden';
      function showhide(layer_ref) {
        if (state == 'visible') {
          state = 'hidden';
        }
        else {
          state = 'visible';
        }
        if (document.all) { //IS IE 4 or 5 (or 6 beta)
          eval( "document.all." + layer_ref + ".style.visibility = state");
        }
        if (document.layers) { //IS NETSCAPE 4 or below
          document.layers[layer_ref].visibility = state;
        }
        if (document.getElementById && !document.all) {
          maxwell_smart = document.getElementById(layer_ref);
          maxwell_smart.style.visibility = state;
        }
      }
//-->
    </script>
endScript;

    $topNav        = $template->getSection('TopNav');
    $leftNav       = $template->getSection('LeftNav');
    $main          = $template->getSection('Main');
    $topNavLinks   = makeLinks('top', $topLinks, 12);
    $leftNavLinks  = makeLinks('left', $leftLinks, 12);
    $FooterInfo    = getFooter();
    $errMsgClass   = (!empty($msg)) ? "ShowError" : "HideError";
    $errMsgStyle   = $template->getSection($errMsgClass);
    $rightNav       = $template->getSection('RightNav');
    $navHeader     = $template->getSection('NavHeader');
    $noLeftNav     = '';
    $noTopNav      = '';
    $noRightNav    = '';
    $headerTitle   = 'Actions:';
    $pageTitle     = 'My-Program O - Chat Logs';
    $mainContent   = $template->getSection('ConversationLogs1');
    $mainTitle     = 'Chat Logs';

    $rightNav    = str_replace('[rightNavLinks]', $show_this . $user_list, $rightNav);
    $rightNav    = str_replace('[navHeader]', $navHeader, $rightNav);
    $rightNav    = str_replace('[headerTitle]', 'Log Actions:', $rightNav);
    $mainContent = str_replace('[show_this]', '', $mainContent);
    $mainContent = str_replace('[convo]', $convo, $mainContent);
    $mainContent = str_replace('[bot_name]', $bot_name, $mainContent);

function getUserNames() {
  $dbconn = db_open();
  $sql = "select `id`, `name` from `users` where 1;";
  $result = mysql_query($sql,$dbconn);
  $nameList = array();
  while ($row = mysql_fetch_assoc($result)) {
    $nameList[$row['id']] = $row['name'];
  }
  mysql_close($dbconn);
  return $nameList;
}

function getuserList($showing) {
  //db globals
  global $template;
  $nameList = getUserNames();
  $curUserid = (isset($_GET['id'])) ? $_GET['id'] : -1;
  #die ("user names:<br />\n" . print_r($nameList, true) . "\n<br />\n");
  $dbconn = db_open();
  $bot_id = $_SESSION['poadmin']['bot_id'];
  $linkTag = $template->getSection('NavLink');
  $sql = "SELECT DISTINCT(`userid`),COUNT(`userid`) AS TOT FROM `conversation_log`  WHERE bot_id = '$bot_id' AND DATE(`timestamp`) = '[repl_date]' GROUP BY `userid` ORDER BY ABS(`userid`) ASC";
  $showarray = array("last 20","previous week","previous 2 weeks","previous month","last 6 months","this year","previous year","all years");
  switch ($showing) {
    case "today":
      $repl_date = date("Y-m-d");
      break;
    case "previous week":
      $repl_date = strtotime("-1 week");
      break;
    case "previous 2 weeks":
      $repl_date = strtotime("-2 week");
      break;
    case "previous month":
      $repl_date = strtotime("-1 month");
      break;
    case "previous 6 months":
      $repl_date = strtotime("-6 month");
      break;
    case "past 12 months":
      $repl_date = strtotime("-1 year");
      break;
    case "all time":
      $sql = "SELECT DISTINCT(`userid`),COUNT(`userid`) AS TOT FROM `conversation_log`  WHERE  bot_id = '$bot_id' GROUP BY `userid` ORDER BY ABS(`userid`) ASC";
      $repl_date = time();
      break;
    default:
      $sql = "SELECT DISTINCT(`userid`),COUNT(`userid`) AS TOT FROM `conversation_log`  WHERE  bot_id = '$bot_id' GROUP BY `userid` ORDER BY ABS(`userid`) ASC";
      $repl_date = time();
  }
  $sql = str_replace('[repl_date]', $repl_date, $sql);
  $list =<<<endList

      <div class="userlist">
        <ul>

endList;
  $result = mysql_query($sql,$dbconn) or die('You have a SQL error on line '. __LINE__ . ' of ' . __FILE__ . '. Error message is: ' . mysql_error() . ".<br />\nSQL = $sql<br />\n");
  while($row = mysql_fetch_array($result)) {
    $userid = $row['userid'];
    $linkClass = ($userid == $curUserid) ? 'selected' : 'noClass';
    $userName = @$nameList[$userid];
    $TOT = $row['TOT'];
    $tmpLink = str_replace('[linkClass]'," class=\"$linkClass\"", $linkTag);
    $tmpLink = str_replace('[linkOnclick]','', $tmpLink);
    $tmpLink = str_replace('[linkHref]',"href=\"./?page=logs&showing=$showing&id=$userid#$userid\" name=\"$userid\"", $tmpLink);
    $tmpLink = str_replace('[linkTitle]'," title=\"Show entries for user $userName\"", $tmpLink);
    $tmpLink = str_replace('[linkLabel]',"USER:$userName($TOT)", $tmpLink);
    $anchor = "            <a name=\"$userid\" />\n";
    $anchor = '';
    $list .= "$tmpLink\n$anchor";
  }
  $list .="\n       </div>\n";
  mysql_close($dbconn);
  return $list;
}

function showThis($showing="last 20") {
  $showarray = array("last 20","today","previous week","previous 2 weeks","previous month","last 6 months","past 12 months","all time");
  $options = "";
  foreach($showarray as $index => $value) {
    if($value == $showing) {
      $sel = " SELECTED=SELECTED";
    }
    else {
      $sel = "";
    }
    $options .= "          <option value=\"$value\"$sel>$value</option>\n";
  }

  $form = <<<endForm
        <form name="showthis" method="post" action="./?page=logs">
          <select name="showing" id="showing">
$options
          </select>
        <input type="submit" id="submit" name="submit" value="show">
      </form>
endForm;
  return $form;
}

function getuserConvo($id, $showing) {
  $bot_name= (isset($_SESSION['poadmin']['bot_name'])) ? $_SESSION['poadmin']['bot_name'] : 'Bot';
  $bot_id = (isset($_SESSION['poadmin']['bot_id'])) ? $_SESSION['poadmin']['bot_id'] : 0;
  $nameList = getUserNames();
  $user_name = $nameList[$id];
  switch ($showing) {
    case "today":
      $sqladd = "AND DATE(`timestamp`) = '".date('Y-m-d')."'";
      $title = "Today's ";
      break;
    case "previous week":
      $lastweek = strtotime("-1 week");
      $sqladd = "AND DATE(`timestamp`) >= '".$lastweek."'";
      $title = "Last week's ";
      break;
    case "previous 2 weeks":
      $lasttwoweek = strtotime("-2 week");
      $sqladd = "AND DATE(`timestamp`) >= '".$lasttwoweek ."'";
      $title = "Last two week's ";
      break;
    case "previous month":
      $lastmonth = strtotime("-1 month");
      $sqladd = "AND DATE(`timestamp`) >= '".$lastmonth ."'";
      $title = "Last month's ";
      break;
    case "previous 6 months":
      $lastsixmonth = strtotime("-6 month");
      $sqladd = "AND DATE(`timestamp`) >= '".$lastsixmonth ."'";
      $title = "Last six month's ";
      break;
    case "past 12 months":
      $lastyear = strtotime("-1 year");
      $sqladd = "AND DATE(`timestamp`) >= '".$lastyear ."'";
      $title = "Last twelve month's ";
      break;
    case "all time":
      $sql = "";
      $title = "All ";
      break;
    default:
      $sqladd = "";
      $title = "Last ";
  }
  $lasttimestamp = "";
  $i = 1;
  $dbconn = db_open();
  //get undefined defaults from the db
  $sql = "SELECT *  FROM `conversation_log` WHERE `bot_id` = '$bot_id' AND `userid` = $id $sqladd ORDER BY `id` ASC";
  $list = "<hr><br/><h4>$title conversations for user: $id</h4>";
  $list .="<div class=\"convolist\">";
  $result = mysql_query($sql,$dbconn)or die('You have a SQL error on line '. __LINE__ . ' of ' . __FILE__ . '. Error message is: ' . mysql_error() . ".<br />\nSQL = $sql\n");
  while($row = mysql_fetch_array($result)) {
    $thisdate = date("Y-m-d",strtotime($row['timestamp']));
    if($thisdate!=$lasttimestamp) {
      if($i>1) {
        if($showing == "last 20") {
            break;
        }
      }
      $date = date("Y-m-d");
      $list .= "<hr><br/><h4>Conversation#$i $thisdate</h4>";
      $i++;
    }
    $list .= "<br><span style=\"color:DARKBLUE;\">$user_name: ".$row['input']."</span>";
    $list .= "<br><span style=\"color:GREEN;\">$bot_name: ".$row['response']."</span>";
    $lasttimestamp = $thisdate;
  }
  $list .="</div>";
  mysql_close($dbconn);
  $list = str_ireplace('<script', '&lt;script', $list);
  return $list;
}
?>
