<?PHP
//-----------------------------------------------------------------------------------------------
//My Program-O Version 2.0.9
//Program-O  chatbot admin area
//Written by Elizabeth Perreau and Dave Morton
//Aug 2011
//for more information and support please visit www.program-o.com
//-----------------------------------------------------------------------------------------------
// index.php

  $thisFile = __FILE__;
  if (!file_exists('../config/global_config.php')) header('location: ../install/install_programo.php');
  require_once('../config/global_config.php');

  error_reporting(E_ALL);
  ini_set('log_errors', true);
  ini_set('error_log', _LOG_PATH_ . 'error.log');
  ini_set('html_errors', false);
  ini_set('display_errors', false);
  $msg = '';


  $bot_name = 'unknown';
  $bot_id = 1;
  session_start();
  $myPage = (isset($_GET['myPage'])) ? $_GET['myPage'] : '';
  $hide_logo = (isset($_SESSION['display'])) ? $_SESSION['display'] : '';
  if (!empty($_SESSION)) {
    if((!isset($_SESSION['poadmin']['uid'])) || ($_SESSION['poadmin']['uid']=="")) {
      $msg .= "Session timed out<br>\n";
      $_GET['page'] = 'logout';
    }
    else {
      $name = $_SESSION['poadmin']['name'];
      $ip = $_SESSION['poadmin']['ip'];
      $last = $_SESSION['poadmin']['lastlogin'];
      $lip = $_SESSION['poadmin']['lip'];
      $llast = $_SESSION['poadmin']['llastlogin'];
      $bot_name = $_SESSION['poadmin']['bot_name'];
      $bot_id = $_SESSION['poadmin']['bot_id'];
    }
  }
  //load shared files
  require_once(_LIB_PATH_ . 'db_functions.php');
  require_once(_LIB_PATH_ . 'error_functions.php');
  require_once(_LIB_PATH_ . 'template.class.php');
  # Load the template file
  $thisPath = dirname(__FILE__);
  $template = new Template("$thisPath/default.page.htm");
  $leftLinks = makeLeftLinks();
  $topLinks = makeTopLinks();
  $githubVersion = getCurrentVersion();
  $version = ($githubVersion == VERSION) ? 'Program O version ' . VERSION : 'There is a new version of Program O available. <a href="https://github.com/Program-O/Program-O/archive/master.zip">Click here</a> to download it.';
# set template section defaults

# Build page sections
# ordered here in the order that the page is constructed
  $logo          = $template->getSection('Logo');
  $titleSpan     = $template->getSection('TitleSpan');
  $main          = $template->getSection('Main');
  $divDecoration = $template->getSection('DivDecoration');
  $mainContent   = $template->getSection('LoginForm');
  $noLeftNav     = $template->getSection('NoLeftNav');
  $noRightNav    = $template->getSection('NoRightNav');
  $navHeader     = $template->getSection('NavHeader');
  $footer        = $template->getSection('Footer');
  $topNav        = '';
  $leftNav       = '';
  $rightNav      = '';
  $rightNavLinks = '';
  $lowerScripts  = $template->getSection('LogoLinkScript');
  $pageTitleInfo = '';
  #$topNavLinks   = makeLinks('top', $topLinks, 12);
  $topNavLinks   = '';
  $leftNavLinks  = '';
  $mediaType     = ' media="screen"';
  $mainTitle     = 'Program O Login';
  $FooterInfo    = '<p>&copy; 2011-2012 My Program-O<br /><a href="http://www.program-o.com">www.program-o.com</a></p>';
  $headerTitle   = '';
  $pageTitle     = 'My-Program O - Login';
  $upperScripts  = '';

  if((isset($_POST['uname']))&&(isset($_POST['pw']))) {
    $_SESSION['poadmin']['display'] = $hide_logo;
    $uname = mysql_real_escape_string(strip_tags(trim($_POST['uname'])));
    $pw = mysql_real_escape_string(strip_tags(trim($_POST['pw'])));
    $dbconn = db_open();
    $sql = "SELECT * FROM `myprogramo` WHERE uname = '".$uname."' AND pword = '".MD5($pw)."'";
    $result = mysql_query($sql,$dbconn) or $msg .= SQL_Error(mysql_errno());
    if ($result) {
      $count = mysql_num_rows($result);
      if($count > 0) {
        $row=mysql_fetch_array($result);
        $_SESSION['poadmin']['uid']=$row['id'];
        $_SESSION['poadmin']['name']=$row['uname'];
        $_SESSION['poadmin']['lip']=$row['lastip'];
        $_SESSION['poadmin']['llastlogin']=date('l jS \of F Y h:i:s A', strtotime($row['lastlogin']));
        if(!empty($_SERVER['HTTP_CLIENT_IP'])) {  //check ip from share internet
          $ip=$_SERVER['HTTP_CLIENT_IP'];
        }
        elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  //to check ip is pass from proxy
          $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else {
          $ip=$_SERVER['REMOTE_ADDR'];
        }
        $sqlupdate = "UPDATE `myprogramo` SET `lastip` = '$ip', `lastlogin` = CURRENT_TIMESTAMP WHERE uname = '$uname' limit 1";
        $result = mysql_query($sqlupdate,$dbconn);
        $transact = mysql_affected_rows($dbconn);
        $_SESSION['poadmin']['ip']=$ip;
        $_SESSION['poadmin']['lastlogin']=date('l jS \of F Y h:i:s A');
        $sql = "SELECT * FROM `bots` WHERE bot_active = '1' ORDER BY bot_id ASC LIMIT 1";
        $result = mysql_query($sql,$dbconn);
        $count = mysql_num_rows($result);
        if($count > 0) {
          $row=mysql_fetch_array($result);
          $_SESSION['poadmin']['bot_id']=$row['bot_id'];
          $_SESSION['poadmin']['bot_name']=$row['bot_name'];
        }
        else {
          $_SESSION['poadmin']['bot_id']=-1;
          $_SESSION['poadmin']['bot_name']="unknown";
        }
      }
      else {
        $msg .= "incorrect username/password<br>\n";
      }
    }
    mysql_close($dbconn);
    if($msg == "") {
      include ('main.php');
    }
  }
  elseif(isset($_GET['msg'])) {
    $msg .= htmlentities($_GET['msg']);
  }
  elseif(isset($_GET['page'])) {
    $curPage = $_GET['page'];
    if ($curPage == 'logout') {
      if(isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-42000, '/');
      }
      session_destroy();
      //header('location: ./');
    }
    else {
      $_SESSION['poadmin']['curPage'] = $curPage;
      include ("$curPage.php");
    }
  }
  $curPage = (isset($curPage)) ? $curPage : 'main';
  $upperScripts .= ($hide_logo == 'HideLogoCSS') ? $template->getSection('HideLogoCSS') : '';

  # Build page content from the template
  $content  = $template->getSection('Header');
  #$content .= "hide_logo = $hide_logo";
  $content .= $template->getSection('PageBody');

  # Replace template labels with real data
  $styleSheet = 'style.css';
  $errMsgClass   = (!empty($msg)) ? "ShowError" : "HideError";
  $errMsgStyle   = $template->getSection($errMsgClass);
/* These are the most common template replacement tags used. Any additional
   replacement tags should be handled in the include file for the current page,
   in a function named replaceTags(&$content). This function will alter the
   $content variable directly, rather than change it and then return it.
*/
  $searches = array(
                    '[myPage]'        => $curPage,
                    '[pageTitle]'     => $pageTitle,
                    '[styleSheet]'    => $styleSheet,
                    '[mediaType]'     => $mediaType,
                    '[upperScripts]'  => $upperScripts,
                    '[logo]'          => $logo,
                    '[pageTitleInfo]' => $pageTitleInfo,
                    '[topNav]'        => $topNav,
                    '[leftNav]'       => $leftNav,
                    '[rightNav]'      => $rightNav,
                    '[main]'          => $main,
                    '[rightNav]'      => $rightNav,
                    '[footer]'        => $footer,
                    '[lowerScripts]'  => $lowerScripts,
                    '[pageTitleInfo]' => $pageTitleInfo,
                    '[titleSpan]'     => $titleSpan,
                    '[divDecoration]' => $divDecoration,
                    '[topNavLinks]'   => $topNavLinks,
                    '[navHeader]'     => $navHeader,
                    '[leftNavLinks]'  => $leftNavLinks,
                    '[mainTitle]'     => $mainTitle,
                    '[mainContent]'   => $mainContent,
                    '[rightNavLinks]' => $rightNavLinks,
                    '[FooterInfo]'    => $FooterInfo,
                    '[headerTitle]'   => $headerTitle,
                    '[errMsg]'        => $msg,
                    '[bot_id]'        => $bot_id,
                    '[bot_name]'      => $bot_name,
                    '[errMsgStyle]'   => $errMsgStyle,
                    '[noRightNav]'    => $noRightNav,
                    '[noLeftNav]'     => $noLeftNav,
                    '[version]'       => $version,
                   );
  foreach ($searches as $search => $replace) {
    $content = str_replace($search, $replace, $content);
  }
  $content = str_replace('[myPage]', $curPage, $content);
  $content = str_replace('[divDecoration]', $divDecoration, $content);
  $content = str_replace('[blank]', '', $content);
  if(function_exists('replaceTags')) replaceTags($content); // Handle any extra replacement tags, as needed.
  #die ('<pre>' . print_r($_SESSION, true) . "</pre><br />\ndisplay = $hide_logo<br />\n");
  exit($content);

  function makeLinks($section, $linkArray, $spaces = 2) {
    #print "<!-- making links for section $section -->\n";
    global $template, $curPage;
    $curPage = (empty($curPage)) ? 'main' : $curPage;
    $botName = (isset($_SESSION['poadmin']['bot_name'])) ? $_SESSION['poadmin']['bot_name'] : 'unknown';
    $out = '';
    # [linkClass][linkHref][linkOnclick][linkAlt][linkTitle]>[linkLabel]
    $linkText = $template->getSection('NavLink');
    foreach ($linkArray as $needle) {
      $tmp = $linkText;
      foreach ($needle as $search => $replace) {
        $tmp = str_replace($search, $replace, $tmp);
      }
      $linkClass = $needle['[linkHref]'];
      $linkClass = str_replace(' href="./?page=', '', $linkClass);
      $linkClass = str_replace('"', '', $linkClass);
      #die ("linkClass = $linkClass<br />\nstrstr = $sp<br />\n");
      #$curClass = ($linkClass == $curPage) ? 'selected' : 'noClass';
      $curClass = ($linkClass == $curPage) ? 'selected' : 'noClass';
      if ($curPage == 'main') $curClass = (stripos($linkClass,'main') !== false) ? 'selected' : 'noClass';
/*
*/
      $tmp = str_replace('[curClass]', $curClass, $tmp);
      $out .= "$tmp\n";
    }
    #print "<!-- returning links for section $section:\n\n out = $out\n\n -->\n";
    $out = str_replace('[curBot]', $botName, $out);
    return trim($out);
  }


  function getFooter() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $name = $_SESSION['poadmin']['name'];
    $lip = $_SESSION['poadmin']['lip'];
    $last = $_SESSION['poadmin']['lastlogin'];
    $llast = $_SESSION['poadmin']['llastlogin'];
    $admess = "You are logged in as: $name from $ip since: $last";
    $admess .= "<br />You last logged in from $lip on $llast";
    $today = date("Y");
    $out = <<<endFooter
    <p>&copy; $today My Program-O<br />$admess</p>
endFooter;
    return $out;
  }

  function makeTopLinks() {
    $out = array(
                         array(
                               '[linkClass]' => ' class="[curClass]"',
                               '[linkHref]' => ' href="./?page=main"',
                               '[linkOnclick]' => '',
                               '[linkAlt]' => ' alt="Home"',
                               '[linkTitle]' => ' title="Home"',
                               '[linkLabel]' => 'Home'
                               ),
                         array(
                               '[linkClass]' => ' class="[curClass]"',
                               '[linkHref]' => ' href="'.NEWS_URL.'"',
                               '[linkOnclick]' => '',
                               '[linkAlt]' => ' alt="Get the latest news from the Program O website"',
                               '[linkTitle]' => ' title="Get the latest news from the Program O website"',
                               '[linkLabel]' => 'News'
                               ),
                         array(
                               '[linkClass]' => ' class="[curClass]"',
                               '[linkHref]' => ' href="'.FAQ_URL.'"',
                               '[linkOnclick]' => '',
                               '[linkAlt]' => ' alt="The Program O User\'s Guide"',
                               '[linkTitle]' => ' title="The Program O User\'s Guide"',
                               '[linkLabel]' => 'Documentation'
                               ),
                         array(
                               '[linkClass]' => ' class="[curClass]"',
                               '[linkHref]' => ' href="./?page=bugs"',
                               '[linkOnclick]' => '',
                               '[linkAlt]' => ' alt="Bug reporting"',
                               '[linkTitle]' => ' title="Bug reporting"',
                               '[linkLabel]' => 'Bug Reporting'
                               ),
                         array(
                               '[linkClass]' => ' class="[curClass]"',
                               '[linkHref]' => ' href="./?page=stats"',
                               '[linkOnclick]' => '',
                               '[linkAlt]' => ' alt="Get bot statistics"',
                               '[linkTitle]' => ' title="Get bot statistics"',
                               '[linkLabel]' => 'Stats'
                               ),
                         array(
                               '[linkClass]' => ' class="[curClass]"',
                               '[linkHref]' => ' href="./?page=support"',
                               '[linkOnclick]' => '',
                               '[linkAlt]' => ' alt="Get support for Program O"',
                               '[linkTitle]' => ' title="Get support for Program O"',
                               '[linkLabel]' => 'Support'
                               ),
                         array(
                               '[linkClass]' => '',
                               '[linkHref]' => ' href="./?page=logout"',
                               '[linkOnclick]' => '',
                               '[linkAlt]' => ' alt="Log out"',
                               '[linkTitle]' => ' title="Log out"',
                               '[linkLabel]' => 'Log Out'
                               )
                        );
    return $out;
  }
  function makeLeftLinks() {
    $out = array(
                 array( # Change bot
                       '[linkClass]' => ' class="[curClass]"',
                       '[linkHref]' => ' href="./?page=select_bots"',
                       '[linkOnclick]' => '',
                       '[linkAlt]' => ' alt="Change or edit the current bot"',
                       '[linkTitle]' => ' title="Change or edit the current bot"',
                       '[linkLabel]' => 'Change/Edit Bot: ([curBot])'
                 ),
                 array(
                       '[linkClass]' => ' class="[curClass]"',
                       '[linkHref]' => ' href="./?page=botpersonality"',
                       '[linkOnclick]' => '',
                       '[linkAlt]' => ' alt="Edit your bot\'s personality"',
                       '[linkTitle]' => ' title="Edit your bot\'s personality"',
                       '[linkLabel]' => 'Bot Personality'
                 ),
                 array(
                       '[linkClass]' => ' class="[curClass]"',
                       '[linkHref]' => ' href="./?page=logs"',
                       '[linkOnclick]' => '',
                       '[linkAlt]' => ' alt="View the log files"',
                       '[linkTitle]' => ' title="View the log files"',
                       '[linkLabel]' => 'Logs'
                 ),
                 array(
                       '[linkClass]' => ' class="[curClass]"',
                       '[linkHref]' => ' href="./?page=teach"',
                       '[linkOnclick]' => '',
                       '[linkAlt]' => ' alt="Train your bot"',
                       '[linkTitle]' => ' title="Train your bot"',
                       '[linkLabel]' => 'Teach'
                 ),
                 array(
                       '[linkClass]' => ' class="[curClass]"',
                       '[linkHref]' => ' href="./?page=upload"',
                       '[linkOnclick]' => '',
                       '[linkAlt]' => ' alt="Upload AIML files"',
                       '[linkTitle]' => ' title="Upload AIML files"',
                       '[linkLabel]' => 'Upload AIML'
                 ),
                 array(
                       '[linkClass]' => ' class="[curClass]"',
                       '[linkHref]' => ' href="./?page=download"',
                       '[linkOnclick]' => '',
                       '[linkAlt]' => ' alt="Download AIML files"',
                       '[linkTitle]' => ' title="Download AIML files"',
                       '[linkLabel]' => 'Download AIML'
                 ),
                 array(
                       '[linkClass]' => ' class="[curClass]"',
                       '[linkHref]' => ' href="./?page=clear"',
                       '[linkOnclick]' => '',
                       '[linkAlt]' => ' alt="Clear AIML Categories"',
                       '[linkTitle]' => ' title="Clear AIML Categories"',
                       '[linkLabel]' => 'Clear AIML Categories'
                 ),
                 array(
                       '[linkClass]' => ' class="[curClass]"',
                       '[linkHref]' => ' href="./?page=spellcheck"',
                       '[linkOnclick]' => '',
                       '[linkAlt]' => ' alt="Edit the SpellCheck entries"',
                       '[linkTitle]' => ' title="Edit the SpellCheck entries"',
                       '[linkLabel]' => 'Spell Check'
                 ),
                 array(
                       '[linkClass]' => ' class="[curClass]"',
                       '[linkHref]' => ' href="./?page=wordcensor"',
                       '[linkOnclick]' => '',
                       '[linkAlt]' => ' alt="Edit the Word Censor entries"',
                       '[linkTitle]' => ' title="Edit the Word Censor entries"',
                       '[linkLabel]' => 'Word Censor'
                 ),
                 array(
                       '[linkClass]' => ' class="[curClass]"',
                       '[linkHref]' => ' href="./?page=search"',
                       '[linkOnclick]' => '',
                       '[linkAlt]' => ' alt="Search and edit specific AIML categories"',
                       '[linkTitle]' => ' title="Search and edit specific AIML categories"',
                       '[linkLabel]' => 'Search/Edit AIML'
                 ),
                 array(
                       '[linkClass]' => ' class="[curClass]"',
                       '[linkHref]' => ' href="./?page=demochat"',
                       '[linkOnclick]' => '',
                       '[linkAlt]' => ' alt="Run a demo version of your bot"',
                       '[linkTitle]' => ' title="Run a demo version of your bot"',
                       '[linkLabel]' => 'Test Your Bot'
                 ),
                 array(
                       '[linkClass]' => '',
                       '[linkHref]' => ' href="./?page=members"',
                       '[linkOnclick]' => '',
                       '[linkAlt]' => ' alt="Edit Admin Accounts"',
                       '[linkTitle]' => ' title="Edit Admin Accounts"',
                       '[linkLabel]' => 'Edit Admin Accounts'
                 ),
                 array(
                       '[linkClass]' => '',
                       '[linkHref]' => ' href="./?page=logout"',
                       '[linkOnclick]' => '',
                       '[linkAlt]' => ' alt="Log out"',
                       '[linkTitle]' => ' title="Log out"',
                       '[linkLabel]' => 'Log Out'
                 ),
                 array(
                       '[linkClass]' => '',
                       '[linkHref]' => ' href="#"',
                       '[linkOnclick]' => ' onclick="toggleLogo(); return false;"',
                       '[linkAlt]' => ' alt="Toggle the Logo"',
                       '[linkTitle]' => ' title="Toggle the Logo"',
                       '[linkLabel]' => 'Toggle the Logo'
                 )
    );
    return $out;
  }

  function getRSS($feed = 'RSS') {
    global $template;
    switch ($feed) {
      case 'support':
      $feedURL = SUP_URL;
      break;
      default:
      $feedURL = RSS_URL;
    }
    $out = '';
    if (function_exists('curl_init')) {
      $ch = curl_init($feedURL);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      $data = curl_exec($ch);
      curl_close($ch);
      if (false === $data) return 'The RSS Feed is not currently available. We apologise for the inconvenience.';
      $rss = new SimpleXmlElement($data, LIBXML_NOCDATA);
      if($rss) {
        $items = $rss->channel->item;
          foreach ($items as $item) {
            $title = $item->title;
            $link = $item->link;
            $published_on = $item->pubDate;
            $description = $item->description;
            $out .= "<h3><a target=\"_blank\" href=\"$link\">$title</a></h3>\n";
            $out .= "<p>$description</p>";
          }
        }
    }
    else $out = 'RSS Feed not available';
    return $out;
  }

  function getCurrentVersion()
  {
    $url = 'https://api.github.com/repos/Program-O/Program-O/contents/version.txt';
    $out = false;
    if (function_exists('curl_init'))
    {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $out = curl_exec($ch);
      if (false === $out) trigger_error('Not sure what it is, but there\'s a problem with checking the current version on GitHub. Maybe this will help: "' . curl_error($ch) . '"');
      curl_close($ch);
      $repoArray = json_decode($out, true);
      $versionB64 = $repoArray['content'];
      $version = base64_decode($versionB64);
      $out = $version;
    }
    return $out;
  }

?>
