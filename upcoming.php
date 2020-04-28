<?php
    include 'file_path.php';

    session_start();
    //check once again if the user is logged in
    //if not, redirect back to login page

    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] == FALSE) {
        session_destroy();
        session_unset();
        $_SESSION = array();
        //header("Location: " . $FILE_PATH . "login.php");
        echo "<script type='text/javascript'> document.location = '" . $FILE_PATH . "login.php'; </script>";
    }
       
    //TODO: Retrieve the upcoming events and meetings, and reserved meetings 
    //of the user to populate the calendar
    require_once './database/dbconfig.php';
    require_once './database/dbquery.php';
    
    $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    if ($mysqli->connect_errno) {
          echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
          exit;
      } 

    //var_dump($_SESSION);
    //Find userID based off of onid
     $data = lookupUser($mysqli, $_SESSION["onidID"]);
     $user = json_decode($data);
     //echo "USER: " . $user->id;

    // Output: if any are found, then a 2D associative array containing event info with
    //         the first dimension being row number of result, else NULL.
    //       2nd dim array keys: id, title, dateStartTime
    $eventsCreatedByUser = eventCreateHist($mysqli, $user->id);
    
    // Output: if any are found, then a 2D associative array containing event info with
    //         the first dimension being row number of result, else NULL.
    //       2nd dim array keys: eventID, inviteID, title, dateStartTime, firstName, lastName
    $eventsYetToReserve = invitesUpcoming($mysqli, $user->id);

    // Output: if any are found, then a 2D associative array containing slot info with
    //         the first dimension being row number of result, else NULL.
    //    2nd dim array keys: eventID, inviteID, slotID, title, dateStartTime, startTime, duration, location, endTime
    $reservationsMadeByUser = reservedSlotHist($mysqli, $user->id);
    
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

    $mysqli->close();

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <title>Schedule-It!</title>

  <script type="text/javascript">
      <?php 
            echo "var onidID = ".$user->id.";"
      ?>
  </script>
  
  <!--Bootstrap core CSS-->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
  <!--Customized css-->
  <link rel="stylesheet" href="./assets/css/main.css" type="text/css">

  <!--NEEDED FOR DIALOG-FORM DISPLAY -->
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
  
  <!-- javascript files -->
  <script src="./assets/js/main.js"></script> 
  <!-- <script src="./assets/js/homepage.js"></script> -->
  <script src="./assets/js/event.js"></script> 

  <!--fullcalendar-->
  <!--Use daygrid-views for homepage -->
  <!--Use Selectable for event creation page-->
  <!--Source  fullcalendar.io-->
  <link href='./assets/js/fullcalendar/packages/core/main.css' rel='stylesheet' />
  <link href='./assets/js/fullcalendar/packages/daygrid/main.css' rel='stylesheet' />
  <link href='./assets/js/fullcalendar/packages/list/main.css' rel='stylesheet' />
  <link href='./assets/js/fullcalendar/packages/timegrid/main.css' rel='stylesheet' />
 <!--NEEDED FOR DIALOG-FORM DISPLAY -->
  <style>

    label, input { display:block; }
    input.text { margin-bottom:12px; width:95%; padding: .4em; }
    fieldset { padding:0; border:0; margin-top:25px; }
    h1 { font-size: 1.2em; margin: .6em 0; }
    div#users-contain { width: 350px; margin: 20px 0; }
    div#users-contain table { margin: 1em 0; border-collapse: collapse; width: 100%; }
    div#users-contain table td, div#users-contain table th { border: 1px solid #eee; padding: .6em 10px; text-align: left; }
    .ui-dialog .ui-state-error { padding: .3em; }
    .validateTips { border: 1px solid transparent; padding: 0.3em; }
    #live_data .ui-dialog {
     width: 100%;
     padding: 0; }

     .tooltip {
        position: relative;
        display: inline-block;
        border-bottom: 1px dotted black;
      }

      .tooltip .tooltiptext {
        visibility: hidden;
        width: 120px;
        background-color: black;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 5px 0;

        /* Position the tooltip */
        position: absolute;
        z-index: 1;
      }

      .tooltip:hover .tooltiptext {
        visibility: visible;
      }

      .hidden>div {
        display:none;
      }

      .visible>div {
        display:block;
      }

</style>

  <script src='./assets/js/fullcalendar/packages/core/main.js'></script>
  <script src='./assets/js/fullcalendar/packages/daygrid/main.js'></script>
  <script src='./assets/js/fullcalendar/packages/list/main.js'></script>
  <script src='./assets/js/fullcalendar/packages/interaction/main.js'></script>
  <script src='./assets/js/fullcalendar/packages/timegrid/main.js'></script>
  <script src='./assets/js/fullcalendar/packages/moment/main.js'></script>
</head>
<body>
  <!-- HEADER CODE FROM OSU WEBSITE TO DEVELOP COHESIVE LOOK -->
  <div class="header-container">
        <header role="banner" class="osu-top-hat">
            <a href="https://oregonstate.edu" title="Schedule-It Home" class="logo">
              <img src="https://oregonstate.edu/themes/osu/drupal8-osuhomepage/logo.svg" alt="Oregon State University" />
            </a>
            <nav role="navigation" id="block-homepage-main-menu" class="d-none d-lg-block">
              <ul class="main-menu nav nav-pills">
                <li class="nav-item">
                  <a href="homepage.php" class="nav-link">Schedule-It Home</a>
                </li>
                <li class="nav-item">
                  <a href="upcoming.php" class="nav-link">Upcoming</a>
                </li>
                <li class="nav-item">
                  <a href="eventmanagement.php" class="nav-link">Manage Events</a>
                </li>
                <li class="nav-item">
                  <a href="view_history.php" class="nav-link">Past Meetings</a>
                </li>
                <li class="nav-item">
                  <a href="logout.php" class="nav-link">Logout</a>
                </li>
              </ul>
            </nav>
        </header>
    </div><p>

    <!-- Passing variables from php to javascript to populate events and reservations -->
    <script type="text/javascript"> 
        let reservations = <?php echo json_encode($reservations) ?>; 
    </script>
    
   
    <!-- div for Events made by user -->
    
    <div class="container-fluid">
    <center><h1> UPCOMING: </h1>   <p>
        <ul class="list-group" id="eventsUserCreated">
            <?php 
                foreach ($eventsCreatedByUser as $idx => $event) {
                    $eventID = $event["id"];
                    $eventTitle = $event["title"];
                    $eventStartDate = explode(" ", $event["dateStartTime"])[0];
                    $upcomingEvents = $eventStartDate;
                    $currentDate = date('Y-m-d');
                    if($eventStartDate >= $currentDate) {
                      $li = "<li>$eventTitle, starting on $upcomingEvents</li>";
                      echo $li;
                    }
                }
            ?>
        </ul></center>
    </div>

</script>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <!--<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

</body>
</html>

