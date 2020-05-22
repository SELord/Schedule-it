// javascript for event management page

function eventInfo(){
    document.getElementById("eventTitle").textContent = eventDetails.title;
    document.getElementById("eventDesc").textContent = eventDetails.description;
    document.getElementById("dateStart").textContent = 'Start: ' + eventDetails.dateStart;
    document.getElementById("dateEnd").textContent = 'End: ' + eventDetails.dateEnd;
    createTable();
}


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


document.addEventListener('DOMContentLoaded', eventInfo);
