<?php
// PHP error reporting for debug info. Commented out for production
// For more information: https://stackify.com/display-php-errors/
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

    require_once 'file_path.php';

    session_start();
    //check once again if the user is logged in
    //if not, redirect back to login page

/*
    echo "Session: " . $_SESSION["loggedin"];
    echo "Session: " . $_SESSION["onidID"];
    echo "Session: " . $_SESSION["lastName"];
    echo "Session: " . $_SESSION["firstName"];
    echo "Session: " . $_SESSION["email"];
    echo "NOT SET: " . !isset($_SESSION["loggedin"]);
    echo "NOT SET: " . $_SESSION["loggedin"];
*/
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] == FALSE) {
        session_destroy();
        session_unset();
        $_SESSION = array();
        header("Location: " . $FILE_PATH . "login.php");
    }
       
    //TODO: Retrieve the upcoming events and meetings, and reserved meetings 
    //of the user to populate the calendar
    require_once './database/dbconfig.php';
    require_once './database/dbquery.php';
    
    $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname, $dbport);
    if ($mysqli->connect_errno) {
          echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
          exit;
      } 

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
    //    2nd dim array keys: eventID, inviteID, slotID, title, dateStartTime, startTime, duration, location, endTime
    // *V EVENT BUG
    $reservationsMadeByUser = reservedSlotHist($mysqli, $user->id);
    
    $reservations = array();
    foreach ($reservationsMadeByUser as $idx => $res) {
        $eventItem = array();
        $slotID = $res["slotID"];
        $inviteID = $res["inviteID"];
        $eventItem["id"] = $slotID;
        $eventItem["eventID"] = $res["eventID"];
        $eventItem["title"] = $res["title"];
        $date = explode(" ", $res["dateStartTime"])[0];
        $eventItem["start"] = $date."T".$res["startTime"];
        $eventItem["end"] = $date."T".$res["endTime"];
        $eventItem["url"] = "view_reservation.php?slot=$slotID&inviteID=$inviteID";
        array_push($reservations, $eventItem);
    }
    $mysqli->close();
    
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <title>Schedule-it</title>
  
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

    <!-- div for Calendar-->
    <div class="container-fluid" id="content">
        
    </div>

<div id="dialog-form" style="display:none;" title="Create new event">
   <p class="validateTips">All form fields are required.</p>

<form>
  <fieldset>
      <input type="hidden" id="date" name="date">
      <label for="title">Meeting title: </label>
      <input type="text" name="title" id="title" class="text ui-widget-content ui-corner-all" required>

      <label for="description">Description: </label>
      <input type="text" name="description" id="description" class="text ui-widget-content ui-corner-all">

      <label for="location">Location:  </label>
      <input type="text" name="location" id="location" class="text ui-widget-content ui-corner-all">  

      <label for="dateStartTime">Start Time: </label>
          <input type="time" name="dateStartTime" id="dateStartTime" class="text ui-widget-content ui-corner-all" required>

      <label for="duration">Duration: <small><i>HH:mm format only</i></small></label>
          <input type="text" name="duration" id="duration" class="text ui-widget-content ui-corner-all" required>

      <label for="slots">How many time slots? </label>
          <input type="number" name="slots" id="slots" class="text ui-widget-content ui-corner-all" min="1">

      <label for="RSVPLim">Max attendees per slot: </label>
          <input type="number" name="RSVPLim" id="RSVPLim" class="text ui-widget-content ui-corner-all" min="0">  

      <label for="RSVPslotLim">Max Reservations per attendee: </label>
          <input type="number" name="RSVPslotLim" id="RSVPslotLim" class="text ui-widget-content ui-corner-all" min="0">  

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
   <p class="validateTips">All form fields are required.</p>
      <button type="button" id="edit-slotbtn">Edit Slots</button>

<form>
  <fieldset>
      <input type="hidden" id="dateedit" name="dateedit" value="">
      <label for="titleedit">Event title: </label>
      <input type="text" name="titleedit" id="titleedit" value="" class="text ui-widget-content ui-corner-all">

      <label for="descriptionedit">Event Description: </label>
      <input type="text" name="descriptionedit" id="descriptionedit" class="text ui-widget-content ui-corner-all">

      <label for="dateStartTimeedit">Event Start Time: </label>
          <input type="time" name="dateStartTimeedit" id="dateStartTimeedit" class="text ui-widget-content ui-corner-all">

      <label for="durationedit">Event Duration: <small><i>HH:mm format only</i></small></label>
          <input type="text" name="durationedit" id="durationedit" class="text ui-widget-content ui-corner-all">

      <label for="RSVPslotLimedit">Max Reservations per attendee: </label>
          <input type="number" name="RSVPslotLimedit" id="RSVPslotLimedit" class="text ui-widget-content ui-corner-all" min="0">  

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
            <tr>  
               <td><button type="button" name="add" id="add" class="btn btn-success">Add Email Slot</button></td>  
               <td><input type="email" name="name[]" placeholder="Enter Email" class="form-control name_list" /></td>  
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
     <div id="live_data" title="Edit Event Slots"></div>                 
</div>  
 

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <!--<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>-->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>
