
<?php
    
    //get onid parameter
    $onidID = $_GET["onidID"];
    //echo $onidID;

    //TODO: Retrieve the upcoming events and meetings, and reserved meetings 
    //of the user to populate the calendar
    require_once '../dbconfig.php';
    require_once '../dbquery.php';
    
    $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    if ($mysqli->connect_errno) {
          echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
          exit;
      } 
      //var_dump($mysqli);

    //Find userID based off of onid
    $data = lookupUser($mysqli, $onidID);
    //var_dump($data);
    $user = json_decode($data);
     //var_dump($user);
     //echo "USER: " . $user->id;

    // Output: if any are found, then a 2D associative array containing event info with
    //         the first dimension being row number of result, else NULL.
    //       2nd dim array keys: id, title, dateStartTime
    $eventsCreatedByUser = eventCreateHist($mysqli, $onidID);
    //var_dump($eventsCreatedByUser);
    // Output: if any are found, then a 2D associative array containing event info with
    //         the first dimension being row number of result, else NULL.
    //       2nd dim array keys: eventID, inviteID, title, dateStartTime, firstName, lastName
    $eventsYetToReserve = invitesUpcoming($mysqli, $onidID);

    // Output: if any are found, then a 2D associative array containing slot info with
    //         the first dimension being row number of result, else NULL.
    //    2nd dim array keys: eventID, inviteID, slotID, title, dateStartTime, startTime, duration, location, endTime
    $reservationsMadeByUser = reservedSlotHist($mysqli, $onidID);
    
    $reservations = array();
    foreach ($reservationsMadeByUser as $idx => $res) {
        $eventItem = array();
        $slotID = $res["slotID"];
        $inviteID = $res["inviteID"];
        $eventItem["id"] = $slotID;
        $eventItem["eventID"] = $res["eventID"];
        $eventItem["title"] = $res["title"];
        $date = explode(" ", $res["dateStartTime"])[0];
        $eventItem["start"] = $date."T".$res["startTime"];
        $eventItem["end"] = $date."T".$res["endTime"];
        $eventItem["url"] = "view_reservation?slot=$slotID&inviteID=$inviteID";
        array_push($reservations, $eventItem);
    }

    echo json_encode($reservations);
    $mysqli->close();


    
?>
