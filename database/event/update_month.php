<?php

//UPDATE EVENT ON CALENDAR VIEW

require_once '../dbconfig.php';
require_once '../dbquery.php';

if(!empty($_POST))
{
  /*
  //echo "update_month.php called";
  $id = $_POST['id'];
  $title = $_POST['title'];
  $description = $_POST['description'];
  $date = $_POST['date'];
  $start = $_POST['start'];
  $duration = $_POST['duration'];
  $RSVPslotLim = $_POST['RSVPslotLim'];
  //var_dump($_POST);
*/

$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if (!$mysqli) {
    echo "Error: unable to connect to MySQL: Errorno - " . mysqli_connect_errno() . PHP_EOL;
    exit; 
} else {
    echo "Connected to database - success";
}

/*  
    date_default_timezone_set('UTC');  // optional
    //echo "date: " . $date . "\n";
    //echo "dateStartTime: " . $start . "\n";
    $get_start = strtotime($date . ' ' . $start);

    list($hrs, $min) = explode(':',$duration);
    $min += $hrs * 60;  
    //echo "min: " . $min;

    //Calculate new duration based off of #of slots
    $numSlots = $connect->query("SELECT count(*) AS slots FROM Slot WHERE eventID='".$id."'");

    $getSlot = $numSlots->fetchColumn();
    //echo "\nNumber of slots: " . $getSlot;

    //Use a different GET for slot
    $time_toconvert = ($min / $getSlot);

    $intervals = date("H:i",mktime(0,$time_toconvert,0,0,0,0));

    $get_end =  strtotime(('+' . $min . 'minutes'), $get_start);
    //echo $time_toconvert . "\n";

    //Convert date/time back to YYYY-MM-DD HH:MM
    $newStart = date('Y-m-d H:i:s', $get_start);
    $newEnd = date('Y-m-d H:i:s', $get_end);
    //echo "Start: " . $newStart;
    //echo "\nEnd: " . $newEnd;

  //Update event query 
  $query = "UPDATE Event SET title ='".$title."', description ='".$description."', dateStartTime ='".$newStart."', dateEndTime ='".$newEnd."', duration = '".$duration."', RSVPslotLim = '".$RSVPslotLim."' WHERE id='".$id."'";

  //echo $query;
  //Submit and execute to the database
  $statement = $connect->prepare($query);
  $statement->execute();
*/
eventUpdate($mysqli, $_POST);

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

//Iinitialize add_time to the duration minutues (need this to mulitply by # of slots to get total min time)
$addTime = $time_toconvert;
//echo $addTime;

//var_dump(count($arraySlot));
$i = 0;

//Go trough each slot and update duration and start-time
foreach($arraySlot as $slot) {
  
  //echo "\nindex: " . $i;
  //add n minutes for a new start time based off of each slot 
  $min_duration = $addTime *  $i;
  //echo $min_duration;
  $intTime = date('H:i:s', strtotime(('+' . $min_duration . 'minutes'), $get_start));

  //echo $intTime;
  $slot->startTime = $intTime;
  $slot->duration = $intervals;
  $i++;
}

//Update the databse in for-loop
for($i = 0; $i < count($arraySlot); $i++) {
  $query1 = $connect->query("UPDATE Slot SET duration = '".$arraySlot[$i]->duration."', startTime = '".$arraySlot[$i]->startTime."' WHERE id='".$arraySlot[$i]->id."'");
}


//var_dump($query1);
//echo var_dump($arraySlot);

}

?>