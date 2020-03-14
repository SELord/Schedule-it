<?php  

  $id = $_POST['id'];

  //edit_slot.php
  require_once '../dbconfig.php';

  $connect = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
  if ($connect->connect_errno) {
      echo "Failed to connect to MySQL: (" . $connect->connect_errno . ") " . $connect->connect_error;
  }

 $output = '';  
 $sql = "SELECT * FROM Slot WHERE eventID='".$id."'";  
 $result = mysqli_query($connect, $sql);  
 $output .= '  
      <div class="table-responsive" title="Edit Event Slots">  
      <caption><i>Edit <b><span style="background-color: #FFFFCC">location</span></b> and <b><span style="background-color: #FFFFCC">RSVPlim</span></b></i></caption>
           <table class="table table-bordered" style="width:100%">  
                <tr>  
                     <th>id</th>
                     <th>startTime</th>  
                     <th>duration</th>
                     <th>location</th>  
                     <th>RSVPlim</th>                     
                </tr>';  
 if(mysqli_num_rows($result) > 0)  
 {  
      while($row = mysqli_fetch_array($result))  
      {  
           $output .= '  
                <tr>  
                     <td>'.$row["id"].'</td>  
                     <td>'.$row["startTime"].'</td>   
                     <td>'.$row["duration"].'</td>
                     <td class="location" bgcolor=#FFFFCC data-id3="'.$row["id"].'" contenteditable>'.$row["location"].'</td>    
                     <td class="RSVPlim" bgcolor=#FFFFCC data-id4="'.$row["id"].'" contenteditable>'.$row["RSVPlim"].'</td>  
                </tr>  
           ';  
      }  
 }  
 else  
 {  
      $output .= '<tr>  
                    <td colspan="4">Data not Found</td>  
                  </tr>';  
 }  
 $output .= '</table>  
      </div>';  
 echo $output;  
 ?>