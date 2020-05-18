// javascript for event management page
function createdEventHist() {
	let mostRecent = pastEvents[pastEvents.length - 1]['start']; // fullcalendar.io can handle getting the date from a string with the date and time
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
        console.log("loc1");
		$( "#dialog-form" ).dialog();
	});

	// add days to a date
	// reference: https://stackoverflow.com/questions/563406/add-days-to-javascript-date
	function addDays(date, days) {
		var result = new Date(date);
		result.setDate(result.getDate() + days);
		// reference: https://stackoverflow.com/questions/23593052/format-javascript-date-as-yyyy-mm-dd
		const offset = result.getTimezoneOffset()
		result = new Date(result.getTime() + (offset*60*1000))
		return result.toISOString().split('T')[0]
	}
	
    //BUTTON TO CREATE NEW EVENT - SUBMIT BUTTON IN CREATE_EVENT.PHP
    $('#signupbtn').on('click',function(e){
        e.preventDefault();
        var title = $('#title').val();
        var description = $('#description').val();
        //get correct date format
        //var date = dateStr;
        var dateStart = $('#dateStart').val();
        var dateEnd = $('#dateEnd').val();
        dateEnd = addDays(dateEnd, 1);      // +1 day for fullcalendar display
        //var slots = $('#slots').val();
        //var RSVPslotLim = $('#RSVPslotLim').val();
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
    });

    calendar.render();
}

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
