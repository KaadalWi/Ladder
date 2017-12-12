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
      // Prepare an delete query
      $remove = $db->prepare("
         delete from challenge
         where challenger = :challenger and challengee = :challengee and scheduled = :scheduled;");

      // Execute the query
      $db->beginTransaction();
      $remove->execute(array(":challengee"=>$_SESSION["user"]->username,
         ":challenger"=>htmlspecialchars_decode($_POST["challenger"]),
         ":scheduled"=>htmlspecialchars_decode($_POST["scheduled"])));

      // Check the results - should be one row
      if ($remove->rowCount() != 1)
      {
         // Can check the status to see if we violated the unique
         // constraint on username and alert the user
         $db->rollBack();
         echo '
            <script type="text/javascript">
               <!--
               alert("Reject failed.");
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
            alert("FATAL ERROR: Reject failed. Please try again.");
            window.history.back();
            // -->
         </script>';
      $accept->closeCursor();
      $db->close();
      die();
   }

   $db->commit();
   $remove->closeCursor();
   
   // Success - send them somewhere! 
   echo '
      <script type="text/javascript">
         <!--
         window.location="/challenges.php#challenges";
         // -->
      </script>';
?>