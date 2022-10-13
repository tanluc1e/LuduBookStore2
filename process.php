<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
	<link href='https://fonts.googleapis.com/css?family=Lato:300,400|Montserrat:700' rel='stylesheet' type='text/css'>
	<style>
		@import url(//cdnjs.cloudflare.com/ajax/libs/normalize/3.0.1/normalize.min.css);
		@import url(//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css);
	</style>
	<link rel="stylesheet" href="https://2-22-4-dot-lead-pages.appspot.com/static/lp918/min/default_thank_you.css">
	<script src="https://2-22-4-dot-lead-pages.appspot.com/static/lp918/min/jquery-1.9.1.min.js"></script>
	<script src="https://2-22-4-dot-lead-pages.appspot.com/static/lp918/min/html5shiv.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i');
        @import url('https://fonts.googleapis.com/css?family=Dancing+Script:400,700');
        * {
            font-family: 'Poppins', sans-serif;
        }
        .text {
            font-family: 'Dancing Script', cursive !important;
            font-size: 1.8em;
        }
    </style>
</head>
<body>
	<header class="site-header" id="header">
		<h1 class="site-header__title" data-lead-id="site-header-title">THANK YOU!</h1>
	</header>

	<div class="main-content">
		<i class="fa fa-check main-content__checkmark" id="checkmark"></i>
		<p class="main-content__body" data-lead-id="main-content-body">Thanks so much for buying. It means a lot to us, just like you! We really appreciate you taking a moment of your time with us today. See you again!</p>
        <div class='text'>Your Order has been received!</div>
	</div>

	<footer class="site-footer" id="footer">
		<p class="site-footer__fineprint" id="fineprint">Copyright ©2014 | All Rights Reserved</p>
	</footer>

    <?php
	    session_start();
		require_once "./functions/database_functions.php";
		// print out header here
		$title = "Purchase Process";

		// connect database
		$conn = db_connect();
		extract($_SESSION['ship']);


		// find customer
		$customerid = getCustomerId($name, $address, $city, $state, $zip_code);
		if($customerid == null) {
			// insert customer into database and return customerid
			$customerid = setCustomerId($name, $address, $city,$state, $zip_code);
		}
		$date = date("Y-m-d H:i:s");
		insertIntoOrder($conn, $customerid, $_SESSION['total_price'], $date, $name, $address, $city, $state, $zip_code);

		// take orderid from order to insert order items
		$orderid = getOrderId($conn, $customerid);
	
		foreach($_SESSION['cart'] as $isbn => $qty)
		{
			$bookprice = getbookprice($isbn);
			$query = "INSERT INTO order_items VALUES 
			('$orderid', '$isbn', '$bookprice', '$qty')";
			$result = mysqli_query($conn, $query);
			if(!$result){
				echo "Insert value false!" . mysqli_error($conn2);
				exit;
			}
		}
		session_unset();
	?>

	<?php
	    if(isset($conn))
	    {
			mysqli_close($conn);
		}
    ?>
    <!-- Bootsrtap JavaScript -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>