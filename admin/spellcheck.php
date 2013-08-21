<?php
//-----------------------------------------------------------------------------------------------
//My Program-O Version 2.0.9
//Program-O  chatbot admin area
//Written by Elizabeth Perreau and Dave Morton
//Aug 2011
//for more information and support please visit www.program-o.com
//-----------------------------------------------------------------------------------------------
// spellcheck.php
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

  $group = (isset($_GET['group'])) ? $_GET['group'] : 1;
  $content  = $template->getSection('SearchSpellForm');
  $sc_action = isset($_REQUEST['action']) ? strtolower($_REQUEST['action']) : '';
  $sc_id = isset($_REQUEST['id']) ? $_REQUEST['id'] : -1;
  if (!empty($sc_action)) {
    switch($sc_action) {
      case 'search':
        $content .= runSpellSearch();
        $content .= spellCheckForm();
        break;
      case 'update':
        $x = updateSpell();
        $content .= spellCheckForm();
        break;
      case 'delete':
        $content .= ($sc_id >= 0) ? delSpell($sc_id) . spellCheckForm() : spellCheckForm();
        break;
      case 'edit':
        $content .= ($sc_id >= 0) ? editSpellForm($sc_id) : spellCheckForm();
        break;
      case 'add':
        $x = insertSpell();
        $content .= spellCheckForm();
        break;
      default:
        $content .= spellCheckForm();
    }
  }
  else {
    $content .= spellCheckForm();
  }
  $content = str_replace('[group]', $group, $content);

    $topNav        = $template->getSection('TopNav');
    $leftNav       = $template->getSection('LeftNav');
    $main          = $template->getSection('Main');
    $topNavLinks   = makeLinks('top', $topLinks, 12);
    $navHeader     = $template->getSection('NavHeader');
    $rightNav      = $template->getSection('RightNav');
    $leftNavLinks  = makeLinks('left', $leftLinks, 12);
    $rightNavLinks = getMisspelledWords();
    $FooterInfo    = getFooter();
    $errMsgClass   = (!empty($msg)) ? "ShowError" : "HideError";
    $errMsgStyle   = $template->getSection($errMsgClass);
    $noLeftNav     = '';
    $noTopNav      = '';
    $noRightNav    = '';
    $headerTitle   = 'Actions:';
    $pageTitle     = 'My-Program O - Spellcheck Editor';
    $mainContent   = $content;
    $mainTitle     = 'Spellcheck Editor';

    $mainContent = str_replace('[spellCheckForm]', spellCheckForm(), $mainContent);
    $rightNav    = str_replace('[rightNavLinks]', $rightNavLinks, $rightNav);
    $rightNav    = str_replace('[navHeader]', $navHeader, $rightNav);
    $rightNav    = str_replace('[headerTitle]', paginate(), $rightNav);

  function paginate() {
    $dbConn = db_open();
    $sql = "select count(*) from `spellcheck` where 1";
    $result = mysql_query($sql) or die('You have a SQL error on line '. __LINE__ . ' of ' . __FILE__ . '. Error message is: ' . mysql_error() . ".<br />\nSQL = $sql<br />\n");
    $row = mysql_fetch_array($result);
    $rowCount = $row[0];
    mysql_close($dbConn);
    $lastPage = intval($rowCount / 50);
    $remainder = ($rowCount / 50) - $lastPage;
    if ($remainder > 0) $lastPage++;
    #die ("Session values:<br />\n" . print_r($_SESSION, true) . "<br />\nCount = $count<br />\n");
    $out = "Missspelled Words<br />\n50 words per page:<br />\n";
    $link=" - <a class=\"paginate\" href=\"./?page=spellcheck&amp;group=[group]\">[label]</a>";
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

  function getMisspelledWords() {
    global $template;
    # pagination variables
    $group = (isset($_GET['group'])) ? $_GET['group'] : 1;
    $_SESSION['poadmin']['group'] = $group;
    $startEntry = ($group - 1) * 50;
    $end = $group + 50;
    $_SESSION['poadmin']['page_start'] = $group;
    $dbconn = db_open();
    $curID = (isset($_GET['id'])) ? $_GET['id'] : -1;
    $sql = "select `id`,`missspelling` from `spellcheck` where 1 order by abs(`id`) asc limit $startEntry, 50;";
    #die ("SQL = $sql<br />\n");
    $baseLink = $template->getSection('NavLink');
    $links = '      <div class="userlist">' . "\n";
    $result = mysql_query($sql, $dbconn) or die('You have a SQL error on line '. __LINE__ . ' of ' . __FILE__ . '. Error message is: ' . mysql_error() . ".<br />\nSQL = $sql<br />\n");
    $count = 0;
    while ($row = mysql_fetch_assoc($result)) {
      $linkId = $row['id'];
      $linkClass = ($linkId == $curID) ? 'selected' : 'noClass';
      $missspelling = $row['missspelling'];
      $tmpLink = str_replace('[linkClass]', " class=\"$linkClass\"", $baseLink);
      $linkHref = " href=\"./?page=spellcheck&amp;action=edit&amp;id=$linkId&amp;group=$group#$linkId\" name=\"$linkId\"";
      $tmpLink = str_replace('[linkHref]', $linkHref, $tmpLink);
      $tmpLink = str_replace('[linkOnclick]', '', $tmpLink);
      $tmpLink = str_replace('[linkTitle]', " title=\"Edit spelling correction for the word '$missspelling'\"", $tmpLink);
      $tmpLink = str_replace('[linkLabel]', $missspelling, $tmpLink);
      $links .= "$tmpLink\n";
      $count++;
    }
    $page_count = intval($count / 50);
    $_SESSION['poadmin']['page_count'] = $page_count + (($count / 50) > $page_count) ? 1 : 0;
    $links .= "\n      </div>\n";
    return $links;
  }

function spellCheckForm() {
  global $template;
  $out = $template->getSection('SpellcheckForm');
  $group = (isset($_GET['group'])) ? $_GET['group'] : 1;
  $out  = str_replace('[group]', $group, $out);
  return $out;
}

function insertSpell() {
    //global vars
    global $template, $msg;
    $dbconn = db_open();

    $correction = mysql_real_escape_string(trim($_POST['correction']));
    $missspell = mysql_real_escape_string(trim($_POST['missspell']));

    if(($correction == "") || ($missspell == "")) {
        $msg = '        <div id="errMsg">You must enter a spelling mistake and the correction.</div>' . "\n";
    }
    else {
        $sql = "INSERT INTO `spellcheck` VALUES (NULL,'$missspell','$correction')";
        $result = mysql_query($sql,$dbconn) or die('You have a SQL error on line '. __LINE__ . ' of ' . __FILE__ . '. Error message is: ' . mysql_error() . ".<br />\nSQL = $sql<br />\n");

        if($result) {
            $msg = '<div id="successMsg">Correction added.</div>';
        }
        else {
            $msg = '<div id="errMsg">There was a problem editing the correction - no changes made.</div>';
        }
    }
    mysql_close($dbconn);

    return $msg;
}

function delSpell($id) {
    global $template, $msg;
    $dbconn = db_open();
    if($id=="") {
        $msg = '<div id="errMsg">There was a problem editing the correction - no changes made.</div>';
    }
    else {
        $sql = "DELETE FROM `spellcheck` WHERE `id` = '$id' LIMIT 1";
        $result = mysql_query($sql,$dbconn)or die('You have a SQL error on line '. __LINE__ . ' of ' . __FILE__ . '. Error message is: ' . mysql_error() . ".<br />\nSQL = $sql<br />\n");
        if($result) {
            $msg = '<div id="successMsg">Correction deleted.</div>';
        }
        else {
            $msg = '<div id="errMsg">There was a problem editing the correction - no changes made.</div>';
        }
    }
    mysql_close($dbconn);
}


function runSpellSearch() {
    //global vars
    global $template;
    $dbconn = db_open();
    $i=0;
    $search = mysql_real_escape_string(trim($_POST['search']));
    $sql = "SELECT * FROM `spellcheck` WHERE `missspelling` LIKE '%$search%' OR `correction` LIKE '%$search%' LIMIT 50";
    $result = mysql_query($sql,$dbconn)or die('You have a SQL error on line '. __LINE__ . ' of ' . __FILE__ . '. Error message is: ' . mysql_error() . ".<br />\nSQL = $sql<br />\n");
    $htmltbl = '<table>
                  <thead>
                    <tr>
                      <th class="sortable">missspelling</th>
                      <th class="sortable">Correction</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                <tbody>';
    while($row=mysql_fetch_array($result)) {
        $i++;
        $misspell = strtoupper($row['missspelling']);
        $correction = strtoupper($row['correction']);
        $id = $row['id'];
        $group = round(($id / 50));
        $action = "<a href=\"./?page=spellcheck&amp;action=edit&amp;id=$id&amp;group=$group#$id\"><img src=\"images/edit.png\" border=0 width=\"15\" height=\"15\" alt=\"Edit this entry\" title=\"Edit this entry\" /></a>
                    <a href=\"./?page=spellcheck&amp;action=del&amp;id=$id&amp;group=$group#$id\" onclick=\"return confirm('Do you really want to delete this missspelling? You will not be able to undo this!')\";><img src=\"images/del.png\" border=0 width=\"15\" height=\"15\" alt=\"Edit this entry\" title=\"Edit this entry\" /></a>";
        $htmltbl .= "<tr valign=top>
                            <td>$misspell</td>
                            <td>$correction</td>
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

function editSpellForm($id) {
  //global vars
  global $template;
  $group = (isset($_GET['group'])) ? $_GET['group'] : 1;
  $form   = $template->getSection('EditSpellForm');
  $dbconn = db_open();
  $sql    = "SELECT * FROM `spellcheck` WHERE `id` = '$id' LIMIT 1";
  $result = mysql_query($sql,$dbconn)or die('You have a SQL error on line '. __LINE__ . ' of ' . __FILE__ . '. Error message is: ' . mysql_error() . ".<br />\nSQL = $sql<br />\n");
  $row    = mysql_fetch_array($result);
  $form   = str_replace('[id]', $row['id'], $form);
  $form   = str_replace('[missspelling]', strtoupper($row['missspelling']), $form);
  $form   = str_replace('[correction]', strtoupper($row['correction']), $form);
  $form   = str_replace('[group]', $group, $form);
  mysql_close($dbconn);
  return $form;
}

function updateSpell() {
  //global vars
  global $template, $msg;
  $dbconn = db_open();
  $missspelling = mysql_real_escape_string(trim($_POST['missspelling']));
  $correction = mysql_real_escape_string(trim($_POST['correction']));
  $id = trim($_POST['id']);
  if(($id=="")||($missspelling=="")||($correction=="")) {
    $msg = '<div id="errMsg">There was a problem editing the correction - no changes made.</div>';
  }
  else {
    $sql = "UPDATE `spellcheck` SET `missspelling` = '$missspelling',`correction`='$correction' WHERE `id`='$id' LIMIT 1";
    $result = mysql_query($sql,$dbconn)or die('You have a SQL error on line '. __LINE__ . ' of ' . __FILE__ . '. Error message is: ' . mysql_error() . ".<br />\nSQL = $sql<br />\n");
    if($result) {
      $msg = '<div id="successMsg">Correction edited.</div>';
    }
    else {
      $msg = '<div id="errMsg">There was a problem editing the correction - no changes made.</div>';
    }
  }
}

?>