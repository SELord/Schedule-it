<?php 
	include '../../file_path.php';
//--------------------------------------------------------------------------------------------------
// Function: newEventEmail(conn, eventID)
// Description: send out an email notification for a new event to all users who are invited.
// 				subject line will be "NEW EVENT: " + event title
// Input: 
//		conn: database connection 
//		eventID: event ID on database 
// Output: none
function newEventEmail($conn, $eventID){
	global $FILE_PATH;
	$result = getEventEmails($conn, $eventID);

	if (count($result) > 0){
		for ($i = 0; $i < count($result); $i++){
			$subject = $result[$i]['title'];
			$url = $FILE_PATH . "make_reservation?invite=" . $result[$i]['id'];
			$message = "You are invited to a new event.  Use this link to get more details and reserve your spot: " . $url;
			sendEmail($result[$i]['email'], $subject, $message, 'newEvent');
		}
	}
	
}


//--------------------------------------------------------------------------------------------------
// Function: updateEventEmail(conn, eventID)
// Description: send out an email notification for an update to an event to all users who are invited.
// 				subject line will be "UPDATE: " + event title
// Input: 
//		conn: database connection 
//		eventID: event ID on database 
// Output: none
function updateEventEmail($conn, $eventID){
	global $FILE_PATH;
	$result = getEventEmails($conn, $eventID);
	
	if ($result != NULL){
		for ($i = 0; $i < count($result); $i++){
			$subject = $result[$i]['title'];
			$url = $FILE_PATH . "make_reservation?invite=" . $result[$i]['id'];
			$message = "An event you are invited to has been updated.  Use this link to get more details: " . $url;
			sendEmail($result[$i]['email'], $subject, $message, 'update');
		}
	}
	
}


//--------------------------------------------------------------------------------------------------
// Function: updateSlotEmail(conn, slotID)
// Description: send out an email notification for an update to a slot to all users who have a reservation.
// 				subject line will be "UPDATE: Reservation to event " + event title
// Input: 
//		conn: database connection 
//		eventID: slot ID on database 
// Output: none
function updateSlotEmail($conn, $slotID){
	global $FILE_PATH;
	$stmt = $conn->prepare("SELECT U.email, E.title FROM scheduleit_Slot S
			INNER JOIN Reservation R ON S.id = R.slotID 
			INNER JOIN Invite I ON R.inviteID = I.id
			INNER JOIN User U ON I.receiverID = U.id
			INNER JOIN Event E ON E.id = S.eventID
			WHERE S.id = ?");
	$stmt->bind_param("i", $slotID);
	$result = NULL;
	if ($stmt->execute()){
		$result = $stmt->get_result();
		$result = $result->fetch_all(MYSQLI_ASSOC);
	}
	else{
		$result = NULL;
	}
	
	if ($result != NULL){
		for ($i = 0; $i < count($result); $i++){
			$subject = "Reservation to event " . $result[$i]['title'];
			$url = $FILE_PATH . "view_reservation?slot=" . $result[$i]['id'];
			$message = "An update has been made to your reservation for an event.  Use this link to get more details: " . $url;
			sendEmail($result[$i]['email'], $subject, $message, 'update');
		}
	}
	
}

//--------------------------------------------------------------------------------------------------
// Function: sendEmail(to, subject, message, type)
// Description: send email to the provided address with the provided subject line.  Append UPDATE,  
//				NEW EVENT, CANCELED, or nothing to subject based on type variable. if a type is 'none'
//				or is not recognized nothing will be appended to subject.  
//				email will be from NOREPLY.ScheduleIt@oregonstate.edu
// Input: 
//		to: recipient's email address
//      subject: text of emails subject line 
//      message: body of email
//		type: one of the following strings: 'update', 'newEvent', 'cancel', or 'none'
// Output: none
function sendEmail($to, $subject, $message, $type){
	// validate email address is correct formate 
	if (filter_var($to, FILTER_VALIDATE_EMAIL)){
		// email formate is valid 
		
		// append to subject based on type variable 
		switch ($type){
			case "update":
				$subject = "UPDATE: " . $subject;
				break;
			case "newEvent":
				$subject = "NEW EVENT: " . $subject;
				break;
			case "cancel":
				$subject = "CANCELED: " . $subject;
			default:
				$subject = $subject;
		}
		
		// append no reply explanation to bottom of message 
		$message = $message . "\n\n*********************************************\nDO NOT reply, this email was sent from an unmonitored account.";
		
		// make message readable 
		$message = wordwrap($message, 70);
		
		$from = "From: NOREPLY.ScheduleIt@oregonstate.edu";
		
		// send the email
		mail($to, $subject, $message, $from);
	}
}

?>