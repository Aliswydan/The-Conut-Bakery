<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="CSS/nav_bar.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans+Condensed:wght@500&display=swap" rel="stylesheet">
</head>

<body>
    <?php
    session_start();

    // Define your menu items
    $menu_items = array(
        
        'Menu' => array(
            'Conuts' => 'Conuts.php',
            'Chimneys' => 'Chimneys.php',
            'Drinks' => 'Drinks.php',
        ),
        'Gallery' => 'Gallery.php',
        'Order Now' => 'Ordernow.php',
    );
    ?>

    <div class="logo-name">
        <a href="index.php"><img src="img/logo.png" alt="Logo" height="60px" width="60px"></a>
        <a href="index.php">The Conut<span id="name-logo-bakery">&nbsp;Bakery</span></a>
    </div>

    <ul class="nav-items">
        <li><a href="index.php">Home</a></li>
        <?php
        // Output the Menu dropdown if it is set
        if (isset($menu_items['Menu'])) : ?>
            <li class="dropdown">
                <a class="droptn" href="#">Menu</a>
                <div class="dropdown-content">
                    <?php foreach ($menu_items['Menu'] as $label => $link) {
                        echo '<a href="' . $link . '">' . $label . '</a>';
                    } ?>
                </div>
            </li>
        <?php endif;

      

        // Output the remaining menu items
        foreach ($menu_items as $label => $link) {
            if (!is_array($link)) {
                echo '<li><a href="' . $link . '">' . $label . '</a></li>';
            }
        }
        ?>
    </ul>

    <?php if (isset($_SESSION['user_id'])) { ?>
        <a class="btnN" href="Feedback.php">Feedback</a>
        <a class="btnN" href="logout.php">LogOut</a>
    <?php } else { ?>
        <a class="btnN" href="login.php">Login</a>
        <a class="btnN" href="signup.php">SignUp</a>
    <?php } ?>

</body>

</html>
