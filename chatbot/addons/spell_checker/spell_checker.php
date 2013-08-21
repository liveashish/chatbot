<?php
/***************************************
* www.program-o.com
* PROGRAM O 
* Version: 2.0.9
* FILE: spell_checker/spell_checker.php
* AUTHOR: ELIZABETH PERREAU
* DATE: MAY 4TH 2011
* DETAILS: this file contains the addon library to spell check into before its matched in the database
***************************************/


/**
 * function run_spellcheck()
 * A function to run the spellchecking of the userinput
 * @param  array $convoArr - the conversation array
 * @return $convoArr (spellchecked)
**/
function run_spell_checker($convoArr)
{
	
	$lookingfor = get_convo_var($convoArr,"aiml","lookingfor");
	$wordArr = explode(' ', $lookingfor);
	
	foreach($wordArr as $index => $word)
	{
		$sentance .= spell_check($word,$convoArr['conversation']['bot_id'])." ";
	}
	
	
	$convoArr['aiml']['lookingfor']=$sentance;
	
	return $convoArr;
}


/**
 * function spell_check()
 * A function query the db and get find mispelt words 
**/
function spell_check($word,$bot_id)
{
	global $con,$dbn; //set in global config file
	
	$sql = "SELECT * FROM `$dbn`.`spellcheck` WHERE `bot_exclude` NOT LIKE '%[$bot_id]%' LIMIT 1";

	$result = db_query($sql,$con);
	
	while($row=mysql_fetch_array($result)){
		$word = preg_replace("/\b".$row['missspelling']."\b/i",$row['correction'],$word);
		if(mysql_num_rows($result)>0)
		{
			$word = preg_replace("/\b".$row['missspelling']."\b/i",$row['correction'],$word);
		}
	}
	return $word;
}







?>