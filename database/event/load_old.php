
<?php

//load.php
  require_once '../dbconfig.php';

  $connect = new PDO('mysql:host='.$dbhost.';dbname='.$dbname, $dbuser, $dbpass);
  if(!$connect) {
      die('Could not connect: ' . MySQL_error());
    }

$data = array();

$query = "SELECT * FROM Event ORDER BY id";

$statement = $connect->prepare($query);

$statement->execute();

$result = $statement->fetchAll();
//Source: https://stackoverflow.com/questions/5322285/how-do-i-convert-datetime-to-iso-8601-in-php

foreach($result as $row)
{
  $start = date($row["dateStartTime"]);
  $end = date($row["dateEndTime"]);
  $data[] = array(
   'id'   => $row["id"],
   'title'  => $row["title"],
   'description' => $row["description"],
   'start'  => $start,
   'end'  =>   $end,
   'duration' => $row["duration"],
   'RSVPslotLim' => $row["RSVPslotLim"],
 );
}

echo json_encode($data);

?>