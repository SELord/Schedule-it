
<?php

//INSERT.PHP = CREATE NEW EVENT ON CALENDAR/WEEKLY/DAY VIEW (SETS TIME AUTOMATICALLY TO 12:00AM ON CALENDAR VIEW)

if(!empty($_POST)) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $date = $_POST['date'];   //NEED TO ADD
    $dateStartTime = $_POST['dateStartTime'];
    $duration = $_POST['duration'];
    $slots = $_POST['slots'];
    $RSVPslotLim = $_POST['RSVPslotLim'];
    $creatorID = $_POST['creatorID'];
    $location = $_POST['location'];
    $RSVPLim = $_POST['RSVPLim'];

/*
    echo $title . "\n";
    echo $description . "\n";
    echo $date . "\n";
    echo $dateStartTime . "\n";
    echo $duration . "\n";
    echo $slots . "\n";
    echo $RSVPslotLim . "\n";
    echo $creatorID . "\n";
    echo $location . "\n";
    echo $RSVPLim;
*/


    //Get duration of each slot to get interval of each slot
    //SOURCE: https://stackoverflow.com/questions/18457164/split-time-interval-in-15-min-slot-using-mysql
    //SOURCE: https://stackoverflow.com/questions/8169139/adding-minutes-to-date-time-in-php
    date_default_timezone_set('UTC');  // optional
    //echo "date: " . $date . "\n";
    //echo "dateStartTime: " . $dateStartTime . "\n";
    $get_start = strtotime($date . ' ' . $dateStartTime);
    //echo  $get_start . "\n";

    list($hrs, $min) = explode(':',$duration);

    //SOURCE: https://www.daniweb.com/programming/web-development/threads/129966/divide-time
    $min += $hrs * 60;  

    //RSVP slot limit is the number of slots a user can reserve 
    //Use a different GET for slot
    $time_toconvert = ($min / $slots);

    $intervals = date("H:i",mktime(0,$time_toconvert,0,0,0,0));

    $get_end =  strtotime(('+' . $min . 'minutes'), $get_start);
    //echo $time_toconvert . "\n";

    //Convert date/time back to YYYY-MM-DD HH:MM
    $start_time = date('Y-m-d H:i:s', $get_start);
    //echo "\nstart time: " . $start_time;
    $dateEndTime = date('Y-m-d H:i:s', $get_end);
    //echo "\ndateEndTime: " . $start_time;
    //echo "Start: " . $start_time;
    //echo "\nEnd: " . $dateEndTime;




    $query = "INSERT INTO `Event` (`title` ,`description` , `dateStartTime` , `dateEndTime`, `duration` ,`RSVPslotLim`,`creatorID`) VALUES ('".$title."', '".$description."', '".$start_time."', '".$dateEndTime."', '".$duration."', '".$RSVPslotLim."', '".$creatorID."')";
    /*$params = array($title, $description, $date, $start_time, $dateEndTime, $duration, $RSVPslotLim, $creatorID);*/
    //echo $query;
    //connect to server and select database
    require_once '../dbconfig.php';
    //echo $query;



    //echo $dbhost . "\n" . $dbuser . "\n" . $dbpass . "\n" . $dbname;

    $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    if (!$mysqli) {
        echo "Error: unable to connect to MySQL: Errorno - " . mysqli_connect_errno() . PHP_EOL;
        exit; 
    } else {
        echo "Connected to database - success";
    }

    if (mysqli_query($mysqli, $query)) {
       echo "New record created successfully";
    } else {
       echo "Error: " . $query . "" . mysqli_error($mysqli);
    }

    $meetingID = mysqli_insert_id($mysqli);
    //echo $meetingID;

    //$mysqli->query($query);
    //$check = mysqli_query($mysqli, $query) or die (mysqli_error($mysqli));

  //Get Event id(pk) of inserted meeting id
    //$meetingID = $mysqli->insert_id;
    //echo $meetingID;

    $addTime = $time_toconvert;
    //Insert number of slots for each meeting id
    for($i = 1; $i<=$slots; $i++) {
      //add 15 minutes for each start time
      $time_toconvert = $addTime * ($i-1);
      $intTime = date('H:i:s', strtotime(('+' . $time_toconvert . 'minutes'), $get_start));
     // echo $time_toconvert . "\n";

        $array[] = array(
            'duration' => $intervals,
            'location' => $location,
            'RSVPlim' => $RSVPLim,    //Should this be the same as $RSVPslotLim?
            'eventID' => $meetingID,
            'startTime' => $intTime 
        );
    }

    $codes = $array;

    //SOURCE: https://stackoverflow.com/questions/28506799/insert-into-mysql-from-php-foreach-loop
    foreach ($codes as $code) {
         $query1 = "INSERT INTO `Slot` (`duration`, `startTime`, `location`, `RSVPlim`, `eventID`) VALUES ('".$code['duration']."','".$code['startTime']."','".$code['location']."','".$code['RSVPlim']."','".$code['eventID']."')";
         //Check db query OK/pass-through
         $q = mysqli_query($mysqli, $query1) or die (mysqli_error($mysqli));
    }

  $mysqli->close();
}


?>
