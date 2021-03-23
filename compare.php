<?php

  session_start();

  include 'config.php';

  if(!(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true))
  {
    header("location: login.php");
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




  if($_SERVER["REQUEST_METHOD"] == "POST")
  {
    if(isset($_POST["search_submit"]) && $_POST["search_submit"] === "search_it")
    {
      $_SESSION["search_sent"] = 1;
      $_SESSION["search_query"] = $_POST["search"];
      header("location: index.php");
    }
    else if(isset($_POST["view_submit"]))
    {
      $_SESSION["product_view_id"] = $_POST["product_id_submit"];
      header("location: view.php");
      exit;
    }
    else if(isset($_POST["cart_submit"]))
    {
      if(!isset($_SESSION["cart"]))
      {
        $_SESSION["cart"] = array();
      }
      if( $_POST["cart_submit"] === "Add to Cart")
      {
        array_push( $_SESSION["cart"], $_POST["product_id_submit"]);
      }
      else if( $_POST["cart_submit"] === "Remove from Cart" )
      {
        array_splice( $_SESSION["cart"], array_search( $_POST["product_id_submit"], $_SESSION["cart"], true), 1);
      }
    }
    else if(isset($_POST["remove_submit"]) && $_POST["remove_submit"] == "Remove from Compare")
    {
      //echo "done.<br>";
      array_splice( $_SESSION["compare"], array_search( $_POST["product_id_submit"], $_SESSION["compare"], true), 1);
    }

  }


  $sql_product_extract = "SELECT ID, IMAGE, MODEL, MRP_PRICE, CPU_TYPE, OS, RAM_SIZE, HDD_SIZE, STOCK FROM products WHERE (";
  if(isset($_SESSION["compare"]))
  {
    foreach ($_SESSION["compare"] as $key => $value)
    {
      $sql_product_extract = $sql_product_extract."ID = ".$value." OR ";
    }
  }
  $sql_product_extract = $sql_product_extract." 0 )";
  $product_extract_err = "";
  if($stmt_product_extract = $conn->prepare($sql_product_extract))
  {
    if($stmt_product_extract->execute())
    {
      $stmt_product_extract->store_result();
      $stmt_product_extract->bind_result($product_id, $image, $model, $mrp, $cpu, $os, $ram, $hdd, $stock);
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


?>

<html lang="en" class="no-js">

<head>
  <meta charset="UTF-8">

  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Products Comparison Table</title>
  <link rel="stylesheet" href="index.css" />
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

  <style>
    /* ----------------------Primary style------------------------------- */
    *, *::after, *::before {
      box-sizing: border-box;
    }

    html {
      font-size: 62.5%;
    }

    body {
      font-size: 1.6rem;
      font-family: "Source Sans Pro", sans-serif;
      color: #404042;
      background-color: #ffffff;
    }

    a {
      color: #9dc997;
      text-decoration: none;
    }

    img {
      max-width: 100%;
    }

    h1 {
      font-size: 2.2rem;
      text-align: center;
      padding: 4em 5%;
    }
    @media only screen and (min-width: 1170px) {
      h1 {
        font-size: 4rem;
        font-weight: 300;
        padding: 3em 5%;
      }
    }

    /* --------------------------------

    Main Components

    -------------------------------- */
    .cd-products-comparison-table {
      margin-bottom: 6em;
    }
    .cd-products-comparison-table::after {
      /* never visible - this is used in jQuery to check the current MQ */
      display: none;
      content: 'mobile';
    }
    .cd-products-comparison-table header {
      padding: 0 5% 25px;
    }
    .cd-products-comparison-table header::after {
      clear: both;
      content: "";
      display: table;
    }
    .cd-products-comparison-table h2 {
      float: left;
      font-weight: bold;
    }
    .cd-products-comparison-table .actions {
      float: right;
    }
    .cd-products-comparison-table .reset, .cd-products-comparison-table .filter {
      font-size: 1.4rem;
    }
    .cd-products-comparison-table .reset {
      color: #404042;
      text-decoration: underline;
    }
    .cd-products-comparison-table .filter {
      padding: .6em 1.5em;
      color: #ffffff;
      background-color: #cccccc;
      border-radius: 3px;
      margin-left: 1em;
      cursor: not-allowed;
      -webkit-transition: background-color 0.3s;
      -moz-transition: background-color 0.3s;
      transition: background-color 0.3s;
    }
    .cd-products-comparison-table .filter.active {
      cursor: pointer;
      background-color: #9dc997;
    }
    .no-touch .cd-products-comparison-table .filter.active:hover {
      background-color: #a7cea1;
    }
    @media only screen and (min-width: 1170px) {
      .cd-products-comparison-table {
        margin-bottom: 8em;
      }
      .cd-products-comparison-table::after {
        /* never visible - this is used in jQuery to check the current MQ */
        content: 'desktop';
      }
      .cd-products-comparison-table header {
        padding: 0 5% 40px;
      }
      .cd-products-comparison-table h2 {
        font-size: 2.4rem;
      }
      .cd-products-comparison-table .reset, .cd-products-comparison-table .filter {
        font-size: 1.6rem;
      }
      .cd-products-comparison-table .filter {
        padding: .6em 2em;
        margin-left: 1.6em;
      }
    }

    .cd-products-table {
      position: relative;
      overflow: hidden;
    }

    .cd-products-table .features {
      /* fixed left column - product properties list */
      position: absolute;
      z-index: 1;
      top: 0;
      left: 0;
      width: 120px;
      border-style: solid;
      border-color: #e6e6e6;
      border-top-width: 1px;
      border-bottom-width: 1px;
      background-color: #fafafa;
      opacity: .95;
    }
    .cd-products-table .features::after {
      /* color gradient on the right of .features -  visible while scrolling inside the .cd-products-table */
      content: '';
      position: absolute;
      top: 0;
      left: 100%;
      width: 4px;
      height: 100%;
      background-color: transparent;
      background-image: -webkit-linear-gradient(left, rgba(0, 0, 0, 0.06), transparent);
      background-image: linear-gradient(to right,rgba(0, 0, 0, 0.06), transparent);
      opacity: 0;
    }
    @media only screen and (min-width: 1170px) {
      .cd-products-table .features {
        width: 210px;
      }
    }

    .cd-products-table.scrolling .features::after {
      opacity: 1;
    }

    .cd-products-wrapper {
      overflow-x: auto;
      /*this fixes the buggy scrolling on webkit browsers - mobile devices only - when overflow property is applied */
      -webkit-overflow-scrolling: touch;
      border-style: solid;
      border-color: #e6e6e6;
      border-top-width: 1px;
      border-bottom-width: 1px;
    }

    .cd-products-columns {
      /* products list wrapper */
      width: 1200px;
      margin-left: 120px;
    }

    .cd-products-columns::after {
      clear: both;
      content: "";
      display: table;
    }

    @media only screen and (min-width: 1170px) {
      .cd-products-columns {
        width: 2000px;
        line-height: 35px;
        margin-left: 210px;
      }
    }

    .cd-products-columns .product {
      position: relative;
      float: left;
      width: 150px;
      text-align: center;
      -webkit-transition: opacity 0.3s, visibility 0.3s, -webkit-transform 0.3s;
      -moz-transition: opacity 0.3s, visibility 0.3s, -moz-transform 0.3s;
      transition: opacity 0.3s, visibility 0.3s, transform 0.3s;
    }

    .filtered .cd-products-columns .product:not(.selected) {
      position: absolute;
    }
    @media only screen and (min-width: 1170px) {
      .cd-products-columns .product {
        width: 400px;
      }
    }

    .cd-features-list li {
      font-size: 1.4rem;
      padding: 25px 40px;
      border-color: #e6e6e6;
      border-style: solid;
      border-top-width: 1px;
      border-right-width: 1px;
    }

    @media only screen and (min-width: 1170px) {
      .cd-features-list li {
        font-size: 1rem;
      }
      .cd-features-list li.rate {
        padding: 22px 0;
      }
    }

    .features .cd-features-list li,
    .cd-products-table .features .top-info {
      /* fixed left column - items */
      font-size: 1.2rem;
      font-weight: bold;
      /* set line-height value equal to font-size of text inside product cells */
      line-height: 14px;
      padding: 25px 10px;
      text-align: left;
    }
    @media only screen and (min-width: 1170px) {
      .features .cd-features-list li,
      .cd-products-table .features .top-info {
        text-transform: uppercase;
        line-height: 35px;
        padding: 25px 20px;
        letter-spacing: 1px;
      }
    }

    .features .cd-features-list li {
      /* truncate text with dots */
      white-space: nowrap;
      text-overflow: ellipsis;
      overflow: hidden;
    }

    .cd-products-table .top-info {
      position: relative;
      height: 177px;
      width: 150px;
      text-align: center;
      padding: 1.25em 2.5em;
      border-color: #e6e6e6;
      border-style: solid;
      border-right-width: 1px;
      -webkit-transition: height 0.3s;
      -moz-transition: height 0.3s;
      transition: height 0.3s;
      cursor: pointer;
      background: #ffffff;
    }
    .cd-products-table .top-info::after {
      /* color gradient below .top-info -  visible when .top-info is fixed */
      content: '';
      position: absolute;
      left: 0;
      top: 100%;
      height: 4px;
      width: 100%;
      background-color: transparent;
      background-image: -webkit-linear-gradient(top, rgba(0, 0, 0, 0.06), transparent);
      background-image: linear-gradient(to bottom,rgba(0, 0, 0, 0.06), transparent);
      opacity: 0;
    }
    .cd-products-table .top-info h3 {
      padding: 1.25em 0 0.625em;
      font-weight: bold;
      font-size: 1.4rem;
    }
    .cd-products-table .top-info img {
      display: block;
      -webkit-backface-visibility: hidden;
      backface-visibility: hidden;
    }

    .cd-products-table .top-info .check {
      position: relative;
      display: inline-block;
      height: 16px;
      width: 16px;
      margin: 0 auto 1em;
    }
    .cd-products-table .top-info .check::after, .cd-products-table .top-info .check::before {
      /* used to create the check icon and green circle dot - visible when product is selected */
      position: absolute;
      top: 0;
      left: 0;
      content: '';
      height: 100%;
      width: 100%;
    }
    .cd-products-table .top-info .check::before {
      /* green circle dot */
      border-radius: 50%;
      border: 1px solid #e6e6e6;
      background: #ffffff;
    }
    .cd-products-table .top-info .check::after {
      /* check icon */
      background: url(../img/cd-check.svg) no-repeat center center;
      background-size: 24px 24px;
      opacity: 0;
    }
    @media only screen and (min-width: 1170px) {
      .cd-products-table .top-info {
        height: 280px;
        width: 310px;
      }
      .cd-products-table .top-info h3 {
        padding-top: 1.4em;
        font-size: 1.6rem;
      }
      .cd-products-table .top-info .check {
        margin-bottom: 1.5em;
      }
    }

    .cd-products-table .features .top-info {
      /* models */
      width: 120px;
      cursor: auto;
      background: #fafafa;
    }
    @media only screen and (min-width: 1170px) {
      .cd-products-table .features .top-info {
        width: 210px;
      }
    }

    .cd-products-table .selected .top-info .check::after {
      /* check icon */
      opacity: 1;
    }

    @media only screen and (min-width: 1170px) {
      .cd-products-table.top-fixed .cd-products-columns > li,
      .cd-products-table.top-scrolling .cd-products-columns > li,
      .cd-products-table.top-fixed .features,
      .cd-products-table.top-scrolling .features {
        padding-top: 160px;
      }

      .cd-products-table.top-fixed .top-info,
      .cd-products-table.top-scrolling .top-info {
        height: 160px;
        position: fixed;
        top: 0;
      }
      .no-cssgradients .cd-products-table.top-fixed .top-info, .no-cssgradients
      .cd-products-table.top-scrolling .top-info {
        border-bottom: 1px solid #e6e6e6;
      }
      .cd-products-table.top-fixed .top-info::after,
      .cd-products-table.top-scrolling .top-info::after {
        opacity: 1;
      }


      .cd-products-table.top-scrolling .top-info {
        position: absolute;
      }
    }
    .cd-table-navigation a {
      position: absolute;
      z-index: 2;
      top: 0;
      right: 15px;
      /* replace text with image */
      overflow: hidden;
      text-indent: 100%;
      white-space: nowrap;
      color: transparent;
      height: 60px;
      width: 40px;
      background: rgba(64, 64, 66, 0.8) url("../img/cd-arrow.svg") no-repeat center center;
      border-radius: 3px;
    }
    .cd-table-navigation a.inactive {
      opacity: 0;
      visibility: hidden;
    }

    .no-touch .cd-table-navigation a:hover {
      background-color: #404042;
    }

    /* --------------------------------

    No JS

    -------------------------------- */
    .no-js .actions {
      display: none;
    }

    .no-js .cd-products-table .top-info {
      height: 145px;
    }
    @media only screen and (min-width: 1170px) {
      .no-js .cd-products-table .top-info {
        height: 248px;
      }
    }

    .no-js .cd-products-columns .check {
      display: none;
    }

    html, body, div, span, applet, object, iframe, h1, h2, h3, h4, h5, h6, p, blockquote, pre, a, abbr, acronym, address, big, cite, code,
    del, dfn, em, img, ins, kbd, q, s, samp, small, strike, strong, sub, sup, tt, var, b, u, i, center, dl, dt, dd, ol, ul, li,
    fieldset, form, label, legend, table, caption, tbody, tfoot, thead, tr, th, td, article, aside, canvas, details, embed,
    figure, figcaption, footer, header, hgroup, menu, nav, output, ruby, section, summary, time, mark, audio, video {
        margin: 0;
        padding: 0;
        border: 0;
        font-size: 100%;
        font: inherit;
        vertical-align: baseline;
      }

    /* HTML5 display-role reset for older browsers */
    article, aside, details, figcaption, figure,
    footer, header, hgroup, menu, nav, section, main {
      display: block;
    }
    body {
      line-height: 1;
    }
    ol, ul {
      list-style: none;
    }
    blockquote, q {
      quotes: none;
    }
    blockquote:before, blockquote:after,
    q:before, q:after {
      content: '';
      content: none;
    }
    table {
      border-collapse: collapse;
      border-spacing: 0;
    }

    .form-group{
      font-size: 25px;
      text-align: center;
    }

  </style>

</head>

<body>

  <!--topnav-->
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
    <a href="add_product.php">Add Product</a>
    <a href="compare.php">Compare</a>
    <!--<a href="#Laptops">Laptops</a>
    <a href="#Mobiles">Mobiles</a>-->
  </div>

  <section class="cd-products-comparison-table">
    <header>
      <h2>Compare Models</h2>
      <?php
        //echo $product_extract_err;
        //echo print_r($_SESSION["compare"]);
        //echo $sql_product_extract;
       ?>

      <!--<div class="actions">
        <a href="#0" class="reset">Reset</a>
        <a href="#0" class="filter">Filter</a>
      </div>-->
    </header>

    <div class="cd-products-table">
      <div class="features">
        <div class="top-info" style="height: 400px;">Models</div>
        <ul class="cd-features-list">
          <li style="height :100px;">Model Name</li>
          <li>Price</li>
          <li>CPU type</li>
          <li>OS</li>
          <li>RAM size</li>
          <li>HDD size</li>
          <li>Quantity</li>
        </ul>
      </div> <!-- .features -->

      <div class="cd-products-wrapper">
        <ul class="cd-products-columns">

          <?php

            while($stmt_product_extract->fetch())
            {

              echo "<li class=\"product\">";
              echo "<div class=\"top-info\" style=\"width: 400px; height: 400px;\" >";
              echo '<div class="check"></div>';
              echo '<img src="'.$image.'" alt="product image">';
              echo '</div>';
              echo '<ul class="cd-features-list">';
              echo '<li style="height :100px;">'.$model.'</li>';
              echo '<li>'.$mrp.'</li>';
              echo  '<li>'.$cpu.'</li>';
              echo  '<li>'.$os.'</li>';
              echo  '<li>'.$ram.'GB</li>';
              echo  '<li>'.$hdd.'GB</li>';
              echo  '<li>'.$stock.'</li>';
              echo  '</ul>';
              echo  '<div class="form-group">';
              echo  '<form  action="'.htmlspecialchars($_SERVER['PHP_SELF']).'" method="POST">';
              echo  '<input type="hidden" name="product_id_submit" value="'.$product_id.'">';
              if( isset($_SESSION["cart"]) && array_search( $product_id, $_SESSION["cart"]) !== FALSE )
              {
                $cart_submit_value = "Remove from Cart";
              }
              else
              {
                $cart_submit_value = "Add to Cart";
              }
              echo  '<button class="add-button" type="submit" name="cart_submit" value="'.$cart_submit_value.'">'.$cart_submit_value.'</button>';
              echo "<br>";
              echo  '<button class="add-button" type="submit" name="view_submit" value="View Product">View Product</button>';
              echo "<br>";
              echo  '<button class="add-button" type="submit" name="remove_submit" value="Remove from Compare">Remove from Compare</button>';
              echo  '</form>';
              echo  '</div>';
              echo '</li>';
            }

          ?>
            <!--<li class="product">
              <div class="top-info">
                <div class="check"></div>
                <img src="product_images/1_redmi_note7_red.png" alt="product image">
              </div>

              <ul class="cd-features-list">
                <li>Redmi Note 7 Pro - 4GB - 64GB - RED</li>
                <li>12,999</li>
                <li>Octa-core 2.02GHz</li>
                <li>Android 10</li>
                <li>4GB</li>
                <li>64GB</li>
                <li>10</li>
              </ul>
              <div class="form-group">
                <form action="/cart/add" method="POST">
                  <button class="add-button" type="submit">Add to Cart</button>
                </form>

                <form method="POST">
                  <button class="add-button" type="submit">View Product</button>
                </form>

                <form method="POST">
                  <button class="add-button" type="submit">Remove this Product</button>
                </form>
              </div>
            </li>--> <!-- .product -->

            <!--<li class="product">
              <div class="top-info">
                <div class="check"></div>
                <img src="product_images/1_redmi_note7_red.png" alt="product image">
              </div>

              <ul class="cd-features-list">
                <li>Redmi Note 7 Pro - 4GB - 64GB - RED</li>
                <li>12,499</li>
                <li>Octa-core 2.02GHz</li>
                <li>Android 10</li>
                <li>4GB</li>
                <li>64GB</li>
                <li>10</li>
              </ul>
              <div class="form-group">
                <form action="/cart/add" method="POST">
                  <button class="add-button" type="submit">Add to Cart</button>
                </form>

                <form method="POST">
                  <button class="add-button" type="submit">View Product</button>
                </form>

                <form method="POST">
                  <button class="add-button" type="submit">Remove this Product</button>
                </form>
              </div>
            </li>--> <!-- .product -->

            <!--<li class="product">
              <div class="top-info">
                <div class="check"></div>
                <img src="product_images/3_redmi_note7_gray.jpg" alt="product image">
              </div>

              <ul class="cd-features-list">
                <li>Redmi Note 7 Pro - 4GB - 64GB - RED</li>
                <li>12,299</li>
                <li>Octa-core 2.02GHz</li>
                <li>Android 10</li>
                <li>4GB</li>
                <li>64GB</li>
                <li>10</li>
              </ul>
              <div class="form-group">
                <form action="/cart/add" method="POST">
                  <button class="add-button" type="submit">Add to Cart</button>
                </form>

                <form method="POST">
                  <button class="add-button" type="submit">View Product</button>
                </form>

                <form method="POST">
                  <button class="add-button" type="submit">Remove this Product</button>
                </form>
              </div>
            </li>--> <!-- .product -->

            <!--<li class="product">
              <div class="top-info">
                <div class="check"></div>
                <img src="product_images/12_iphone7_rose_gold.jpg" alt="product image">
              </div>

              <ul class="cd-features-list">
                <li>Apple iPhone 7 (128GB) - Rose Gold</li>
                <li>29,999</li>
                <li>A10 Fusion chip</li>
                <li>iOS13</li>
                <li>2GB</li>
                <li>128GB</li>
                <li>10</li>
              </ul>
              <div class="form-group">
                <form action="/cart/add" method="POST">
                  <button class="add-button" type="submit">Add to Cart</button>
                </form>

                <form method="POST">
                  <button class="add-button" type="submit">View Product</button>
                </form>

                <form method="POST">
                  <button class="add-button" type="submit">Remove this Product</button>
                </form>
              </div>
            </li>--><!-- .product -->

            <!--<li class="product">
              <div class="top-info">
                <div class="check"></div>
                <img src="product_images/13_iphone7_gold.jpg" alt="product image">
              </div>

              <ul class="cd-features-list">
                <li>Apple iPhone 7 (128GB) - Gold</li>
                <li>29,999</li>
                <li>A10 Fusion chip</li>
                <li>iOS13</li>
                <li>2GB</li>
                <li>128GB</li>
                <li>10</li>
              </ul>
              <div class="form-group">
                <form action="/cart/add" method="POST">
                  <button class="add-button" type="submit">Add to Cart</button>
                </form>

                <form method="POST">
                  <button class="add-button" type="submit">View Product</button>
                </form>

                <form method="POST">
                  <button class="add-button" type="submit">Remove this Product</button>
                </form>
              </div>
            </li>--><!-- .product -->

        </ul> <!-- .cd-products-columns -->
      </div> <!-- .cd-products-wrapper -->

      <!--<ul class="cd-table-navigation">
        <li><a href="#0" class="prev inactive">Prev</a></li>
        <li><a href="#0" class="next">Next</a></li>
      </ul>-->
    </div> <!-- .cd-products-table -->
  </section> <!-- .cd-products-comparison-table -->
</body>
</html>
