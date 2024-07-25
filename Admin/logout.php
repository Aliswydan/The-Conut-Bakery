<?php
    session_start();


    

    if(isset($_POST['logoutButton'])) {
      $cookieName = 'loginInformation';
      setcookie($cookieName,'',time() - 3600);

      unset($_SESSION['username']);
      unset($_SESSION['password']);
      session_destroy();

      header('Location: login.php');

      exit(0);
    }

  
?>