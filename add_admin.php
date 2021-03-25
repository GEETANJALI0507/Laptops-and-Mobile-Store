<?php
  include 'config.php';

  //require 'login.php';
  session_start();
  if(!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true))
  {
    header("location: login.php");
    exit;
  }

  if($_SESSION["user_type"] === "CUSTOMER")
  {
    header("location: index.php");
    exit;
  }

  $sql_for_json = "SELECT MODEL FROM products";
  $sql_json_result = $conn->query($sql_for_json);
  while($row = $sql_json_result->fetch_array())
  {
    $model_data[] = array("name" => $row["MODEL"]);
  }

  $file = "data.json";
  if(file_put_contents($file, json_encode($model_data)))
  {  }//echo("File created");}
  else
  {
    echo("JSON file creation Failed");
  }


  $username = $password = $confirm_password = "";
  $username_err = $password_err = $confirm_password_err = "";

  $admin_insert_err = "";
  if($_SERVER["REQUEST_METHOD"] == "POST")
  {
    if(isset($_POST["search_submit"]) && $_POST["search_submit"] === "search_it")
    {
      $_SESSION["search_sent"] = 1;
      $_SESSION["search_query"] = $_POST["search"];
      header("location: index.php");
    }
    else if( isset($_POST["submit"]) && $_POST["submit"] === "Submit" )
    {
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

      if(empty($username_err) && empty($password_err) && empty($confirm_password_err))
      {
        $sql_admin_insert = "INSERT INTO user (USER_NAME, USER_PASSWORD, USER_TYPE) VALUES (?, ?, 'ADMIN')";
        if($stmt_admin_insert = $conn->prepare($sql_admin_insert))
        {
          $stmt_admin_insert->bind_param("ss", $param_username, $param_password);
          // Set parameters
          $param_username = $username;
          $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
          if($stmt_admin_insert->execute())
          {
            $admin_insert_err = "New Admin inserted successfully.";
          }
          else
          {
            $admin_insert_err = "Execution failed.";
          }
          $stmt_admin_insert->close();
        }
        else
        {
          $admin_insert_err = "Preparation failed.";
        }
      }

    }
  }


?>



<html lang="en">

  <head>
    <link rel="stylesheet" href="index.css" />
    <!-- CSS only -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
    <!-- JavaScript Bundle with Popper -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />-->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
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
        <h2>Add Admin</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="registration">

            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label for="username">Username </label>
                <input type="text" id="username" placeholder="Username" name="username" value="<?php echo $username; ?>" required>
                <span class="help-block" style="color:red;"><?php echo $username_err; ?></span>
              </div>
            <!--<br><br>-->
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label for="password">Password </label>
                <input type="password" id="password" placeholder="Password" name="password" value="<?php echo $password; ?>" required>
                <span class="help-block" style="color:red;" ><?php echo $password_err; ?></span>
              </div>
            <!--<br><br>-->
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label for="confirm_password">Confirm Password </label>
                <input type="password" id="cofirm_password" placeholder="Confirm Password" name="confirm_password" value="<?php echo $confirm_password; ?>" required>
                <span class="help-block" style="color:red;" ><?php echo $confirm_password_err; ?></span>
              </div>
            <!--<br><br>-->
            <div class="form-group">
                  <input id="SB" type="submit" class="btn btn-primary" name="submit" value="Submit">
                  <input id="RB" type="reset" class="btn btn-default" value="Reset">
                  <span class="help-block" style="color: red;" ><?php echo $admin_insert_err; ?></span>
              </div>
            <!--<br><br>-->
          </form>
        </div>

  </body>

</html>


<script>
  $(document).ready(function(){
    $.ajaxSetup({ cache: false });
    $('#search').keyup(function(){
      $('#result').html('');
      $('#state').val('');
      var searchField = $('#search').val();
      var expression = new RegExp(searchField, "i");
      $.getJSON('data.json', function(data) {
        $.each(data, function(key, value){
          if (value.name.search(expression) != -1 )
          {
            $('#result').append('<li class="list-group-item link-class">  '+value.name+'</li>');
          }
        });
      });
    });

  $('#result').on('click', 'li', function() {
  var click_text = $(this).text().split('|');
  $('#search').val($.trim(click_text[0]));
  $("#result").html('');
    });
  });

  window.onload = function(){
            var result = document.getElementById('result');
            document.onclick = function(e){
               if(e.target.id !== 'result' && e.target.id!== 'search'){
                  result.style.display = 'none';
               }
               else {
                 result.style.display = 'block';
               }
            };
         };
</script>
