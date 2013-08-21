<?php
/***************************************
* www.program-o.com
* PROGRAM O 
* Version: 2.0.9
* FILE: chatbot/core/conversation/make_conversation.php
* AUTHOR: ELIZABETH PERREAU
* DATE: MAY 4TH 2011
* DETAILS: this file contains the functions control the creation of the conversation 
***************************************/

/**
 * function make_conversation()
 * A controller function to run the instructions to make the conversation
 * @param  array $convoArr - the current state of the conversation array
 * @return $convoArr (updated)
**/	
function make_conversation($convoArr){
	
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Making conversation",4);
	global $offset;
	//get the user input and clean it
	//$convoArr = clean_for_aiml_match('user_say','lookingfor',$convoArr);
	$convoArr['aiml']['lookingfor'] =  clean_for_aiml_match($convoArr['user_say'][$offset]);
	//find an aiml match in the db
	$convoArr = get_aiml_to_parse($convoArr);
	$convoArr = parse_matched_aiml($convoArr,'normal');
		
	//parse the aiml to build a response
	//store the conversation
	$convoArr = push_on_front_convoArr('parsed_template',$convoArr['aiml']['parsed_template'],$convoArr);
	$convoArr = push_on_front_convoArr('template',$convoArr['aiml']['template'],$convoArr);
	//display conversation vars to user.
	$convoArr['conversation']['totallines']++;
	return $convoArr;
}

/**
 * function add_aiml_to_php()
 * A controller function to add/update the php code stored in the aiml table
 * @param  array $convoArr - the current state of the conversation array
 * @return $convoArr (updated)
**/	
function add_aiml_to_php($convoArr){
	
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Adding PHP to table",4);
	global $dbn,$con;
	$evalthis = mysql_real_escape_string($convoArr['aiml']['aiml_to_php']);
	$sql = "UPDATE `$dbn`.`aiml` SET `php_code` = \"$evalthis\" WHERE `id` = '".$convoArr['aiml']['aiml_id']."' LIMIT 1";
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Adding new PHP to aiml table SQL: $sql",3);
	$result = db_query($sql,$con);
	return $convoArr;
}

/**
 * function make_safe_to_eval()
 * A function to escape the dollarsigns in the php code so it can be evaluated
 * @param  string $evalthis - string to make safe
 * @return string $evalthis
**/	
function make_safe_to_eval($evalthis){
	
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Making it safe to eval",4);
	$evalthis = str_replace('"','\"',$evalthis);
	$evalthis = str_replace('$','\$',$evalthis);
	return $evalthis;
}

/**
 * function eval_aiml_to_php_code()
 * @param  array $convoArr - the current state of the conversation array
 * @param  string $evalthis - string to make safe
 * @return string $botsay
**/	
function eval_aiml_to_php_code($convoArr,$evalthis){
	
	runDebug( __FILE__, __FUNCTION__, __LINE__, "",4);
	$botsay = @run_aiml_to_php($convoArr,$evalthis);
	//if run correctly $botsay should be re valued
	return $botsay;
}



/**
 * function run_aiml_to_php()
 * @param  array $convoArr - the current state of the conversation array
 * @param  string $evalthis - string to make safe
 * @return string $result (-botsay)
**/	
function run_aiml_to_php($convoArr,$evalthis){
	
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Evaluating Stored PHP Code from the Database",4);
	global $botsay;
	global $error_response;

	//this must be NULL if it is FALSE then its failed but  if its NULL its a success
	$error_flag = eval($evalthis);
	if($error_flag===NULL){ //success
		runDebug( __FILE__, __FUNCTION__, __LINE__, "EVALUATED: $evalthis ",4);
		$result = $botsay;
	} else { //error
		runDebug( __FILE__, __FUNCTION__, __LINE__, "ERROR TRYING TO EVAL: $evalthis ",1);
		runDebug( __FILE__, __FUNCTION__, __LINE__, "ERROR TRYING TO EVAL: ".print_r($convoArr['aiml'],true),1);
		$result = $error_response;}
	
	return $result;
}
?>