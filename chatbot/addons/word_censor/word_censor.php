<?php
/***************************************
* www.program-o.com
* PROGRAM O 
* Version: 2.0.9
* FILE: word_censor/word_censor.php
* AUTHOR: ELIZABETH PERREAU
* DATE: MAY 4TH 2011
* DETAILS: this file contains the addon library to censor output before they are output to user
* 			the swear words are encoded in the session array to protect little eyes
***************************************/


/**
 * function run_censor()
 * A function to run the censorship of words
 * if the censor session array is not set this will set it
 * @param  array $convoArr - the conversation array
 * @return $convoArr (censored)
**/
function run_censor($convoArr)
{
	if(!isset($_SESSION['programo_bigArray']['censor']))
	{
		initialise_censor($convoArr['conversation']['bot_id']);
	}
	
	
	$convoArr['send_to_user']=censor_words($convoArr['send_to_user']);
	

	
	return $convoArr;
}


/**
 * function intialise_censor()
 * A function to build session array containing the words from the censor list in the db 

**/
function initialise_censor($bot_id)
{
	global $con,$dbn; //set in global config file
	
	$sql = "SELECT * FROM `$dbn`.`wordcensor` WHERE `bot_exclude` NOT LIKE '%[$bot_id]%'";
	$result = db_query($sql,$con);
	
	
	
	while($row=mysql_fetch_array($result)){
		$_SESSION['programo_bigArray']['censor'][base64_encode("/\b".$row['word_to_censor']."\b/i")]=base64_encode("/\b".$row['replace_with']."\b/i");
	}
}


/**
 * function censor_words()
 * A function to censor words before outputting them to screen 
 * @param  str $output - the string we wish to censor
 * @return $coutput (censored)
**/
function censor_words($output)
{
	foreach($_SESSION['programo_bigArray']['censor'] as $find => $replace)
	{
		$output = preg_replace(base64_decode($find), base64_decode($replace), $output); 
	}
	return $output;
}





?>