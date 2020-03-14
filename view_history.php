<?php
	// session 
	session_start();
    //check once again if the user is logged in
    //if not, redirect back to login page
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] == FALSE) {   
        session_destroy();   
        header("Location: http://web.engr.oregonstate.edu/~alasagae/Schedule-it/login.php");  
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
    <div class="container-fluid">
        <div class="row border-bottom border-dark">
            <div class="col-sm-3"><h2>Schedule-it</h2></div>
            <div class="col-sm-6"></div>

            <div class="col-sm-3"><img src="./assets/img/OSU_horizontal_2C_O_over_B.png" alt="OSU Logo" width=60% height=auto></div>

        </div>
    </div>
    
    <!-- Hamburgur menu -->
    <div class="container-fluid">
        <div class="row" >
            <div class="col-sm-1">
                <div class="menu-wrapper border-dark">
                    <input type="checkbox" class="toggle"/>
        
                    <div class="hamburger">
                        <div class="bar">  
                        </div>
                    </div>
        
                    <div class="menu">
                        <ul>
                            <li><a href="homepage.php">Home</a></li>
                            <!-- <li><a href="#">Upcoming Events</a></li>
                            <li><a href="#">Upcoming Meetings</a></li>-->
                            <li><a href="view_history.php">Past Meetings</a></li> 
                            <li><a href="logout.php">Logout</a></li>  
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-sm-2"><h4>Events & Reservations History</h4></div>
            <div class="col-sm-9"></div>
        </div>
    </div>
	
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