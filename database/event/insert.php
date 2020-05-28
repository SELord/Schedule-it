
<?php
// PHP error reporting for debug info. Commented out for production
// For more information: https://stackify.com/display-php-errors/
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

//INSERT.PHP = CREATE NEW EVENT ON CALENDAR/WEEKLY/DAY VIEW (SETS TIME AUTOMATICALLY TO 12:00AM ON CALENDAR VIEW)

if(!empty($_POST)) {

    //connect to server and select database
    require_once '../dbconfig.php';
    require_once '../dbquery.php';

    $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname, $dbport);
    if (!$mysqli) {
        echo "Error: unable to connect to MySQL: Errorno - " . mysqli_connect_errno() . PHP_EOL;
        exit; 
    } else {
        echo "Connected to database - success";
    }
    $eventID = newEvent($mysqli, $_POST);

  $mysqli->close();
}
?>