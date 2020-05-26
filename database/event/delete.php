
<?php

echo "TESTING I IN DELETE";
//delete.php
require_once '../dbconfig.php';
require_once '../dbquery.php';

if(isset($_POST["id"]))
{
	$id = $_POST["id"];

  $connect = new mysqli($dbhost, $dbuser, $dbpass,$dbname);
  if(!$connect) {
      die('Could not connect: ' . mysqli_error());
    }
   
   $sql = eventDelete($connect, $id);
  	if (mysqli_query($connect, $sql)) {
		echo "Event deleted successfully";
	} else {
		echo "Error deleting event: " . mysqli_error($connect);
	}
}

?>