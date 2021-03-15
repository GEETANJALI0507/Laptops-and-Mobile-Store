<?php
  include 'config.php';

  //require 'login.php';
  session_start();
  if(!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true))
  {
    header("location: login.php");
    exit;
  }


  //echo "\nSession[id] = ".$_SESSION["id"]."\nSession[username] = ".$_SESSION["username"]."\nSession[user_type] = ".$_SESSION["user_type"];

  $price_max = $price_min = NULL;


  $sql_product_type = "SELECT DISTINCT PRODUCT_TYPE FROM products";
  $sql_product_type_err = "";
  if($stmt_product_type = $conn->prepare($sql_product_type))
  {
    if($stmt_product_type->execute())
    {
      $stmt_product_type->store_result();
      $stmt_product_type->bind_result( $res_product_type);
    }
    else
    {
      $sql_product_type_err = "Execution failed.";
    }
  }
  else
  {
    $sql_product_type_err = "Prepare failed.";
  }

  $sql_manufacturer = "SELECT DISTINCT MANUFACTURER FROM products";
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

  $sql_RAM = "SELECT DISTINCT RAM_SIZE FROM products";
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

  $sql_HDD = "SELECT DISTINCT HDD_SIZE FROM products";
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

  $sql_OS = "SELECT DISTINCT OS FROM products";
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
  $price_err = $product_type_err = $manufacturer_err = $RAM_err = $HDD_err = "";
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

      if(isset($_POST["product_type_choosen"]))
        $product_type_select = $_POST["product_type_choosen"];
      else if(!isset($product_type_select))
      {
        $product_type_err = "select atleast one product type";
        $product_type_select = array();
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

      //if(isset($_POST["price_min"]) && isset($_POST["price_max"]))
      /*echo "Minimum Price = ".$price_min."   Maximum Price = ".$price_max."<br><br>";


      foreach($product_type_select as $x => $x_value)
      {
        echo "Key=" . $x . ", Value=" . $x_value;
        echo "<br>";
      }
      echo "<br>";
      foreach($manufacturer_select as $x => $x_value)
      {
        echo "Key=" . $x . ", Value=" . $x_value;
        echo "<br>";
      }
      echo "<br>";
      foreach($RAM_select as $x => $x_value)
      {
        echo "Key=" . $x . ", Value=" . $x_value;
        echo "<br>";
      }
      echo "<br>";
      foreach($HDD_select as $x => $x_value)
      {
        echo "Key=" . $x . ", Value=" . $x_value;
        echo "<br>";
      }*/


      /*foreach($OS_select as $x => $x_value)
      {
        echo "Key=" . $x . ", Value=" . $x_value;
        echo "<br>";
      }*/
    }

    if(isset($_POST["product_submit"]) && $_POST["product_submit"] == "View Details")
    {
      $_SESSION["product_view_id"] = $_POST["product_view"];
      header("location: view.php");
      exit;
    }

  }

  $sql_product_extract = "SELECT ID, MODEL, MRP_PRICE, DISCOUNT FROM products";
  if((isset($_POST["filter_submit"]) && $_POST["filter_submit"] == "Submit") && empty($price_err) && empty($manufacturer_err) && empty($product_type_err) && empty($RAM_err) && empty($HDD_err) )
  {
      $sql_product_extract = $sql_product_extract." WHERE ";
      //adding price filter to sql command
      $sql_product_extract = $sql_product_extract."( MRP_PRICE>=".$price_min." and MRP_PRICE<=".$price_max." )";
      //adding product_type filter to sql command
      $sql_product_extract = $sql_product_extract." and ( ";
      foreach ($product_type_select as $key => $value)
      {
        $sql_product_extract = $sql_product_extract."PRODUCT_TYPE=\"".$value."\" or ";
      }
      $sql_product_extract = $sql_product_extract."0 )";
      //adding brands filter to sql command
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

  //echo $sql_product_extract;

  $product_display_count = 0;

  $sql_product_extract_err = "";
  if($stmt_product_extract = $conn->prepare($sql_product_extract))
  {
    if($stmt_product_extract->execute())
    {
      $stmt_product_extract->store_result();
      $stmt_product_extract->bind_result( $product_id, $model_name, $MRP, $discount);
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
    <script src="index.js"></script>
  </head>

  <body>

    <!--topnav-->
    <div class="top-navbar">
    <!-- insert a logo image -->
    <a href="https://imgbb.com/"><img src="https://i.ibb.co/j8GNM6K/Electronikart-dbms-project.png" alt="logo" class="logo" /></a>
    <input type="text" placeholder="Search.." />
    <!-- search icon -->
    <span class="input-group-text"> <i class="fa fa-search"></i></span>

    <div class="menu-bar">
      <ul>
        <li><a href="#"><i class="fa fa-shopping-basket"></i>Cart</a></li>
        <li><a href="reset-password.php">Reset Password</a></li>
        <li><a href="logout.php">Sign Out</a></li>
      </ul>
    </div>
  </div>

    <!--second navbar-->
    <div class="navbar2">
      <a href="index.php">Home</a>
      <!--<a href="#Laptops">Laptops</a>
      <a href="#Mobiles">Mobiles</a>-->
    </div>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
      <!-- sidebar for filters -->
      <div id="mySidenav" class="sidenav">

        <div>
          <h3>Price</h3>
          <label>Minimum :<input type="numeric" name="price_min" value="<?php echo $price_min; ?>" style="width: 100px;" required>
            <br>Maximum :<input type = "numeric" name="price_max" value="<?php echo $price_max; ?>" style="width: 100px;" required></label>
            <span class="help-block"><?php echo $price_err; ?></span>
        </div>

        <br />
        <div class="product_type">
          <h3>Product Type</h3>
          <?php
            while($stmt_product_type->fetch())
            {
              echo "<label><input type=\"checkbox\" name=\"product_type_choosen[".$res_product_type."]\" value=\"".$res_product_type."\"";
              if((isset($product_type_select) && array_search($res_product_type, $product_type_select)) || !(isset($_POST["filter_submit"]) && $_POST["filter_submit"] == "Submit") )
                echo "checked";
              echo ">".$res_product_type."</label>\n";
            }
          ?>
          <span class="help-block"><?php echo $product_type_err; ?></span>
        </div>

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
            <span class="help-block"><?php echo $manufacturer_err; ?></span>
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
          <span class="help-block"><?php echo $RAM_err; ?></span>
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
          <span class="help-block"><?php echo $HDD_err; ?></span>
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
        echo "<img src=\"https://i.ibb.co/vkDvRtY/1-redmi-red.png\" alt=\"1-redmi-red\" border=\"0\" style=\"width: 100%\" />";
        echo "<p class=\"price\">".$MRP."</p>";
        echo "<p>Discount : ".$discount."%</p>";
        echo "<input type=\"hidden\" name=\"product_view\" value=".$product_id.">";
        echo "<p><input type=\"submit\" name=\"product_submit\" value=\"View Details\"><p>";
        echo "</div>";
        echo "</form>";
        $product_display_count+=1;
      }
    ?>

    
  </div>


</body>

</html>
