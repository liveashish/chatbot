<?php
//-----------------------------------------------------------------------------------------------
//My Program-O Version 2.0.9
//Program-O  chatbot admin area
//Written by Elizabeth Perreau and Dave Morton
//Aug 2011
//for more information and support please visit www.program-o.com
//-----------------------------------------------------------------------------------------------
// stats.php

  $oneday      = getStats("today");
  $oneweek     = getStats("-1 week");
  $onemonth    = getStats("-1 month");
  $sixmonths   = getStats("-6 month");
  $oneyear     = getStats("1 year ago");
  $alltime     = getStats("all");

  $singlelines = getChatLines(1,1);
  $alines      = getChatLines(1,25);
  $blines      = getChatLines(26,50);
  $clines      = getChatLines(51,100);
  $dlines      = getChatLines(101,1000000);
  $avg         = getChatLines("average",1000000);


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
  $pageTitle     = 'My-Program O - Bot Stats';
  $mainContent   = $template->getSection('StatsPage');
  $mainTitle     = 'Bot Statistics for ' . $bot_name;

  $mainContent = str_replace('[oneday]', $oneday, $mainContent);
  $mainContent = str_replace('[oneweek]', $oneweek, $mainContent);
  $mainContent = str_replace('[onemonth]', $onemonth, $mainContent);
  $mainContent = str_replace('[sixmonths]', $sixmonths, $mainContent);
  $mainContent = str_replace('[oneyear]', $oneyear, $mainContent);
  $mainContent = str_replace('[alltime]', $alltime, $mainContent);
  $mainContent = str_replace('[singlelines]', $singlelines, $mainContent);
  $mainContent = str_replace('[alines]', $alines, $mainContent);
  $mainContent = str_replace('[blines]', $blines, $mainContent);
  $mainContent = str_replace('[clines]', $clines, $mainContent);
  $mainContent = str_replace('[dlines]', $dlines, $mainContent);
  $mainContent = str_replace('[avg]', $avg, $mainContent);

function getStats($interval) {
	global $bot_id;
	$dbconn = db_open();
	if($interval!="all") {
		$intervaldate =  date("Y-m-d", strtotime($interval));
		$sqladd = " AND date(timestamp) >= '$intervaldate'";
	}
	else {
		$sqladd ="";
	}
	//get undefined defaults from the db
	$sql = "SELECT count(distinct(`userid`)) AS TOT FROM `conversation_log` WHERE bot_id = '$bot_id' $sqladd";
	$result = mysql_query($sql,$dbconn) or die('You have a SQL error on line '. __LINE__ . ' of ' . __FILE__ . '. Error message is: ' . mysql_error() . ".<br />\nSQL = $sql<br />\n");
	$row = mysql_fetch_assoc($result);
	$res = $row['TOT'];
	return $res;
}

function getChatLines($i,$j) {
  global $bot_id;
	$dbconn = db_open();
		$sql = <<<endSQL
SELECT AVG(`chatlines`) AS TOT
				FROM `users`
				INNER JOIN `conversation_log` ON `users`.`id` = `conversation_log`.`userid`
				WHERE `conversation_log`.`bot_id` = $bot_id AND [endCondition];
endSQL;
	if($i=="average") {
	  $endCondition = '`chatlines` != 0;';
	}
	else {
		$endCondition = "(`chatlines` >= $i AND `chatlines` <= $j)";
	}
  $sql = str_replace('[endCondition]', $endCondition, $sql);
	//get undefined defaults from the db
	$result = mysql_query($sql,$dbconn) or die('You have a SQL error on line '. __LINE__ . ' of ' . __FILE__ . '. Error message is: ' . mysql_error() . ".<br />\nSQL = $sql<br />\n");
	$row = mysql_fetch_assoc($result);
	$res = $row['TOT'];
	return $res;
}











?>