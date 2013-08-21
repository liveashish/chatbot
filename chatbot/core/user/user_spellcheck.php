<?php

function make_wordArray($user_say)
{
	//break up the sentance into words
	return explode(" ",$user_say);

}

function glue_words_for_sql($wordArr)
{
	foreach ($wordArr as $index => $word)
	{
		$wordlist .= "\"".db_make_safe($word)."\", ";
	}
	
	$wordlist = str_replace(",","",$wordlist);
	$wordlist = str_replace(", ","",$wordlist);
	
	
	
	return $wordlist;
}


function spellcheck($user_say)
{
	global $dbconn;

	$wordArr = make_wordArray($user_say);
	$limit_res = count($wordArr);
	$sqlwords = glue_words_for_sql($wordArr);
	
	
	$sql = "SELECT * FROM `$dbn`.`spellcheck` WHERE `missspelling` IN ($sqlwords) LIMIT $limit_res";
	$result = db_query($sql,$dbconn);
	
	if($result)
	{
		if(db_res_count($result)>0)
		{
			while($row=db_res_array($result))//loop thru results
			{
				$pattern = '/\b'.$row['missspelling'].'\b/';
				$replacement = $row['correction'];
				if($user_say = preg_replace($pattern, $replacement, $user_say))
				{
					runDebug( __FILE__, __FUNCTION__, __LINE__, "Replacing ".$row['missspelling']." with ".$row['correction'],4);	
	
				}
			}
		}
	}	
}

?>