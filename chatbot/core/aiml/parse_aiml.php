<?php
/***************************************
* www.program-o.com
* PROGRAM O 
* Version: 2.0.9
* FILE: chatbot/core/aiml/parse_aiml.php
* AUTHOR: ELIZABETH PERREAU
* DATE: MAY 4TH 2011
* DETAILS: this file contains the functions used to convert aiml to php
***************************************/


/**
 * function get_formatted__date()
 * @param string $date_format - the date format code
 * @param string $date - the date to format
 * @return $formatted_date - the date
**/
function get_formatted_date($date_format,$date=""){
  $locale = setlocale(LC_ALL,"0");
  $x = setlocale(LC_TIME, $locale);
  $timestamp = (empty($date)) ? time() : strtotime($date);
  $suffix = array();
  for ($day = 1; $day <= 31; $day++) {
    switch ($day) {
      case 1:
      case 21:
      case 31:
        $suffix[$day] = "st";
        break;
      case 2:
      case 22:
        $suffix[$day] = "nd";
        break;
      case 3:
      case 23:
        $suffix[$day] = "rd";
        break;
      default:
        $suffix[$day] = "th";
    }
  }
    if ((strpos($date_format, "#sfx") !== false) and ((strpos($date_format, "%d") !== false) or (strpos($date_format, "%e") !== false))) {
      $curDay = (int)strftime("%d");
      $date_format = str_replace(" #sfx", $suffix[$curDay], $date_format);
      $date_format = str_replace("#sfx", $suffix[$curDay], $date_format);
    }
  #$timestamp = time();
  $formatted_date = strftime($date_format, $timestamp);
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Date $date ($date_format) formatted to $formatted_date",4);
	return $formatted_date;

}

/**
 * function transform_prounoun()
 * @param array $convoArr
 * @param int $person
 * @param string $value
 * @return the tranformed string
**/
function transform_prounoun($convoArr,$person,$value){

	runDebug( __FILE__, __FUNCTION__, __LINE__, "Will start the trasform pronoun process. Person: $person, Value: $value",4);
	$tmp = trim($value);
  	$tmp = swapPerson($convoArr,$person,$tmp); // The actual person transforms are now handled elsewhere.
	
	return $tmp;

}

/**
 * function buildVerbList()
 * @param array $convoArr
 * @param string $name
 * @param string $gender
**/
function buildVerbList ($name,$gender) {
	
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Building the verb list. Name:$name. Gender:$gender",4);
// person transform arrays:
    $firstPersonPatterns       = array();
    $firstPersonReplacements   = array();
    $secondPersonKeyedPatterns = array();
    $secondPersonPatterns      = array();
    $secondPersonReplacements  = array();
    $thirdPersonReplacements   = array();


    switch ($gender) {
      case "male":
        $g3 = "he";
        $tpWord = 'third';
        break;
      case "female";
        $g3 = "she";
        $tpWord = 'third';
        break;
      default:
        $g3 = "they";
        $tpWord = 'second';
  }

// Search and replacement templates - grouped in pairs/triplets
    // first to second/third
    $firstPersonSearchTemplate          = '/\bi [word]\b/i';
    $secondPersonKeyedReplaceTemplate   = 'y ou [word]';
    $thirdPersonReplaceTemplate         = "$g3 [word]";

    //second to first
    $secondPersonSearchTemplate         = '/\byou [word]\b/i';
    $firstPersonReplaceTemplate         = 'I [word]';

    //second (reversed) to first
    $secondPersonSearchTemplateReversed = '/\b[word] you\b/i';
    $firstPersonReplaceTemplateReversed = '[word] @II';

    $secondPersonKeyedSearchTemplate    = '/\by ou [word]\b/i';
    $secondPersonReplaceTemplate        = 'you [word]';

	//the list of verbs is stored in the config folder
 	$file =  _CONF_PATH_."verbList.dat";
    $verbList = file($file, FILE_USE_INCLUDE_PATH | FILE_SKIP_EMPTY_LINES);//or exit("<br>Unable to open tr");
 
    sort($verbList);
    //  fill the arrays
    foreach ($verbList as $line) {
      $line = rtrim($line, "\r\n");
      #print "line = |$line|<br />\n";
      if (empty($line)) continue;
      $firstChar = substr($line, 0, 1);
      if ($firstChar === '#') continue;
      if ($firstChar === '$') {
        $words = str_replace('$ ', '', $line);
        list ($first, $third) = explode(', ', $words);
        $second = $first;
      }
      elseif ($firstChar === '~') {
        $words = str_replace('~ ', '', $line);
        $first = $words;
        $second = $first;
        $third = $first;
      }
      else {
        list ($first, $second, $third) = explode(', ', $line);
      }
      // build first patterns to second (both keyed and non) patterns and replacements, and first patterns to third replacements
      $firstPersonPatterns[]        = str_replace('[word]', $first,  $firstPersonSearchTemplate);
      $secondPersonKeyedPatterns[]  = str_replace('[word]', $second, $secondPersonKeyedSearchTemplate);
      #$secondPersonKeyedPatterns[] = str_replace('[word]', $second, $secondPersonSearchTemplateReversed);
      $secondPersonReplacements[]   = str_replace('[word]', $first, $secondPersonReplaceTemplate);
      #$secondPersonReplacements[]  = str_replace('[word]', $first, $firstPersonReplaceTemplateReversed);
      $thirdPersonReplacements[]    = str_replace('[word]', $tpWord,  $thirdPersonReplaceTemplate);
      // build second patterns to first replacements - reversed (e.g. "would I") first
      $secondPersonPatterns[]       = str_replace('[word]', $second, $secondPersonSearchTemplate);
      $firstPersonReplacements[]    = str_replace('[word]', $first,  $firstPersonReplaceTemplate);
      // build first patterns to third replacements
    }
    $_SESSION['verbList'] = true;
// debugging - Let's see what the contents of the files are!
    $transformList = array(
                       'firstPersonPatterns'=> $firstPersonPatterns,
                       'secondPersonKeyedPatterns'=> $secondPersonKeyedPatterns,
                       'secondPersonReplacements'=> $secondPersonReplacements,
                       'thirdPersonReplacements'=> $thirdPersonReplacements,
                       'secondPersonPatterns'=> $secondPersonPatterns,
                       'firstPersonReplacements'=> $firstPersonReplacements
                      );
    foreach ($transformList as $transform_index => $transform_value) {
      $_SESSION['transform_list'][$transform_index] = $transform_value;
    }
  }


/**
 * function swapPerson()
 * @param array $convoArr
 * @param int $person
 * @param string $in
 * @return the tranformed string
**/
function swapPerson($convoArr,$person =2, $in) { //2 = swap first with second poerson // otherwise swap with third person
 
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Person:$person In:$in",4);
 
    $name = get_convo_var($convoArr,'client_properties','name');
    $gender = get_convo_var($convoArr,'client_properties','gender');
 
  	$tmp = trim($in);
  
  	if ((!isset($_SESSION['transform_list']))|| ($_SESSION['transform_list'] == NULL ) ){
		buildVerbList($name,$gender);
	}

	switch ($gender) {
    	case "male":
    		$g1 = "his";
    		$g2 = "him";
		    $g3 = "he";
		    break;
		case "female";
		    $g1 = "hers";
		    $g2 = "her";
		    $g3 = "she";
		    break;
		default:
		    $g1 = "theirs";
		    $g2 = "them";
		    $g3 = "they";
  	}

// the "simple" transform arrays - more for exceptions to the above rules than for anything "simple" :)
  $simpleFirstPersonPatterns = array(
                    '/(\bi am\b)/i',
                    '/(\bam i\b)/i',
                    '/(\bi\b)/i',
                    '/(\bmy\b)/i',
                    '/(\bmine\b)/i',
                    '/(\bmyself\b)/i',
                    '/(\bcan i\b)/i'
                    );
  $simpleSecondPersonKeyedReplacements = array(
                    'you are',
                    'are you',
                    'you',
                    'your',
                    'yours',
                    'yourself',
                    'can you'
                    );
  $simpleFirstToThirdPersonPatterns = array(
                    '/(\bi am\b)/i',
                    '/(\bam i\b)/i',
                    '/(\bi\b)/i',
                    '/(\bmy\b)/i',
                    '/(\bmine\b)/i',
                    '/(\bmyself\b)/i',
                    '/(\bwill i\b)/i',
                    '/(\bshall i\b)/i',
                    '/(\bmay i\b)/i',
                    '/(\bmight i\b)/i',
                    '/(\bcan i\b)/i',
                    '/(\bcould i\b)/i',
                    '/(\bmust i\b)/i',
                    '/(\bshould i\b)/i',
                    '/(\bwould i\b)/i',
                    '/(\bneed i\b)/i',
                    '/(\bam i\b)/i',
                    '/(\bwas i\b)/i',
                    );
  $simpleThirdPersonReplacements = array(
                    "$g3 is",
                    "is $g3",
                    "$g3",
                    "$g1",
                    "$g1",
                    "$g2".'self',
                    'will '.$g3,
                    'shall '.$g3,
                    'may '.$g3,
                    'might '.$g3,
                    'can '.$g3,
                    'could '.$g3,
                    'must '.$g3,
                    'should '.$g3,
                    'would '.$g3,
                    'need '.$g3,
                    'is '.$g3,
                    'was '.$g3,
                    );
  $simpleSecondPersonPatterns = array(
                    '/(\bhelp you\b)/i',
                    '/(\bwill you\b)/i',
                    '/(\bshall you\b)/i',
                    '/(\bmay you\b)/i',
                    '/(\bmight you\b)/i',
                    '/(\bcan you\b)/i',
                    '/(\bcould you\b)/i',
                    '/(\bmust you\b)/i',
                    '/(\bshould you\b)/i',
                    '/(\bwould you\b)/i',
                    '/(\bneed you\b)/i',
                    '/(\bare you\b)/i',
                    '/(\bwere you\b)/i',
                    '/(\byour\b)/i',
                    '/(\byours\b)/i',
                    '/(\byourself\b)/i',
                    '/(\bthy\b)/i'
                    );
# will, shall, may, might, can, could, must, should, would, need
  $simpleFirstPersonReplacements = array(
                    'help m e',
                    'will @II',
                    'shall @II',
                    'may @II',
                    'might @II',
                    'can @II',
                    'could @II',
                    'must @II',
                    'should @II',
                    'would @II',
                    'need @II',
                    'am @II',
                    'was @II',
                    'my',
                    'mine',
                    'myself',
                    'my'
                    );

  if ($person == 2) {
    $tmp = preg_replace('/\bare you\b/i', 'am @II', $tmp);                                       // simple second to first transform
    $tmp = preg_replace('/\byou and i\b/i', 'y ou and @II', $tmp);                               // fix the "Me and you" glitch
    $tmp = preg_replace($simpleSecondPersonPatterns, $simpleFirstPersonReplacements, $tmp);      // "simple" second to keyed first transform
    $tmp = preg_replace($simpleFirstPersonPatterns, $simpleSecondPersonKeyedReplacements, $tmp); // simple first to keyed second transform
    $tmp = preg_replace($_SESSION['transform_list']['secondPersonPatterns'], $_SESSION['transform_list']['firstPersonReplacements'], $tmp);                  // second to first transform
    $tmp = preg_replace('/\bme\b/i', 'you', $tmp);                                              // simple second to first transform (me)
    #$tmp = preg_replace('/\bi\b/i', 'y ou', $tmp);                                              // simple second to first transform (I)
    $tmp = preg_replace('/\byou\b/i', 'me', $tmp);                                               // simple second to first transform
    $tmp = str_replace('you', 'you', $tmp);                                                     // replace second person key (y ou) with non-keyed value (you)
    $tmp = str_replace(' me', ' me', $tmp);                                                     // replace first person key (m e) with non-keyed value (me)
    $tmp = str_replace(' my', ' my', $tmp);                                                     // replace first person key (m e) with non-keyed value (me)
    $tmp = str_replace('my ', 'my ', $tmp);                                                     // replace first person key (m e) with non-keyed value (me)
    $tmp = str_replace(' mine', ' mine', $tmp);                                                 // replace first person key (m e) with non-keyed value (me)
    $tmp = str_replace('mine ', 'mine ', $tmp);                                                 // replace first person key (m e) with non-keyed value (me)
    $tmp = str_replace(' @II ', ' I ', $tmp);                                                    // replace first person key (@I) with non-keyed value (I)
    $tmp = str_replace('@II ', 'I ', $tmp);                                                    // replace first person key (@I) with non-keyed value (I)
    $tmp = str_replace(' @II', ' I', $tmp);                                                    // replace first person key (@I) with non-keyed value (I)
    $tmp = str_replace('@you', 'I', $tmp);                                                       // replace first person key (@I) with non-keyed value (I)
    #$tmp = ucfirst(  $tmp);
  }
elseif ($person == 3) {


    $tmp = preg_replace($_SESSION['transform_list']['firstPersonPatterns'], $_SESSION['transform_list']['thirdPersonReplacements'], $tmp);                   // first to third transform, but only when specifically needed
      $tmp = preg_replace('/(\byour gender\b)/i',$g3,$tmp);
      $tmp = preg_replace('/(\bthey\b)/i',$g3,$tmp);
       $tmp = preg_replace('/(\bi\b)/i',$g3,$tmp);
       $tmp = preg_replace('/(\bme\b)/i',$g3,$tmp);
      
  }


  //debug
 // if (RUN_DEBUG) runDebug(4, __FILE__, __FUNCTION__, __LINE__,"<br>\nTransformation complete. was: $in, is: $tmp");
  return $tmp;
  //return

}

/**
 * function parse_matched_aiml()
 * This function controls and triggers all the functions to parse the matched aiml
 * @param array $convoArr - the conversation array
 * @param string $type - normal or srai
 * @return array $convoArr - the updated conversation array
**/
function parse_matched_aiml($convoArr,$type="normal")
{
	//which debug mode?
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Run the aiml parse in $type mode (normal or srai)",3);
	$convoArr = expand_shorthand_to_longhand($convoArr);
	$convoArr = set_wildcards($convoArr);
	$convoArr = aiml_to_phpfunctions($convoArr);

	if($type!="srai"){
		runDebug( __FILE__, __FUNCTION__, __LINE__, "$type - Saving for next turn",4);
		$convoArr = save_for_nextturn($convoArr);
	} else {
		runDebug( __FILE__, __FUNCTION__, __LINE__, "$type - Not saving for next turn",4);
	}
	return $convoArr;
}

/**
 * function clean_that()
 * This function cleans the 'that' of html and other bits and bobs
 * @param string $that - the string to clean
 * @return string $that - the cleaned string
**/
function clean_that($that)
{
	$in = $that;
	$that = str_replace("<br/>",".",$that);
	$that = strip_tags($that);
	$that= remove_allpuncutation($that);
	$that= whitespace_clean($that);
	$that= captialise($that);
	
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Cleaning the that - that: $in cleanthat:$that",4);
	
	return $that;
}

/**
 * function save_for_nextturn()
 * This function puts the bot results of an srai search into the main convoArr
 * @param array $convoArr - the conversation array
 * @return array $convoArr - the updated conversation array
**/
function save_for_nextturn($convoArr)
{
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Saving that and that_raw for next turn",4);
	
	$savethis = get_convo_var($convoArr,'aiml','parsed_template');
	
	$convoArr = push_on_front_convoArr('that_raw',$savethis,$convoArr);
	$convoArr = push_on_front_convoArr('that',$savethis,$convoArr);
	return $convoArr;
}

/**
 * function set_wildcards()
 * This function extracts wildcards from a patterns and puts their values in their associated array
 * @param array $convoArr - the conversation array
 * @return array $convoArr - the updated conversation array
**/
function set_wildcards($convoArr)
{
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Setting Wildcards",4);
	
	$aiml_pattern = get_convo_var($convoArr,'aiml','pattern');
	
	
	
	$ap = $aiml_pattern;	
	$ap = str_replace("+","\+",$ap);
	$ap = str_replace("*","(.*)",$ap);
	$ap = str_replace("_","(.*)",$ap);
	
	$wildcards = str_replace("_","(.*)?",str_replace("*","(.*)?",$aiml_pattern));
	
	if($wildcards!=$aiml_pattern)
	{
		if(!isset($convoArr['aiml']['user_raw'])) {
			$checkagainst = $convoArr['aiml']['lookingfor'];
		} else {
			$checkagainst = $convoArr['aiml']['user_raw'];
		}
		
		if (preg_match('/'.$ap.'/si', $checkagainst, $matches,PREG_OFFSET_CAPTURE)) {
			runDebug( __FILE__, __FUNCTION__, __LINE__, print_r($matches,true),4);
	    	$totalStars = count($matches)-1;
			for($i = $totalStars; $i>=1; $i--){
					$convoArr = push_on_front_convoArr('star',$matches[$i][0],$convoArr);
				}
			}
	}
	return $convoArr;
}

/**
 * function expand_shorthand_to_longhand()
 * This function replaces the annotated aiml with the long hand version
 * and also makes some changes to the original aiml to make it program o friendly
 * @param array $convoArr - the conversation array
 * @return array $convoArr - the updated conversation array
**/
function expand_shorthand_to_longhand($convoArr)
{
	global $allowed_html_tags;
	
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Expanding shorthand to longhand",4);
	
	$template = $convoArr['aiml']['template'];
	$convoArr['aiml']['shorthand_template']=htmlentities($template);
	
	$template = trim($template);
	
	$i=0;
	$find[$i]='#index="([0-9]*),(\*)"#';
	$replace[$i]='index="$1,all"';
	
	$i++;
	$find[$i]='#<srai>PUSH(.*)</srai>#';
	$replace[$i]='<pushstack>$1</pushstack>';

	$i++;
	$find[$i]='#\s|\s+#';
	$replace[$i]=' ';

	$i++;
	$find[$i]='#<star([^<]*)>#';
	$replace[$i]=' <star$1> ';

	
	$template = preg_replace($find, $replace, $template);	
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Completed preg_replace expansion: ".htmlentities($template),4);


	//TODO not sure if this is correct implementation of star if lots of problems will see about making changes in future releases
	$template = str_replace("<topicstar","<star",$template);
	$template = str_replace("<thatstar","<star",$template);
	
	$template = str_replace("value=\"*\"","value=\"<star index=\"1\"/>\"",$template);
	$template = str_replace(" />","/>",$template);
	$template = str_replace("<date/>","<date format=\"%c\">",$template);
	$template = str_replace("<sr/>","<srai><star/></srai>",$template);
	$template = str_replace("<person2/>","<person2><star/></person2>",$template);
	$template = str_replace("<person/>","<person><star/></person>",$template);
	$template = str_replace("<gender/>","<gender><star/></gender>",$template);
	
	$template = str_replace("<star>","<star/>",$template);
	$template = str_replace('<star/><star index="2"/>','<star/> <star index="2"/>',$template);
	$template = str_replace("<star/>","<star index=\"1\"/>",$template);
	$template = str_replace("<birthday/>","<bot name=\"birthday\"/>",$template);
	$template = str_replace("<birthplace/>","<bot name=\"birthplace\"/>",$template);
	$template = str_replace("<boyfriend/>","<bot name=\"boyfriend\"/>",$template);
	$template = str_replace("<favoriteband/>","<bot name=\"favoriteband\"/>",$template);
	$template = str_replace("<favoritebook/>","<bot name=\"favoritebook\"/>",$template);
	$template = str_replace("<favoritecolor/>","<bot name=\"favoritecolor\"/>",$template);
	$template = str_replace("<favoritefood/>","<bot name=\"favoritefood\"/>",$template);
	$template = str_replace("<favoritemovie/>","<bot name=\"favoritemovie\"/>",$template);
	$template = str_replace("<favoritesong/>","<bot name=\"favoritesong\"/>",$template);
	$template = str_replace("<favroritemovie/>","<bot name=\"favroritemovie\"/>",$template);
	$template = str_replace("<for_fun/>","<bot name=\"forfun\"/>",$template);
	$template = str_replace("<friends/>","<bot name=\"friends\"/>",$template);
	$template = str_replace("<gender/>","<bot name=\"gender\"/>",$template);
	$template = str_replace("<girlfriend/>","<bot name=\"girlfriend\"/>",$template);
	$template = str_replace("<kind_music/>","<bot name=\"kindmusic\"/>",$template);
	$template = str_replace("<location/>","<bot name=\"location\"/>",$template);
	$template = str_replace("<look_like/>","<bot name=\"looklike\"/>",$template);
	$template = str_replace("<botmaster/>","<bot name=\"master\"/>",$template);
	$template = str_replace("<question/>","<bot name=\"question\"/>",$template);
	$template = str_replace("<sign/>","<bot name=\"sign\"/>",$template);
	$template = str_replace("<talk_about/>","<bot name=\"talkabout\"/>",$template);
	$template = str_replace("<wear/>","<bot name=\"wear\"/>",$template);
	$template = str_replace("<id/>","<get name=\"id\"/>",$template);
	$template = str_replace("<size/>","<bot name=\"size\"/>",$template);
	$template = str_replace("<version/>","<bot name=\"version\"/>",$template);	
	$template = str_replace("<input/>","<input index=\"1\"/>",$template);
	$template = str_replace("<that/>","<that index=\"1,1\"/>",$template);	
	$template = str_replace("<srai>POP</srai>","<popstack></popstack>",$template);		
	$template = str_replace("<![CDATA[","</say><say>",$template);
	$template = str_replace("]]>","</say><say>",$template);	

	runDebug( __FILE__, __FUNCTION__, __LINE__, "Completed str_replace expansion: ".htmlentities($template),4);
	
	//this might actually have no aiml in it all so lets check.
	if(substr($template, 0, 1)!="<") 
	{
		$template = "<say>$template</say>";
	}	
	else 
	{
		$htmls = implode("|",$allowed_html_tags);
		//$matches = preg_match_all("# ?(([^\.\?!]*)+(?:[\.\?!]|(?:<br ?/))*)#ui",$value,$sentances);
		$matches = preg_match_all("#".$htmls."#ui",$template,$tags);
		if($matches > 0)
		{
			$chktag = trim($tags[0][0]);
			$len = strlen($chktag);
			if(substr(trim($template), 0, $len)==$chktag) 
			{
				$template = "<say>$template</say>";
			}	
		}
	}
	$template = trim($template);
	$ex = explode("<",$template);
	$ex = remove_nulls_from_array($ex);
	
	if((trim($ex[0])=="think>") && (trim($ex[count($ex)-1])=="/think>"))
	{
		$template = rtrim($template,"</think>");
		$template = ltrim($template,"<think>");
		$template .= "<bigthink></bigthink>";
	}
	
	if(trim($ex[0])=="think>")
	{
		$template = str_replace_once("<think>" , "" , $template);
		$template = str_replace_once("</think>" , "<bigthink></bigthink> <say>" , $template);
		$template .= "</say>";
		$template = str_replace_once("<bigthink></bigthink> <say> </think>" , "</think><bigthink></bigthink> <say> " , $template);
	}

	//$template = preg_replace("#<say>([^<]*)<#si","<say>$1</say><",$template);	
	$template = str_replace("</say></say>","</say>",$template);	
	
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Completed Program O specific: ".htmlentities($template),4);
	
	$convoArr['aiml']['template']=$template;
	$convoArr['aiml']['longhand_template']=htmlentities($template);	
	return $convoArr;
}


/**
 * function str_replace_once()
 * This function replaces the first occurence of $needle in $haystack
 * @param string $needle - look for this
 * @param $replace - replace with this
 * @param $haystack - look in this
 * @return string - the replaced string
**/
function str_replace_once($needle , $replace , $haystack){
	
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Replacing $needle in $haystack with $replace",4);
	
    $pos = strpos($haystack, $needle);
    if ($pos === false) {
        // Nothing found
    return $haystack;
    }
    return substr_replace($haystack, $replace, $pos, strlen($needle));
} 


/**
 * function make_null()
 * This function removes a string
 * @param string $c - the string to remove
 * @return "" - to overwrite the sent string
**/
function make_null($c)
{
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Deleting $c from string",4);
	return "";
}

/**
 * function set_simple()
 * This function sets simple client properties
 * @param array $convoArr - a reference to the existing conversation array
 * @param string $index - the array index to set 
 * @param string $value - the value of the array index
 * @return string $value - the value of the array index
**/
function set_simple(&$convoArr,$index,$value)
{
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Setting simple value $index to equal $value",4);

	if($index=="topic")
	{
		$convoArr = push_on_front_convoArr('topic',$value,$convoArr);
	}
	else {
		
		$value = clean_client_properties($value);
		
		$convoArr['client_properties'][$index]=$value;
		
	}
	return $value." ";
}

/**
 * function clean_client_properties()
 * When storing a client's properties we dont need extra bits and bobs they may have added
 * e.g. name is elizabeth.... should store elizabeth not elizabeth....
 * so lets clean them here
 */
function clean_client_properties($value)
{
	//remove lots of occurances of things
	$value = preg_replace('/\.+/i', '.', $value);
	$value = preg_replace('/\!+/i', '!', $value);
	$value = preg_replace('/\?+/i', '?', $value);
	$value = preg_replace('/\s+/i', ' ', $value);
	
	$value = trim($value,'!');
	$value = trim($value,'?');
	$value = trim($value,'.');
	$value = trim($value);
	
	
	return $value;
}



/**
 * function select_random()
 * This function is given a string of <li>'s it is then converted into PHP code so that a random option can be selected
 * @param string $random_options - a string of <li>this</li><li>that</li> options to be made into an array and PHP code
 * @return array $array - the clean array
**/
function select_random($random_options)
{
	//echo "HERE WITH ".htmlentities($random_options);
	//initialise php code string
	$str = "";
	//check if there is another random in here
	$find="#<random>(.*)</random>#is";
	
	if(preg_match($find, $random_options, $matches))
	{
		//found a match and recall this function
		$replace = select_random($matches[1]);
		$find="#<random>(.*)</random>#is";
		
		$random_options = preg_replace($find,$replace,$random_options);
	}
	
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Building an array to select random option from",4);
	
	$arrayname = "$".get_random_str();

	//split up the <li>'s
	$random_options = preg_split('#<li>|</li>#',$random_options);
	//remove any blanks from array
	$random_options = remove_nulls_from_array($random_options);
	//count the total options
	$mx= count($random_options)-1;
	//add to php code string
	$str .= "'; \r\n\r\n$arrayname = rand(0,$mx);\r\n\r\n";	
	
	//build big if else so that we can select a random options later
	foreach($random_options as $index => $value)
	{
		if($index==0)
		{
			$str .= "if(".$arrayname."=='".$index."'){\r\n\t\$tmp_botsay.= '".$value."'; }\r\n";
		}
		elseif((count($random_options)-1)==$index)
		{
			$str .= "else{\r\n\t\$tmp_botsay.= '". $value ."'; }\r\n\r\n";
			$str .= "\$tmp_botsay .= '";
		}
		else
		{
			$str .= "elseif(".$arrayname."=='".$index."'){\r\n\t\$tmp_botsay.= '".$value."'; }\r\n";
		}
	}
	//return the php code string
	
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Built PHP code string so that we can select option later: $str",4);
	
	return $str;
}
	
/**
 * function remove_nulls_from_array()
 * This function is just a little cleaning function to remove nulls from a given array
 * @param array $array - the array to clean
 * @return array $array - the clean array
**/
function remove_nulls_from_array($array)
{
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Removing nulls from array",4);	
	$newArray=array();
	foreach($array as $index => $value)
	{
		if(($value != NULL) && (trim($value)!=""))
		{
			$newArray[]=$value;
		}
	}
	
	return $newArray;
}

/**
 * function run_srai()
 * This function controls the SRAI recursion calls
 * @param array $convoArr - a reference to the existing conversation array
 * @param string $now_look_for_this - the text to search for
 * @return string $srai_parsed_template - the result of the search
**/
function run_srai(&$convoArr,$now_look_for_this)
{
	global $srai_iterations, $offset,$error_response;
	
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Running SRAI $srai_iterations on $now_look_for_this",3);	
	runDebug( __FILE__, __FUNCTION__, __LINE__, $convoArr['aiml']['html_template'],4);	
	//number of srai iterations - will stop recursion if it is over 10
	$srai_iterations++;
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Incrementing srai iterations to $srai_iterations",4);			
	if($srai_iterations>10){
		runDebug( __FILE__, __FUNCTION__, __LINE__, "ERROR - Too much recursion breaking out",1);
		$convoArr['aiml']['parsed_template']=$error_response;
		return $error_response;
	}
	
	$tmp_convoArr = array();
	$tmp_convoArr = $convoArr;

	$tmp_convoArr['aiml']=array();
	$tmp_convoArr['that'][$offset][$offset]=""; //added
	$tmp_convoArr['aiml']['parsed_template']="";
	$tmp_convoArr['aiml']['lookingfor']=$now_look_for_this;

	$tmp_convoArr = get_aiml_to_parse($tmp_convoArr);
	$tmp_convoArr = parse_matched_aiml($tmp_convoArr,"srai");

	$srai_parsed_template = $tmp_convoArr['aiml']['parsed_template'];
	
	runDebug( __FILE__, __FUNCTION__, __LINE__, "SRAI Found: '$srai_parsed_template'",2);
	
	$convoArr['client_properties'] = $tmp_convoArr['client_properties'];
	$convoArr['topic'] = $tmp_convoArr['topic'];
	$convoArr['stack'] = $tmp_convoArr['stack'];
	

	$srai_iterations--;
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Decrementing srai iterations to $srai_iterations",4);	
	return $srai_parsed_template." ";
	
}



/**
 * function format()
 * This function formats a given text
 * @param string $format - the format
 * @param string $texttoformat - the text to format
 * @return string $texttoformat - the formated text
**/
function format($format,$texttoformat)
{
	switch($format)
	{
		case "uppercase":
			$texttoformat = strtoupper($texttoformat);
			break;
		case "lowercase":
			$texttoformat = strtolower($texttoformat);
			break;		
		case "formal":
			$texttoformat = ucwords($texttoformat);
			break;
		case "sentence":
			$texttoformat = ucfirst($texttoformat);
			break;	
	}
	
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Formated the text to: $texttoformat",4);
	
	return $texttoformat;
}

/**
 * function url_encode_star()
 * This function encode the star value to make it safe for web addresses
 * @param array $convoArr - conversation array
 * @return string $encoded_star - the encoded string
**/
function url_encode_star($convoArr)
{
	
	$encoded_star = urlencode(get_convo_var($convoArr,'star'));
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Urlencoded the string to: $encoded_star",4);
	return $encoded_star;
}


/**
 * function push_stack()
 * This function pushes an item on to the stack
 * @param array $convoArr - conversation array
 * @param string $item - the item to push
 * @return string $item - the pushed item
**/
function push_stack(&$convoArr,$item)
{
	if((trim($item))!=(trim($convoArr['stack']['top']))){
		runDebug( __FILE__, __FUNCTION__, __LINE__, "Pushing $item onto to the stack",4);	
		$convoArr['stack']['last'] = $convoArr['stack']['seventh'];
		$convoArr['stack']['seventh'] = $convoArr['stack']['sixth'];
		$convoArr['stack']['sixth'] = $convoArr['stack']['fifth'];
		$convoArr['stack']['fifth'] = $convoArr['stack']['fourth'];
		$convoArr['stack']['fourth'] = $convoArr['stack']['third'];
		$convoArr['stack']['third'] = $convoArr['stack']['second'];
		$convoArr['stack']['second'] = $convoArr['stack']['top'];
	    $convoArr['stack']['top'] = $item;
	}else{
		runDebug( __FILE__, __FUNCTION__, __LINE__, "Could not push empty item onto to the stack",1);	
	} 
	
    return $item;
}


/**
 * function pop_stack()
 * This function pops an item off the stack
 * @param array $convoArr - conversation array
 * @return string $item - the popped item
**/
function pop_stack(&$convoArr)
{
	$item = trim($convoArr['stack']['top']);
	
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Popped $item off the stack",4);
	
	$convoArr['stack']['top']  = $convoArr['stack']['second'];
	$convoArr['stack']['second'] = $convoArr['stack']['third'];
    $convoArr['stack']['third'] = $convoArr['stack']['fourth'];
    $convoArr['stack']['fourth'] = $convoArr['stack']['fifth'];
  	$convoArr['stack']['fifth'] = $convoArr['stack']['sixth'];
    $convoArr['stack']['sixth'] = $convoArr['stack']['seventh'];
    $convoArr['stack']['seventh'] = $convoArr['stack']['last'];
    $convoArr['stack']['last'] = "om";
    return $item;			
}

/**
 * function make_learn()
 * This function builds the sql insert a learnt aiml cateogry in to the db
 * @param array $convoArr - conversation array
 * @param string $pattern - the pattern we will insert
 * @param string $template - the template to insert
**/
function make_learn($convoArr, $pattern, $template)
{
	global $con,$dbn;

	runDebug( __FILE__, __FUNCTION__, __LINE__, "Making learn",2);
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Pattern:  $pattern",2);
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Template: $template",2);
	
	$pattern = clean_for_aiml_match($pattern);
	$aiml = "<learn> <category> <pattern> <eval>$pattern</eval> </pattern> <template> <eval>$template</eval> </template> </category> </learn>";
	$aiml = mysql_real_escape_string($aiml);
	$pattern = mysql_real_escape_string($pattern." "); 
	$template = mysql_real_escape_string($template." "); 
	$u_id = $convoArr['conversation']['user_id'];
	$bot_id = $convoArr['conversation']['bot_id'];
	
	$sql = "INSERT INTO `$dbn`.`aiml_userdefined` 
				VALUES
				(NULL, '$aiml','$pattern','$template','$u_id','$bot_id',NOW())";
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Make learn SQL: $sql",3);
	$res = mysql_query($sql,$con);
}

/**
 * function run_system()
 * This function runs the system math operations
 * @param char $operator - maths operator
 * @param int $num_1 - the first number
 * @param int $num_2 - the second number
 * @param int $output - the result of the math operation
**/
function run_system($operator,$num_1,$num_2="")
{
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Running system tag math $num_1 $operator $num_2",4);
	
	switch (strtolower($operator)) {
		case "add":
			$output = $num_1 + $num_2;
			break;
		case "subtract":
			$output = $num_1 - $num_2;
			break;
		case "multiply":
			$output = $num_1 * $num_2;
			break;
		case "divide":
    		if ($num_2 == 0) {
    			$output = "You can't divide by 0!";
    		} else {
	    		$output = $num_1 / $num_2;
	    	}
			break;
		case "sqrt":
			$output = sqrt($num_1);
			break;
		case "power":
			$output = pow($num_1, $num_2);
			break;
		default:
			$output = $operator."?";
	}

	return $output;
	
}
?>