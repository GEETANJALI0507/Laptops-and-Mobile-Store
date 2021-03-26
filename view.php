<?php
  session_start();

  include 'config.php';

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
  {
    echo("JSON file creation Failed");
  }

  $product_extract_err = "";
  $product_id = $_SESSION["product_view_id"];
  $sql_product_extract = "SELECT ID, PRODUCT_TYPE, MODEL, MANUFACTURER, STOCK, CPU_TYPE, OS, RAM_SIZE, HDD_SIZE, DESCRIPTION, MRP_PRICE, IMAGE FROM products WHERE ID = ?";
  if($stmt_product_extract = $conn->prepare($sql_product_extract))
  {
    $stmt_product_extract->bind_param("i", $product_id);
    if($stmt_product_extract->execute())
    {
      $stmt_product_extract->store_result();
      $stmt_product_extract->bind_result( $product_id, $product_type, $model, $manufacturer, $stock, $cpu_type, $os, $ram, $hdd, $description, $mrp, $image);
      $stmt_product_extract->fetch();
    }
    else
    {
      $product_extract_err = "Execution failed.";
    }
    $stmt_product_extract->close();
  }
  else
  {
    $product_extract_err = "Preparation failed.";
  }
  $description=str_replace("#","|",$description);
  $description = explode("|", $description);

  $update_stock_err = "";
  $compare_err = "";
  if($_SERVER["REQUEST_METHOD"] == "POST")
  {
    if(isset($_POST["search_submit"]) && $_POST["search_submit"] === "search_it")
    {
      $_SESSION["search_sent"] = 1;
      $_SESSION["search_query"] = $_POST["search"];
      header("location: index.php");
    }
    else if( isset($_POST["compare_submit"]) )
    {
      if( !isset($_SESSION["compare"]) )
        $_SESSION["compare"] = array();
      if( $_POST["compare_submit"] === "Add to Compare" &&  count($_SESSION["compare"])<=5 )
      {
        if( !isset($_SESSION["compare"]) )
          $_SESSION["compare"] = array();
        array_push( $_SESSION["compare"], $product_id);
      }
      else if($_POST["compare_submit"] === "Add to Compare")
      {
        $compare_err = "Compare page is full. Max 5 products allowed.";
      }
      else if( $_POST["compare_submit"] === "Remove from Compare" )
      {
        array_splice( $_SESSION["compare"], array_search( $product_id, $_SESSION["compare"], true), 1);
      }
    }
    else if( isset($_POST["cart_submit"]) )
    {
      if( $_POST["cart_submit"] === "Add to Cart")
      {
        if( !isset($_SESSION["cart"]) )
          $_SESSION["cart"] = array();
        array_push( $_SESSION["cart"], $product_id);
      }
      else if( $_POST["cart_submit"] === "Remove from Cart" )
      {
        array_splice( $_SESSION["cart"], array_search( $product_id, $_SESSION["cart"], true), 1);
      }
    }
    else if( isset($_POST["update_stock_submit"]) )
    {
      $stock = $_POST["updated_stock"];
      $sql_update_stock = "UPDATE products SET STOCK = ? WHERE ID = ?";
      if($stmt_update_stock = $conn->prepare($sql_update_stock))
      {
        $stmt_update_stock->bind_param("ii", $stock, $product_id);
        if($stmt_update_stock->execute())
        {
          $update_stock_err = "Stock updated.";
        }
        else
        {
          $update_stock_err = "Execution failed.";
        }
      }
      else
      {
        $update_stock_err = "Preparation failed.";
      }
      $stmt_update_stock->close();
    }

  }


  if( isset($_SESSION["compare"]) && array_search( $product_id, $_SESSION["compare"], true) !== FALSE )
  {
    $compare_submit_value = "Remove from Compare";
  }
  else
  {
    $compare_submit_value = "Add to Compare";
  }

  if($_SESSION["user_type"] === "CUSTOMER")
  {

    if( isset($_SESSION["cart"]) && array_search( $product_id, $_SESSION["cart"]) !== FALSE )
    {
      $cart_submit_value = "Remove from Cart";
    }
    else
    {
      $cart_submit_value = "Add to Cart";
    }
  }




?>


<html lang="en">

  <head>
    <!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">-->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
    <!-- JavaScript Bundle with Popper -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />-->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <style>

      * {
        margin: 0;
        padding: 0;
        outline: none;
      }

      .top-navbar {
        height: 47px;
        top: 0;
        position: sticky;
        background: white;
        border-bottom: 3px solid orange;
        opacity:1;
        z-index: 2;
      }

      .logo {
        float: left;
        height: 47px;
      }

      .top-navbar input[type="text"] {
        margin-top: 9px;
        margin-left: 25px;
        border: 1px solid orange;
        border-top-left-radius: 20px;
        border-bottom-left-radius: 20px;
        border-top-right-radius: 0px;
        border-bottom-right-radius: 0px;
        box-shadow: none;
        width: 50%;
        height: 30px;
        margin-bottom: 9px;
      }

      .input-group-text {
        background: orange;
        border: 5px solid orange;
        border-top-left-radius: 0px;
        border-bottom-left-radius: 0px;
        border-top-right-radius: 20px;
        border-bottom-right-radius: 20px;
        cursor: pointer;
      }
      #search{
        padding-left: 20px;
      }
      .search-box {
        display: inline-flex;
      }

      .fa-search {
        color: white;
      }
      .menu-bar {
        height: 55px;
        float: right;
      }


      .menu-bar a {
        border-left: 0.5px solid white;
        padding: 15px 30px;
        text-align: center;
        background-color: orange;
        cursor: pointer;
        display: inline-flex;
        font-weight: bold;
        font-size: 15px;
        color: white;
      }

      .fa-shopping-basket {
        margin-right: 5px;
      }

      #result:focus {outline: 3px solid #ddd;}

      .list-group  {
        top:48px;
        margin-left: 48px;

        position: absolute;
        background-color:white;
        /*border: 1px solid #ddd;*/
        z-index: 4;
        color: black;
        padding: auto ;
        text-decoration: none;
        display: none;
        cursor:pointer;

      }


      .list-group li:hover {

        background-color: rgba(255, 166, 0, 0.322) !important;
      }
      .list-group li{
        margin-left: 20px;
      }

      /*drop down content*/
      .menu-bar a:hover, .dropdown:hover .dropbtn {
        background-color: rgba(255, 166, 0, 0.356);
      }

      .dropdown {
        float: left;
        overflow: hidden;
      }

      .dropbtn{
        background-color:orange ;
      }
      .dropdown .dropbtn {
        font-size: 16px;
        border: none;
        outline: none;
        color: white;
        padding: 15px 30px;
        font-size: 15px;
        font-weight: bold;
        margin: 0;
      }


      /* Dropdown content (hidden by default) */
      .dropdown-content {
        display: none;
        position: absolute;
        background-color:rgba(255, 166, 0, 0.322);
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        z-index: 3;
      }

      /* Links inside the dropdown */
      .dropdown-content a {
        float: none;
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
        text-align: left;
      }

      /* Add a grey background color to dropdown links on hover */
      .dropdown-content a:hover {
        background-color: #ddd;
      }

      /* Show the dropdown menu on hover */
      .dropdown:hover .dropdown-content {
        display: block;
      }
      @media only screen and (max-width: 980px) {
        .top-navbar {
          height: 118px;
          border-bottom: 0;
        }
        .search-box {
          width: 100%;
        }
        .menu-bar {
          width: 100%;
        }

      }

      /*--------------------navbar2-----------------*/
      .navbar2 {
        top:48px;
        position:sticky;
        overflow: hidden;
        background-color: white;
        opacity:1;
        font-family: Arial, Helvetica, sans-serif;
        width:100%;
        z-index:1;

      }

      .navbar2 a {
        float: left;
        font-size: 16px;
        color: black;
        text-align: center;
        padding: 14px 16px;
        text-decoration: none;
      }


      .navbar2 a:hover{
        background-color: orange;
      }
      .navbar2 button:hover{
        background-color: orange;
      }

      body {
        font-family: 'Roboto', sans-serif;
        background: #353535;
      }



      .container {
        width: 100%;
        height: 100%;
        margin: 30px auto;
      }

      .card {
        border-radius: 25px;
        box-shadow: -11px 11px 1px rgba(0, 0, 0, 0.3);
      }

      .card-head {
        padding-left: 100px;
        padding-right: 100px;
        position: relative;
        background: #fa782e;
        border-radius: 25px 25px 0 0;
      }

      .card-head img{
        height: 100%;
        width: 60%;
        margin: auto;
        display: block;
        z-index: 1;
      }

      .card-head h1{
        text-align: center;
        font-size: 50px;
      }


      /*.product-img {
      position: absolute;
      left: 0;
      margin-top: -16px;
      margin-left: 50px;
      }*/

      .product-detail {
        padding: 0 0 20px 20px;
        font-size: 20px;
        color: #fff;
      }

      .product-detail h2 {
        font-size: 18px;
        font-weight: 750;
        letter-spacing: 2px;
        padding-bottom: 10px;
        text-transform: uppercase;
        top: 10px;
        color: #241f1c;
      }

      .back-text {
        display: inline-block;
        font-size: 125px;
        font-weight: 900;
        /*margin-left: -7px;
        margin-top: -12px;*/
        opacity: 0.1;
        z-index: 2;
      }

      .card-body {
        height: 400px;
        background: #fff;
        border-radius: 0 0 25px 25px;
      }

      .card-body button{
        font-size: 20px;
        background-color: grey;
        color: white;
      }

      .form-group{
        font-size: 25px;
        float: right;
        margin-right: -145px;
        margin-top: 125px;
        text-align: center;
      }

      .product-title {
        padding: 20px 20px 5px 20px;
        display: block;
        font-size: 25px;
        font-weight: 500;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: #241f1c;
      }

      .product-title b {
        font-weight: 900;
        letter-spacing: 0px;
      }

      .badge {
        position: relative;
        font-size: 10px;
        font-weight: 300;
        color: #fff;
        background: #11e95b;
        padding: 2px 5px;
        border-radius: 4px;
        top: -2px;
        margin-left: 5px;
      }

      .product-caption {
        display: block;
        padding: 0 20px;
        font-size: 20px;
        font-weight: 400;
        text-transform: uppercase;
      }

      .product-rating {
        padding: 0 20px;
        font-size: 11px;
      }

      .product-rating i.grey {
        color: #acacab;
      }

      .product-size h4 {
        font-size: 11px;
        padding: 0 21px;
        margin-top: 15px;
        padding-bottom: 10px;
        text-transform: uppercase;
      }

      .product-price {
        position: relative;
        float: right;
        margin-right: 300px;
        bottom: 1px;
        background: #11e95b;
        padding: 7px 5px;
        text-align: center;
        display: inline-block;
        font-size: 18px;
        font-weight: 200;
        color: #fff;
        border-radius: 5px;
        margin-top: 20px;
        margin-left: -5px;
      }

      .product-price b {
        margin-left: 5px;
      }

      .cart-button:hover{
        background-color: red;
      }

      .compare-button:hover{
        background-color: red;
      }

      .table-responsive{
        text-align: left;
      }

      .pl-0{
        font-size: 20px;
        float: left;
      }

      .table{
        margin-left: 300px;

        margin-right: auto;
      }

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

    <div class="container">
      <div class="card">
        <div class="card-head">
          <span class="back-text"><?php echo $manufacturer; ?></span>
          <h1><?php echo $model; ?></h1>
          <img src = "<?php echo $image ?>" alt="1-redmi-red"  class="product-img">
          <div class="product-detail">
            <?php
              foreach ($description as $key => $value)
              {
                echo "<p>#".$value."</p>";
              }
              //print_r($_SESSION["compare"]);
            ?>
          </div>
        </div>

        <div class="card-body">

          <div class="product-desc">
            <span class="product-title"><b><?php echo $manufacturer; ?></b><span class="badge">New</span></span>
            <!--<span class="product-caption">Note 7 Pro</span>-->
            <div class="product-properties">
              <span class="product-price">Rs.<b><?php echo $mrp ?></b></span>
              <div class="form-group">

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                  <?php
                    if($_SESSION["user_type"] === "CUSTOMER")
                    {
                      echo "<button class=\"add-button\" type=\"submit\" name=\"cart_submit\" value=\"".$cart_submit_value."\" >".$cart_submit_value."</button>";
                    }
                    else
                    {
                      echo "<input type=\"number\" name=\"updated_stock\" value=".$stock.">";
                      echo "<button class=\"add-button\" type=\"submit\" name=\"update_stock_submit\" value=\"Update Stock\" >Update Stock</button>";
                      echo "<span class=\"help-block\" style=\"display: block; margin-top: 5px; margin-bottom: 10px; color: red; font-family: -apple-system,BlinkMacSystemFont,Segoe UI,Helvetica,Arial,sans-serif,Apple Color Emoji,Segoe UI Emoji; font-size: 14px;\">".$update_stock_err."</span>";
                    }
                  ?>
                </form>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                  <?php
                    echo "<button class=\"add-button\" type=\"submit\" name=\"compare_submit\" value=\"".$compare_submit_value."\" >".$compare_submit_value."</button>";
                    echo "<span class\"help-block\" style=\"display: block; margin-top: 5px; margin-bottom: 10px; color: red; font-family: -apple-system,BlinkMacSystemFont,Segoe UI,Helvetica,Arial,sans-serif,Apple Color Emoji,Segoe UI Emoji; font-size: 14px;\">".$compare_err."</span>";
                  ?>
                </form>

              </div>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table">
              <tbody>
                <tr>
                  <th class="pl-0" scope="row"><strong>Model</strong></th>
                  <td><?php echo $model; ?></td>
                </tr>
                <tr>
                  <th class="pl-0" scope="row"><strong>Quantity Available</strong></th>
                  <td><?php echo $stock; ?></td>
                </tr>
                <tr>
                  <th class="pl-0" scope="row"><strong>CPU_Type</strong></th>
                  <td><?php echo $cpu_type; ?></td>
                </tr>
                <tr>
                  <th class="pl-0" scope="row"><strong>OS</strong></th>
                  <td><?php echo $os; ?></td>
                </tr>
                <tr>
                  <th class="pl-0" scope="row"><strong>RAM_Size</strong></th>
                  <td><?php echo $ram; ?>GB</td>
                </tr>
                <tr>
                  <th class="pl-0" scope="row"><strong>HDD_Size</strong></th>
                  <td><?php echo $hdd; ?>GB</td>
                </tr>
              </tbody>
            </table>
          </div>

        </div>

        <div>
        </div>

      </div>
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
