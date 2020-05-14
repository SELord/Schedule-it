<?php 
	//UPDATE EDIT_SLOT BUTTON GETS SLOT INFORMATION
	//edit_slot.php
	require_once '../dbconfig.php';
	require_once '../dbquery.php';

	$connect = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
	if ($connect->connect_errno) {
		echo "Failed to connect to MySQL: (" . $connect->connect_errno . ") " . $connect->connect_error;
	}

	if ($_POST["key"] == "add") {
		$result = newSlot($connect, $_POST["id"]);
		//echo $result;
		echo json_encode($result);
	} else if ($_POST["key"] == "delete") {
		slotDelete($connect, $_POST["id"]);
	} else {
		slotUpdate($connect, $_POST);
	}

?>
