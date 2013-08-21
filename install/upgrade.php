<?php
/***************************************
* http://www.program-o.com
* PROGRAM O 
* Version: 2.0.9
* FILE: install/upgrade.php
* AUTHOR: ELIZABETH PERREAU AND DAVE MORTON
* DATE: JUNE 4TH 2012
* DETAILS: PLEASE RUN THIS FILE TO UPGRADE THE DATABASE FROM VERSION 1 TO VERSION 2
***************************************/

  $thisFile = __FILE__;
  if (!file_exists('../config/global_config.php')) header('location: ../install/install_programo.php');
  require_once('../config/global_config.php');


	//load shared files
	include_once(_LIB_PATH_."db_functions.php");
	include_once(_LIB_PATH_."error_functions.php");
	
	runDebug( __FILE__, __FUNCTION__, __LINE__, "Starting upgrade");
	
	create_bots_tbl();
	update_aiml_tbl();
	
	//open db connection
	$con = db_open();

	
	
	function create_bots_tbl()
	{
		global $con,$dbn;
		
		$sql = "CREATE TABLE IF NOT EXISTS `$dbn`.`bots` (
		  `bot_id` int(11) NOT NULL AUTO_INCREMENT,
		  `bot_name` varchar(255) NOT NULL,
		  `bot_desc` varchar(255) NOT NULL,
		  `bot_active` int(11) NOT NULL DEFAULT '1',
		  `format` varchar(10) NOT NULL DEFAULT 'html',
		  `use_aiml_code` int(11) NOT NULL DEFAULT '1',
		  `update_aiml_code` int(11) NOT NULL DEFAULT '1',
		  `save_state` enum('session','database') NOT NULL DEFAULT 'session',
		  `conversation_lines` int(11) NOT NULL DEFAULT '7',
		  `remember_up_to` int(11) NOT NULL DEFAULT '10',
		  `debugemail` int(11) NOT NULL,
		  `debugshow` int(11) NOT NULL DEFAULT '1',
		  `debugmode` int(11) NOT NULL DEFAULT '1',
		  `default_aiml_pattern` varchar(255) NOT NULL DEFAULT 'RANDOM PICKUP LINE',
		  PRIMARY KEY (`bot_id`)
		)";
		
		$result = mysql_query($sql,$con);
		if($result)	{
			outputDebug( __FILE__, __FUNCTION__, __LINE__, "Created bot table");
		} else {
			outputDebug( __FILE__, __FUNCTION__, __LINE__, "Errorwhile creating bot table - Exiting");
			exit;
		}
		
		
		$sql = "INSERT INTO `$dbn`.`bots` (`bot_id`, `bot_name`, `bot_desc`, `bot_active`, `format`, `use_aiml_code`, `update_aiml_code`, `save_state`, `conversation_lines`, `remember_up_to`, `debugemail`, `debugshow`, `debugmode`, `default_aiml_pattern`)
				VALUES (1, 'Program O', 'The default Program O chatbot...', 1, 'html', 0, 0, 'session', 7, 10, 0, 1, 1, 'RANDOM PICKUP LINE')";

		$result = mysql_query($sql,$con);
		if($result)	{
			outputDebug( __FILE__, __FUNCTION__, __LINE__, "Added default bot");
		} else {
			outputDebug( __FILE__, __FUNCTION__, __LINE__, "Error while adding default bot - Exiting");
			exit;
		}		
		
		
		
	}
	
	
	function update_aiml_tbl()
	{
		global $con,$dbn;
		$sql = "ALTER TABLE `$dbn`.aiml` ADD `php_code` TEXT NOT NULL ";
			$result = mysql_query($sql,$con);
		if($result)	{
			outputDebug( __FILE__, __FUNCTION__, __LINE__, "Altered AIML tbl");
		} else {
			outputDebug( __FILE__, __FUNCTION__, __LINE__, "Error while altering AIML tabl - Exiting");
			exit;
		}
	}
	



?>