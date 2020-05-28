<?php
// PHP error reporting for debug info. Commented out for production
// For more information: https://stackify.com/display-php-errors/
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
/*******************************************************************
TRIGGED BY MANAGE_EVENT.JS WHEN USER HITS "SEND" AFTER ENTERING A MESSAGE
WHAT THIS FILE DOES: 
- GATHERS LIST OF EMAILS AND CALLS EMAILER.PHP FUNCTION TO SEND
/********************************************************************/
require '../dbconfig.php';  // database connection 
require '../dbquery.php';   // functions for accessing database
require '../../assets/php/emailer.php';  // email functions

//connect to the database
$connect = new mysqli($dbhost, $dbuser, $dbpass, $dbname, $dbport);
    if (!$connect) {
        echo "Error: unable to connect to MySQL: Errorno - " . mysqli_connect_errno() . PHP_EOL;
        exit; 
    } else {
        echo "Connected to database - success\n";
    }

//get eventID, creatorID and list of emails from event.js
$eventID = $_POST['id'];
$subject = $_POST['subject'];
$message = $_POST['message'];

// get emails from all event invitees
// $result is a 2D assoc array or null if no event invitees
$result = getEventEmails($connect, $eventID);

// send out emails
if(count($result) > 0){
    for($i = 0; $i < count($result); $i++){
        echo $result[$i]['email'];
        $emailAddress = $result[$i]['email'];
        sendEmail($emailAddress, $subject, $message, 'none');
    }
}

$connect->close();
?>