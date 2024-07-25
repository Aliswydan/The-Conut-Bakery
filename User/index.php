

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> The Conut Bakery | Home </title>
    <link rel="icon" href="img/logo.png" type="image/ico">

	<link rel="stylesheet"  href="CSS/index.css">
	 <link rel="stylesheet"  href="CSS/nav_bar.css">
	<link rel="stylesheet"  href="CSS/footer.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap" rel="stylesheet">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Fira+Sans+Condensed:wght@500&display=swap" rel="stylesheet">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	
	<script>
		$(document).ready(function(){
			$(".dropdown").click(function(){
				$(".dropdown-content").slideDown("slow");
			});
		});
	</script>
	
	<script>
		function ScrollToTop(){
			window.scrollTo(0,0);
		}
	</script>

</head>


<body>
<!----------------------------------------------------------- Nav ----------------------------------------------------------->

	<nav> <?php include 'nav_bar.php'?></nav>
	


<!----------------------------------------------------------- Header ----------------------------------------------------------->
	
<div class="scrolltop" onclick="ScrollToTop();">Up</div>
<div class="rotate">
	
	<div class="rotated">
	<h1 style="font-size:4em;margin-bottom:60px;">Hungary's Original Dessert</h1>
	
	<p style="font-size:1.2em;">We are company that make and distribute the special hungarian taste<br>
		Our main product is made with attention to detail and served with your favorite options
	</p>
	
	<button class="btn bt1"><a href="#come">See The Menu</a></button>
	<button class="btn bt2"><a href="Ordernow.php">Order Now</a></button>
</div></div>

<!----------------------------------------------------------- main ----------------------------------------------------------->
	<main>
		<div class="welcome">
			<p class="welcome-p">Welcome To The Conut Bakery</p>
			<p class="welcome-p2">Our Hungarian delight conut and chimney cake will leave you craving for more!</p>
		</div>
<!----------------------------------------------------------- conut ----------------------------------------------------------->


<div class="conut-container">
	<div class="conut-left">
	   <img  src="img/Conut.png"  alt="Conut" height="400px"  width="400px">
	</div>
	    <div class="conut-paragraph">
			<h1>Conuts</h1>
			<p>Have our original Hungarian conut with bites of perfection</p>
			<p> Don't miss out conut filled with love , spice , and everything nice </p>
	    </div> 
</div>

<!----------------------------------------------------------- chimney ----------------------------------------------------------->
<div class="chimney-container">
	
		<div class="chimney-paragraph">
			<h1>Chimney Cakes</h1>
			<p> Immerse yourself in an unforgottable tasting experience with our world renowned chimney cake</p>
			<p> Explore and order your chimney cake with your own touch of toppings and spreads</p>
		</div>
		<div class="chimney-right">
			<img src="img/chimneyright.png" alt="Chimney Cake" height="400px"  width="400px">
		</div>
</div>


<!----------------------------------------------------------- drink ----------------------------------------------------------->
<div class="drink-container">
	<div class="drink-left">
			<img src="img/Drinks.png" alt="Drink" height="400px"  width="400px">
		</div>
		<div class="drink-paragraph">
			<h1>Drinks</h1>
			<p>Grab your refreshing drinks and milkshakes with the best flavors and ingredients</p>
			<p> Make more of the moment and enjoy our specialty coffee thats always the way you like</p>
		</div>	  
</div>			
	

<!----------------------------------------------------------- slider ----------------------------------------------------------->
					
<div class="slid">


				<section class="auto-slider">
					<div id="slider">
						<figure>
							<img src="img/tran-1.jpg" alt="Sweet"> 
							<img src="img/tran-2.jpg" alt="Delight" >
							<img src="img/tran-3.jpg" alt="Rich">
							<img src="img/tran-1.jpg" alt="Sweet"> 
							<img src="img/tran-2.jpg" alt="Delight" >
							<img src="img/tran-3.jpg" alt="Rich">
						</figure>
						<div class="indicator"></div>
					</div>
				</section>         

</div>
<!----------------------------------------------------------- our items ----------------------------------------------------------->

		<div style="margin:2em;" id="come">
			<h2 class="toto">Our Items</h2>
			<div style="display: flex; justify-content: center ;">
			<div class="card">
				<img class="imgHome" src="img/Home-Count.jpg" alt="" width="150" height="150">
				<h1>Count</h1>
				<p>Our speciality dough, conical shaped, golden grilled &amp; cinnamon sugar coated Conuts.</p>
				<button><a class="toto1" href="Conuts.php">FOR MORE</a> </button>
			  </div>
			<div class="card">
				<img class="imgHome"  src="img/Home-Chimney.jpg" alt="" width="150" height="150">
				<h1>Chimney</h1>
				<p>Our speciality dough, cylandridal shaped, golden grilled &amp; cinnamon sugar coated soft Chimneys.</p>
				<button><a class="toto1" href="Chimneys.php">FOR MORE</a> </button>
			  </div>
			  <div class="card">
				<img class="imgHome"  src="img/Home-Drinks.jpg" alt="" width="150" height="150">
				<h1>Drinks</h1>
				<p>Delicious &amp;delectable expert made drinks that will leave you captivated with the taste.</p>
				<button><a class="toto1" href="Drinks.php">FOR MORE</a> </button>
			</div>
		</div>
	</div>
	
</main>
<!----------------------------------------------------------- Footer ----------------------------------------------------------->
<footer>
    <?php include 'Footer.php'; ?>
</footer>
</body>
 </html>
 <!----------------------------------------------------------- End ----------------------------------------------------------->