<?php
/***************************************
* www.program-o.com
* PROGRAM O 
* Version: 2.0.9
* FILE: chatbot/core/user/handle_user.php
* AUTHOR: ELIZABETH PERREAU
* DATE: MAY 4TH 2011
* DETAILS: this file contains the functions to handle the  
*          user in the conversation 
***************************************/

/**
 * function load_new_client_defaults()
 * A function to intialise clients values
 * @param  array $convoArr - the current state of the conversation array
 * @return the update $convoArr
**/
function load_new_client_defaults($convoArr)
{
	
	//to do could put this in an array
	//todo check this out
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Loading client defaults",1);
	$convoArr['client_properties']['name'] = "my friend";
	$convoArr['client_properties']['id'] = $_SERVER['REMOTE_ADDR'];
	
	return $convoArr;
}	

/**
 * function get_user_id()
 * A function to get the user id
 * @param  array $convoArr - the current state of the conversation array
 * @return the update $convoArr
**/
function get_user_id($convoArr)
{
	//db globals
	global $con,$dbn,$unknown_user;
	
	//get undefined defaults from the db
	$sql = "SELECT * FROM `$dbn`.`users` WHERE `session_id` = '".$convoArr['conversation']['convo_id']."' limit 1";
	$result = mysql_query($sql,$con);
	
	$count = mysql_num_rows($result);
	if($count>0)
	{
		$row = mysql_fetch_array($result);
		$convoArr['conversation']['user_id'] = $row['id'];
    // add user name, if set
		$convoArr['conversation']['user_name'] = (!empty($row['name'])) ? $row['name'] : (!empty($convoArr['client_properties']['name'])) ? $convoArr['client_properties']['name'] : $unknown_user;
		$msg = "existing";
	}
	else
	{
		$convoArr['conversation']['user_id'] = intisaliseUser($convoArr['conversation']['convo_id']);
		$msg = "new";
	}
	
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Getting $msg user id:".$convoArr['conversation']['user_id'],4);
	runDebug( __FILE__, __FUNCTION__, __LINE__, "get_user_id SQL: $sql",3);
	return $convoArr;
	
}

/**
 * function intisaliseUser()
 * This function gets data such as the referer to store in the db
 * @param string $convo_id - user session
 * @return int $user_id - the newly created user id
**/
function intisaliseUser($convo_id)
{
	//db globals
	global $con,$dbn, $default_bot_id;
	
	$sr = "";
	$sa = "";
	$sb = "unknown browser";
	
	if(isset($_SERVER['REMOTE_ADDR'])){
		$sa = mysql_real_escape_string($_SERVER['REMOTE_ADDR']);
	} 
	
	if(isset($_SERVER['HTTP_REFERER'])){
		$sr = mysql_real_escape_string($_SERVER['HTTP_REFERER']);
	}
	
	if(isset($_SERVER['HTTP_USER_AGENT'])){
		$sb = mysql_real_escape_string($_SERVER['HTTP_USER_AGENT']);
	}

	$sql = "INSERT INTO `$dbn`.`users` (`id` ,`session_id`, `bot_id`, `chatlines` ,`ip` ,`referer` ,`browser` ,`date_logged_on` ,`last_update`)
	VALUES ( NULL , '$convo_id', $default_bot_id, '0', '$sa', '$sr', '$sb', CURRENT_TIMESTAMP , '0000-00-00 00:00:00')";

	mysql_query($sql,$con);
	$user_id = mysql_insert_id($con);
	
	runDebug( __FILE__, __FUNCTION__, __LINE__, "intisaliseUser #$user_id SQL: $sql",3);
	
	return $user_id;
}

?>