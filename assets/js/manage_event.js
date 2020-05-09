// javascript for event management page

function eventInfo(){
    document.getElementById("eventTitle").textContent = eventDetails.title;
    document.getElementById("eventDesc").textContent = eventDetails.description;
    document.getElementById("eventDate").textContent = eventDetails.date;
    document.getElementById("startTime").textContent = 'Start: ' + eventDetails.dateStartTime;
    document.getElementById("endTime").textContent = 'End: ' + eventDetails.dateEndTime;
    //document.getElementById("location").textContent = 'Location: ' + slotDetails.location;
    //document.getElementById("remainingRes").textContent = 'Available Reservations: ' + slotDetails.remainingRes;
}


document.addEventListener('DOMContentLoaded', eventInfo);
