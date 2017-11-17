<?php
   require("dbConnection.php");
   require("classPlayer.php");
   session_unset();
   session_destroy();
   echo "
      <script type='text/javascript'>
         <!--
         window.location='/';
         // -->
      </script>";
   $db->close();
?>