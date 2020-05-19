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

    //BUTTON TO CREATE NEW EVENT - SUBMIT BUTTON IN CREATE_EVENT.PHP
    $('#signupbtn').on('click',function(e){
        e.preventDefault();
        var title = $('#title').val();
        var description = $('#description').val();
        var dateStart = $('#dateStart').val();
        var dateEnd = $('#dateEnd').val();
        var creatorID = $('#creatorID').val();    
        var location = $('#location').val();
        $.ajax({
            url:"../Schedule-it/database/event/insert.php",
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
    // Don't try to use this for the dates or after submitting, no matter where
    // you click on the calendar it will just say mm/dd/yyyy until you refresh
    title = $('#title').val('');
    description = $('#description').val('');
    location = $('#location').val('');
    });

    // GIVES FUNCTIONALITY TO X BUTTON. Now actually clears form when clicked
    // Also does not work with dates
    $(document).on('click', '.ui-dialog-titlebar-close', function(){
        var title = $('#title').val();
        var description = $('#description').val();
        var location = $('#location').val();
        title = $('#title').val('');
        description = $('#description').val('');
        location = $('#location').val('');
    });

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
