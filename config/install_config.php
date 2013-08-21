<?php
/***************************************
* http://www.program-o.com
* PROGRAM O
* Version: 2.0.9
* FILE: config/install_config.php
* AUTHOR: ELIZABETH PERREAU AND DAVE MORTON
* DATE: MAY 4TH 2011
* DETAILS: this file is a stripped down, "install" version of the config file,
* and as such, only has the most minimal settings within it. The install script
*  will create a full and complete config file during the install process
***************************************/
    //------------------------------------------------------------------------
    // Paths - only set this manually if the below doesnt work
    //------------------------------------------------------------------------

    chdir( dirname ( __FILE__ ) );
    $thisConfigFolder = dirname( realpath( __FILE__ ) ) . DIRECTORY_SEPARATOR;
    $thisConfigParentFolder = preg_replace( '~[/\\\\][^/\\\\]*[/\\\\]$~' , DIRECTORY_SEPARATOR , $thisConfigFolder);
    $baseURL = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'];
    $docRoot = $_SERVER['DOCUMENT_ROOT'];

    define("_BASE_DIR_", $thisConfigParentFolder);
    $path_separator = DIRECTORY_SEPARATOR;

    $thisFile = str_replace(_BASE_DIR_, '', $thisFile);
    $thisFile = str_replace($path_separator, '/', $thisFile);
    $baseURL = str_replace($thisFile, '', $baseURL);
    define("_BASE_URL_", $baseURL);

    //------------------------------------------------------------------------
    // Define paths for include files
    //------------------------------------------------------------------------

    define("_INC_PATH_",_BASE_DIR_.$path_separator);
    define("_ADMIN_PATH_",_BASE_DIR_."admin".$path_separator);
    define("_GLOBAL_PATH_",_BASE_DIR_."global".$path_separator);
    define("_BOTCORE_PATH_",_BASE_DIR_."chatbot".$path_separator."core".$path_separator);
    define("_AIMLPHP_PATH_",_BASE_DIR_."chatbot".$path_separator."aiml_to_php".$path_separator);
    define("_LIB_PATH_",_BASE_DIR_."library".$path_separator);
    define("_ADDONS_PATH_",_BASE_DIR_."chatbot".$path_separator."addons".$path_separator);
    define("_CONF_PATH_",_BASE_DIR_."config".$path_separator);
    define("_LOG_PATH_",_BASE_DIR_."logs".$path_separator);
    define("_DEBUG_PATH_",_BASE_DIR_."chatbot".$path_separator."debug".$path_separator);
    define("_INSTALL_PATH_",_BASE_DIR_.$path_separator."install".$path_separator);

    //------------------------------------------------------------------------
    // Error reporting
    //------------------------------------------------------------------------

    $e_all = defined('E_DEPRECATED') ? E_ALL ^ E_DEPRECATED : E_ALL;
    error_reporting($e_all);
    ini_set('log_errors', true);
    ini_set('error_log', _LOG_PATH_ . 'install-error.log');
    ini_set('html_errors', false);
    ini_set('display_errors', false);

    //------------------------------------------------------------------------
    // Default chatbot values
    //------------------------------------------------------------------------

    $default_pattern = "RANDOM PICKUP LINE";
    $default_error_response = "No AIML category found. This is a Default Response.";
    $default_conversation_lines = '1';
    $default_remember_up_to = '10';


?>