<?php
//-----------------------------------------------------------------------------------------------
//My Program-O Version 2.0.9
//Program-O  chatbot admin area
//Written by Elizabeth Perreau and Dave Morton
//Aug 2011
//for more information and support please visit www.program-o.com
//-----------------------------------------------------------------------------------------------
// wordcensor.php
  $msg = '';
  $upperScripts = <<<endScript

    <script type="text/javascript" src="scripts/tablesorter.js"></script>
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
  $form = print_r($_REQUEST, true);
  #die("<pre>\nRequest vars:\n\n$form\n</pre>\n");
  $group = (isset($_REQUEST['group'])) ? $_REQUEST['group'] : 1;
  $content  = $template->getSection('SearchWordCensorForm');
  $wc_action = isset($_REQUEST['action']) ? strtolower($_REQUEST['action']) : '';
  $wc_id = isset($_REQUEST['censor_id']) ? $_REQUEST['censor_id'] : -1;
  if (!empty($wc_action)) {
    switch($wc_action) {
      case 'search':
        $content .= runWordCensorSearch();
        $content .= wordCensorForm();
        break;
      case 'update':
        $x = updateWordCensor();
        $content .= wordCensorForm();
        break;
      case 'delete':
        #die("Delete called! id = $wc_id");
        $content .= ($wc_id >= 0) ? delWordCensor($wc_id) . wordCensorForm() : wordCensorForm();
        break;
      case 'edit':
        $content .= ($wc_id >= 0) ? editWordCensorForm($wc_id) : wordCensorForm();
        break;
      case 'add':
        $x = insertWordCensor();
        $content .= wordCensorForm();
        break;
      default:
        $content .= wordCensorForm();
    }
  }
  else {
    $content .= wordCensorForm();
  }
  $content = str_replace('[group]', $group, $content);

    $topNav        = $template->getSection('TopNav');
    $leftNav       = $template->getSection('LeftNav');
    $main          = $template->getSection('Main');
    $topNavLinks   = makeLinks('top', $topLinks, 12);
    $navHeader     = $template->getSection('NavHeader');
    $rightNav      = $template->getSection('RightNav');
    $leftNavLinks  = makeLinks('left', $leftLinks, 12);
    $rightNavLinks = getWordCensorWords();
    $FooterInfo    = getFooter();
    $errMsgClass   = (!empty($msg)) ? "ShowError" : "HideError";
    $errMsgStyle   = $template->getSection($errMsgClass);
    $noLeftNav     = '';
    $noTopNav      = '';
    $noRightNav    = '';
    $headerTitle   = 'Actions:';
    $pageTitle     = 'My-Program O - Word Censor Editor';
    $mainContent   = $content;
    $mainTitle     = 'Word Censor Editor';

    $mainContent = str_replace('[wordCensorForm]', wordCensorForm(), $mainContent);
    $rightNav    = str_replace('[rightNavLinks]', $rightNavLinks, $rightNav);
    $rightNav    = str_replace('[navHeader]', $navHeader, $rightNav);
    $rightNav    = str_replace('[headerTitle]', paginate(), $rightNav);

  function paginate() {
    $dbConn = db_open();
    $sql = "select count(*) from `wordcensor` where 1";
    $result = mysql_query($sql) or die('You have a SQL error on line '. __LINE__ . ' of ' . __FILE__ . '. Error message is: ' . mysql_error() . ".<br />\nSQL = $sql<br />\n");
    $row = mysql_fetch_array($result);
    $rowCount = $row[0];
    mysql_close($dbConn);
    $lastPage = intval($rowCount / 50);
    $remainder = ($rowCount / 50) - $lastPage;
    if ($remainder > 0) $lastPage++;
    #die ("Session values:<br />\n" . print_r($_SESSION, true) . "<br />\nCount = $count<br />\n");
    $out = "Censored Words<br />\n50 words per page:<br />\n";
    $link=" - <a class=\"paginate\" href=\"./?page=wordcensor&amp;group=[group]\">[label]</a>";
    $curStart = (isset($_GET['group'])) ? $_GET['group'] : 1;
    $firstPage = 1;
    $prev  = ($curStart > ($firstPage + 1)) ? $curStart - 1 : -1;
    $next = ($lastPage > ($curStart + 1)) ? $curStart + 1 : -1;
    $firstLink = ($firstPage != $curStart) ? str_replace('[group]', '1', $link) : '';
    $prevLink = ($prev > 0) ? str_replace('[group]', $prev, $link) : '';
    $curLink = "- $curStart ";
    if (empty($firstLink) and empty($prevLink)) $curLink = $curStart;
    $nextLink = ($next > 0) ? str_replace('[group]', $next, $link) : '';
    $lastLink = ($lastPage > $curStart) ? str_replace('[group]', $lastPage, $link) : '';
    $firstLink = str_replace('[label]', 'first', $firstLink);
    $prevLink = str_replace('[label]', '&lt;&lt;', $prevLink);
    $nextLink = str_replace('[label]', '&gt;&gt;', $nextLink);
    $lastLink = str_replace('[label]', 'last', $lastLink);
    $out .= ltrim("$firstLink\n$prevLink\n$curLink\n$nextLink\n$lastLink", " - ");
    return $out;
  }

  function getWordCensorWords() {
    global $template;
    # pagination variables
    $group = (isset($_GET['group'])) ? $_GET['group'] : 1;
    $_SESSION['poadmin']['group'] = $group;
    $startEntry = ($group - 1) * 50;
    $startEntry = ($startEntry < 0) ? 0 : $startEntry;
    $end = $group + 50;
    $_SESSION['poadmin']['page_start'] = $group;
    $dbconn = db_open();
    $curID = (isset($_GET['id'])) ? $_GET['id'] : -1;
    $sql = "select `censor_id`,`word_to_censor` from `wordcensor` where 1 order by abs(`censor_id`) asc limit $startEntry, 50;";
    #die ("SQL = $sql<br />\n");
    $baseLink = $template->getSection('NavLink');
    $links = '      <div class="userlist">' . "\n";
    $result = mysql_query($sql, $dbconn) or die('You have a SQL error on line '. __LINE__ . ' of ' . __FILE__ . '. Error message is: ' . mysql_error() . ".<br />\nSQL = $sql<br />\n");
    $count = 0;
    while ($row = mysql_fetch_assoc($result)) {
      $linkId = $row['censor_id'];
      $linkClass = ($linkId == $curID) ? 'selected' : 'noClass';
      $word_to_censor = $row['word_to_censor'];
      $tmpLink = str_replace('[linkClass]', " class=\"$linkClass\"", $baseLink);
      $linkHref = " href=\"./?page=wordcensor&amp;action=edit&amp;censor_id=$linkId&amp;group=$group#$linkId\" name=\"$linkId\"";
      $tmpLink = str_replace('[linkHref]', $linkHref, $tmpLink);
      $tmpLink = str_replace('[linkOnclick]', '', $tmpLink);
      $tmpLink = str_replace('[linkTitle]', " title=\"Edit spelling replace_with for the word '$word_to_censor'\"", $tmpLink);
      $tmpLink = str_replace('[linkLabel]', $word_to_censor, $tmpLink);
      $links .= "$tmpLink\n";
      $count++;
    }
    $page_count = intval($count / 50);
    $_SESSION['poadmin']['page_count'] = $page_count + (($count / 50) > $page_count) ? 1 : 0;
    $links .= "\n      </div>\n";
    return $links;
  }

function wordCensorForm() {
  global $template;
  $out = $template->getSection('WordCensorForm');
  $group = (isset($_GET['group'])) ? $_GET['group'] : 1;
  $out  = str_replace('[group]', $group, $out);
  return $out;
}

function insertWordCensor() {
    //global vars
    global $template, $msg;
    $dbconn = db_open();

    $replace_with = mysql_real_escape_string(trim($_POST['replace_with']));
    $word_to_censor = mysql_real_escape_string(trim($_POST['word_to_censor']));

    if(($replace_with == "") || ($word_to_censor == "")) {
        $msg = '        <div id="errMsg">You must enter a spelling mistake and the replace_with.</div>' . "\n";
    }
    else {
        $sql = "INSERT INTO `wordcensor` (`censor_id`, `word_to_censor`, `replace_with`, `bot_exclude`) VALUES (NULL,'$word_to_censor','$replace_with', '')";
        $result = mysql_query($sql,$dbconn) or die('You have a SQL error on line '. __LINE__ . ' of ' . __FILE__ . '. Error message is: ' . mysql_error() . ".<br />\nSQL = $sql<br />\n");

        if($result) {
            $msg = '<div id="successMsg">Correction added.</div>';
        }
        else {
            $msg = '<div id="errMsg">There was a problem editing the replace_with - no changes made.</div>';
        }
    }
    mysql_close($dbconn);

    return $msg;
}

function delWordCensor($id) {
    global $template, $msg;
    $dbconn = db_open();
    if($id=="") {
        $msg = '<div id="errMsg">There was a problem editing the replace_with - no changes made.</div>';
    }
    else {
        $sql = "DELETE FROM `wordcensor` WHERE `censor_id` = '$id' LIMIT 1";
        $result = mysql_query($sql,$dbconn)or die('You have a SQL error on line '. __LINE__ . ' of ' . __FILE__ . '. Error message is: ' . mysql_error() . ".<br />\nSQL = $sql<br />\n");
        if($result) {
            $msg = '<div id="successMsg">Correction deleted.</div>';
        }
        else {
            $msg = '<div id="errMsg">There was a problem editing the replace_with - no changes made.</div>';
        }
    }
    mysql_close($dbconn);
}


function runWordCensorSearch() {
    //global vars
    global $template;
    $dbconn = db_open();
    $i=0;
    $search = mysql_real_escape_string(trim($_POST['search']));
    $sql = "SELECT * FROM `wordcensor` WHERE `word_to_censor` LIKE '%$search%' OR `replace_with` LIKE '%$search%' LIMIT 50";
    $result = mysql_query($sql,$dbconn)or die('You have a SQL error on line '. __LINE__ . ' of ' . __FILE__ . '. Error message is: ' . mysql_error() . ".<br />\nSQL = $sql<br />\n");
    $htmltbl = '<table>
                  <thead>
                    <tr>
                      <th class="sortable">word_to_censor</th>
                      <th class="sortable">Correction</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                <tbody>';
    while($row=mysql_fetch_array($result)) {
        $i++;
        $word_to_censor = strtoupper($row['word_to_censor']);
        $replace_with = strtoupper($row['replace_with']);
        $id = $row['censor_id'];
        $group = round(($id / 50));
        $action = "<a href=\"./?page=wordcensor&amp;action=edit&amp;censor_id=$id&amp;group=$group#$id\"><img src=\"images/edit.png\" border=0 width=\"15\" height=\"15\" alt=\"Edit this entry\" title=\"Edit this entry\" /></a>
                    <a href=\"./?page=wordcensor&amp;action=delete&amp;censor_id=$id&amp;group=$group#$id\" onclick=\"return confirm('Do you really want to delete this entry? You will not be able to undo this!')\";><img src=\"images/del.png\" border=0 width=\"15\" height=\"15\" alt=\"Delete this entry\" title=\"Delete this entry\" /></a>";
        $htmltbl .= "<tr valign=top>
                            <td>$word_to_censor</td>
                            <td>$replace_with</td>
                            <td align=center>$action</td>
                        </tr>";
    }
    $htmltbl .= "</tbody></table>";

    if($i >= 50) {
        $msg = "Found more than 50 results for '<b>$search</b>', please refine your search further";
    }
    elseif($i == 0) {
        $msg = "Found 0 results for '<b>$search</b>'. You can use the form below to add that entry.";
        $htmltbl="";
    }
    else {
        $msg = "Found $i results for '<b>$search</b>'";
    }
    $htmlresults = "<div id=\"pTitle\">$msg</div>".$htmltbl;
    mysql_close($dbconn);
    return $htmlresults;
}

function editWordCensorForm($id) {
  //global vars
  global $template;
  $group = (isset($_GET['group'])) ? $_GET['group'] : 1;
  $form   = $template->getSection('EditWordCensorForm');
  $dbconn = db_open();
  $sql    = "SELECT * FROM `wordcensor` WHERE `censor_id` = '$id' LIMIT 1";
  $result = mysql_query($sql,$dbconn)or die('You have a SQL error on line '. __LINE__ . ' of ' . __FILE__ . '. Error message is: ' . mysql_error() . ".<br />\nSQL = $sql<br />\n");
  $row    = mysql_fetch_array($result);
  $form   = str_replace('[censor_id]', $row['censor_id'], $form);
  $form   = str_replace('[word_to_censor]', strtoupper($row['word_to_censor']), $form);
  $form   = str_replace('[replace_with]', strtoupper($row['replace_with']), $form);
  $form   = str_replace('[group]', $group, $form);
  mysql_close($dbconn);
  return $form;
}

function updateWordCensor() {
  //global vars
  global $template, $msg;
  $dbconn = db_open();
  $word_to_censor = mysql_real_escape_string(trim($_POST['word_to_censor']));
  $replace_with = mysql_real_escape_string(trim($_POST['replace_with']));
  $id = trim($_POST['id']);
  if(($id=="")||($word_to_censor=="")||($replace_with=="")) {
    $msg = '<div id="errMsg">There was a problem editing the replace_with - no changes made.</div>';
  }
  else {
    $sql = "UPDATE `wordcensor` SET `word_to_censor` = '$word_to_censor',`replace_with`='$replace_with' WHERE `censor_id`='$id' LIMIT 1";
    $result = mysql_query($sql,$dbconn)or die('You have a SQL error on line '. __LINE__ . ' of ' . __FILE__ . '. Error message is: ' . mysql_error() . ".<br />\nSQL = $sql<br />\n");
    if($result) {
      $msg = '<div id="successMsg">Correction edited.</div>';
    }
    else {
      $msg = '<div id="errMsg">There was a problem editing the replace_with - no changes made.</div>';
    }
  }
}

?>