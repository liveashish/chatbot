<?php
/***************************************
* www.program-o.com
* PROGRAM O 
* Version: 2.0.9
* FILE: chatbot/core/user/load_userfunctions.php
* AUTHOR: ELIZABETH PERREAU
* DATE: MAY 4TH 2011
* DETAILS: this file contains the includes to load all user function files  
***************************************/

include_once("user_input_clean.php");
include_once("user_spellcheck.php");
include_once("handle_user.php");

runDebug( __FILE__, __FUNCTION__, __LINE__, "userfunctions include files loaded",4);
?>