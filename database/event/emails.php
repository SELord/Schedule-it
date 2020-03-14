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
    $emails = $_POST['emails']; //this is the array object that holds emails


    //put into array
    for($i = 0; $i < count($emails); $i++) {  

      //parse email to onidUID 
      list($onidUID, $edu) = explode('@', $emails[$i]);
      echo "\n$onidUID" . $onidUID;
      echo "\n$edu" . $edu;

      //create new user w/ null firstName and lastName values
      $data = lookupUser($connect, $onidUID);
      var_dump($data);
      $user = json_decode($data);

      // check if user exists, if null, create new user
      if($data == null) {
        //create a new user if user does not exists
        //NOTE: I had problems with newInvite function from dbquery.php, so copied and pasted it here
        $stmt = $connect->prepare("INSERT INTO User (onidUID, email) VALUES (?, ?)");
        $stmt->bind_param("ss", $onidUID, $emails[$i]);
        $stmt->execute();

        //get userID of recently created user
        $userID = mysqli_insert_id($connect);
      } else {
        //if user in database, then return recieverID
        $userID = $user->id;
      }

      //Now create new invite - (id, email, status, receiverID, eventID)
      //NOTE: I had problems with newInvite function from dbquery.php, so copied and pasted it here
      $newInvite_stmt = $connect->prepare("INSERT INTO Invite (receiverID, eventID, email)
          VALUES (?, ?, ?)");
      $newInvite_stmt ->bind_param("iis", $userID, $eventID, $emails[$i]);
      if($newInvite_stmt ->execute()){
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