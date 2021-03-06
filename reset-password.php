<?php
  // Initialize the session
  session_start();

  // Check if the user is logged in, if not then redirect to login page
  if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true)
  {
      header("location: login.php");
      exit;
  }
  $_SESSION["order_finished"] = 0;

  // Include config file
  require_once "config.php";

  // Define variables and initialize with empty values
  $new_password = $confirm_password = "";
  $new_password_err = $confirm_password_err = "";

  // Processing form data when form is submitted
  if($_SERVER["REQUEST_METHOD"] == "POST")
  {
    // Validate new password
    if(empty(trim($_POST["new_password"])))
    {
        $new_password_err = "Please enter the new password.";
    }
    elseif(strlen(trim($_POST["new_password"])) < 6)
    {
        $new_password_err = "Password must have atleast 6 characters.";
    }
    else
    {
        $new_password = trim($_POST["new_password"]);
    }

    // Validate confirm password
    if(empty(trim($_POST["confirm_password"])))
    {
        $confirm_password_err = "Please confirm the password.";
    }
    else
    {
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($new_password_err) && ($new_password != $confirm_password))
        {
            $confirm_password_err = "Password did not match.";
        }
    }

    // Check input errors before updating the database
    if(empty($new_password_err) && empty($confirm_password_err))
    {
        // Prepare an update statement
        $sql = "UPDATE user SET USER_PASSWORD = ? WHERE ID = ?";

        if($stmt = $conn->prepare($sql))
        {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("si", $param_password, $param_id);

            // Set parameters
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            $param_id = $_SESSION["id"];

            // Attempt to execute the prepared statement
            if($stmt->execute())
            {
                // Password updated successfully. Destroy the session, and redirect to login page
                session_destroy();
                header("location: login.php");
                exit();
            }
            else
            {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }

    // Close connection
    $conn->close();
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="index.css" />
    <!-- CSS only -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">

    <!-- JavaScript Bundle with Popper -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />-->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <style type="text/css">
        body { font-family: -apple-system,BlinkMacSystemFont,Segoe UI,Helvetica,Arial,sans-serif,Apple Color Emoji,Segoe UI Emoji; font-size: 14px; }
        .wrapper { margin-left: 500px; margin-top: 100px; width: 400px; padding: 20px; background-color:#e1e4e8; border-radius: 25px; }
        h2 { text-align: center; font-size:45px; }
        #SB, #RB { opacity: 0.9; }
    </style>
</head>
<body>

  <!--topnav-->
  <div class="top-navbar">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <!-- insert a logo image -->
    <a href="index.php"><img src="https://i.ibb.co/j8GNM6K/Electronikart-dbms-project.png" alt="logo" class="logo" /></a>

    <input type="text" name="search" id="search" placeholder="Search.." required>
    <!-- search icon -->
    <button class="input-group-text" type="submit" name="search_submit" value="search_it"><i class="fa fa-search"></i></button>

    <div class="menu-bar">
        <?php
          if($_SESSION["user_type"] == "CUSTOMER")
            echo '<a href="cart.php"><i class="fa fa-shopping-basket"></i>Cart</a>';
        ?>
        <div class = "dropdown">
          <button class="dropbtn">Account<i class="fa fa-caret-down"></i></button>
          <div class="dropdown-content">
            <!--<a href="#">Account Details</a>-->
            <a href="reset-password.php">Reset-password</a>
            <?php
              if($_SESSION["user_type"] == "CUSTOMER")
                echo '<a href = "manage_address.php">Manage Addresses</a>';
            ?>
            <!--<a href = "manage_address.php">Manage Addresses</a>-->
          </div>
         </div>
         <a href="logout.php">Sign Out</a>
         <?php
          if($_SESSION["user_type"] == "ADMIN")
          {
            echo '<div class = "dropdown">';
            echo '<button class="dropbtn">Admin<i class="fa fa-caret-down"></i></button>';
            echo '<div class="dropdown-content">';
            echo '<a href="add_admin.php">Add Admin</a>';
            echo '<a href="add_product.php">Add Product</a>';
            echo '</div>';
            echo '</div>';
          }
         ?>

       </div>
     </form>
     <ul class="list-group" id="result"></ul>
  </div>

  <!--second navbar-->
  <div class="navbar2">
    <a href="index.php">Home</a>
    <a href="compare.php">Compare</a>
  </div>

    <div class="wrapper">
        <h2>Reset Password</h2>
        <p>Please fill out this form to reset your password.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($new_password_err)) ? 'has-error' : ''; ?>">
                <label for="password">New Password</label>
                <input type="password" id="password" name="new_password" class="form-control" value="<?php echo $new_password; ?>">
                <span class="help-block"><?php echo $new_password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <a class="btn btn-link" href="index.php">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
