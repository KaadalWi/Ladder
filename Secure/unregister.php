<?php
   require("../classPlayer.php");
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
   require("../dbConnection.php");
   
   try
   {
      $query = $db->prepare("select * from player where username = :username;");
      $query->execute(array(":username"=>$_SESSION['user']->username));
   }
   catch (PDOException $e)
   {
      echo '
         <script type="text/javascript">
            <!--
            alert("FATAL ERROR: Unregistration failed. Please try again.");
            window.history.back();
            // -->
         </script>';
      $query->closeCursor();
      $db->close();
      die();
   }
   
   $player = $query->fetch(PDO::FETCH_ASSOC);
   if (sha1($_POST['password']) == $player['password'])
   {
      try
      {
         $db->beginTransaction();
         
         $players = $db->prepare("select * from player where rank > :rank;");
         $players->execute(array(":rank"=>$_SESSION["user"]->rank));
         $query = $db->prepare("delete from challenge where challenger = :username or challengee = :username;");
         $query->execute(array(":username"=>$_SESSION["user"]->username));
         $query = $db->prepare("delete from game where winner = :username or loser = :username;");
         $query->execute(array(":username"=>$_SESSION["user"]->username));
         $query = $db->prepare("delete from player where username = :username");
         $query->execute(array(":username"=>$_SESSION["user"]->username));

         foreach($players->fetchAll(PDO::FETCH_ASSOC) as $playerRow)
         {
            $player = $db->prepare("update player set rank = :rank where username = :username;");
            $player->execute(array(":rank"=>($playerRow["rank"] - 1), ":username"=>$playerRow["username"]));
         }

         $db->commit();
      }
      catch (PDOException $e)
      {
         echo '
            <script type="text/javascript">
               <!--
               alert("FATAL ERROR: Unregistration failed. Please try again.");
               window.history.back();
               // -->
            </script>';
         $db->rollBack();
         $query->closeCursor();
         $db->close();
         die();
      }
   }
   else
   {
      echo '
         <script type="text/javascript">
            <!--
            alert("Incorrect password. Please try again.");
            window.history.back();
            // -->
         </script>';
      $query->closeCursor();
      die();
   }

   $query->closeCursor();
   
   // Success - send them somewhere! 
   echo '
      <script type="text/javascript">
         <!--
         window.location="/logout.php";
         // -->
      </script>';
?>