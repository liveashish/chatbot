<?php
/***************************************
* http://www.program-o.com
* PROGRAM O 
* Version: 2.0.9
* FILE: library/error_functions.php
* AUTHOR: ELIZABETH PERREAU
* DATE: MAY 4TH 2011
* DETAILS: common library of debugging functions
***************************************/


/**
 * function myErrorHandler()
 * Process PHP errors
 * @param string $errno - the severity of the error 
 * @param  string $errstr - the file the error came from
 * @param  string $errfile - the file the error came from
 * @param  string $errline - the line of code
**/
function myErrorHandler($errno, $errstr, $errfile, $errline) {
    switch ($errno) {
        case E_NOTICE:
        case E_USER_NOTICE:
            $errors = "Notice";
            break;
        case E_WARNING:
        case E_USER_WARNING:
            $errors = "Warning";
            break;
        case E_ERROR:
        case E_USER_ERROR:
            $errors = "Fatal Error";
            break;
        default:
            $errors = "Unknown";
            break;
        }

    $info = "PHP ERROR [$errors] -$errstr in $errfile on Line $errline";
    
    //a littl hack to hide the pass by reference errors of which there may be a few
    if($errstr != "Call-time pass-by-reference has been deprecated")    
    {
    	runDebug($errfile, '', $errline, $info, 1);    
    }

}


/**
 * function sqlErrorHandler()
 * Process sql errors
 * @param  string $fileName - the file the error came from
 * @param  string $functionName - the function that triggered the error
 * @param  string $line - the line of code
 * @param  string $sql - the sql query
 * @param  string $error - the mysql_error
 * @param  string $erno - the mysql_error
**/
function sqlErrorHandler( $sql, $error, $erno, $file, $function, $line){
    $info = "MYSQL ERROR $erno - $error when excuting\n $sql";
	runDebug($file, $function, $line, $info, 1);
}


/**
 * function runDebug()
 * Building to a global debug array
 * @param  string $fileName - the file the error came from
 * @param  string $functionName - the function that triggered the error
 * @param  string $line - the line of code
 * @param  string $info - the message to display
**/
function runDebug($fileName, $functionName, $line, $info, $level=0) {
  
	global $debugArr,$srai_iterations,$debuglevel,$quickdebug,$writetotemp;
	if(empty($functionName)) $functionName = "Called outside of function";
	//only log the debug info if the info level is equal to or less than the chosen level
	if(($level<=$debuglevel)&&($level!=0)&&($debuglevel!=0))
    {
		if($quickdebug==1)
		{
			outputDebug($fileName, $functionName, $line, $info);
		}

		list($usec, $sec) = explode(" ",microtime());

		//build timestamp index for the debug array
		$index = date("d-m-Y H:i:s").ltrim($usec, '0');
		//add to array
		$debugArr[$index]['fileName']=basename($fileName);
		$debugArr[$index]['functionName']=$functionName;
		$debugArr[$index]['line']=$line;
		$debugArr[$index]['info']=$info;
		
		if($srai_iterations<1)
        {
			$sr_it = 0;}
		else {
			$sr_it = $srai_iterations;}
		
		$debugArr[$index]['srai_iteration']=$sr_it;
		
		//if we are logging to file then build a log file. This will be overwriten if the program completes
		if($writetotemp==1)
		{	
			writefile_debug($debugArr);
		}
	}
	
	//return $debugArr;
}

/**
 * function sort2DArray()
 * Small helper function to sort a 2d array
 * @param string $opName - name of this operation i.e. show top scored aiml
 * @param array $thisArr - the array to sort
 * @param $sortByItem - the array field to sort by
 * @param $sortAsc - 1 = ascending order, 0 = descending
 * @param $limit - the number of results to return
 * @return void;
 **/
function sort2DArray($opName,$thisArr,$sortByItem, $sortAsc=1,$limit=10)
{
	runDebug( __FILE__, __FUNCTION__, __LINE__, "$opName - sorting ".count($thisArr)." results and getting the top $limit for debugging",4);
	
	$i=0;
	
	$tmpSortArr = array();
	$resArr = array();
	$last_high_score= 0;
	
	//loop through the results and put in tmp array to sort
	foreach($thisArr as $all => $subrow){
		
		if(isset($subrow[$sortByItem]))
		{$tmpSortArr[$subrow[$sortByItem]]=$subrow[$sortByItem];}
	}	

	//sort the results
	if($sortAsc==1) { //ascending
		krsort($tmpSortArr);
	}
	else { //descending
		ksort($tmpSortArr);
	}
	//loop through scores
	foreach($tmpSortArr as $sortedKey => $idValue){
		//no match against orig res arr
		foreach($thisArr as $i => $subArr){
			if(isset($subrow[$sortByItem]))
			{
				if(( (string)$subArr[$sortByItem]==(string)$idValue)) {
					$resArr[]=$subArr;
				}
			}
		}
	}
	
	//get the limited top results
	$outArr = array_slice($resArr, 0, $limit);   
	//send to debug
	runDebug( __FILE__, __FUNCTION__, __LINE__, "$opName ". print_r($outArr, true),3);
	
	
}



/**
 * function handleDebug()
 * Handle the debug array at the end of the process
 * @param  array $convoArr - conversation arrau
 * @return array $convoArr;
 * TODO THIS MUST BE IMPLMENTED
**/
function handleDebug($convoArr){
	
	global $debugArr;
	$convoArr['debug']=$debugArr;
	$log = '';
	
	foreach($debugArr as $time => $subArray){
		$log .= $time."[NEWLINE]";
		foreach($subArray as $index=>$value){
			
			
			
			
			if(($index == "fileName") || ($index == "functionName") || ($index == "line"))
			{
				$log .= "[".$value."]";
			} 
			elseif($index == "info")
			{
				$log .= "[NEWLINE]".$value."[NEWLINE]-----------------------[NEWLINE]";
			}
		
			
			
		}
	}
	
	
	$log .= "[NEWLINE]-----------------------[NEWLINE]";
	$log .= "CONVERSATION ARRAY";
	$log .= "[NEWLINE]-----------------------[NEWLINE]";
	
	$debuglevel = get_convo_var($convoArr, 'conversation', 'debugshow', '', '');

	if($debuglevel == 4 )
	{
		//show the full array
		$showArr = $convoArr;
		unset($showArr['debug']);
	}
	else
	{
		//show a reduced array
		$showArr = reduceConvoArr($convoArr);
	}
	
	
	$log .= print_r($showArr,true);
	
	
	
	
	switch($convoArr['conversation']['debugmode']){
		case 0: //show in source code
			$log = str_replace("[NEWLINE]","\r\n",$log);
			display_on_page(0,$log);
			break;
		case 1: //write to log file
			$log = str_replace("[NEWLINE]","\r\n",$log);
			writefile_debug($log);
			break;
		case 2: //show in webpage
			$log = str_replace("[NEWLINE]","<br/>",$log);
			display_on_page(1,$log);
			break;

		case 3: //email to user
			$log = str_replace("[NEWLINE]","\r\n",$log);
			email_debug($convoArr['conversation']['debugemail'],$log);
			break;
		
	}
	

	return $convoArr;
}

/** reduceConvoArr()
 *  A small function to create a smaller convoArr just for debuggin!
 *  @param array $convoArr - the big array to be reduced
 */
function reduceConvoArr($convoArr)
{
	$showconvoArr = array();
	
	$showconvoArr['conversation'] = $convoArr['conversation'];
	$showconvoArr['topic']['1'] = $convoArr['topic']['1'];
	$showconvoArr['that']['1'] = $convoArr['that']['1'];
	$showconvoArr['star']['1'] = $convoArr['star']['1'];
	$showconvoArr['input']['1'] = $convoArr['input']['1'];
	$showconvoArr['stack']['top'] = $convoArr['stack']['top'];
	$showconvoArr['stack']['last'] = $convoArr['stack']['last'];
	$showconvoArr['client_properties'] = $convoArr['client_properties'];
	$showconvoArr['aiml']['user_raw'] = $convoArr['aiml']['user_raw'];
	$showconvoArr['aiml']['lookingfor'] = $convoArr['aiml']['lookingfor'];
	$showconvoArr['aiml']['pattern'] = $convoArr['aiml']['pattern'];
	$showconvoArr['aiml']['thatpattern'] = $convoArr['aiml']['thatpattern'];
	$showconvoArr['aiml']['topic'] = $convoArr['aiml']['topic'];
	$showconvoArr['aiml']['score'] = $convoArr['aiml']['score'];
	$showconvoArr['aiml']['aiml_to_php'] = $convoArr['aiml']['aiml_to_php'];
	$showconvoArr['aiml']['aiml_id'] = $convoArr['aiml']['aiml_id'];
	$showconvoArr['aiml']['parsed_template'] = $convoArr['aiml']['parsed_template'];
	$showconvoArr['user_say']['1'] = $convoArr['user_say']['1'];
	$showconvoArr['that_raw']['1'] = $convoArr['that_raw']['1'];
	$showconvoArr['parsed_template']['1'] = $convoArr['parsed_template']['1'];
	
	return $showconvoArr;
}


/**
 * function writefile_debug()
 * Handles the debug when written to a file
 * @param  string $myFile - the name of the file which is also the convo id
 * @param  string $log - the data to write
**/
function writefile_debug($log)
{
	$myFile = _DEBUG_PATH_.session_id().".txt";
    if (DIRECTORY_SEPARATOR == '\\') {
      $log = str_replace("\n", "\r\n", $log);
    }
	file_put_contents($myFile, $log);
}


/**
 * function display_on_page()
 * Handles the debug when it is displayed on the webpage either in the source or on the page
 * @param  int $show_on_page - 0=show in source 1=output to user
 * @param  string $log - the data to show
**/
function display_on_page($show_on_page,$log)
{
	if($show_on_page==0){
		echo "<!--<pre>";
		print_r($log);
		echo "</pre>-->";
	}else{
		echo "<pre>";
		print_r($log);
		echo "</pre>";
	}
}


/**
 * function email_debug()
 * Handles the debug when it is emailed to the botmaster
 * @param  string $email - email address
 * @param  string $log - the data to send
**/
function email_debug($email,$log)
{
	$to      = $email;
	$subject = 'Debug Data';
	$message = $log;
	$headers = 'From: '.$email . "\r\n" .
	    'Reply-To: '.$email . "\r\n" .
	    'X-Mailer: PHP/' . phpversion();
	
	mail($to, $subject, $message, $headers);
}




/**
 * function outputDebug()
 * Used in the install/upgrade files will display it straightaway
 * @param  string $fileName - the file the error came from
 * @param  string $functionName - the function that triggered the error
 * @param  string $line - the line of code
 * @param  string $info - the message to display
**/
function outputDebug($fileName, $functionName, $line, $info) {
  
	global $srai_iterations;
	list($usec, $sec) = explode(" ",microtime());
	
	//build timestamp index for the debug array
	$string = ((float)$usec + (float)$sec);
	$string2 = explode(".", $string);
	$index = date("d-m-Y H:i:s", $string2[0]).":".$string2[1];	
	
		if($srai_iterations<1){
			$sr_it = 0;}
		else {
			$sr_it = $srai_iterations;}	
	
	//add to array
	print "<br/>----------------------------------------------------";
	print "<br/>".$index.": ".$fileName;
	print "<br/>".$index.": ".$functionName;
	print "<br/>".$index.": ".$line;
	print "<br/>".$index.": ".$info;
	print "<br/>".$index.": srai:".$sr_it;
	print "<br/>----------------------------------------------------";
}

  function SQL_Error($errNum, $file = 'unknown', $function = 'unknown', $line = 'unknown') {
    $msg = "There's a problem with your Program O installation. Please run the <a href=\"../install/\">install script</a> to correct the problem.<br>\n";
    switch ($errNum) {
      case '1146':
      $msg .= "The database and/or table used in the config file doesn't exist.<br>\n";
      break;
      default:
      $msg = "Error number $errNum!<br>\n";
    }
    return $msg;
  }

  function save_file($file, $content, $append = false) {
    if (function_exists('file_put_contents')) {
      $x = file_put_contents($file, $content, $append);
    }
    else {
      $fileMode = ($append === true) ? "a" : "w";
      $fh = fopen($file, $fileMode)or die("Can't open the file!");
      $cLen = strlen($content);
      fwrite($fh, $content, $cLen);
      fclose($fh);
    }
    return 1;
  }

?>