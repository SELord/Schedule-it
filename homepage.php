<?php
    include 'file_path.php';
	// session 
	session_start();
    //check once again if the user is logged in
    //if not, redirect back to login page
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] == FALSE) {   
        session_destroy();   
        header("Location: " . $FILE_PATH . "login.php");  
    }   
	
	// database connection 
	require './database/dbconfig.php';
	// functions for accessing database
	require './database/dbquery.php';
	
	// connect to database 
	$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
	if ($mysqli->connect_errno) {
		die("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error . "\n");
	}
	
	
	// get userID from session 
    //Find userID based off of onid
     $data = lookupUser($mysqli, $_SESSION["onidID"]);
     $user = json_decode($data);
	
	// get past events, invites, and reservations from database 
	$events = eventCreateHist($mysqli, $user->id);
	$invites = inviteHist($mysqli, $user->id);
	$reservations = reservedSlotHist($mysqli, $user->id);
	
	// process events to build an array for fullcalendar.io
	$pastEvents = array();
	for($i = 0; $i < count($events); $i++){
		$tmp = array();
		$tmp['id'] = $events[$i]['id'];
		$tmp['title'] = $events[$i]['title'];
		$tmp['start'] = $events[$i]['dateStartTime'];
		$tmp['end'] = substr($events[$i]['dateStartTime'],0,10) . " " . eventEndTime($mysqli, $events[$i]['id']);
		$pastEvents[$i] = $tmp;
	}
	
	// process invites to build an array for fullcalendar.io
	$pastInvites = array();
	for($i = 0; $i < count($invites); $i++){
		$tmp = array();
		$tmp['id'] = $invites[$i]['inviteID'];
		$tmp['title'] = $invites[$i]['title'] . " (" . $invites[$i]['status'] . ")";
		$tmp['start'] = $invites[$i]['dateStartTime'];
		$tmp['end'] = substr($invites[$i]['dateStartTime'],0,10) . " " . eventEndTime($mysqli, $invites[$i]['eventID']);
		$pastInvites[$i] = $tmp;
	}
	
	// process reservations to build an array for fullcalendar.io
	$pastReservations = array();
	for($i = 0; $i < count($reservations); $i++){
		$tmp = array();
		$tmp['id'] = $reservations[$i]['slotID'];
		$tmp['title'] = $reservations[$i]['title'] . ", Location: " . $reservations[$i]['location'];
		$tmp['start'] = substr($reservations[$i]['dateStartTime'],0,10) . 'T' . $reservations[$i]['startTime'];
		$tmp['end'] = substr($reservations[$i]['dateStartTime'],0,10) . 'T' . $reservations[$i]['endTime'];
		$tmp['url'] = './view_reservation.php?slot=' . $reservations[$i]['slotID'];
		$pastReservations[$i] = $tmp;
	}
	
	
	// send to javascript on client
	echo "<script>\n";
	echo "var pastEvents = " . json_encode($pastEvents) . ";\n";
	echo "var pastInvites = " . json_encode($pastInvites) . ";\n";
	echo "var pastReservations = " . json_encode($pastReservations) . ";\n";
	echo "</script>";


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
  <script src="./assets/js/view_history.js"></script>
  


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
                  <a href="calendar.php" class="nav-link">Calendar</a>
                </li>
                <li class="nav-item">
                  <a href="eventmanagement.php" class="nav-link">Manage Events</a>
                </li>
                <li class="nav-item">
                  <a href="view_history.php" class="nav-link">Past Meetings</a>
                </li>
                <!-- Temporary spacing fix -->
                　　　　　　　　　　　　　　　　　　　　　　　
                <li class="nav-item">
                  <a href="logout.php" class="nav-link">Logout</a>
                </li>
              </ul>
            </nav>
        </header>
    </div><p>
	
	<!-- buttons to switch between past events created by user and slots reserved by user -->
	<div class="container-fluid">
	    <div class="row">
			<div class="col-sm-3"></div>
			<div class="col-sm-2"><button type="button" class="btn btn-block" onclick="showResHist(event)" id="resHistButton" disabled>Reservations</div>
			<div class="col-sm-2"><button type="button" class="btn btn-block" onclick="showInviteHist(event)" id="inviteHistButton" >Invites</div>
			<div class="col-sm-2"><button type="button" class="btn btn-block" onclick="showEventHist(event)" id="eventHistButton">Created Events</div>
			<div class="col-sm-3"></div>
		</div>
	</div>
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-2"></div>
			<div class="col-sm-8"><h5 class="text-center" id="viewTitle"></h5></div>
			<div class="col-sm-2"></div>
		</div>
	</div>
	<!-- div for calendar-->
    <div class="container-fluid" id="content">
        
    </div>


	<!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>