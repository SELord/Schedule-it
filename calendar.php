<?php
    include 'file_path.php';
    session_start();
    //check once again if the user is logged in
    //if not, redirect back to login page

    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] == FALSE) {
        session_destroy();
        session_unset();
        $_SESSION = array();
        //header("Location: " . $FILE_PATH . "login.php");
        echo "<script type='text/javascript'> document.location = '" . $FILE_PATH . "login.php'; </script>";
    }
       
    //TODO: Retrieve the upcoming events and meetings, and reserved meetings 
    //of the user to populate the calendar
    require_once './database/dbconfig.php';
    require_once './database/dbquery.php';
    
    $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    if ($mysqli->connect_errno) {
          echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
          exit;
      } 

    //var_dump($_SESSION);
    //Find userID based off of onid
     $data = lookupUser($mysqli, $_SESSION["onidID"]);
     $user = json_decode($data);
     //echo "USER: " . $user->id;

    // Output: if any are found, then a 2D associative array containing event info with
    //         the first dimension being row number of result, else NULL.
    //       2nd dim array keys: id, title, dateStartTime
    $eventsCreatedByUser = eventCreateHist($mysqli, $user->id);
    
    // Output: if any are found, then a 2D associative array containing event info with
    //         the first dimension being row number of result, else NULL.
    //       2nd dim array keys: eventID, inviteID, title, dateStartTime, firstName, lastName
    $eventsYetToReserve = invitesUpcoming($mysqli, $user->id);

    // Output: if any are found, then a 2D associative array containing slot info with
    //         the first dimension being row number of result, else NULL.
    //    2nd dim array keys: eventID, inviteID, slotID, title, dateStartTime, startTime, location, endTime
    $reservationsMadeByUser = reservedSlotHist($mysqli, $user->id);
    
    $reservations = array();
    foreach ($reservationsMadeByUser as $idx => $res) {
        $eventItem = array();
        $slotID = $res["slotID"];
        $inviteID = $res["inviteID"];
        $eventItem["id"] = $slotID;
        $eventItem["eventID"] = $res["eventID"];
        $eventItem["title"] = $res["title"];
        //$date = $res["dateStart"];
        $eventItem["start"] = $res["startDateTime"];
        $eventItem["end"] = $res["endDateTime"];
        $eventItem["url"] = "view_reservation?slot=$slotID&inviteID=$inviteID";
        array_push($reservations, $eventItem);
    }

    $mysqli->close();

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <title>Schedule-It!</title>

  <script type="text/javascript">
      <?php 
            echo "var onidID = ".$user->id.";"
      ?>
  </script>
  
  <!--Bootstrap core CSS-->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
  <!--Customized css-->
  <link rel="stylesheet" href="./assets/css/main.css" type="text/css">

  <!--NEEDED FOR DIALOG-FORM DISPLAY -->
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
  
  <!-- javascript files -->
  <script src="./assets/js/main.js"></script> 
  <!-- <script src="./assets/js/homepage.js"></script> -->
  <script src="./assets/js/event.js"></script> 

  <!-- fontawesome for icon usage eg. navbar hamburger icon -->
  <script src="https://kit.fontawesome.com/96abf9bb58.js" crossorigin="anonymous"></script>

  <!--fullcalendar-->
  <!--Use daygrid-views for homepage -->
  <!--Use Selectable for event creation page-->
  <!--Source  fullcalendar.io-->
  <link href='./assets/js/fullcalendar/packages/core/main.css' rel='stylesheet' />
  <link href='./assets/js/fullcalendar/packages/daygrid/main.css' rel='stylesheet' />
  <link href='./assets/js/fullcalendar/packages/list/main.css' rel='stylesheet' />
  <link href='./assets/js/fullcalendar/packages/timegrid/main.css' rel='stylesheet' />
 <!--NEEDED FOR DIALOG-FORM DISPLAY -->
  <style>

    label, input { display:block; }
    input.text { margin-bottom:12px; width:95%; padding: .4em; }
    fieldset { padding:0; border:0; margin-top:25px; }
    h1 { font-size: 1.2em; margin: .6em 0; }
    div#users-contain { width: 350px; margin: 20px 0; }
    div#users-contain table { margin: 1em 0; border-collapse: collapse; width: 100%; }
    div#users-contain table td, div#users-contain table th { border: 1px solid #eee; padding: .6em 10px; text-align: left; }
    .ui-dialog .ui-state-error { padding: .3em; }
    .validateTips { border: 1px solid transparent; padding: 0.3em; }
    #live_data .ui-dialog {
     width: 100%;
     padding: 0; }

     .tooltip {
        position: relative;
        display: inline-block;
        border-bottom: 1px dotted black;
      }

      .tooltip .tooltiptext {
        visibility: hidden;
        width: 120px;
        background-color: black;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 5px 0;

        /* Position the tooltip */
        position: absolute;
        z-index: 1;
      }

      .tooltip:hover .tooltiptext {
        visibility: visible;
      }

      .hidden>div {
        display:none;
      }

      .visible>div {
        display:block;
      }

</style>

  <script src='./assets/js/fullcalendar/packages/core/main.js'></script>
  <script src='./assets/js/fullcalendar/packages/daygrid/main.js'></script>
  <script src='./assets/js/fullcalendar/packages/list/main.js'></script>
  <script src='./assets/js/fullcalendar/packages/interaction/main.js'></script>
  <script src='./assets/js/fullcalendar/packages/timegrid/main.js'></script>
  <script src='./assets/js/fullcalendar/packages/moment/main.js'></script>
</head>
<body>
  <!-- Mobile responsive navbar -->
    <nav class="navbar navbar-expand-md schedule-it-top-hat">
        <div class="container-fluid">
            <a class="navbar-brand logo" href="https://oregonstate.edu"><img src="https://oregonstate.edu/themes/osu/drupal8-osuhomepage/logo.svg"></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                <span><i class="fas fa-bars fa-1x"></i></span>
            </button>
 
            <!-- Collapsible content -->
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav schedule-it-main-menu mr-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="homepage.php">Schedule-It Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="calendar.php">Calendar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="eventmanagement.php">Manage Events</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view_history.php">Past Meetings</a>
                    </li>
                </ul>
                <ul class="navbar-nav schedule-it-main-menu ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <p>

    <!-- Passing variables from php to javascript to populate events and reservations -->
    <script type="text/javascript"> 
        let reservations = <?php echo json_encode($reservations) ?>; 
    </script>
    
    <!-- div for Events the user still need to make reservations for -->
    <div class="text_container">
        <ul id="upcomingEvents">
            <?php 
                foreach ($eventsYetToReserve as $idx => $event) {
                    $eventID = $event["eventID"];
                    $inviteID = $event["inviteID"];
                    $eventTitle = $event["title"];
                    $eventStartDate = $event["dateStart"];
                    $upcomingEvents = $eventStartDate;
                    $eventCreator = $event["firstName"]." ".$event["lastName"];
                    $li = "<a href=\"make_reservation?invite=$inviteID\" class=\"list-group-item list-group-item-action\" id=inviteID>Please RSVP to $eventTitle, starting on $eventStartDate, created by $eventCreator</a>";
                    echo $li;
                }
            ?>
        </ul>
    </div>

    <!-- div for Calendar-->
    <div class="container-fluid">
            <center><p><i>
            To create an event, while in <b>"Calendar View"</b>, click anywhere on any date in calendar month-view, week-view, or day-view and a <b>pop-up</b> will appear to create a new event/meeting. 
            </i></p>
<!--          <button type="button" class="btn btn-large" onclick="showList(event)" id="listButton">List View</button>
            <button type="button" class="btn btn-large" onclick="showCalendar(event)" id="calendarButton">Calendar View</button>
            </center>
-->
          <div class="row">
            <div class="col-sm-2"></div>
            <div class="col-sm-8">
              <center>
              <button type="button" class="btn btn-large" onclick="showList(event)" id="listButton">List View</button>
              <button type="button" class="btn btn-large" onclick="showCalendar(event)" id="calendarButton">Calendar View</button>
              </center>
            </div>
            <!-- div for create event button -->
            <div class="col-sm-2" id="createEventDiv" style="display: flex; justify-content: flex-end">
              <button type="button" class="btn btn-large" id="createEvent">Create Event</button>
            </div>
          </div>
    </div>
    <div class="container-fluid" id="content">
    </div>
    
<div id="dialog-form" style="display:none;" title="Create new event">

<form>
  <fieldset>
      <input type="text" name="titleedit" id="titleedit" placeholder="Event Title" value="" class="text ui-widget-content ui-corner-all">

      <input type="text" name="descriptionedit" id="descriptionedit" placeholder="Description" class="text ui-widget-content ui-corner-all">

      <input type="text" name="locationedit" id="locationedit" placeholder="Location" class="text ui-widget-content ui-corner-all">  

      <!-- Don't see why we need two date boxes since we're dealing with office hours,
      so now we just have one -->
      <input type="date" name="dateStartEdit" id="dateStartEdit" class="text ui-widget-content ui-corner-all"></td>

      <!-- TODO: Start Time and End Time need to be added back into database.
      Please note "Duration" has been replaced with "End Time." -->
      <table>
        <tr>
          <td>Start Time</td>
          <td>End Time</td>
        </tr>
        <tr>
          <td><input type="time" id="dateStartTime" name="dateStartTime" data-format="HH:mm" data-template="HH : mm" class="text ui-widget-content ui-corner-all" required></td>
          <td><input type="time" id="dateEndTime" name="dateEndTime" data-format="HH:mm" data-template="HH : mm" class="text ui-widget-content ui-corner-all" required></td>
        </tr>
      </table>
          <!--THIS IS CREATOR_ID -- SHOULD GET FROM SESSION -->
      <input type="hidden" name="creatorID" id="creatorID" value="<?php echo $user->id;?>" />   

      <!-- Allow form submission with keyboard without duplicating the dialog button -->
      <input type="submit" id="signupbtn">
    </fieldset>
  </form>
</div>


<!-- FORM FOR EDIT AND DELETE BUTTONS -->
<div id="edit-delete" style="display:none;" title="Edit or Delete">
<form>
  <fieldset>
      <input type="hidden" id="date" name="date" value="">
      <input type="hidden" name="creatorID" id="creatorID" value="<?php echo $user->id;?>" />   <!--THIS IS CREATOR_ID -- SHOULD GET FROM SESSION -->
      <!-- Allow form submission with keyboard without duplicating the dialog button -->
      <button type="button" id="sendEmail">Send Emails</button>
      <button type="button" id="editbtn">Edit</button>
      <button type="button" id="deletebtn">Delete</button>
    </fieldset>
  </form>
</div>

<!-- FORM FOR EDIT EVENT -->
<div id="edit-form" style="display:none;" title="Edit Current Event">
      <button type="button" id="edit-slotbtn">Edit Slots</button>

<form>
  <fieldset>
      <input type="text" name="titleedit" id="titleedit" placeholder="Event Title" value="" class="text ui-widget-content ui-corner-all">

      <input type="text" name="descriptionedit" id="descriptionedit" placeholder="Description" class="text ui-widget-content ui-corner-all">

      <input type="text" name="locationedit" id="locationedit" placeholder="Location" class="text ui-widget-content ui-corner-all">  

      <!-- Don't see why we need two date boxes since we're dealing with office hours,
      so now we just have one -->
      <input type="date" name="dateStartEdit" id="dateStartEdit" class="text ui-widget-content ui-corner-all"></td>

      <!-- TODO: Start Time and End Time need to be added back into database.
      Please note "Duration" has been replaced with "End Time." -->
      <table>
        <tr>
          <td>Start Time</td>
          <td>End Time</td>
        </tr>
        <tr>
          <td><input type="time" id="dateStartTime" name="dateStartTime" data-format="HH:mm" data-template="HH : mm" class="text ui-widget-content ui-corner-all" required></td>
          <td><input type="time" id="dateEndTime" name="dateEndTime" data-format="HH:mm" data-template="HH : mm" class="text ui-widget-content ui-corner-all" required></td>
        </tr>
      </table>

      <input type="hidden" name="creatorID" id="creatorID" value="<?php echo $user->id;?>" />   <!--THIS IS CREATOR_ID -- SHOULD GET FROM SESSION -->
      <!-- Allow form submission with keyboard without duplicating the dialog button -->
      <button type="button" id="edit-submit">Confirm Changes</button>
    </fieldset>
  </form>
</div>


<!-- FORM TO SEND EMAILS AFTER EVENT CREATED -->
<div id="send-email" style="display:none;" title="Send Emails">
<label for="email_invites">Email invites to: </label>
  <div class="form-group">  
    <form name="add_name" id="add_name">  
      <div class="table-responsive" id="add_name">  
         <table class="table table-bordered" id="dynamic_field">
          <button type="button" name="addEmail" id="addEmail" class="btn btn-success">Add Email Slot</button>  
          <p>
          <tr id="row0">
            <td><input type="text" name="name[]" placeholder="Enter Email" class="form-control name_list" /></td>
            <td><button type="button" name="remove" id="0" class="btn btn-danger btn_remove">X</button></td>
          </tr>
         </table>  
         <input type="hidden" name="creatorID" id="creatorID" value="<?php echo $user->id;?>" /> 
        <input type="button" name="submit" id="submitEmail" class="btn btn-info" value="Submit" />
      </div>  
    </form>  
  </div>  
</div>  


<!-- FORM FOR EDIT SLOT EVENT -->
<div class="table-responsive"  style="display:none;" title="Edit Event Slots">  
  <div id="live_data" title="Edit Event Slots">
    <div class="table-responsive" title="Edit Event Slots" id="editSlotDiv">
      <button type="button" name="addSlot" id="addSlot" class="btn btn-success">Add Slot</button>  
      <table class="table table-bordered" id="slotEditTable" style="width:100%">
        <tr id="slotTableHeader">
          <th>Start Date</th>
          <th>Start Time</th>
          <th>End Date</th>
          <th>End Time</th>
          <th>Location</th>
          <th>RSVP Limit</th>
          <th>Delete</th>
        </tr>
      </table>
    </div> 
  </div>                
</div>  
 
<!-- SCRIPT FOR THE HIDDEN TITLE AND DESCRIPTION -->
<script type="text/javascript">
  $(document).ready(function(){
    $('.text_container').addClass("hidden");

    $('.text_container').click(function() {
      var $this = $(this);

      if ($this.hasClass("hidden")) {
        $(this).removeClass("hidden").addClass("visible");

      } else {
        $(this).removeClass("visible").addClass("hidden");
      }
    });
  });

//-- SCRIPT FOR "HOVER OVER" FOR UPCOMING EVENTS, WAITING FOR RESPONSE 
$(document).ready(function(){
  $('[data-toggle="tooltip"]').tooltip();   
});

</script>
    <!-- Optional JavaScript -->
    <!-- Popper.js, then Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>

</body>
</html>

