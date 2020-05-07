 <?php 

/*******************************************************************
TRIGGED BY EVENT.JS WHEN USER HITS "SUBMIT" AFTER SENDING EMAILS

WHAT THIS FILE DOES: 
- CREATES A NEW USER FOR EACH EMAIL
- THEN, CREATES A NEW INVITE FOR EACH PERSON

TO-DO/BUGS NOTED IN COMMENTS
/********************************************************************/

require '../dbconfig.php';  // database connection 
require '../dbquery.php';   // functions for accessing database
require '../../assets/php/emailer.php';  // email functions

 //connect to the database
 $connect = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    if (!$connect) {
        echo "Error: unable to connect to MySQL: Errorno - " . mysqli_connect_errno() . PHP_EOL;
        exit; 
    } else {
        echo "Connected to database - success";
    }

    //get eventID, creatorID and list of emails from event.js
    $eventID = $_POST['id'];
    $creatorID = $_POST['creatorID'];
    $onidUID = $_POST['emails']; //this is the array object that holds ONIDs


    //put into array
    for($i = 0; $i < count($onidUID); $i++) {  

      //create new user with only ONID
      $data = lookupUser($connect, $onidUID);
      var_dump($data);
      $user = json_decode($data);

      // check if user exists, if null, create new user
      if($data == null) {
        //create a new user if user does not exists
        $info['onidUID'] = $onidUID[$i];
        $info['firstName'] = "";
        $info['lastName'] = "";
        $info['email'] = "";
        $userID = newUser($connect, $info);

      } else {
        //if user in database, then return recieverID
        $userID = $user->id;
      }

      //Now create new invite - (id, status, receiverID, eventID)
      $info['receiverID'] = $userID;
      $info['eventID'] = $eventID;
      $inviteID = newInvite($connect, $info);
      if($inviteID){
        echo "Invite created";
      } else{
        echo "Error - invite not created";
        exit;
      }
    }
  
  // send out emails for new event invites
  newEventEmail($connect, $eventID);

  $connect->close();

  /******************TO DO FOR NEXT GROUP****************
  (1) CREATE AN EMAIL TO PHYSICALLY SEND RESERVATION TO INVITEE AND SEND A URL TO INVITEE
  (2) USE THESE FUNCTIONS TO SEND EMAIL -- "newEventEmail", "updateEventEmail", and "updateSlotEmail"
        - Find these functions in Schedule-it/assets/php/emailer.php 

  **/


 ?> 