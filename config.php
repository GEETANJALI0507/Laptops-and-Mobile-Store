<?php
  $host="localhost";
  $user="root";
  $password="";
  $dbname="laptops_and_mobile_store";

  $conn = new mysqli($host, $user, $password, $dbname);
  if($conn === false)
  {
      die("ERROR: Could not connect. " . $conn->connect_error);
  }

?>
