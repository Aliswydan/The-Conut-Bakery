<?php
    //checkRoot checks if the admin is the root or not
    $checkRoot = $_SESSION['username'] != 'root' ? false : true;

    // Define the current page
    //The function will return the file name instead of absolute path
    $current_page = basename($_SERVER['PHP_SELF']);
  
    // Define your menu items
    //All pages in the menu bar
    $menu_items = array(
          'Statistics' => 'statistics.php',
          'History' => 'history.php',
          'Orders' => 'orders.php',
          'Items' => 'items.php',
          'Offers' => 'offers.php',
          'Admins' => 'admins.php'
        );

        echo "<div class=\"menu\">";
  
        // Generate the menu items
        foreach ($menu_items as $name => $url) {
          // Add the 'active' class if this is the current page
          //One item in the menu bar will take the class active which is the activated one and the rest will take empty string
          $class = ($current_page == $url) ? 'active' : '';
          

          //Based on checkRoot we decide wheather to put the admins item in the menu bar or not
          if($checkRoot) {
            echo "<a class=\"$class\" href=\"$url\">$name</a>";
          } elseif($name != 'Admins') {
            echo "<a class=\"$class\" href=\"$url\">$name</a>";
          }
  
          
        }

        echo "<form method=\"post\" action=\"logout.php\"> 
                <button type=\"submit\" name=\"logoutButton\" value=\"signout\">Log Out</button>
              </form>";

        echo "</div>";
?>