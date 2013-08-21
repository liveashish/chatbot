<?php

  /***************************************
  * http://www.program-o.com
  * PROGRAM O
  * Version: 2.0.9
  * FILE: chatbot/conversation_start.php
  * AUTHOR: ELIZABETH PERREAU
  * DATE: 19 JUNE 2012
  * DETAILS: this file is the landing page for all calls to access the bots
  ***************************************/
  $thisFile = __FILE__;
  if ((isset ($_REQUEST['say'])) && (trim($_REQUEST['say']) == "clear properties"))
  {
    session_start();
    // Unset all of the session variables.
    $_SESSION = array();
    // If it's desired to kill the session, also delete the session cookie.
    // Note: This will destroy the session, and not just the session data!
    if (ini_get("session.use_cookies"))
    {
      $params = session_get_cookie_params();
      setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }
    // Finally, destroy the session.
    session_destroy();
    session_start();
    session_regenerate_id();
    $new_id = session_id();
    //TODO WHICH ONE IS IT?
    $_GET['convo_id'] = $new_id;
    $_POST['convo_id'] = $new_id;
    $_REQUEST['convo_id'] = $new_id;
    $_REQUEST['say'] = "Hello";
  }
  else
  {
    session_start();
  }
  $time_start = microtime(true);
  require_once ("../config/global_config.php");
  //load shared files
  include_once (_LIB_PATH_ . "db_functions.php");
  include_once (_LIB_PATH_ . "error_functions.php");
  //leave this first debug call in as it wipes any existing file for this session
  runDebug(__FILE__, __FUNCTION__, __LINE__, "Conversation Starting", 4);
  //load all the chatbot functions
  include_once (_BOTCORE_PATH_ . "aiml" . $path_separator . "load_aimlfunctions.php");
  //load all the user functions
  include_once (_BOTCORE_PATH_ . "conversation" . $path_separator . "load_convofunctions.php");
  //load all the user functions
  include_once (_BOTCORE_PATH_ . "user" . $path_separator . "load_userfunctions.php");
  //load all the user addons
  include_once (_ADDONS_PATH_ . "load_addons.php");
  //------------------------------------------------------------------------
  // Error Handler
  //------------------------------------------------------------------------
  // set to the user defined error handler
  set_error_handler("myErrorHandler");
  //open db connection
  $con = db_open();
  //initialise globals
  $convoArr = array();
  $display = "";
  runDebug(__FILE__, __FUNCTION__, __LINE__, "Loaded all Includes", 4);
  //if the user has said something
  if ((isset ($_REQUEST['say'])) && (trim($_REQUEST['say']) != ""))
  {
    $say = trim($_REQUEST['say']);
    //add any pre-processing addons
    $say = run_pre_input_addons($convoArr, $say);
    #die('say = ' . $say);
    runDebug(__FILE__, __FUNCTION__, __LINE__, "Details:\nUser say: " . $_REQUEST['say'] . "\nConvo id: " . $_REQUEST['convo_id'] . "\nBot id: " . $_REQUEST['bot_id'] . "\nFormat: " . $_REQUEST['format'], 2);
    //get the stored vars
    $convoArr = read_from_session();
    //now overwrite with the recieved data
    $convoArr = check_set_bot($convoArr);
    $convoArr = check_set_convo_id($convoArr);
    $convoArr = check_set_user($convoArr);
    $convoArr = check_set_format($convoArr);
    $convoArr['time_start'] = $time_start;
    //if totallines = 0 then this is new user
    if (isset ($convoArr['conversation']['totallines']))
    {
    //reset the debug level here
      $debuglevel = get_convo_var($convoArr, 'conversation', 'debugshow', '', '');
    }
    else
    {
    //load the chatbot configuration
      $convoArr = load_bot_config($convoArr);
      //reset the debug level here
      $debuglevel = get_convo_var($convoArr, 'conversation', 'debugshow', '', '');
      //insita
      $convoArr = intialise_convoArray($convoArr);
      //add the bot_id dependant vars
      $convoArr = add_firstturn_conversation_vars($convoArr);
      $convoArr['conversation']['totallines'] = 0;
      $convoArr = get_user_id($convoArr);
    }
    $convoArr['aiml'] = array();
    //add the latest thing the user said
    $convoArr = add_new_conversation_vars($say, $convoArr);
    //parse the aiml
    $convoArr = make_conversation($convoArr);
    $convoArr = log_conversation($convoArr);
    $convoArr = log_conversation_state($convoArr);
    $convoArr = write_to_session($convoArr);
    $convoArr = get_conversation($convoArr);
    $convoArr = run_post_response_useraddons($convoArr);
    //return the values to display
    $display = $convoArr['send_to_user'];
    runDebug(__FILE__, __FUNCTION__, __LINE__, "Conversation Ending", 4);
    $convoArr = handleDebug($convoArr);
    runDebug(__FILE__, __FUNCTION__, __LINE__, "Returning " . $convoArr['conversation']['format'], 4);
    if ($convoArr['conversation']['format'] == "html")
    {
    //TODO what if it is ajax call
      $time_start = $convoArr['time_start'];
      $time_end = microtime(true);
      $time = $time_end - $time_start;
      runDebug(__FILE__, __FUNCTION__, __LINE__, "Script took $time seconds", 2);
      return $convoArr['send_to_user'];
    }
    else
    {
      echo $convoArr['send_to_user'];
    }
  }
  else
  {
    runDebug(__FILE__, __FUNCTION__, __LINE__, "Conversation intialised waiting user", 2);
  }
  runDebug(__FILE__, __FUNCTION__, __LINE__, "Closing Database", 2);
  $time_end = microtime(true);
  $time = $time_end - $time_start;
  runDebug(__FILE__, __FUNCTION__, __LINE__, "Script took $time seconds", 2);

?>