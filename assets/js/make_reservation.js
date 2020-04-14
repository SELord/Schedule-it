// javascript functions for make_reservation.php 
var availableSlots = pageInfo['fullCal'];
var eventInfo = pageInfo['eventInfo'];
var eventDate = eventInfo['dateStartTime'].substr(0,10); // extract just the date of event 
var RSVPremaining = eventInfo['RSVPremaining'];


// real functions
function generateList() {
    let calendarE1 = document.getElementById('content');
    let calendar = new FullCalendar.Calendar(calendarE1, {
        plugins: [ 'list', 'timeGrid' ],

        header: {
		  left: '',
          center: 'title',
		  right: 'listDay, timeGridDay'
        },
		
		// customize button names
		views: {
			listDay: { buttonText: 'list view'}, 
			timeGridDay: { buttonText: 'grid view'}
		},
  
        defaultView: 'listDay',
        defaultDate: eventDate,
        navLinks: true,  //can click day/week names to navigate views
        editable: false,
        eventLimit: true, //allow "more" link when too many events
        events: availableSlots,
		eventRender: function(info) {
			let remainingRes = info.event.extendedProps.RSVPlim - info.event.extendedProps.RSVPs;
			// display current reservation availability 
			let dispRSVP = document.createElement('td');
			dispRSVP.className = 'fc-list-item-title fc-widget-content';
			let RSVPdata = document.createElement('a');
			RSVPdata.append('Available reservations: ' + remainingRes);
			dispRSVP.append(RSVPdata);
			info.el.append(dispRSVP);
		},
		eventClick: function(info) {
			// use for handling when a user clicks a slot to make a reservation 
			// reference: https://stackoverflow.com/questions/133925/javascript-post-request-like-a-form-submit?rq=1
			info.jsEvent.preventDefault();
			var body = {};  // hold data that will be sent to server
			body['inviteID'] = info.event.extendedProps.inviteID;
			body['slotID'] = info.event.id;
			body['eventID'] = info.event.extendedProps.eventID;
			
			// create reservation in database
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

function eventDisplay() {
	document.getElementById("eventTitle").innerHTML = eventInfo['title'];
	document.getElementById("eventDesc").innerHTML = eventInfo['description'];
	let RSVPslotLim = eventInfo['RSVPslotLim']
	if ( RSVPslotLim === null){
		RSVPslotLim = 'Unlimited';
	}
	document.getElementById("RSVPslotLim").innerHTML = 'Your remaining RSVPs for Event: ' + RSVPremaining;
}

function RSVPlimitReached() {
	document.getElementById("eventTitle").innerHTML = eventInfo['title'];
	document.getElementById("eventDesc").innerHTML = eventInfo['description'];
	document.getElementById("RSVPslotLim").innerHTML = 'You have reached the reservation limit for this event';
}

function noSlots() {
	document.getElementById("content").innerHTML = 'Event is full, no reservations available';
}

if (RSVPremaining > 0 || RSVPremaining == 0){
	document.addEventListener('DOMContentLoaded', eventDisplay);
	if (availableSlots === null){
		document.addEventListener('DOMContentLoaded', noSlots);
	}
	else{
		document.addEventListener('DOMContentLoaded', generateList);
	}
}
else{
	document.addEventListener('DOMContentLoaded', RSVPlimitReached);
}