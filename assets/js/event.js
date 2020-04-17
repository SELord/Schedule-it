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

//CURRENT BUG: Validationg email addresses accepts "username@" and just "username" without the @oregonstate.edu email address
//Function to validate where email is @oregonstate.edu
function validateEmail(email) { 
  var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  if(re.test(email)){
      //Email valid. Procees to test if it's from the right domain (Second argument is to check that the string ENDS with this domain, and that it doesn't just contain it)
      if(email.indexOf("@oregonstate.edu", email.length - "@oregonstate.edu".length) !== -1 || email.indexOf("@eecs.oregonstate.edu", email.length - "@eecs.oregonstate.edu".length) !== -1){
          //VALID
          return email;
      } else {
          return false; //this will automatically return an error because input type HAS to be email
      }
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
        events: '../Schedule-it/database/event/load.php',
        dateClick: function(info) {
            $("#date").attr("value", info.dateStr);  
            $( "#dialog-form" ).dialog();
          }
        });
    calendar.render();
}

//For calendaring-viewing capabilities
function generateGrid() {
    let calendarE1 = document.getElementById('content');
    let calendar = new FullCalendar.Calendar(calendarE1, {
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
          url: '../Schedule-it/database/event/reservations.php?onidID='+onidID,
        },
        {
          url: '../Schedule-it/database/event/load.php',
          color: 'coral'
        }
        ],
        //SOURCE: https://stackoverflow.com/questions/55929421/how-to-refresh-fullcalendar-v4-after-change-events-object-using-ajax
        eventDrop: function(info) {
            //alert(info.event.title + " was dropped on " + info.event.start.toISOString());
            var id = info.event.id;
            var title = info.event.title;
            var start = info.event.start.toISOString();
            var end = info.event.end.toISOString();
            //alert(id + ' ' + title + ' ' + start + ' ' + end);
            //console.log(start + ' ' + end);
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
                });
            }
            //else {info.revert();}
        },
        eventResize: function(info) {
            var id = info.event.id;
            var event = calendar.getEventById(id);
            var title = info.event.title;
            console.log(id + ' ' + title);
            var start = event.start.toISOString();
            var end = event.end.toISOString();
            console.log(start + ' ' + end);
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

            //console.log(dateStr);

            $("#date").attr("value", dateStr);  
            //SOURCE: https://stackoverflow.com/questions/20518516/how-can-i-get-time-from-iso-8601-time-format-in-javascript
            var mydate = new Date(info.dateStr);
            var time = ConvertNumberToTwoDigitString(mydate.getUTCHours()) + 
           ":" + ConvertNumberToTwoDigitString(mydate.getUTCMinutes());

            // Returns the given integer as a string and with 2 digits
            // For example: 7 --> "07"
            //console.log(time);
            $("#dateStartTime").attr("value", time);
            $( "#dialog-form" ).dialog();
        }//,
        //eventRender: function (info) {  //To get additional object attributes from DB
            //console.log(info.event.extendedProps);
            //console.log(info.event.extendedProps.description);
            //console.log(info.event.extendedProps.location);
            //console.log(info.event.extendedProps.slots);
            //console.log(info.event.extendedProps.RSVPLim);
        //}

    });

    calendar.render();

//TRIGGER EDIT SLOT CHANGES
$('#edit-slotbtn').on('click',function(e){
  e.preventDefault();
  //console.log("Inside slot edit form");
  var id = $("#edit-delete").data('id');  //to get ID from event-click variable
  $("#live_data").dialog({
          resizable: true,
          width: 700,
          height: 300,  // gbdg-ebg 12/12/2011 Change height from 190 to 250
          modal: true,
  });
  $.ajax({
  url:"../Schedule-it/database/event/edit_slot.php",
  type:"POST",
  data: {id:id}, 
      success:function(data){  
        $('#live_data').html(data);  
      },  
      error: function(error) {
      console.log(error);
     }
  });  
  
  function edit_data(id, text, parameter) {  
   $.ajax({  
      url:"../Schedule-it/database/event/update_slot.php",  
      method:"POST",  
      data:{
        id:id,
        text:text, 
        parameter:parameter
      },  
      dataType:"text",  
      success:function(data){  
           //alert(data);  
      }  
   });  
  }  
  $(document).on('blur', '.location', function(){  
   var id = $(this).data("id3");  
   var location = $(this).text();  
   edit_data(id, location, "location");  
  });  
  $(document).on('blur', '.RSVPlim', function(){  
   var id = $(this).data("id4");  
   var RSVPlim = $(this).text();  
   edit_data(id, RSVPlim, "RSVPlim");  
  });    
 });


//TRIGGER EDIT CHANGES FROM EDIT-FORM ON "CONFIRM CHANGES" BUTTON
$('#edit-submit').on('click',function(e){
  console.log("Inside edit event changes submit button");
  var id = $("#edit-delete").data('id');  //to get ID from event-click variable
  var event = calendar.getEventById(id);
  console.log(id);
  e.preventDefault();
   var title = $('#titleedit').val();
  var description = $('#descriptionedit').val();
  var getdate = event.start.toISOString();
  //turn date YYYY-MM-DD
  var date = getdate.split("T")[0];
  console.log(date);
  var dateStartTime = $('#dateStartTimeedit').val();
  var duration = $('#durationedit').val();
  var RSVPslotLim = $('#RSVPslotLimedit').val();
  $.ajax({
    url:"../Schedule-it/database/event/update_month.php",
    type:"POST",
    data: {
      id:id, 
      title:title, 
      description:description, 
      date:date,
      start:dateStartTime, 
      duration:duration, 
      RSVPslotLim:RSVPslotLim 
    },
   success: function()
   {
    calendar.refetchEvents();
    alert("Added Successfully");
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
  //console.log("Inside sign-up");
  var title = $('#title').val();
  var description = $('#description').val();
  //get correct date format
  var date = dateStr;
  //console.log(date)
  var dateStartTime = $('#dateStartTime').val();
  var duration = $('#duration').val();
  var slots = $('#slots').val();
  var RSVPslotLim = $('#RSVPslotLim').val();
  var creatorID = $('#creatorID').val();    
  var location = $('#location').val();
  var RSVPLim = $('#RSVPLim').val();
  $.ajax({
   url:"../Schedule-it/database/event/insert.php",
   type:"POST",
   data: {title:title, description:description, date:date, dateStartTime:dateStartTime, duration:duration, RSVPslotLim:RSVPslotLim, creatorID:creatorID, slots:slots, location:location, RSVPLim:RSVPLim},
    complete: function() {
    $( "#dialog-form" ).dialog( "close" );
   },
   success: function()
   {
    calendar.refetchEvents();
    alert("Added Successfully");
   },
   error: function(error) {
    console.log(error);
   }
  })
 });

//BUTTON TO TRIGGER DELETE - THIS GET'S FORM DATA FOR EDIT-FORM 
$('#deletebtn').on('click',function(e) {
  var id = $("#edit-delete").data('id');
  var remove = calendar.getEventSourceById(id);
  var event = calendar.getEventById(id);
  //console.log(id);
  //console.log("Delete button");
  e.preventDefault();
  if(confirm("Are you sure you want to remove " + event.title + "?")) {
      $.ajax({
         url:"../Schedule-it/database/event/delete.php",
         type:"POST",
         data:{id:id},
         success:function() {
          //console.log("Success");
            calendar.refetchEvents();
            $("#edit-delete").dialog('close');
            },
         complete: function() {
            //console.log("complete");
            $("#edit-delete").dialog('close');
            calendar.refetchEvents();
            },
         error: function(error) {
            console.log(error);
            }
        })
    }
});

//BUTTON TO TRIGGER THE EDIT-MODE - THIS GET'S FORM DATA FOR EDIT-FORM 
/**** TO DO FOR NEXT GROUP : Be able to change location and # of slots dynamically ***/
$('#editbtn').on('click',function(e) {
  console.log("inside edit btn");
  e.preventDefault();
  var id = $("#edit-delete").data('id');  //to get ID from event-click variable
  console.log(id);
  var event = calendar.getEventById(id);
  var titleedit = event.title;
  var descriptionedit = event.extendedProps.description;
  var dateStartTimeedit = event.start.toISOString();
  var getTime = new Date(dateStartTimeedit);
  var time = ConvertNumberToTwoDigitString(getTime.getUTCHours()) + 
         ":" + ConvertNumberToTwoDigitString(getTime.getUTCMinutes());
  var durationedit = event.extendedProps.duration;
  var RSVPslotLimedit = event.extendedProps.RSVPslotLim;
  $("#date").attr("value", event.dateStr);
  $("#titleedit").attr("value", titleedit);
  $("#descriptionedit").attr("value", descriptionedit); 
  $("#dateStartTimeedit").attr("value", time);
  $("#durationedit").attr("value", durationedit);
  $("#RSVPslotLimedit").attr("value", RSVPslotLimedit);
  $("#edit-form").dialog();
  });
 
//FOR DYNAMIC EMAIL FUNCTIONALITY
var i=1;  
$('#add').click(function(){  
     i++;  
     $('#dynamic_field').append('<tr id="row'+i+'"><td><input type="email" name="name[]" placeholder="Enter your email" class="form-control name_list" /></td><td><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove">X</button></td></tr>');  
});  

$(document).on('click', '.btn_remove', function(){  
     var button_id = $(this).attr("id");   
     $('#row'+button_id+'').remove();  
});  

//BUTTON TO TRIGGER EMAIL FORM
$('#sendEmail').on('click',function(e){
  $("#send-email").dialog();
});


//CURRENT BUG: Validationg email addresses accepts "username@" and just "username" without the @oregonstate.edu email address
$('#submitEmail').on('click',function(){     
  var id = $("#edit-delete").data('id');  //to get ID from event-click variable
  console.log(id);
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
  console.log(jsonPayload);
    $.ajax({
     url:"../Schedule-it/database/event/emails.php",
     type:"POST",
     data: jsonPayload,
     success:function(data) {
        alert("Emails have been sent!");
        console.log(data);
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

document.addEventListener('DOMContentLoaded', generateGrid);

