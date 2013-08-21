<?php
/***************************************
* www.program-o.com
* PROGRAM O 
* Version: 2.0.9
* FILE: chatbot/addons/load_addons.php
* AUTHOR: ELIZABETH PERREAU
* DATE: MAY 4TH 2011
* DETAILS: this file contains the calls to include addon functions
***************************************/

//load the word censor functions
include("custom_tags/custom_tags.php");
include("word_censor/word_censor.php");
include("parseBBCode/parseBBCode.php"); // A new addon to allow parsing of output that's consistent with BBCode tags
include("checkForBan/checkForBan.php"); // A new addon for verifying that a user has not been banned by IP address

runDebug( __FILE__, __FUNCTION__, __LINE__, "Loading addons",4);

function run_pre_input_addons(&$convoArr, $say) {
  global $format;
  $convoArr = checkIP($convoArr);
  if ($format == 'html') $say =  parseInput($say);
  return $say;
}

function run_post_response_useraddons($convoArr) {
  $format = $convoArr['conversation']['format'];
  $response = (isset($convoArr['send_to_user'])) ? $convoArr['send_to_user'] : die('<pre>' . print_r($convoArr, true) . "\n</pre>\n");
  $curTime = date('H:i:s');
  $response = str_replace('[serverTime]',$curTime, $response);
  if ($convoArr['send_to_user'] != $response) $convoArr['send_to_user'] = $response;
  $convoArr =  run_censor($convoArr);
  if ($format == 'html') $convoArr =  checkForParsing($convoArr);
  $ip = $convoArr['client_properties']['ip_address'];
  if ($convoArr['client_properties']['banned'] === true) add_to_ban($ip);
  return $convoArr;
}










?>