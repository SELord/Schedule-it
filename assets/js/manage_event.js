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



function showShareableEventLink(){
    // fill bootstrap modal (popup box) with link for event reservation
    let modalList = document.getElementById("eventLink");
    //let len = attendees.length;
           
    // build list
    //for(let i = 0; i < len; i++){
    let link = document.createElement('li');
    link.className = 'list-group-item';
    link.textContent = attendees[i]['lastName'] + ', ' + attendees[i]['firstName'];
    modalList.appendChild(item);
    //}
}
/*
$('#submitAnnouncement').on('click', function(){
    let id = eventDetails.id;
});
*/
document.addEventListener('DOMContentLoaded', eventInfo);
