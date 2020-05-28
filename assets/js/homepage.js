//--------THIS FILE IS DEPRECATED - CHANGES MERGED OVER TO EVENT.JS 
//NOTE: SAVING FOR THE NEXT GROUP -- USING THIS FILE GIVES FUTURE STUDENTS BETTER UNDERSTANDING OF FULCALENDAR.IO

let today = new Date();
let dd = String(today.getDate()).padStart(2, '0');
let mm = String(today.getMonth() + 1).padStart(2, "0");
let yyyy = today.getFullYear();
today = yyyy + '-' + mm + '-' + dd;

function generateList() {
    let calendarE1 = document.getElementById('content');
    
    let calendar = new FullCalendar.Calendar(calendarE1, {
        plugins: [ 'list' ],

        header: {
          left: 'prev,next today',
          center: 'title',
          right: 'listDay,listWeek,dayGridMonth'
        },
  
        // customize the button names,
        // otherwise they'd all just say "list"
        views: {
          listDay: { buttonText: 'list day' },
          listWeek: { buttonText: 'list week' }
        },
  
        defaultView: 'listWeek',
        defaultDate: today,
        navLinks: true,  //can click day/week names to navigate views
        editable: true,
        eventLimit: true, //allow "more" link when too many events
        events: reservations
    });
    calendar.render();
}

function generateGrid() {
    let calendarE1 = document.getElementById('content');
    let calendar = new FullCalendar.Calendar(calendarE1, {
        plugins: ['interaction','dayGrid'],
        header: {
            left: 'prevYear, prev, next, nextYear today',
            center: 'title',
            right: 'dayGridMonth, dayGridWeek, dayGridDay'
        },
        defaultDate: today,
        navLinks: true,  //can click day/week names to navigate views
        editable: true,
        eventLimit: true, //allow "more" link when too many events
        events: reservations
    });
    calendar.render();
    
}

function showList(event) {
    event.stopPropagation();
    document.getElementById("listButton").disabled = true;
    document.getElementById("calendarButton").disabled = false;
    document.getElementById("content").innerHTML = "";
    generateList();
}

function showCalendar(event) {
    event.stopPropagation();
    document.getElementById("listButton").disabled = false;
    document.getElementById("calendarButton").disabled = true;
    document.getElementById("content").innerHTML = "";
    generateGrid();
}

document.addEventListener('DOMContentLoaded', generateGrid);

$.fn.bootstrapBtn = $.fn.button.noConflict();
