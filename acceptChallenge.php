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
      // Prepare an update query
      $accept = $db->prepare("
         update challenge
         set accepted = :accepted
         where challenger = :challenger and challengee = :challengee and scheduled = :scheduled;");
      $remove = $db->prepare("
         delete from challenge
         where (challenger = :challengee or challengee = :challengee)
            and not (challenger = :challenger and challengee = challengee and scheduled = :scheduled);");

      $currentTime = date("Y-m-d H:i:s");

      // Execute the query
      $db->beginTransaction();
      $accept->execute(array(":challengee"=>$_SESSION["user"]->username,
         ":challenger"=>htmlspecialchars_decode($_POST["challenger"]), 
         ":accepted"=>$currentTime, ":scheduled"=>htmlspecialchars_decode($_POST["scheduled"])));
      $remove->execute(array(":challengee"=>$_SESSION["user"]->username,
         ":challenger"=>htmlspecialchars_decode($_POST["challenger"]),
         ":scheduled"=>htmlspecialchars_decode($_POST["scheduled"])));

      // Check the results - should be one row
      if ($accept->rowCount() != 1)
      {
         // Can check the status to see if we violated the unique
         // constraint on username and alert the user
         $db->rollBack();
         echo '
            <script type="text/javascript">
               <!--
               alert("Challenge failed.");
               window.history.back();
               // -->
            </script>';
         $accept->closeCursor();
         die();
      }
   }
   catch (PDOException $e)
   {
      $db->rollBack();
      echo '
         <script type="text/javascript">
            <!--
            alert("FATAL ERROR: Challenge failed. Please try again.");
            window.history.back();
            // -->
         </script>';
      $accept->closeCursor();
      $db->close();
      die();
   }

   $db->commit();
   $accept->closeCursor();
   
   // Success - send them somewhere! 
   echo '
      <script type="text/javascript">
         <!--
         window.location="/challenges.php#challenges";
         // -->
      </script>';
?>