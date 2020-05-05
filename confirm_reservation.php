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
	$userID = $_SESSION['userID'];   
	
	// extract inviteID, slotID, and eventID from POST request
	$inviteID = $_POST['inviteID'];
	$slotID = $_POST['slotID'];
	$eventID = $_POST['eventID'];
	
	// data used for creating reservation
	$resData['inviteID'] = $inviteID;
	$resData['slotID'] = $slotID;

	// create reservation on database
	$status = null;
	$success = newReservation($mysqli, $resData);
	if ($success){
		$status = "SUCCESS!!!";
		inviteStatusUpdate($mysqli, $inviteID, 'accepted');
	}
	else{
		// problem with making reservation, may already exist
		$status = "ERROR!!! Reservation could not be created.";
	}
	
	// get event & slot info from database for page display purposes 
	$eventInfo = eventDetails($mysqli, $eventID);
	$eventDate = substr($eventInfo['dateStartTime'], 0, 10);
	$slotInfo = slotDetails($mysqli, $slotID);
	
	
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
  <!-- <script src="./assets/js/make_reservation.js"></script> -->


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

	<!-- Display reservation status -->
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-3"></div>
			<div class="col-sm-6"><h3 class="text-center" id="eventTitle"><?php echo $eventInfo['title']; ?></h3></div>
			<div class="col-sm-3"></div>
		</div>
		<div class="row">
			<div class="col-sm-2"></div>
			<div class="col-sm-8"><p id="eventDesc"><?php echo $eventInfo['description']; ?></p></div>
			<div class="col-sm-2"></div>
		</div>
		<div class="row">
			<div class="col-sm-12"><h4 class="text-center" id="status"><?php echo $status; ?></h4></div>
		</div>
		<div class="row">
			<div class="col-sm-4"><h6 class="text-left" id="date"><?php echo "Date: " . $eventDate; ?></h6></div>
			<div class="col-sm-4"><h6 class="text-center" id="time"><?php echo "Time: " . $slotInfo['startTime'] . "-" . $slotInfo['endTime']; ?></h6></div>
			<div class="col-sm-4"><h6 class="text-right" id="location"><?php echo "Location: " . $slotInfo['location']; ?></h6></div>
		</div>
	</div>
	
	<!-- Form to submit a message and upload a file -->
	
	<!-- Buttons to return to event reservation or return home -->
	    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-4">
                <a href="./homepage.php" class="btn btn-block">Home</a>
            </div>
            <div class="col-sm-4">
				<a href="./view_reservation.php?slot=<?php echo $slotID; ?>&invite=<?php echo $inviteID ?>" class="btn btn-block">View Reservation</a>
			</div>
			<div class="col-sm-4">
				<a href="./make_reservation.php?invite=<?php echo $inviteID; ?>" class="btn btn-block">Reserve Another Spot</a>
			</div>
        </div>
    </div>
	
	
	
	
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>	