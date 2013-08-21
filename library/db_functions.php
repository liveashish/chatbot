<?php
/***************************************
* http://www.program-o.com
* PROGRAM O 
* Version: 2.0.9
* FILE: library/db_functions.php
* AUTHOR: ELIZABETH PERREAU
* DATE: MAY 4TH 2011
* DETAILS: common library of db functions
***************************************/



/**
 * function db_open()
 * Connect to the database
 * @param  string $host -  db host
 * @param  string $user - db user
 * @param  string $password - db password
 * @param  string $database_name - db name
 * @return resource $con - the database connection resource
**/
  function db_open() {
    global $dbh, $dbu, $dbp, $dbn, $dbPort;
    $host = (!empty($dbPort) and $dbPort != 3306) ? "$dbh:$dbPort" : $dbh; // add port selection if not the standard port number
    $conn = mysql_connect($host, $dbu, $dbp) or sqlErrorHandler( "mysql_connect", mysql_error(), mysql_errno(), __FILE__, __FUNCTION__, __LINE__);
    $x = mysql_select_db($dbn) or sqlErrorHandler( "mysql_select_db", mysql_error(), mysql_errno(), __FILE__, __FUNCTION__, __LINE__);
    return $conn;
  }

/**
 * function db_close()
 * Close the connection to the database
 * @param resource $con - the open connection
**/
function db_close($con) {
 $discdb = mysql_close($con) or sqlErrorHandler( "mysql_close", mysql_error(), mysql_errno(), __FILE__, __FUNCTION__, __LINE__);

}

/**
 * function db_query()
 * Run a query on the db
 * @param resource $con - the open connection
 * @param string $sql - the sql query to run
 * @return resource $result - the result resource
**/
function db_query($sql,$dbconn){
	//run query
	$result = mysql_query($sql,$dbconn)or sqlErrorHandler($sql, mysql_error(), mysql_errno(), __FILE__, __FUNCTION__, __LINE__);
	//if no results output message
	if(!$result){
	}
	//return result resource
	return $result;
}

/**
 * function db_make_safe()
 * Makes a str safe to insert in the db
 * @param string $str - the string to make safe
 * @return string $str - the safe string
**/
function db_make_safe($str){
  $dbconn = db_open();
  $out =  mysql_real_escape_string($str, $dbconn); // Takes into account the character set of the chosen database
  mysql_close($dbconn);
  return $out;
}

/**
 * function db_res_count()
 * Makes a str safe to insert in the db
 * @param resource $result - the result resource
 * @return int $res - the number of results
**/
function db_res_count($result){
	return mysql_num_rows($result);
}

/**
 * function db_res_array()
 * returns an array of rows from the result
 * @param resource $result - the result resource
 * @return array $row - the array of results
**/
function db_res_array($result){
	return mysql_fetch_array($result);
}

?>