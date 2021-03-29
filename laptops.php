<?php
  include 'config.php';

  //require 'login.php';
  session_start();
  if(!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true))
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

  //echo "\nSession[id] = ".$_SESSION["id"]."\nSession[username] = ".$_SESSION["username"]."\nSession[user_type] = ".$_SESSION["user_type"];

  $price_max = $price_min = NULL;


  $sql_manufacturer = "SELECT DISTINCT MANUFACTURER FROM products WHERE PRODUCT_TYPE = 'LAPTOP' ";
  $sql_manufacturer_err = "";
  if($stmt_manufacturer = $conn->prepare($sql_manufacturer))
  {
    if($stmt_manufacturer->execute())
    {
      $stmt_manufacturer->store_result();
      $stmt_manufacturer->bind_result( $res_manufacturer);
    }
    else
    {
      $sql_manufacturer_err = "Execution failed.";
    }
  }
  else
  {
    $sql_manufacturer_err = "Prepare failed.";
  }

  $sql_RAM = "SELECT DISTINCT RAM_SIZE FROM products WHERE PRODUCT_TYPE = 'LAPTOP'";
  $sql_RAM_err = "";
  if($stmt_RAM = $conn->prepare($sql_RAM))
  {
    if($stmt_RAM->execute())
    {
      $stmt_RAM->store_result();
      $stmt_RAM->bind_result( $res_RAM);
    }
    else
    {
      $sql_RAM_err = "Execution failed.";
    }
  }
  else
  {
    $sql_RAM_err = "Prepare failed.";
  }

  $sql_HDD = "SELECT DISTINCT HDD_SIZE FROM products  WHERE PRODUCT_TYPE = 'LAPTOP'";
  $sql_HDD_err = "";
  if($stmt_HDD = $conn->prepare($sql_HDD))
  {
    if($stmt_HDD->execute())
    {
      $stmt_HDD->store_result();
      $stmt_HDD->bind_result( $res_HDD);
    }
    else
    {
      $sql_HDD_err = "Execution failed.";
    }
  }
  else
  {
    $sql_HDD_err = "Prepare failed.";
  }

  $sql_OS = "SELECT DISTINCT OS FROM products WHERE PRODUCT_TYPE = 'LAPTOP'";
  $sql_OS_err = "";
  if($stmt_OS = $conn->prepare($sql_OS))
  {
    if($stmt_OS->execute())
    {
      $stmt_OS->store_result();
      $stmt_OS->bind_result( $res_OS);
    }
    else
    {
      $sql_OS_err = "Execution failed.";
    }
  }
  else
  {
    $sql_OS_err = "Prepare failed.";
  }

  //echo "<br><br>";
  $price_err = $manufacturer_err = $RAM_err = $HDD_err = "";
  if($_SERVER["REQUEST_METHOD"] == "POST")
  {
    //print_r($_POST);
    if(isset($_POST["filter_submit"]) && $_POST["filter_submit"] == "Submit")
    {

      if($_POST["price_min"] <= $_POST["price_max"])
      {
        $price_min = $_POST["price_min"];
        $price_max = $_POST["price_max"];
      }
      else
      {
        $price_err = "error:minimmum price is greater than maximim price";
      }     

      if(isset($_POST["manufacturer_choosen"]))
        $manufacturer_select = $_POST["manufacturer_choosen"];
      else if(!isset($manufacturer_select))
      {
        $manufacturer_err = "select atleast one brand";
        $manufacturer_select = array();
      }

      if(isset($_POST["RAM_choosen"]))
        $RAM_select = $_POST["RAM_choosen"];
      else if(!isset($RAM_select))
      {
        $RAM_err = "select atleast one RAM size";
        $RAM_select = array();
      }

      if(isset($_POST["HDD_choosen"]))
        $HDD_select = $_POST["HDD_choosen"];
      else if(!isset($HDD_select))
      {
        $HDD_err = "select atleast one storage size";
        $HDD_select = array();
      }

    }

    if(isset($_POST["product_submit"]) && $_POST["product_submit"] == "View Details")
    {
      $_SESSION["product_view_id"] = $_POST["product_view"];
     //echo $_SESSION["product_view_id"];
      header("location: view.php");
      exit;
    }

  }

  $sql_product_extract = "SELECT ID, MODEL, MRP_PRICE, DISCOUNT, IMAGE FROM products WHERE PRODUCT_TYPE = 'LAPTOP'";
  if((isset($_POST["filter_submit"]) && $_POST["filter_submit"] == "Submit") && empty($price_err) && empty($manufacturer_err)  && empty($RAM_err) && empty($HDD_err) )
  {
     
      $sql_product_extract = $sql_product_extract."and ( MRP_PRICE>=".$price_min." and MRP_PRICE<=".$price_max." )";
      $sql_product_extract = $sql_product_extract." and ( ";
      foreach ($manufacturer_select as $key => $value)
      {
        $sql_product_extract = $sql_product_extract."MANUFACTURER=\"".$value."\" or ";
      }
      $sql_product_extract = $sql_product_extract."0 )";
      //adding RAM filter to sql command
      $sql_product_extract = $sql_product_extract." and ( ";
      foreach ($RAM_select as $key => $value)
      {
        $sql_product_extract = $sql_product_extract."RAM_SIZE=".$value." or ";
      }
      $sql_product_extract = $sql_product_extract."0 )";
      //adding HDD filter to sql command
      $sql_product_extract = $sql_product_extract." and ( ";
      foreach ($HDD_select as $key => $value)
      {
        $sql_product_extract = $sql_product_extract."HDD_SIZE=".$value." or ";
      }
      $sql_product_extract = $sql_product_extract."0 )";
  }

  else if(isset($_POST["search_submit"]) && $_POST["search_submit"] == "search_it")
  {
    $sql_product_extract = $sql_product_extract." and MODEL LIKE '%".trim($_POST["search"])."%'";
  }
  else if(isset($_SESSION["search_sent"]) && $_SESSION["search_sent"] === 1)
  {
    $_SESSION["search_sent"] = 0;
    $sql_product_extract = $sql_product_extract." WHERE MODEL LIKE '%".trim($_SESSION["search_query"])."%'";
  }
  //echo $sql_product_extract;

  $product_display_count = 0;

  $sql_product_extract_err = "";
  if($stmt_product_extract = $conn->prepare($sql_product_extract))
  {
    if($stmt_product_extract->execute())
    {
      $stmt_product_extract->store_result();
      $stmt_product_extract->bind_result( $product_id, $model_name, $MRP, $discount, $image);
    }
    else
    {
      $sql_product_extract_err = "Execution failed.";
    }
  }
  else
  {
    $sql_product_extract_err = "Prepare failed.";
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
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

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
      <a href="mobiles.php">Mobiles</a>
      <a href="compare.php">Compare</a>
    </div>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
      <!-- sidebar for filters -->
      <div id="mySidenav" class="sidenav">

        <div>
          <h3>Price</h3>
          <label>Minimum :<input type="numeric" name="price_min" value="<?php echo $price_min; ?>" style="width: 100px;" required>
            <br>Maximum :<input type = "numeric" name="price_max" value="<?php echo $price_max; ?>" style="width: 100px;" required></label>
            <span class="help-block" style="display: block; margin-top: 5px; margin-bottom: 10px; color: red; font-family: -apple-system,BlinkMacSystemFont,Segoe UI,Helvetica,Arial,sans-serif,Apple Color Emoji,Segoe UI Emoji; font-size: 14px;"><?php echo $price_err; ?></span>
        </div>

        <br />

        <br /> 
        <div>
          <h3>Brand</h3>
          <?php
            while($stmt_manufacturer->fetch())
            {
              echo "<label><input type=\"checkbox\" name=\"manufacturer_choosen[".$res_manufacturer."]\" value=\"".$res_manufacturer."\"";
              if((isset($manufacturer_select) && array_search($res_manufacturer, $manufacturer_select)) || !(isset($_POST["filter_submit"]) && $_POST["filter_submit"] == "Submit") )
                echo "checked";
              echo ">".$res_manufacturer."</label>\n";
              }
            ?>
            <span class="help-block" style="display: block; margin-top: 5px; margin-bottom: 10px; color: red; font-family: -apple-system,BlinkMacSystemFont,Segoe UI,Helvetica,Arial,sans-serif,Apple Color Emoji,Segoe UI Emoji; font-size: 14px;"><?php echo $manufacturer_err; ?></span>
          </div>

        <br />
        <div class="ram">
          <h3>Ram</h3>
          <?php
            while($stmt_RAM->fetch())
            {
              echo "<label><input type=\"checkbox\" name=\"RAM_choosen[".$res_RAM."]\" value=\"".$res_RAM."\"";
              if((isset($RAM_select) && array_search($res_RAM, $RAM_select)) || !(isset($_POST["filter_submit"]) && $_POST["filter_submit"] == "Submit") )
                echo "checked";
              echo ">".$res_RAM."</label>\n";
            }
          ?>
          <span class="help-block" style="display: block; margin-top: 5px; margin-bottom: 10px; color: red; font-family: -apple-system,BlinkMacSystemFont,Segoe UI,Helvetica,Arial,sans-serif,Apple Color Emoji,Segoe UI Emoji; font-size: 14px;"><?php echo $RAM_err; ?></span>
        </div>

        <br />
        <div class="storage">
          <h3>Storage</h3>
          <?php
            while($stmt_HDD->fetch())
            {
              echo "<label><input type=\"checkbox\" name=\"HDD_choosen[".$res_HDD."]\" value=\"".$res_HDD."\"";
              if((isset($HDD_select) && array_search($res_HDD, $HDD_select)) || !(isset($_POST["filter_submit"]) && $_POST["filter_submit"] == "Submit") )
                echo "checked";
              echo ">".$res_HDD."</label>\n";
            }
          ?>
          <span class="help-block" style="display: block; margin-top: 5px; margin-bottom: 10px; color: red; font-family: -apple-system,BlinkMacSystemFont,Segoe UI,Helvetica,Arial,sans-serif,Apple Color Emoji,Segoe UI Emoji; font-size: 14px;"><?php echo $HDD_err; ?></span>
          <label><input type="submit" name="filter_submit" value"Filter"></label>
        </div>
      </div>
    </form>


    <div class="main">

      <?php
        while($stmt_product_extract->fetch())
        {
          echo "<form action=\"".htmlspecialchars($_SERVER['PHP_SELF'])."\" method=\"post\">";
          echo "<div class=\"card\">";
          echo "<h2>".$model_name."</h2>";
          echo "<img src=\"".$image."\" alt=\"1-redmi-red\" border=\"0\" style=\"width: 100%\" />";
          echo "<p class=\"price\">Price:₹".$MRP."</p>";
          echo "<p>Discount : ".$discount."%</p>";
          echo "<input type=\"hidden\" name=\"product_view\" value=".$product_id.">";
          echo "<p><input type=\"submit\" name=\"product_submit\" value=\"View Details\"><p>";
          echo "</div>";
          echo "</form>";
          $product_display_count+=1;
        }
        if($product_display_count == 0)
        {
          echo '<div> No item to display according to inputed filter or search</div>';
        }
      ?>

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
