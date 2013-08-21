<?PHP
//-----------------------------------------------------------------------------------------------
//My Program-O Version 2.0.9
//Program-O  chatbot admin area
//Written by Elizabeth Perreau and Dave Morton
//May 2011
//for more information and support please visit www.program-o.com
//-----------------------------------------------------------------------------------------------
ini_set('display_errors', 1);
session_start();
if (!defined('SCRIPT_INSTALLED')) header('location: install_programo.php');

if((isset($_POST['uname']))&&(isset($_POST['pw'])))
{

	$uname = mysql_real_escape_string(strip_tags(trim($_POST['uname'])));
	$pw = mysql_real_escape_string(strip_tags(trim($_POST['pw'])));
	$dbconn = db_open();
	$sql = "SELECT * FROM `$dbn`.`myprogramo` WHERE uname = '".$uname."' AND pword = '".MD5($pw)."'";

	$result = mysql_query($sql,$dbconn)or die(mysql_error());
	$count = mysql_num_rows($result);
	$msg ="";
	
	if($count>0)
	{
		$row=mysql_fetch_array($result);
		$_SESSION['poadmin']['uid']=$row['id'];
		$_SESSION['poadmin']['name']=$row['uname'];
		$_SESSION['poadmin']['lip']=$row['lastip'];
		$_SESSION['poadmin']['llastlogin']=date('l jS \of F Y h:i:s A', strtotime($row['lastlogin']));


		if(!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
		{
			$ip=$_SERVER['HTTP_CLIENT_IP'];
		}
		elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
		{
		  	$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else
		{
		  	$ip=$_SERVER['REMOTE_ADDR'];
		}
		
		$sqlupdate = "UPDATE `$dbn`.`myprogramo` SET `lastip` = '$ip', `lastlogin` = CURRENT_TIMESTAMP WHERE uname = '".$uname."' limit 1";
		//echo $sql;
		mysql_query($sqlupdate,$dbconn)or die(mysql_error());
		
		$_SESSION['poadmin']['ip']=$ip;
		$_SESSION['poadmin']['lastlogin']=date('l jS \of F Y h:i:s A');
	}
	else
	{
		$msg = "incorrect username/password";
	}
	
	mysql_close($dbconn);
	
	if($msg == "")
	{
		header("location: pages/index.php");
	}
}
elseif(isset($_GET['msg']))
{
	$msg = htmlentities($_GET['msg']);
}
else
{
	$msg = "";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>My Program-O</title> 
<link rel="stylesheet" type="text/css" href="pages/inc/style.css" /> 
</head>

<body>
<div id="container"><p>&nbsp;</p><div align=center><h1><span class="orange">My</span> Program-O</h1></div>
  <p><?php echo "<div id=\"errMsg\">$msg</div>";?></p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <table width="38%" border="0" align="center" cellpadding="5" cellspacing="0">
    <tr>
      <td bgcolor="#FFFFFF">  <fieldset> 
    <legend>Login </legend> 
  <form id="fm-form" method="post" action="index.php" > 

    <div class="fm-req"> 
      <label for="uname">Username:</label> 
      <input name="uname" id="uname" type="text" maxlength="20" size="15"/> 
    </div> 
    <div class="fm-req"> 
      <label for="pw">Password:</label> 
      <input name="pw" id="pw" type="password" maxlength="20" size="15"/> 
    </div> 
    
    <div id="fm-submit" class="fm-req"> 
      <input name="Submit" value="Submit" type="submit" /> 
    </div> 
  </form></fieldset></td>
    </tr>
  </table>
  <p>&nbsp;</p>
 </div>
</body>
</html>
