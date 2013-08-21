<?PHP
//-----------------------------------------------------------------------------------------------
//My Program-O Version 2.0.9
//Program-O  chatbot admin area
//Written by Elizabeth Perreau and Dave Morton
//Aug 2011
//for more information and support please visit www.program-o.com
//-----------------------------------------------------------------------------------------------
// bots.php

# set template section defaults

# Build page sections
# ordered here in the order that the page is constructed
  $bot_name = (isset($_SESSION['poadmin']['bot_name'])) ? $_SESSION['poadmin']['bot_name'] : 'unknown';
  $func = (isset($_POST['func'])) ? $_POST['func'] : 'getBot';
  #die ("func = $func");
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
  $pageTitle     = 'My-Program O - Bot Personality';
  #$mainContent   = ($func != 'updateBot') ? $func() : '';
  $mainContent   = "main content";
  #$msg           = "function = $func";
  switch ($func) {
    case 'updateBot':
    $msg = $func();
    $mainContent = getBot();
    break;
    default:
    $mainContent = $func();
    #$msg = "function = $func";
  }
/*
*/
  #$mainContent   = "test... func = $func";
  $mainTitle     = 'Bot Personality Settings for '.$bot_name;
  if ($func == 'updateBot' or $func == 'addBotPersonaity') {
    $msg = updateBot();
    include('main.php');
  }
function getBot() {
  #die('entered function.');
  global $dbn;
  $dbconn = db_open();
  $formCell  = '                <td><label for="[row_label]"><span class="label">[row_label]:</span></label> <span class="formw"><input name="[row_label]" id="[row_label]" value="[row_value]" /></span></td>
';
  $blankCell ='                <td style="text-align: center"><label for="newEntryName[cid]"><span class="label">New Entry Name: <input name="newEntryName[cid]" id="newEntryName[cid]" style="width: 98%" /></label></span>&nbsp;<span class="formw"><label for="newEntryValue[cid]" style="float: left; padding-left: 3px;">New Entry Value: </label><input name="newEntryValue[cid]" id="newEntryValue[cid]" /></span></td>
';
  $startDiv = '      <td>' . "\n        ";
  $endDiv = "\n      </td>\n      <br />\n";
  $inputs="";
  $row_class = 'row fm-opt';
  $bot_name = $_SESSION['poadmin']['bot_name'];
  $bot_id = (isset($_SESSION['poadmin']['bot_id'])) ? $_SESSION['poadmin']['bot_id'] : 0;
  //get the current bot's personality table from the db
  $sql = "SELECT * FROM `botpersonality` where  bot = $bot_id";
  #die ("SQL = $sql<br />db name = $dbn\n");
  $result = mysql_query($sql,$dbconn)or $msg .= SQL_Error(mysql_errno());
  if ($result) {
  $rowCount = mysql_num_rows($result);
  if ($rowCount > 0) {
    $left = true;
    $colCount = 0;
    while($row = mysql_fetch_assoc($result)) {
      $rid = $row['id'];
      $label = $row['name'];
      $value = stripslashes_deep($row['value']);
      $tmpRow = str_replace('[row_class]', $row_class, $formCell);
      $tmpRow = str_replace('[row_id]', $rid, $tmpRow);
      $tmpRow = str_replace('[row_label]', $label, $tmpRow);
      $tmpRow = str_replace('[row_value]', $value, $tmpRow);
      $inputs .= $tmpRow;
      $colCount++;
      if ($colCount >=3) {
        $inputs .= '              </tr>
              <tr>';
        $colCount = 0;
      }
    }
    $inputs .= "<!-- colCount = $colCount -->\n";
    if (($colCount > 0) and ($colCount < 3)) {
      for ($n = 0; $n < (3 - $colCount); $n++) {
        $addCell = str_replace('[cid]',"[$n]", $blankCell);
        $inputs .= $addCell;
      }
    }
    mysql_close($dbconn);
    $action = 'Update Data';
    $func   = 'updateBot';
  }
  else {
    $inputs = newForm();
    $action = 'Add New Data';
    $func   = 'addBotPersonality';
  }
  }
  if (empty($func)) $func = 'getBot';
  $form = <<<endForm2
          <form name="botpersonality" action="./?page=botpersonality" method="post">
            <table class="botForm">
              <tr>
$inputs
              </tr>
              <tr>
                <td colspan="3">
                  <input type="hidden" id="bot_id" name="bot_id" value="$bot_id">
                  <input type="hidden" id="func" name="func" value="$func">
                  <input type="submit" name="action" id="action" value="$action">
                </td>
              </tr>
            </table>
          </form>
  <!-- fieldset>
  </fieldset -->
endForm2;
  return $form;
}

function stripslashes_deep($value) {
  $newValue = stripslashes($value);
  while ($newValue != $value) {
    $value = $newValue;
    $newValue = stripslashes($value);
  }
  return $newValue;
}


function updateBot() {
  global $bot_id, $bot_name;
  $botId = (isset($_POST['bot_id'])) ? $_POST['bot_id'] : $bot_id;
  $dbconn = db_open();
  $msg = "";
  if (!empty($_POST['newEntryName'])) {
    $newEntryNames  = $_POST['newEntryName'];
    $newEntryValues = $_POST['newEntryValue'];
    $addSQL = "Insert into `botpersonality` (`id`, `bot`, `name`, `value`) values\n";
    $addSQLTemplate = "(null, $bot_id, '[key]', '[value]'),\n";
    foreach ($newEntryNames as $index => $key) {
      $value = $newEntryValues[$index];
      if (empty($value)) continue;
      $tmpSQL = str_replace('[key]', $key, $addSQLTemplate);
      $tmpSQL = str_replace('[value]', $value, $tmpSQL);
      $addSQL .= $tmpSQL;
    }
    $addSQL = rtrim($addSQL,",\n");
    $result = mysql_query($addSQL,$dbconn) or die('You have a SQL error on line '. __LINE__ . ' of ' . __FILE__ . '. Error message is: ' . mysql_error() . ".<br />SQL:<br /><pre>\n$addSQL\n<br />\n</pre>\n");
    if(!$result) {
      $msg = 'Error updating bot personality.';
    }
    elseif($msg == "") {
      $msg = 'Bot personality added.';
    }
  }

  $updateSQL = "UPDATE `botpersonality` SET `value` = CASE `name` \n";
  $sql = "SELECT * FROM `botpersonality` where bot = $botId;";
  $changes = array();
  $additions = array();
  $result = mysql_query($sql, $dbconn) or $msg .= SQL_Error(mysql_errno());
  if ($result) {
  while ($row = mysql_fetch_assoc($result)) {
    $id = $row['id'];
    $name = $row['name'];
    $value = $row['value'];
    $postVal = (isset($_POST[$name])) ? $_POST[$name] : '';
    if (!empty($postVal)) {
       if ($postVal != $value){
        $changes[$id] = mysql_real_escape_string(stripslashes_deep($postVal));
        $additions[$id] = $name;
       }
    }
  }
  }
  if (!empty($additions)) {
    $changesText = implode(',', array_keys($changes));
    foreach ($changes as $id => $value) {
      $name = $additions[$id];
      $updateSQL .= sprintf("WHEN '%s' THEN '%s' \n", $name, $value);
    }
    $updateSQL .= "END WHERE `id` IN ($changesText);";
    $saveSQL = str_replace("\n", "\r\n", $updateSQL);
    $result = mysql_query($updateSQL, $dbconn) or die('You have a SQL error on line '. __LINE__ . ' of ' . __FILE__ . '. Error message is: ' . mysql_error() . ".<br />SQL:<br /><pre>\n$updateSQL\n<br />\n</pre>\n");
    if (!$result) $msg = 'Error updating bot.';
    $msg = (empty($msg)) ? 'Bot personality updated.' : $msg;
  }
  else $msg = 'Something';
  mysql_close($dbconn);
  return $msg;
}

function addBotPersonality() {
  $dbconn = db_open();
/*
  $postVars = print_r($_POST, true);
  die ("Post vars:<pre>\n$postVars\n</pre>");
*/
  $bot_id = $_POST['bot_id'];
  $sql = "Insert into `botpersonality` (`id`, `bot`, `name`, `value`) values\n";
  $sql2 = "(null, $bot_id, '[key]', '[value]'),\n";
  $msg = "";
  $newEntryNames = (isset($_POST['newEntryName'])) ? $_POST['newEntryName'] : '';
  $newEntryValues = (isset($_POST['newEntryValue'])) ? $_POST['newEntryValue'] : '';
  if (!empty($newEntryNames)) {
    foreach ($newEntryNames as $index => $key) {
      $value = $newEntryValues[$index];
      if (!empty($value)) {
        $tmpSQL = str_replace('[key]', $key, $sql2);
        $tmpSQL = str_replace('[value]', $value, $tmpSQL);
        $sql .= $tmpSQL;
      }
    }
  }

  $skipKeys = array('bot_id', 'action', 'func', 'newEntryName', 'newEntryValue');
  foreach($_POST as $key => $value) {
    if(!in_array($key, $skipKeys)) {
      if($value=="")  continue;
      if (is_array($value)) {
        foreach ($value as $index => $fieldValue) {
          $field = $key[$fieldValue];
          $fieldValue = mysql_real_escape_string(trim($fieldValue));
          $tmpSQL = str_replace('[key]', $field, $sql2);
          $tmpSQL = str_replace('[value]', $fieldValue, $tmpSQL);
          $sql .= $tmpSQL;
        }
        continue;
      }
      else {
        $value = mysql_real_escape_string(trim($value));
        $tmpSQL = str_replace('[key]', $key, $sql2);
        $tmpSQL = str_replace('[value]', $value, $tmpSQL);
        $sql .= $tmpSQL;
      }
    }
  }
  $sql = rtrim($sql,",\n");
  $result = mysql_query($sql,$dbconn) or die('You have a SQL error on line '. __LINE__ . ' of ' . __FILE__ . '. Error message is: ' . mysql_error() . ".<br />SQL:<br /><pre>\n$sql\n<br />\n</pre>\n");
  if(!$result) {
    $msg = 'Error updating bot personality.';
  }
  elseif($msg == "") {
    $msg = 'Bot personality added!';
  }
  mysql_close($dbconn);
  return $msg;
}


  function newForm() {
    $out = '                <table class="botForm">
                  <tr>
';
    $rowTemplate = '                    <td><label for="[field]"><span class="label">[uc_field]:</span></label> <span class="formw"><input name="[field]" id="[field]" value="" /></span></td>
';
    $tr = '                  </tr>
                  <tr>
';
    $blankTD = '                    <td>&nbsp;</td>
';

    $lastBit = '                  </tr>
                  <tr>
                    <td style="text-align: center"><label for="newEntryName[0]"><span class="label">New Entry Name: <input name="newEntryName[0]" id="newEntryName[0]" style="width: 98%" /></label></span>&nbsp;<span class="formw"><label for="newEntryValue[0]" style="float: left; padding-left: 3px;">New Entry Value: </label><input name="newEntryValue[0]" id="newEntryValue[0]" /></span></td>
                    <td style="text-align: center"><label for="newEntryName[1]"><span class="label">New Entry Name: <input name="newEntryName[1]" id="newEntryName[1]" style="width: 98%" /></label></span>&nbsp;<span class="formw"><label for="newEntryValue[1]" style="float: left; padding-left: 3px;">New Entry Value: </label><input name="newEntryValue[1]" id="newEntryValue[1]" /></span></td>
                    <td style="text-align: center"><label for="newEntryName[2]"><span class="label">New Entry Name: <input name="newEntryName[2]" id="newEntryName[2]" style="width: 98%" /></label></span>&nbsp;<span class="formw"><label for="newEntryValue[2]" style="float: left; padding-left: 3px;">New Entry Value: </label><input name="newEntryValue[2]" id="newEntryValue[2]" /></span></td>
                  </tr>
                </table>
';

    $fields = file(_CONF_PATH_ . 'default_botpersonality_fields.dat');
    $count = 0;
    foreach ($fields as $field)
    {
      $count++;
      $field = trim($field);
      $tmpRow = str_replace('[field]', $field, $rowTemplate);
      $tmpRow = str_replace('[uc_field]', ucfirst($field), $tmpRow);
      $out .= $tmpRow;
      if ($count % 3 == 0) $out .= $tr;
    }
    switch ($count % 3)
    {
      case 1:
      $out .= $blankTD;
      break;
      case 2:
      $out .= $blankTD . $blankTD;
    }
    $out .= $lastBit;
    return $out;

  }


?>