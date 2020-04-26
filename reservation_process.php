<?php 
	include 'file_path.php';
	// session 
	session_start();
	//check once again if the user is logged in
	//if not, redirect back to login page
	 if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] == FALSE) {
		session_destroy();
		header("Location: login.php");
	}
	// database connection 
	require_once './database/dbconfig.php';
	
	// functions for accessing database
	require_once './database/dbquery.php';
	
	if ($_SERVER["REQUEST_METHOD"] != "POST") {
	header("Location: " . $FILE_PATH . "homepage.php");
	} 

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

	$status;	// for later use

	// function used for verifying the user and deleting post
	function deletePost($conn, $user, $postID, $slotID, &$status) {
		// verify user first (the user who created the post must be the one deleting it)
		if ($user->id != postOwner($conn, $postID)["senderID"]) {
			$status = "User verification failed - post delete";
		}
		// delete the file first if it exists
		$userSlotPost = userSlotPost($conn, $slotID, $user->id);
		if (!isset($status) && $userSlotPost["fileName"]) {
			if(!unlink("files/" . $user->onidUID . "_slot" . $userSlotPost["slotID"] . "_" . $userSlotPost["fileName"])) {
				$status = "File Delete Failed (post undeleted)";
			}
		}
		// then delete the post
		if(!isset($status)) {
			if (postDelete($conn, $postID)) {
				$status = "Post Successfully Deleted";
			} else {
				$status = "Post Delete Failed";
			} 
		}
	}
	//update or insert post
	if (!isset($_POST["delete"])) {
		$fileName = $_FILES["postFile"]["name"];

		// delete the message (post) from the reservation board
		if ($_POST["deletePost"]) {
			deletePost($mysqli, $user, $_POST["postID"], $_POST["slotID"], $status);
		}
		//Upload the file. $status is set if failed.
		if (!isset($status) && $fileName) { 
			//make sure that the file is truly a pdf file
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mime = finfo_file($finfo, $_FILES["postFile"]["tmp_name"]);
			finfo_close($finfo);
			if ($mime != "application/pdf")
			{
				$status = "Only pdf file is permitted";
			} else {
				// save to "./files/" directory ("./files/{{onid}}_slot{{slotID}}_filename")
				$target_file = "files/" . $user->onidUID . "_slot" . $_POST["slotID"] . "_" . $fileName;
				//If user previously uploaded a file, delete it first.
				$previousPost = userSlotPost($mysqli, $_POST["slotID"], $userID);
				if ($previousPost["fileName"]) {
					unlink("files/" . $user->onidUID . "_slot" . $_POST["slotID"] . "_" . $previousPost["fileName"]);
				}
				$moveSuccess = move_uploaded_file($_FILES["postFile"]["tmp_name"], $target_file);
				// change permission of the file so it is accessible by the server
				chmod($target_file, 0644);
				if (!$moveSuccess) {
					$status = "Error uploading file";
				}
			}
		} 
		// If no file upload is needed or the file upload is successful 
		if (!isset($status)) {
			if ($_POST["postID"]) {	//If the post exists, update it
				$result = postUpdate($mysqli, $_POST["postID"], $_POST["text"], $fileName);
			} else {	//If the post does not exist, insert it
				$info = array("senderID" => $userID,
								"text" => $_POST["text"],
								"fileName" => $fileName,
								"slotID" => $_POST["slotID"]);
				$result = newPost($mysqli, $info);
			}
			if ($result) {
				$status = "Post Successfully Updated";
			} else {
				$status = "Unable to update post. Please try again later.";
			}
		}
	//delete the reservation
	} else{
		// verify the user first
		if ($userID != getReservationReceiverID($mysqli, $_POST["inviteID"], $_POST["slotID"])["receiverID"]) {
			$status = "User verification failed - reservation delete";
		}
		// deleting the post
		if (!isset($status) && $_POST["postID"]) {
			deletePost($mysqli, $user, $_POST["postID"], $_POST["slotID"], $status);
		}
		// then delete the reservation
		if(!isset($status)) {
			$result = reservationDelete($mysqli, $_POST["inviteID"], $_POST["slotID"]);
			if ($result) {
				$status = "Reservation Deleted";
				$event = eventFromInviteID($mysqli, $_POST["inviteID"]);
				$RSVPs = userEventRSVPCount($mysqli, $userID, $event["id"]);
				if ($RSVPs == 0)
					inviteStatusUpdate($mysqli, $_POST["inviteID"], "no response");
				//delete the file if user had uploaded one.
				//To reduce a query to the database, just try to delete the file no matter whether it exists.
				//$target_file = "files/FILE_".strval($_POST["slotID"])."_".strval($userID);
				//unlink($target_file);
			} else 
				$status = "Unable to delete reservation. Please try again later.";
		}
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
	


	<!--fullcalendar-->
	<!--Use daygrid-views for homepage -->
	<!--Use Selectable for event creation page-->
	<!--Source	fullcalendar.io-->

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
			<div class="col-sm-2"><h4>Reservation Update</h4></div>
			<div class="col-sm-9"></div>
		</div>
	</div>
	
	<!-- div for List-->
	<div class="container-fluid" id="content">
		<?php 
			echo $status;
		?>
	</div>

	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-2">
				<a href="homepage.php" class="btn btn-block">Homepage</a>
			</div>
			<div class="col-sm-10"></div>
		</div>
	</div>

	<!-- Optional JavaScript -->
	<!-- jQuery first, then Popper.js, then Bootstrap JS -->
	<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>