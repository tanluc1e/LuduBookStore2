<?php
session_start();
include("connect.php");

//Kiểm tra nếu đã đăng nhập (get user_email == true) sẽ lấy giá trị từ database
if(session_id() == '') session_start();
if (isset($_SESSION['user_email']) == true) {
    //GET CURRENT VALUES FROM DATABASE (User_name)
    $user_email = $_SESSION['user_email'];
    $sql = "SELECT * FROM Users WHERE user_email='$user_email'";
    $query = mysqli_query($conn, $sql);
    if ($row = mysqli_fetch_assoc($query)) { 
	$current_username = $row['user_name'];
    }
} 
// Get random 10 book
$stmt = $db->prepare('SELECT * FROM books ORDER BY RAND() LIMIT 10');
$stmt->execute();
$resultSet = $stmt->get_result();
$data = $resultSet->fetch_all(MYSQLI_ASSOC);

// Get random 100 book
$stmt2 = $db->prepare('SELECT * FROM books ORDER BY RAND() LIMIT 100');
$stmt2->execute();
$resultSet2 = $stmt2->get_result();
$data2 = $resultSet2->fetch_all(MYSQLI_ASSOC);

// Travel
$travel = $db->prepare('SELECT * FROM books WHERE category="Travel" ORDER BY RAND() LIMIT 10');
$travel->execute();
$travelResult = $travel->get_result();
$travelData = $travelResult->fetch_all(MYSQLI_ASSOC);

// Wildlife
$wildlife = $db->prepare('SELECT * FROM books WHERE category="Wildlife" ORDER BY RAND() LIMIT 10');
$wildlife->execute();
$wildlifeResult = $wildlife->get_result();
$wildlifeData = $wildlifeResult->fetch_all(MYSQLI_ASSOC);

// Nature
$nature = $db->prepare('SELECT * FROM books WHERE category="Nature" ORDER BY RAND() LIMIT 10');
$nature->execute();
$natureResult = $nature->get_result();
$natureData = $natureResult->fetch_all(MYSQLI_ASSOC);


include("connect.php");
require_once "./functions/database_functions.php";
require_once "./functions/cart_functions.php";
error_reporting(0);
if(session_id() == '') session_start();
if (isset($_SESSION['user_email']) == true) {
    //GET CURRENT VALUES FROM DATABASE (User_name)
    $user_email = $_SESSION['user_email'];
    $sql = "SELECT * FROM Users WHERE user_email='$user_email'";
    $query = mysqli_query($conn, $sql);
    if ($row = mysqli_fetch_assoc($query)) { 
	$current_username = $row['user_name'];
    }
} 
/*
if (isset($_SESSION['user_email']) == false) {
    header("location: index.php");
    exit();
} */
// bookid got from form post method, change this place later.
if(isset($_POST['bookisbn']))
{
    $bookid = $_POST['bookisbn'];
}
if(isset($bookid))
{
    // new iem selected
    if(!isset($_SESSION['cart']))
    {
        // $_SESSION['cart'] is associative array that bookisbn => qty
        $_SESSION['cart'] = array();
           $_SESSION['total_items'] = 0;
        $_SESSION['total_price'] = '0.00';
    }
    if(!isset($_SESSION['cart'][$bookid]))
    {
           $_SESSION['cart'][$bookid] = 1;
    } 

    elseif(isset($_POST['cart']))
    {
        $_SESSION['cart'][$bookid]++;
        unset($_POST);
    }
}
// if save change button is clicked , change the qty of each bookisbn
if(isset($_POST['save_change']))
{
    foreach($_SESSION['cart'] as $isbn =>$qty)
    {
        if($_POST[$isbn] == '0')
        {
            unset($_SESSION['cart']["$isbn"]);
        } 
        else 
        {
            $_SESSION['cart']["$isbn"] = $_POST["$isbn"];
        }
    }
}
$getTotalPrice = total_price($_SESSION['cart']);
?>

<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Ludu Bookstore</title>
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css'>
  <link rel="stylesheet" href="./style2.css">

</head>
<body>
<!-- partial:index.partial.html -->
<div class="hero-section">	
		<div class="about-text hover-target">about</div>
		<div class="contact-text hover-target">contact</div>
		<div class="section-center">
			<div class="container-fluid">
				<div class="row justify-content-center">
					<div class="col-12 text-center">
						<h1>ONLNE BOOKSTORE</h1>
						<h2>Hello, <?php 
							if (!empty($_SESSION['user_email'])){
								echo
								"<a href='#'>
								<i class='icon icon-user'></i>
								<span class='current_name'>$current_username</span>
								</a>";
							} else {
								echo
								"<a href='logandreg.php'>
								<i class='icon icon-user'></i>
								<span>[<span class='current_name'>...</span>]</span>
								</a>";
							}?>
						</h2>
					</div>
					<div class="col-12 text-center mb-2">
						<div class="dancing">Welcome to Ludu</div><br><br>
						<div style="color: #fff">Your cart: <span style="color: #D7A1F9;"><?php echo number_format($getTotalPrice) ?></span></div>
						<div class="btn-group dropright">
							<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							Menu
							</button>
							<div class="dropdown-menu">
							<!-- Items 1 -->
							<?php if (isset($_SESSION['user_email']) == true) 
							echo 
							'
							<a class="dropdown-item" href="cart.php">My cart</a>
							<a class="dropdown-item" href="#">Information</a>
							<a class="dropdown-item" href="#">Change password</a>
							'; else {
								echo '<a class="dropdown-item" href="logandreg.php">Log In</a>';
							}
							?>

							<!-- Items 2 -->
							<?php if (isset($_SESSION['user_email']) == true) 
							echo 
							'
							<div class="dropdown-divider"></div>
							<a class="dropdown-item" href="logout.php">Log out</a>
							'?>
							</div>
						</div>
					</div>
					<div class="col-12 text-center mt-4 mt-lg-5">
						<p>
							<span class="travel hover-target">travel</span> 
							<span class="wildlife hover-target">wildlife</span> 
							<span class="nature hover-target">nature</span>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>	
	
	<div class="about-section">	
		<div class="about-close hover-target"></div>
		<div class="section-center">
			<div class="container">
				<div class="row justify-content-center">
					<div class="col-5 text-center" style="display: flex;">
						<img src="./img/ThanhVien1.jpg" alt="">
						<img src="./img/ThanhVien2.jpg" alt="">
					</div>
					<div class="col-lg-8 text-center mt-4">
						<p>The more that you read, the more things you will know. The more that you learn, the more places you’ll go.</p>
					</div>
					<div class="col-12 text-center">
						<p><span>Ludu</span></p>
					</div>
				</div>
			</div>
		</div>		
	</div>
	
	<div class="contact-section">	
		<div class="contact-close hover-target"></div>
		<div class="section-center">
			<div class="container">
				<div class="row justify-content-center">
					<div class="col-12 text-center">
						<a href="#" class="hover-target">ludu@gmail.com</a>
					</div>
					<div class="col-12 text-center social mt-4">
						<a href="#" class="hover-target">instagram</a>
						<a href="#" class="hover-target">twitter</a>
						<a href="#" class="hover-target">facebook</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="travel-section">	
		<div class="travel-close hover-target"></div>
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-12 text-center">
					<h3>travel</h3>
				</div>
				<div class="col-12 mt-3 text-center">
					<p><span>L U D U</span></p>
				</div>
				<div class="col-12 text-center">
					<p>
					Prepare for your next adventure by researching the best travel destinations in the world with our extensive selection of used and new travel books.
					</p>
				</div>
				<?php foreach ($travelData as $book): ?>
					<div class="col-md-6 col-lg-4 productCursor">
						<img src="<?=$book['image']?>" alt="<?=$book['name']?>">
						<figure class="product-style">
						<form method="post" action="cart.php">
							<input type="hidden" name="bookisbn" value="<?=$book['bookid']?>">
							<button type="submit" name="cart" class="add-to-cart" data-product-tile="add-to-cart">Add to Cart</button>
						</form>
						<figcaption>
							<h3 class="itemName"><?=$book['name']?></h3>
							<p class="itemAuthor"><?=$book['author']?></p>
							<div class="itemPrice"><?=number_format($book['price'])?></div>
						</figcaption>
						</figure>
					</div>
                <?php endforeach; ?>
				<div class="col-md-6 col-lg-4">
					<img src="https://i.ibb.co/rtxdpF3/NoBook.jpg" alt="">
				</div>
				<div class="col-md-6 col-lg-4">
					<img src="https://i.ibb.co/rtxdpF3/NoBook.jpg" alt="">
				</div>
				<div class="col-md-6 col-lg-4">
					<img src="https://i.ibb.co/rtxdpF3/NoBook.jpg" alt="">
				</div>
				<div class="col-md-6 col-lg-4">
					<img src="https://i.ibb.co/rtxdpF3/NoBook.jpg" alt="">
				</div>
				<div class="col-md-6 col-lg-4">
					<img src="https://i.ibb.co/rtxdpF3/NoBook.jpg" alt="">
				</div>
			</div>
		</div>
	</div>
	
	<div class="wildlife-section">	
		<div class="wildlife-close hover-target"></div>
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-12 text-center">
					<h3>wildlife</h3>
				</div>
				<div class="col-12 mt-3 text-center">
					<p><span>L U D U</span></p>
				</div>
				<?php foreach ($wildlifeData as $book): ?>
					<div class="col-md-6 col-lg-4 productCursor">
						<img src="<?=$book['image']?>" alt="<?=$book['name']?>">
						<figure class="product-style">
						<form method="post" action="cart.php">
							<input type="hidden" name="bookisbn" value="<?=$book['bookid']?>">
							<button type="submit" name="cart" class="add-to-cart" data-product-tile="add-to-cart">Add to Cart</button>
						</form>
						<figcaption>
							<h3 class="itemName"><?=$book['name']?></h3>
							<p class="itemAuthor"><?=$book['author']?></p>
							<div class="itemPrice"><?=number_format($book['price'])?></div>
						</figcaption>
						</figure>
					</div>
                <?php endforeach; ?>
				<div class="col-md-6 col-lg-4">
					<img src="https://i.ibb.co/rtxdpF3/NoBook.jpg" alt="">
				</div>
				<div class="col-md-6 col-lg-4">
					<img src="https://i.ibb.co/rtxdpF3/NoBook.jpg" alt="">
				</div>
				<div class="col-md-6 col-lg-4">
					<img src="https://i.ibb.co/rtxdpF3/NoBook.jpg" alt="">
				</div>
			</div>
		</div>
	</div>
	
	<div class="nature-section">	
		<div class="nature-close hover-target"></div>
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-12 text-center">
					<h3>nature</h3>
				</div>
				<div class="col-12 mt-3 text-center">
					<p><span>L U D U</span></p>
				</div>
				<?php foreach ($natureData as $book): ?>
					<div class="col-md-6 col-lg-4 productCursor">
						<img src="<?=$book['image']?>" alt="<?=$book['name']?>">
						<figure class="product-style">
						<form method="post" action="cart.php">
							<input type="hidden" name="bookisbn" value="<?=$book['bookid']?>">
							<button type="submit" name="cart" class="add-to-cart" data-product-tile="add-to-cart">Add to Cart</button>
						</form>
						<figcaption>
							<h3 class="itemName"><?=$book['name']?></h3>
							<p class="itemAuthor"><?=$book['author']?></p>
							<div class="itemPrice"><?=number_format($book['price'])?></div>
						</figcaption>
						</figure>
					</div>
                <?php endforeach; ?>
				<div class="col-md-6 col-lg-4">
					<img src="https://i.ibb.co/rtxdpF3/NoBook.jpg" alt="">
				</div>
				<div class="col-md-6 col-lg-4">
					<img src="https://i.ibb.co/rtxdpF3/NoBook.jpg" alt="">
				</div>
				<div class="col-md-6 col-lg-4">
					<img src="https://i.ibb.co/rtxdpF3/NoBook.jpg" alt="">
				</div>
				<div class="col-md-6 col-lg-4">
					<img src="https://i.ibb.co/rtxdpF3/NoBook.jpg" alt="">
				</div>
				<div class="col-md-6 col-lg-4">
					<img src="https://i.ibb.co/rtxdpF3/NoBook.jpg" alt="">
				</div>
				<div class="col-md-6 col-lg-4">
					<img src="https://i.ibb.co/rtxdpF3/NoBook.jpg" alt="">
				</div>
				<div class="col-md-6 col-lg-4">
					<img src="https://i.ibb.co/rtxdpF3/NoBook.jpg" alt="">
				</div>
				<div class="col-md-6 col-lg-4">
					<img src="https://i.ibb.co/rtxdpF3/NoBook.jpg" alt="">
				</div>
				<div class="col-md-6 col-lg-4">
					<img src="https://i.ibb.co/rtxdpF3/NoBook.jpg" alt="">
				</div>
				<div class="col-md-6 col-lg-4">
					<img src="https://i.ibb.co/rtxdpF3/NoBook.jpg" alt="">
				</div>
				<div class="col-md-6 col-lg-4">
					<img src="https://i.ibb.co/rtxdpF3/NoBook.jpg" alt="">
				</div>
				<div class="col-md-6 col-lg-4">
					<img src="https://i.ibb.co/rtxdpF3/NoBook.jpg" alt="">
				</div>
			</div>
		</div>
	</div>
	
	<div class='cursor' id="cursor"></div>
	<div class='cursor2' id="cursor2"></div>
	<div class='cursor3' id="cursor3"></div>

<!-- Link to page
================================================== -->

<a href="#" class="link-to-portfolio hover-target" target=”_blank”></a>
<!-- partial -->
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    </script><script  src="./script.js"></script>

</body>
</html>

<style>
.product-style figcaption {
    margin-top: 15px;
    margin-bottom: 15px;
    text-align: center;
}
.product-style button.add-to-cart {
	background: #1f2029;
    color: white;
    text-transform: uppercase;
    text-align: center;
    line-height: 3;
    position: absolute;
    bottom: 160px;
    left: 240px;
    z-index: 9;
    opacity: 0;
    transition: 0.5s ease-out;
    border-radius: 30% 70% 70% 30%/85% 70% 70% 189%;
    border: 2px solid white;
	cursor: pointer;
}
.productCursor:hover button.add-to-cart {
      opacity: 1;
}
.itemName{
	color: #D7A1F9;
}
.itemPrice{
	color: #D7A1F9;
}
.current_name{
	color: #D7A1F9;
}
</style>