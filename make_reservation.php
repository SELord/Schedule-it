<?php 
// PHP error reporting for debug info. Commented out for production
// For more information: https://stackify.com/display-php-errors/
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

    require_once 'file_path.php';

	// session 
	session_start();

    // Capture the current page with query string to be passed to the login.php page
    $tempArr = explode("scheduleit/", $_SERVER['REQUEST_URI']);
    $returnPage = urlencode($tempArr[1]);

    //check once again if the user is logged in
    //if not, redirect back to login page
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] == FALSE) {   
        session_destroy();   
        header("Location: " . $FILE_PATH . "login.php?returnPage=" . $returnPage);
    }   
	
	// database connection 
	require './database/dbconfig.php';
	// functions for accessing database
	require './database/dbquery.php';
		
	// connect to database 
	$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname, $dbport);
	if ($mysqli->connect_errno) {
		die("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error . "\n");
	}
	
	// get userID from session 
    //Find userID based off of onid
     $data = lookupUser($mysqli, $_SESSION["onidID"]);
     $user = json_decode($data);
     $userID = $user->id;
	    
	// extract inviteID from request if url had invite variable included
    // conditional reference: https://teamtreehouse.com/community/passing-a-get-variable-through-a-link-with-php-2
    if(isset($_GET['invite']) && $_GET['invite'] !== ''){
	    $inviteID = $_GET['invite'];
    }
    // extract eventID from request for invites sent via link instead of email, IF url had event variable included
    // additionally, enter the invite into the invite table for user and then set inviteID
    elseif(isset($_GET['event']) && $_GET['event'] !== ''){
        $eventID = $_GET['event'];

        // Check if invite already exists for user and event
        $checkForInvite = lookupInvite($mysqli, $userID, $eventID);

        // If invited doesn't exist, create it and save inviteID
        if($checkForInvite == false){
            $inviteID = newInvite($mysqli, $userID, $eventID);
        }
        else{
            $invite = json_decode($checkForInvite);
            $inviteID = $invite->id;
        }
    }

	// query db for invite info 
	$inviteInfo = inviteDetails($mysqli, $inviteID);
	
	// query database for event info
	$eventInfo = eventDetails($mysqli, $inviteInfo['eventID']);
	$eventDate = $eventInfo['dateStart'];
	
	// enforce Event attendee RSVP limit
	$RSVPcount = userEventRSVPCount($mysqli, $userID, $inviteInfo['eventID']);
	$RSVPremaining = $eventInfo['RSVPslotLim'] - $RSVPcount;
	// add to event info array for sending to page 
	$eventInfo['RSVPremaining'] = $RSVPremaining;
	
	if ($RSVPremaining > 0){
		// query database for associated slots
		$slots = eventAvailableSlots($mysqli, $inviteInfo['eventID'], $userID);
		
		if (count($slots) > 0){
			// build FullCalendar.io associative array 
			$fullCal = array();
			for($i = 0; $i < count($slots); $i++){
				$tmp = array();
				$tmp['id'] = $slots[$i]['id'];
				$tmp['title'] = $slots[$i]['location'];
				$tmp['start'] = $slots[$i]['startDateTime'];
				$tmp['end'] = $slots[$i]['endDateTime'];
				$tmp['RSVPlim'] = $slots[$i]['RSVPlim'];
				$tmp['RSVPs'] = $slots[$i]['RSVPs'];
				$tmp['eventID'] = $eventInfo['id'];
				$tmp['inviteID'] = $inviteID;
				$fullCal[$i] = $tmp;
			}
			
			// build associative array containing event info and FullCalendar.io info
			$pageInfo = array("eventInfo" => $eventInfo, "fullCal" => $fullCal);
		
		}
		else{
			// no reservable slots found
			$pageInfo = array("eventInfo" => $eventInfo, "fullCal" => NULL);
		}
		echo "<script>\n";
		echo 'var pageInfo = ' . json_encode($pageInfo) . ';';
		echo '</script>';

	}
	else{
		// User has used up their RSVPs and can't make any more reservations
		$pageInfo = array("eventInfo" => $eventInfo);
	
		echo "<script>\n";
		echo 'var pageInfo = ' . json_encode($pageInfo) . ';';
		echo '</script>';
	}


	$mysqli->close();	
	
?>


<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <title>Schedule-it</title>
  
  <!--Bootstrap core CSS-->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
  <!--Customized css-->

  <link rel="stylesheet" href="./assets/css/main.css" type="text/css">
  
  <!-- javascript files -->
  <script src="./assets/js/main.js"></script>
  <script src="./assets/js/make_reservation.js"></script>

  <!-- fontawesome for icon usage eg. navbar hamburger icon -->
  <script src="https://kit.fontawesome.com/96abf9bb58.js" crossorigin="anonymous"></script>

  <!--fullcalendar-->
  <!--Use daygrid-views for homepage -->
  <!--Use Selectable for event creation page-->
  <!--Source  fullcalendar.io-->

  <link href='./assets/js/fullcalendar/packages/core/main.css' rel='stylesheet' />
  <link href='./assets/js/fullcalendar/packages/daygrid/main.css' rel='stylesheet' />
  <link href='./assets/js/fullcalendar/packages/list/main.css' rel='stylesheet' />
  <link href='./assets/js/fullcalendar/packages/timegrid/main.css' rel='stylesheet' />

  <script src='./assets/js/fullcalendar/packages/core/main.js'></script>
  <script src='./assets/js/fullcalendar/packages/daygrid/main.js'></script>
  <script src='./assets/js/fullcalendar/packages/list/main.js'></script>
  <!-- <script src='../assets/js/fullcalendar/packages/interaction/main.js'></script> -->
  <script src='./assets/js/fullcalendar/packages/timegrid/main.js'></script>


</head>
<body>
   
    <!-- Mobile responsive navbar -->
    <nav class="navbar navbar-expand-md schedule-it-top-hat">
        <div class="container-fluid">
            <a class="navbar-brand logo" href="https://oregonstate.edu"><img src="https://oregonstate.edu/themes/osu/drupal8-osuhomepage/logo.svg"></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                <span><i class="fas fa-bars fa-1x"></i></span>
            </button>

            <!-- Collapsible content -->
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav schedule-it-main-menu mr-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="homepage.php">Schedule-It Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="calendar.php">Calendar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="eventmanagement.php">Manage Events</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view_history.php">Past Meetings</a>
                    </li>
                </ul>
                <ul class="navbar-nav schedule-it-main-menu ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <p>

	<!-- Event Info -->
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-3"></div>
			<div class="col-sm-6"><h3 class="text-center" id="eventTitle"></h3></div>
			<div class="col-sm-3"></div>
		</div>
		<div class="row">
			<div class="col-sm-2"></div>
			<div class="col-sm-8"><p id="eventDesc"></p></div>
			<div class="col-sm-2"></div>
		</div>
		<div class="row">
			<div class="col-sm-4"><h6 class="text-left" id="RSVPslotLim"></h6></div>
			<div class="col-sm-8"></div>
		</div>
	</div>
	
    <!-- div for List-->
    <div class="container-fluid" id="content">
        
    </div>


    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>
