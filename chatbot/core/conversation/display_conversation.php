<?php
/***************************************
* http://www.program-o.com
* PROGRAM O 
* Version: 2.0.9
* FILE: chatbot/core/conversation/display_conversation.php
* AUTHOR: ELIZABETH PERREAU
* DATE: MAY 4TH 2011
* DETAILS: this file contains the functions to handle the return of the conversation lines back to the user
***************************************/

/**
 * function get_conversation_to_display()
 * This function gets the conversation from the db to display/return to the user
 * @param  array $convoArr - the conversation array
 * @return array $orderedRows - a list of conversation line
**/
function get_conversation_to_display($convoArr)
{
	global $con,$dbn, $bot_name;
  $user_id = $convoArr['conversation']['user_id'];
  $bot_id = $convoArr['conversation']['bot_id'];
  $sql = "select `name` from `users` where `id` = $user_id limit 1;";
  $result = db_query($sql,$con);
  $row = mysql_fetch_assoc($result);
  $user_name = $row['name'];
  $user_name = (!empty($user_name)) ? $user_name : 'User';
  $convoArr['conversation']['user_name'] = $user_name;
  $convoArr['conversation']['bot_name'] = $bot_name;
	if (empty($bot_name)) {
	  $sql = "select `bot_name` from `bots` where `bot_id` = $bot_id limit 1;";
		$result = db_query($sql,$con);
    $row = mysql_fetch_assoc($result);
    $bot_name = $row['bot_name'];

	}
	if($convoArr['conversation']['conversation_lines']!=0){
		$limit = " LIMIT ".$convoArr['conversation']['conversation_lines'];
	}else{
		$limit = "";}
	
	$sql = "SELECT * FROM `$dbn`.`conversation_log`
		WHERE 
		`userid` = '".$convoArr['conversation']['user_id']."'
		AND `bot_id` = '".$convoArr['conversation']['bot_id']."'
		ORDER BY id DESC $limit ";
		#$x = save_file('conversationLogSQL.txt', "SQL = \r\n$sql");
	runDebug( __FILE__, __FUNCTION__, __LINE__, "get_conversation SQL: $sql",3);
		
		$result = db_query($sql,$con);

		if(db_res_count($result)>0){
				while($row=mysql_fetch_array($result,MYSQL_ASSOC)){
					$allrows[]=$row;
				}
			$orderedRows = array_reverse($allrows,false);
		}
		else 
		{
			$orderedRows =array('id'=>NULL, 'input'=>"", 'response'=>"", 'userid'=>$convoArr['conversation']['user_id'], 'bot_id'=>$convoArr['conversation']['bot_id'], 'timestamp'=>"");
			
		}
	
	
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Found '".db_res_count($result)."' lines of conversation",2);
	
	return 	$orderedRows;
}
	
/**
 * function get_conversation()
 * This function gets the conversation format
 * @param  array $convoArr - the conversation array
 * @return array $convoArr
**/	
function get_conversation($convoArr)
{
	$conversation = get_conversation_to_display($convoArr);
	
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Processing conversation as ".$convoArr['conversation']['format'],4);
	
	switch($convoArr['conversation']['format'])
	{
		case "html":
				$convoArr = get_html($convoArr,$conversation);
			break;
		case "json":
				$convoArr = get_json($convoArr,$conversation);
			break;
		case "xml":
				$convoArr = get_xml($convoArr,$conversation);
			break;
	}
	return $convoArr;
}

/**
 * function get_html()
 * This function formats the response as html
 * @param  array $convoArr - the conversation array
 * @param  array $conversation - the conversation lines to format
 * @return array $convoArr
**/	
function get_html($convoArr,$conversation)
{
	$conversation_lines = $convoArr['conversation']['conversation_lines'];
	$show= "";
	$user_name = $convoArr['conversation']['user_name'];
	$bot_name  = $convoArr['conversation']['bot_name'];
	foreach($conversation as $index => $conversation_subarray){
		$show .= "<div class=\"usersay\">$user_name: ".stripslashes($conversation_subarray['input'])."</div>";
		$show .= "<div class=\"botsay\">$bot_name: ".stripslashes($conversation_subarray['response'])."</div>";
		
		
		
		
	}
	
	$convoArr['send_to_user']=$show;
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Returning HTML",4);
	return $convoArr;
}
	

/**
 * function get_json()
 * This function formats the response as json
 * @param  array $convoArr - the conversation array
 * @param  array $conversation - the conversation lines to format
 * @return array $convoArr
**/	
function get_json($convoArr,$conversation)
{
	$conversation_lines = $convoArr['conversation']['conversation_lines'];
	$show_json = array();
	$i=0;
	
	foreach($conversation as $index => $conversation_subarray){
		
		$show_json['convo_id'] = $convoArr['conversation']['convo_id'];
		$show_json['usersay'] = stripslashes($conversation_subarray['input']);
		$show_json['botsay'] = stripslashes($conversation_subarray['response']);
		

		
		$i++;
	
	}
	
	
	
	$convoArr['send_to_user']= json_encode($show_json);
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Returning JSON",4);
	return $convoArr;	
}	


/**
 * function get_xml()
 * This function formats the response as xml
 * @param  array $convoArr - the conversation array
 * @param  array $conversation - the conversation lines to format
 * @return array $convoArr
**/	
function get_xml($convoArr,$conversation)
{
	$user_name = $convoArr['conversation']['user_name'];
	$user_id   = $convoArr['conversation']['user_id'];
	$bot_name  = $convoArr['conversation']['bot_name'];

	$conversation_lines = $convoArr['conversation']['conversation_lines'];
	$convo_xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n  <conversation convo_id=\"".$convoArr['conversation']['convo_id']."\">\n";
	$convo_xml .= "    <bot_name>$bot_name</bot_name>\n";
	$convo_xml .= "    <user_name>$user_name</user_name>\n";
	$convo_xml .= "    <user_id value='$user_id' />\n";
/*
	$convo_xml .= "    <chat>\n";
*/
	foreach($conversation as $index => $conversation_subarray)	{	
		$convo_xml .= "      <usersay>".stripslashes($conversation_subarray['input'])."</usersay>\n      <botsay>".stripslashes($conversation_subarray['response'])."</botsay>\n";
		}
		$convo_xml .= "  </conversation>\n";
		#$convo_xml .= "    </chat>\n  </conversation>\n";
		$convoArr['send_to_user']=$convo_xml;
		runDebug( __FILE__, __FUNCTION__, __LINE__, "Returning XML",4);
	return $convoArr;
}	
?>