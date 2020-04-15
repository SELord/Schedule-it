<?php
	// session 
	session_start();
    //check once again if the user is logged in
    //if not, redirect back to login page
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] == FALSE) {   
        session_destroy();   
        header("Location: http://web.engr.oregonstate.edu/~" . $DEV_ONID . "/Schedule-it/login.php"); 
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
	

	 //Find userID based off of onid
     $data = lookupUser($mysqli, $_SESSION["onidID"]);
     $user = json_decode($data);

	// get userID from session 
	$userID = $user->id;   


	// extract slotID from request
	$slotID = $_GET['slot'];
	$inviteID = $_GET['inviteID'];

	// TODO: check that user who is logged in has a reservation for this slot and only display info if they do 

	// get slot info from database
	// Output: if slot is found, then a 1D associative array containing the info, else NULL 
	// 		array keys: id, startTime, duration, location, RSVPlim, eventID, endTime

	//BUG: Could not get "slotDetails" to work - redo function slotDetails_elaine
	$slotInfo = slotDetails($mysqli, $slotID);
	
	// query database for event info
	$eventInfo = eventDetails($mysqli, $slotInfo['eventID']);
	$eventDate = substr($eventInfo['dateStartTime'], 0, 10);
	$eventInfo['date'] = $eventDate;
	

	
	// get reservation count from database
	$RSVPcnt = slotRSVPCount($mysqli, $slotID);
	$remainingRes = $slotInfo['RSVPlim'] - $RSVPcnt;
	$slotInfo['remainingRes'] = $remainingRes;
	
	// get posts to slot from database 
	$posts = slotPosts($mysqli, $slotID);
	
	// get list of attendees who have reserved the same slot 
	$attendees = slotAttendees($mysqli, $slotID);

	// send to javascript on client
	echo "<script>\n";
	echo "var eventDetails = " . json_encode($eventInfo) . ";\n";
	echo "var slotDetails = " . json_encode($slotInfo) . ";\n";
	echo "var posts = " . json_encode($posts) . ";\n";
	echo "var attendees = " . json_encode($attendees) . ";\n";
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
  <script src="./assets/js/view_reservation.js"></script>


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
            <div class="col-sm-2"><h4>Home Page</h4></div>
            <div class="col-sm-9"></div>
        </div>
    </div>

	<!-- Event Info -->
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-3"></div>
			<div class="col-sm-6"><h3 class="text-center" id="eventTitle"></h3></div>
			<div class="col-sm-3"></div>
		</div>
		<div class="row">
			<div class="col-sm-3"></div>
			<div class="col-sm-6"><h4 class="text-center" id="eventDate"></h4></div>
			<div class="col-sm-3"></div>
		</div>
		<div class="row">
			<div class="col-sm-4"><h5 class="text-left" id="startTime"></h5></div>
			<div class="col-sm-4"><h5 class="text-center" id="endTime"></h5></div>
			<div class="col-sm-4"><h5 class="text-right" id="location"></h5></div>
		</div>
		<div class="row">
			<div class="col-sm-8"><p id="eventDesc"></p></div>
			<div class="col-sm-4">
				<a class="btn btn-block" href=<?php echo "edit_reservation?invite=$inviteID&slotID=$slotID"?>>Edit Reservation</a>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-6"><h6 class="text-left" id="remainingRes"></h6></div>
			<div class="col-sm-2"></div>
			<div class="col-sm-4"><button type="button" class="btn btn-block" data-toggle="modal"
			data-target="#attendeeListModal">Attendee List</button></div>
		</div>
	</div>
	
	<!-- Modal for Attendee List to display who is attending the event -->
	<div id="attendeeListModal" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Attendee List</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<ul id="attendeeList" class="list-group">
					</ul>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>


	<!-- Posts -->
	<div class="container-fluid" id="content">

        <table class="table-responsive table-bordered table-striped">

			<thead>
				<tr>
					<th scope="col">Time</th>
					<th scope="col">Attendee</th>
					<th scope="col">Message</th>
					<th scope="col">File</th>
				</tr>
			</thead>
			<tbody id="posts">
			</tbody>
		</table>
    </div>
    
	<!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>