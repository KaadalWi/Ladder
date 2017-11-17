<?php
   try
   {
      $db = new PDO("pgsql:host='localhost'; dbname='ladder';", "bitnami", "imantib",
         array(PDO::ATTR_PERSISTENT => true));
   }
   catch (PDOException $e)
   {
      echo '
         <script type="text/javascript">
            <!--
            alert("FATAL ERROR: Data failed to load.");
            window.location="/ladder.php";
            // -->
         </script>';
      die();
   }
?>