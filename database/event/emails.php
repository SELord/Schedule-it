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
 $connect = new mysqli($dbhost, $dbuser, $dbpass, $dbname, $dbport);
    if (!$connect) {
        echo "Error: unable to connect to MySQL: Errorno - " . mysqli_connect_errno() . PHP_EOL;
        exit; 
    } else {
        echo "Connected to database - success\n";
    }

    //get eventID, creatorID and list of emails from event.js
    $eventID = $_POST['id'];
    $creatorID = $_POST['creatorID'];
    $emails = $_POST['emails']; //this is the array object that holds emails

    //put into array
    for($i = 0; $i < count($emails); $i++) {  

      //parse email to onidID 
      list($onidID, $edu) = explode('@', $emails[$i]);

      //create new user w/ null firstName and lastName values
      $data = lookupUser($connect, $onidID);
      $user = json_decode($data);

      // check if user exists, if null, create new user
      if($data == null) {
        //create a new user if user does not exists
        $userInfo['onidID'] = $onidID;
        $userInfo['firstName'] = "";
        $userInfo['lastName'] = "";
        $userInfo['email'] = $emails[$i];
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


 ?> 
