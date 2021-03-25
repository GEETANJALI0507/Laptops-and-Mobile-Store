<?php
  include 'config.php';

  //require 'login.php';
  session_start();
  if(!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true))
  {
    header("location: login.php");
    exit;
  }

  if($_SESSION["user_type"] === "ADMIN")
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
    echo("JSON file creation Failed");

  $Order_err = $Address_err = "";
  $address_id_select = -1;
  if($_SERVER["REQUEST_METHOD"] == "POST")
  {
    if(isset($_POST["search_submit"]) && $_POST["search_submit"] === "search_it")
    {
      $_SESSION["search_sent"] = 1;
      $_SESSION["search_query"] = $_POST["search"];
      header("location: index.php");
    }
    else if(isset($_POST["remove_product_id"]))
    {
      array_splice( $_SESSION["cart"], array_search( $_POST["remove_product_id"], $_SESSION["cart"], true), 1);
      if(isset($_POST["stock_requested"]))
        $stock_requested = $_POST["stock_requested"];
      if(isset($_POST["address_id_select"]))
        $address_id_select = $_POST["address_id_select"];
    }
    else if(isset($_POST["order_submit"]))
    {

      $stock_requested = $_POST["stock_requested"];
      $address_id_select = $_POST["address_id_select"];
      if($_POST["order_submit"] == "Evaluate Order")
      {
        if($address_id_select == -1)
        {
          $Address_err = "Please select an Address.";
          $Order_err = "Please select an Address.";
        }
        else
        {
          $_SESSION["order_finished"] = 1;
        }
      }

      else if($_POST["order_submit"] == "Place Order")
      {
        $stock_requested = $_POST["stock_requested"];
        $address_id_select = $_POST["address_id_select"];
        $customer_id = $_POST["customer_id"];
        $total_amount = $_POST["total_amount"];
        $subtotal = $_POST["subtotal"];
        //print_r($_POST["subtotal"]);

        $sql_order_id_extract = "SELECT MAX(ID) FROM orders";
        $stmt_order_id_extract = $conn->prepare($sql_order_id_extract);
        $stmt_order_id_extract->execute();
        $stmt_order_id_extract->store_result();
        $order_id_num_rows = $stmt_order_id_extract->num_rows;
        $stmt_order_id_extract->bind_result($order_id);
        $stmt_order_id_extract->fetch();


        if($order_id_num_rows != 1)
        {
          $order_id = 1;
        }
        else
        {
          $order_id = $order_id + 1;
        }

        $sql_order_insert = "INSERT INTO orders ( ID, CUSTOMER_ID, ORDER_DATE, ADDRESS_ID, TOTAL_AMOUNT ) VALUES ( ?, ?, LOCALTIMESTAMP(), ?, ?)";
        $sql_order_items_insert = "INSERT INTO order_items ( ORDER_ID, PRODUCT_ID, SUB_TOTAL, STOCK, DELIVERY_DATE) VALUES ( ?, ?, ?, ?, DATE_ADD( LOCALTIMESTAMP(), INTERVAL 15 DAY ) )";
        $sql_update_stock = "UPDATE products SET STOCK = STOCK - ? WHERE ID = ? ";
        if($stmt_order_insert = $conn->prepare($sql_order_insert))
        {
          $stmt_order_insert->bind_param("iiii", $order_id, $customer_id, $address_id_select, $total_amount);
          //echo 'order_id='.$order_id.' $customer_id='.$customer_id.' $address_id_select '.$address_id_select.' total amount='.$total_amount;
          if($stmt_order_insert->execute())
          {
            foreach ($_SESSION["cart"] as $key => $product_id)
            {
              $stmt_order_items_insert = $conn->prepare($sql_order_items_insert);
              $stmt_order_items_insert->bind_param("iiii", $order_id, $product_id, $param_subtotal, $param_stock);
              $param_subtotal = $subtotal[$product_id];
              $param_stock = $stock_requested[$product_id];
              //echo 'order_id='.$order_id.' product_id='.$product_id.' subtotal='.$param_subtotal.' stock_requested='.$param_stock;
              $stmt_order_items_insert->execute();
              $stmt_order_items_insert->close();
              $stmt_update_stock = $conn->prepare($sql_update_stock);
              $stmt_update_stock->bind_param("ii", $stock_requested[$product_id], $product_id);
              $stmt_update_stock->execute();
              $stmt_update_stock->close();
            }
            $_SESSION["order_finished"] = 2;
          }
          else
          {
            $Order_err = "Order insert Execution failed.";
          }
          $stmt_order_insert->close();
        }
        else
        {
          $Order_err = "Order insert Preparation failed.";
        }
      }
      else if($_POST["order_submit"] == "Order Placed" )
      {
        $stock_requested = $_POST["stock_requested"];
        $address_id_select = $_POST["address_id_select"];
        $customer_id = $_POST["customer_id"];
        $total_amount = $_POST["total_amount"];
        $subtotal = $_POST["subtotal"];
      }

    }
  }

  $total_amount = 0.00;
  $num_products = 0;
  $num_addresses = 0;
  $sql_product_extract = "SELECT ID, IMAGE, MODEL, MRP_PRICE, STOCK FROM products WHERE ";
  if(isset($_SESSION["cart"]))
  {
    foreach ($_SESSION["cart"] as $key => $value)
    {
      $sql_product_extract = $sql_product_extract."ID = ".$value." OR ";
    }
  }
  $sql_product_extract = $sql_product_extract." 0 ";
  $product_extract_err = "";
  if($stmt_product_extract = $conn->prepare($sql_product_extract))
  {
    if($stmt_product_extract->execute())
    {
      $stmt_product_extract->store_result();
      $stmt_product_extract->bind_result($product_id, $image, $model, $mrp, $stock);
      $num_products = $stmt_product_extract->num_rows;
    }
    else
    {
      $product_extract_err = "Extraction failed.";
    }
  }
  else
  {
    $product_extract_err = "Preparation failed.";
  }

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


  $sql_address_extract = "SELECT ID, ADDRESS_LINE1 FROM address WHERE CUSTOMER_ID = ?";
  $address_extract_err = "";
  if($stmt_address_extract = $conn->prepare($sql_address_extract))
  {
    $stmt_address_extract->bind_param("i", $customer_id);
    if($stmt_address_extract->execute())
    {
      $stmt_address_extract->store_result();
      $stmt_address_extract->bind_result( $address_id, $address_line1);
      $num_addresses = $stmt_address_extract->num_rows;
    }
    else
    {
      $address_extract_err = "Execution failed.";
    }
  }
  else
  {
    $address_extract_err = "Preparation failed.";
  }



?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="index.css" />
  <!-- CSS only -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
  <!-- JavaScript Bundle with Popper -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />-->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

  <title>Cart</title>

  <style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body {
    font-family: "Poppins", sans-serif;
  }


  /* cart items */
  .small-container{
    padding-left: 20px;
    padding-right: 20px;
    padding-bottom: 20px;
  }
  .cart-page {
    margin: 90px auto;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  .cart-info {
    display: flex;
    flex-wrap: wrap;
  }

  th {
    text-align: left;
    padding: 5px;
    color: #ffffff;
    background: #ff523b;
    font-weight: normal;
  }
  td {
    padding: 10px 5px;
  }

  td input {
    width: 40px;
    height: 30px;
    padding: 5px;
  }

  td a {
    color: #ff523b;
    font-size: 12px;
  }

  td img {
    width: 80px;
    height: 80px;
    margin-right: 10px;
  }

  .total-price {
    display: flex;
    justify-content: flex-end;
  }

  .total-price table {
    border-top: 3px solid #ff523b;
    width: 100%;
    max-width: 400px;
  }

  td:last-child {
    text-align: right;
  }

  th:last-child {
    text-align: right;
  }

  .removebn{
    background-color: red;
    border: none;
    border-radius: 0.2rem;
    color: white;
    cursor: pointer;
  }
  input[type = submit] {
    float :right;
    background-color: green;
    color: white;
    padding: 14px 20px;
    margin: 10px 0;
    border: none;
    cursor: pointer;
    width: 10%;
  }
  .custom-select {
    position: relative;
    font-family: Arial;
  }

  .custom-select select {
    display: none; /*hide original SELECT element: */
  }

  .select-selected {
    background-color: DodgerBlue;
  }

  /* Style the arrow inside the select element: */
  .select-selected:after {
    position: absolute;
    content: "";
    top: 14px;
    right: 10px;
    width: 0;
    height: 0;
    border: 6px solid transparent;
    border-color: #fff transparent transparent transparent;
  }

  /* Point the arrow upwards when the select box is open (active): */
  .select-selected.select-arrow-active:after {
    border-color: transparent transparent #fff transparent;
    top: 7px;
  }

  /* style the items (options), including the selected item: */
  .select-items div,.select-selected {
    color: #ffffff;
    padding: 8px 16px;
    border: 1px solid transparent;
    border-color: transparent transparent rgba(0, 0, 0, 0.1) transparent;
    cursor: pointer;
  }

  /* Style items (options): */
  .select-items {
    position: absolute;
    background-color: DodgerBlue;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 99;
  }

  /* Hide the items when the select box is closed: */
  .select-hide {
    display: none;
  }

  .select-items div:hover, .same-as-selected {
    background-color: rgba(0, 0, 0, 0.1);
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

  <!-- cart items details -->
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <div class="small-container cart-page">
      <table>
        <tr>
          <th>Product</th>
          <th>Quantity</th>
          <th>Subtotal</th>
        </tr>
        <?php

          while($stmt_product_extract->fetch())
          {
            echo '<tr>';
            echo '<td>';
            echo '<div class="cart-info">';
            echo '<img src="'.$image.'" alt="39-Apple-Mac-Book-Air" >';
            echo '<div>';
            echo '<p>'.$model.'</p>';
            echo '<small>Price : ₹'.$mrp.'</small>';
            if($_SESSION["order_finished"] > 0 )
            {
              $multiply = $stock_requested[$product_id]*$mrp;
              echo '<input type="hidden" name="subtotal['.$product_id.']" value="'.$multiply.'" >';
            }
            echo '<br/>';
            if($_SESSION["order_finished"] == 0 )
              echo '<button type ="submit"  name="remove_product_id" value="'.$product_id.'" class = "removebn">Remove</button>';
            echo '</div>';
            echo '</div>';
            echo '</td>';
            echo '<td><input type="number" name="stock_requested['.$product_id.']" min="1"';
            if($_SESSION["order_finished"] != 2)
            ' max="'.$stock.'" ';
            if(isset($stock_requested))
              echo 'value="'.$stock_requested[$product_id].'"';
            else
              echo 'value="1"';
            echo '/>';
            echo '<span style="display: block; margin-top: 5px; margin-bottom: 10px; color: red; font-family: -apple-system,BlinkMacSystemFont,Segoe UI,Helvetica,Arial,sans-serif,Apple Color Emoji,Segoe UI Emoji; font-size: 14px;">';

            echo '</span></td>';
            echo '<td>₹';
            if(isset($stock_requested))
              echo $stock_requested[$product_id]*$mrp;
            else
              echo $mrp;
            echo '</td>';
            echo '</tr>';
            if(isset($stock_requested))
              $total_amount = $total_amount + $stock_requested[$product_id]*$mrp;
            else
              $total_amount = $total_amount + $mrp;
          }
          if($num_products == 0)
            echo "<span>Cart is Empty</span>";
          $stmt_product_extract->close();

        ?>
      </table>
      <p>Price is inclusive of all Taxes</p>
      <br>
      <div class="custom-select" style="width:400px;">
        <select name="address_id_select"  >
          <option value=-1>Select address:</option>
          <?php
            while($stmt_address_extract->fetch())
            {
              echo '<option value="'.$address_id.'" ';
              if($address_id_select == $address_id)
               echo 'selected';
              echo ' >'.$address_line1.'</option>';
            }
          ?>
        </select>
        <span style="display: block; margin-top: 5px; margin-bottom: 10px; color: red; font-family: -apple-system,BlinkMacSystemFont,Segoe UI,Helvetica,Arial,sans-serif,Apple Color Emoji,Segoe UI Emoji; font-size: 14px;">
          <?php if($num_addresses<1) echo "Add an Address to you account"; else echo $Address_err;?>
        </span>
      </div>
      <div class="total-price">
        <table>
          <tr>
            <td>Subtotal</td>
            <td>₹<?php echo $total_amount;?></td>
          </tr>
          <tr>
            <td>Total</td>
            <td>₹<?php echo $total_amount;?></td>
          </tr>
        </table>
      </div>
      <?php
        if($_SESSION["order_finished"] > 0)
        {
          echo '<input type="hidden" name="total_amount" value="'.$total_amount.'" >';
          echo '<input type="hidden" name="customer_id" value="'.$customer_id.'" >';
        }
      ?>
      <input id="SB" type="submit"  name="order_submit" value="<?php if($_SESSION["order_finished"] == 0) echo "Evaluate Order"; else if($_SESSION["order_finished"] == 1) echo "Place Order"; else if($_SESSION["order_finished"] == 2) echo "Order Placed";?>">
      <span style="display: block; margin-top: 5px; margin-bottom: 10px; color: red; font-family: -apple-system,BlinkMacSystemFont,Segoe UI,Helvetica,Arial,sans-serif,Apple Color Emoji,Segoe UI Emoji; font-size: 14px;">
        <?php echo $Order_err; ?>
      </span>
    </div>
  </form>

</body>

<script>
  var x, i, j, l, ll, selElmnt, a, b, c;
  /* Look for any elements with the class "custom-select": */
  x = document.getElementsByClassName("custom-select");
  l = x.length;
  for (i = 0; i < l; i++) {
    selElmnt = x[i].getElementsByTagName("select")[0];
    ll = selElmnt.length;
    /* For each element, create a new DIV that will act as the selected item: */
    a = document.createElement("DIV");
    a.setAttribute("class", "select-selected");
    a.innerHTML = selElmnt.options[selElmnt.selectedIndex].innerHTML;
    x[i].appendChild(a);
    /* For each element, create a new DIV that will contain the option list: */
    b = document.createElement("DIV");
    b.setAttribute("class", "select-items select-hide");
    for (j = 1; j < ll; j++) {
      /* For each option in the original select element,
      create a new DIV that will act as an option item: */
      c = document.createElement("DIV");
      c.innerHTML = selElmnt.options[j].innerHTML;
      c.addEventListener("click", function(e) {
        /* When an item is clicked, update the original select box,
        and the selected item: */
        var y, i, k, s, h, sl, yl;
        s = this.parentNode.parentNode.getElementsByTagName("select")[0];
        sl = s.length;
        h = this.parentNode.previousSibling;
        for (i = 0; i < sl; i++) {
          if (s.options[i].innerHTML == this.innerHTML) {
            s.selectedIndex = i;
            h.innerHTML = this.innerHTML;
            y = this.parentNode.getElementsByClassName("same-as-selected");
            yl = y.length;
            for (k = 0; k < yl; k++) {
              y[k].removeAttribute("class");
            }
            this.setAttribute("class", "same-as-selected");
            break;
          }
        }
        h.click();
      });
      b.appendChild(c);
    }
    x[i].appendChild(b);
    a.addEventListener("click", function(e) {
      /* When the select box is clicked, close any other select boxes,
      and open/close the current select box: */
      e.stopPropagation();
      closeAllSelect(this);
      this.nextSibling.classList.toggle("select-hide");
      this.classList.toggle("select-arrow-active");
    });
  }

  function closeAllSelect(elmnt) {
    /* A function that will close all select boxes in the document,
    except the current select box: */
    var x, y, i, xl, yl, arrNo = [];
    x = document.getElementsByClassName("select-items");
    y = document.getElementsByClassName("select-selected");
    xl = x.length;
    yl = y.length;
    for (i = 0; i < yl; i++) {
      if (elmnt == y[i]) {
        arrNo.push(i)
      } else {
        y[i].classList.remove("select-arrow-active");
      }
    }
    for (i = 0; i < xl; i++) {
      if (arrNo.indexOf(i)) {
        x[i].classList.add("select-hide");
      }
    }
  }

  /* If the user clicks anywhere outside the select box,
  then close all select boxes: */
  document.addEventListener("click", closeAllSelect);
</script>

</html>
