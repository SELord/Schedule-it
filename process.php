<?php
	//This file only processes POST and GET requests. Everything else redirects back 
	//to login page
	if ($_SERVER["REQUEST_METHOD"] != "POST" && $_SERVER["REQUEST_METHOD"] != "GET") {
		header("Location: http://web.engr.oregonstate.edu/~ohsa/Schedule-it/login.php");
	}

	require_once './database/dbconfig.php';
	require_once './database/dbquery.php';
	//Post request is from backdoor
	if ($_SERVER["REQUEST_METHOD"] == "POST") { //from backdoor
		$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
		if ($mysqli->connect_errno) {
	    	echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
			exit;
		}
		$data = lookupUser($mysqli, $_POST["user"]);
		$user = json_decode($data);
		//var_dump($user);
		if (!$user->id) {
			echo "Backdoor User Does Not Exist. Contact Service Team.";
			exit;
		}

		session_start();
		$_SESSION["loggedin"] = TRUE;
		$_SESSION["userID"] = $user->id;
		$_SESSION["onidID"] = $user->onidUID;
		$_SESSION["firstName"] = $user->firstName;
		$_SESSION["lastName"] = $user->lastName;
		$_SESSION["email"] = $user->email;
		$_SESSION["backdoor"] = TRUE;
		
		//var_dump($_SESSION);
		header("Location: http://web.engr.oregonstate.edu/~ohsa/Schedule-it/homepage.php");
	} 
	else {  //GET from OSU CAS
		//TODO: Receives the ticket in the URL, then check with CAS serviceValidate endpoint
		//Let user login for now to see the sample page
		session_start();
		$_SESSION["loggedin"] = TRUE;
		header("Location: http://web.engr.oregonstate.edu/~ohsa/Schedule-it/homepage.php");
	}
?>
