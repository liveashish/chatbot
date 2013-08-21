<?php
/***************************************
* http://www.program-o.com
* PROGRAM O 
* Version: 2.0.9
* FILE: chatbot/core/aiml/load_aimlfunctions.php
* AUTHOR: ELIZABETH PERREAU
* DATE: MAY 4TH 2011
* DETAILS: this file contains all the includes that are needed to load all the aiml functions
***************************************/

include_once("find_aiml.php");
include_once("parse_aiml.php");
include_once("make_aiml_to_php_code.php");
include_once("buildingphp_code_functions.php");
include_once("replace_tomakesafe.php");

runDebug( __FILE__, __FUNCTION__, __LINE__, "Aimlfunction include files loaded",4);
?>