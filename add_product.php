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

  $file_json = "data.json";
  if(file_put_contents($file_json, json_encode($model_data)))
  {  }//echo("File created");}
  else
  {
    echo("JSON file creation Failed");
  }

  $product_type_err = $model_err = $manufacturer_err = $stock_err = $release_date_err = $cpu_type_err = $os_err = $ram_err = $hdd_err = $description_err = $mrp_err = $discount_err = $tax_err = $image_err = "";
  $product_insert_err = "";
  $product_type = $model = $manufacturer = "";
  $stock = $release_date = NULL;
  $cpu_type = $os = "";
  $ram = $hdd = NULL;
  $description = "";
  $mrp = $discount = $tax = NULL;
  $image = NULL;

  if($_SERVER["REQUEST_METHOD"] == "POST")
  {
    if(isset($_POST["search_submit"]) && $_POST["search_submit"] === "search_it")
    {
      $_SESSION["search_sent"] = 1;
      $_SESSION["search_query"] = $_POST["search"];
      header("location: index.php");
    }
    else if(isset($_POST["add_product_submit"]) && $_POST["add_product_submit"] === "Submit")
    {

      if(empty(trim($_POST["product_type"])))
      {
        $product_type_err = "Choose a product type.";
      }
      else
      {
        $product_type = trim($_POST["product_type"]);
      }

      if(empty(trim($_POST["model"])))
      {
        $model_err = "Model name cannot be empty.";
      }
      else
      {
        $sql_model_check = "SELECT ID FROM products WHERE MODEL = ?";
        if($stmt_model_check = $conn->prepare($sql_model_check))
        {
          $stmt_model_check->bind_param("s",$param_model);
          $param_model = trim($_POST["model"]);
          if($stmt_model_check->execute())
          {
            $stmt_model_check->store_result();
            if($stmt_model_check->num_rows >0)
            {
              $model_err = "Model already exists in the Database.";
            }
            else
            {
              $model = trim($_POST["model"]);
            }
          }
          else
          {
            $product_insert_err = "Model check Execution failed.";
          }
          $stmt_model_check->close();
        }
        else
        {
          $product_insert_err = "Model check preparation failed.";
        }
        $product_type = trim($_POST["product_type"]);
      }

      if(empty(trim($_POST["manufacturer"])))
      {
        $manufacturer_err = "Manufacturer name cannot be empty.";
      }
      else
      {
        $manufacturer = trim($_POST["manufacturer"]);
      }

      if(empty(trim($_POST["stock"])))
      {
        $stock_err = "Stock cannot be empty.";
      }
      else if($_POST["stock"]<0)
      {
        $stock_err = "Stock value cannot be negative.";
      }
      else
      {
        $stock = $_POST["stock"];
      }

      if(empty(trim($_POST["release_date"])))
      {
        $release_date_err = "Release date cannot be empty.";
      }
      else
      {
        $release_date = trim($_POST["release_date"]);
      }

      if(empty(trim($_POST["cpu_type"])))
      {
        $cpu_type_err = "CPU Type cannot be empty.";
      }
      else
      {
        $cpu_type = trim($_POST["cpu_type"]);
      }

      if(empty(trim($_POST["os"])))
      {
        $os_err = "OS cannot be empty.";
      }
      else
      {
        $os = trim($_POST["os"]);
      }

      if(empty(trim($_POST["cpu_type"])))
      {
        $cpu_type_err = "CPU Type cannot be empty.";
      }
      else
      {
        $cpu_type = trim($_POST["cpu_type"]);
      }

      if(empty(trim($_POST["ram"])))
      {
        $ram_err = "RAM Size cannot be empty.";
      }
      else if($_POST["ram"]<=0)
      {
        $ram_err = "RAM Size cannot be non-positive.";
      }
      else
      {
        $ram = $_POST["ram"];
      }

      if(empty(trim($_POST["hdd"])))
      {
        $hdd_err = "HDD Size cannot be empty.";
      }
      else if($_POST["hdd"]<=0)
      {
        $hdd_err = "HDD Size cannot be non-positive.";
      }
      else
      {
        $hdd = $_POST["hdd"];
      }

      if(empty(trim($_POST["description"])))
      {
        $description_err = "Description cannot be empty.";
      }
      else
      {
        $description = trim($_POST["description"]);
      }

      if(empty(trim($_POST["mrp"])))
      {
        $mrp_err = "MRP cannot be empty.";
      }
      else if($_POST["mrp"]<=0)
      {
        $mrp_err = "MRP cannot be non-positive.";
      }
      else
      {
        $mrp = $_POST["mrp"];
      }

      if(empty(trim($_POST["discount"])))
      {
        $discount_err = "Discount Rate cannot be empty.";
      }
      else if($_POST["discount"]<=0)
      {
        $discount_err = "Discount Rate cannot be non-positive.";
      }
      else
      {
        $discount = $_POST["discount"];
      }

      if(empty(trim($_POST["tax"])))
      {
        $tax_err = "Tax Rate cannot be empty.";
      }
      else if($_POST["tax"]<=0)
      {
        $tax_err = "Tax Rate cannot be non-positive.";
      }
      else
      {
        $tax = $_POST["tax"];
      }

      $image_file_name = $_FILES['image']['name'];
      $image_file_size = $_FILES['image']['size'];
      $image_file_tmp = $_FILES['image']['tmp_name'];
      $image_file_type = $_FILES['image']['type'];
      $exploded = explode('.', $image_file_name);
      $ended = end($exploded);
      $image_file_ext=strtolower($ended);
      $extensions= array("jpeg","jpg","png");

      if(in_array($image_file_ext,$extensions) === false){
         $image_err=$image_err."Extension not allowed, please choose a JPEG or PNG file.";
      }

      if($image_file_size > 2097152) {
         $image_err=$image_err."File size must be excately 2 MB";
      }

      if(empty($image_err))
      {
        move_uploaded_file($image_file_tmp,"product_images/".$image_file_name);
      }
      if(empty($product_type_err) && empty($model_err) && empty($manufacturer_err) && empty($stock_err) && empty($release_date_err) && empty($cpu_type_err) && empty($os_err)
      && empty($ram_err) && empty($hdd_err) && empty($description_err) && empty($mrp_err) && empty($discount_err) && empty($tax_err) && empty($image_err) && empty($product_insert_err) )
      {
        $sql_product_insert = "INSERT INTO products ( PRODUCT_TYPE, MODEL, MANUFACTURER, STOCK, RELEASE_DATE, CPU_TYPE, OS, RAM_SIZE, HDD_SIZE, DESCRIPTION, MRP_PRICE, DISCOUNT, TAX_RATE, IMAGE) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )";
        if($stmt_product_insert = $conn->prepare($sql_product_insert))
        {
          $stmt_product_insert->bind_param("sssisssiisiiis", $product_type, $model, $manufacturer, $stock, $release_date, $cpu_type, $os, $ram, $hdd, $description, $mrp, $discount, $tax, $param_image );
          $param_image = "product_images/".$image_file_name;
          if($stmt_product_insert->execute())
          {
            $product_insert_err = "Product inserted successfully.";
          }
          else
          {
            $product_insert_err = "Product insertion execution failed.";
          }
          $stmt_product_insert->close();
        }
        else
        {
          $product_insert_err = "Product insertion preparation failed.";
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

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
      <div class="top-navbar">
        <!-- insert a logo image -->
        <a href="https://imgbb.com/"><img src="https://i.ibb.co/j8GNM6K/Electronikart-dbms-project.png" alt="logo" class="logo" /></a>

        <input type="text" name="search" id="search" placeholder="Search.." required>
        <!-- search icon -->
        <!--<span class="input-group-text">--><button class="input-group-text" type="submit" name="search_submit" value="search_it"><i class="fa fa-search"></i></button><!--</span>-->



        <div class="menu-bar">
          <ul>
            <li><a href="#"><i class="fa fa-shopping-basket"></i>Cart</a></li>
            <li><a href="reset-password.php">Reset Password</a></li>
            <li><a href="logout.php">Sign Out</a></li>
          </ul>
        </div>
      </div>
    </form>
    <ul class="list-group" id="result"></ul>
    <!--second navbar-->
    <div class="navbar2">
      <a href="index.php">Home</a>
      <a href="manage_address.php">Manage Addresses</a>
      <a href="add_admin.php">Add Admin</a>
      <!--<a href="#Laptops">Laptops</a>
      <a href="#Mobiles">Mobiles</a>-->
    </div>

    <div class="wrapper">
        <h2>Add Product </h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" id="registration">

            <div class="form-group">
                <label for="product_type">Product Type :</label>
                <select id="product_type" name="product_type" value="<?php echo $product_type;?>" required>
                  <option value="LAPTOP">LAPTOP</option>
                  <option value="MOBILE">MOBILE</option>
                </select>
                <span class="help-block"><?php echo $product_type_err; ?></span>
              </div>
            <!--<br><br>-->
            <div class="form-group">
                <label for="model">Model :</label>
                <input type="text" id="model" placeholder="Model" name="model" value="<?php echo $model; ?>" required>
                <span class="help-block"><?php echo $model_err; ?></span>
            </div>
            <!--<br><br>-->
            <div class="form-group">
                <label for="manufacturer">Manufacturer :</label>
                <input type="text" id="manufacturer" placeholder="Manufacturer" name="manufacturer" value="<?php echo $manufacturer; ?>" required>
                <span class="help-block"><?php echo $manufacturer_err; ?></span>
            </div>
            <!--<br><br>-->
            <div class="form-group">
                <label for="stock">Stock :</label>
                <input type="number" min="0" id="stock" placeholder="Stock" name="stock" value="<?php echo $stock; ?>" required>
                <span class="help-block"><?php echo $stock_err; ?></span>
            </div>
            <!--<br><br>-->
            <div class="form-group">
                <label for="release_date">Release Date :</label>
                <input type="date" id="release_date" placeholder="Release Date" name="release_date" value="<?php echo $release_date; ?>" required>
                <span class="help-block"><?php echo $release_date_err; ?></span>
            </div>
            <!--<br><br>-->
            <div class="form-group">
                <label for="cpu_type">CPU Type :</label>
                <input type="text" id="cpu_type" placeholder="CPU Type" name="cpu_type" value="<?php echo $cpu_type; ?>" required>
                <span class="help-block"><?php echo $cpu_type_err; ?></span>
            </div>
            <!--<br><br>-->
            <div class="form-group">
                <label for="os">OS :</label>
                <input type="text" id="os" placeholder="OS" name="os" value="<?php echo $os; ?>" required>
                <span class="help-block"><?php echo $os_err; ?></span>
            </div>
            <!--<br><br>-->
            <div class="form-group">
                <label for="ram">RAM Size :</label>
                <input type="number" min="0" id="ram" placeholder="RAM Size" name="ram" value="<?php echo $ram; ?>" required>
                <span class="help-block"><?php echo $ram_err; ?></span>
            </div>
            <!--<br><br>-->
            <div class="form-group">
                <label for="hdd">HDD Size :</label>
                <input type="number" min="0" id="hdd" placeholder="HDD Size" name="hdd" value="<?php echo $hdd; ?>" required>
                <span class="help-block"><?php echo $hdd_err; ?></span>
            </div>
            <!--<br><br>-->
            <div class="form-group">
                <label for="description">Description :</label>
                <input type="text" id="description" placeholder="Description" name="description" value="<?php echo $description; ?>" required>
                <span class="help-block"><?php echo $description_err; ?></span>
            </div>
            <!--<br><br>-->
            <div class="form-group">
                <label for="mrp">MRP :</label>
                <input type="number" min="0" id="mrp" placeholder="MRP" name="mrp" value="<?php echo $mrp; ?>" required>
                <span class="help-block"><?php echo $mrp_err; ?></span>
            </div>
            <!--<br><br>-->
            <div class="form-group">
                <label for="discount">Discount Rate :</label>
                <input type="number" min="0" id="discount" placeholder="Discount Rate" name="discount" value="<?php echo $discount; ?>" required>
                <span class="help-block"><?php echo $discount_err; ?></span>
            </div>
            <!--<br><br>-->
            <div class="form-group">
                <label for="tax">Tax Rate :</label>
                <input type="number" min="0" id="tax" placeholder="Tax Rate" name="tax" value="<?php echo $tax; ?>" required>
                <span class="help-block"><?php echo $tax_err; ?></span>
            </div>
            <!--<br><br>-->
            <div class="form-group">
                <label for="image">Image File :</label>
                <input type="file" id="image" placeholder="Image File" name="image" value="<?php echo $image_file_name; ?>" required>
                <span class="help-block"><?php echo $image_err; ?></span>
            </div>
            <!--<br><br>-->
            <div class="form-group">
                  <input id="SB" type="submit" class="btn btn-primary" name="add_product_submit" value="Submit">
                  <input id="RB" type="reset" class="btn btn-default" value="Reset">
                  <span class="help-block"><?php echo $product_insert_err; ?></span>
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
