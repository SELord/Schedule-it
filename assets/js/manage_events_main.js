/**
 * This file is used for the Event Management page.
 * It displays created events and event history.
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
  * createdEventHist() uses the FullCalendar.io interface to display 
  *     all events the user has created.
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
        eventLimit: true,   //allows "more" link when too many events
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
            url:"../Schedule-it/database/event/insert.php",
            type:"POST",
            data: {
                title:title,
                description:description,
                location:location,
                RSVPslotLim:RSVPslotLim,
                dateStart:dateStart,
                dateEnd:dateEnd,
                creatorID:creatorID
            },
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

/**
 * showEventHist() displays all events created by the user.
 * @param {*} event 
 */
function showEventHist(event){
	event.stopPropagation();
	document.getElementById("viewTitle").innerHTML = 'All Events You Created';
	document.getElementById("content").innerHTML = "";
	createdEventHist();
}


document.addEventListener('DOMContentLoaded', showEventHist);
