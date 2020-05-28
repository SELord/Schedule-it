/**
 * This file is used for the Event Management page.
 * It displays event information and the shareable event link.
 * NOTE: Actually, I don't think this file is currently being used...
 */

 /**
  * eventInfo() displays the event information --
  *     title, description, start date, and end date.
  * The eventDate, location, and remainingRes items are no longer being used.
  */
function eventInfo(){
    document.getElementById("eventTitle").textContent = eventDetails.title;
    document.getElementById("eventDesc").textContent = eventDetails.description;
    //document.getElementById("eventDate").textContent = eventDetails.date;
    document.getElementById("dateStart").textContent = 'Start: ' + eventDetails.dateStart;
    document.getElementById("dateEnd").textContent = 'End: ' + eventDetails.dateEnd;
    //document.getElementById("location").textContent = 'Location: ' + slotDetails.location;
    //document.getElementById("remainingRes").textContent = 'Available Reservations: ' + slotDetails.remainingRes;
}


/**
 * showShareableEventLink() displays the shareable event link.
 * It displays attendees' first and last name.
 */
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
