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

	// Displays the create event button.
	document.getElementById('createEventDiv').innerHTML = '<right><button type="button" class="btn btn-large" id="createEvent">Create Event</button><br />';
	$('#createEvent').click(function(){
		$( "#dialog-form" ).dialog();
	});

    //BUTTON TO CREATE NEW EVENT - SUBMIT BUTTON IN CREATE_EVENT.PHP
    $('#signupbtn').on('click',function(e){
        e.preventDefault();
        var title = $('#title').val();
        var description = $('#description').val();
        var dateStart = $('#dateStart').val();
        var dateEnd = $('#dateEnd').val();
        var creatorID = $('#creatorID').val();    
        var location = $('#location').val();

        // input validation for dates
        if ((Date.parse(dateStart) > Date.parse(dateEnd))) {
            alert("Error: start date cannot be after end date!");
            document.getElementById("dateEnd").value = "";
        }
        else {
        $.ajax({
            url:"../scheduleit/database/event/insert.php",
            type:"POST",
            data: {title:title, description:description, dateStart:dateStart, dateEnd:dateEnd, creatorID:creatorID, location:location},
            complete: function() {
                $( "#dialog-form" ).dialog( "close" );
            },
            success: function(){
                calendar.refetchEvents();
                alert("Added Successfully");
            },
            error: function(error) {
                console.log(error);
            }
        })
    // THIS CODE CLEARS THE FORM. Without it, data stays even after submitting
    title = $('#title').val('');
    description = $('#description').val('');
    location = $('#location').val('');
    }});

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
    let mostRecent;
    if (pastReservations.length > 0) {
        // fullcalendar.io can handle getting the date from a string with the date and time
        mostRecent = pastReservations[pastReservations.length - 1]['start']; 
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
    let mostRecent;
    if (pastInvites.length > 0) {
        // fullcalendar.io can handle getting the date from a string with the date and time
        mostRecent = pastInvites[pastInvites.length - 1]['start']; 
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
	document.getElementById("createEventDiv").innerHTML = "";
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
	document.getElementById("createEventDiv").innerHTML = "";
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
	document.getElementById("createEventDiv").innerHTML = "";
	createdEventHist();
}

document.addEventListener('DOMContentLoaded', showResHist);
