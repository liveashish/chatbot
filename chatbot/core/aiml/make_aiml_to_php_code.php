<?php
/***************************************
* http://www.program-o.com
* PROGRAM O 
* Version: 2.0.9
* FILE: chatbot/core/aiml/make_aiml_to_php_code.php
* AUTHOR: ELIZABETH PERREAU
* DATE: MAY 4TH 2011
* DETAILS: this file contains the functions generate php code from aiml
***************************************/

/**
 * function aiml_to_phpfunctions()
 * This function performs a big find and replace on the aiml to convert it to php code
 * @param  array $convoArr - the existing conversation array
 * @return array $convoArr
**/
function aiml_to_phpfunctions($convoArr)
{
	//TODO do we need this still?
	global $botsay,$srai_iterations,$error_response; //TODO read from bot vars
	
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Converting the AIML to PHP code",4);
	
	
	//extra debug info
	$msg = "";
    $useStoredPHP = $convoArr['conversation']['use_aiml_code'];
    $uac = $convoArr['conversation']['update_aiml_code'];
    #$uac = 0;
	if($convoArr['aiml']['aiml_to_php']==""){
		$msg .= " php code does not exist,";
	} else {
		$msg .= " php code exists,";
	}
	if($useStoredPHP==1) {
		$msg .= " Use stored php code is set to YES($useStoredPHP)";
	} else {
		$msg .= " Use stored php code is set to NO($useStoredPHP)";
	}
	if($uac==1) {
		$msg .= " update aiml to php is set to YES($uac)";
	} else {
		$msg .= " update aiml to php is set to NO($uac)";
	}

	//THIS MAY already have the code contained in the db in which case we can skip all of this
	//UNLESS - update_aiml_code is set to 1 this means we want to re-write it each time
	if(($convoArr['aiml']['aiml_to_php']!="") and ($uac==0) and ($useStoredPHP == 1)){

		runDebug( __FILE__, __FUNCTION__, __LINE__, "Using existing AIML to PHP code - $msg",2);
		$parsed_template = get_convo_var($convoArr,'aiml','aiml_to_php');
	} else {
		
		runDebug( __FILE__, __FUNCTION__, __LINE__, "Generating new AIML to PHP code - $msg",2);
			
		//load the existing aiml template
		$template = get_convo_var($convoArr,'aiml','template');

		//make stars, apostrophes and encode foriegn chars just to make everything safe before the big replace
		$template = str_replace("*","\*",$template);
		$template = str_replace("'",'~apos~',$template);
		$template = foreignchar_replace('encode',$template);
		$template = entity_replace('encode',$template);	
		$i=0;
		
		//to do this is in the add custom tags thing
		//start the find and replace


$template = preg_replace('#<bot name="([^"]*)"/>#ie', "\$convoArr['bot_properties']['$1']", $template);
			runDebug( __FILE__, __FUNCTION__, __LINE__, "Made initial bot property replacements",4);


			$find[$i]='#</say>#i';
			$replace[$i]='\';';

			
			$i++;
			$find[$i]='#<template>(\s)*?</template>#i';
			$replace[$i]='';			

			$i++;
			$find[$i]='#<template/>#i';
			$replace[$i]='';
				
			
			$i++;
			$find[$i]='#<say>#i';
			$replace[$i]='$tmp_botsay .= \'';	

			$i++;
			$find[$i]='#<bot name="([^"]*)"/>#i';
			$replace[$i]='\'.call_user_func(\'get_convo_var\',$convoArr,\'bot_properties\',\'$1\').\'';


			$i++;
			$find[$i]='#<date format="([^"]*)"/>#i';
			$replace[$i]='\'.call_user_func(\'get_formatted_date\',\'$1\').\'';

			$i++;
			$find[$i]='#<bigthink></bigthink>#i';
			$replace[$i]='; $tmp_botsay = ""; ';	

			$i++;
			$find[$i]='#<pushstack>(.*)([^<]*)</pushstack>#';
			$replace[$i]='\'.call_user_func(\'push_stack\',$convoArr,\'$1\').\'';

			$i++;
			$find[$i]='#PUSH\s?<([^>]*)>#';
			$replace[$i]='\'.call_user_func(\'push_stack\',$convoArr,\'<$1>\').\'';

			$i++;
			$find[$i]='#POP\s?<([^>]*)>#';
			$replace[$i]='\'.call_user_func(\'pop_stack\',$convoArr).\'';

			$i++;
			$find[$i]='#<popstack></popstack>#';
			$replace[$i]='\'.call_user_func(\'pop_stack\',$convoArr).\'';

			$i++;
			$find[$i]='#<personf/>#i';
			$replace[$i]='\'.call_user_func(\'url_encode_star\',$convoArr).\'';

			$i++;
			$find[$i]='#<topic name=\"(A-Z0-9)\">#i';
			$replace[$i]='\'.call_user_func(\'set_topicname\',$convoArr,\'$1\').\'';

			$i++;
			$find[$i]='#<star index="([^"]*?)"/>#i';
			$replace[$i]='\'.call_user_func(\'get_convo_var\',$convoArr,\'star\',\'\',\'$1\').\'';



			$i++;
			$find[$i]='#<that index="(.*?),(.*?)"/>#i';
			$replace[$i]='\'.call_user_func(\'get_convo_var\',$convoArr,\'that\',\'\',\'$1\',\'$2\').\'';

			$i++;
			$find[$i]='#<input index="([^"]*)"/>#i';
			$replace[$i]='\'.call_user_func(\'get_convo_var\',$convoArr,\'input\',\'\',\'$1\').\'';

			$i++;
			$find[$i]='#<thatstar index="([^"]*)"/>#i';
			$replace[$i]='\'.call_user_func(\'get_convo_var\',$convoArr,\'that_star\',\'\',\'$1\').\'';

			$i++;
			$find[$i]='#<topicstar index="([^"]*)"/>#i';
			$replace[$i]='\'.call_user_func(\'get_convo_var\',$convoArr,\'topic_star\',\'\',\'$1\').\'';


			$i++;
			$find[$i]='#<get name="topic"(\s)?(/)?>#i';
			$replace[$i]='\'.call_user_func(\'get_convo_var\',$convoArr,\'topic\').\'';

			$i++;
			$find[$i]='#<get name="([^"]*)"(/)?>#i';
			$replace[$i]='\'.call_user_func(\'get_convo_var\',$convoArr,\'client_properties\',\'$1\').\'';






			$i++;
			$find[$i]='#<id/>#i';
			$replace[$i]='\'.call_user_func(\'get_convo_var\',$convoArr,\'client_properties\',\'id\').\'';

			$i++;
			$find[$i]='#<uppercase>([^<]*)</uppercase>#i';
			$replace[$i]='\'.call_user_func(\'format\',\'uppercase\',\'$1\').\'';

			$i++;
			$find[$i]='#<lowercase>([^<]*)</lowercase>#i';
			$replace[$i]='\'.call_user_func(\'format\',\'lowercase\',\'$1\').\'';	

			$i++;
			$find[$i]='#<formal>([^<]*)</formal>#i';
			$replace[$i]='\'.call_user_func(\'format\',\'formal\',\'$1\').\'';		

			$i++;
			$find[$i]='#<sentence>([^<]*)</sentence>#i';
			$replace[$i]='\'.call_user_func(\'format\',\'sentence\',\'$1\').\'';		

			$i++;
			$find[$i]='#<srai>#i';
			$replace[$i]='\'.call_user_func(\'run_srai\',$convoArr,\'';	

			$i++;
			$find[$i]='#</srai>#i';
			$replace[$i]='\').\'';	

			$i++;
			$find[$i]='#<think>#i';
			$replace[$i]='\'.call_user_func(\'make_null\',\'';

			$i++;
			$find[$i]='#</think>#i';
			$replace[$i]='\').\'';

			$i++;
			$find[$i]='#<person>([^<]*)</person>#i';
			$replace[$i]='\'.call_user_func(\'transform_prounoun\',$convoArr,\'3\',\'$1\').\'';

			$i++;
			$find[$i]='#<person2>([^<]*)</person2>#i';
			$replace[$i]='\'.call_user_func(\'transform_prounoun\',$convoArr,\'2\',\'$1\').\'';

			$i++;
			$find[$i]='#<condition>[\s]?<li name="([^"]*)" value="([^"]*)">#i';
			$replace[$i]="';\r\n".' if( ((isset($convoArr[\'$1\'])) && (strtoupper($convoArr[\'$1\']) === strtoupper(\'$2\'))) || ((isset($convoArr[\'client_properties\'][\'$1\'])) && (strtoupper($convoArr[\'client_properties\'][\'$1\']) === strtoupper(\'$2\')))  )'."\r\n".' { $tmp_botsay .= \'';		

			$i++;
			$find[$i]='#<condition name="([^"]*)">#i';
			$replace[$i]="\r\n".'; $condition = call_user_func(\'clean_condition\',\'$1\'); ';

			$i++;
			$find[$i]='#<li name="([0-9a-z]*)" value="([0-9a-z]*)">#i';
			$replace[$i]="\r\n".' elseif( ((isset($convoArr[\'$1\'])) && (strtoupper($convoArr[\'$1\']) === strtoupper(\'$2\'))) || ((isset($convoArr[\'client_properties\'][\'$1\'])) && (strtoupper($convoArr[\'client_properties\'][\'$1\']) === strtoupper(\'$2\')))  )'."\r\n".' { $tmp_botsay .= \'';		

			$i++;
			$find[$i]='#<li value="([^"]*)">#i';
			$replace[$i]="\r\n".' elseif( ((isset($convoArr[$condition])) && (strtoupper($convoArr[$condition]) === strtoupper(\'$1\'))) || ((isset($convoArr[\'client_properties\'][$condition])) && (strtoupper($convoArr[\'client_properties\'][$condition]) === strtoupper(\'$1\')))  )'."\r\n".' { $tmp_botsay .= \'';		

			$i++;
			$find[$i]='#;(\s|\s+)?elseif#i';
			$replace[$i]=";\r\nif";		

			
			$i++;
			$find[$i]="#<\random>(\?|\.|\!\s)?</li>#eis";
			$replace[$i]='</random>$1\';}';			
			
			
			//this has to be be evalutated immeditately as nothing will change we are just collecting a random value
			$i++;
			$find[$i]="#<random>([^<]*)</random>#eis";
			$replace[$i]='call_user_func(\'select_random\',\'$1\')';

			
			// this needs a second attempt before i work out a proper fix
			// the first removes the out random but if there is a nested random it wont work
			$i++;
			$find[$i]="#<random>(.*)</random>#eis";
			$replace[$i]='call_user_func(\'select_random\',\'$1\')';
			

			$i++;
			$find[$i]='#<li>(.*)</li>#i';
			$replace[$i]="\r\n".'else { $tmp_botsay .=\'$1\'; } ';	

			$i++;
			$find[$i]='#</li>#i';
			$replace[$i]='\'; } ';

			$i++;
			$find[$i]='#</condition>#i';
			$replace[$i]="\r\n".'$condition = ""; ';		

			//TODO WORK OUT WHY THIS OCCURES
			$i++;
			$find[$i]='#</conditio#i';
			$replace[$i]="\r\n".'$condition = ""; ';	

			$i++;
			$find[$i]='#<set name="([^"]*)">#i';
			$replace[$i]='\'.call_user_func(\'set_simple\',$convoArr,\'$1\',\'';		

			$i++;
			$find[$i]='#<set name=\\\"([^\\\]*)\\\">#i';
			$replace[$i]='\'.call_user_func(\'set_simple\',$convoArr,\'$1\',\'';		

			$i++;
			$find[$i]='#</set>#i';
			$replace[$i]='\').\'';		

			$i++;
			$find[$i]='#</get>#i';
			$replace[$i]='';		

			$i++;
			$find[$i]='#<system>\s?(add|power|sqrt|divide|multiply|subtract)\s?(.*)[\s?](.*)</system>#i';
			$replace[$i]='\'.call_user_func(\'run_system\',\'$1\',\'$2\',\'$3\').\'';


			$i++;	
			$find[$i]='#<learn>\s+?<category>\s+?<pattern>\s+?<eval>([^<]*)</eval>\s+?</pattern>\s+?<template>\s+?<eval>([^<]*)</eval>\s+?</template>\s+?</category>\s+?</learn>#ius';
			$replace[$i]='\'; call_user_func(\'make_learn\',$convoArr,\'$1\',\'$2\');';	
		
			
			runDebug( __FILE__, __FUNCTION__, __LINE__, "Built core tag array to make replacements",4);
			
			//custom tags handled here
			$custom_tag_handle = custom_aiml_to_phpfunctions($find,$replace,$i);
			
			$find = $custom_tag_handle['find'];
			$replace = $custom_tag_handle['replace'];
			
			runDebug( __FILE__, __FUNCTION__, __LINE__, "Built custom tag array to make replacements",4);
			runDebug( __FILE__, __FUNCTION__, __LINE__, "Looking to replace: ".print_r($find,true),4);
			runDebug( __FILE__, __FUNCTION__, __LINE__, "With replacements: ".print_r($replace,true),4);
		
			//actually do the find and replace here
			$parsed_template = preg_replace($find, $replace, $template);
			

		
			//clean up the find/replace code so that it can actaully evaluate as real php code
			$parsed_template = clean_for_eval($parsed_template,0);


			//decode back before sending
			$parsed_template = entity_replace('decode',$parsed_template);
			$parsed_template = foreignchar_replace('decode',$parsed_template);			
			
			
			//write to convoArr
			$convoArr['aiml']['aiml_to_php'] = $parsed_template;
			
			//update the aiml table
			$convoArr = add_aiml_to_php($convoArr);
	}

	
	
	//evaluate the generated code
	$botsay = eval_aiml_to_php_code($convoArr, $parsed_template); //if it works it works if not display error message
	
	//write the result (what the bot said) to the convoArr
	$convoArr['aiml']['parsed_template']=$botsay." ";
	
	runDebug( __FILE__, __FUNCTION__, __LINE__, "The bot will say: $botsay",2);
	

	return $convoArr;
}
?>