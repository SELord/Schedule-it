<?php
// PHP error reporting for debug info. Commented out for production
// For more information: https://stackify.com/display-php-errors/
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

//UPDATE EVENT ON EVENT-RESIZE WEEKLY VIEW AND ON MONTH VIEW
require_once '../dbconfig.php';
require_once '../dbquery.php';

if(isset($_POST["id"]))
{
  //$dateStart = $_POST['dateStart'];
  //$dateEnd = $_POST['dateEnd'];
  //$id = $_POST['id'];

  $connect = new PDO('mysql:host='.$dbhost.';dbname='.$dbname, $dbuser, $dbpass);
  if(!$connect) {
      die('Could not connect: ' . MySQL_error());
    }
    var_dump($connect);

    eventUpdate($connect, $_POST);

/*
  //Convert times to epoch time
  $epochStart = date("U", strtotime($start));
  $epochEnd = date("U", strtotime($end));

  //Then convert to datetime format in mysql
  $dtStart = new DateTime("@$epochStart");
  $newStart = $dtStart->format('Y-m-d H:i:s');
  //echo "New Start:\n " . $newStart;

  $dtEnd = new DateTime("@$epochEnd");
  $newEnd = $dtEnd->format('Y-m-d H:i:s');

  //get duration
  $datetime1 = new DateTime($newStart);
  $datetime2 = new DateTime($newEnd);
  $interval = $datetime1->diff($datetime2);
  $elapsed = $interval->format('%H:%i');
  //echo "New Duration: " . $elapsed;

 $query = "UPDATE Event 
 SET dateStartTime ='".$newStart."', dateEndTime ='".$newEnd."', duration = '".$elapsed."' 
 WHERE id='".$id."'";

//echo $query;
$statement = $connect->prepare($query);
$statement->execute();




//Calculate new duration based off of #of slots
$numSlots = $connect->query("SELECT count(*) AS slots FROM Slot WHERE eventID='".$id."'");

$getSlot = $numSlots->fetchColumn();
//echo "\nNumber of slots: " . $getSlot;


class Slots {
  public $id;
  public $startTime;
  public $duration;
}

$arraySlot = (array) null;

$slotQuery = $connect->query("SELECT id, startTime, duration FROM Slot WHERE eventID='".$id."'");

foreach($slotQuery as $row) {
  $curSlot = new Slots();
  $curSlot->id = $row["id"];
  $curSlot->startTime = $row["startTime"];
  $curSlot->duration = $row["duration"];

  array_push($arraySlot, $curSlot);
}

//var_dump($arraySlot);

//Recalculate duration 
list($hrs, $min) = explode(':',$elapsed);
$min += $hrs * 60;  

//RSVP slot limit is the number of slots a user can reserve 
//Use a different GET for slot
$min_duration = ($min / $getSlot);

$intervals = date("H:i",mktime(0,$min_duration,0,0,0,0));
//echo "\nNew duration: " . $intervals;
*/
/**UPDATE EACH SLOT 
//$updateSlot = "UPDATE Slot SET duration = '".$intervals."', WHERE id='".$id."'";
$get_start = strtotime($newStart);

$addTime = $min_duration;
//Insert number of slots for each meeting id 
var_dump(count($arraySlot));
$i = 0;

foreach($arraySlot as $slot) {
 //add 15 minutes for each start time
  //echo "\nindex: " . $i;
  $min_duration = $addTime *  $i;
  //echo "min_duration: " . $min_duration;
  $intTime = date('H:i:s', strtotime(('+' . $min_duration . 'minutes'), $get_start));

  $slot->startTime = $intTime;
  $slot->duration = $intervals;
  $i++;
}

for($i = 0; $i < count($arraySlot); $i++) {
  $query1 = $connect->query("UPDATE Slot SET duration = '".$arraySlot[$i]->duration."', startTime = '".$arraySlot[$i]->startTime."' WHERE id='".$arraySlot[$i]->id."'");
}


//var_dump($query1);
//echo var_dump($arraySlot);
**/
}

?>