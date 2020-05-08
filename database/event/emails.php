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
        echo "Connected to database - success\n";
    }

    //get eventID, creatorID and list of emails from event.js
    $eventID = $_POST['id'];
    $creatorID = $_POST['creatorID'];
    $onidID = $_POST['emails']; //this is the array object that holds ONIDs

    //put into array
    for($i = 0; $i < count($onidID); $i++) {  

      //create new user with only ONID
      $data = lookupUser($connect, $onidID[$i]);
      $user = json_decode($data);

      // check if user exists, if null, create new user
      if($data == null) {
        //create a new user if user does not exists
        $userInfo['onidID'] = $onidID[$i];
        $userInfo['firstName'] = "";
        $userInfo['lastName'] = "";
        $userInfo['email'] = "";
        $userID = newUser($connect, $userInfo);

      } else {
        //if user in database, then return recieverID
        $userID = $user->id;
      }

      //Now create new invite, if it does not already exist
      if (!lookupInvite($connect, $userID, $eventID)){
        if(newInvite($connect, $userID, $eventID)){
          echo "Invite created for ";
        } else{
          echo "Error - invite not created for ";
          exit;
        }
      } else {
        echo "Invite already exists for ";
      }
      echo "userID " . $userID . "\n";
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