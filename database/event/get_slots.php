<?php
// get all the slots for the event

	$id = $_POST['id'];

	//edit_slot.php
	require_once '../dbconfig.php';
	require_once '../dbquery.php';

	$connect = new mysqli($dbhost, $dbuser, $dbpass, $dbname, $dbport);
	if ($connect->connect_errno) {
		echo "Failed to connect to MySQL: (" . $connect->connect_errno . ") " . $connect->connect_error;
	}

	$result = eventSlots($connect, $id);

	echo JSON_encode($result);
?>