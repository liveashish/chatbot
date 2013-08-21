<?php
//-----------------------------------------------------------------------------------------------
//My Program-O Version 2.0.9
//Program-O  chatbot admin area
//Written by Elizabeth Perreau and Dave Morton
//May 2011
//for more information and support please visit www.program-o.com
//-----------------------------------------------------------------------------------------------
// help.php
ini_set("display_errors", false);
ini_set("log_errors",true);
ini_set("error_log","../logs/error.log");

define ('SECTION_START', '<!-- Section [section] Start -->'); # search params for start and end of sections
define ('SECTION_END', '<!-- Section [section] End -->'); # search params for start and end of sections

$template = file_get_contents('help.tpl.htm');

$content = getSection('HelpPage', $template, false);
$helpContent = getSection('HelpMain',$template);
$content = str_replace('[helpContent]', $helpContent, $content);

die($content);


function getSection($sectionName, $page_template, $notFoundReturn = true) {
  $sectionStart = str_replace('[section]', $sectionName, SECTION_START);
  $sectionStartLen = strlen($sectionStart);
  $sectionEnd   = str_replace('[section]', $sectionName, SECTION_END);
  $startPos = strpos($page_template, $sectionStart, 0);
   if ($startPos === false) {
     if ($notFoundReturn) {
       return '';
     }
     else $startPos = 0;
  }
  else $startPos += $sectionStartLen;
  $endPos = strpos($page_template, $sectionEnd, $startPos) - 1;
  $sectionLen = $endPos - $startPos;
  $out = substr($page_template, $startPos, $sectionLen);
  return trim($out);
}


?>
