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
	

	 //Find userID based off of onid
     $data = lookupUser($mysqli, $_SESSION["onidID"]);
     $user = json_decode($data);

	// get userID from session 
	$userID = $user->id;   

	// extract eventID from request
	$eventID = $_GET['event'];

	// query database for event info
	$eventInfo = eventDetails($mysqli, $eventID);
	$eventDate = substr($eventInfo['dateStartTime'], 0, 10);
	$eventInfo['date'] = $eventDate;
	

	// get posts to slot from database
    // Commented out due to thought needed on how to display this data at the event level
	//$posts = slotPosts($mysqli, $slotID);
	
	// get list of attendees who have reserved the same slot 
	//$attendees = slotAttendees($mysqli, $slotID);

	// send to javascript on client
	echo "<script>\n";
	echo "let eventDetails = " . json_encode($eventInfo) . ";\n";
	//echo "var slotDetails = " . json_encode($slotInfo) . ";\n";
	//echo "var posts = " . json_encode($posts) . ";\n";
	//echo "var attendees = " . json_encode($attendees) . ";\n";
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
  <script src="./assets/js/manage_event.js"></script>


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

    <!-- Buttons to perform actions related to the event -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-3"></div>
            <div class="col-sm-2">
                <button type="button" class="btn btn-block" data-toggle="modal" data-target="#eventLinkModal">Get Shareable Link</button>
            </div>
            <div class="col-sm-2">
                <button type="button" class="btn btn-block" data-toggle="modal" data-target="#eventAnnouncementModal">Send Announcement</button>
            </div>
            <div class="col-sm-2"><button type="button" class="btn btn-block" onclick="editEvent(event)" id="editEventButton">Edit Event</div>
            <div class="col-sm-3"></div>
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
            <div class="col-sm-2"></div>
			<div class="col-sm-4"><h5 class="text-left" id="startTime"></h5></div>
            <div class="col-sm-1"></div>
			<div class="col-sm-4"><h5 class="text-center" id="endTime"></h5></div>
            <div class="col-sm-1"></div>
			<!-- <div class="col-sm-4"><h5 class="text-right" id="location"></h5></div> -->
		</div>
		<div class="row">
            <div class="col-sm-1"></div>
			<div class="col-sm-10"><p id="eventDesc"></p></div>
			<div class="col-sm-1">
			<!--	<a class="btn btn-block" href=<?php // echo "edit_reservation?invite=$inviteID&slotID=$slotID"?>>Edit Reservation</a> -->
			</div>
		</div>
	<!--	<div class="row">
			<div class="col-sm-6"><h6 class="text-left" id="remainingRes"></h6></div>
			<div class="col-sm-2"></div>
			<div class="col-sm-4"><button type="button" class="btn btn-block" data-toggle="modal"
			data-target="#attendeeListModal">Attendee List</button></div>
		</div> -->
	</div>
	
	<!-- Modal to display and copy event invite link -->
    <!-- TODO: Having a shareable link would require re-thinking the invite db table since it currently only creates a new invite id when a user email gets added/sent -->
	<div id="eventLinkModal" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Event Invite Link</h4>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<ul id="eventLink" class="list-group">
					</ul>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>



    <!-- Modal to receive input for announcement -->
	<form method="POST" id="announceModalForm">
        <div id="eventAnnouncementModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header text-center">
                        <h4 class="modal-title">Send Announcement</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body mx-3">
                        <div class="md-form mb-5">
                            <label data-error="wrong" data-success="right" for="announce-subject">Subject</label>
                            <input type="text" id="announce-subject" class="form-control validate">
                        </div>

                        <div class="md-form">
                            <label data-error="wrong" data-success="right" for="announce-message">Your message</label>
                            <textarea type="text" id="announce-message" class="md-textarea form-control" rows="6"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="submitAnnouncement" class="btn btn-default">Send</button>
                    </div>
                </div>
            </div>
        </div>
    </form>



    <div class="container" id="slot-confirmations">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col"></th>
                    <th scope="col" class="text-center">Slot 1 Date/Time</th>
                    <th scope="col" class="text-center">Slot 2 Date/Time</th>
                    <th scope="col" class="text-center">Slot 3 Date/Time</th>
                </tr>
                <tr>
                    <th scope="col"># of Participants</th>
                    <th scope="col" class="text-center">Slot 1 total</th>
                    <th scope="col" class="text-center">Slot 2 total</th>
                    <th scope="col" class="text-center">Slot 3 total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">lords</th>
                    <td></td>
                    <td class="text-center">&#x2705</td>
                    <td></td>
                </tr>
                <tr>
                    <th scope="row">ohsa</th>
                    <td class="text-center">&#x2705</td>
                    <td class="text-center">&#x2705</td>
                    <td></td>
                </tr>
                <tr>
                    <th scope="row">fisherv</th>
                    <td></td>
                    <td class="text-center">&#x2705</td>
                    <td class="text-center">&#x2705</td>
                </tr>
            </tbody>
        </table>
    </div>



	<!-- Posts -->
<!--
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
-->    
	<!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>
