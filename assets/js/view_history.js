/**
 * view_history.js is used on the homepage and in view_history.php.
 * It displays all events the user has either created, RSVPed for, or been invited to.
 */

// Get today's date
// Today's date is used to default the list view to the current date so the 
// user sees which events are coming up next by default
let today = new Date();
let dd = String(today.getDate()).padStart(2, '0');
let mm = String(today.getMonth() + 1).padStart(2, "0");
let yyyy = today.getFullYear();
today = yyyy + '-' + mm + '-' + dd;

/**
 * createdEventHist() uses fullcalendar.io to display events the user has created.
 */
function createdEventHist() {
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
        defaultDate: today,
        navLinks: true,     //can click day/week names to navigate views
        editable: false,
        eventLimit: true,   //allow "more" link when too many events
        events: pastEvents,
	});
    calendar.render();
}

    // GIVES FUNCTIONALITY TO X BUTTON. Now actually clears form when clicked
    $(document).on('click', '.ui-dialog-titlebar-close', function(){
        var title = $('#title').val();
        var description = $('#description').val();
        var location = $('#location').val();
        title = $('#title').val('');
        description = $('#description').val('');
		location = $('#location').val('');
		dateStart = $('#dateStart').val('');
		dateEnd = $('#dateEnd').val('');
    });

/**
 * reservationHist() uses fullcalendar.io to display events user has RSVPed to.
 */
function reservationHist() {
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
        defaultDate: today,
        navLinks: true,     //can click day/week names to navigate views
        editable: false,
        eventLimit: true,   //allow "more" link when too many events
        events: pastReservations,
    });
    calendar.render();
}

/**
 * inviteHist() uses fullcalendar.io to display events user has been invited to.
 */
function inviteHist() {
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
        defaultDate: today,
        navLinks: true,     //can click day/week names to navigate views
        editable: false,
        eventLimit: true,   //allow "more" link when too many events
        events: pastInvites,
    });
    calendar.render();
}

/**
 * showResHist() displays the user's reservation history.
 * It also disables the reservation history button, since it's already being displayed.
 * Enables event history button and invite history button.
 * @param {*} event 
 */
function showResHist(event){
	event.stopPropagation();
	document.getElementById("resHistButton").disabled = true;
	document.getElementById("eventHistButton").disabled = false;
	document.getElementById("inviteHistButton").disabled = false;
	document.getElementById("viewTitle").innerHTML = 'All Your Reservations';
	document.getElementById("content").innerHTML = "";
	//document.getElementById("createEventDiv").innerHTML = "";
	reservationHist();
	
}

/**
 * showInviteHist() displays the user's invite history.
 * It also disables the invite history button, since it's already being displayed.
 * Enables event history button and reservation history button.
 * @param {*} event 
 */
function showInviteHist(event){
	event.stopPropagation();
	document.getElementById("inviteHistButton").disabled = true;
	document.getElementById("eventHistButton").disabled = false;
	document.getElementById("resHistButton").disabled = false;
	document.getElementById("viewTitle").innerHTML = 'All Events You Are Invited To';
	document.getElementById("content").innerHTML = "";
	//document.getElementById("createEventDiv").innerHTML = "";
	inviteHist();
	
}

/**
 * showEventHist() displays the user's created event history.
 * It also disables the event history button, since it's already being displayed.
 * Enables reservation history button and invite history button.
 * @param {*} event 
 */
function showEventHist(event){
	event.stopPropagation();
	document.getElementById("eventHistButton").disabled = true;
	document.getElementById("resHistButton").disabled = false;
	document.getElementById("inviteHistButton").disabled = false;
	document.getElementById("viewTitle").innerHTML = 'All Events You Created';
	document.getElementById("content").innerHTML = "";
	//document.getElementById("createEventDiv").innerHTML = "";
	createdEventHist();
}

document.addEventListener('DOMContentLoaded', showResHist);
