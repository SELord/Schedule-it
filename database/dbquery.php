<?php
// PHP error reporting for debug info. Commented out for production
// For more information: https://stackify.com/display-php-errors/
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

//--------------------------------------------------------------------------------------------------
// FUNCTION DOCUMENTATION
//--------------------------------------------------------------------------------------------------
// Function: lookupUser(conn, onidID)
// Description: look up a users info based on OSU user name 
// Input: 
//		conn = MySQL database connection object 
//		onidID = string containing an ONID user name (example: 'smithj')
// Output:  if user is found, then a 1D associative array containing their info, else NULL 
// 		array keys: id, onidID, firstName, lastName, email
//--------------------------------------------------------------------------------------------------
// Function: newUser(conn, info[])
// Description: create a new user in the database
// Input: 
//		conn = MySQL database connection object 
//		info = associative array containing the data for creating a new user using following keys:
//			onidID (OSU user name), firstName, lastName, email
// Output: database id of new user if successful, else false 
//--------------------------------------------------------------------------------------------------
// Function: newEvent(conn, info[])
// Description: create a new event in the database
// Input: 
//		conn = MySQL database connection object 
//		info = associative array containing the data for creating a new event using following keys:
//			title, description, dateStart (YYYY-MM-DD), dateEnd (YYYY-MM-DD), RSVPslotLim, creatorID
// Output: database id of new event if successful, else false
//--------------------------------------------------------------------------------------------------
// Function: newSlot(conn, info[])
// Description: create a new time slot for an event in the database
// Input: 
//		conn = MySQL database connection object 
//		info = associative array containing the data for creating a new time slot using following keys:
//			startDateTime (HH:MM), endDateTime (HH:MM), location, RSVPlim, eventID
// Output:  database id of new time slot if successful, else false
//--------------------------------------------------------------------------------------------------
// Function: lookupInvite(conn, receiverID, eventID)
// Description: lookup invite from receiverID and eventID
// Input: 
//		conn = MySQL database connection object 
//		recevierID = receiver ID from db
//		eventID = event ID from db
// Output: database id of invite if found, else false
//--------------------------------------------------------------------------------------------------
// Function: newInvite(conn, info[])
// Description: create a new invite for an event in the database
// Input: 
//		conn = MySQL database connection object 
//		info = associative array containing the data for creating a new invite using following keys:
//			receiverID, eventID, email
// Output: database id of new invite if successful, else false
//--------------------------------------------------------------------------------------------------
// Function: newPost(conn, info[])
// Description: create a new post to a slot in the database
// Input: 
//		conn = MySQL database connection object 
//		info = associative array containing the data for creating a new post using following keys:
//			senderID, text, fileName, slotID
// Output: database id of new post if successful, else false
//--------------------------------------------------------------------------------------------------
// Function: newReservation(conn, info[])
// Description: create a new reservation for a slot in the database
// Input: 
//		conn = MySQL database connection object 
//		info = associative array containing the data for creating a new reservation using following keys:
//			inviteID, slotID
// Output: true if successful, else false 
//--------------------------------------------------------------------------------------------------
// Function: eventCreateHist(conn, id)
// Description: find all events created by a user in ascending order by event date 
// Input: 
//		conn = MySQL database connection object 
//		id = id of user on database 
// Output: if any are found, then a 2D associative array containing event info with
//         the first dimension being row number of result, else NULL.
//		2nd dim array keys: *
//--------------------------------------------------------------------------------------------------
// Function: inviteHist(conn, id)
// Description: find all events a user has been invited to in ascending order by event date 
// Input: 
//		conn = MySQL database connection object 
//		id = id of user on database 
// Output: if any are found, then a 2D associative array containing event info with
//         the first dimension being row number of result, else NULL.
//		2nd dim array keys:  eventID, inviteID, title, description, dateStart, dateEnd, firstName (of creator), lastName (of creator), status
//--------------------------------------------------------------------------------------------------
// Function: reservedSlotHist(conn, id)
// Description: find all slots with associated events a user has reserved
//				in ascending order by event date 
// Input: 
//		conn = MySQL database connection object 
//		id = id of user on database 
// Output: if any are found, then a 2D associative array containing slot info with
//         the first dimension being row number of result, else NULL.
//		2nd dim array keys: eventID, inviteID, slotID, title, dateStart, dateEnd, startDateTime, location, endDateTime
//--------------------------------------------------------------------------------------------------
// Function: usersAccepted(conn, $id)
// Description: all users who have made a reservation for an event in ascending order by last name 
// Input: 
//		conn = MySQL database connection object 
//		id = id of event on database 
// Output: if any are found, then a 2D associative array containing user info with
//         the first dimension being row number of result, else NULL.
//		2nd dim array keys: firstName, lastName, onidID, email
//--------------------------------------------------------------------------------------------------
// Function: usersDeclined(conn, $id)
// Description: all users who have declined an event invite in ascending order by last name 
// Input: 
//		conn = MySQL database connection object 
//		id = id of event on database 
// Output: if any are found, then a 2D associative array containing user info with
//         the first dimension being row number of result, else NULL.
//		2nd dim array keys: firstName, lastName, onidID, email
//--------------------------------------------------------------------------------------------------
// Function: usersNoResponse(conn, $id)
// Description: all users who have not responded to an event invite in ascending order by last name 
// Input: 
//		conn = MySQL database connection object 
//		id = id of event on database 
// Output: if any are found, then a 2D associative array containing user info with
//         the first dimension being row number of result, else NULL.
//		2nd dim array keys: firstName, lastName, onidID, email
//--------------------------------------------------------------------------------------------------
// Function: invitesUpcoming(conn, id)
// Description: future events that user hasn't responded to in ascending order by event date 
// Input: 
//		conn = MySQL database connection object 
//		id = id of user on database
// Output: if any are found, then a 2D associative array containing event info with
//         the first dimension being row number of result, else NULL.
//		2nd dim array keys: eventID, inviteID, title, dateStart, dateEnd, firstName, lastName
//--------------------------------------------------------------------------------------------------
// Function: eventsUpcoming(conn, id)
// Description: future events a user created in ascending order by event date 
// Input: 
//		conn = MySQL database connection object 
//		id = id of user on database
// Output: if any are found, then a 2D associative array containing event info with
//         the first dimension being row number of result, else NULL.
//		2nd dim array keys: id, title, dateStart, dateEnd
//--------------------------------------------------------------------------------------------------
// Function: reservationsUpcoming(conn, id)
// Description: future slots a user has reserved in ascending order by event date and slot start time 
// Input: 
//		conn = MySQL database connection object 
//		id = id of user on database
// Output: if any are found, then a 2D associative array containing event and slot info with
//         the first dimension being row number of result, else NULL.
//		2nd dim array keys: eventID, inviteID, slotID, title, dateStart, dateEnd, startDateTime, location, endDateTime
//--------------------------------------------------------------------------------------------------
// Function: eventEndTime(conn, $id)
// Description: determine an event's ending time based on end of last associated slot 
// Input: 
//		conn = MySQL database connection object 
//		id = id of event on database
// Output: if found a string representing the ending time of the event in format HH:MM:00, else NULL
//--------------------------------------------------------------------------------------------------
// Function: adminCheck(conn, onidID)
// Description: check if a user is approved for admin access 
// Input: 
//		conn = MySQL database connection object 
//		onidID = string containing an ONID user name (example: 'smithj')
// Output: true if approved, else false 
//--------------------------------------------------------------------------------------------------
// Function: inviteDetails(conn, id)
// Description: database entry for a specific invite 
// Input: 
//		conn = MySQL database connection object 
//		id = id of invite on database
// Output: if invite is found, then a 1D associative array containing the info, else NULL 
// 		array keys: id, email, status, receiverID, eventID
//--------------------------------------------------------------------------------------------------
// Function: eventDetails(conn, id)
// Description: database entry for a specific event 
// Input: 
//		conn = MySQL database connection object 
//		id = id of event on database
// Output: if event is found, then a 1D associative array containing the info, else NULL 
// 		array keys: id, title, description, dateStart, dateEnd, RSVPslotLim, creatorID
//--------------------------------------------------------------------------------------------------
// Function: eventSlots(conn, id)
// Description: all slots for a specific event in ascending order by startDateTime
// Input: 
//		conn = MySQL database connection object 
//		id = id of event on database
// Output: if any are found, then a 2D associative array containing slot info with
//         the first dimension being row number of result, else NULL.
//		2nd dim array keys: id, startDateTime, location, RSVPlim, eventID, endDateTime
//--------------------------------------------------------------------------------------------------
// Function: eventAvailableSlots(conn, id)
// Description: all available slots for a specific event in ascending order by startDateTime
// Input: 
//		conn = MySQL database connection object 
//		id = id of event on database
// Output: if any are found, then a 2D associative array containing slot info with
//         the first dimension being row number of result, else NULL.
//		2nd dim array keys: id, startDateTime, endDateTime, location, eventID, RSVPlim, and RSVPs 
//--------------------------------------------------------------------------------------------------
// Function: eventFromInviteID(conn, inviteID)
// Description: return the eventID given the invite ID
// Input: 
//		conn = MySQL database connection object 
//		inviteID = id of invite on database
// Output: If any is found, then an 1D array of the associated event is returned. Otherwise, null is returned.
//--------------------------------------------------------------------------------------------------
// Function: slotDetails(conn, id)
// Description: 
// Input: 
//		conn = MySQL database connection object 
//		id = id of slot on database 
// Output: if slot is found, then a 1D associative array containing the info, else NULL 
// 		array keys: id, startDateTime, location, RSVPlim, eventID, endDateTime
//--------------------------------------------------------------------------------------------------
// Function: slotPosts(conn, id)
// Description: all posts associated with a slot in ascending order by post timeStamp
// Input: 
//		conn = MySQL database connection object 
//		id = id of slot on database 
// Output: if any are found, then a 2D associative array containing slot info with
//         the first dimension being row number of result, else NULL.
//		2nd dim array keys: id, text, fileName, timeStamp, userID, firstName, lastName
//--------------------------------------------------------------------------------------------------
// Function: userSlotPost(conn, slotID, userID)
// Description: Return post created by userID for slotID
// Input: 
//		conn = MySQL database connection object 
//		slotID = id of slot on database 
//		userID = id of user / senderID
// Output: if any are found, then a 1D associative array containing slot info with id, text, fileName, timeStamp, userID, firstName, lastName
//--------------------------------------------------------------------------------------------------
// Function: postOwner(conn, postID)
// Description: Return post owner (senderID) of the post
// Input: 
//		conn = MySQL database connection object 
//		postID = id of post on database
// Output: if any are found, then a 1D associative array containing sender ID (post owner)
//--------------------------------------------------------------------------------------------------
// Function: userEventRSVPCount(conn, userID, eventID)
// Description: How many reservations for an event a user has made 
// Input: 
//		conn = MySQL database connection object 
//		userID = id of user on database 
//		eventID = id of event on database 
// Output: user's event reservation count 
//--------------------------------------------------------------------------------------------------
// Function: slotRSVPCount(conn, id)
// Description: how many users have reserved a specific slot 
// Input: 
//		conn = MySQL database connection object 
//		id = id of slot on database 
// Output: slot reservation count 
//--------------------------------------------------------------------------------------------------
// Function: slotAttendees(conn, id)
// Description: users who have made a reservation for a slot in ascending order by lastName
// Input: 
//		conn = MySQL database connection object 
//		id = id of slot on database 
// Output: if any are found, then a 2D associative array containing attendees' names with
//         the first dimension being row number of result, else NULL.
//		2nd dim array keys: lastName, firstName
//--------------------------------------------------------------------------------------------------
// Function: eventUpdate(conn, id, info[])
// Description: update an event's details
// Input: 
//		conn = MySQL database connection object 
//		id = id of event on database 
//		info = associative array containing the updated data using following keys:
//				title, description, dateStart, dateEnd, RSVPslotLim
// Output: true if successful, false if update failed 
//--------------------------------------------------------------------------------------------------
// Function: userUpdate(conn, id, info[])
// Description: 
// Input: 
//		conn = MySQL database connection object 
//      id = id of user on database 
//      info = associative array containing the updated data using following keys:
//				firstName, lastName, email
// Output: true if successful, false if update failed 
//--------------------------------------------------------------------------------------------------
// Function: reservationUpdate(conn, inviteID, slotID)
// Description: update a reservation to a different slot 
// Input: 
//		conn = MySQL database connection object 
//      inviteID = id of invite on database 
//		slotID = id of slot on database 
// Output: true if successful, false if update failed 
//-------------------------------------------------------------------------------------------------
// Function: postUpdate(conn, id, msg, fileName)
// Description: update the message and file name associated with a post 
// Input: 
//		conn = MySQL database connection object 
//      id = id of post on database 
//      msg = text of post
//		fileName = string containing new file name 
// Output: true if successful, false if update failed 
//--------------------------------------------------------------------------------------------------
// Function: inviteStatusUpdate(conn, id, status)
// Description: update the status of an invite
// Input: 
//		conn = MySQL database connection object 
//      id = id of invite on database 
//      status = string of either 'accepted','declined','no response'
// Output: true if successful, false if update failed 
//--------------------------------------------------------------------------------------------------
// Function: eventDelete(conn, id)
// Description: delete event from database 
// Input: 
//		conn = MySQL database connection object 
//      id = id of event on database 
// Output: true if successful, false if delete failed 
//--------------------------------------------------------------------------------------------------
// Function: slotDelete(conn, id)
// Description: delete slot from database 
// Input: 
//		conn = MySQL database connection object 
//      id = id of slot on database 
// Output: true if successful, false if delete failed 
//--------------------------------------------------------------------------------------------------
// Function: reservationDelete(conn, inviteID, slotID)
// Description: delete a reservation for a slot 
// Input: 
//		conn = MySQL database connection object 
//      inviteID = id of invite on database
//		slotID = id of slot on database 
// Output: true if successful, false if delete failed 
//--------------------------------------------------------------------------------------------------
// Function: postDelete(conn, id)
// Description: delete a post from the database 
// Input: 
//		conn = MySQL database connection object 
//      id = id of post on database 
// Output: true if successful, false if delete failed 
//--------------------------------------------------------------------------------------------------
// Function: inviteDelete(conn, id)
// Description: delete an invite from the database, this will remove a user from 
//              the event and any slot reservations 
// Input: 
//		conn = MySQL database connection object 
//      id = id of invite on database 
// Output: true if successful, false if delete failed 
//--------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------
//  FUNCTION CODE
//--------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------
// Function: lookupUser(conn, onidID)
// Description: look up a users info based on OSU user name 
// Input: 
//		conn = MySQL database connection object 
//		onidID = string containing an ONID user name (example: 'smithj')
// Output:  if user is found, then a 1D associative array containing their info, else NULL 
// 		array keys: id, onidID, firstName, lastName, email
function lookupUser($conn, $onidID){
	$stmt = $conn->prepare("SELECT * FROM scheduleit_User WHERE onidID=?");
	$stmt->bind_param("s", $onidID);
	$stmt->execute();
	
	$result = $stmt->get_result();
	
	if ($result->num_rows == 1){
		$data = mysqli_fetch_all($result, MYSQLI_ASSOC);
		//echo json_encode($data);
		// fetch_all returns multi dimension array, but only need 1st row here 
		return json_encode($data[0]);
	}
	else{
		return NULL;
	}
}

//--------------------------------------------------------------------------------------------------
// Function: newUser(conn, info[])
// Description: create a new user in the database
// Input: 
//		conn = MySQL database connection object 
//		info = associative array containing the data for creating a new user using following keys:
//			onidID (OSU user name), firstName, lastName, email
// Output: database id of new user if successful, else false 
function newUser($conn, $info){
	$stmt = $conn->prepare("INSERT INTO scheduleit_User (onidID, firstName, lastName, email) VALUES (?, ?, ?, ?)");
	$stmt->bind_param("ssss", $info['onidID'], $info['firstName'], $info['lastName'], $info['email']);
	if ($stmt->execute()){
		// execute() returns true on success, false on failure
		return $conn->insert_id;
	}
	else{
		return false;
	}
}
//--------------------------------------------------------------------------------------------------
// Function: newEvent(conn, info[])
// Description: create a new event in the database
// Input: 
//		conn = MySQL database connection object 
//		info = associative array containing the data for creating a new event using following keys:
//			title, description, dateStart, dateEnd, RSVPslotLim, creatorID
// Output: database id of new event if successful, else false
function newEvent($conn, $info){
	$stmt = $conn->prepare("INSERT INTO scheduleit_Event (title, description, location, dateStart, dateEnd, RSVPslotLim, creatorID) VALUES (?, ?, ?, ?, ?, ?, ?)");
	$stmt->bind_param("sssssii", $info['title'], $info['description'], $info['location'], $info['dateStart'], $info['dateEnd'], $info['RSVPslotLim'], $info['creatorID']);
	if ($stmt->execute()){
		// execute() returns true on success, false on failure
		return $conn->insert_id;
	}
	else{
		return false;
	}
}
//--------------------------------------------------------------------------------------------------
// Function: newSlot(conn, info[])
// Description: create a new time slot for an event in the database
// Input: 
//		conn = MySQL database connection object 
//		info = associative array containing the data for creating a new time slot using following keys:
//			startDateTime, endDateTime, location, RSVPlim, eventID
// Output:  detail of the slot just created
function newSlot($conn, $eventID){
	$time = " 00:00:00";
	$stmt = $conn->prepare("SELECT * FROM scheduleit_Event WHERE id = ?");
	$stmt->bind_param("i", $eventID);
	if ($stmt->execute()){
		$result = $stmt->get_result();
		$data = $result->fetch_all(MYSQLI_ASSOC);
		$date = $data[0]["dateStart"];
		$location = $data[0]["location"];
		$dateTime = $date . $time;
		$stmt = $conn->prepare("INSERT INTO scheduleit_Slot (startDateTime, endDateTime, location, eventID)
		VALUES (?, ?, ?, ?)");
		$stmt->bind_param("sssi", $dateTime, $dateTime, $location, $eventID);
		if ($stmt->execute()) {
			$created["id"] = $conn->insert_id;
			$created["startDateTime"] = $dateTime;
			$created["endDateTime"] = $dateTime;
			$created["location"] = $location;
			$created["RSVPlim"] = 1;
			return $created;
		} else {
			return false;
		}
	} else {
		return false;
	}
}
//--------------------------------------------------------------------------------------------------
// Function: lookupInvite(conn, receiverID, eventID)
// Description: lookup invite from receiverID and eventID
// Input: 
//		conn = MySQL database connection object 
//		recevierID = receiver ID from db
//		eventID = event ID from db
// Output: database id of invite if found, else false
function lookupInvite($conn, $receiverID, $eventID){
	$stmt = $conn->prepare("SELECT * FROM scheduleit_Invite
			WHERE receiverID = ? AND eventID = ?");
	$stmt->bind_param("ii", $receiverID, $eventID);
	$stmt->execute();
	
	$result = $stmt->get_result();
	
	if ($result->num_rows == 1){
		$data = mysqli_fetch_all($result, MYSQLI_ASSOC);
		// fetch_all returns multi dimension array, but only need 1st row here 
		return json_encode($data[0]);
	}
	else{
		return false;
	}
}

//--------------------------------------------------------------------------------------------------
// Function: newInvite(conn, info[])
// Description: create a new invite for an event in the database
// Input: 
//		conn = MySQL database connection object 
//		info = associative array containing the data for creating a new invite using following keys:
//			receiverID, eventID, email
// Output: database id of new invite if successful, else false
function newInvite($conn, $receiverID, $eventID){
	$stmt = $conn->prepare("INSERT INTO scheduleit_Invite (receiverID, eventID)
			VALUES (?, ?)");
	$stmt->bind_param("ii", $receiverID, $eventID);
	if ($stmt->execute()){
		// execute() returns true on success, false on failure
		return $conn->insert_id;
	}
	else{
		return false;
	}
}
//--------------------------------------------------------------------------------------------------
// Function: newPost(conn, info[])
// Description: create a new post to a slot in the database
// Input: 
//		conn = MySQL database connection object 
//		info = associative array containing the data for creating a new post using following keys:
//			senderID, text, fileName, slotID
// Output: database id of new post if successful, else false
function newPost($conn, $info){
	$stmt = $conn->prepare("INSERT INTO scheduleit_Post (senderID, text, fileName, slotID)
			VALUES (?, ?, ?, ?)");
	$stmt->bind_param("issi", $info['senderID'], $info['text'], $info['fileName'], $info['slotID']);
	if ($stmt->execute()){
		// execute() returns true on success, false on failure
		return $conn->insert_id;
	}
	else{
		return false;
	}
}
//--------------------------------------------------------------------------------------------------
// Function: newReservation(conn, info[])
// Description: create a new reservation for a slot in the database
// Input: 
//		conn = MySQL database connection object 
//		info = associative array containing the data for creating a new reservation using following keys:
//			inviteID, slotID
// Output: true if successful, else false 
function newReservation($conn, $info){
	// this is done as a transaction to ensure RSVPlim of slot is not exceeded during process of checking
	// the limit and making the reservation 
	$success = null;  // track if all queries to make reservation were successful 
	$conn->autocommit(FALSE);  // turn off auto commit
	// get slot RSVPlim 
	$slotInfo = slotDetails($conn, $info['slotID']);
	$RSVPs = slotRSVPCount($conn, $info['slotID']);
	
	// check attendee limits
	if ($slotInfo['RSVPlim'] > $RSVPs){
		// there's room!  make the reservation 
		$stmt = $conn->prepare("INSERT INTO scheduleit_Reservation (inviteID, slotID) VALUES (?, ?)");
		$stmt->bind_param("ii", $info['inviteID'], $info['slotID']);
		$success = $stmt->execute();  // reservations do not have an auto increment id 

		// check that slot RSVPlim hasn't been exceeded while executing query to make reservation
		$checkRSVPs = slotRSVPCount($conn, $slotID);
		if ($slotInfo['RSVPlim'] >= $checkRSVPs){
			// everything is good commit the change to database
			$success = $conn->commit();
		}
		else{
			// Someone beat us to it!!!!!!!  Slot RSVPlim reached while processing reservation
			$conn->rollback();
			$success = false;
		}	
	}
	else{
		// no room available
		$success = false;
	}
	$conn->autocommit(TRUE);  // turn on auto commit just for safety 
	return $success;	
}
//--------------------------------------------------------------------------------------------------
// Function: eventCreateHist(conn, id)
// Description: find all events created by a user in ascending order by event date 
// Input: 
//		conn = MySQL database connection object 
//		id = id of user on database 
// Output: if any are found, then a 2D associative array containing event info with
//         the first dimension being row number of result, else NULL.
//		2nd dim array keys: *
//		It returns the endDate +1 day for the fullCalendar display.
function eventCreateHist($conn, $id){
	$stmt = $conn->prepare("SELECT * FROM scheduleit_Event 
			WHERE creatorID=?
			ORDER BY dateStart ASC");
	$stmt->bind_param("i", $id);
	if ($stmt->execute()){
		$result = $stmt->get_result();
		$data = $result->fetch_all(MYSQLI_ASSOC);
		$returnData = array();
		// for fullCalendar display of dateEnd
		foreach ($data as $event) {
			$event["dateEnd"] = date('Y-m-d', strtotime('+1 day', strtotime($event["dateEnd"])));
			array_push($returnData, $event);
		}
		return $returnData;
	}
	else{
		return NULL;
	}
}
//--------------------------------------------------------------------------------------------------
// Function: inviteHist(conn, id)
// Description: find all events a user has been invited to in ascending order by event date 
// Input: 
//		conn = MySQL database connection object 
//		id = id of user on database 
// Output: if any are found, then a 2D associative array containing event info with
//         the first dimension being row number of result, else NULL.
//		2nd dim array keys:  eventID, inviteID, title, description, dateStart, dateEnd firstName (of creator), lastName (of creator), status
function inviteHist($conn, $id){
	$stmt = $conn->prepare("SELECT I.eventID, I.id AS inviteID, E.title, E.description, E.location, E.dateStart, E.dateEnd, U.firstName, U.lastName, I.status FROM scheduleit_Event E 
			INNER JOIN scheduleit_Invite I ON E.id = I.eventID 
			INNER JOIN scheduleit_User U ON E.creatorID = U.id 
			WHERE I.receiverID = ?
			ORDER BY E.dateStart ASC");
	$stmt->bind_param("i", $id);
	if ($stmt->execute()){
		$result = $stmt->get_result();
		return $result->fetch_all(MYSQLI_ASSOC);
	}
	else{
		return NULL;
	}
}
//--------------------------------------------------------------------------------------------------
// Function: reservedSlotHist(conn, id)
// Description: find all slots with associated events a user has reserved
//				in ascending order by event date 
// Input: 
//		conn = MySQL database connection object 
//		id = id of user on database 
// Output: if any are found, then a 2D associative array containing slot info with
//         the first dimension being row number of result, else NULL.
//		2nd dim array keys: eventID, inviteID, slotID, title, dateStart, dateEnd, startDateTime, location, endDateTime
function reservedSlotHist($conn, $id){
	$stmt = $conn->prepare("SELECT I.eventID, R.inviteID, R.slotID, E.title, E.dateStart, E.dateEnd, S.startDateTime, S.endDateTime, S.location FROM scheduleit_Slot S 
			INNER JOIN scheduleit_Reservation R ON S.id = R.slotID 
			INNER JOIN scheduleit_Invite I ON R.inviteID = I.id
			INNER JOIN scheduleit_User U ON I.receiverID = U.id 
			INNER JOIN scheduleit_Event E ON I.eventID = E.id 
			WHERE U.id = ?
			ORDER BY E.dateStart ASC");
	$stmt->bind_param("i", $id);
	if ($stmt->execute()){
		$result = $stmt->get_result();
		return $result->fetch_all(MYSQLI_ASSOC);
	}
	else{
		return NULL;
	}
}
//--------------------------------------------------------------------------------------------------
// Function: usersAccepted(conn, $id)
// Description: all users who have made a reservation for an event in ascending order by last name 
// Input: 
//		conn = MySQL database connection object 
//		id = id of event on database 
// Output: if any are found, then a 2D associative array containing user info with
//         the first dimension being row number of result, else NULL.
//		2nd dim array keys: firstName, lastName, onidID, email
function usersAccepted($conn, $id){
	$stmt = $conn->prepare("SELECT U.firstName, U.lastName, U.onidID, U.email FROM scheduleit_Reservation R
			INNER JOIN scheduleit_Invite I ON R.inviteID = I.id 
			INNER JOIN scheduleit_User U ON I.receiverID = U.id 
			WHERE I.eventID = ?
			ORDER BY U.lastName ASC");
	$stmt->bind_param("i", $id);
	if ($stmt->execute()){
		$result = $stmt->get_result();
		return $result->fetch_all(MYSQLI_ASSOC);
	}
	else{
		return NULL;
	}
}
//--------------------------------------------------------------------------------------------------
// Function: usersDeclined(conn, $id)
// Description: all users who have declined an event invite in ascending order by last name 
// Input: 
//		conn = MySQL database connection object 
//		id = id of event on database 
// Output: if any are found, then a 2D associative array containing user info with
//         the first dimension being row number of result, else NULL.
//		2nd dim array keys: firstName, lastName, onidID, email
function usersDeclined($conn, $id){
	$stmt = $conn->prepare("SELECT U.firstName, U.lastName, U.onidID, U.email FROM scheduleit_Invite I
			INNER JOIN scheduleit_User U ON I.receiverID = U.id 
			WHERE I.status = 'declined' AND I.eventID = ?
			ORDER BY U.lastName ASC");
	$stmt->bind_param("i", $id);
	if ($stmt->execute()){
		$result = $stmt->get_result();
		return $result->fetch_all(MYSQLI_ASSOC);
	}
	else{
		return NULL;
	}
}
//--------------------------------------------------------------------------------------------------
// Function: usersNoResponse(conn, $id)
// Description: all users who have not responded to an event invite in ascending order by last name 
// Input: 
//		conn = MySQL database connection object 
//		id = id of event on database 
// Output: if any are found, then a 2D associative array containing user info with
//         the first dimension being row number of result, else NULL.
//		2nd dim array keys: firstName, lastName, onidID, email
function usersNoResponse($conn, $id){
	$stmt = $conn->prepare("SELECT U.firstName, U.lastName, U.onidID, U.email FROM scheduleit_Invite I
			INNER JOIN scheduleit_User U ON I.receiverID = U.id 
			WHERE I.status = 'no response' AND I.eventID = ?
			ORDER BY U.lastName ASC");
	$stmt->bind_param("i", $id);
	if ($stmt->execute()){
		$result = $stmt->get_result();
		return $result->fetch_all(MYSQLI_ASSOC);
	}
	else{
		return NULL;
	}
}
//--------------------------------------------------------------------------------------------------
// Function: invitesUpcoming(conn, id)
// Description: future events that user hasn't responded to in ascending order by event date 
// Input: 
//		conn = MySQL database connection object 
//		id = id of user on database
// Output: if any are found, then a 2D associative array containing event info with
//         the first dimension being row number of result, else NULL.
//		2nd dim array keys: eventID, inviteID, title, dateStart, dateEnd, firstName, lastName
function invitesUpcoming($conn, $id){
	$stmt = $conn->prepare("SELECT I.eventID, I.id AS inviteID, E.title, E.dateStart, E.dateEnd, U.firstName, U.lastName FROM scheduleit_Invite I 
			INNER JOIN scheduleit_Event E ON I.eventID = E.id 
			INNER JOIN scheduleit_User U ON E.creatorID = U.id 
			WHERE I.status = 'no response' AND E.dateStart >= NOW() AND I.receiverID = ?
			ORDER BY E.dateStart ASC");
	$stmt->bind_param("i", $id);
	if ($stmt->execute()){
		$result = $stmt->get_result();
		return $result->fetch_all(MYSQLI_ASSOC);
	}
	else{
		return NULL;
	}
}
//--------------------------------------------------------------------------------------------------
// Function: eventsUpcoming(conn, id)
// Description: future events a user created in ascending order by event date 
// Input: 
//		conn = MySQL database connection object 
//		id = id of user on database
// Output: if any are found, then a 2D associative array containing event info with
//         the first dimension being row number of result, else NULL.
//		2nd dim array keys: id, title, dateStart, dateEnd
function eventsUpcoming($conn, $id){
	$stmt = $conn->prepare("SELECT id, title, dateStart FROM scheduleit_Event 
			WHERE dateStart >= NOW() AND creatorID = ?
			ORDER BY dateStart ASC");
	$stmt->bind_param("i", $id);
	if ($stmt->execute()){
		$result = $stmt->get_result();
		return $result->fetch_all(MYSQLI_ASSOC);
	}
	else{
		return NULL;
	}
}
//--------------------------------------------------------------------------------------------------
// Function: reservationsUpcoming(conn, id)
// Description: future slots a user has reserved in ascending order by event date and slot start time 
// Input: 
//		conn = MySQL database connection object 
//		id = id of user on database
// Output: if any are found, then a 2D associative array containing event and slot info with
//         the first dimension being row number of result, else NULL.
//		2nd dim array keys: eventID, inviteID, slotID, title, dateStart, dateEnd, startDateTime, endDateTime, location
function reservationsUpcoming($conn, $id){
	$stmt = $conn->prepare("SELECT I.eventID, R.inviteID, R.slotID, E.title, E.dateStart, E.dateEnd, S.startDateTime, S.endDateTime, S.location FROM scheduleit_Slot S 
			INNER JOIN scheduleit_Reservation R ON S.id = R.slotID 
			INNER JOIN scheduleit_Invite I ON R.inviteID = I.id
			INNER JOIN scheduleit_User U ON I.receiverID = U.id 
			INNER JOIN scheduleit_Event E ON I.eventID = E.id 
			WHERE E.dateStart >= NOW() AND U.id = ?
			ORDER BY E.dateStart ASC, S.startDateTime ASC");
	$stmt->bind_param("i",$id);
	if ($stmt->execute()){
		$result = $stmt->get_result();
		return $result->fetch_all(MYSQLI_ASSOC);
	}
	else{
		return NULL;
	}
}
//--------------------------------------------------------------------------------------------------
// Function: eventEndTime(conn, $id)
// Description: determine an event's ending time based on end of last associated slot 
// Input: 
//		conn = MySQL database connection object 
//		id = id of event on database
// Output: if found a string representing the ending time of the event in format HH:MM:00, else NULL
function eventEndTime($conn, $id){
	$stmt = $conn->prepare("SELECT MAX(endDateTime) AS endDateTime FROM scheduleit_Slot
			WHERE eventID = ?;");
	$stmt->bind_param("i", $id);
	if ($stmt->execute()){
		$result = $stmt->get_result();
		$data = $result->fetch_all(MYSQLI_ASSOC); 
		return $data[0]['endDateTime'];
	}
	else{
		return NULL;
	}
}
//--------------------------------------------------------------------------------------------------
// Function: adminCheck(conn, onidID)
// Description: check if a user is approved for admin access 
// Input: 
//		conn = MySQL database connection object 
//		onidID = string containing an ONID user name (example: 'smithj')
// Output: true if approved, else false 
function adminCheck($conn, $onidID){
	$stmt = $conn->prepare("SELECT * FROM scheduleit_AdminList WHERE onidID = ?;");
	$stmt->bind_param("s", $onidID);
	if ($stmt->execute()){
		$result = $stmt->get_result();
		$data = $result->fetch_all(MYSQLI_ASSOC);
		if ($onidID == $data[0]['onidID']){
			return true;
		}
		else{
			return false;
		}
	}
	else{
		return false;
	}
}
//--------------------------------------------------------------------------------------------------
// Function: inviteDetails(conn, id)
// Description: database entry for a specific invite 
// Input: 
//		conn = MySQL database connection object 
//		id = id of invite on database
// Output: if invite is found, then a 1D associative array containing the info, else NULL 
// 		array keys: id, email, status, receiverID, eventID
function inviteDetails($conn, $id){
	$stmt = $conn->prepare("SELECT * FROM scheduleit_Invite WHERE id = ?");
	$stmt->bind_param("i", $id);
	if ($stmt->execute()){
		$result = $stmt->get_result();
		$data = $result->fetch_all(MYSQLI_ASSOC);
		return $data[0];
	}
	else{
		return NULL;
	}
}
//--------------------------------------------------------------------------------------------------
// Function: eventDetails(conn, id)
// Description: database entry for a specific event 
// Input: 
//		conn = MySQL database connection object 
//		id = id of event on database
// Output: if event is found, then a 1D associative array containing the info, else NULL 
// 		array keys: id, title, description, dateStart, dateEnd, RSVPslotLim, creatorID
function eventDetails($conn, $id){
	$stmt = $conn->prepare("SELECT * FROM scheduleit_Event WHERE id = ?");
	$stmt->bind_param("i", $id);
	if ($stmt->execute()){
		$result = $stmt->get_result();
		$data = $result->fetch_all(MYSQLI_ASSOC);
		return $data[0];
	}
	else{
		return NULL;
	}
}
//--------------------------------------------------------------------------------------------------
// Function: eventSlots(conn, id)
// Description: all slots for a specific event in ascending order by startDateTime
// Input: 
//		conn = MySQL database connection object 
//		id = id of event on database
// Output: if any are found, then a 2D associative array containing slot info with
//         the first dimension being row number of result, else NULL.
//		2nd dim array keys: id, startDateTime, location, RSVPlim, eventID, endDateTime
function eventSlots($conn, $id){
	$stmt = $conn->prepare("SELECT * FROM scheduleit_Slot 
			WHERE eventID = ? ORDER BY startDateTime ASC");
	$stmt->bind_param("i", $id);
	if ($stmt->execute()){
		$result = $stmt->get_result();
		return $result->fetch_all(MYSQLI_ASSOC);
	}
	else{
		return NULL;
	}
}
//--------------------------------------------------------------------------------------------------
// Function: eventAvailableSlots(conn, id)
// Description: all available slots for a specific event in ascending order by startDateTime
// Input: 
//		conn = MySQL database connection object 
//		id = id of event on database
// Output: if any are found, then a 2D associative array containing slot info with
//         the first dimension being row number of result, else NULL.
//		2nd dim array keys: id, startDateTime, endDateTime, location, eventID, RSVPlim, and RSVPs 
function eventAvailableSlots($conn, $eventID, $userID){
	$sql = "SELECT S.id, S.startDateTime, S.endDateTime, S.location, S.RSVPlim, C.count AS RSVPs
	FROM (
	SELECT scheduleit_Slot.id, scheduleit_Slot.eventID, startDateTime, endDateTime, scheduleit_Slot.location, scheduleit_Slot.RSVPlim AS RSVPlim
	FROM scheduleit_Slot
	INNER JOIN scheduleit_Event ON scheduleit_Slot.eventID = scheduleit_Event.id
	WHERE scheduleit_Slot.eventID = ?
	) AS S
	LEFT JOIN (
	SELECT slotID, COUNT( * ) AS count
	FROM scheduleit_Reservation
	GROUP BY slotID
	) AS C ON C.slotID = S.id
	INNER JOIN scheduleit_Invite I ON S.eventID = I.eventID AND I.receiverID = ?
	LEFT JOIN scheduleit_Reservation R ON S.id = R.slotID AND I.ID = R.inviteID
	WHERE (S.RSVPlim > C.count OR C.count IS NULL) AND R.inviteID IS NULL
	ORDER by S.startDateTime ASC;
	";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("ii", $eventID, $userID);
	if ($stmt->execute()){
		$result = $stmt->get_result();
		return $result->fetch_all(MYSQLI_ASSOC);
	}
	else{
		return NULL;
	}
}
//--------------------------------------------------------------------------------------------------
// Function: eventFromInviteID(conn, inviteID)
// Description: return the eventID given the invite ID
// Input: 
//		conn = MySQL database connection object 
//		inviteID = id of invite on database
// Output: If any is found, then an 1d associative array is returned. The keys are id, title, description, dateStart, dateEnd, RSVPslotLim, and creatorID
//         Otherwise, null is returned.
function eventFromInviteID($conn, $inviteID) {
	$sql = "SELECT * FROM scheduleit_Event 
			INNER JOIN scheduleit_Invite ON scheduleit_Invite.eventID = scheduleit_Event.id
			WHERE scheduleit_Invite.id = ?";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("i", $inviteID);
	if ($stmt->execute()){
		$result = $stmt->get_result();
		$data = $result->fetch_all(MYSQLI_ASSOC);
		return $data[0];
	}
	else{
		return NULL;
	}
}

//--------------------------------------------------------------------------------------------------
// Function: slotDetails(conn, id)
// Description: 
// Input: 
//		conn = MySQL database connection object 
//		id = id of slot on database 
// Output: if slot is found, then a 1D associative array containing the info, else NULL 
// 		array keys: id, startDateTime, endDateTime, location, RSVPlim, eventID, endDateTime
function slotDetails($conn, $id){
	$stmt = $conn->prepare("SELECT * FROM scheduleit_Slot WHERE id = ? ORDER BY endDateTime DESC");
	$stmt->bind_param("i", $id);
	if ($stmt->execute()){
		$result = $stmt->get_result();
		$data = $result->fetch_all(MYSQLI_ASSOC);
		return $data[0];
	}
	else{
		return NULL;
	}
}

//--------------------------------------------------------------------------------------------------
// Function: slotPosts(conn, id)
// Description: all posts associated with a slot in ascending order by post timeStamp
// Input: 
//		conn = MySQL database connection object 
//		id = id of slot on database 
// Output: if any are found, then a 2D associative array containing slot info with
//         the first dimension being row number of result, else NULL.
//		2nd dim array keys: id, text, fileName, timeStamp, userID, firstName, lastName
function slotPosts($conn, $id){
	$stmt = $conn->prepare("SELECT P.id, P.text, P.fileName, P.timeStamp, U.id AS userID, U.firstName, U.lastName, U.onidID, P.slotID FROM scheduleit_Slot S
			INNER JOIN scheduleit_Post P ON S.id = P.slotID 
			INNER JOIN scheduleit_User U ON P.senderID = U.id 
			WHERE S.id = ?
			ORDER BY P.timeStamp ASC");
	$stmt->bind_param("i", $id);
	if ($stmt->execute()){
		$result = $stmt->get_result();
		return $result->fetch_all(MYSQLI_ASSOC);
	}
	else{
		return NULL;
	}
}

//--------------------------------------------------------------------------------------------------
// Function: userSlotPost(conn, slotID, userID)
// Description: Return post created by userID for slotID
// Input: 
//		conn = MySQL database connection object 
//		slotID = id of slot on database 
//		userID = id of user / senderID
// Output: if any are found, then a 1D associative array containing slot info with id, text, fileName, timeStamp, userID, firstName, lastName
function userSlotPost($conn, $slotID, $userID){
	$stmt = $conn->prepare("SELECT * FROM `scheduleit_Post` WHERE `senderID` = ? AND `slotID` = ?");
	$stmt->bind_param("ii",$userID, $slotID);
	if ($stmt->execute()){
		$result = $stmt->get_result();
		$data = $result->fetch_all(MYSQLI_ASSOC);
		return $data[0];
	}
	else{
		return NULL;
	}
}

//--------------------------------------------------------------------------------------------------
// Function: postOwner(conn, postID)
// Description: Return post owner (senderID) of the post
// Input: 
//		conn = MySQL database connection object 
//		postID = id of post on database
// Output: if any are found, then a 1D associative array containing sender ID (post owner)
function postOwner($conn, $postID){
	$stmt = $conn->prepare("SELECT senderID FROM `scheduleit_Post` WHERE `id` = ?");
	$stmt->bind_param("i",$postID);
	if ($stmt->execute()){
		$result = $stmt->get_result();
		$data = $result->fetch_all(MYSQLI_ASSOC);
		return $data[0];
	}
	else{
		return NULL;
	}
}
//--------------------------------------------------------------------------------------------------
// Function: getReservationReceiverID(conn, id)
// Description: return the creator of the reservation (invitation receiver)
// Input: 
//		conn = MySQL database connection object 
//		inviteID = id of invite on database
//		slotID = id of slot on database 
// Output: if any are found, then a 1D associative array containing receiverID (reservation owner)
function getReservationReceiverID($conn, $inviteID, $slotID){
	$stmt = $conn->prepare("SELECT I.receiverID FROM scheduleit_Reservation R
			INNER JOIN scheduleit_Invite I ON R.inviteID = I.id
			WHERE R.inviteID = ? AND R.slotID = ?");
	$stmt->bind_param("ii", $inviteID, $slotID);
	if ($stmt->execute()){
		$result = $stmt->get_result();
		$data = $result->fetch_all(MYSQLI_ASSOC);
		return $data[0];
	}
	else{
		return NULL;
	}
}
//--------------------------------------------------------------------------------------------------
// Function: userEventRSVPCount(conn, userID, eventID)
// Description: How many reservations for an event a user has made 
// Input: 
//		conn = MySQL database connection object 
//		userID = id of user on database 
//		eventID = id of event on database 
// Output: user's event reservation count 
function userEventRSVPCount($conn, $userID, $eventID){
	$stmt = $conn->prepare("SELECT COUNT(*) AS userRSVPcount FROM scheduleit_User U 
			INNER JOIN scheduleit_Invite I ON U.id = I.receiverID 
			INNER JOIN scheduleit_Reservation R on I.id = R.inviteID 
			WHERE U.id = ? AND I.eventID = ?");
	$stmt->bind_param("ii",$userID, $eventID);
	if ($stmt->execute()){
		$result = $stmt->get_result();
		$data = $result->fetch_all(MYSQLI_ASSOC);
		return $data[0]['userRSVPcount'];
	}
	else{
		return NULL;
	}
}
//--------------------------------------------------------------------------------------------------
// Function: slotRSVPCount(conn, id)
// Description: how many users have reserved a specific slot 
// Input: 
//		conn = MySQL database connection object 
//		id = id of slot on database 
// Output: slot reservation count 
function slotRSVPCount($conn, $id){
	$stmt = $conn->prepare("SELECT COUNT(*) AS slotRSVPcount FROM scheduleit_Reservation R 
			INNER JOIN scheduleit_Slot S ON R.slotID = S.id 
			WHERE S.id = ?");
	$stmt->bind_param("i", $id);
	if ($stmt->execute()){
		$result = $stmt->get_result();
		$data = $result->fetch_all(MYSQLI_ASSOC);
		return $data[0]['slotRSVPcount'];
	}
	else{
		return NULL;
	}
}
//--------------------------------------------------------------------------------------------------
// Function: slotAttendees(conn, id)
// Description: users who have made a reservation for a slot in ascending order by lastName
// Input: 
//		conn = MySQL database connection object 
//		id = id of slot on database 
// Output: if any are found, then a 2D associative array containing attendees' names with
//         the first dimension being row number of result, else NULL.
//		2nd dim array keys: lastName, firstName
function slotAttendees($conn, $id){
	$stmt = $conn->prepare("SELECT U.lastName, U.firstName FROM scheduleit_Slot S 
			INNER JOIN scheduleit_Reservation R ON S.id = R.slotID 
			INNER JOIN scheduleit_Invite I ON R.inviteID = I.id 
			INNER JOIN scheduleit_User U ON I.receiverID = U.id 
			WHERE S.id = ?
			ORDER BY U.lastName ASC");
	$stmt->bind_param("i", $id);
	if ($stmt->execute()){
		$result = $stmt->get_result();
		return $result->fetch_all(MYSQLI_ASSOC);
	}
	else{
		return NULL;
	}
}
//--------------------------------------------------------------------------------------------------
// Function: getEventEmails(conn, id)
// Description: emails of the users invited in the event
// Input: 
//		conn = MySQL database connection object 
//		id = id of event in db 
// Output: if any are found, then a 2D associative array containing attendees' emails
function getEventEmails($conn, $id){
	$stmt = $conn->prepare("SELECT I.id, U.email, E.title FROM scheduleit_Event E
			INNER JOIN scheduleit_Invite I ON E.id = I.eventID
			INNER JOIN scheduleit_User U ON I.receiverID = U.id
			WHERE E.id = ?");
	$stmt->bind_param("i", $id);
	if ($stmt->execute()){
		$result = $stmt->get_result();
		return ($result->fetch_all(MYSQLI_ASSOC));
	}
	else{
		return NULL;
	}
}
//--------------------------------------------------------------------------------------------------
// Function: getEventReservationStatus(conn, id)
// Description: all users who have been invited to an event with their reservation status for each slot
// Input: 
//		conn = MySQL database connection object 
//		id = id of event in db 
// Output: if any are found, then a 2D associative array containing invitees
//         1st dimension = row number of result, else NULL
//         2nd dimension = array keys: lastName, firstName, email, startDateTime, status
function getEventReservationStatus($conn, $id){
    $stmt = $conn->prepare("SELECT U.lastName, U.firstName, U.email, S.startDateTime, I.status
    FROM scheduleit_Invite I 
    INNER JOIN scheduleit_User U ON I.receiverID = U.id
    INNER JOIN scheduleit_Slot S ON I.eventID = S.eventID
    WHERE I.eventID = ?
    ORDER BY U.lastName ASC;");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()){
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    else{
        return NULL;
    }
}



//--------------------------------------------------------------------------------------------------
// Function: eventUpdate(conn, id, info[])
// Description: update an event's details
// Input: 
//		conn = MySQL database connection object 
//		id = id of event on database 
//		info = associative array containing the updated data using following keys:
//				title, description, dateStart, dateEnd, RSVPslotLim
// Output: true if successful, false if update failed 
function eventUpdate($conn, $info){
	$stmt = $conn->prepare("UPDATE scheduleit_Event 
			SET title = ?, description = ?, location = ?, dateStart = ?, dateEnd = ?, RSVPslotLim = ?
			WHERE id = ?");
	$stmt->bind_param("sssssii", $info['title'], $info['description'], $info['location'], $info['dateStart'], $info['dateEnd'], $info['RSVPslotLim'], $info['id']);
	return $stmt->execute();
}
//--------------------------------------------------------------------------------------------------
// Function: slotUpdate(conn, id, key, value)
// Description: update a slot's details
// Input: 
//		conn = MySQL database connection object 
//		id = id of slot on database 
// Output: true if successful, false if update failed 
function slotUpdate($conn, $info){
	if ($info["key"] == "startDateTime") {
		$stmt = $conn->prepare("UPDATE scheduleit_Slot SET startDateTime = ? WHERE id = ?");
	} else if ($info["key"] == "endDateTime") {
		$stmt = $conn->prepare("UPDATE scheduleit_Slot SET endDateTime = ? WHERE id = ?");
	} else if ($info["key"] == "location") {
		$stmt = $conn->prepare("UPDATE scheduleit_Slot SET location = ? WHERE id = ?");
	} else if ($info["key"] == "RSVPlim") {
		$stmt = $conn->prepare("UPDATE scheduleit_Slot SET RSVPlim = ? WHERE id = ?");
	}
	$stmt->bind_param("si", $info["value"], $info["id"]);
	return $stmt->execute();
}
//--------------------------------------------------------------------------------------------------
// Function: userUpdate(conn, id, info[])
// Description: 
// Input: 
//		conn = MySQL database connection object 
//      id = id of user on database 
//      info = associative array containing the updated data using following keys:
//				firstName, lastName, email
// Output: true if successful, false if update failed 
function userUpdate($conn, $id, $info){
	$stmt = $conn->prepare("UPDATE scheduleit_User 
			SET firstName = ?, lastName = ?, email = ?
			WHERE id = ?");
	$stmt->bind_param("sssi", $info['firstName'], $info['lastName'], $info['email'], $id);
	return $stmt->execute();
}
//--------------------------------------------------------------------------------------------------
// Function: reservationUpdate(conn, inviteID, slotID)
// Description: update a reservation to a different slot 
// Input: 
//		conn = MySQL database connection object 
//      inviteID = id of invite on database 
//		slotID = id of slot on database 
// Output: true if successful, false if update failed 
function reservationUpdate($conn, $inviteID, $slotID){
	$stmt = $conn->prepare("UPDATE scheduleit_Reservation 
			SET slotID = ?
			WHERE inviteID = ?");
	$stmt->bind_param("ii", $slotID, $inviteID);
	return $stmt->execute();
}
//--------------------------------------------------------------------------------------------------
// Function: postUpdate(conn, id, msg, fileName)
// Description: update the message and file name associated with a post 
// Input: 
//		conn = MySQL database connection object 
//      id = id of post on database 
//      msg = text of post
//		fileName = string containing new file name 
// Output: true if successful, false if update failed 
function postUpdate($conn, $id, $msg, $fileName){
	$stmt = $conn->prepare("UPDATE scheduleit_Post 
			SET text = ?, fileName = ?
			WHERE id = ?");
	$stmt->bind_param("ssi", $msg, $fileName, $id);
	return $stmt->execute();
}

//--------------------------------------------------------------------------------------------------
// Function: inviteStatusUpdate(conn, id, status)
// Description: update the status of an invite
// Input: 
//		conn = MySQL database connection object 
//      id = id of invite on database 
//      status = string of either 'accepted','declined','no response'
// Output: true if successful, false if update failed 
function inviteStatusUpdate($conn, $id, $status){
	$stmt = $conn->prepare("UPDATE scheduleit_Invite 
			SET status = ?
			WHERE id = ?");
	$stmt->bind_param("si", $status, $id);
	if ($status == 'accepted' || $status == 'declined' || $status == 'no response'){
		return $stmt->execute();
	}
	else{
		return false;
	}
}
//--------------------------------------------------------------------------------------------------
// Function: eventDelete(conn, id)
// Description: delete event from database 
// Input: 
//		conn = MySQL database connection object 
//      id = id of event on database 
// Output: true if successful, false if delete failed 
function eventDelete($conn, $id){
	$stmt = $conn->prepare("DELETE FROM scheduleit_Event WHERE id = ?");
	$stmt->bind_param("i", $id);
	return $stmt->execute();
}
//--------------------------------------------------------------------------------------------------
// Function: slotDelete(conn, id)
// Description: delete slot from database 
// Input: 
//		conn = MySQL database connection object 
//      id = id of slot on database 
// Output: true if successful, false if delete failed 
function slotDelete($conn, $id){
	$stmt = $conn->prepare("DELETE FROM scheduleit_Slot WHERE id = ?");
	$stmt->bind_param("i", $id);
	return $stmt->execute();
}
//--------------------------------------------------------------------------------------------------
// Function: reservationDelete(conn, inviteID, slotID)
// Description: delete a reservation for a slot 
// Input: 
//		conn = MySQL database connection object 
//      inviteID = id of invite on database
//		slotID = id of slot on database 
// Output: true if successful, false if delete failed 
function reservationDelete($conn, $inviteID, $slotID){
	$stmt = $conn->prepare("DELETE FROM scheduleit_Reservation WHERE inviteID = ? AND slotID = ?");
	$stmt->bind_param("ii", $inviteID, $slotID);
	return $stmt->execute();
}
//--------------------------------------------------------------------------------------------------
// Function: postDelete(conn, id)
// Description: delete a post from the database 
// Input: 
//		conn = MySQL database connection object 
//      id = id of post on database 
// Output: true if successful, false if delete failed 
function postDelete($conn, $id){
	$stmt = $conn->prepare("DELETE FROM scheduleit_Post WHERE id = ?");
	$stmt->bind_param("i", $id);
	return $stmt->execute();
}
//--------------------------------------------------------------------------------------------------
// Function: inviteDelete(conn, id)
// Description: delete an invite from the database, this will remove a user from 
//              the event and any slot reservations 
// Input: 
//		conn = MySQL database connection object 
//      id = id of invite on database 
// Output: true if successful, false if delete failed 
function inviteDelete($conn, $id){
	$stmt = $conn->prepare("DELETE FROM scheduleit_Invite WHERE id = ?");
	$stmt->bind_param("i", $id);
	return $stmt->execute();
}

?>
