<?php
  // Initialize the session
  session_start();
  include 'config.php';

  // Check if the user is logged in, if not then redirect to login page
  if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true)
  {
      header("location: login.php");
      exit;
  }
  $_SESSION["order_finished"] = 0;

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
    echo("JSON file creation Failed");


  $sql_customer_id_extract = "SELECT ID FROM customer WHERE USER_ID = ?";
  $customer_extract_err = "";
  if($stmt =$conn->prepare($sql_customer_id_extract))
  {
    $stmt->bind_param("i",$_SESSION["id"]);
    if($stmt->execute())
    {
      $stmt->store_result();
      $stmt->bind_result( $param_customer_id);
      $stmt->fetch();
      $customer_id = $param_customer_id;
    }
    else
    {
      $customer_extract_err = "Execution failed.";
    }
    $stmt->close();
  }
  else
  {
    $customer_extract_err = "Preparation failed";
  }


  $Address_Line1 = $Address_Line2 = $District = $State = $Landmark = "";
  $Address_Line1_err = $Address_Line2_err = $District_err = $State_err = $Pincode_err = $Landmark_err = "";
  $Pincode = NULL;

  $address_insert_err = "";
  $address_check_err = "";
  $address_remove_err = "";

  $sql_address_count = "SELECT COUNT(*) FROM address WHERE CUSTOMER_ID = ?";
  $stmt_address_count = $conn->prepare($sql_address_count);
  $stmt_address_count->bind_param("i", $customer_id);
  $stmt_address_count->execute();
  $stmt_address_count->store_result();
  $stmt_address_count->bind_result($address_count);
  $stmt_address_count->fetch();
  $stmt_address_count->close();

  if($_SERVER["REQUEST_METHOD"] == "POST")
  {

    if( isset($_POST["address_submit"]) && $_POST["address_submit"] == "Submit" && $address_count<3)
    {

      if(empty(trim($_POST["Address_Line1"])))
        $Address_Line1_err = "Address Line 1 is empty.";
      else
        $Address_Line1 = trim($_POST["Address_Line1"]);

      if(empty(trim($_POST["Address_Line2"])))
        $Address_Line2_err = "Address Line 2 is empty.";
      else
        $Address_Line2 = trim($_POST["Address_Line2"]);

      if(empty(trim($_POST["District"])))
        $District_err = "District is empty.";
      else
        $District = trim($_POST["District"]);

      if(empty(trim($_POST["State"])))
        $State_err = "State is empty.";
      else
        $State = trim($_POST["State"]);

      $Pincode = $_POST["Pincode"];

      if(empty(trim($_POST["Landmark"])))
        $Landmark_err = "Landmark is empty.";
      else
        $Landmark = trim($_POST["Landmark"]);


      if(empty($Address_Line1_err) && empty($Address_Line2_err) && empty($District_err) && empty($State_err) && empty($Pincode_err) && empty($Landmark_err))
      {
        $sql_address_check = "SELECT COUNT(*) FROM address WHERE ADDRESS_LINE1 = ? AND ADDRESS_LINE2 = ? AND DISTRICT = ? AND STATE = ? AND PINCODE = ? AND LANDMARK = ? AND CUSTOMER_ID = ?";
        if($stmt_address_check = $conn->prepare($sql_address_check))
        {
          $stmt_address_check->bind_param("ssssisi", $Address_Line1, $Address_Line2, $District, $State, $Pincode, $Landmark, $customer_id);
          if($stmt_address_check->execute())
          {
            $stmt_address_check->store_result();
            $stmt_address_check->bind_result($param_count);
            $stmt_address_check->fetch();
            if($param_count>0)
              $address_check_err = "This Address already exists in your account.";
          }
          else
          {
            $address_check_err = "Execution failed";
          }
        }
        else
        {
            $address_check_err = "Preparation failed";
        }

        $sql_address_insert = "INSERT INTO address(ADDRESS_LINE1, ADDRESS_LINE2, DISTRICT, STATE, PINCODE, LANDMARK, CUSTOMER_ID) VALUES( ?, ?, ?, ?, ?, ?, ?)";
        if(empty($address_check_err) && $stmt_addresss_insert = $conn->prepare($sql_address_insert))
        {
          $stmt_addresss_insert->bind_param("ssssisi", $Address_Line1, $Address_Line2, $District, $State, $Pincode, $Landmark, $customer_id);
          if($stmt_addresss_insert->execute())
          {
            $address_insert_err = "New Address added into account.";
          }
          else
          {
            $address_insert_err = "Execution failed.";
          }
          $stmt_addresss_insert->close();
        }

      }
    }

    else if( isset($_POST["search_submit"]) && $_POST["search_submit"] === "search_it")
    {
      $_SESSION["search_sent"] = 1;
      $_SESSION["search_query"] = $_POST["search"];
      header("location: index.php");
    }

    else if( isset($_POST["remove_submit"]) )
    {
      $sql_address_delete = "DELETE FROM address WHERE ID = ?";
      if($stmt_address_delete = $conn->prepare($sql_address_delete))
      {
        //echo 'remove id = '.$_POST["remove_submit"];
        $stmt_address_delete->bind_param("i",$_POST["remove_submit"]);
        if($stmt_address_delete->execute())
        {
          $address_remove_err = "Address deleted successfully";
        }
        else
        {
          $address_remove_err = "This Address already has an order and can't be deleted.";
        }
        $stmt_address_delete->close();
      }
      else
      {
        $address_remove_err = "SQL Preparation failed.";
      }
    }

  }

  $sql_addresses_extract = "SELECT ID, ADDRESS_LINE1, ADDRESS_LINE2, DISTRICT, STATE, PINCODE, LANDMARK FROM address WHERE CUSTOMER_ID = ?";
  $address_extract_err = "";
  if($stmt_address_extract = $conn->prepare($sql_addresses_extract))
  {
      $stmt_address_extract->bind_param("i",$customer_id);
      if($stmt_address_extract->execute())
      {
        $stmt_address_extract->store_result();
        $address_count = $stmt_address_extract->num_rows;
        $stmt_address_extract->bind_result( $param_ID, $param_Address_Line1, $param_Address_Line2, $param_District, $param_State, $param_Pincode, $param_Landmark);
      }
      else
      {
        $address_extract_err = "Execution failed.";
      }
  }
  else
  {
    $address_extract_err = "Preparation failed";
  }


?>


<!DOCTYPE html>
<html lang="en" >
<head>
      <meta charset="UTF-8">
      <title>Add Address</title>
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
          .wrapper { margin-left: 500px; margin-top: 30px; width: 400px; padding: 20px; background-color:#e1e4e8; border-radius: 25px; }
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
    <a href = "laptops.php">Laptops</a>
     <a href = "mobiles,php">Mobiles</a>
    <a href="compare.php">Compare</a>
  </div>

  <div class="addresses-list" style="padding: 20px; margin-left: 400px;">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
      <h3>Address List</h3>
      <?php
        while($stmt_address_extract->fetch())
        {
          echo '<div style="padding: 15px;  background-color:#e1e4e8; border-radius: 25px; width: 600px">';
          echo '<address>';
          echo $param_Address_Line1.'<br>';
          echo $param_Address_Line2.'<br>';
          echo $param_District.'<br>';
          echo $param_State.' - ';
          echo $param_Pincode.'<br>';
          echo $param_Landmark.'<br>';
          echo '<button type="submit" class="btn btn-default" style="opacity: 0.9;" name="remove_submit" value="'.$param_ID.'">Remove</button><br>';
          echo '</address>';

          echo '</div>';
        }
        if($address_count == 0)
        {
          echo '<span class="help-block" style="color: red;" >Address list is empty</span>';
        }
        echo '<span class="help-block" style="color: red;" >'.$address_remove_err.'</span>';
      ?>

    </form>
  </div>


  <div class="wrapper">
      <h3>Add Address</h3>
      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="address">

          <div class="form-group">
              <label for="Address_Line1">Address_Line1</label>
              <input type="text" id="Address_Line1" placeholder="Enter your Address" name="Address_Line1" value="<?php echo $Address_Line1; ?>" required>
              <span class="help-block"><?php echo $Address_Line1_err; ?></span>
          </div>
          <!--<br><br>-->
          <div class="form-group">
              <label for="Address_Line2">Address_Line2</label>
              <input type="text" id="Address_Line2" placeholder="Enter your Address" name="Address_Line2" value="<?php echo $Address_Line2; ?>" required>
              <span class="help-block"><?php echo $Address_Line2_err; ?></span>
          </div>
          <!--<br><br>-->
          <div class="form-group">
              <label for="District">District</label>
              <input type="text" id="District" placeholder="District name" name="District" value="<?php echo $District; ?>" required>
              <span class="help-block"><?php echo $District_err; ?></span>
          </div>
          <!--<br><br>-->
          <div class="form-group">
              <label for="State">State</label>
              <input type="text" id="State" placeholder="State name" name="State" value="<?php echo $State; ?>" required>
                <span class="help-block"><?php echo $State_err; ?></span>
          </div>
          <!--<br><br>-->
          <div class="form-group">
              <label for="Pincode">Pincode</label>
              <input type="number" id="Pincode" placeholder="Pincode" name="Pincode" min="110000" max="999999" value="<?php echo $Pincode; ?>" required>
              <span class="help-block"><?php echo $Pincode_err; ?></span>
            </div>
          <!--<br><br>-->
          <div class="form-group">
              <label for="Landmark">Landmark</label>
              <input type="text" id="Landmark" placeholder="Any Landmark near your place" name="Landmark" value="<?php echo $Landmark; ?>" required>
              <span class="help-block"><?php echo $Landmark_err; ?></span>
          </div>
          <!--<br><br>-->
          <div class="form-group">
                <input id="SB" type="submit" class="btn btn-primary" name="address_submit" value="Submit">
                <input id="RB" type="reset" class="btn btn-default" value="Reset">
                <span class="help-block"><?php if($address_count>=3) echo "You cannot add anymomre addresses for this account. Max limit is 3."; ?></span>
                <span class="help-block"><?php  echo $address_check_err; ?></span>
                <span class="help-block"><?php echo $address_insert_err; ?></span>
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
