/**
 * This file is used for the Event Management page.
 * It displays event information and the shareable event link.
 * NOTE: Actually, I don't think this file is currently being used...
 */

function loadButtons(){
    document.getElementById('csvExport').addEventListener('click', exportToCSV, false);
    //document.getElementById('submitAnnouncement-button').addEventListener('click', sendAnnouncement);
    // announcement button
	//document.getElementById('createEventDiv').innerHTML = '<right><button type="button" class="btn btn-large" id="createEvent">Create Event</button><br />';
	$('#announcementButton').click(function(){
		$( "#dialog-form-announcement" ).dialog();
    });
    
    $('#submitAnnouncement-button').on('click',function(){     
        let id = eventDetails['id'];  //eventID
        let subject = $('#announce-subject').val();
        let message = $('#announce-message').val();
        let jsonPayload = {
            id:id,
            subject:subject,
            message:message
        };
        $.ajax({
            url:"../scheduleit/database/event/announcement.php",
            type:"POST",
            data: jsonPayload,
            success:function(data) {
                alert("Emails have been sent!");
                $("#dialog-form-announcement").dialog('close');
                //console.log(data);  // Need this console.log to see any php echo statements in announcement.php and others called through this ajax
            },
            error: function(error) {
                alert("Unable to send emails");
                console.log(error);
            }
        }); 
    });
}


// Populates Slot reservation data in table
 /**
  * eventInfo() displays the event information --
  *     title, description, start date, and end date.
  * The eventDate, location, and remainingRes items are no longer being used.
  */
function eventInfo(){
    document.getElementById("eventTitle").textContent = eventDetails.title;
    document.getElementById("eventDesc").textContent = eventDetails.description;
    document.getElementById("dateStart").textContent = 'Start: ' + eventDetails.dateStart;
    document.getElementById("dateEnd").textContent = 'End: ' + eventDetails.dateEnd;
    createTable();
}

// Adds rows for each slot in event
function createTable(){
    // loop to add each slot to the table
    for(x in slotDetails){
        addRow("reservationSlotTableBody", x, slotDetails[x]);
    }
}


// code referenced from https://developer.mozilla.org/en-US/docs/Web/API/HTMLTableElement/insertRow
function addRow(tableID, date, resCount){
    let tableRef = document.getElementById(tableID);

    // insert row at end of table
    let newRow = tableRef.insertRow(-1)

    // insert cell for dateTime
    let newCell_1 = newRow.insertCell(0);

    // insert cell for reservation count
    let newCell_2 = newRow.insertCell(1);

    // append the text to each cell
    let newText_1 = document.createTextNode(date);
    let newText_2 = document.createTextNode(resCount);

    // insert into table
    newCell_1.appendChild(newText_1);
    newCell_2.appendChild(newText_2);
}


// reference: https://stackoverflow.com/questions/14964035/how-to-export-javascript-array-info-to-csv-on-client-side
function exportToCSV(event){
    event.stopPropagation();

    // csvExportArr is defined in view_event.php
    let len = csvExportArr.length;

    // Set document type and header row info for csv file
    let csvContent = "data:text/csv;charset=utf-8,";
    csvContent = csvContent + "lastName,firstName,email,startDateTime,status\n";

    // loop through each row of the 2D array
    for(let row = 0; row < len; row++){
        // new row so clear out the previous row string
        let rowStr = "";

        // append each element value to the row string separated by a comma
        for(let key in csvExportArr[row]){
            rowStr = rowStr + csvExportArr[row][key] + ",";

        }
        // remove the trailing comma
        rowStr = rowStr.substring(0,rowStr.length - 1);
        
        // add a newline char to the end of the row string
        rowStr = rowStr + "\n";

        // append the completed row string to the output csvContent string
        csvContent = csvContent + rowStr;
    
    }
    // Encode the output csv string
    let encodedUri = encodeURI(csvContent);

    // create hidden link so that we can rename the output file to something more meaningful
    let link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "Schedule-it_Export.csv");
    document.body.appendChild(link);

    // automatically click the link to start the download
    link.click();
}

document.addEventListener('DOMContentLoaded', eventInfo);
document.addEventListener('DOMContentLoaded', loadButtons);
