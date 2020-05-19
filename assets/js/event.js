//Get today's date
let today = new Date();
let dd = String(today.getDate()).padStart(2, '0');
let mm = String(today.getMonth() + 1).padStart(2, "0");
let yyyy = today.getFullYear();
today = yyyy + '-' + mm + '-' + dd;

//Get global date
var dateStr;
var eventID;

//Function to convert epoch to H:m  
function ConvertNumberToTwoDigitString(n) {
    return n > 9 ? "" + n : "0" + n;
}

//Function to validate that entered email is actually an email and domain is either @oregonstate.edu or @eecs.oregonstate.edu
function validateEmail(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if(re.test(email)){
        //Email valid. Proceed to test if it's from the right domain (Second argument is to check that the string ENDS with this domain, and that it doesn't just contain it)
        if(email.indexOf("@oregonstate.edu", email.length - "@oregonstate.edu".length) !== -1 || email.indexOf("@eecs.oregonstate.edu", email.length - "@eecs.oregonstate.edu".length) !== -1){
            //VALID
            return email;
        } else {
            return false; //this will automatically return an error because input type HAS to be email
        }
    } else {
        //Entered email, does not match regex, so it is not a valid email
        return false;
    }
}

//For list-viewing capabilities
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
        selectable: true,
        navLinks: true,  //can click day/week names to navigate views
        editable: true,
        eventLimit: true, //allow "more" link when too many events
        events: '../Schedule-it/database/event/load.php?onidID='+ onidID,
        dateClick: function(info) {
            $("#dateStart").attr("value", dateStr);
            $("#dateEnd").attr("value", dateStr);
            $( "#dialog-form" ).dialog();
        }
    });
    calendar.render();
}

//For calendaring-viewing capabilities
function generateGrid() {
    let calendarE1 = document.getElementById('content');
    let calendar = new FullCalendar.Calendar(calendarE1, {
        contentHeight: 600,
        plugins: [ 'interaction', 'dayGrid', 'timeGrid'],
        timeZone: 'UTC',
        header: {
            left: 'prevYear, prev, next, nextYear, today',
            center: 'title',
            right: 'dayGridMonth, timeGridWeek, timeGridDay'
        },
        defaultDate: today,
        selectable: true,
        droppable: true,
        navLinks: true,  //can click day/week names to navigate views
        editable: true,
        eventLimit: true, //allow "more" link when too many events
        selectable: true,
        eventSources: [
        {
            url: '../Schedule-it/database/event/reservations.php?onidID='+ onidID,
        },
        {
            url: '../Schedule-it/database/event/load.php?onidID='+ onidID,
            color: 'coral'
        }
        ],
        //SOURCE: https://stackoverflow.com/questions/55929421/how-to-refresh-fullcalendar-v4-after-change-events-object-using-ajax
        eventDrop: function(info) {
            //alert(info.event.title + " was dropped on " + info.event.start.toISOString());
            var id = info.event.id;
            var title = info.event.title;
            var dateStart = info.event.dateStart.toISOString();
            var dateEnd = info.event.dateEnd.toISOString();
            if (confirm("Are you sure about this change?")) {
                $.ajax({
                    url:"../Schedule-it/database/event/update.php",
                    type:"POST",
                    data:{id:id, dateStart:dateStart, dateEnd:dateEnd},
                    success:function() {
                        calendar.refetchEvents();
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            }
        },
        eventResize: function(info) {
            var id = info.event.id;
            var event = calendar.getEventById(id);
            var title = info.event.title;
            var start = event.start.toISOString();
            var end = event.end.toISOString();
            if (confirm("Are you sure about this change?")) {
                $.ajax({
                    url:"../Schedule-it/database/event/update.php",
                    type:"POST",
                    data:{id:id, start:start, end:end},
                    success:function() {
                        calendar.refetchEvents();
                    },
                    error: function(error) {
                        console.log(error);
                    }
                })
            } 
        },
        eventClick: function(info) {
            var id = info.event.id;
            //TO POPULATE EVENT ID TO AJAX CALLS 
            $("#edit-delete")
            .data('id', id)
            .dialog();
        },
        dateClick: function(info) {
            dateStr = info.dateStr;
            if(dateStr.indexOf("T") > -1) {
                dateStr = dateStr.split("T")[0];
            }
            $("#dateStart").attr("value", dateStr);
            $("#dateEnd").attr("value", dateStr);
            $( "#dialog-form" ).dialog();
        }
    });
    calendar.render();

    // Create Event button
    $('#createEvent').click(function(){
        $( "#dialog-form" ).dialog();
    });

    // for formulating a row in slot display modal, used in edit_data() and $('#edit-slotbtn')
    function slotRow(item) {
        return '<tr id="slot' + item.id + '">'
        + '<td><input type="date" class="slotStartDateEdit" data-id="' + item.id + '" id="slotAttr' + item.id + '" value="' + item.startDateTime.split(' ')[0] + '" style="width: 9em"></td>'
        + '<td><input type="time" class="slotStartTimeEdit" data-id="' + item.id + '" id="slotAttr' + item.id + '" value="' + item.startDateTime.split(' ')[1] + '"></td>'
        + '<td><input type="date" class="slotEndDateEdit" data-id="' + item.id + '" id="slotAttr' + item.id + '" value="' + item.endDateTime.split(' ')[0] + '" style="width: 9em"></td>'
        + '<td><input type="time" class="slotEndTimeEdit" data-id="' + item.id + '" id="slotAttr' + item.id + '" value="' + item.endDateTime.split(' ')[1] + '"></td>'
        + '<td><input type="text" class="slotLocationEdit" data-id="' + item.id + '" id="slotAttr' + item.id + '" value="' + item.location + '"></td>'
        + '<td><input type="number" class="slotRVSPlimEdit" data-id="' + item.id + '" id="slotAttr' + item.id + '" value="' + item.RSVPlim + '" style="width: 4em"></td>'
        + '<td><button type="button" class="btn btn-danger slotDeleteButton" data-id="' + item.id + '" id="slotAttr' + item.id + '">X</button></td>'
        + '</tr>';
    }

    // for use in edit_data() and $('#edit-slotbtn')
    const noSlots = '<td id="noSlotsRow" colspan="7">No slots in the event</td>';

    // for editing slot data, used below
    function edit_data(id, key, value) {  
        $.ajax({  
            url:"../Schedule-it/database/event/update_slot.php",  
            method:"POST",  
            data:{
                id:id,
                key:key, 
                value:value
            },  
            success:function(data){
                // return integer means a slot was created
                if (data) {
                    // display the row on the table
                    const item = JSON.parse(data);
                    $('#slotEditTable').append(slotRow(item));
                }
            },
            error: function(error) {
                console.log(error);
            }  
        });  
    }
    
    // add slot button
    $('#addSlot').click(function(){
        const eventID = $("#edit-delete").data('id');  //to get ID from event-click variable
        edit_data(eventID, "add");
        $('#noSlotsRow').remove()
    });

    // delete button for each slot
    $(document).on('click', '.slotDeleteButton', function(){
        edit_data($(this).data("id"), "delete");        // edit the data in database
        var button_id = $(this).data("id");
        $('#slot'+button_id+'').remove();               // remove the row from the modal
        if ($('#slotEditTable tr').length < 2) {        // add "no slots" comment if no slots
            $('#slotEditTable').append(noSlots);
        }
    });

    // update the slot in database real-time for each entry field
    $(document).on('blur', '.slotStartDateEdit', function(){
        var dateTime = $(this).val() + " " + $('#slotAttr' + $(this).data("id") + '.slotStartTimeEdit').val()
        edit_data($(this).data("id"), "startDateTime", dateTime);
    });
    $(document).on('blur', '.slotStartTimeEdit', function(){
        var dateTime = $('#slotAttr' + $(this).data("id") + '.slotStartDateEdit').val() + " " + $(this).val()
        edit_data($(this).data("id"), "startDateTime", dateTime);
    });
    $(document).on('blur', '.slotEndDateEdit', function(){
        var dateTime = $(this).val() + " " + $('#slotAttr' + $(this).data("id") + '.slotEndTimeEdit').val()
        edit_data($(this).data("id"), "endDateTime", dateTime);
    });
    $(document).on('blur', '.slotEndTimeEdit', function(){
        var dateTime = $('#slotAttr' + $(this).data("id") + '.slotEndDateEdit').val() + " " + $(this).val()
        edit_data($(this).data("id"), "endDateTime", dateTime);
    });
    $(document).on('blur', '.slotLocationEdit', function(){
        edit_data($(this).data("id"), "location", $(this).val());
    });
    $(document).on('blur', '.slotRVSPlimEdit', function(){
        edit_data($(this).data("id"), "RSVPlim", $(this).val());
    });

    //TRIGGER EDIT SLOT CHANGES (click "edit slots" button)
    $('#edit-slotbtn').on('click',function(e){
        e.preventDefault();
        const eventID = $("#edit-delete").data('id');  //to get ID from event-click variable
        $("#live_data").dialog({
            resizable: true,
            width: 1100,
            //height: 300,  // gbdg-ebg 12/12/2011 Change height from 190 to 250
            modal: true,
            close: function() {
                // repopulate the slot data modal every time it opens
                $('#slotEditTable').empty();
                $('#slotEditTable').append('<tr id="slotTableHeader"><th>Start Date</th><th>Start Time</th><th>End Date</th><th>End Time</th><th>Location</th><th>RSVP Limit</th><th>Delete</th></tr>');
            }
        });
        // get the list of slots from db
        $.ajax({
            url:"../Schedule-it/database/event/get_slots.php",
            type:"POST",
            data: {id:eventID}, 
            success:function(data){
                data = JSON.parse(data);
                // display the slots on the table
                if (data.length > 0) {
                    data.forEach(item => {
                        $('#slotEditTable').append(slotRow(item));
                    });
                } else {
                    $('#slotEditTable').append(noSlots);        // add "no slots" comment if no slots
                }
            },  
            error: function(error) {
                console.log(error);
            }
        });  
    });


    //TRIGGER EDIT CHANGES FROM EDIT-FORM ON "CONFIRM CHANGES" BUTTON (click "Confirm Changes" button on event)
    $('#edit-submit').on('click',function(e){
        var id = $("#edit-delete").data('id');  //to get ID from event-click variable
        //var event = calendar.getEventById(id);
        e.preventDefault();
        var title = $('#titleedit').val();
        var description = $('#descriptionedit').val();
        var location = $('#locationedit').val();
        var dateStart = $('#dateStartEdit').val();
        var dateEnd = $('#dateEndEdit').val();
        $.ajax({
            url:"../Schedule-it/database/event/update_month.php",
            type:"POST",
            data: {
                id:id,
                title:title,
                description:description,
                location:location,
                dateStart:dateStart,
                dateEnd:dateEnd
            },
            success: function(response){
                //console.log(response);
                calendar.refetchEvents();
                alert("Edited Successfully");
                $("#edit-form").dialog("close");
                $("#edit-delete").dialog("close");
            },
            error: function(error) {
                console.log(error);
            },
            complete: function() {
                $("#edit-form").dialog("close");
                $("#edit-delete").dialog("close");
            }
        })
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
    // Don't try to use this for the dates or after submitting, no matter where
    // you click on the calendar it will just say mm/dd/yyyy until you refresh
    title = $('#title').val('');
    description = $('#description').val('');
    location = $('#location').val('');
    });

    // GIVES FUNCTIONALITY TO X BUTTON. Now actually clears form when clicked
    // Also does not work with dates
    $(document).on('click', '.ui-dialog-titlebar-close', function(){
        var title = $('#title').val();
        var description = $('#description').val();
        var location = $('#location').val();
        title = $('#title').val('');
        description = $('#description').val('');
        location = $('#location').val('');
    });

    $("body > *").not("body > button").click(function(e) {
        console.log(e.target.id);
       if(e.target.id=='dialog-form'){
               return false;
        }
        $('div#dialog-form').hide();

   });

    //BUTTON TO TRIGGER DELETE - THIS GETS FORM DATA FOR EDIT-FORM 
    $('#deletebtn').on('click',function(e) {
        var id = $("#edit-delete").data('id');
        var remove = calendar.getEventSourceById(id);
        var event = calendar.getEventById(id);
        e.preventDefault();
        if(confirm("Are you sure you want to remove " + event.title + "?")) {
            $.ajax({
                url:"../Schedule-it/database/event/delete.php",
                type:"POST",
                data:{id:id},
                success:function() {
                    calendar.refetchEvents();
                    $("#edit-delete").dialog('close');
                },
                complete: function() {
                    $("#edit-delete").dialog('close');
                    calendar.refetchEvents();
                },
                error: function(error) {
                    console.log(error);
                }
            })
        }
    });

    //BUTTON TO TRIGGER THE EDIT-MODE - THIS GETS FORM DATA FOR EDIT-FORM (click "Edit" button on event)
    $('#editbtn').on('click',function(e) {
        e.preventDefault();
        var id = $("#edit-delete").data('id');  //to get ID from event-click variable
        var event = calendar.getEventById(id);
        var titleedit = event.title;
        var descriptionedit = event.extendedProps.description;
        var locationedit = event.extendedProps.location;
        var dateStartEdit = event.start.toISOString().split('T')[0];
        var dateEndEdit = new Date;                 // jump through hoops to display date - 1
        dateEndEdit.setDate(event.end.getDate() - 1);
        dateEndEdit = dateEndEdit.toISOString().split('T')[0];
        $("#date").attr("value", event.dateStr);
        $("#titleedit").attr("value", titleedit);
        $("#descriptionedit").attr("value", descriptionedit); 
        $("#locationedit").attr("value", locationedit); 
        $("#dateStartEdit").attr("value", dateStartEdit);
        $("#dateEndEdit").attr("value", dateEndEdit);
        $("#edit-form").dialog();
    });
 
    //FOR DYNAMIC EMAIL FUNCTIONALITY
    var i=1;
    // adding an email row HTML
    function emailRow(i) {
        return '<tr id="row'+i+'"><td><input type="text" name="name[]" placeholder="Enter Email" class="form-control name_list" /></td><td><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove">X</button></td></tr>';
    }

    // add an email row
    $('#addEmail').click(function(){  
        i++;  
        $('#dynamic_field').append(emailRow(i));  
    });  

    // remove an email row
    $(document).on('click', '.btn_remove', function(){  
        var button_id = $(this).attr("id");   
        $('#row'+button_id+'').remove();  
    });  

    //BUTTON TO TRIGGER EMAIL FORM
    $('#sendEmail').on('click',function(e){
        if ($('#dynamic_field tr').length < 1) {
            i++;
            $('#dynamic_field').append(emailRow(i));  
        }
        $("#send-email").dialog({ width: 400 });
    });

    //Get email from form, validate it, and send using emails.php file
    $('#submitEmail').on('click',function(){     
        var id = $("#edit-delete").data('id');  //to get ID from event-click variable
        var event = calendar.getEventById(id);
        var creatorID = $('#creatorID').val();    
        var jsonPayload = {
            id:id,
            creatorID: creatorID,
            emails: []
        };
        var nameList = $("#dynamic_field .name_list");
        var nameListindex = 0;
        for(nameListindex = 0; nameListindex < nameList.length; nameListindex++) {
            if(validateEmail(nameList[nameListindex].value) == false) {
                alert("Error!! " + nameList[nameListindex].value + " is NOT a valid '@oregonstate.edu' email address");
                return false;
            } else {
                jsonPayload.emails.push(nameList[nameListindex].value);
            }
        };
        $.ajax({
            url:"../Schedule-it/database/event/emails.php",
            type:"POST",
            data: jsonPayload,
            success:function(data) {
                alert("Emails have been sent!");
                $("#send-email").dialog('close');
                $("#edit-delete").dialog('close');
                calendar.refetchEvents();
            },
            complete: function() {
                calendar.refetchEvents();
                //$("#send-email").dialog('close');
            },
            error: function(error) {
                alert("Unable to send emails");
                console.log(error);
            }
        }); 
    });  
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

//Call to generateGrid once Homepage is loaded
document.addEventListener('DOMContentLoaded', generateGrid);
