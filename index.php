<?php
  //require 'login.php';
  session_start();
  echo "\nSession[id] = ".$_SESSION["id"]."\nSession[username] = ".$_SESSION["username"]."\nSession[user_type] = ".$_SESSION["user_type"];
?>

<!DOCTYPE html>
<html lang="en">
  <head>
  </head>
  <body>
    <div class="page-header">
        <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to our site.</h1>
    </div>
    <p>
        <a href="reset-password.php" class="btn btn-warning">Reset Your Password</a>
        <a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a>
    </p>
  </body>
</html>
