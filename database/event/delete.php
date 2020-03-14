
<?php

echo "TESTING I IN DELETE";
//delete.php
require_once '../dbconfig.php';
require_once '../dbquery.php';

if(isset($_POST["id"]))
{
	echo "INSIDE IF\n";
	
	$id = $_POST["id"];
	echo $id;

  $connect = new mysqli($dbhost, $dbuser, $dbpass,$dbname);
  if(!$connect) {
      die('Could not connect: ' . mysqli_error());
    }
   echo 'Connected successfully<br>';
   
   $sql = "DELETE FROM Event WHERE id = '".$id."'";
   //$sql = eventDelete($connect, $id);
   var_dump($sql); 
  	if (mysqli_query($connect, $sql)) {
		echo "Event deleted successfully";
	} else {
		echo "Error deleting event: " . mysqli_error($connect);
	}
}

echo "\nI'm at the end!";

?>