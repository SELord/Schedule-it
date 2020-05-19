// javascript for event management page
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

    //BUTTON TO CREATE NEW EVENT - SUBMIT BUTTON IN CREATE_EVENT.PHP
    $('#signupbtn').on('click',function(e){
        e.preventDefault();
        var title = $('#title').val();
        var description = $('#description').val();
        var dateStart = $('#dateStart').val();
        var dateEnd = $('#dateEnd').val();
        var creatorID = $('#creatorID').val();    
        var location = $('#location').val();
        //var RSVPLim = $('#RSVPLim').val();
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
    title = $('#title').val('');
    description = $('#description').val('');
	location = $('#location').val('');
	dateStart = $('#dateStart').val('');
	dateEnd = $('#dateEnd').val('');
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

/*
function eventInfo(){
    document.getElementById("eventTitle").textContent = eventDetails.title;
    document.getElementById("eventDesc").textContent = eventDetails.description;
    document.getElementById("eventDate").textContent = eventDetails.date;
    document.getElementById("startTime").textContent = 'Start: ' + eventDetails.dateStartTime;
    document.getElementById("endTime").textContent = 'End: ' + eventDetails.dateEndTime;
    //document.getElementById("location").textContent = 'Location: ' + slotDetails.location;
    //document.getElementById("remainingRes").textContent = 'Available Reservations: ' + slotDetails.remainingRes;
}
*/

function showEventHist(event){
	event.stopPropagation();
	document.getElementById("viewTitle").innerHTML = 'All Events You Created';
	document.getElementById("content").innerHTML = "";
	createdEventHist();
}


document.addEventListener('DOMContentLoaded', showEventHist);
