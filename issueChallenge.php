<?php
   require("classPlayer.php");
   session_start();
   if (!isset($_SESSION["user"]))
   {
      echo '
         <script type="text/javascript">
            <!--
            alert("Welcome! Please log in.");
            window.location="/";
            // -->
         </script>';
      die();
   }
   require("dbConnection.php");

   try
   {
      // Prepare an insert query
      $query = $db->prepare("
         insert into challenge
            (challenger, challengee, issued, scheduled) 
         values (:challenger, :challengee, :issued, :scheduled);");

      $currentTime = date("Y-m-d H:i:s");

      // Execute the query
      $query->execute(array(":challenger"=>$_SESSION["user"]->username,
         ":challengee"=>htmlspecialchars_decode($_POST["challengee_username"]), 
         ":issued"=>$currentTime, ":scheduled"=>$_POST["match_time"]));

      // Check the results - should be one row
      if ($query->rowCount() != 1)
      {
         // Can check the status to see if we violated the unique
         // constraint on username and alert the user
         echo '
            <script type="text/javascript">
               <!--
               alert("Challenge failed.");
               window.history.back();
               // -->
            </script>';
         $query->closeCursor();
         die();
      }
   }
   catch (PDOException $e)
   {
      echo '
         <script type="text/javascript">
            <!--
            alert("FATAL ERROR: Challenge failed. Please try again.");
            window.history.back();
            // -->
         </script>';
      $query->closeCursor();
      $db->close();
      die();
   }

   $query->closeCursor();
   
   // Success - send them somewhere! 
   echo '
      <script type="text/javascript">
         <!--
         window.location="/ladder.php";
         // -->
      </script>';
?>