<?php
// PHP error reporting for debug info. Commented out for production
// For more information: https://stackify.com/display-php-errors/
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

//UPDATE EVENT ON CALENDAR VIEW

require_once '../dbconfig.php';
require_once '../dbquery.php';

if(!empty($_POST))
{

  $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname, $dbport);
  if (!$mysqli) {
      echo "Error: unable to connect to MySQL: Errorno - " . mysqli_connect_errno() . PHP_EOL;
      exit; 
  }

  eventUpdate($mysqli, $_POST);

}

?>
