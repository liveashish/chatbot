<?php
//-----------------------------------------------------------------------------------------------
//My Program-O Version 2.0.9
//Program-O  chatbot admin area
//Written by Elizabeth Perreau and Dave Morton
//Aug 2011
//for more information and support please visit www.program-o.com
//-----------------------------------------------------------------------------------------------
// bugs.php

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
        var subj = "Bug Report Submission";
        var msg = "Your Message Here...";
        function renewImage() {
          document.getElementById("capImg").src = "captcha.php?xx=" + Math.random();
        }
        function clearElement(e) {
          var name = e.name;
          if(name == "subject" && e.value == subj) e.value = "";
          if(name == "message" && e.value == msg) e.value = "";
        }
//-->
      </script>
endScript;
  foreach ($_POST as $key => $value) {
    $$key = mysql_real_escape_string($value);
  }
  $func = (isset($_POST['func'])) ? $_POST['func'] : 'showBugForm';
# Build page sections
# ordered here in the order that the page is constructed
# Only the variables that are different from the
# login page need be set here.
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
    $pageTitle     = 'My-Program O - Report a Bug';
    $mainContent   = $func();
    $lowerScripts  .= '      <script type="text/javascript">var fName = document.contactForm.name;fName.focus();</script>';
    $mainTitle     = 'Send a Bug Report';


  function showBugForm() {
    return <<<endForm
      <form method="POST" action="./?page=bugs" name="contactForm">
        <input name="func" value="sendMail" type="hidden">
        <input name="contactMe" value="true" type="hidden">
        <table border="1" width="100%">
          <tr>
            <td align="center" width="33%">
              Your Name:<br />
              <input name="name" value="" type="text">
            </td>
            <td align="center">
              Your Email Address:<br />
              <input name="email" value="" type="text">
            </td>
            <td align="center" width="33%">
              Subject:<br />
              <input name="subject" value="Bug Report Submission" onfocus="clearElement(this)" type="text">
            </td>
          </tr>
          <tr>
            <td colspan="3" align="center">
              Message:<br />
              <textarea rows="7" cols="70" name="message" onfocus="clearElement(this)">Your Message Here...</textarea>
            </td>
          </tr>
          <tr>
            <td>
              <p class="indent">
                Use this handy form to submit a bug report to Liz and Company over at the Program O website.
                Please describe the problem as completely as possible, including what actions you were trying
                to perform at the time that you noticed the problem.
              </p>
            </td>
            <td align="center">
              <img id="capImg" src="captcha.php" title="Captcha script ©2009-2011 Geek Cave Creations"><br />
              <input onclick="renewImage(); return false" value="Refresh" type="button">
            </td>
            <td>
              <p class="indent">
                Answer the question you see in the image on the left into the text area
                below. We are looking for a one word answer (no numbers). Sry, but bots are not allowed.
              </p>
              <input name="captcha" type="text">
            </td>
          </tr>
          <tr>
            <td colspan="4" align="center">
              <input name="Post" value="Send Mail" type="submit">
            </td>
          </tr>
        </table>
      </form>
endForm;
  }
  function sendMail() {
    global $email, $name, $subject, $message, $captcha;
    #print "<!-- Ginger message = $message --\n";
    $_SESSION['message'] = $message;
    $rawCap = $captcha;
    $captcha = sha1(strtolower($captcha));
    $capKey = (isset($_SESSION["capKey"])) ? $_SESSION["capKey"] : "";
    $out = "";
    $time = date('h:j:s');
    $date = date('m/d/Y');
    $remoteAddr = $_SERVER['REMOTE_ADDR'];
    $remoteHost = (isset($_SERVER['REMOTE_HOST'])) ? $_SERVER['REMOTE_HOST'] : 'localhost';
    $localServer = $_SERVER['HTTP_HOST'];
    $senderInfo = "\n\nThis message was sent through the Program O Bug Reporting System at $localServer, from $remoteAddr at $time on $date\n";
    $message .= $senderInfo;
    $cba = checkBadAddress($email);
    $cbip = checkBadIP();
	if ($email != "" and $name != "" and $subject != "" and $cba == 0 and $cbip == 0 and $message != "" and ($captcha == $capKey)) {
      $toAddr = "dmorton@geekcavecreations.com, " . BUGS_EMAIL;
      $fromAddr = "$email";
      $header = "From: $name <$email>";
      $result = mail ($toAddr, $subject, $message, $header);
      $out .= <<<endThanx
      <p>
      Ok, message sent. Thanx for taking the time to submit your bug report.
      With your help, we can make Program O even better than ever!<br />
      </p>
      <p style="text-align:center">
        <a href="./">Home</a>
      </p>
endThanx;
    }
    Else {
      $description = "";
      $description .= ($cba == 1)              ? "        <li>Your email address is on our ban list.</li>\n" : "";
      $description .= ($cbip == 1)             ? "        <li>Your IP address is on our ban list.</li>\n" : "";
      $description .= ($name == "")            ? "        <li>The name field was left blank.</li>\n" : "";
      $description .= ($subject == "")         ? "        <li>The subject field was left blank.</li>\n" : "";
      $description .= ($message == "")         ? "        <li>The message field was left blank.</li>\n" : "";
      $description .= ($captcha != $capKey) ? "        <li>The typed CAPTCHA did not match the image (image was $capKey and text was $captcha. Text entered was $rawCap).</li>\n" : "";
      $insert = ($cba == 1 or $cbip == 1) ? " don't" : "";

      $out .= <<<endOops
      <ul>The following errors need to be addressed:
        $description
      </ul>
      Please$insert try again.<br />      <a href="#" onclick='history.go(-1)'>Back</a>
endOops;
    }
    return $out;
  }

  function checkBadAddress ($address) {
    global $content;
    $out = 0;
	$excluded = array("namecheap2.ehost-services150.com", "rxciales.info", "mail.ru", "rxcilliss.info", "PaulkyLyday@gmail.com");
	foreach ($excluded as $check) {
      $isPresent =  strpos($address, $check);
      $content .= "<!-- google address = $address : check = $check : isPresent = |$isPresent| -->\n";
      if ($isPresent !== false) $out = 1;
	}
      $content .= "<!-- giggle out = $out -->\n";
	return $out;
  }

  function checkBadIP () {
    global $content;
    $IP = $_SERVER['REMOTE_ADDR'];
    $out = 0;
	$excluded = array("89.28.114", "85.140.66.54");
	foreach ($excluded as $check) {
      $isPresent =  strpos($IP, $check);
      $content .= "<!-- google IP address = $IP : check = $check : isPresent = |$isPresent| -->\n";
      if ($isPresent !== false) $out = 1;
	}
      $content .= "<!-- giggle out = $out -->\n";
	return $out;
  }
?>