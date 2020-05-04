<?php
//--------------------------------------------------------------------------------------------------
// FUNCTION DOCUMENTATION
//--------------------------------------------------------------------------------------------------
// Function: lookupUser(conn, onidUID)
// Description: look up a users info based on OSU user name 
// Input: 
//		conn = MySQL database connection object 
//		onidUID = string containing an ONID user name (example: 'smithj')
// Output:  if user is found, then a 1D associative array containing their info, else NULL 
// 		array keys: id, onidUID, firstName, lastName, email
//--------------------------------------------------------------------------------------------------
// Function: newUser(conn, info[])
// Description: create a new user in the database
// Input: 
//		conn = MySQL database connection object 
//		info = associative array containing the data for creating a new user using following keys:
//			onidUID (OSU user name), firstName, lastName, email
// Output: database id of new user if successful, else false 
//--------------------------------------------------------------------------------------------------
// Function: newEvent(conn, info[])
// Description: create a new event in the database
// Input: 
//		conn = MySQL database connection object 
//		info = associative array containing the data for creating a new event using following keys:
//			title, description, dateStartTime (YYYY-MM-DD HH:MM), RSVPslotLim, creatorID
// Output: database id of new event if successful, else false
//--------------------------------------------------------------------------------------------------
// Function: newSlot(conn, info[])
// Description: create a new time slot for an event in the database
// Input: 
//		conn = MySQL database connection object 
//		info = associative array containing the data for creating a new time slot using following keys:
//			startTime (HH:MM), duration (HH:MM), location, RSVPlim, eventID
// Output:  database id of new time slot if successful, else false
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
//		2nd dim array keys:  eventID, inviteID, title, description, dateStartTime, firstName (of creator), lastName (of creator), status
//--------------------------------------------------------------------------------------------------
// Function: reservedSlotHist(conn, id)
// Description: find all slots with associated events a user has reserved
//				in ascending order by event date 
// Input: 
//		conn = MySQL database connection object 
//		id = id of user on database 
// Output: if any are found, then a 2D associative array containing slot info with
//         the first dimension being row number of result, else NULL.
//		2nd dim array keys: eventID, inviteID, slotID, title, dateStartTime, startTime, duration, location, endTime
//--------------------------------------------------------------------------------------------------
// Function: usersAccepted(conn, $id)
// Description: all users who have made a reservation for an event in ascending order by last name 
// Input: 
//		conn = MySQL database connection object 
//		id = id of event on database 
// Output: if any are found, then a 2D associative array containing user info with
//         the first dimension being row number of result, else NULL.
//		2nd dim array keys: firstName, lastName, onidUID, email
//--------------------------------------------------------------------------------------------------
// Function: usersDeclined(conn, $id)
// Description: all users who have declined an event invite in ascending order by last name 
// Input: 
//		conn = MySQL database connection object 
//		id = id of event on database 
// Output: if any are found, then a 2D associative array containing user info with
//         the first dimension being row number of result, else NULL.
//		2nd dim array keys: firstName, lastName, onidUID, email
//--------------------------------------------------------------------------------------------------
// Function: usersNoResponse(conn, $id)
// Description: all users who have not responded to an event invite in ascending order by last name 
// Input: 
//		conn = MySQL database connection object 
//		id = id of event on database 
// Output: if any are found, then a 2D associative array containing user info with
//         the first dimension being row number of result, else NULL.
//		2nd dim array keys: firstName, lastName, onidUID, email
//--------------------------------------------------------------------------------------------------
// Function: invitesUpcoming(conn, id)
// Description: future events that user hasn't responded to in ascending order by event date 
// Input: 
//		conn = MySQL database connection object 
//		id = id of user on database
// Output: if any are found, then a 2D associative array containing event info with
//         the first dimension being row number of result, else NULL.
//		2nd dim array keys: eventID, inviteID, title, dateStartTime, firstName, lastName
//--------------------------------------------------------------------------------------------------
// Function: eventsUpcoming(conn, id)
// Description: future events a user created in ascending order by event date 
// Input: 
//		conn = MySQL database connection object 
//		id = id of user on database
// Output: if any are found, then a 2D associative array containing event info with
//         the first dimension being row number of result, else NULL.
//		2nd dim array keys: id, title, dateStartTime
//--------------------------------------------------------------------------------------------------
// Function: reservationsUpcoming(conn, id)
// Description: future slots a user has reserved in ascending order by event date and slot start time 
// Input: 
//		conn = MySQL database connection object 
//		id = id of user on database
// Output: if any are found, then a 2D associative array containing event and slot info with
//         the first dimension being row number of result, else NULL.
//		2nd dim array keys: eventID, inviteID, slotID, title, dateStartTime, startTime, duration, location, endTime
//--------------------------------------------------------------------------------------------------
// Function: eventEndTime(conn, $id)
// Description: determine an event's ending time based on end of last associated slot 
// Input: 
//		conn = MySQL database connection object 
//		id = id of event on database
// Output: if found a string representing the ending time of the event in format HH:MM:00, else NULL
//--------------------------------------------------------------------------------------------------
// Function: adminCheck(conn, onidUID)
// Description: check if a user is approved for admin access 
// Input: 
//		conn = MySQL database connection object 
//		onidUID = string containing an ONID user name (example: 'smithj')
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
// 		array keys: id, title, description, dateStartTime, RSVPslotLim, creatorID
//--------------------------------------------------------------------------------------------------
// Function: eventSlots(conn, id)
// Description: all slots for a specific event in ascending order by startTime
// Input: 
//		conn = MySQL database connection object 
//		id = id of event on database
// Output: if any are found, then a 2D associative array containing slot info with
//         the first dimension being row number of result, else NULL.
//		2nd dim array keys: id, startTime, duration, location, RSVPlim, eventID, endTime
//--------------------------------------------------------------------------------------------------
// Function: eventAvailableSlots(conn, id)
// Description: all available slots for a specific event in ascending order by startTime
// Input: 
//		conn = MySQL database connection object 
//		id = id of event on database
// Output: if any are found, then a 2D associative array containing slot info with
//         the first dimension being row number of result, else NULL.
//		2nd dim array keys: id, startTime, endTime, location, eventID, RSVPlim, and RSVPs 
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
// 		array keys: id, startTime, duration, location, RSVPlim, eventID, endTime
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
//				title, description, dateStartTime (YYYY-MM-DD HH:MM), RSVPslotLim
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
// Function: lookupUser(conn, onidUID)
// Description: look up a users info based on OSU user name 
// Input: 
//		conn = MySQL database connection object 
//		onidUID = string containing an ONID user name (example: 'smithj')
// Output:  if user is found, then a 1D associative array containing their info, else NULL 
// 		array keys: id, onidUID, firstName, lastName, email
function lookupUser($conn, $onidUID){
	$stmt = $conn->prepare("SELECT * FROM User WHERE onidUID=?");
	$stmt->bind_param("s", $onidUID);
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
//			onidUID (OSU user name), firstName, lastName, email
// Output: database id of new user if successful, else false 
function newUser($conn, $info){
	$stmt = $conn->prepare("INSERT INTO User (onidUID, firstName, lastName, email) VALUES (?, ?, ?, ?)");
	$stmt->bind_param("ssss", $info['onidUID'], $info['firstName'], $info['lastName'], $info['email']);
	$stmt->execute();
}
//--------------------------------------------------------------------------------------------------
// Function: newEvent(conn, info[])
// Description: create a new event in the database
// Input: 
//		conn = MySQL database connection object 
//		info = associative array containing the data for creating a new event using following keys:
//			title, description, dateStartTime (YYYY-MM-DD HH:MM), RSVPslotLim, creatorID
// Output: database id of new event if successful, else false
function newEvent($conn, $info){
	$stmt = $conn->prepare("INSERT INTO Event (title, description, dateStartTime, RSVPslotLim, creatorID)
			VALUES (?, ?, ?, ?, ?)");
	$stmt->bind_param("sssii", $info['title'], $info['description'], $info['dateStartTime'], $info['RSVPslotLim'], $info['creatorID']);
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
//			startTime (HH:MM), duration (HH:MM), location, RSVPlim, eventID
// Output:  database id of new time slot if successful, else false
function newSlot($conn, $info){
	$stmt = $conn->prepare("INSERT INTO Slot (startTime, duration, location, RSVPlim, eventID)
			VALUES (?, ?, ?, ?, ?)");
	$stmt->bind_param("sssii", $info['startTime'], $info['duration'], $info['location'], $info['RSVPlim'], $info['eventID']);
	if ($stmt->execute()){
		// execute() returns true on success, false on failure
		return $conn->insert_id;
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
function newInvite($conn, $info){
	$stmt = $conn->prepare("INSERT INTO Invite (receiverID, eventID, email)
			VALUES (?, ?, ?)");
	$stmt->bind_param("iis", $info['receiverID'], $info['eventID'], $info['email']);
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
	$stmt = $conn->prepare("INSERT INTO Post (senderID, text, fileName, slotID)
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
		$stmt = $conn->prepare("INSERT INTO Reservation (inviteID, slotID) VALUES (?, ?)");
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
function eventCreateHist($conn, $id){
	$stmt = $conn->prepare("SELECT * FROM Event 
			WHERE creatorID=?
			ORDER BY dateStartTime ASC");
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
// Function: inviteHist(conn, id)
// Description: find all events a user has been invited to in ascending order by event date 
// Input: 
//		conn = MySQL database connection object 
//		id = id of user on database 
// Output: if any are found, then a 2D associative array containing event info with
//         the first dimension being row number of result, else NULL.
//		2nd dim array keys:  eventID, inviteID, title, description, dateStartTime, firstName (of creator), lastName (of creator), status
function inviteHist($conn, $id){
	$stmt = $conn->prepare("SELECT I.eventID, I.id AS inviteID, E.title, E.description, E.dateStartTime, U.firstName, U.lastName, I.status FROM Event E 
			INNER JOIN Invite I ON E.id = I.eventID 
			INNER JOIN User U ON E.creatorID = U.id 
			WHERE I.receiverID = ?
			ORDER BY E.dateStartTime ASC");
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
//		2nd dim array keys: eventID, inviteID, slotID, title, dateStartTime, startTime, duration, location, endTime
function reservedSlotHist($conn, $id){
	$stmt = $conn->prepare("SELECT I.eventID, R.inviteID, R.slotID, E.title, E.dateStartTime, S.startTime, S.duration, S.location, ADDTIME(S.startTime, S.duration) AS endTime FROM Slot S 
			INNER JOIN Reservation R ON S.id = R.slotID 
			INNER JOIN Invite I ON R.inviteID = I.id
			INNER JOIN User U ON I.receiverID = U.id 
			INNER JOIN Event E ON I.eventID = E.id 
			WHERE U.id = ?
			ORDER BY E.dateStartTime ASC");
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
//		2nd dim array keys: firstName, lastName, onidUID, email
function usersAccepted($conn, $id){
	$stmt = $conn->prepare("SELECT U.firstName, U.lastName, U.onidUID, U.email FROM Reservation R
			INNER JOIN Invite I ON R.inviteID = I.id 
			INNER JOIN User U ON I.receiverID = U.id 
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
//		2nd dim array keys: firstName, lastName, onidUID, email
function usersDeclined($conn, $id){
	$stmt = $conn->prepare("SELECT U.firstName, U.lastName, U.onidUID, U.email FROM Invite I
			INNER JOIN User U ON I.receiverID = U.id 
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
//		2nd dim array keys: firstName, lastName, onidUID, email
function usersNoResponse($conn, $id){
	$stmt = $conn->prepare("SELECT U.firstName, U.lastName, U.onidUID, U.email FROM Invite I
			INNER JOIN User U ON I.receiverID = U.id 
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
//		2nd dim array keys: eventID, inviteID, title, dateStartTime, firstName, lastName
function invitesUpcoming($conn, $id){
	$stmt = $conn->prepare("SELECT I.eventID, I.id AS inviteID, E.title, E.dateStartTime, U.firstName, U.lastName FROM Invite I 
			INNER JOIN Event E ON I.eventID = E.id 
			INNER JOIN User U ON E.creatorID = U.id 
			WHERE I.status = 'no response' AND E.dateStartTime >= NOW() AND I.receiverID = ?
			ORDER BY E.dateStartTime ASC");
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
//		2nd dim array keys: id, title, dateStartTime
function eventsUpcoming($conn, $id){
	$stmt = $conn->prepare("SELECT id, title, dateStartTime FROM Event 
			WHERE dateStartTime >= NOW() AND creatorID = ?
			ORDER BY dateStartTime ASC");
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
//		2nd dim array keys: eventID, inviteID, slotID, title, dateStartTime, startTime, duration, location, endTime
function reservationsUpcoming($conn, $id){
	$stmt = $conn->prepare("SELECT I.eventID, R.inviteID, R.slotID, E.title, E.dateStartTime, S.startTime, S.duration, S.location, ADDTIME(S.startTime, S.duration) AS endTime FROM Slot S 
			INNER JOIN Reservation R ON S.id = R.slotID 
			INNER JOIN Invite I ON R.inviteID = I.id
			INNER JOIN User U ON I.receiverID = U.id 
			INNER JOIN Event E ON I.eventID = E.id 
			WHERE E.dateStartTime >= NOW() AND U.id = ?
			ORDER BY E.dateStartTime ASC, S.startTime ASC");
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
	$stmt = $conn->prepare("SELECT MAX(ADDTIME(startTime,duration)) AS endTime FROM Slot
			WHERE eventID = ?;");
	$stmt->bind_param("i", $id);
	if ($stmt->execute()){
		$result = $stmt->get_result();
		$data = $result->fetch_all(MYSQLI_ASSOC); 
		return $data[0]['endTime'];
	}
	else{
		return NULL;
	}
}
//--------------------------------------------------------------------------------------------------
// Function: adminCheck(conn, onidUID)
// Description: check if a user is approved for admin access 
// Input: 
//		conn = MySQL database connection object 
//		onidUID = string containing an ONID user name (example: 'smithj')
// Output: true if approved, else false 
function adminCheck($conn, $onidUID){
	$stmt = $conn->prepare("SELECT * FROM AdminList WHERE onidUID = ?;");
	$stmt->bind_param("s", $onidUID);
	if ($stmt->execute()){
		$result = $stmt->get_result();
		$data = $result->fetch_all(MYSQLI_ASSOC);
		if ($onidUID == $data[0]['onidUID']){
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
	$stmt = $conn->prepare("SELECT * FROM Invite WHERE id = ?");
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
// 		array keys: id, title, description, dateStartTime, RSVPslotLim, creatorID
function eventDetails($conn, $id){
	$stmt = $conn->prepare("SELECT * FROM Event WHERE id = ?");
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
// Description: all slots for a specific event in ascending order by startTime
// Input: 
//		conn = MySQL database connection object 
//		id = id of event on database
// Output: if any are found, then a 2D associative array containing slot info with
//         the first dimension being row number of result, else NULL.
//		2nd dim array keys: id, startTime, duration, location, RSVPlim, eventID, endTime
function eventSlots($conn, $id){
	$stmt = $conn->prepare("SELECT *, ADDTIME(startTime, duration) AS endTime FROM Slot 
			WHERE eventID = ? ORDER BY startTime ASC");
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
// Description: all available slots for a specific event in ascending order by startTime
// Input: 
//		conn = MySQL database connection object 
//		id = id of event on database
// Output: if any are found, then a 2D associative array containing slot info with
//         the first dimension being row number of result, else NULL.
//		2nd dim array keys: id, startTime, endTime, location, eventID, RSVPlim, and RSVPs 
function eventAvailableSlots($conn, $eventID, $userID){
	$sql = "SELECT S.id, S.startTime, S.endTime, S.location, S.RSVPlim, C.count AS RSVPs
	FROM (
	SELECT Slot.id, Slot.eventID, Slot.startTime, ADDTIME(Slot.startTime, Slot.duration) AS endTime, Slot.location, Slot.RSVPlim AS RSVPlim
	FROM Slot
	INNER JOIN Event ON Slot.eventID = Event.id
	WHERE Slot.eventID = ?
	) AS S
	LEFT JOIN (
	SELECT slotID, COUNT( * ) AS count
	FROM Reservation
	GROUP BY slotID
	) AS C ON C.slotID = S.id
	INNER JOIN Invite I ON S.eventID = I.eventID AND I.receiverID = ?
	LEFT JOIN Reservation ON S.id = Reservation.slotID AND I.ID = Reservation.inviteID
	WHERE (S.RSVPlim > C.count OR C.count IS NULL) AND Reservation.inviteID IS NULL
	ORDER by S.startTime ASC;
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
// Output: If any is found, then an 1d associative array is returned. The keys are id, title, description, dateStartTime, RSVPslotLim, and creatorID
//         Otherwise, null is returned.
function eventFromInviteID($conn, $inviteID) {
	$sql = "SELECT * FROM Event 
			INNER JOIN Invite ON Invite.eventID = Event.id
			WHERE Invite.id = ?";
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
// 		array keys: id, startTime, duration, location, RSVPlim, eventID, endTime
function slotDetails($conn, $id){
	$stmt = $conn->prepare("SELECT *, ADDTIME(startTime, duration) AS endTime FROM Slot WHERE id = ? ORDER BY endTime DESC");
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
	$stmt = $conn->prepare("SELECT P.id, P.text, P.fileName, P.timeStamp, U.id AS userID, U.firstName, U.lastName, U.onidUID, P.slotID FROM Slot S
			INNER JOIN Post P ON S.id = P.slotID 
			INNER JOIN User U ON P.senderID = U.id 
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
	$stmt = $conn->prepare("SELECT * FROM `Post` WHERE `senderID` = ? AND `slotID` = ?");
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
	$stmt = $conn->prepare("SELECT senderID FROM `Post` WHERE `id` = ?");
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
	$stmt = $conn->prepare("SELECT I.receiverID FROM Reservation R
			INNER JOIN Invite I ON R.inviteID = I.id
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
	$stmt = $conn->prepare("SELECT COUNT(*) AS userRSVPcount FROM User U 
			INNER JOIN Invite I ON U.id = I.receiverID 
			INNER JOIN Reservation R on I.id = R.inviteID 
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
	$stmt = $conn->prepare("SELECT COUNT(*) AS slotRSVPcount FROM Reservation R 
			INNER JOIN Slot S ON R.slotID = S.id 
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
	$stmt = $conn->prepare("SELECT U.lastName, U.firstName FROM Slot S 
			INNER JOIN Reservation R ON S.id = R.slotID 
			INNER JOIN Invite I ON R.inviteID = I.id 
			INNER JOIN User U ON I.receiverID = U.id 
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
// Function: eventUpdate(conn, id, info[])
// Description: update an event's details
// Input: 
//		conn = MySQL database connection object 
//		id = id of event on database 
//		info = associative array containing the updated data using following keys:
//				title, description, dateStartTime (YYYY-MM-DD HH:MM), RSVPslotLim
// Output: true if successful, false if update failed 
function eventUpdate($conn, $id, $info){
	$stmt = $conn->prepare("UPDATE Event 
			SET title = ?, description = ?, dateStartTime = ?, RSVPslotLim = ?
			WHERE id = ?");
	$stmt->bind_param("sssii", $info['title'], $info['description'], $info['dateStartTime'], $info['RSVPslotLim'], $id);
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
	$stmt = $conn->prepare("UPDATE User 
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
	$stmt = $conn->prepare("UPDATE Reservation 
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
	$stmt = $conn->prepare("UPDATE Post 
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
	$stmt = $conn->prepare("UPDATE Invite 
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
	$stmt = $conn->prepare("DELETE FROM Event WHERE id = ?");
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
	$stmt = $conn->prepare("DELETE FROM Slot WHERE id = ?");
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
	$stmt = $conn->prepare("DELETE FROM Reservation WHERE inviteID = ? AND slotID = ?");
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
	$stmt = $conn->prepare("DELETE FROM Post WHERE id = ?");
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
	$stmt = $conn->prepare("DELETE FROM Invite WHERE id = ?");
	$stmt->bind_param("i", $id);
	return $stmt->execute();
}

?>