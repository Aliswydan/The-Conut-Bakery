<?php
    echo "<div class=\"inner-menu\">";

    $submenuItems = array(
        'Conuts' => 'conuts.php',
        'Chimney Cakes' => 'chimney_cakes.php',
        'Drinks' => 'drinks.php',
        'Spreads' => 'spreads.php',
        'Toppings' => 'toppings.php'
    );

    foreach($submenuItems as $itemName => $url) {

        $class = (isset($_GET['submenuPage']) && $_GET['submenuPage'] == $url) || (isset($_POST['submenuPage']) && $_POST['submenuPage'] == $url) || (isset($_SESSION['submenuPage']) && $_SESSION['submenuPage'] == $url) || (isset($_GET['submenuPageFromEdit']) && $_GET['submenuPageFromEdit'] == $url) ? 'activePage' : '';

        echo "<a class=\"$class\" href=\"view_items.php?submenuPage=$url\"> $itemName </a>";
    }

    echo "</div>";
?>