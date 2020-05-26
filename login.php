<?php
include 'file_path.php';

// Get returnPage variable
if(isset($_GET['returnPage']) && $_GET['returnPage'] !== ''){
    $thisService = $FILE_PATH . 'login.php?returnPage=' . $_GET['returnPage'];
} else {
    $thisService = $FILE_PATH . 'login.php';
}

// Set up some variables for CAS
$casService = 'https://login.oregonstate.edu/idp/profile/cas';


//TODO: Retrieve the upcoming events and meetings, and reserved meetings 
//of the user to populate the calendar
require_once './database/dbconfig.php';
require_once './database/dbquery.php';

/*
* Check to see if there is a ticket in the GET request.
* CAS uses "ticket" for the service ticket. Bad choice of words, but
* it is what CAS uses.
*
* If the ticket exists, validate it with CAS. If not, redirect the user
* to CAS.
*
* Of course, you will want to hook this in with your application's
* session management system, i.e., if the user already has a session,
* you don't want to do either of these two things.
*
*/
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["ticket"])) {
   if ($response = responseForTicket($_GET["ticket"])) {

      $responseArray = preg_split("/\n/", $response);
      // Get the line that has the cas:user tag

      //Getting USER info
      $casUserArray = preg_grep("/<\/cas:user>/", $responseArray);
      $userAr = preg_split("/>/", $casUserArray[3]);
      $user = preg_split("/</",  $userAr[1]);

      //Getting LASTNAME info
      $lastnameArray = preg_grep("/<\/cas:lastname>/", $responseArray);
      $lastnameAr = preg_split("/>/", $lastnameArray[13]);
      $lastname = preg_split("/</",  $lastnameAr[1]);

      //Getting FIRSTNAME info
      $firstnameArray = preg_grep("/<\/cas:firstname>/", $responseArray);
      $firstnameAr = preg_split("/>/", $firstnameArray[6]);
      $firstname = preg_split("/</",  $firstnameAr[1]);

      //Getting EMAIL info
      $emailArray = preg_grep("/<\/cas:email>/", $responseArray);         
      $emailAr = preg_split("/>/", $emailArray[20]);
      $email = preg_split("/</",  $emailAr[1]);
      
      session_start();
      $_SESSION["loggedin"]=TRUE;
      $_SESSION["onidID"]=$user[0];
      $_SESSION["lastName"]=$lastname[0];
      $_SESSION["firstName"]=$firstname[0];
      $_SESSION["email"]=$email[0];

        //var_dump($_SESSION);

      // connect to database and check if user exists, else create new user 
      $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname, $dbport);
      if ($mysqli->connect_errno) {
        die("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error . "\n");
      } else {
        //Find userID based off of onid
         $data = lookupUser($mysqli, $_SESSION["onidID"]);

         // check if user exists, if null, create new user
        if($data == null) {
          $userID = newUser($mysqli, $_SESSION);
        } else {
          $user = json_decode($data);

          //If invite is sent, but firstName/LastName/email is NULL 
          if($user->firstName == null || $user->lastName == null || $user->email == null) {
            userUpdate($mysqli, $user->id, $_SESSION);
          }
        }
        // Redirect to the returnPage if the query string was used, else just go to the homepage
        if(isset($_GET['returnPage']) && $_GET['returnPage'] !== ''){
            header("Location: " . $FILE_PATH . $_GET['returnPage']);
        }
        else{
            header("Location: " . $FILE_PATH . "homepage.php");
        }
      }
   }
   else{
       echo "Authentication Not Validated!";
   }
}
else{
   header("Location: $casService/login?service=$thisService");
}
 
 
/*
* Returns the CAS response if the ticket is valid, and false if not.
*/
function responseForTicket($ticket) {
   global $casService, $thisService;
 
   $casGet = "$casService/serviceValidate?ticket=$ticket&service=" . urlencode($thisService);
 
   // See the PHP docs for warnings about using this method:
   // http://us3.php.net/manual/en/function.file-get-contents.php
   $response = file_get_contents($casGet);
   //print_r($response);
 
   if (preg_match('/cas:authenticationSuccess/', $response)) {
      return $response;
   }
   else {
      return false;
   }
}

?> 
