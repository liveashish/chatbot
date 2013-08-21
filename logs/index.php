<?php
// Debug viewer
$iframeURL = (isset($_POST['file'])) ? $_POST['file'] : 'about:blank';
$optionTemplate = '        <option value="[file]">[file]</option>' . "\n";
$fileList = glob('*.log');
$options = '';
foreach ($fileList as $file) {
  $row = str_replace('[file]', trim($file), $optionTemplate);
  $options .= $row;
}



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>Log File Reader</title>
    <style type="text/css">
      #viewer {
        position: absolute;
        left: 5px;
        top: 65px;
        right: 5px;
        bottom: 5px;
      }
    </style>
  </head>
  <body>
    <form name="fileChoice" action="./" method="POST">
      Select a Log File to view: <select name="file" id="file" size="1" onchange="document.forms[0].submit();">
        <option value="about:blank">Empty Selection</option>
<?php echo rtrim($options); ?>
      </select><br />
      <div id="viewer"><iframe  width="99%" height="99%" src="<?php echo $iframeURL ?>"></iframe></div>
    </form>
  </body>
</html>
