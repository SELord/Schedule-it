// java script for user's event history page
function createdEventHist() {
    let mostRecent;
    if (pastEvents.length > 0) {
        mostRecent = pastEvents[pastEvents.length - 1]['start']; // fullcalendar.io can handle getting the date from a string with the date and time
    }
    let calendarE1 = document.getElementById('content');
    let calendar = new FullCalendar.Calendar(calendarE1, {
        plugins: [ 'list' ],

        header: {
		  left: '',
          center: 'title',
		  right: 'listDay, listWeek, listMonth, prev, next'
        },
		
		// customize button names
		views: {
			listDay: { buttonText: 'Day'}, 
			listWeek: { buttonText: 'Week'},
			listMonth: { buttonText: 'Month'},
		},
		
        defaultView: 'listWeek',
        defaultDate: mostRecent,
        navLinks: true,  //can click day/week names to navigate views
        editable: false,
        eventLimit: true, //allow "more" link when too many events
        events: pastEvents,
	});

	// create event button
	document.getElementById('createEventDiv').innerHTML = '<right><button type="button" class="btn btn-large" id="createEvent">Create Event</button><br />';
	$('#createEvent').click(function(){
		$( "#dialog-form" ).dialog();
	});

    calendar.render();
}

function reservationHist() {
    let mostRecent;
    if (pastEvents.length > 0) {
        mostRecent = pastReservations[pastReservations.length - 1]['start']; // fullcalendar.io can handle getting the date from a string with the date and time
    }
    let calendarE1 = document.getElementById('content');
    let calendar = new FullCalendar.Calendar(calendarE1, {
        plugins: [ 'list' ],

        header: {
		  left: '',
          center: 'title',
		  right: 'listDay, listWeek, listMonth, prev, next'
        },
		
		// customize button names
		views: {
			listDay: { buttonText: 'Day'}, 
			listWeek: { buttonText: 'Week'},
			listMonth: { buttonText: 'Month'},
		},
		
        defaultView: 'listWeek',
        defaultDate: mostRecent,
        navLinks: true,  //can click day/week names to navigate views
        editable: false,
        eventLimit: true, //allow "more" link when too many events
        events: pastReservations,
    });
    calendar.render();
}

function inviteHist() {
    let mostRecent;
    if (pastEvents.length > 0) {
        mostRecent = pastInvites[pastInvites.length - 1]['start']; // fullcalendar.io can handle getting the date from a string with the date and time
    }
    let calendarE1 = document.getElementById('content');
    let calendar = new FullCalendar.Calendar(calendarE1, {
        plugins: [ 'list' ],

        header: {
		  left: '',
          center: 'title',
		  right: 'listDay, listWeek, listMonth, prev, next'
        },
		
		// customize button names
		views: {
			listDay: { buttonText: 'Day'}, 
			listWeek: { buttonText: 'Week'},
			listMonth: { buttonText: 'Month'},
		},
		
        defaultView: 'listWeek',
        defaultDate: mostRecent,
        navLinks: true,  //can click day/week names to navigate views
        editable: false,
        eventLimit: true, //allow "more" link when too many events
        events: pastInvites,
    });
    calendar.render();
}

function showResHist(event){
	event.stopPropagation();
	document.getElementById("resHistButton").disabled = true;
	document.getElementById("eventHistButton").disabled = false;
	document.getElementById("inviteHistButton").disabled = false;
	document.getElementById("viewTitle").innerHTML = 'All Your Reservations';
	document.getElementById("content").innerHTML = "";
	document.getElementById("createEventDiv").innerHTML = "";
	reservationHist();
	
}

function showInviteHist(event){
	event.stopPropagation();
	document.getElementById("inviteHistButton").disabled = true;
	document.getElementById("eventHistButton").disabled = false;
	document.getElementById("resHistButton").disabled = false;
	document.getElementById("viewTitle").innerHTML = 'All Events You Are Invited To';
	document.getElementById("content").innerHTML = "";
	document.getElementById("createEventDiv").innerHTML = "";
	inviteHist();
	
}

function showEventHist(event){
	event.stopPropagation();
	document.getElementById("eventHistButton").disabled = true;
	document.getElementById("resHistButton").disabled = false;
	document.getElementById("inviteHistButton").disabled = false;
	document.getElementById("viewTitle").innerHTML = 'All Events You Created';
	document.getElementById("content").innerHTML = "";
	document.getElementById("createEventDiv").innerHTML = "";
	createdEventHist();
}

document.addEventListener('DOMContentLoaded', showResHist);
