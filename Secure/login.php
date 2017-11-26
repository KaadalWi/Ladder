<?php
   require("../dbConnection.php");
   require("../classPlayer.php");
   // Find this user, could retrieve other data we want to remember...
   $loggedIn = false;

   try
   {
      $query = $db->prepare("select * from player where username = :username");
      $query->execute(array(":username"=>$_POST["username"]));
   }
   catch (PDOException $e)
   {
      echo '
         <script type="text/javascript">
            <!--
            alert("FATAL ERROR: Login failed. Please try again.");
            window.history.back();
            // -->
         </script>';
      $query->closeCursor();
      $db->close();
      die();
   }

   if ($player = $query->fetch())
   { // must have found them
      // Use the preferred SHA-1 encryption to check the password
      if (sha1($_POST["password"]) == $player["password"])
      { // Success!
         // Clean out any old session
         session_start();
         session_unset();
         session_destroy();
         // Start a new session and remember the username (and more?)
         session_start();
         $user = new Player($player["name"], $player["email"], $player["phone"],
            $player["username"], $player["rank"]);
         $_SESSION["user"] = $user;
         echo '
            <script type="text/javascript">
               <!--
               window.location="/ladder.php#ladder";
               // -->
            </script>';
         $loggedIn = true;
      }
   }
   
   $query->closeCursor();

   if (!$loggedIn)
   {
      // If we reach this point either the user 
      // doesn’t exist in the database or they gave 
      // a bad username/password; alert them
      // and send them back to the main page
      echo '
         <script type="text/javascript">
            <!--
            alert("Login failed.");
            window.history.back();
            // -->
         </script>';
   }
?>