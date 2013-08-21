<?php
/*
 * use this script to enable your visitors to download
 * your files. 
 */
$pageBack = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : 'http://www.google.com/search?q=prevent+stupid+hacker+tricks';
$fileserver_path = dirname(__FILE__) . '/downloads';	// change this to the directory your files reside
$req_file 		 = basename($_GET['file']);
$whoami			 = basename(__FILE__);	// you are free to rename this file
if (strstr($pageBack,'google') !== false) header("Location: $pageBack");
if (empty($req_file)) {
	print "Usage: $whoami?file=&lt;file_to_download&gt;";
	exit;
}

/* no web spamming */
if (!preg_match("/^[a-zA-Z0-9._-]+$/", $req_file, $matches)) {
	print "I can't do that. sorry.";
	exit;
}

/* download any file, but not this one */
if ($req_file == $whoami) {
	print "I can't do that. sorry.";
	exit;
}

/* check if file exists */
if (!file_exists("$fileserver_path/$req_file")) {
	print "File <strong>$req_file</strong> doesn't exist.";
	exit;
}

if (empty($_GET['send_file'])) {
	header("Refresh: 5; url=$whoami?file=$req_file&send_file=yes");
}
else {
	header('Content-Description: File Transfer');
	header('Content-Type: application/force-download');
	header('Content-Length: ' . filesize("$fileserver_path/$req_file"));
	header('Content-Disposition: attachment; filename=' . $req_file);
	#readfile("$fileserver_path/$req_file");
    print file_get_contents("$fileserver_path/$req_file");
	exit;
}
header("Location: $pageBack");
?>

