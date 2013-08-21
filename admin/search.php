<?php
//-----------------------------------------------------------------------------------------------
//My Program-O Version 2.0.9
//Program-O  chatbot admin area
//Written by Elizabeth Perreau and Dave Morton
//Aug 2011
//for more information and support please visit www.program-o.com
//-----------------------------------------------------------------------------------------------
// search.php


  if((isset($_POST['action']))&&($_POST['action']=="search")) {
    $mainContent = $template->getSection('SearchAIMLForm');
    $mainContent .= runSearch();
  }
  elseif((isset($_POST['action']))&&($_POST['action']=="update")) {
    $mainContent = $template->getSection('SearchAIMLForm');
    $mainContent .= updateAIML();
  }
  elseif((isset($_GET['action']))&&($_GET['action']=="del")&&(isset($_GET['id']))&&($_GET['id']!="")) {
    $mainContent = $template->getSection('SearchAIMLForm');
    $mainContent .= delAIML($_GET['id']);
  }
  elseif((isset($_GET['action']))&&($_GET['action']=="edit")&&(isset($_GET['id']))&&($_GET['id']!="")) {
    $mainContent = $template->getSection('SearchAIMLForm');
    $mainContent .= editAIMLForm($_GET['id']);
  }
  else {
    $mainContent = $template->getSection('SearchAIMLForm');
  }

  $upperScripts = '<script type="text/javascript" src="scripts/tablesorter.js"></script>'."\n";
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
  $pageTitle     = 'My-Program O - Search/Edit AIML';
  #$mainContent   = 'This will eventually be the page for searching and/or editing AIML categories.';
  $mainTitle     = 'Search/Edit AIML';

  function delAIML($id) {
    
    $dbconn = db_open();
    if($id!="") {
      $sql = "DELETE FROM `aiml` WHERE `id` = '$id' LIMIT 1";
      $result = mysql_query($sql,$dbconn) or die('You have a SQL error on line '. __LINE__ . ' of ' . __FILE__ . '. Error message is: ' . mysql_error() . ".<br />\nSQL = $sql<br />\n");
      if(!$result) {
        $msg = 'Error AIML couldn\'t be deleted - no changes made.</div>';
      }
      else {
        $msg = 'AIML has been deleted.';
      }
    }
    else {
      $msg = 'Error AIML couldn\'t be deleted - no changes made.';
    }
    mysql_close($dbconn);
    return $msg;
  }


  function runSearch() {
    global $bot_id, $bot_name;
    $dbconn = db_open();
    $i=0;
    $searchTermsTemplate = " like '[value]' or\n  ";
    $searchTerms = '';
    $search_topic    = mysql_real_escape_string(trim($_POST['search_topic']));
    $search_filename = mysql_real_escape_string(trim($_POST['search_filename']));
    $search_pattern  = mysql_real_escape_string(trim($_POST['search_pattern']));
    $search_template = mysql_real_escape_string(trim($_POST['search_template']));
    $search_that     = mysql_real_escape_string(trim($_POST['search_that']));
    if(!empty($search_topic) or !empty($search_filename) or !empty($search_pattern) or !empty($search_template) or !empty($search_that)) {
      $sql = "SELECT * FROM `aiml` WHERE `bot_id` = '$bot_id'  AND (\n  [searchTerms]\n) LIMIT 50;";
      $searchTerms .= (!empty($search_topic)) ? '`topic`' . str_replace('[value]', $search_topic, $searchTermsTemplate) : '';
      $searchTerms .= (!empty($search_filename)) ? '`filename`' . str_replace('[value]', $search_filename, $searchTermsTemplate) : '';
      $searchTerms .= (!empty($search_pattern)) ? '`pattern`' . str_replace('[value]', $search_pattern, $searchTermsTemplate) : '';
      $searchTerms .= (!empty($search_template)) ? '`template`' . str_replace('[value]', $search_template, $searchTermsTemplate) : '';
      $searchTerms .= (!empty($search_that)) ? '`thatpattern`' . str_replace('[value]', $search_that, $searchTermsTemplate) : '';
      $searchTerms = rtrim($searchTerms, " or\n ");
      $sql = str_replace('[searchTerms]', $searchTerms, $sql);
      #die ("SQL = <pre>\n$sql\n</pre>\n");
      $result = mysql_query($sql,$dbconn) or die('You have a SQL error on line '. __LINE__ . ' of ' . __FILE__ . '. Error message is: ' . mysql_error() . ".<br />\nSQL = $sql<br />\n");
      $htmltbl = <<<endtHead
          <table width="99%" border="1" cellpadding="1" cellspacing="1">
            <thead>
              <tr>
                <th class="sortable">Topic</th>
                <th class="sortable">Previous Bot Response</th>
                <th class="sortable">User Input</th>
                <th class="sortable">Bot Response</th>
                <th class="sortable">Filename</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
endtHead;
      while($row=mysql_fetch_array($result)) {
        $i++;
        $topic = $row['topic'];
        $pattern = $row['pattern'];
        $thatpattern = $row['thatpattern'];
        $template = htmlentities($row['template']);
        $filename = $row['filename'];
        $id = $row['id'];
        $action = <<<endLink
          <a href="./?page=search&amp;action=edit&amp;id=$id">
            <img src="images/edit.png" border=0 width="15" height="15" alt="Edit this entry" title="Edit this entry" />
          </a>
          <a href="./?page=search&amp;action=del&amp;id=$id" onclick="return confirm('Do you really want to delete this AIML record? You will not be able to undo this!')";>
            <img src="images/del.png" border=0 width="15" height="15" alt="Delete this entry" title="Delete this entry" />
          </a>
endLink;

      $htmltbl .= <<<endRow
            <tr valign=top>
              <td>$topic</td>
              <td>$thatpattern</td>
              <td>$pattern</td>
              <td>$template</td>
              <td>$filename</td>
              <td align=center>$action</td>
            </tr>
endRow;
    }
      mysql_close($dbconn);
      $htmltbl .= "          </tbody>\n        </table>";
      if($i == 50) {
        $msg = "Found more than 50 results for your specified search terms. please refine your search further";
      }
      elseif($i == 0) {
        $msg = "Found 0 results for your specified search terms. please try again";
        $htmltbl="";
      }
      else {
        $msg = "Found $i results for your specified search terms.";
      }
      $htmlresults = "<div id=\"pTitle\">$msg</div>".$htmltbl;
    }
    else {
      $htmlresults =  'Please enter a search term in any one of the available search boxes.';
    }
    return $htmlresults;
  }


  function editAIMLForm($id) {
    //db globals
    global $template;
    $dbconn = db_open();
    $sql = "SELECT * FROM `aiml` WHERE `id` = '$id' LIMIT 1";
    $result = mysql_query($sql,$dbconn) or die('You have a SQL error on line '. __LINE__ . ' of ' . __FILE__ . '. Error message is: ' . mysql_error() . ".<br />\nSQL = $sql<br />\n");
    $row=mysql_fetch_array($result);
    $topic = $row['topic'];
    $pattern = $row['pattern'];
    $thatpattern = $row['thatpattern'];
    $row_template = htmlentities($row['template']);
    $filename = $row['filename'];
    $id = $row['id'];
    $form = $template->getSection('EditAIMLForm');
    $form = str_replace('[id]', $id, $form);
    $form = str_replace('[topic]', $topic, $form);
    $form = str_replace('[pattern]', $pattern, $form);
    $form = str_replace('[thatpattern]', $thatpattern, $form);
    $form = str_replace('[template]', $row_template, $form);
    $form = str_replace('[filename]', $filename, $form);
    mysql_close($dbconn);
    return $form;
  }

  function updateAIML() {
  //db globals
    $dbconn = db_open();
    $template = mysql_real_escape_string(trim($_POST['template']));
    $filename = mysql_real_escape_string(trim($_POST['filename']));
    $pattern = strtoupper(mysql_real_escape_string(trim($_POST['pattern'])));
    $thatpattern = strtoupper(mysql_real_escape_string(trim($_POST['thatpattern'])));
    $topic = strtoupper(mysql_real_escape_string(trim($_POST['topic'])));
    $id = trim($_POST['id']);
    if(($template == "")||($pattern== "")||($id=="")) {
      $msg =  'Please make sure you have entered a user input and bot response ';
    }
    else {
      $sql = "UPDATE `aiml` SET `pattern` = '$pattern',`thatpattern`='$thatpattern',`template`='$template',`topic`='$topic',`filename`='$filename' WHERE `id`='$id' LIMIT 1";
      $result = mysql_query($sql,$dbconn) or die('You have a SQL error on line '. __LINE__ . ' of ' . __FILE__ . '. Error message is: ' . mysql_error() . ".<br />\nSQL = $sql<br />\n");
      if($result) {
        $msg =  'AIML Updated.';
      }
      else {
        $msg =  'There was an error updating the AIML - no changes made.';
      }
    }
    mysql_close($dbconn);
    return $msg;
  }

?>