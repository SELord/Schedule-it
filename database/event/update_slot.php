<?php 
    //UPDATE EDIT_SLOT BUTTON GETS SLOT INFORMATION
  //edit_slot.php
  require_once '../dbconfig.php';

    $connect = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    if ($connect->connect_errno) {
        echo "Failed to connect to MySQL: (" . $connect->connect_errno . ") " . $connect->connect_error;
    }

   $id = $_POST["id"];  
   $text = $_POST["text"];
   $parameter = $_POST["parameter"]; 
   
   $sql = "UPDATE Slot SET ".$parameter."='".$text."' WHERE id='".$id."'";  

   if(mysqli_query($connect, $sql))  
   {  
        echo 'Data Updated';  
   }  
   
?>
