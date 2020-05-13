<?php

	$id = $_POST['id'];

	//edit_slot.php
	require_once '../dbconfig.php';
	require_once '../dbquery.php';

	$connect = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
	if ($connect->connect_errno) {
		echo "Failed to connect to MySQL: (" . $connect->connect_errno . ") " . $connect->connect_error;
	}

	$result = eventSlots($connect, $id);
	$output = '
		<div class="table-responsive" title="Edit Event Slots">
			 <table class="table table-bordered" id="slotEditTable" style="width:100%">
				<tr>
					 <th>startTime</th>
					 <th>endTime</th>
					 <th>location</th>
					 <th>RSVPlim</th>
					 <th>Delete</th>
				</tr>';
	if(sizeof($result) > 0)
	{
		foreach($result as $row)
		{
			 $output .= '
				<tr>	
					<td id="slot'.$row["id"].'"><input type="time" class="slotStartTimeEdit" data-id="'.$row["id"].'" value="'.$row["startTime"].'"></td>
					<td id="slot'.$row["id"].'"><input type="time" class="slotEndTimeEdit" data-id="'.$row["id"].'" value="'.$row["endTime"].'"></td>
					<td id="slot'.$row["id"].'"><input type="text" class="slotLocationEdit" data-id="'.$row["id"].'" value="'.$row["location"].'"></td>
					<td id="slot'.$row["id"].'"><input type="number" class="slotRVSPlimEdit" data-id="'.$row["id"].'" value="'.$row["RSVPlim"].'" style="width: 4em"></td>
					<td id="slot'.$row["id"].'"><button type="button" class="btn btn-danger slotDeleteButton" data-id="'.$row["id"].'">X</button></td>
				</tr>
			 ';
		}
	}
	else
	{
		$output .= '<tr>
					<td colspan="5">Data not Found</td>
					</tr>';
	}
	$output .= '</table>
		</div>';
	echo $output;
?>