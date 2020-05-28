/**
 * This is the JS file for view_reservation.php.
 */

/** 
 * postMessages() displays the reservation information.
 */
function postMessages() {

	let len = posts.length;
	
	let tableBody = document.getElementById('posts');
	
	// build table 
	for(let i = 0; i < len; i++){
		// create new row 
		let row = document.createElement('tr');
		tableBody.appendChild(row);
		
		// add 4 cells to new row 
		for(var j = 0; j < 4; j++){
			let cell = document.createElement('td');
			row.appendChild(cell);
		}
	}
	
	// fill in table
	for(let i = 0; i < len; i++){
		let tableCells = tableBody.rows[i].cells;
		tableCells[0].textContent = posts[i].timeStamp;
		tableCells[1].textContent = posts[i].firstName + ' ' + posts[i].lastName;
		tableCells[2].textContent = posts[i].text;
		
		// get files (files/{{onid}}_slot{{slotID}}_filename)
		if(posts[i].fileName){
			console.log("fileName: \'" + posts[i].fileName + "\'");
			let fileLink = document.createElement('a');
			fileLink.setAttribute('href','files/' + posts[i].onidID + "_slot" + posts[i].slotID + "_" + posts[i].fileName);
			fileLink.setAttribute('download', posts[i].fileName);
			fileLink.append(posts[i].fileName);
			tableCells[3].append(fileLink);
		}
	}
}

/** 
 * eventInfo() displays event information -- title, description, start time, end time,
 * 		location, and the number of reservation slots remaining.
 */
function eventInfo(){
	document.getElementById("eventTitle").textContent = eventDetails.title;
	document.getElementById("eventDesc").textContent = eventDetails.description;
	document.getElementById("startTime").textContent = 'Start: ' + slotDetails.startDateTime;
	document.getElementById("endTime").textContent = 'End: ' + slotDetails.endDateTime;
	document.getElementById("location").textContent = 'Location: ' + slotDetails.location;
	document.getElementById("remainingRes").textContent = 'Available Reservations: ' + slotDetails.remainingRes;

}

/**
 * buildAttendeeList() displays all attendees for any given event.
 */
function buildAttendeeList(){
	// fill bootstrap modal (popup box) with list of people who have reserved slot
	let modalList = document.getElementById("attendeeList");
	let len = attendees.length;
	
	// build list
	for(let i = 0; i < len; i++){
		let item = document.createElement('li');
		item.className = 'list-group-item';
		item.textContent = attendees[i]['lastName'] + ', ' + attendees[i]['firstName'];
		modalList.appendChild(item);
	}
	
	
}

document.addEventListener('DOMContentLoaded', eventInfo);
document.addEventListener('DOMContentLoaded', postMessages);
document.addEventListener('DOMContentLoaded', buildAttendeeList);