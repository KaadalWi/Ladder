<?php

   // Get a database connection
   require("../dbConnection.php");

   try
   {
      // Check username availability
      $query = $db->prepare("select username from player where username = :username;");
      $query->execute(array(":username"=>$_POST["username"]));
      if ($query->fetch())
      {
         // Can check the status to see if we violated the unique
         // constraint on username and alert the user
         echo '
            <script type="text/javascript">
               <!--
               alert("Username taken.");
               window.history.back();
               // -->
            </script>';
         $query->closeCursor();
         die();
      }

      // Prepare an insert query
      $query = $db->prepare("
         insert into player
            (name, email, rank, phone, username, password) 
         select :name, :email, coalesce(max(rank), 0) + 1, :phone,
            :username, :password from player;");

      // Execute the query
      $query->execute(array(":name"=>$_POST["name"], ":email"=>$_POST["email"], 
         ":username"=>$_POST["username"], ":phone"=>$_POST["phone"], 
         ":password"=>sha1($_POST["password"])));

      // Check the results - should be one row
      if ($query->rowCount() != 1)
      {
         // Can check the status to see if we violated the unique
         // constraint on username and alert the user
         echo '
            <script type="text/javascript">
               <!--
               alert("Registration failed.");
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
            alert("FATAL ERROR: Registration failed. Please try again.");
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