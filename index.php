<?php

  if (!file_exists("config/global_config.php"))
  {
  # No config exists we will run install
    header("location: install");
  }
  else
  {
    # Config exists we will goto the bot
    $thisFile = __FILE__;
    require_once('config/global_config.php');
    switch ($default_format)
    {
      case 'JSON':
      $gui = 'jquery';
      break;
      case 'XML':
      $gui= 'xml';
      break;
      default:
      $gui = 'plain';
    }
    header("location: gui/$gui");
  }

?>
