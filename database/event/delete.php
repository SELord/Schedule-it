
<?php
// PHP error reporting for debug info. Commented out for production
// For more information: https://stackify.com/display-php-errors/
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

echo "TESTING I IN DELETE";
//delete.php
require_once '../dbconfig.php';
require_once '../dbquery.php';

if(isset($_POST["id"]))
{
	$id = $_POST["id"];

  $connect = new mysqli($dbhost, $dbuser, $dbpass,$dbname, $dbport);
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