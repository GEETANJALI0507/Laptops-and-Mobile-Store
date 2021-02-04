<?php
include 'connection.php';
if(isset($conn))
{
  echo "connection is set.\n";
}
$name=filter_input(INPUT_POST,'name');
$username=filter_input(INPUT_POST,'username');
$password=filter_input(INPUT_POST,'password');
$gender=filter_input(INPUT_POST,'gender');
$email=filter_input(INPUT_POST,'email');
$phone=filter_input(INPUT_POST,'phone');

// Check connection
if ($conn->connect_error)
{
  die("Connection failed: " . mysqli_connect_error());
}
$stmt=$conn->prepare("insert into users(name,username,password,gender,email,phone) values(?,?,?,?,?,?)");
$stmt->bind_param("sssssi",$name,$username,$password,$gender,$email,$phone);
$stmt->execute();
echo "Registration Successful....!";
$stmt->close();
?>
