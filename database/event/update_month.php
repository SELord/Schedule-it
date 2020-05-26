<?php

//UPDATE EVENT ON CALENDAR VIEW

require_once '../dbconfig.php';
require_once '../dbquery.php';

if(!empty($_POST))
{

  $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
  if (!$mysqli) {
      echo "Error: unable to connect to MySQL: Errorno - " . mysqli_connect_errno() . PHP_EOL;
      exit; 
  }

  eventUpdate($mysqli, $_POST);

}

?>
