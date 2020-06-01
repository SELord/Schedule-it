<?php 
// PHP error reporting for debug info. Commented out for production
// For more information: https://stackify.com/display-php-errors/
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

    require_once 'file_path.php';

	// session 
	session_start();   
    //check once again if the user is logged in
    //if not, redirect back to login page
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] == FALSE) {   
        session_destroy();   
        header("Location: login.php");   
    }   
	
	if ($_SERVER["REQUEST_METHOD"] != "GET") {
        header("Location: " . $FILE_PATH . "homepage.php");
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
	
	//Find userID based off of onid
	$data = lookupUser($mysqli, $_SESSION["onidID"]);
	$user = json_decode($data);

	// get userID from session 
	$userID = $user->id;   
	
	// extract inviteID from request
	$inviteID = $_GET['invite'];
	$slotID = $_GET['slotID'];

	// get the slot information
	// Output: if slot is found, then a 1D associative array containing the info, else NULL 
	// 		array keys: id, startTime, location, RSVPlim, eventID, endTime
	//
	$slotInfo = slotDetails($mysqli, $slotID);

	// query database for user post of this slot
	// Output: if any are found, then a 1D associative array containing slot info with id, text, fileName, timeStamp, userID, firstName, lastName
	$userSlotPost = userSlotPost($mysqli, $slotID, $userID);
	if ($userSlotPost) {
		$postID = $userSlotPost["id"];
		$fileName = $userSlotPost["fileName"];
	}
	else {
		$postID = NULL;
		$fileName = NULL;
	}
		
	// query db for invite info 
	//Output: if invite is found, then a 1D associative array containing the info, else NULL 
	// 		array keys: id, email, status, receiverID, eventID
	$inviteInfo = inviteDetails($mysqli, $inviteID);

	// query database for event info
	$eventInfo = eventDetails($mysqli, $inviteInfo['eventID']);
	$eventDate = explode(" ", $eventInfo['dateStartTime'])[0];
	
	
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
			<div class="col-sm-6"><h3 class="text-center" id="eventTitle"><?php echo $eventInfo["title"]?></h3></div>
			<div class="col-sm-3"></div>
		</div>
		<div class="row">
			<div class="col-sm-2"></div>
			<div class="col-sm-8"><p id="eventDesc"><?php echo $eventInfo["description"]?></p></div>
			<div class="col-sm-2"></div>
		</div>
	</div>
	
    <!-- div for List-->
    <div class="container-fluid" id="content">
		<div class="row">
			<div class="col-sm-2"><h4>Slot:</h4></div>
			<div class="col-sm-6"></div>
			<div class="col-sm-4">
				<form action="reservation_process.php" method="post" id="deleteReservation">
					<input type="hidden"  name="postID" value=<?php echo $postID ?>>
					<input type="hidden"  name="slotID" value=<?php echo $slotID ?>>
					<input type="hidden"  name="delete" value="true">
					<input type="hidden"  name="inviteID" value=<?php echo $inviteID ?>>
					<button class="btn btn-primary" type="submit">Cancel Reservation</button>
				</form>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-4">Time: <?php echo $slotInfo["startTime"] ?> to <?php echo $slotInfo["endTime"] ?>, <?php echo $eventDate ?></div>
			<div class="col-sm-8"></div>
		</div>
		<div class="row">
			<div class="col-sm-4">Location: <?php echo $slotInfo["location"] ?></div>
			<div class="col-sm-8"></div>
		</div>

		<form action="reservation_process.php" method="post" enctype="multipart/form-data">
			<div class="form-group">
				<label for="postMessage">Message</label>
				<textarea class="form-control" id="postMessage" name="text" maxlength="1000" rows="3"><?php if ($userSlotPost && $userSlotPost["text"]) {echo $userSlotPost["text"];}?></textarea>
			</div>
			<div class="form-group">
				<?php
					if ($fileName)
						echo "<span>File Uploaded: <em>$fileName</em></span><br>";
				?>
				<label for="postFile">
					<?php 
						if ($fileName)
							echo "Replace PDF File (Max size: 5 MB)";
						else
							echo "Upload PDF File (Max size: 5 MB)";
					?>
				</label>
				<input type="file" class="form-control-file" id="postFile" name="postFile" accept="application/pdf">
			</div>
			<!--hidden field-->
			<input type="hidden" id="postID" name="postID" value=<?php echo $postID ?>>
			<input type="hidden" id="slotID" name="slotID" value=<?php echo $slotID ?>>
			<button class="btn btn-primary" type="submit" id="updatePost">Submit</button>
			<!-- <button class="btn btn-primary" value="submit" type="submit">Submit</button> -->
		</form>
		<!--delete post-->
		<form action="reservation_process.php" method="post" enctype="multipart/form-data">
			<input type="hidden" id="postID" name="postID" value=<?php echo $postID ?>>
			<input type="hidden" id="slotID" name="slotID" value=<?php echo $slotID ?>>
			<input type="hidden" id="deletePost" name="deletePost" value="true">
			<button class="btn btn-primary">Delete</button>
        </form>
    </div>
	<script>
		//source :https://stackoverflow.com/questions/5697605/limit-the-size-of-an-file-upload-html-input/17173301#17173301
		let uploadField = document.getElementById("postFile");
		uploadField.onchange = function() {
			let maxSize = 5242880;
			if (this.files[0].size > maxSize) {
				alert("File is too big!");
				this.value = "";
			}
		};
	</script>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>
