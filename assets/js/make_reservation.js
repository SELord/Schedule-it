/**
 * Global variables used for functions in make_reservation.php.
 * @availableSlots: used in generateList() and at end of file
 * @eventInfo: used in generateList(), eventDisplay(), RSVPlimitReached()
 * @RSVPremaining: used in eventDisplay() and at end of file
 */
var availableSlots = pageInfo['fullCal'];
var eventInfo = pageInfo['eventInfo'];
var RSVPremaining = eventInfo['RSVPremaining'];


/**
 * generateList() generates a list of reservations.
 * It creates a calendar, displays the list of existing reservations,
 * 	allows user to click a slot to make a reservation, and adds
 * 	that reservation to the database.
 */
function generateList() {
	let calendarE1 = document.getElementById('content');

	// creates calendar
    let calendar = new FullCalendar.Calendar(calendarE1, {
        plugins: [ 'list', 'timeGrid' ],

        header: {
		  left: '',
          center: 'title',
		  right: 'listDay, timeGridDay'
        },
		
		// customizes button names
		views: {
			listDay: { buttonText: 'list view'}, 
			timeGridDay: { buttonText: 'grid view'}
		},
  
        defaultView: 'listDay',
        defaultDate: eventInfo['dateStart'],
        navLinks: true,  	// can click day/week names to navigate views
        editable: false,
        eventLimit: true, 	// allows "more" link when too many events
		events: availableSlots,
		
		/**
		 * eventRender() displays current reservation availability.
		 * @param {*} info 
		 */
		eventRender: function(info) {
			let remainingRes = info.event.extendedProps.RSVPlim - info.event.extendedProps.RSVPs;
			let dispRSVP = document.createElement('td');
			dispRSVP.className = 'fc-list-item-title fc-widget-content';
			let RSVPdata = document.createElement('a');
			RSVPdata.append('Available reservations: ' + remainingRes);
			dispRSVP.append(RSVPdata);
			info.el.append(dispRSVP);
		},
		
		/**
		 * eventClick() is used for handling when a user clicks a slot to make a reservation.
		 * Reference: https://stackoverflow.com/questions/133925/javascript-post-request-like-a-form-submit?rq=1
		 * @param {*} info 
		 */
		eventClick: function(info) {
			info.jsEvent.preventDefault();
			var body = {};  	// holds data that will be sent to server
			body['inviteID'] = info.event.extendedProps.inviteID;
			body['slotID'] = info.event.id;
			body['eventID'] = info.event.extendedProps.eventID;
			
			// creates reservation in database
			const form = document.createElement('form');
			form.method = 'post';
			form.action = 'confirm_reservation.php';
			
			for (const key in body) {
				if (body.hasOwnProperty(key)) {
				const hiddenField = document.createElement('input');
				hiddenField.type = 'hidden';
				hiddenField.name = key;
				hiddenField.value = body[key];
			
				form.appendChild(hiddenField);
				}
			}
			
			document.body.appendChild(form);
			form.submit();
		}
    });
    calendar.render();
}

/**
 * eventDisplay() displays events -- title, description, RSVP slot limit.
 * If there is no RSVP slot limit, it will be set to unlimited.
 * Also tells the user how many RSVPs they have remaining for their event.
 */
function eventDisplay() {
	document.getElementById("eventTitle").innerHTML = eventInfo['title'];
	document.getElementById("eventDesc").innerHTML = eventInfo['description'];
	let RSVPslotLim = eventInfo['RSVPslotLim']
	if ( RSVPslotLim === null) {
		RSVPslotLim = 'Unlimited';
	}
	document.getElementById("RSVPslotLim").innerHTML = 'Your remaining RSVPs for Event: ' + RSVPremaining;
}

/**
 * RSVPlimitReached() tells the user when they have reached the RSVP limit for their event.
 */
function RSVPlimitReached() {
	document.getElementById("eventTitle").innerHTML = eventInfo['title'];
	document.getElementById("eventDesc").innerHTML = eventInfo['description'];
	document.getElementById("RSVPslotLim").innerHTML = 'You have reached the reservation limit for this event';
}

/**
 * noSlots() tells the user when the event is full.
 */
function noSlots() {
	document.getElementById("content").innerHTML = 'Event is full, no reservations available';
}

/**
 * Driver code displays functions as needed.
 * eventDisplay() is called by default.
 * noSlots() is called when there are no slots remaining.
 * generateList() is called when noSlots() is not called.
 * RSVPlimitReached() is called when neither noSlots() nor generateList() is called.
 */
if (RSVPremaining > 0 || RSVPremaining == 0){
	document.addEventListener('DOMContentLoaded', eventDisplay);
	if (availableSlots === null){
		document.addEventListener('DOMContentLoaded', noSlots);
	}
	else {
		document.addEventListener('DOMContentLoaded', generateList);
	}
}
else {
	document.addEventListener('DOMContentLoaded', RSVPlimitReached);
}
