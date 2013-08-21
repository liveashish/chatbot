<?php

    $thisFile = __FILE__;
    if (!file_exists('global_config.php')) die('Please place this file in your /config folder to run it.');
    require('global_config.php');

    $dsn    = "mysql:host=$dbh;port=$dbPort;dbname=$dbn";

    $sql1 = "
ALTER DATABASE $dbn
    CHARACTER SET utf8
    DEFAULT CHARACTER SET utf8
    COLLATE utf8_general_ci
    DEFAULT COLLATE utf8_general_ci;";
    $sql_row1 = "

ALTER TABLE $dbn.[table]
	DEFAULT CHARACTER SET utf8
	COLLATE utf8_general_ci;
ALTER TABLE $dbn.[table]";
    $sql_row2 = '

change `[field]` `[field]` [type] CHARACTER SET utf8 COLLATE utf8_general_ci [null],';
# ALTER TABLE `aiml` CHANGE `thatpattern` `thatpattern` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
    try {
      $dbh = new PDO($dsn, $dbu, $dbp);
      $dbh->setAttribute (PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $dbh->setAttribute (PDO::ATTR_PERSISTENT, true);
      $dbh->setAttribute (PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
    }
    catch (PDOException $e) {
      $dbh = 'Connection failed: ' . $e->getMessage();
      die("<pre>DBH = $dbh");
    }

    $fetchMode = PDO::FETCH_ASSOC;
    $sql = "SHOW tables in `$dbn`;";
    $query = $dbh->query($sql);
    $script_sql = $sql1;
    $result = $query->fetchAll($fetchMode);
    foreach ($result as $row)
    {
      $addFlag = false;
      $table_name = $row["Tables_in_$dbn"];
      $sql = "SHOW COLUMNS FROM `$dbn`.`$table_name`;";
      $query = $dbh->query($sql);
      $this_result = $query->fetchAll();
      $tmp_sql_row = str_replace('[table]', $table_name, $sql_row1);
      foreach ($this_result as $table_row)
      {
        #die ('<pre>this result = ' . print_r($this_result, true));
        $field = $table_row['Field'];
        $type = $table_row['Type'];
        $null = $table_row['Null'];
        $null = ($null == 'NO') ? 'NOT NULL' : 'NULL';
        if ($type == 'text' or (strstr($type, 'varchar')))
        {
          $tmp_sql_field_row = str_replace('[field]', $field, $sql_row2);
          $tmp_sql_field_row = str_replace('[type]', $type, $tmp_sql_field_row);
          $tmp_sql_field_row = str_replace('[null]', $null, $tmp_sql_field_row);
          $tmp_sql_row .= $tmp_sql_field_row;
          $addFlag = true;
        }
      }
      $script_sql .= ($addFlag) ? rtrim($tmp_sql_row,"\n,") . ";\n" : '';
   }

   $result = $dbh->exec($script_sql);

    if (false !== $result) die('Database converted successfully! Enjoy! <a href="../">Your Chatbot</a>');
    die("<pre>There was a problem. SQL =\n$script_sql");



?>