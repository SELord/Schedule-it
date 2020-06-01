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
    document.getElementById("viewTitle").innerHTML = 'All Events You Created';
	document.getElementById("content").innerHTML = "";
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
    calendar.render();
}

//document.addEventListener('DOMContentLoaded', showEventHist);
document.addEventListener('DOMContentLoaded', createdEventHist);
