<?php
    session_start();

      $cookieName = 'loginInformation';
      setcookie($cookieName,'',time() - 3600);

      unset($_SESSION['user_id']);
    
      session_destroy();

      header('Location: login.php');

      exit(0);

  
?>