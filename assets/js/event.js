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
            //$("#date").attr("value", info.dateStr);  
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
            dateEnd = addDays(dateEnd, 1);      // +1 day for fullcalendar display
            //alert(id + ' ' + title + ' ' + start + ' ' + end);
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
            //else {info.revert();}
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
            //$("#date").attr("value", dateStr);
            $("#dateStart").attr("value", dateStr);
            $("#dateEnd").attr("value", dateStr);
            //SOURCE: https://stackoverflow.com/questions/20518516/how-can-i-get-time-from-iso-8601-time-format-in-javascript
            var mydate = new Date(info.dateStr);
            var time = ConvertNumberToTwoDigitString(mydate.getUTCHours()) + 
                ":" + ConvertNumberToTwoDigitString(mydate.getUTCMinutes());
            // Returns the given integer as a string and with 2 digits
            // For example: 7 --> "07"
            //$("#dateStartTime").attr("value", time);
            $( "#dialog-form" ).dialog();
        }
    });
    calendar.render();

    //TRIGGER EDIT SLOT CHANGES
    $('#edit-slotbtn').on('click',function(e){
        e.preventDefault();
        var id = $("#edit-delete").data('id');  //to get ID from event-click variable
        $("#live_data").dialog({
            resizable: true,
            width: 750,
            height: 300,  // gbdg-ebg 12/12/2011 Change height from 190 to 250
            modal: true,
        });
        $.ajax({
            url:"../Schedule-it/database/event/edit_slot.php",
            type:"POST",
            data: {id:id}, 
            success:function(data){  
                $('#live_data').html(data);
                //var editSlotDiv = '<div class="table-responsive" title="Edit Event Slots" id="editSlotDiv">';
                //var slotEditTable = '<table class="table table-bordered" id="slotEditTable" style="width:100%">';
                //$('#live_data').append(editSlotDiv);
                //$('#editSlotDiv').append(slotEditTable);
            },  
            error: function(error) {
                console.log(error);
            }
        });  
  
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
                    if (data)
                        console.log(data);
                },
                error: function(error) {
                    console.log(error);
                }  
            });  
        }
        // delete button for each slot
        $(document).on('click', '.slotDeleteButton', function(){
            edit_data($(this).data("id"));
            $("#"+$(this).attr("id")).remove();
        });

        // update the database real-time for each entry field
        $(document).on('blur', '.slotStartTimeEdit', function(){
            edit_data($(this).data("id"), "startTime", $(this).val());
        });
        $(document).on('blur', '.slotEndTimeEdit', function(){
            edit_data($(this).data("id"), "endTime", $(this).val());
        });
        $(document).on('blur', '.slotLocationEdit', function(){
            edit_data($(this).data("id"), "location", $(this).val());
        });
        $(document).on('blur', '.slotRVSPlimEdit', function(){
            edit_data($(this).data("id"), "RSVPlim", $(this).val());
        });
    });


    //TRIGGER EDIT CHANGES FROM EDIT-FORM ON "CONFIRM CHANGES" BUTTON
    $('#edit-submit').on('click',function(e){
        var id = $("#edit-delete").data('id');  //to get ID from event-click variable
        //var event = calendar.getEventById(id);
        e.preventDefault();
        var title = $('#titleedit').val();
        var description = $('#descriptionedit').val();
        //var getdate = event.start.toISOString();
        //turn date YYYY-MM-DD
        //var date = getdate.split("T")[0];
        var dateStart = $('#dateStartEdit').val();
        var dateEnd = $('#dateEndEdit').val();
        dateEnd = addDays(dateEnd, 1);      // +1 day for fullcalendar display
        //var duration = $('#durationedit').val();
        //var RSVPslotLim = $('#RSVPslotLimedit').val();
        $.ajax({
            url:"../Schedule-it/database/event/update_month.php",
            type:"POST",
            data: {
                id:id, 
                title:title, 
                description:description, 
                dateStart:dateStart,
                dateEnd:dateEnd
            },
            success: function(response){
                console.log(response);
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

    //BUTTON TO TRIGGER THE EDIT-MODE - THIS GETS FORM DATA FOR EDIT-FORM 
    /**** TO DO FOR NEXT GROUP : Be able to change location and # of slots dynamically ***/
    $('#editbtn').on('click',function(e) {
        e.preventDefault();
        var id = $("#edit-delete").data('id');  //to get ID from event-click variable
        var event = calendar.getEventById(id);
        var titleedit = event.title;
        var descriptionedit = event.extendedProps.description;
        var locationedit = event.extendedProps.location;
        var dateStartEdit = event.start.toISOString().split('T')[0];
        var dateEndEdit = event.end.toISOString().split('T')[0];
        dateEndEdit = addDays(dateEndEdit, -1);      // -1 day for fullcalendar display (reverse)
        //var getTime = new Date(dateStartTimeedit);
        //var time = ConvertNumberToTwoDigitString(getTime.getUTCHours()) + 
        //    ":" + ConvertNumberToTwoDigitString(getTime.getUTCMinutes());
        //var durationedit = event.extendedProps.duration;
        //var RSVPslotLimedit = event.extendedProps.RSVPslotLim;
        $("#date").attr("value", event.dateStr);
        $("#titleedit").attr("value", titleedit);
        $("#descriptionedit").attr("value", descriptionedit); 
        $("#locationedit").attr("value", locationedit); 
        $("#dateStartEdit").attr("value", dateStartEdit);
        $("#dateEndEdit").attr("value", dateEndEdit);
        //$("#durationedit").attr("value", durationedit);
        //$("#RSVPslotLimedit").attr("value", RSVPslotLimedit);
        $("#edit-form").dialog();
    });
 
    //FOR DYNAMIC EMAIL FUNCTIONALITY
    var i=1;  
    $('#addEmail').click(function(){  
        i++;  
        $('#dynamic_field').append('<tr id="row'+i+'"><td><input type="text" name="name[]" placeholder="Enter Email" class="form-control name_list" /></td><td><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove">X</button></td></tr>');  
    });  

    $(document).on('click', '.btn_remove', function(){  
        var button_id = $(this).attr("id");   
        $('#row'+button_id+'').remove();  
    });  

    //BUTTON TO TRIGGER EMAIL FORM
    $('#sendEmail').on('click',function(e){
        $("#send-email").dialog();
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
