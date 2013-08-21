<?PHP
//-----------------------------------------------------------------------------------------------
//My Program-O Version 2.0.9
//Program-O  chatbot admin area
//Written by Elizabeth Perreau and Dave Morton
//Aug 2011
//for more information and support please visit www.program-o.com
//-----------------------------------------------------------------------------------------------
// select_bots.php

$content ="";

  $upperScripts = <<<endScript

    <script type="text/javascript">
<!--
      function showMe() {
        var sh = document.getElementById('showHelp');
        var tf = document.getElementById('clearForm');
        sh.style.display = 'block';
        tf.style.display = 'none';
      }
      function hideMe() {
        var sh = document.getElementById('showHelp');
        var tf = document.getElementById('clearForm');
        sh.style.display = 'none';
        tf.style.display = 'block';
      }
      function showHide() {
        var display = document.getElementById('showHelp').style.display;
        switch (display) {
          case '':
          case 'none':
            return showMe();
            break;
          case 'block':
            return hideMe();
            break;
          default:
            alert('display = ' + display);
        }
      }
//-->
    </script>
endScript;


if((isset($_POST['action']))&&($_POST['action']=="clear")) {
  $content .= clearAIML();
}
elseif((isset($_POST['clearFile']))&&($_POST['clearFile'] != "null")) {
  $content .= clearAIMLByFileName($_POST['clearFile']);
}
else {
}
    $content .= renderMain();

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
    $pageTitle     = "My-Program O - Clear AIML Categories";
    $mainContent   = $content;
    $mainTitle     = "Clear AIML Categories for the bot named $bot_name [helpLink]";
    $showHelp = $template->getSection('ClearShowHelp');

    $mainTitle     = str_replace('[helpLink]', $template->getSection('HelpLink'), $mainTitle);
    $mainContent   = str_replace('[showHelp]', $showHelp, $mainContent);
    $mainContent     = str_replace('[upperScripts]', $upperScripts, $mainContent);
  function replaceTags(&$content) {
    return $content;
  }

  function clearAIML() {
    global $dbn, $bot_id, $bot_name;
    $dbconn = db_open();

    $sql = "DELETE FROM `aiml` WHERE `bot_id` = $bot_id;";
    #return "SQL = $sql";
    $result = mysql_query($sql,$dbconn) or die(mysql_error());
    mysql_close($dbconn);
    $msg = "<strong>All AIML categories cleared for $bot_name!</strong><br />";
    return $msg;
  }

  function clearAIMLByFileName($filename) {
    global $dbn, $bot_id;
    $dbconn = db_open();
    $cleanedFilename = mysql_real_escape_string($filename, $dbconn);
    $sql = "delete from `aiml` where `filename` like '$cleanedFilename' and `bot_id` = $bot_id;";
    #return "SQL = $sql";
    $result = mysql_query($sql,$dbconn) or die(mysql_error());
    mysql_close($dbconn);
    $msg = "<br/><strong>AIML categories cleared for file $filename!</strong><br />";
    return $msg;
  }

  function getSelOpts() {
    global $dbn, $bot_id, $msg;
    $out = "                  <!-- Start Selectbox Options -->\n";
    $dbconn = db_open();
    $optionTemplate = "                  <option value=\"[val]\">[val]</option>\n";
    $sql = "SELECT DISTINCT filename FROM `aiml` where `bot_id` = $bot_id order by `filename`;";
    #return "SQL = $sql";
    $result = mysql_query($sql,$dbconn) or die(mysql_error());
    if (mysql_num_rows($result) == 0) $msg = "This bot has no AIML categories to clear.";
    while ($row = mysql_fetch_assoc($result)) {
      if (empty($row['filename'])) {
        $curOption = "                  <option value=\"\">{No Filename entry}</option>\n";
      }
      else $curOption = str_replace('[val]', $row['filename'], $optionTemplate);
      $out .= $curOption;
    }
    mysql_close($dbconn);
    $out .= "                  <!-- End Selectbox Options -->\n";
    return $out;
  }

  function renderMain() {
    $selectOptions = getSelOpts();
    $content = <<<endForm
          Deleting AIML categories from the database is <strong>permanent</strong>!
          This action <strong>CANNOT</strong> be undone!<br />
          <div id="clearForm">
          <form name="clearForm" action="./?page=clear" method="POST" onsubmit="return verify()">
          <table class="formTable">
            <tr>
              <td>
                <input type="radio" name="action" id="actionClearAll" value="clear">
                <label for="actionClearAll" style="width: 250px">Clear <strong>ALL</strong> AIML categories (Purge database)</label>
              </td>
              <td>
                <input type="radio" name="action" value="void" id="actionClearFile" checked="checked">
                <label for="actionClearFile" style="width: 210px; text-align: left">Clear categories from this AIML file: </label><br />
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <select name="clearFile" id="clearFile" size="1" style="margin: 14px;" onclick="document.getElementById('actionClearFile').checked = true" onchange="document.getElementById('actionClearFile').checked = true">
                  <option value="null" selected="selected">Choose a file</option>
$selectOptions
                </select>
              </td>
            </tr>
            <tr>
              <td colspan="2">
                <input type="submit" name="" value="Submit">
              </td>
            </tr>
          </table>
          </form>
          </div>
[showHelp]
          <script type="text/javascript">
            function verify() {
              var fn = document.getElementById('clearFile').value;
              var clearAll = document.getElementById('actionClearAll').checked;
              if (fn == 'null' && clearAll === false) return false;
              if (clearAll) fn = 'repository for all files';
              return confirm('This will delete all categories from the AIML file ' + fn + '! This cannot be undone! Are you sure you want to do this?');
            }
          </script>
endForm;

    return $content;
  }
?>