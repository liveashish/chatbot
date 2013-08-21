<?php
//-----------------------------------------------------------------------------------------------
//My Program-O Version 2.0.9
//Program-O  chatbot admin area
//Written by Elizabeth Perreau and Dave Morton
//Aug 2011
//for more information and support please visit www.program-o.com
//-----------------------------------------------------------------------------------------------
// members.php
  ini_set('memory_limit','128M');
  ini_set('max_execution_time','0');
  $myPost = print_r($_POST, true);
  #$msg = "<pre>$myPost</pre><br>\n";
  #if (!empty($_POST)) die ("<pre>\n Post Vars:\n$myPost\n</pre>\n");

  $uname = '';
  $action = (isset($_POST['action'])) ? ucfirst(strtolower($_POST['action'])) : 'Add';
  if (!empty($_POST)) {
    $msg = save($action);
    #$action = ($action == 'editfromlist') ? 'Edit' : $action;
  }

  $id = (isset($_POST['id']) and $action != 'Add') ? $_POST['id'] : getNextID();
  $id = ($id <= 0) ? getNextID() : $id;
  if (isset($_POST['memberSelect'])) {
    $id = $_POST['memberSelect'];
    getMemberData($_POST['memberSelect']);
  }
  $upperScripts = <<<endScript

    <script type="text/javascript">
<!--
      function showMe() {
        var sh = document.getElementById('showHelp');
        var tf = document.getElementById('membersForm');
        sh.style.display = 'block';
        tf.style.display = 'none';
      }
      function hideMe() {
        var sh = document.getElementById('showHelp');
        var tf = document.getElementById('membersForm');
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

  $XmlEntities = array(
    '&amp;'  => '&',
    '&lt;'   => '<',
    '&gt;'   => '>',
    '&apos;' => '\'',
    '&quot;' => '"',
  );

  $AdminsOpts   = getAdminsOpts();

  $membersForm       = $template->getSection('MembersForm');
  $members_list_form = $template->getSection('MembersListForm');
  $showHelp          = $template->getSection('MembersShowHelp');

  $topNav            = $template->getSection('TopNav');
  $leftNav           = $template->getSection('LeftNav');
  $main              = $template->getSection('Main');
  $topNavLinks       = makeLinks('top', $topLinks, 12);
  $navHeader         = $template->getSection('NavHeader');
  $leftNavLinks      = makeLinks('left', $leftLinks, 12);
  $FooterInfo        = getFooter();
  $errMsgClass       = (!empty($msg)) ? "ShowError" : "HideError";
  $errMsgStyle       = $template->getSection($errMsgClass);
  $noLeftNav         = '';
  $noTopNav          = '';
  $noRightNav        = $template->getSection('NoRightNav');
  $headerTitle       = 'Actions:';
  $pageTitle         = 'My-Program O - Admin Accounts';
  $mainContent       = $template->getSection('MembersMain');
  $mainTitle         = "Modify Admin Account Data [helpLink]";

  $members_list_form = str_replace('[adminList]', $AdminsOpts, $members_list_form);
  $mainContent       = str_replace('[members_content]', $membersForm, $mainContent);
  $mainContent       = str_replace('[showHelp]', $showHelp, $mainContent);
  $mainContent       = str_replace('[members_list_form]', $members_list_form, $mainContent);
  $mainContent       = str_replace('[uname]', $uname, $mainContent);
  $mainContent       = str_replace('[action]', $action, $mainContent);
  $mainContent       = str_replace('[id]', $id, $mainContent);
  $mainTitle         = str_replace('[helpLink]', $template->getSection('HelpLink'), $mainTitle);


  function updateDB($sql) {
    $dbconn = db_open();
    $result = mysql_query($sql,$dbconn)or die('You have a SQL error on line '. __LINE__ . ' of ' . __FILE__ . '. Error message is: ' . mysql_error() . ".<br />\nSQL = <pre>$sql</pre><br />\n");
    $commit = mysql_affected_rows($dbconn);
    return $commit;
  }

  function save($action) {
    global $dbn, $action;
    #return 'action = ' . $action;
    if (isset($_POST['memberSelect'])) {
      $id = $_POST['memberSelect'];
    }
    else {
      if (!isset($_POST['uname']) or !isset($_POST['pword']) or !isset($_POST['pwordConfirm'])) return 'You left something out!';
      $id = $_POST['id'];
      $uname = $_POST['uname'];
      $pword1 = $_POST['pword'];
      $pword2 = $_POST['pwordConfirm'];
      $pword = md5($pword1);
      if ($action != 'Delete' and ($pword1 != $pword2)) return 'The passwords don\'t match!';
    }
    switch ($action) {
      case 'Add':
      $ip = $_SERVER['REMOTE_HOST'];
      $sql = "insert into myprogramo (id, uname, pword, lastip, lastlogin) values (null, '$uname', '$pword','$ip', CURRENT_TIMESTAMP);";
      $out = "Account for $uname successfully added!";
      break;
      case 'Delete':
      $action = 'Add';
      $sql = "DELETE FROM `$dbn`.`myprogramo` WHERE `myprogramo`.`id` = $id LIMIT 1";
      $out = "Account for $uname successfully deleted!";
      break;
      case 'Edit':
      $action = 'Add';
      $sql = "update myprogramo set uname = '$uname', pword = '$pword' where id = $id;";
      $out = "Account for $uname successfully updated!";
      break;
      default:
      $action = 'Edit';
      $sql = '';
      $out = '';
    }
    $x = (!empty($sql)) ? updateDB($sql) : '';
    #return "action = $action<br />\n SQL = $sql";
    return $out;
  }



    function getAdminsOpts() {
    global $dbn;
    $out = "                  <!-- Start List of Current Admin Accounts -->\n";
    $dbconn = db_open();
    $optionTemplate = "                  <option value=\"[val]\">[key]</option>\n";
    $sql = 'SELECT id, uname FROM myprogramo order by uname;';
    $result = mysql_query($sql,$dbconn) or die(mysql_error());
    while ($row = mysql_fetch_assoc($result)) {
      $uname = $row['uname'];
      $id = $row['id'];
      $curOption = str_replace('[key]', $row['uname'], $optionTemplate);
      $curOption = str_replace('[val]', $row['id'], $curOption);
      $out .= $curOption;
    }
    mysql_close($dbconn);
    $out .= "                  <!-- End List of Current Admin Accounts -->\n";
    return $out;
  }

  function getMemberData($id) {
    if ($id <= 0) return false;
    global $dbn, $uname, $id;
    $sql = "select id, uname from myprogramo where id = $id limit 1;";
    $dbconn = db_open();
    $result = mysql_query($sql,$dbconn) or die(mysql_error());
    $row = mysql_fetch_assoc($result);
    $uname = $row['uname'];
    $id = $row['id'];
    mysql_close($dbconn);
  }

  function getNextID() {
    global $dbn, $uname;
    $sql = "select id from myprogramo order by id desc limit 1;";
    $dbconn = db_open();
    $result = mysql_query($sql,$dbconn) or die(mysql_error());
    $row = mysql_fetch_assoc($result);
    $id = $row['id'];
    mysql_close($dbconn);
    return $id + 1;
  }

?>