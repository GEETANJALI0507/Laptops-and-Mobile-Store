<?php
  //include configuration file
  include 'config.php';
  $customer_insert_err = "";
  $username = $password = $confirm_password = $email = $phone = "";
  $username_err = $password_err = $confirm_password_err = $email_err = $phone_err = "";
  $first_name = $lastname = $gender = "";
  $first_name=trim(filter_input(INPUT_POST,'first_name'));
  $last_name=trim(filter_input(INPUT_POST,'last_name'));
  /*$username=filter_input(INPUT_POST,'username');
  $password=filter_input(INPUT_POST,'password');*/
  $gender=filter_input(INPUT_POST,'gender');
  //$email=trim(filter_input(INPUT_POST,'email'));
  //$phone=trim(filter_input(INPUT_POST,'phone'));

  if($_SERVER["REQUEST_METHOD"] == "POST")
  {
      // Validate username
      if(empty(trim($_POST["username"])))
      {
        $username_err = "Please enter a username.";
      }
      else
      {
        // Prepare a select statement
        $sql = "SELECT ID FROM user WHERE USER_NAME = ?";
        if($stmt = $conn->prepare($sql))
        {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_username);

            // Set parameters
            $param_username = trim($_POST["username"]);

            // Attempt to execute the prepared statement
            if($stmt->execute())
            {
                // store result
                $stmt->store_result();

                if($stmt->num_rows == 1)
                {
                    $username_err = "This username is already taken.";
                }
                else
                {
                    $username = trim($_POST["username"]);
                }
            }
            else
            {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
      }

      //Validate email
      if(empty(trim($_POST["email"])))
      {
        $username_err = "Please enter an Email-id.";
      }
      else if(!filter_var(trim($_POST["email"]),FILTER_VALIDATE_EMAIL))
      {
        $email_err= "Email-id is invalid.";
      }
      else
      {
        // Prepare a select statement
        $sql = "SELECT I FROM customer WHERE EMAIL = ?";

        if($stmt = $conn->prepare($sql))
        {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_email);

            // Set parameters
            $param_email = trim($_POST["email"]);

            // Attempt to execute the prepared statement
            if($stmt->execute())
            {
                // store result
                $stmt->store_result();

                if($stmt->num_rows == 1)
                {
                    $email_err = "This Email-id already has an account.";
                }
                else
                {
                    $email = trim($_POST["email"]);
                }
            }
            else
            {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
      }

      // Validate phone number
      if(empty(trim($_POST["phone"])))
      {
        $username_err = "Please enter a Phone number.";
      }
      else
      {
        // Prepare a select statement
        $sql = "SELECT ID FROM customer WHERE MOBILE_NUMBER = ?";
        if($stmt = $conn->prepare($sql))
        {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_phone);

            // Set parameters
            $param_phone = trim($_POST["phone"]);

            // Attempt to execute the prepared statement
            if($stmt->execute())
            {
                // store result
                $stmt->store_result();

                if($stmt->num_rows == 1)
                {
                    $phone_err = "This Phone number already has an account.";
                }
                else
                {
                    $phone = trim($_POST["phone"]);
                }
            }
            else
            {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
      }

      // Validate password
      if(empty(trim($_POST["password"])))
      {
        $password_err = "Please enter a password.";
      }
      elseif(strlen(trim($_POST["password"])) < 6)
      {
        $password_err = "Password must have atleast 6 characters.";
      }
      else
      {
        $password = trim($_POST["password"]);
      }

      // Validate confirm password
      if(empty(trim($_POST["confirm_password"])))
      {
        $confirm_password_err = "Please confirm password.";
      }
      else
      {
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password))
        {
            $confirm_password_err = "Password did not match.";
        }
      }

      //validate emaail

      // Check input errors before inserting in database
      if(empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($email_err) && empty($phone_err) )
      {
        // Prepare an insert statement
        $sql = "INSERT INTO user (USER_NAME, USER_PASSWORD, USER_TYPE) VALUES (?, ?, 'CUSTOMER')";

        if($stmt = $conn->prepare($sql))
        {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("ss", $param_username, $param_password);

            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

            // Attempt to execute the prepared statement
            if($stmt->execute())
            {
              // Redirect to login page
              //header("location: login.php");
              $customer_insert_err = "";
              $sql_user_id = "SELECT ID FROM user WHERE USER_NAME = ?";
              if($stmt_user_id = $conn->prepare($sql_user_id))
              {
                $stmt_user_id->bind_param("s",$param_username);
                $param_username = $username;
                if($stmt_user_id->execute())
                {
                  $stmt_user_id->store_result();
                  $stmt_user_id->bind_result($user_id);
                  if($stmt_user_id->fetch())
                  {

                    $sql_customer = "INSERT INTO customer (FIRST_NAME, LAST_NAME, GENDER, MOBILE_NUMBER, EMAIL, USER_ID) VALUES (?, ?, ?, ?, ?, ?)";

                    if($stmt_customer = $conn->prepare($sql_customer))
                    {
                      $stmt_customer->bind_param("sssssi",$param_first_name, $param_last_name, $param_gender, $param_phone, $param_email, $param_user_id);

                      //Set parameters
                      $param_first_name = $first_name;
                      $param_last_name = $last_name;
                      $param_gender = $gender;
                      $param_phone = $phone;
                      $param_email = $email;
                      $param_user_id = $user_id;
                      echo $user_id;
                      //attempt to execute the prepared statement
                      if($stmt_customer->execute())
                      {
                        // Redirect to login page
                        header("location: login.php");
                      }
                      else
                      {
                        $customer_insert_err = "customer insertion execution failed.";
                      }
                      $stmt_customer->close();
                    }
                    else
                    {
                      $customer_insert_err = "customer insertion preparation failed.";
                    }
                    $stmt_user_id->close();
                  }
                }
                else
                {
                  $customer_insert_err = "user id extraction execution failed.";
                }
              }
              else
              {
                $customer_insert_err = "User id  extraction preparation failed.";
              }
              if(!empty($customer_insert_err))
              {
                $sql_delete = "DELETE FROM user WHERE USER_NAME = ?";
                $stmt_delete = $conn->prepare($sql_delete);
                $stmt_delete->bind_param("s",$username);
                $stmt_delete->execute();
                $stmt_delete->close();
              }
            }
            else
            {
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }

      }
      //Close connection
      $conn->close();
  }
?>

<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Registration</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
  <style type="text/css">
  body { font-family: -apple-system,BlinkMacSystemFont,Segoe UI,Helvetica,Arial,sans-serif,Apple Color Emoji,Segoe UI Emoji; font-size: 14px; }
  .wrapper { margin-left: 500px; margin-top: 100px; width: 400px; padding: 20px; background-color:#e1e4e8; border-radius: 25px; }
  h2 { text-align: center; font-size:45px; }
  input[type="text"], input[type="password"], input[type="email"] { width: 300px; }
  #SB, #RB { opacity: 0.9; }
  </style>
</head>

<body>
  <div class="wrapper">
    <h2>Registration</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="registration">
      <div class="form-group">
        <label for="first_name">First Name </label>
        <input type="text" id="first_name" placeholder="First Name" name="first_name" required>
      </div>
      <!--<br><br>-->
      <div class="form-group">
        <label for="last_name">Last Name </label>
        <input type="text" id="last_name" placeholder="Last Name" name="last_name" required>
      </div>
      <!--<br><br>-->
      <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
        <label for="username">Username </label>
        <input type="text" id="username" placeholder="Username" name="username" value="<?php echo $username; ?>" required>
        <span class="help-block"><?php echo $username_err; ?></span>
      </div>
      <!--<br><br>-->
      <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
        <label for="password">Password </label>
        <input type="password" id="password" placeholder="Password" name="password" value="<?php echo $password; ?>" required>
        <span class="help-block"><?php echo $password_err; ?></span>
      </div>
      <!--<br><br>-->
      <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
        <label for="confirm_password">Confirm Password </label>
        <input type="password" id="cofirm_password" placeholder="Confirm Password" name="confirm_password" value="<?php echo $confirm_password; ?>" required>
        <span class="help-block"><?php echo $confirm_password_err; ?></span>
      </div>
      <!--<br><br>-->
      <div class="form-group">
        <label for="email" >Email ID </label>
        <br>
        <input type="email" id="email" placeholder="Email" name="email" value="<?php echo $email; ?>" required>
        <span class="help-block"><?php echo $email_err; ?></span>
      </div>
      <!--<br><br>-->
      <div class="form-group">
        <label for="phone">Phone Number </label>
        <input type="text" id="phone" placeholder="Phone Number" name="phone" value="<?php echo $phone; ?>" required>
        <span class="help-block"><?php echo $phone_err; ?></span>
      </div>
      <!--<br><br>-->
      <div class="form-group">
        <label for="Gender">Gender :</label>
        <label><input type="radio" id="Gender" name="gender" value="MALE" required>Male</label>
        <label><input type="radio" id="Gender" name="gender" value="FEMALE" required>Female</label>
        <label><input type="radio" id="Gender" name="gender" value="OTHER" required>Other</label>
      </div>
      <!--<br><br>-->
      <div class="form-group">
        <input id="SB" type="submit" class="btn btn-primary" name="submit" value="Submit">
        <input id="RB" type="reset" class="btn btn-default" value="Reset">
        <span class="help-block"><?php echo $customer_insert_err; ?></span>
      </div>
      <!--<br><br>-->
      <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </form>
  </div>
</body>
</html>
