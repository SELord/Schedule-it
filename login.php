<?php
    include 'file_path.php';
 //phpinfo();
// Set up some variables for CAS
$casService = 'https://login.oregonstate.edu/idp/profile/cas';
$thisService = 'http://web.engr.oregonstate.edu/~' . $DEV_ONID . '/Schedule-it/login.php';

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
if ($_SERVER["REQUEST_METHOD"] == "GET" && $_GET["ticket"]) {
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
      $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
      if ($mysqli->connect_errno) {
        die("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error . "\n");
      } else {
        //Find userID based off of onid
         $data = lookupUser($mysqli, $_SESSION["onidID"]);
         //var_dump($data);
        // check if user exists, if null, create new user
        if($data == null) {
          // This should call newUser($conn, $info) but had problems inserting in database 
          $stmt = $mysqli->prepare("INSERT INTO User (onidUID, firstName, lastName, email) VALUES (?, ?, ?, ?)");
          $stmt->bind_param("ssss", $_SESSION["onidID"], $_SESSION["firstName"], $_SESSION["lastName"], $_SESSION["email"]);
          $stmt->execute();
        } else {
          $user = json_decode($data);

          //If invite is sent, but firstName/LastName is NULL 
          if($user->firstName == null || $user->lastName == null) {
            $newUser_stmt = $mysqli->prepare("UPDATE User 
                SET firstName = ?, lastName = ?
                WHERE onidUID = ?");
            $newUser_stmt->bind_param("sss", $_SESSION['firstName'], $_SESSION['lastName'], $_SESSION['onidID']);
            $newUser_stmt->execute();
          }
        }
        header("Location: " . $FILE_PATH . "homepage.php");
      }
   }
} else {
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
 
/*
* Returns the UID from the passed in response, or it
* returns false if there is no UID.

function uid($response) {
   // Turn the response into an array
   $responseArray = preg_split("/\n/", $response);
   // Get the line that has the cas:user tag
   $casUserArray = preg_grep("/<\/cas:user>/", $responseArray);
   $lastnameArray = preg_grep("/<\/cas:lastname>/", $responseArray);
   $firstnameArray = preg_grep("/<\/cas:firstname>/", $responseArray);
   $emailArray = preg_grep("/<\/cas:email>/", $responseArray);  
   //var_dump($casUserArray);
   //var_dump($lastnameArray);
   //var_dump($firstnameArray);
   //var_dump($emailArray);

   if (is_array($casUserArray)) {
         return $uid;
      }
   }
   return false;
}
 */
/*
<?xml version="1.0" encoding="UTF-8"?>
<cas:serviceResponse xmlns:cas="http://www.yale.edu/tp/cas">
  <cas:authenticationSuccess>
    <cas:user>ohsa</cas:user>
      <cas:attributes>
        <cas:commonName>Alasagas, Elaine</cas:commonName>
        <cas:firstname>Elaine</cas:firstname>
        <cas:osuprimarymail>ohsa@oregonstate.edu</cas:osuprimarymail>
        <cas:eduPersonAffiliation>student</cas:eduPersonAffiliation>
        <cas:eduPersonAffiliation>member</cas:eduPersonAffiliation>
        <cas:osupidm>3828675</cas:osupidm>
        <cas:givenName>Elaine</cas:givenName>
        <cas:osuuid>44979764121</cas:osuuid>
        <cas:lastname>Alasagas</cas:lastname>
        <cas:uid>ohsa</cas:uid>
        <cas:eduPersonPrimaryAffiliation>student</cas:eduPersonPrimaryAffiliation>
        <cas:UDC_IDENTIFIER>D8EAB9A5475297ED61DDDAD3422561E4</cas:UDC_IDENTIFIER>
        <cas:surname>Alasagas</cas:surname>
        <cas:eduPersonPrincipalName>ohsa@oregonstate.edu</cas:eduPersonPrincipalName>
        <cas:fullname>Alasagas, Elaine</cas:fullname>
        <cas:email>ohsa@oregonstate.edu</cas:email>
      </cas:attributes>
  </cas:authenticationSuccess>
</cas:serviceResponse>
*/
?>
<!--
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <title>Schedule-it</title>
  
  --Bootstrap core CSS
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
  --Customized css--
  <link rel="stylesheet" href="./assets/css/main.css" type="text/css">

</head>
<body>
  <div id="frm" style="text-align: center; vertical-align: middle; margin-top: 200px">
    <h1>Schedule-it</h1>
    <p>
       <a href="https://login.oregonstate.edu/idp/profile/cas/login?service=http://people.oregonstate.edu/~ohsa/schedule-it.php" role="button">
      <button type="button" class="btn btn-warning btn-primary btn-lg ">Login</a></button>
    </p>
  </div>
</body>
</html> 
-->